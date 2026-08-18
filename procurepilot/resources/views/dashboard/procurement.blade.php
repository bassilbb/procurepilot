<x-app-layout>
    <x-slot name="header">Procurement Overview</x-slot>

    @php $org = currentOrganization(); @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-stat-card label="Suppliers" :value="$counts['suppliers']" tone="sky" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Tenders" :value="$counts['tenders']" tone="violet" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Open Tenders" :value="$openTenders" tone="rose" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Contracts" :value="$counts['contracts']" tone="emerald" href="{{ route('contracts.index') }}" />
        <x-stat-card label="Purchase Orders" :value="$counts['purchaseOrders']" tone="amber" href="{{ route('purchase-orders.index') }}" />
        <x-stat-card label="Budgets" :value="$counts['budgets']" tone="blue" href="{{ route('budgets.index') }}" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-stat-card label="Pending Supplier Approvals" :value="$pendingSuppliers" tone="amber" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Pending Requests" :value="$pendingRequests" tone="violet" href="{{ route('requests.index') }}" />
        <x-stat-card label="Draft Requests" :value="$draftRequests" tone="slate" href="{{ route('requests.index') }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="hd-card p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-900">Monthly Procurement Spend ({{ now()->year }})</h3>
                <span class="text-xs text-slate-500">{{ $org->currency }} {{ number_format(collect($monthlySpend)->sum(), 0) }}</span>
            </div>
            <div class="relative h-56">
                <canvas id="monthlySpendChart"></canvas>
            </div>
        </div>

        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Contract Value</h3>
            <div class="text-3xl font-bold text-slate-900">{{ $org->currency }} {{ number_format($contractValue, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Total active & draft contract value</div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Budget Utilization (Active)</h4>
                <div class="space-y-3">
                    @forelse ($budgets->take(4) as $budget)
                        @php
                            $spent = (float) $budget->spent_amount;
                            $allocated = (float) $budget->allocated_amount;
                            $pct = $allocated > 0 ? min(100, round(($spent / $allocated) * 100)) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-600 truncate">{{ $budget->name }}</span>
                                <span class="text-slate-400">{{ $pct }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-rose-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No active budgets.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="hd-card p-6 lg:col-span-2">
            <h3 class="font-semibold text-slate-900 mb-4">Recent Procurement Requests</h3>
            <div class="space-y-3">
                @forelse ($recentRequests as $request)
                    <a href="{{ route('requests.show', $request) }}" class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0 group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold {{ statusBadgeClass($request->status) }} border">
                            {{ strtoupper(substr($request->status, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate group-hover:text-emerald-700">{{ $request->title }}</div>
                            <div class="text-xs text-slate-400">{{ $request->reference }} · {{ $request->department?->name }} · {{ $org->currency }} {{ number_format((float) $request->estimated_cost, 0) }}</div>
                        </div>
                        <span class="text-xs text-emerald-600 font-medium shrink-0">View</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No requests yet. Create your first procurement request.</p>
                @endforelse
            </div>
        </div>

        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Contracts Expiring Soon</h3>
            <div class="space-y-3">
                @forelse ($expiringContracts as $contract)
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-200">
                        <div class="text-sm font-medium text-amber-900 truncate">{{ $contract->title }}</div>
                        <div class="text-xs text-amber-700 mt-1">{{ $contract->supplier?->name }}</div>
                        <div class="text-xs text-amber-600 mt-1">Expires {{ $contract->end_date?->format('j M Y') }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No contracts expiring in the next 60 days.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Recent Tenders</h4>
                <div class="space-y-2">
                    @forelse ($recentTenders as $tender)
                        <a href="{{ route('tenders.show', $tender) }}" class="flex items-center justify-between text-sm group">
                            <span class="text-slate-600 truncate group-hover:text-emerald-600">{{ $tender->reference }} — {{ $tender->title }}</span>
                            <span class="ml-2 shrink-0 text-xs {{ $tender->status === 'published' ? 'text-emerald-600' : 'text-slate-400' }}">{{ ucfirst($tender->status) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No tenders yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('monthlySpendChart');
        if (!el || typeof window.Chart === 'undefined') return;

        @php
            $chartLabels = collect(range(1, 12))->map(fn ($m) => date('M', mktime(0, 0, 0, $m, 1)))->all();
            $chartValues = collect(range(1, 12))->map(fn ($m) => (float) ($monthlySpend[sprintf('%02d', $m)] ?? 0))->all();
        @endphp

        const labels = @json($chartLabels);
        const values = @json($chartValues);

        new Chart(el, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Procurement Spend',
                    data: values,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    hoverBackgroundColor: '#059669',
                    borderRadius: 6,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` {{ $org->currency }} ${Number(ctx.parsed.y).toLocaleString()}`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (v) => Number(v).toLocaleString() },
                        grid: { color: 'rgba(148,163,184,.15)' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    });
    </script>
</x-app-layout>
