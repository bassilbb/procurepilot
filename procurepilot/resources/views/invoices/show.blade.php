<x-app-layout>
    <x-slot name="header">Invoice {{ $invoice->number }}</x-slot>

    <x-page-header :title="$invoice->number" :description="$invoice->supplier?->name . ' — ' . $invoice->statusLabel()">
        <x-slot name="actions">
            <a href="{{ route('invoices.index') }}" class="text-sm text-slate-600 hover:text-slate-900">← Back to invoices</a>
        </x-slot>
    </x-page-header>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg">{{ session('warning') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Invoice lines</h3>
                    <span class="text-xs font-mono text-slate-400">{{ $invoice->number }}</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                            <th class="py-2 px-4">Description</th>
                            <th class="py-2 px-4">Qty</th>
                            <th class="py-2 px-4">Unit Price</th>
                            <th class="py-2 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="text-slate-800">{{ $item->description }}</div>
                                    @if ($item->poItem)
                                        <div class="text-xs text-slate-400 mt-0.5">PO line: {{ $item->poItem->description }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 px-4 text-right font-medium">{{ $invoice->currency }} {{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-slate-100 bg-slate-50 text-sm">
                        <tr><td colspan="3" class="py-2 px-4 text-right text-slate-500">Subtotal</td><td class="py-2 px-4 text-right">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td></tr>
                        <tr><td colspan="3" class="py-2 px-4 text-right text-slate-500">Tax</td><td class="py-2 px-4 text-right">{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td></tr>
                        <tr><td colspan="3" class="py-2 px-4 text-right font-semibold text-slate-900">Total</td><td class="py-2 px-4 text-right font-bold">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="hd-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Three-way matching</h3>
                    <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $invoice->match_status === 'full' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($invoice->match_status === 'partial' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">{{ $invoice->matchStatusLabel() }}</span>
                </div>
                <div class="px-6 py-4">
                    @if ($invoice->match_detail)
                        <table class="w-full text-sm mb-4">
                            <thead>
                                <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                    <th class="py-2">Line</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Invoice Qty</th>
                                    <th class="py-2">Received Qty</th>
                                    <th class="py-2">PO Qty</th>
                                    <th class="py-2">PO Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($invoice->match_detail as $line)
                                    <tr>
                                        <td class="py-2">{{ $line['line'] }}</td>
                                        <td class="py-2"><span class="text-xs px-2 py-0.5 rounded-full border {{ $line['status'] === 'matched' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($line['status'] === 'mismatch' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">{{ ucfirst($line['status']) }}</span></td>
                                        <td class="py-2">{{ number_format($line['quantity'] ?? 0, 2) }}</td>
                                        <td class="py-2">{{ number_format($line['received_qty'] ?? 0, 2) }}</td>
                                        <td class="py-2">{{ number_format($line['po_qty'] ?? 0, 2) }}</td>
                                        <td class="py-2">{{ number_format($line['po_price'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if (auth()->user()->isProcurement() && in_array($invoice->status, ['pending', 'verified', 'matched']))
                        <form action="{{ route('invoices.match', $invoice) }}" method="POST" class="inline">
                            @csrf
                            <button class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Run three-way match</button>
                        </form>
                        @if ($invoice->status === 'pending')
                            <form action="{{ route('invoices.verify', $invoice) }}" method="POST" class="inline">
                                @csrf
                                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Mark verified</button>
                            </form>
                        @endif
                    @endif

                    @if (auth()->user()->isApprover() && in_array($invoice->status, ['verified', 'matched']))
                        <div class="flex gap-2 flex-wrap mt-2">
                            <form action="{{ route('invoices.approve', $invoice) }}" method="POST" class="inline">
                                @csrf
                                <button class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Approve for payment</button>
                            </form>
                            <form action="{{ route('invoices.reject', $invoice) }}" method="POST" class="inline">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="Reason for rejection" required class="rounded-lg border-red-300 text-sm px-3 py-2 focus:border-red-500 focus:ring-red-500">
                                <button class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Reject</button>
                            </form>
                        </div>
                    @endif

                    @if (auth()->user()->isApprover() && $invoice->status === 'approved')
                        <form action="{{ route('invoices.pay', $invoice) }}" method="POST">
                            @csrf
                            <button class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Mark as paid</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Details</h3>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-slate-400 text-xs uppercase">Supplier</dt><dd class="font-medium text-slate-800">{{ $invoice->supplier?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Purchase Order</dt><dd class="font-medium text-slate-800">{{ $invoice->purchaseOrder?->reference ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Contract</dt><dd class="font-medium text-slate-800">{{ $invoice->contract?->reference ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Invoice Date</dt><dd class="font-medium text-slate-800">{{ $invoice->invoice_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Due Date</dt><dd class="font-medium text-slate-800">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Currency</dt><dd class="font-medium text-slate-800">{{ $invoice->currency }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Verified by</dt><dd class="font-medium text-slate-800">{{ $invoice->verifier?->name ?? '—' }} @if ($invoice->verified_at)<span class="text-slate-400">· {{ $invoice->verified_at->format('d M Y H:i') }}</span>@endif</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Approved by</dt><dd class="font-medium text-slate-800">{{ $invoice->approver?->name ?? '—' }} @if ($invoice->approved_at)<span class="text-slate-400">· {{ $invoice->approved_at->format('d M Y H:i') }}</span>@endif</dd></div>
                    @if ($invoice->paid_at)
                        <div><dt class="text-slate-400 text-xs uppercase">Paid at</dt><dd class="font-medium text-slate-800">{{ $invoice->paid_at->format('d M Y H:i') }}</dd></div>
                    @endif
                    @if ($invoice->notes)
                        <div><dt class="text-slate-400 text-xs uppercase">Notes</dt><dd class="text-slate-600 whitespace-pre-line">{{ $invoice->notes }}</dd></div>
                    @endif
                </dl>
            </div>
            @if (in_array($invoice->status, ['pending', 'rejected']))
                <div class="bg-white rounded-xl border border-red-200 p-6">
                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full text-center px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100">Delete invoice</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
