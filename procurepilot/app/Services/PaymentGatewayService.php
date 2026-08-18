<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class PaymentGatewayService
{
    public const PAYSTACK_BASE = 'https://api.paystack.co';
    public const FLUTTERWAVE_BASE = 'https://api.flutterwave.com/v3';
    public const MONO_BASE = 'https://api.withmono.com/v2';

    public function getActiveGateway(?string $provider = null): ?PaymentGateway
    {
        $org = currentOrganization();

        if (! $org) {
            return null;
        }

        $query = PaymentGateway::where('organization_id', $org->id)->where('is_active', true);

        if ($provider) {
            $query->where('provider', $provider);
        }

        return $query->whereNotNull('secret_key')->first();
    }

    /**
     * Initialize a payment and return the authorization/checkout URL.
     */
    public function initialize(PaymentGateway $gateway, array $payload): array
    {
        return match ($gateway->provider) {
            'paystack'   => $this->initializePaystack($gateway, $payload),
            'flutterwave' => $this->initializeFlutterwave($gateway, $payload),
            'mono'       => $this->initializeMono($gateway, $payload),
            default      => throw new \InvalidArgumentException('Unsupported payment provider: '.$gateway->provider),
        };
    }

    /**
     * Verify a previously initiated transaction.
     *
     * @return array{status: string, reference: string, amount: ?float, currency: ?string, raw: array}
     */
    public function verify(PaymentGateway $gateway, string $reference): array
    {
        return match ($gateway->provider) {
            'paystack'   => $this->verifyPaystack($gateway, $reference),
            'flutterwave' => $this->verifyFlutterwave($gateway, $reference),
            'mono'       => $this->verifyMono($gateway, $reference),
            default      => throw new \InvalidArgumentException('Unsupported payment provider: '.$gateway->provider),
        };
    }

    /**
     * Validate an incoming webhook. Returns normalized payload or throws.
     */
    public function handleWebhook(PaymentGateway $gateway, array $payload, array $headers): array
    {
        return match ($gateway->provider) {
            'paystack'   => $this->webhookPaystack($gateway, $payload, $headers),
            'flutterwave' => $this->webhookFlutterwave($gateway, $payload, $headers),
            'mono'       => $this->webhookMono($gateway, $payload, $headers),
            default      => throw new \InvalidArgumentException('Unsupported payment provider: '.$gateway->provider),
        };
    }

    /* ------------------------------------------------------------------ */
    /* Paystack                                                            */
    /* ------------------------------------------------------------------ */

    private function initializePaystack(PaymentGateway $gateway, array $payload): array
    {
        $response = Http::withToken($gateway->secret_key)
            ->acceptJson()
            ->post(self::PAYSTACK_BASE.'/transaction/initialize', [
                'email'         => $payload['email'],
                'amount'        => (int) round((float) $payload['amount'] * 100), // kobo
                'currency'      => $payload['currency'] ?? 'NGN',
                'reference'     => $payload['reference'],
                'callback_url'  => $payload['callback_url'],
                'metadata'      => $payload['metadata'] ?? [],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Paystack initialize failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        return [
            'authorization_url' => $data['authorization_url'] ?? null,
            'access_code'       => $data['access_code'] ?? null,
            'reference'         => $data['reference'] ?? $payload['reference'],
        ];
    }

    private function verifyPaystack(PaymentGateway $gateway, string $reference): array
    {
        $response = Http::withToken($gateway->secret_key)
            ->acceptJson()
            ->get(self::PAYSTACK_BASE.'/transaction/verify/'.$reference);

        if ($response->failed()) {
            throw new \RuntimeException('Paystack verify failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        return [
            'status'    => strtolower((string) ($data['status'] ?? 'unknown')),
            'reference' => $reference,
            'amount'    => isset($data['amount']) ? (float) $data['amount'] / 100 : null,
            'currency'  => $data['currency'] ?? null,
            'raw'       => $data,
        ];
    }

    private function webhookPaystack(PaymentGateway $gateway, array $payload, array $headers): array
    {
        $raw = $headers['raw_body'] ?? '';

        $signature = $headers['x-paystack-signature'] ?? null;
        $computed = hash_hmac('sha512', $raw, (string) $gateway->secret_key);

        if (! $signature || ! hash_equals($computed, $signature)) {
            throw new \RuntimeException('Invalid Paystack webhook signature');
        }

        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        if ($event !== 'charge.success') {
            return ['event' => $event, 'success' => false];
        }

        return [
            'event'     => $event,
            'success'   => strtolower((string) ($data['status'] ?? '')) === 'success',
            'reference' => $data['reference'] ?? null,
            'amount'    => isset($data['amount']) ? (float) $data['amount'] / 100 : null,
            'currency'  => $data['currency'] ?? null,
            'raw'       => $data,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Flutterwave                                                        */
    /* ------------------------------------------------------------------ */

    private function initializeFlutterwave(PaymentGateway $gateway, array $payload): array
    {
        $response = Http::withToken($gateway->secret_key)
            ->acceptJson()
            ->post(self::FLUTTERWAVE_BASE.'/payments', [
                'tx_ref'          => $payload['reference'],
                'amount'          => (float) $payload['amount'],
                'currency'        => $payload['currency'] ?? 'NGN',
                'redirect_url'    => $payload['callback_url'],
                'customer'        => [
                    'email' => $payload['email'],
                    'name'  => $payload['name'] ?? null,
                ],
                'customizations'  => [
                    'title'       => $payload['description'] ?? 'Subscription Payment',
                    'description' => $payload['description'] ?? '',
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Flutterwave initialize failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        return [
            'authorization_url' => $data['link'] ?? null,
            'reference'         => $data['tx_ref'] ?? $payload['reference'],
        ];
    }

    private function verifyFlutterwave(PaymentGateway $gateway, string $reference): array
    {
        $response = Http::withToken($gateway->secret_key)
            ->acceptJson()
            ->get(self::FLUTTERWAVE_BASE.'/transactions/verify_by_reference', [
                'tx_ref' => $reference,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Flutterwave verify failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        return [
            'status'    => strtolower((string) ($data['status'] ?? 'unknown')),
            'reference' => $reference,
            'amount'    => isset($data['amount']) ? (float) $data['amount'] : null,
            'currency'  => $data['currency'] ?? null,
            'raw'       => $data,
        ];
    }

    private function webhookFlutterwave(PaymentGateway $gateway, array $payload, array $headers): array
    {
        $verifHash = $headers['verif-hash'] ?? null;

        if (! $verifHash || ! hash_equals((string) $verifHash, (string) $gateway->secret_key)) {
            throw new \RuntimeException('Invalid Flutterwave webhook hash');
        }

        $data = $payload['data'] ?? [];

        return [
            'event'     => $payload['event'] ?? 'unknown',
            'success'   => ($payload['event'] ?? '') === 'charge.completed'
                && strtolower((string) ($data['status'] ?? '')) === 'successful',
            'reference' => $data['tx_ref'] ?? null,
            'amount'    => isset($data['amount']) ? (float) $data['amount'] : null,
            'currency'  => $data['currency'] ?? null,
            'raw'       => $data,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Mono                                                               */
    /* ------------------------------------------------------------------ */

    private function initializeMono(PaymentGateway $gateway, array $payload): array
    {
        $response = Http::withHeaders(['mono-sec-key' => $gateway->secret_key])
            ->acceptJson()
            ->post(self::MONO_BASE.'/payments/initiate', [
                'amount'        => (float) $payload['amount'],
                'currency'      => $payload['currency'] ?? 'NGN',
                'type'          => 'one-time',
                'reference'     => $payload['reference'],
                'description'   => $payload['description'] ?? 'Subscription Payment',
                'redirect_url'  => $payload['callback_url'],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Mono initialize failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        return [
            'authorization_url' => $data['checkout_url'] ?? $data['checkoutUrl'] ?? null,
            'reference'         => $data['reference'] ?? $payload['reference'],
        ];
    }

    private function verifyMono(PaymentGateway $gateway, string $reference): array
    {
        $response = Http::withHeaders(['mono-sec-key' => $gateway->secret_key])
            ->acceptJson()
            ->get(self::MONO_BASE.'/payments/transactions/'.$reference);

        if ($response->failed()) {
            throw new \RuntimeException('Mono verify failed: '.$response->body());
        }

        $data = $response->json('data') ?? [];

        return [
            'status'    => strtolower((string) ($data['status'] ?? 'unknown')),
            'reference' => $reference,
            'amount'    => isset($data['amount']) ? (float) $data['amount'] : null,
            'currency'  => $data['currency'] ?? null,
            'raw'       => $data,
        ];
    }

    private function webhookMono(PaymentGateway $gateway, array $payload, array $headers): array
    {
        $signature = $headers['x-mono-signature'] ?? null;
        $raw = $headers['raw_body'] ?? '';

        $computed = hash_hmac('sha512', $raw, (string) $gateway->secret_key);

        if (! $signature || ! hash_equals($computed, $signature)) {
            throw new \RuntimeException('Invalid Mono webhook signature');
        }

        return [
            'event'     => $payload['event'] ?? 'unknown',
            'success'   => ($payload['event'] ?? '') === 'payment.success',
            'reference' => $payload['data']['reference'] ?? $payload['data']['id'] ?? null,
            'amount'    => isset($payload['data']['amount']) ? (float) $payload['data']['amount'] : null,
            'currency'  => $payload['data']['currency'] ?? null,
            'raw'       => $payload['data'] ?? [],
        ];
    }
}
