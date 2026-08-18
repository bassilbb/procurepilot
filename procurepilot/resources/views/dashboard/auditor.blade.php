<x-app-layout>
    <x-slot name="header">Audit & Compliance Overview</x-slot>

    @php $org = currentOrganization(); @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-stat-card label="Audit Log Entries" :value="$counts['auditLogs']" tone="violet" href="{{ route('audit.index') }}" />
        <x-stat-card label="Contracts" :value="$counts['contracts']" tone="emerald" href="{{ route('contracts.index') }}" />
        <x-stat-card label="Supplier Invoices" :value="$counts['invoices']" tone="amber" href="{{ route('invoices.index') }}" />
        <x-stat-card label="Tenders" :value="$counts['tenders']" tone="sky" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Suppliers" :value="$counts['suppliers']" tone="blue" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Procurement Requests" :value="$counts['requests']" tone="rose" href="{{ route('requests.index') }}" />
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
                <h4 class="text-sm font-medium text-slate-600 mb-3">Invoice Three-Way Matching</h4>
                <div class="flex items-center gap-4">
                    <div class="flex-1 p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                        <div class="text-xl font-bold text-emerald-700">{{ $matchedInvoices }}</div>
                        <div class="text-xs text-emerald-600 mt-0.5">Fully matched</div>
                    </div>
                    <div class="flex-1 p-3 rounded-lg bg-amber-50 border border-amber-200">
                        <div class="text-xl font-bold text-amber-700">{{ $unmatchedInvoices }}</div>
                        <div class="text-xs text-amber-600 mt-0.5">Pending / unmatched</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Spend by Status</h4>
                <div class="space-y-2">
                    @forelse ($spendByStatus as $status => $total)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                            <span class="font-medium text-slate-900">{{ $org->currency }} {{ number_format((float) $total, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No purchase order spend yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Recent Audit Activity</h3>
            <div class="space-y-3">
                @forelse ($recentAuditLogs as $log)
                    <div class="flex items-start gap-3 py-2 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">{{ $log->action ? substr(strtoupper($log->action), 0, 3) : 'LOG' }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-slate-700 break-words">{{ $log->action }}</div>
                            <div class="text-xs text-slate-400">{{ $log->user?->name }} · {{ $log->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No audit activity yet.</p>
                @endforelse
            </div>
        </div>

        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Active Contracts</h3>
            <div class="space-y-3">
                @forelse ($contracts as $contract)
                    <a href="{{ route('contracts.show', $contract) }}" class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0 group">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate group-hover:text-emerald-700">{{ $contract->title }}</div>
                            <div class="text-xs text-slate-400">{{ $contract->supplier?->name }} · Expires {{ $contract->end_date?->format('j M Y') }}</div>
                        </div>
                        <span class="text-xs font-medium text-emerald-600 shrink-0">{{ $org->currency }} {{ number_format((float) $contract->value, 0) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No active contracts.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Compliance</h4>
                <a href="{{ route('audit.compliance') }}" class="inline-flex items-center gap-2 text-sm text-emerald-600 font-medium hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    View compliance dashboard
                </a>
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
                    backgroundColor: 'rgba(139, 92, 246, 0.75)',
                    hoverBackgroundColor: '#6d28d9',
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
