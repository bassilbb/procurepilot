<x-app-layout>
    <x-slot name="header">Supplier Invoices</x-slot>

    <x-page-header title="Supplier Invoices" description="Invoice verification and three-way matching">
        <x-slot name="actions">
            <form method="GET" action="{{ route('invoices.index') }}" class="flex gap-2">
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords($status) }}</option>
                    @endforeach
                </select>
                <select name="match_status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Any match</option>
                    @foreach ($matchStatuses as $ms)
                        <option value="{{ $ms }}" @selected(request('match_status') === $ms)>{{ ucwords($ms === 'none' ? 'Not checked' : $ms) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ Register Invoice</a>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Invoices" :value="$counts['total']" tone="slate" icon="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        <x-stat-card label="Pending / To Verify" :value="$counts['pending']" tone="amber" icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-stat-card label="Approved for Payment" :value="$counts['approved']" tone="blue" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-stat-card label="Paid" :value="$counts['paid']" tone="emerald" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
    </div>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Number</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">PO Reference</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Invoice Date</th>
                        <th class="py-3 px-4">Match</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $invoice->number }}</td>
                            <td class="py-3 px-4"><a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $invoice->supplier?->name }}</a></td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $invoice->purchaseOrder?->reference ?? '—' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $invoice->invoice_date?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $invoice->match_status === 'full' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($invoice->match_status === 'partial' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">{{ $invoice->matchStatusLabel() }}</span>
                            </td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($invoice->status) }}">{{ $invoice->statusLabel() }}</span></td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No supplier invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $invoices->links() }}</div>
</x-app-layout>
