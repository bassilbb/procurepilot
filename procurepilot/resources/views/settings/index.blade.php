<x-app-layout>
    <x-slot name="header">Settings</x-slot>

    <x-page-header title="Organization Settings" description="Manage your organization profile and defaults" />

    <form method="POST" action="{{ route('settings.organization.update') }}" class="hd-card p-6 max-w-3xl">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="name" value="Organization Name" />
                <x-text-input id="name" name="name" class="mt-1 w-full" required value="{{ $org->name }}" />
            </div>
            <div>
                <x-input-label for="email" value="Organization Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 w-full" value="{{ $org->email }}" />
            </div>
            <div>
                <x-input-label for="phone" value="Phone" />
                <x-text-input id="phone" name="phone" class="mt-1 w-full" value="{{ $org->phone }}" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="address" value="Address" />
                <textarea id="address" name="address" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ $org->address }}</textarea>
            </div>
            <div>
                <x-input-label for="country" value="Country" />
                <x-text-input id="country" name="country" class="mt-1 w-full" value="{{ $org->country }}" />
            </div>
            <div>
                <x-input-label for="currency" value="Default Currency" />
                <select id="currency" name="currency" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach (['NGN' => 'Nigerian Naira (₦)', 'USD' => 'US Dollar ($)', 'EUR' => 'Euro (€)', 'GBP' => 'British Pound (£)', 'GHS' => 'Ghana Cedi (GH₵)', 'KES' => 'Kenyan Shilling (KSh)'] as $code => $label)
                        <option value="{{ $code }}" @selected($org->currency === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <x-input-label for="tax_id" value="Tax Identification Number" />
                <x-text-input id="tax_id" name="tax_id" class="mt-1 w-full" value="{{ $org->tax_id }}" />
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Settings</button>
        </div>
    </form>

    <div class="hd-card p-6 max-w-3xl mt-6">
        <h3 class="font-semibold text-slate-900 mb-2">Subscription Status</h3>
        @php $sub = $org->subscription; @endphp
        @if ($sub)
            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div><dt class="text-slate-500">Plan</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $sub->plan?->name }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd class="font-medium text-slate-900 mt-0.5">{{ ucfirst($sub->status) }}</dd></div>
                <div><dt class="text-slate-500">Cycle</dt><dd class="font-medium text-slate-900 mt-0.5">{{ ucfirst($sub->billing_cycle) }}</dd></div>
                <div><dt class="text-slate-500">Renews</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $sub->ends_at?->format('d M Y') ?? '—' }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-slate-500">No active subscription.</p>
        @endif
    </div>

    <div class="hd-card p-6 max-w-3xl mt-6">
        <h3 class="font-semibold text-slate-900 mb-1">Payment Gateways</h3>
        <p class="text-sm text-slate-500 mb-4">Connect Paystack, Flutterwave or Mono to accept subscription payments. Customers are redirected to the selected provider at checkout.</p>

        @php
            $gatewayLabels = [
                'paystack'    => ['Paystack', 'card.png', 'p'],
                'flutterwave' => ['Flutterwave', 'card.png', 'f'],
                'mono'        => ['Mono', 'card.png', 'm'],
            ];
        @endphp

        <form method="POST" action="{{ route('settings.gateways.update') }}" class="space-y-5">
            @csrf @method('PUT')

            @foreach (['paystack', 'flutterwave', 'mono'] as $provider)
                @php
                    $gw = $gateways->get($provider);
                @endphp
                <div class="rounded-xl border border-slate-200 p-4 {{ $gw?->is_active ? 'border-emerald-300 bg-emerald-50/40' : '' }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $provider === 'paystack' ? 'from-sky-500 to-indigo-600' : ($provider === 'flutterwave' ? 'from-amber-500 to-orange-600' : 'from-slate-600 to-slate-800') }} text-white text-sm font-bold flex items-center justify-center shadow-sm">{{ $gatewayLabels[$provider][2] }}</span>
                            <div>
                                <h4 class="font-semibold text-slate-900 text-sm">{{ $gatewayLabels[$provider][0] }}</h4>
                                <p class="text-xs text-slate-400">Get keys from your {{ $gatewayLabels[$provider][0] }} dashboard</p>
                            </div>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="gateways[{{ $provider }}][is_active]" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked($gw?->is_active)>
                            <span class="text-slate-600">Active</span>
                        </label>
                    </div>

                    <input type="hidden" name="gateways[{{ $provider }}][provider]" value="{{ $provider }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :for="'gpk_' . $provider" :value="$provider === 'mono' ? 'Public / Live Key' : 'Public Key'" />
                            <x-text-input :id="'gpk_' . $provider" :name="'gateways[' . $provider . '][public_key]'" class="mt-1 w-full font-mono text-xs" :value="$gw?->public_key" placeholder="pk_..." autocomplete="off" />
                        </div>
                        <div>
                            <x-input-label :for="'gsk_' . $provider" value="Secret Key" />
                            <x-text-input :id="'gsk_' . $provider" :name="'gateways[' . $provider . '][secret_key]'" class="mt-1 w-full font-mono text-xs" :value="$gw?->secret_key" placeholder="sk_..." autocomplete="off" />
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end pt-2">
                <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Gateways</button>
            </div>
        </form>
    </div>

    <div class="hd-card p-6 max-w-3xl mt-6">
        <h3 class="font-semibold text-slate-900 mb-1">Supplier Registration Requirements</h3>
        <p class="text-sm text-slate-500 mb-4">Define the documents suppliers must upload when registering. Each requirement appears as its own upload field on the registration form and in the supplier compliance checklist.</p>

        <form method="POST" action="{{ route('settings.requirements.store') }}" class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-4 mb-5 space-y-3">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <x-input-label for="req_name" value="Document Name" />
                    <x-text-input id="req_name" name="name" class="mt-1 w-full" placeholder="e.g. CAC Certificate" required />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="req_description" value="Description (optional)" />
                    <x-text-input id="req_description" name="description" class="mt-1 w-full" placeholder="What this document is for" />
                </div>
            </div>
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_required" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-slate-600">Required on registration form</span>
                </label>
                <button class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Add Requirement</button>
            </div>
        </form>

        @if ($requirements->isEmpty())
            <p class="text-sm text-slate-500">No requirements yet. Add the first one above — suppliers will see it on the registration form immediately.</p>
        @else
            <div class="space-y-3">
                @foreach ($requirements as $requirement)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <form method="POST" action="{{ route('settings.requirements.update', $requirement) }}" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <input type="text" name="name" value="{{ $requirement->name }}" class="text-sm font-semibold text-slate-900 rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-medium {{ $requirement->is_required ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $requirement->is_required ? 'Required' : 'Optional' }}
                                        </span>
                                    </div>
                                    <input type="text" name="description" value="{{ $requirement->description }}" placeholder="Description (optional)" class="mt-2 w-full text-sm rounded-md border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                                        <input type="checkbox" name="is_required" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked($requirement->is_required)>
                                        Required
                                    </label>
                                    <button class="px-3 py-1.5 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save</button>
                                    <button type="button" onclick="event.preventDefault(); document.getElementById('del-req-{{ $requirement->id }}').submit();" class="px-3 py-1.5 text-xs text-red-600 border border-red-200 rounded-lg hover:bg-red-50">Delete</button>
                                </div>
                            </div>
                        </form>
                        <form id="del-req-{{ $requirement->id }}" method="POST" action="{{ route('settings.requirements.destroy', $requirement) }}" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
