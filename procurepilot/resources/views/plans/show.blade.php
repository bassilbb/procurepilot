<x-app-layout>
    <x-slot name="header">Procurement Plan</x-slot>

    <x-page-header :title="$plan->title">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($plan->status) }}">{{ $plan->statusLabel() }}</span>
            <a href="{{ route('plans.edit', $plan) }}" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Edit</a>
            @if ($plan->status === 'draft')
                <form method="POST" action="{{ route('plans.submit', $plan) }}">
                    @csrf
                    <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Submit for Approval</button>
                </form>
            @endif
            @if ($plan->status === 'submitted' && auth()->user()->isApprover())
                <form method="POST" action="{{ route('plans.approve', $plan) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Approve</button>
                </form>
                <form method="POST" action="{{ route('plans.reject', $plan) }}">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Reject</button>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Plan Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div><dt class="text-slate-500">Fiscal Year</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $plan->fiscal_year }}</dd></div>
                    <div><dt class="text-slate-500">Line Items</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $plan->items->count() }}</dd></div>
                    <div><dt class="text-slate-500">Estimated Budget</dt><dd class="font-medium text-slate-900 mt-0.5">{{ currentOrganization()->currency }} {{ number_format($plan->totalEstimatedCost(), 2) }}</dd></div>
                    <div class="sm:col-span-3"><dt class="text-slate-500">Description</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $plan->description ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Plan Items ({{ $plan->items->count() }})</h3>
                @if ($plan->items->isEmpty())
                    <p class="text-sm text-slate-500">No line items added yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                    <th class="py-2 pr-3">Item</th>
                                    <th class="py-2 pr-3">Category</th>
                                    <th class="py-2 pr-3">Qty</th>
                                    <th class="py-2 pr-3">Est. Cost</th>
                                    <th class="py-2 pr-3">Method</th>
                                    <th class="py-2">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($plan->items as $item)
                                    <tr>
                                        <td class="py-3 pr-3">
                                            <div class="font-medium text-slate-800">{{ $item->title }}</div>
                                            <div class="text-xs text-slate-400">{{ $item->expected_date?->format('M Y') ?? '—' }}</div>
                                        </td>
                                        <td class="py-3 pr-3 text-slate-500">{{ $item->category?->name ?? '—' }}</td>
                                        <td class="py-3 pr-3 text-slate-500">{{ $item->quantity }}</td>
                                        <td class="py-3 pr-3 font-medium text-slate-800">{{ currentOrganization()->currency }} {{ number_format($item->estimated_cost, 0) }}</td>
                                        <td class="py-3 pr-3"><span class="text-xs">{{ $item->methodLabel() }}</span></td>
                                        <td class="py-3">
                                            <span class="text-xs px-2 py-0.5 rounded-full border {{ $item->priority === 'critical' ? 'bg-red-100 text-red-700 border-red-200' : ($item->priority === 'high' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">{{ ucfirst($item->priority) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Approval Workflow</h3>
                @if ($plan->status === 'approved')
                    <p class="text-sm text-emerald-700 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Approved {{ $plan->approved_at?->diffForHumans() }} by {{ $plan->approver?->name }}</p>
                @else
                    <ol class="space-y-3 text-sm">
                        <li class="flex items-center gap-2 {{ in_array($plan->status, ['submitted', 'approved']) ? 'text-emerald-600' : 'text-slate-400' }}"><span class="w-6 h-6 rounded-full flex items-center justify-center text-xs {{ in_array($plan->status, ['submitted', 'approved']) ? 'bg-emerald-500 text-white' : 'bg-slate-200' }}">1</span> Created by {{ $plan->creator?->name }}</li>
                        <li class="flex items-center gap-2 {{ $plan->status === 'approved' ? 'text-emerald-600' : 'text-slate-400' }}"><span class="w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $plan->status === 'approved' ? 'bg-emerald-500 text-white' : 'bg-slate-200' }}">2</span> Tenders Board approval</li>
                    </ol>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
