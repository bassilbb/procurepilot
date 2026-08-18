<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Subscription;
use Carbon\Carbon;

class BillingService
{
    public function subscribe(Organization $org, $plan, string $cycle = 'monthly', string $status = 'active'): Subscription
    {
        $now = Carbon::now();

        $subscription = Subscription::updateOrCreate(
            ['organization_id' => $org->id],
            [
                'plan_id'        => $plan->id,
                'billing_cycle'  => $cycle,
                'status'         => $status,
                'starts_at'      => $now,
                'ends_at'        => $cycle === 'yearly' ? $now->copy()->addYear() : $now->copy()->addMonth(),
                'trial_ends_at'  => null,
                'cancelled_at'   => null,
            ]
        );

        $this->createInvoice($org, $subscription, $plan, $cycle);

        return $subscription;
    }

    public function startTrial(Organization $org, $plan): Subscription
    {
        $now = Carbon::now();
        $days = (int) ($plan->trial_days ?? 14);

        return Subscription::updateOrCreate(
            ['organization_id' => $org->id],
            [
                'plan_id'       => $plan->id,
                'billing_cycle' => 'monthly',
                'status'        => 'trialing',
                'starts_at'     => $now,
                'ends_at'       => $now->copy()->addDays($days),
                'trial_ends_at' => $now->copy()->addDays($days),
                'cancelled_at'  => null,
            ]
        );
    }

    public function createInvoice(Organization $org, Subscription $subscription, $plan, string $cycle): Invoice
    {
        $price = (float) $plan->priceFor($cycle);

        return Invoice::create([
            'organization_id'  => $org->id,
            'subscription_id'  => $subscription->id,
            'number'           => ReferenceService::invoice($org),
            'title'            => "{$plan->name} Plan — " . ucfirst($cycle) . ' subscription',
            'amount'           => $price,
            'currency'         => $plan->currency,
            'status'           => 'pending',
            'due_at'           => Carbon::now()->addDays(7),
        ]);
    }

    public function recordPayment(Invoice $invoice, string $method = 'card', string $gateway = 'demo', ?string $reference = null): Payment
    {
        $payment = Payment::create([
            'organization_id' => $invoice->organization_id,
            'invoice_id'      => $invoice->id,
            'amount'          => $invoice->amount,
            'currency'        => $invoice->currency,
            'method'          => $method,
            'gateway'         => $gateway,
            'reference'       => $reference ?? ReferenceService::payment($invoice->organization),
            'status'          => 'success',
            'paid_at'         => Carbon::now(),
        ]);

        $this->finalizePayment($payment, $method);

        return $payment;
    }

    /**
     * Create a pending payment row ahead of redirecting to the gateway.
     */
    public function createPendingPayment(Invoice $invoice, string $gateway, string $reference): Payment
    {
        return Payment::create([
            'organization_id' => $invoice->organization_id,
            'invoice_id'      => $invoice->id,
            'amount'          => $invoice->amount,
            'currency'        => $invoice->currency,
            'method'          => 'card',
            'gateway'         => $gateway,
            'reference'       => $reference,
            'status'          => 'pending',
        ]);
    }

    /**
     * Mark a payment successful and activate the related subscription.
     */
    public function finalizePayment(Payment $payment, string $method = 'card'): Payment
    {
        $payment->update([
            'status'  => 'success',
            'method'  => $method,
            'paid_at' => Carbon::now(),
        ]);

        $payment->invoice?->update([
            'status'         => 'paid',
            'paid_at'        => Carbon::now(),
            'payment_method' => $method,
            'transaction_ref'=> $payment->reference,
        ]);

        $subscription = $payment->invoice?->subscription;
        if ($subscription && in_array($subscription->status, ['trialing', 'past_due', 'pending', 'cancelled'])) {
            $subscription->update(['status' => 'active', 'trial_ends_at' => null]);
        }

        return $payment;
    }

    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status'       => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);

        return $subscription;
    }
}
