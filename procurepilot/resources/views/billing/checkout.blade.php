<x-app-layout>
    <x-slot name="header">Checkout</x-slot>

    <x-page-header :title="$plan->name . ' Plan — ' . ucfirst($cycle) . ' billing'" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Order Summary</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Plan</dt><dd class="font-medium">{{ $plan->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Billing Cycle</dt><dd class="font-medium">{{ ucfirst($cycle) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Organization</dt><dd class="font-medium">{{ $org->name }}</dd></div>
                <div class="flex justify-between pt-3 border-t border-slate-100 text-base"><dt class="font-semibold text-slate-900">Amount Due</dt><dd class="font-bold text-slate-900">{{ $plan->currency }} {{ number_format($plan->priceFor($cycle), 2) }}</dd></div>
            </dl>

            @if ($gateways->isNotEmpty())
                <div class="mt-6 p-4 rounded-lg bg-emerald-50 text-xs text-emerald-700">
                    <strong>Secure checkout:</strong> you will be redirected to the selected payment provider to complete your payment securely.
                </div>
            @else
                <div class="mt-6 p-4 rounded-lg bg-slate-50 text-xs text-slate-500">
                    <strong>Demo payment gateway:</strong> use any card number (e.g. 4242 4242 4242 4242), any future expiry, any 3-digit CVC.
                </div>
            @endif
        </div>

        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Payment Details</h3>

            <form method="POST" action="{{ route('billing.subscribe', $plan) }}" class="space-y-4" x-data="{ gateway: {{ $gateways->isNotEmpty() ? "'" . $gateways->first()->provider . "'" : "'demo'" }} }">
                @csrf
                <input type="hidden" name="cycle" value="{{ $cycle }}">

                <div>
                    <x-input-label value="Payment Method" />
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        @forelse ($gateways as $gw)
                            <label class="cursor-pointer">
                                <input type="radio" name="gateway" value="{{ $gw->provider }}" class="sr-only peer" x-model="gateway">
                                <span class="block px-4 py-3 rounded-xl border-2 border-slate-200 text-sm font-medium capitalize peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition">
                                    {{ $gw->provider }}
                                    <span class="block text-xs font-normal text-slate-400 mt-0.5">Redirect to {{ ucfirst($gw->provider) }}</span>
                                </span>
                            </label>
                        @empty
                            <label class="cursor-pointer">
                                <input type="radio" name="gateway" value="demo" class="sr-only peer" x-model="gateway">
                                <span class="block px-4 py-3 rounded-xl border-2 border-slate-200 text-sm font-medium capitalize peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition">
                                    Demo
                                    <span class="block text-xs font-normal text-slate-400 mt-0.5">Simulated card payment</span>
                                </span>
                            </label>
                        @endforelse
                    </div>
                    @error('gateway')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div x-show="gateway === 'demo'" x-cloak>
                    <div>
                        <x-input-label for="card_name" value="Cardholder Name" />
                        <x-text-input id="card_name" name="card_name" class="mt-1 w-full" x-bind:required="gateway === 'demo'" placeholder="e.g. Jane Doe" value="{{ old('card_name', auth()->user()->name) }}" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="card_number" value="Card Number" />
                        <x-text-input id="card_number" name="card_number" class="mt-1 w-full font-mono" x-bind:required="gateway === 'demo'" placeholder="4242 4242 4242 4242" value="{{ old('card_number') }}" />
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="card_expiry" value="Expiry (MM/YY)" />
                            <x-text-input id="card_expiry" name="card_expiry" class="mt-1 w-full" x-bind:required="gateway === 'demo'" placeholder="12/28" value="{{ old('card_expiry') }}" />
                        </div>
                        <div>
                            <x-input-label for="card_cvc" value="CVC" />
                            <x-text-input id="card_cvc" name="card_cvc" class="mt-1 w-full" x-bind:required="gateway === 'demo'" placeholder="123" value="{{ old('card_cvc') }}" />
                        </div>
                    </div>
                </div>

                <button class="w-full px-5 py-3 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                    Pay {{ $plan->currency }} {{ number_format($plan->priceFor($cycle), 2) }} and Subscribe
                </button>
                <a href="{{ route('billing.plan') }}" class="block text-center text-sm text-slate-500 hover:text-slate-700">← Back to plans</a>
            </form>
        </div>
    </div>
</x-app-layout>
