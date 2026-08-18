<x-app-layout>
    <x-slot name="header">Billing & Subscriptions</x-slot>

    <x-page-header title="Choose Your Plan" description="Subscription plans for procurement teams of every size" />

    @php
        $sub = currentOrganization()?->subscription;
        $currentPlan = $sub?->plan;
    @endphp

    @if ($sub && $sub->status === 'cancelled')
        <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-5 flex items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-amber-900">Your subscription is cancelled</h3>
                <p class="text-sm text-amber-700 mt-1">Resume to keep full access to procurement features.</p>
            </div>
            <form method="POST" action="{{ route('billing.resume') }}">
                @csrf
                <button class="px-4 py-2 bg-amber-600 text-white text-sm rounded-lg">Resume Subscription</button>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($plans as $plan)
            <div class="relative {{ $currentPlan?->id === $plan->id ? 'is-current' : '' }} hd-card hd-card-strong p-6 flex flex-col">
                @if ($plan->is_popular)
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-violet-600 text-white text-xs font-semibold rounded-full z-20 shadow-lg shadow-violet-600/30 ring-2 ring-white">Most Popular</span>
                @endif
                @if ($currentPlan?->id === $plan->id)
                    <span class="absolute -top-3 right-4 px-3 py-1 bg-emerald-600 text-white text-xs font-semibold rounded-full z-20 shadow-lg shadow-emerald-600/30 ring-2 ring-white">Current Plan</span>
                @endif
                <span class="hd-card-title mb-2" style="font-size:1.05rem;">{{ $plan->name }}</span>
                <p class="text-sm text-slate-500 mt-1">{{ $plan->description }}</p>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-slate-900">{{ $plan->currency }} {{ number_format($plan->price_monthly, 0) }}<span class="text-sm font-normal text-slate-400">/month</span></div>
                    <div class="text-sm text-slate-500 mt-1">or {{ $plan->currency }} {{ number_format($plan->price_yearly, 0) }}/year</div>
                </div>
                <ul class="mt-6 space-y-2 flex-1 text-sm">
                    @foreach (($plan->features ?? []) as $feature)
                        <li class="flex items-start gap-2 text-slate-600">
                            <span class="text-emerald-500 mt-0.5">✓</span>{{ $feature }}
                        </li>
                    @endforeach
                </ul>
                @if ($currentPlan?->id === $plan->id && $sub && $sub->status !== 'cancelled')
                    <div class="mt-6">
                        <span class="block w-full text-center px-4 py-2.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg">
                            {{ $sub->status === 'trialing' ? 'Trial until ' . $sub->trial_ends_at?->format('j M Y') : 'Active — ' . ucfirst($sub->billing_cycle) }}
                        </span>
                        @if ($sub->status !== 'trialing')
                            <form method="POST" action="{{ route('billing.cancel') }}" class="mt-2" onsubmit="return confirm('Cancel your subscription?')">
                                @csrf
                                <button class="w-full px-4 py-2 text-red-600 text-sm border border-red-200 rounded-lg hover:bg-red-50">Cancel Subscription</button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-2 gap-2">
                        <a href="{{ route('billing.checkout', ['plan' => $plan, 'cycle' => 'monthly']) }}" class="text-center px-4 py-2.5 {{ $plan->is_popular ? 'bg-violet-600 hover:bg-violet-700' : 'bg-slate-900 hover:bg-slate-800' }} text-white text-sm font-medium rounded-lg">Monthly</a>
                        <a href="{{ route('billing.checkout', ['plan' => $plan, 'cycle' => 'yearly']) }}" class="text-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Yearly</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8 hd-card p-6">
        <h3 class="font-semibold text-slate-900 mb-4">Billing History</h3>
        <div class="flex gap-4 text-sm">
            <a href="{{ route('billing.invoices') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Invoices →</a>
            <a href="{{ route('billing.payments') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Payments →</a>
        </div>
    </div>
</x-app-layout>
