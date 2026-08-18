<x-app-layout>
    <x-slot name="header">Invoices</x-slot>

    <x-page-header title="Invoices" description="Billing history for your organization">
        <x-slot name="actions">
            <a href="{{ route('billing.plan') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Change Plan</a>
        </x-slot>
    </x-page-header>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Due</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $invoice->number }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $invoice->title }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($invoice->status) }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td class="py-3 px-4 text-slate-500">{{ $invoice->due_at?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('billing.invoice-show', $invoice) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
