<x-app-layout>
    <x-slot name="header">Purchase Order</x-slot>

    <x-page-header :title="$purchaseOrder->title">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($purchaseOrder->status) }}">{{ $purchaseOrder->statusLabel() }}</span>
            @if ($purchaseOrder->status === 'draft')
                <form method="POST" action="{{ route('purchase-orders.issue', $purchaseOrder) }}">
                    @csrf
                    <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Issue to Supplier</button>
                </form>
            @endif
            @if ($purchaseOrder->status === 'issued' && auth()->user()->isApprover())
                <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Approve</button>
                </form>
            @endif
            @if (in_array($purchaseOrder->status, ['draft', 'issued', 'approved']))
                <button onclick="document.getElementById('receive-modal').showModal()" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Record Goods Receipt</button>
                <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Cancel</button>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Order Details</h3>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div><dt class="text-slate-500">Reference</dt><dd class="font-mono text-xs font-medium text-slate-900 mt-0.5">{{ $purchaseOrder->reference }}</dd></div>
                    <div><dt class="text-slate-500">Supplier</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $purchaseOrder->supplier?->name }}</dd></div>
                    <div><dt class="text-slate-500">Order Date</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $purchaseOrder->order_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Expected Delivery</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $purchaseOrder->expected_delivery?->format('d M Y') ?? '—' }}</dd></div>
                    <div class="sm:col-span-4">
                        <dt class="text-slate-500">Total Value</dt>
                        <dd class="text-2xl font-bold text-slate-900 mt-0.5">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total, 2) }}</dd>
                    </div>
                </dl>
                @if ($purchaseOrder->description)
                    <div class="mt-4 text-sm text-slate-600 bg-slate-50 rounded-lg p-4">{{ $purchaseOrder->description }}</div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                <th class="py-2 pr-3">Description</th>
                                <th class="py-2 pr-3">Qty</th>
                                <th class="py-2 pr-3">Received</th>
                                <th class="py-2 pr-3">Unit Price</th>
                                <th class="py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($purchaseOrder->items as $item)
                                <tr>
                                    <td class="py-2 pr-3 text-slate-800">{{ $item->description }}</td>
                                    <td class="py-2 pr-3">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="py-2 pr-3 {{ $item->received_qty >= $item->quantity ? 'text-emerald-600 font-medium' : 'text-slate-500' }}">{{ $item->received_qty }}</td>
                                    <td class="py-2 pr-3">{{ $purchaseOrder->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-2 font-medium">{{ $purchaseOrder->currency }} {{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($purchaseOrder->receipts->isNotEmpty())
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">Goods Receipts</h3>
                    <div class="space-y-3">
                        @foreach ($purchaseOrder->receipts as $receipt)
                            <div class="p-4 rounded-lg bg-slate-50">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-slate-800">Receipt #{{ $receipt->id }}</span>
                                    <span class="text-slate-500">{{ $receipt->received_at?->format('d M Y H:i') }} · {{ $receipt->receiver?->name }}</span>
                                </div>
                                @if ($receipt->items->isNotEmpty())
                                    <div class="mt-2 text-xs text-slate-500">
                                        @foreach ($receipt->items as $ri)
                                            <div>{{ $ri->quantity }} × {{ $ri->poItem?->description }} @if ($ri->condition && $ri->condition !== 'ok') <span class="text-amber-600">({{ $ri->condition }})</span> @endif</div>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($receipt->note)
                                    <div class="mt-2 text-xs text-slate-500">Note: {{ $receipt->note }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($purchaseOrder->contract)
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-3">Linked Contract</h3>
                    <a href="{{ route('contracts.show', $purchaseOrder->contract) }}" class="block p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                        <div class="text-sm font-medium text-slate-800">{{ $purchaseOrder->contract->title }}</div>
                        <div class="text-xs text-slate-500">{{ $purchaseOrder->contract->reference }}</div>
                    </a>
                </div>
            @endif

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Fulfilment</h3>
                <div class="space-y-2 text-sm">
                    @php
                        $totalQty = $purchaseOrder->items->sum('quantity');
                        $receivedQty = $purchaseOrder->items->sum('received_qty');
                        $pct = $totalQty > 0 ? round(($receivedQty / $totalQty) * 100) : 0;
                    @endphp
                    <div class="flex justify-between">
                        <span class="text-slate-500">Received</span>
                        <span class="font-medium">{{ $receivedQty }} / {{ $totalQty }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full">
                        <div class="h-2 bg-emerald-500 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    @if ($purchaseOrder->approved_by)
                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-400">Approved by {{ $purchaseOrder->approver?->name }} on {{ $purchaseOrder->approved_at?->format('d M Y') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (in_array($purchaseOrder->status, ['draft', 'issued', 'approved']))
        <dialog id="receive-modal" class="rounded-xl p-6 w-full max-w-lg">
            <h3 class="font-semibold text-slate-900 mb-4">Record Goods Receipt</h3>
            <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="space-y-4">
                @csrf
                @foreach ($purchaseOrder->items as $item)
                    @php $remaining = $item->remaining(); @endphp
                    @if ($remaining > 0)
                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-6 text-sm text-slate-700">{{ $item->description }}</div>
                            <div class="col-span-3">
                                <input type="number" name="items[{{ $item->id }}][quantity]" min="0" max="{{ $remaining }}" step="0.01"
                                       placeholder="Qty (max {{ $remaining }})"
                                       class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                            </div>
                            <div class="col-span-3">
                                <select name="items[{{ $item->id }}][condition]" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="ok">OK</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="short">Short</option>
                                    <option value="over">Over-delivered</option>
                                </select>
                            </div>
                        </div>
                    @endif
                @endforeach
                <div>
                    <x-input-label for="note" value="Receipt Note" />
                    <textarea id="note" name="note" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('receive-modal').close()" class="px-4 py-2 text-sm text-slate-600">Cancel</button>
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg">Record Receipt</button>
                </div>
            </form>
        </dialog>
    @endif
</x-app-layout>
