<x-app-layout>
    <x-slot name="header">Invoice {{ $invoice->number }}</x-slot>

    <div class="hd-card overflow-hidden max-w-3xl mx-auto">
        <div class="p-8 border-b border-slate-100">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ config('app.name') }}</div>
                    <div class="text-sm text-slate-500">Procurement Management Suite</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-semibold text-slate-900">INVOICE</div>
                    <div class="text-sm text-slate-500 font-mono">{{ $invoice->number }}</div>
                </div>
            </div>
        </div>
        <div class="p-8 grid grid-cols-2 gap-6 text-sm">
            <div>
                <div class="text-xs uppercase text-slate-400 mb-1">Billed To</div>
                <div class="font-semibold text-slate-900">{{ $invoice->organization->name }}</div>
                @if ($invoice->organization->email)<div class="text-slate-500">{{ $invoice->organization->email }}</div>@endif
                @if ($invoice->organization->address)<div class="text-slate-500">{{ $invoice->organization->address }}</div>@endif
            </div>
            <div>
                <div class="text-xs uppercase text-slate-400 mb-1">Details</div>
                <div class="space-y-1 text-slate-500">
                    <div>Issued: {{ $invoice->created_at->format('d M Y') }}</div>
                    <div>Due: {{ $invoice->due_at?->format('d M Y') ?? '—' }}</div>
                    <div>Status: <span class="px-2 py-0.5 rounded-full border text-xs font-medium {{ statusBadgeClass($invoice->status) }}">{{ ucfirst($invoice->status) }}</span></div>
                </div>
            </div>
        </div>
        <div class="px-8">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                        <th class="py-2 pr-3">Description</th>
                        <th class="py-2 pr-3">Period</th>
                        <th class="py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-4 pr-3 text-slate-800">{{ $invoice->title }}</td>
                        <td class="py-4 pr-3 text-slate-500">
                            @if ($invoice->subscription)
                                {{ $invoice->subscription->starts_at->format('d M Y') }} – {{ $invoice->subscription->ends_at?->format('d M Y') ?? '—' }}
                            @else
                                One-time
                            @endif
                        </td>
                        <td class="py-4 text-right font-medium">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-100">
                        <td colspan="2" class="py-4 text-right font-semibold text-slate-900">Total</td>
                        <td class="py-4 text-right font-bold text-slate-900 text-lg">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if ($invoice->payments->isNotEmpty())
            <div class="px-8 pb-8">
                <div class="text-xs uppercase text-slate-400 mb-2">Payments</div>
                @foreach ($invoice->payments as $payment)
                    <div class="flex justify-between text-sm py-2 border-t border-slate-50">
                        <span class="text-slate-600">{{ $payment->reference }} · {{ ucfirst($payment->method) }} · {{ $payment->gateway }}</span>
                        <span class="font-medium text-emerald-600">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="px-8 pb-8 flex justify-end">
            <a href="{{ route('billing.invoices') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">← Back to invoices</a>
        </div>
    </div>
</x-app-layout>
