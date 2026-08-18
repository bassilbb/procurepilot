<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Services\BillingService;
use App\Services\PaymentGatewayService;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function plans()
    {
        $plans = Plan::where('is_active', true)->orderBy('price_monthly')->get();
        $current = currentOrganization()?->subscription?->plan;

        return view('billing.plans', compact('plans', 'current'));
    }

    public function choosePlan(Request $request, Plan $plan)
    {
        $cycle = $request->query('cycle', 'monthly');

        if (! in_array($cycle, ['monthly', 'yearly'])) {
            $cycle = 'monthly';
        }

        $org = currentOrganization();
        $gateways = PaymentGateway::where('organization_id', $org->id)
            ->where('is_active', true)
            ->get();

        return view('billing.checkout', compact('plan', 'cycle', 'org', 'gateways'));
    }

    public function subscribe(Request $request, Plan $plan)
    {
        $request->validate([
            'cycle'  => ['required', 'in:monthly,yearly'],
            'gateway' => ['required', 'string', 'in:demo,paystack,flutterwave,mono'],
        ]);

        $org = currentOrganization();
        $cycle = $request->cycle;
        $provider = $request->gateway;

        // Demo flow still collects card details for simulation.
        if ($provider === 'demo') {
            $request->validate([
                'card_name'   => ['required', 'string'],
                'card_number' => ['required', 'string', 'min:12'],
                'card_expiry' => ['required', 'string'],
                'card_cvc'    => ['required', 'string', 'min:3'],
            ]);
        }

        // Snapshot prior subscription before subscribe() replaces it.
        $priorSubscription = $org->subscription;
        $previous = $priorSubscription ? $priorSubscription->toArray() : null;

        $subscription = app(BillingService::class)->subscribe($org, $plan, $cycle);
        $invoice = $subscription->invoices()->latest()->first();

        if ($provider === 'demo') {
            app(BillingService::class)->recordPayment($invoice, 'card', 'demo');

            return redirect()->route('billing.invoices')->with('success', 'Subscription activated successfully. Welcome to ' . $plan->name . '!');
        }

        // Real gateways: subscription stays 'pending' until payment is confirmed.
        $subscription->update(['status' => 'pending']);

        $gateway = PaymentGateway::where('organization_id', $org->id)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (! $gateway) {
            $this->restoreSubscription($subscription, $previous);

            return back()->withErrors(['gateway' => 'This payment gateway is not configured. Please contact support.']);
        }

        $reference = ReferenceService::payment($org);
        $payment = app(BillingService::class)->createPendingPayment($invoice, $provider, $reference);

        try {
            $result = app(PaymentGatewayService::class)->initialize($gateway, [
                'email'        => $org->email ?? auth()->user()->email,
                'name'         => auth()->user()->name,
                'amount'       => $invoice->amount,
                'currency'     => $invoice->currency,
                'reference'    => $reference,
                'description'  => $invoice->title,
                'callback_url' => route('billing.callback', ['gateway' => $provider, 'reference' => $reference]),
                'metadata'     => ['organization_id' => $org->id, 'invoice_id' => $invoice->id],
            ]);
        } catch (\Throwable $e) {
            $payment->update(['status' => 'failed']);
            $this->restoreSubscription($subscription, $previous);

            return back()->withErrors(['gateway' => 'Payment could not be initiated: ' . $e->getMessage()]);
        }

        $authorizationUrl = $result['authorization_url'] ?? null;
        $reference = $result['reference'] ?? $reference;

        if (! $authorizationUrl) {
            $payment->update(['status' => 'failed']);
            $this->restoreSubscription($subscription, $previous);

            return back()->withErrors(['gateway' => 'The payment gateway did not return a checkout URL.']);
        }

        $payment->update(['reference' => $reference, 'metadata' => array_merge($payment->metadata ?? [], $result)]);

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request, string $gateway)
    {
        $provider = in_array($gateway, PaymentGateway::PROVIDERS) ? $gateway : 'paystack';
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('tx_ref');

        if (! $reference) {
            abort(422, 'Missing payment reference.');
        }

        $payment = Payment::where('organization_id', currentOrganization()->id)
            ->where('reference', $reference)
            ->where('gateway', $provider)
            ->first();

        if (! $payment) {
            abort(404, 'Payment record not found.');
        }

        if ($payment->status === 'success') {
            return redirect()->route('billing.invoices')->with('success', 'Payment already confirmed.');
        }

        $gatewayConfig = PaymentGateway::where('organization_id', currentOrganization()->id)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (! $gatewayConfig) {
            abort(422, 'Payment gateway is not configured.');
        }

        try {
            $result = app(PaymentGatewayService::class)->verify($gatewayConfig, $reference);
        } catch (\Throwable $e) {
            return redirect()->route('billing.invoices')->withErrors(['gateway' => 'Could not verify payment: ' . $e->getMessage()]);
        }

        if (in_array($result['status'], ['success', 'successful'])) {
            app(BillingService::class)->finalizePayment($payment, 'card');

            return redirect()->route('billing.invoices')->with('success', 'Payment successful. Subscription activated!');
        }

        $payment->update(['status' => 'failed']);

        return redirect()->route('billing.invoices')->withErrors(['gateway' => 'Payment was not completed.']);
    }

    public function webhook(Request $request, string $gateway)
    {
        $provider = in_array($gateway, PaymentGateway::PROVIDERS) ? $gateway : 'paystack';

        $gatewayConfig = PaymentGateway::where('provider', $provider)->first();

        if (! $gatewayConfig || ! $gatewayConfig->is_active) {
            return response()->json(['status' => 'ignored'], 404);
        }

        try {
            $result = app(PaymentGatewayService::class)->handleWebhook(
                $gatewayConfig,
                $request->all(),
                array_merge($request->headers->all(), ['raw_body' => $request->getContent()])
            );
        } catch (\Throwable $e) {
            return response()->json(['status' => 'invalid'], 400);
        }

        $reference = $result['reference'] ?? null;

        if ($reference && $result['success'] ?? false) {
            $payment = Payment::where('reference', $reference)
                ->where('gateway', $provider)
                ->first();

            if ($payment && $payment->status !== 'success') {
                app(BillingService::class)->finalizePayment($payment, 'card');
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function invoices()
    {
        $invoices = Invoice::where('organization_id', currentOrganization()->id)
            ->with('subscription.plan')
            ->latest()
            ->get();

        return view('billing.invoices', compact('invoices'));
    }

    public function invoiceShow(Invoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);

        return view('billing.invoice-show', compact('invoice'));
    }

    public function payments()
    {
        $payments = Payment::where('organization_id', currentOrganization()->id)
            ->with('invoice')
            ->latest()
            ->get();

        return view('billing.payments', compact('payments'));
    }

    public function cancel(Request $request)
    {
        $subscription = currentOrganization()->subscription;

        if ($subscription) {
            app(BillingService::class)->cancel($subscription);
        }

        return back()->with('success', 'Subscription cancelled. You can resubscribe anytime.');
    }

    public function resume()
    {
        $subscription = currentOrganization()->subscription;

        if ($subscription && $subscription->status === 'cancelled' && $subscription->plan) {
            app(BillingService::class)->subscribe(currentOrganization(), $subscription->plan, $subscription->billing_cycle);
        }

        return back()->with('success', 'Subscription resumed successfully.');
    }

    /**
     * Roll a subscription back to its pre-checkout state after a gateway failure.
     *
     * @param  array|null  $previous  prior subscription row as array
     */
    private function restoreSubscription($subscription, ?array $previous): void
    {
        if (! $previous) {
            $subscription->delete();

            return;
        }

        $restore = collect($previous)->only([
            'plan_id', 'status', 'billing_cycle', 'starts_at',
            'ends_at', 'trial_ends_at', 'cancelled_at',
        ])->all();

        $subscription->update($restore);
    }
}
