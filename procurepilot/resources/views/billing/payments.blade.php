<x-app-layout>
    <x-slot name="header">Payments</x-slot>

    <x-page-header title="Payment History" description="All payments made for subscriptions" />

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Gateway</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $payment->reference }}</td>
                            <td class="py-3 px-4"><a href="{{ route('billing.invoice-show', $payment->invoice) }}" class="font-mono text-xs text-emerald-600 hover:text-emerald-700">{{ $payment->invoice?->number }}</a></td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                            <td class="py-3 px-4 capitalize text-slate-600">{{ $payment->method }}</td>
                            <td class="py-3 px-4 capitalize text-slate-500">{{ $payment->gateway }}</td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($payment->status) }}">{{ ucfirst($payment->status) }}</span></td>
                            <td class="py-3 px-4 text-slate-500">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
