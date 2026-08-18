<x-app-layout>
    <x-slot name="header">Overview</x-slot>

    @php
        $org = currentOrganization();
        $sub = $org?->subscription;
    @endphp

    @if ($sub && $sub->status === 'trialing')
        <div class="mb-6 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Trial period active</h2>
                    <p class="text-indigo-100 text-sm mt-1">
                        Your {{ $sub->plan->name }} plan trial ends {{ $sub->trial_ends_at?->format('j M Y') }}. Choose a plan to keep full access.
                    </p>
                </div>
                <a href="{{ route('billing.plan') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                    Choose a plan →
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-stat-card label="Suppliers" :value="$counts['suppliers']" tone="sky" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Tenders" :value="$counts['tenders']" tone="violet" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Bids" :value="$counts['bids']" tone="blue" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Contracts" :value="$counts['contracts']" tone="emerald" href="{{ route('contracts.index') }}" />
        <x-stat-card label="Purchase Orders" :value="$counts['purchaseOrders']" tone="amber" href="{{ route('purchase-orders.index') }}" />
        <x-stat-card label="Open Tenders" :value="$openTenders" tone="rose" href="{{ route('tenders.index') }}" />
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
                <h4 class="text-sm font-medium text-slate-600 mb-3">Pending Approvals</h4>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-bold">{{ $pendingApprovals }}</div>
                    <p class="text-sm text-slate-500">items require Tenders Board attention</p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Awaiting Request Approval</h4>
                <div class="space-y-2">
                    @forelse ($pendingRequestApprovals as $pendingRequest)
                        @if ($pendingRequest instanceof \App\Models\ProcurementRequest)
                            <a href="{{ route('requests.show', $pendingRequest) }}" class="flex items-center justify-between text-sm group">
                                <span class="text-slate-600 truncate group-hover:text-emerald-600">{{ $pendingRequest->reference }}</span>
                                <span class="ml-2 shrink-0 font-medium text-amber-600">{{ number_format((float) $pendingRequest->estimated_cost, 0) }}</span>
                            </a>
                        @endif
                    @empty
                        <p class="text-sm text-slate-500">No requests awaiting approval.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="hd-card p-6 lg:col-span-2">
            <h3 class="font-semibold text-slate-900 mb-4">Recent Activity</h3>
            <div class="space-y-3">
                @forelse ($recent as $item)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold
                            {{ $item instanceof \App\Models\Tender ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $item instanceof \App\Models\Tender ? 'T' : 'C' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate">{{ $item->title }}</div>
                            <div class="text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</div>
                        </div>
                        @if ($item instanceof \App\Models\Tender)
                            <a href="{{ route('tenders.show', $item) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View</a>
                        @else
                            <a href="{{ route('contracts.show', $item) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View</a>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No activity yet. Create your first tender to get started.</p>
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
                    <p class="text-sm text-slate-500">No contracts expiring in the next 30 days.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">Recent Invoices</h4>
                <div class="space-y-2">
                    @forelse ($recentInvoices as $invoice)
                        <a href="{{ route('billing.invoice-show', $invoice) }}" class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ $invoice->number }}</span>
                            <span class="font-medium {{ $invoice->isPaid() ? 'text-emerald-600' : 'text-slate-500' }}">{{ $org->currency }} {{ number_format($invoice->amount, 0) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No invoices yet.</p>
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

    @if (auth()->user()->isAdmin() && ! empty($requirements))
        <div class="hd-card p-6 mt-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Supplier Registration Requirements</h3>
                    <p class="text-sm text-slate-500 mt-1">Documents suppliers must upload to register. Uploads feed each supplier's compliance checklist.</p>
                </div>
                <a href="{{ route('settings.requirements.pdf') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Download PDF
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="space-y-2.5">
                        @foreach ($requirements as $requirement)
                            <div class="flex items-center gap-3 p-3 rounded-lg border {{ $requirement->is_required ? 'border-slate-200 bg-slate-50' : 'border-dashed border-slate-200 bg-slate-50/40' }}">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $requirement->is_required ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500' }}">{{ $loop->iteration }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-slate-800">{{ $requirement->name }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-medium {{ $requirement->is_required ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $requirement->is_required ? 'Required' : 'Optional' }}
                                        </span>
                                    </div>
                                    @if ($requirement->description)
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $requirement->description }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-500 shrink-0">{{ $requirement->documents_count }} uploads</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <form method="POST" action="{{ route('settings.requirements.store') }}" class="rounded-xl border border-dashed border-slate-300 bg-slate-50/60 p-4 space-y-3">
                        @csrf
                        <h4 class="text-sm font-semibold text-slate-700">Add a Requirement</h4>
                        <div>
                            <x-input-label for="dash_req_name" value="Document Name" />
                            <x-text-input id="dash_req_name" name="name" class="mt-1 w-full" placeholder="e.g. Tax Clearance Certificate" required />
                        </div>
                        <div>
                            <x-input-label for="dash_req_desc" value="Description (optional)" />
                            <x-text-input id="dash_req_desc" name="description" class="mt-1 w-full" placeholder="Purpose of this document" />
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_required" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-slate-600">Required on registration</span>
                        </label>
                        <button class="w-full px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Add Requirement</button>
                        <p class="text-[11px] text-slate-400">Manage all requirements under Settings → Supplier Registration Requirements.</p>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
