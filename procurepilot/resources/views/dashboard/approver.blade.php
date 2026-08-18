<x-app-layout>
    <x-slot name="header">Approvals Dashboard</x-slot>

    @php $org = currentOrganization(); @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
        <x-stat-card label="Requests Awaiting Approval" :value="$counts['requests']" tone="violet" href="{{ route('requests.index') }}" />
        <x-stat-card label="Recommended Awards" :value="$counts['awards']" tone="emerald" href="{{ route('awards.index') }}" />
        <x-stat-card label="Closed Tenders" :value="$counts['closedTenders']" tone="amber" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Supplier Approvals" :value="$counts['suppliers']" tone="sky" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Active Contracts" :value="$counts['contracts']" tone="rose" href="{{ route('contracts.index') }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="hd-card p-6 lg:col-span-2">
            <h3 class="font-semibold text-slate-900 mb-4">Awaiting Your Approval — Procurement Requests</h3>
            <div class="space-y-3">
                @forelse ($pendingRequestApprovals as $pendingRequest)
                    @if ($pendingRequest instanceof \App\Models\ProcurementRequest)
                        <a href="{{ route('requests.show', $pendingRequest) }}" class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0 group">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold {{ statusBadgeClass($pendingRequest->priority) }} border">
                                {{ strtoupper(substr($pendingRequest->priority, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-800 truncate group-hover:text-emerald-700">{{ $pendingRequest->title }}</div>
                                <div class="text-xs text-slate-400">{{ $pendingRequest->reference }} · {{ $pendingRequest->department?->name }} · {{ $org->currency }} {{ number_format((float) $pendingRequest->estimated_cost, 0) }}</div>
                            </div>
                            <span class="text-xs text-emerald-600 font-medium shrink-0">Review</span>
                        </a>
                    @endif
                @empty
                    <p class="text-sm text-slate-500">No procurement requests awaiting approval.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Recommended Awards</h4>
                <div class="space-y-2">
                    @forelse ($recommendedAwards as $award)
                        <a href="{{ route('awards.show', $award) }}" class="flex items-center justify-between text-sm group">
                            <span class="text-slate-600 truncate group-hover:text-emerald-600">{{ $award->tender?->reference }} — {{ $award->tender?->title }}</span>
                            <span class="ml-2 shrink-0 text-xs font-medium text-emerald-600">{{ $org->currency }} {{ number_format((float) $award->award_amount, 0) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No recommended awards.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Submitted Procurement Plans</h4>
                <div class="space-y-2">
                    @forelse ($submittedPlans as $plan)
                        <a href="{{ route('plans.show', $plan) }}" class="flex items-center justify-between text-sm group">
                            <span class="text-slate-600 truncate group-hover:text-emerald-600">{{ $plan->title }}</span>
                            <span class="ml-2 shrink-0 text-xs text-violet-600 font-medium">{{ $plan->fiscal_year }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No submitted plans.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Closed Tenders</h3>
            <div class="space-y-3">
                @forelse ($closedTenders as $tender)
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-200">
                        <div class="text-sm font-medium text-amber-900 truncate">{{ $tender->title }}</div>
                        <div class="text-xs text-amber-700 mt-1">{{ $tender->reference }}</div>
                        <a href="{{ route('tenders.show', $tender) }}" class="text-xs text-amber-800 font-medium mt-1 inline-block">Evaluate bids →</a>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No closed tenders awaiting evaluation.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Supplier Registration Approvals</h4>
                <div class="space-y-2">
                    @forelse ($pendingSuppliers as $supplier)
                        <a href="{{ route('suppliers.show', $supplier) }}" class="flex items-center justify-between text-sm group">
                            <span class="text-slate-600 truncate group-hover:text-emerald-600">{{ $supplier->name }}</span>
                            <span class="ml-2 shrink-0 text-xs text-amber-600 font-medium">Pending</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No suppliers awaiting approval.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Recent Decisions</h4>
                <div class="space-y-2">
                    @forelse ($recentDecisions as $decision)
                        <div class="text-xs">
                            <span class="text-slate-600">{{ $decision->approvable?->title ?? 'Decision' }}</span>
                            <span class="ml-1 {{ $decision->status === 'approved' ? 'text-emerald-600' : 'text-rose-600' }} font-medium">{{ ucfirst($decision->status) }}</span>
                            <div class="text-slate-400">{{ $decision->decided_at?->diffForHumans() }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No recent decisions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
