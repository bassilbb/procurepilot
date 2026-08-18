<x-app-layout>
    <x-slot name="header">Procurement Request</x-slot>

    <x-page-header :title="$procurementRequest->reference" :description="$procurementRequest->title">
        <x-slot name="actions">
            <span class="text-xs px-2.5 py-1 rounded-full border font-medium {{ statusBadgeClass($procurementRequest->status) }}">{{ $procurementRequest->statusLabel() }}</span>
            @if ($procurementRequest->status === 'draft')
                <a href="{{ route('requests.edit', $procurementRequest) }}" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Edit</a>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Request details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-xs uppercase text-slate-400">Department</dt><dd class="mt-1 text-slate-800 font-medium">{{ $procurementRequest->department?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Requester</dt><dd class="mt-1 text-slate-800 font-medium">{{ $procurementRequest->requester?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Category</dt><dd class="mt-1 text-slate-800 font-medium">{{ $procurementRequest->category?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Budget Code</dt><dd class="mt-1 text-slate-800 font-medium">{{ $procurementRequest->budget_code ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Priority</dt><dd class="mt-1 text-slate-800 font-medium capitalize">{{ $procurementRequest->priority }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Required By</dt><dd class="mt-1 text-slate-800 font-medium">{{ $procurementRequest->required_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Estimated Cost</dt><dd class="mt-1 text-slate-800 font-semibold">{{ $procurementRequest->currency }} {{ number_format($procurementRequest->estimated_cost, 2) }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Approved By</dt><dd class="mt-1 text-slate-800 font-medium">{{ $procurementRequest->approver?->name ?? '—' }}</dd></div>
                </dl>
                @if ($procurementRequest->justification)
                    <div class="mt-4">
                        <dt class="text-xs uppercase text-slate-400">Justification</dt>
                        <dd class="mt-1 text-slate-600 text-sm leading-relaxed">{{ $procurementRequest->justification }}</dd>
                    </div>
                @endif
                @if ($procurementRequest->rejection_reason)
                    <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                        <strong>Rejection reason:</strong> {{ $procurementRequest->rejection_reason }}
                    </div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                <th class="py-2">Description</th>
                                <th class="py-2">Qty</th>
                                <th class="py-2">Unit</th>
                                <th class="py-2 text-right">Unit Cost</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($procurementRequest->items as $item)
                                <tr>
                                    <td class="py-3 text-slate-800">{{ $item->description }}</td>
                                    <td class="py-3 text-slate-600">{{ number_format($item->quantity, 0) }}</td>
                                    <td class="py-3 text-slate-600">{{ $item->unit ?? '—' }}</td>
                                    <td class="py-3 text-right text-slate-600">{{ number_format($item->estimated_unit_cost, 2) }}</td>
                                    <td class="py-3 text-right font-medium text-slate-800">{{ number_format($item->estimated_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($procurementRequest->approvals->count() > 1)
                <div class="hd-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">Approval progress</h3>
                    <ol class="space-y-3">
                        @foreach ($procurementRequest->approvals as $record)
                            @php
                                $isCurrent = $record->status === 'pending';
                                $done = in_array($record->status, ['approved', 'rejected', 'skipped']);
                                $dotClass = $record->status === 'approved' ? 'bg-emerald-500' : ($record->status === 'rejected' ? 'bg-red-500' : ($isCurrent ? 'bg-amber-500 ring-4 ring-amber-100' : 'bg-slate-300'));
                                $textClass = $done || $isCurrent ? 'text-slate-800' : 'text-slate-400';
                            @endphp
                            <li class="flex items-center gap-3">
                                <span class="w-3.5 h-3.5 rounded-full shrink-0 {{ $dotClass }}"></span>
                                <div class="text-sm {{ $textClass }}">
                                    <span class="font-medium">{{ $record->level?->name ?? 'Approval' }}</span>
                                    @if ($record->status === 'pending' && $isCurrent)
                                        <span class="text-xs text-amber-600 font-medium ml-2">Current step</span>
                                    @endif
                                </div>
                                @if ($record->status === 'approved')
                                    <span class="ml-auto text-xs text-emerald-600">Approved by {{ $record->approver?->name ?? '—' }}</span>
                                @elseif ($record->status === 'pending' && $isCurrent)
                                    @php $awaitingUsers = $record->level?->approverUsers() ?? collect(); @endphp
                                    <span class="ml-auto text-xs text-slate-400">Awaiting {{ $record->level?->roleLabel() ?? 'approval' }}@if($awaitingUsers->isNotEmpty()) ({{ $awaitingUsers->pluck('name')->implode(', ') }})@endif</span>
                                @endif                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            @if ($procurementRequest->approvals->count())
                <div class="hd-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">Approval history</h3>
                    <ul class="space-y-3">
                        @foreach ($procurementRequest->approvals as $record)
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $record->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($record->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-500') }}">{{ $record->status === 'approved' ? '✓' : ($record->status === 'rejected' ? '✕' : '•') }}</span>
                                <div class="text-sm">
                                    <div class="text-slate-800 font-medium">{{ $record->level?->name ?? 'Approval' }} <span class="text-slate-400 font-normal">· {{ $record->approver?->name ?? '—' }}</span></div>
                                    <div class="text-xs text-slate-400">{{ $record->status }} @if ($record->decided_at)· {{ $record->decided_at->format('d M Y H:i') }}@endif</div>
                                    @if ($record->comment)
                                        <div class="text-slate-600 mt-1">{{ $record->comment }}</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if (auth()->user()->isApprover() && $procurementRequest->status === 'submitted' && $canApprove)
                <div class="hd-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Approval action</h3>
                    @if ($currentApproval && $currentApproval->level)
                        <p class="text-xs text-slate-500 mb-3">Step {{ $currentApproval->level->sequence }} of {{ $procurementRequest->approvals->count() }}: <strong>{{ $currentApproval->level->name }}</strong></p>
                    @endif
                    <form method="POST" action="{{ route('requests.approve', $procurementRequest) }}">
                        @csrf
                        <textarea name="comment" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm mb-3" placeholder="Approval comment (optional)"></textarea>
                        <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Approve Request</button>
                    </form>
                    <form method="POST" action="{{ route('requests.reject', $procurementRequest) }}" class="mt-3">
                        @csrf
                        <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Rejection reason" required></textarea>
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg">Reject</button>
                    </form>
                </div>
            @endif

            @if ($procurementRequest->status === 'draft')
                <div class="hd-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Submit for approval</h3>
                    <form method="POST" action="{{ route('requests.submit', $procurementRequest) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg">Submit for Approval</button>
                    </form>
                </div>
            @endif

            @if ($procurementRequest->status === 'draft' && (auth()->user()->isAdmin() || auth()->id() === $procurementRequest->requester_id))
                <div class="hd-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Danger zone</h3>
                    <form method="POST" action="{{ route('requests.destroy', $procurementRequest) }}" onsubmit="return confirm('Delete this request permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg">Delete Request</button>
                    </form>
                </div>
            @endif

            <div class="hd-card p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Summary</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Items</dt><dd class="font-medium text-slate-800">{{ $procurementRequest->items->count() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Estimated cost</dt><dd class="font-medium text-slate-800">{{ $procurementRequest->currency }} {{ number_format($procurementRequest->estimated_cost, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Created</dt><dd class="text-slate-600">{{ $procurementRequest->created_at->format('d M Y') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
