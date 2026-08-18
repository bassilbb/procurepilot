<x-app-layout>
    <x-slot name="header">Contract</x-slot>

    <x-page-header :title="$contract->title">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($contract->status) }}">{{ $contract->statusLabel() }}</span>
            <a href="{{ route('contracts.edit', $contract) }}" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Edit</a>
            @if ($contract->status === 'draft')
                <form method="POST" action="{{ route('contracts.activate', $contract) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Activate</button>
                </form>
            @endif
            @if ($contract->status === 'active')
                <form method="POST" action="{{ route('contracts.complete', $contract) }}">
                    @csrf
                    <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Mark Complete</button>
                </form>
                @if (auth()->user()->isApprover())
                    <form method="POST" action="{{ route('contracts.terminate', $contract) }}" onsubmit="return confirm('Terminate this contract?')">
                        @csrf
                        <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Terminate</button>
                    </form>
                @endif
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Contract Details</h3>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div><dt class="text-slate-500">Reference</dt><dd class="font-mono font-medium text-slate-900 mt-0.5 text-xs">{{ $contract->reference }}</dd></div>
                    <div><dt class="text-slate-500">Supplier</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $contract->supplier?->name }}</dd></div>
                    <div><dt class="text-slate-500">Value</dt><dd class="font-semibold text-slate-900 mt-0.5">{{ $contract->currency }} {{ number_format($contract->value, 2) }}</dd></div>
                    <div><dt class="text-slate-500">Payment Terms</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $contract->payment_terms ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Start Date</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $contract->start_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">End Date</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $contract->end_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Signed</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $contract->signed_at?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Status</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $contract->statusLabel() }}</dd></div>
                </dl>
                @if ($contract->description)
                    <div class="mt-4 text-sm text-slate-600 bg-slate-50 rounded-lg p-4">{{ $contract->description }}</div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Milestones ({{ $contract->milestones->count() }})</h3>
                <form method="POST" action="{{ route('contracts.milestones.store', $contract) }}" class="grid grid-cols-12 gap-2 mb-4">
                    @csrf
                    <div class="col-span-4"><input name="title" placeholder="Milestone title" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-2"><input name="due_date" type="date" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-3"><input name="amount" type="number" min="0" step="0.01" placeholder="Amount" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-3"><button class="w-full px-2 py-2 bg-emerald-600 text-white text-sm rounded-lg">Add Milestone</button></div>
                </form>
                @if ($contract->milestones->isEmpty())
                    <p class="text-sm text-slate-500">No milestones defined.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                    <th class="py-2 pr-3">Milestone</th>
                                    <th class="py-2 pr-3">Due</th>
                                    <th class="py-2 pr-3">Amount</th>
                                    <th class="py-2 pr-3">Status</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($contract->milestones as $milestone)
                                    <tr>
                                        <td class="py-2 pr-3 font-medium text-slate-800">{{ $milestone->title }}</td>
                                        <td class="py-2 pr-3 text-slate-500">{{ $milestone->due_date?->format('d M Y') }}</td>
                                        <td class="py-2 pr-3">{{ $contract->currency }} {{ number_format($milestone->amount, 0) }}</td>
                                        <td class="py-2 pr-3"><span class="text-xs px-2 py-0.5 rounded-full border {{ statusBadgeClass($milestone->status) }}">{{ $milestone->statusLabel() }}</span></td>
                                        <td class="py-2 text-right">
                                            @if ($milestone->status !== 'completed')
                                                <form method="POST" action="{{ route('contracts.milestones.complete', $milestone) }}" class="inline">
                                                    @csrf
                                                    <button class="text-xs text-emerald-600 mr-2">Complete</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('contracts.milestones.destroy', $milestone) }}" class="inline" onsubmit="return confirm('Remove?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs text-red-500">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Documents</h3>
                <form method="POST" action="{{ route('contracts.documents.store', $contract) }}" enctype="multipart/form-data" class="flex gap-2 mb-4">
                    @csrf
                    <input type="file" name="documents[]" multiple class="flex-1 text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg">Upload</button>
                </form>
                @if ($contract->documents->isEmpty())
                    <p class="text-sm text-slate-500">No documents uploaded.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($contract->documents as $doc)
                            <li class="py-2 flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm">
                                    <span>📄</span>
                                    <span class="text-slate-700">{{ $doc->name }}</span>
                                    <span class="text-xs text-slate-400">{{ round($doc->size / 1024, 1) }} KB</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('contracts.documents.download', $doc) }}" class="text-xs text-emerald-600 font-medium">Download</a>
                                    <form method="POST" action="{{ route('contracts.documents.destroy', $doc) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 font-medium">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            @if ($contract->tender)
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-3">Linked Procurement</h3>
                    <a href="{{ route('tenders.show', $contract->tender) }}" class="block p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                        <div class="text-sm font-medium text-slate-800">{{ $contract->tender->title }}</div>
                        <div class="text-xs text-slate-500">{{ $contract->tender->reference }}</div>
                    </a>
                    @if ($contract->award)
                        <a href="{{ route('awards.show', $contract->award) }}" class="block mt-2 p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                            <div class="text-sm font-medium text-slate-800">Award Record</div>
                            <div class="text-xs text-slate-500">{{ ucfirst($contract->award->status) }}</div>
                        </a>
                    @endif
                </div>
            @endif

            @if ($contract->purchaseOrders->isNotEmpty())
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-3">Purchase Orders</h3>
                    <div class="space-y-2">
                        @foreach ($contract->purchaseOrders as $po)
                            <a href="{{ route('purchase-orders.show', $po) }}" class="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                                <div>
                                    <div class="text-sm font-medium text-slate-800">{{ $po->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $po->reference }}</div>
                                </div>
                                <span class="text-sm font-semibold">{{ $po->currency }} {{ number_format($po->total, 0) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Value Summary</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Contract Value</dt><dd class="font-medium">{{ $contract->currency }} {{ number_format($contract->value, 0) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Milestones Value</dt><dd class="font-medium">{{ $contract->currency }} {{ number_format($contract->milestones->sum('amount'), 0) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Milestones Completed</dt><dd class="font-medium">{{ $contract->milestones->where('status', 'completed')->count() }}/{{ $contract->milestones->count() }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
