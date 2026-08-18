<x-app-layout>
    <x-slot name="header">{{ $tender->reference }}</x-slot>

    <x-page-header :title="$tender->title">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($tender->status) }}">{{ $tender->statusLabel() }}</span>
            <span class="text-xs px-3 py-1.5 rounded-full border bg-slate-50 text-slate-600 border-slate-200">{{ $tender->typeLabel() }}</span>
            @if ($tender->status === 'draft' && auth()->user()->isProcurement())
                <a href="{{ route('tenders.edit', $tender) }}" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Edit</a>
                <form method="POST" action="{{ route('tenders.publish', $tender) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Publish Tender</button>
                </form>
            @endif
            @if ($tender->status === 'published' && auth()->user()->isApprover())
                <form method="POST" action="{{ route('tenders.close', $tender) }}">
                    @csrf
                    <button class="px-4 py-2 bg-amber-600 text-white text-sm rounded-lg hover:bg-amber-700">Close Bidding</button>
                </form>
            @endif
            @if ($tender->status === 'closed' && auth()->user()->isApprover())
                <form method="POST" action="{{ route('tenders.evaluate-start', $tender) }}">
                    @csrf
                    <button class="px-4 py-2 bg-violet-600 text-white text-sm rounded-lg hover:bg-violet-700">Start Evaluation</button>
                </form>
            @endif
            @if (in_array($tender->status, ['draft']) && auth()->user()->isProcurement())
                <form method="POST" action="{{ route('tenders.cancel', $tender) }}" onsubmit="return confirm('Cancel this tender?')">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Cancel Tender</button>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Tender Overview</h3>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mb-4">
                    <div><dt class="text-slate-500">Category</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->category?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Method</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->methodLabel() }}</dd></div>
                    <div><dt class="text-slate-500">Budget</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->currency }} {{ number_format($tender->budget ?? 0, 0) }}</dd></div>
                    <div><dt class="text-slate-500">Evaluation</dt><dd class="font-medium text-slate-900 mt-0.5">{{ ucwords(str_replace('_', ' ', $tender->evaluation_method)) }}</dd></div>
                    <div><dt class="text-slate-500">Published</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->published_at?->format('d M Y H:i') ?? 'Not yet' }}</dd></div>
                    <div><dt class="text-slate-500">Deadline</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->closing_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Bid Opening</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->opening_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Created by</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $tender->creator?->name ?? '—' }}</dd></div>
                </dl>
                @if ($tender->description)
                    <div class="text-sm text-slate-600 bg-slate-50 rounded-lg p-4">{{ $tender->description }}</div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Line Items ({{ $tender->items->count() }})</h3>
                @if ($tender->items->isEmpty())
                    <p class="text-sm text-slate-500">No line items defined.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                    <th class="py-2 pr-3">Description</th>
                                    <th class="py-2 pr-3">Qty</th>
                                    <th class="py-2 pr-3">Unit</th>
                                    <th class="py-2 pr-3">Est. Unit Price</th>
                                    <th class="py-2">Est. Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($tender->items as $item)
                                    <tr>
                                        <td class="py-2 pr-3 text-slate-800">{{ $item->description }}</td>
                                        <td class="py-2 pr-3">{{ $item->quantity }}</td>
                                        <td class="py-2 pr-3 text-slate-500">{{ $item->unit ?? '—' }}</td>
                                        <td class="py-2 pr-3">{{ $item->estimated_unit_price ? $tender->currency . ' ' . number_format($item->estimated_unit_price, 2) : '—' }}</td>
                                        <td class="py-2 font-medium">{{ $tender->currency }} {{ number_format($item->total_estimate, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Evaluation Criteria ({{ $tender->criteria->sum('weight') }}% total weight)</h3>
                @if ($tender->criteria->isEmpty())
                    <p class="text-sm text-slate-500">No evaluation criteria defined.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($tender->criteria as $criterion)
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm">
                                        <span class="font-medium text-slate-800">{{ $criterion->name }}</span>
                                        <span class="text-slate-500">{{ $criterion->weight }}%</span>
                                    </div>
                                    <div class="mt-1 h-1.5 bg-slate-100 rounded-full">
                                        <div class="h-1.5 bg-violet-500 rounded-full" style="width: {{ $criterion->weight }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Submitted Bids ({{ $tender->bids->count() }})</h3>
                @if ($tender->bids->isEmpty())
                    <p class="text-sm text-slate-500">No bids received.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($tender->bids as $bid)
                            <a href="{{ route('bids.show', $bid) }}" class="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-slate-800 truncate">{{ $bid->supplier?->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $bid->reference }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-slate-800">{{ $tender->currency }} {{ number_format($bid->total_amount, 0) }}</div>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full border {{ statusBadgeClass($bid->status) }}">{{ $bid->statusLabel() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Invited Suppliers ({{ $tender->suppliers->count() }})</h3>
                @if ($tender->suppliers->isNotEmpty())
                    <ul class="space-y-2 text-sm">
                        @foreach ($tender->suppliers as $supplier)
                            <li class="flex items-center justify-between">
                                <span class="text-slate-700">{{ $supplier->name }}</span>
                                <form method="POST" action="{{ route('tenders.suppliers.destroy', [$tender, $supplier]) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500">No suppliers invited.</p>
                @endif

                @if ($tender->status === 'draft')
                    <form method="POST" action="{{ route('tenders.suppliers.store', $tender) }}" class="mt-4 space-y-2">
                        @csrf
                        <select name="supplier_ids[]" multiple class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" size="5">
                            @foreach ($approvedSuppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <button class="w-full px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Invite Suppliers</button>
                    </form>
                @endif
            </div>

            @if ($tender->award_notice)
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                    <h4 class="font-semibold text-emerald-800 text-sm mb-2">Award Notice</h4>
                    <p class="text-sm text-emerald-700">{{ $tender->award_notice }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
