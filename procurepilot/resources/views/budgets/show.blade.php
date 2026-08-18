<x-app-layout>
    <x-slot name="header">{{ $budget->name }}</x-slot>

    <x-page-header :title="$budget->name" :description="'FY ' . $budget->fiscal_year . ' · ' . ($budget->department?->name ?? 'Organization-wide')">
        <x-slot name="actions">
            <a href="{{ route('budgets.edit', $budget) }}" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Edit</a>
            <a href="{{ route('budgets.index') }}" class="text-sm text-slate-600 hover:text-slate-900">← Back</a>
        </x-slot>
    </x-page-header>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-5">Utilization</h3>
                <div class="flex items-end justify-between mb-2">
                    <div>
                        <div class="text-3xl font-bold text-slate-900">₦ {{ number_format($budget->allocated_amount, 2) }}</div>
                        <div class="text-xs text-slate-400 uppercase mt-1">Allocated</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold {{ $budget->utilization_percent > 90 ? 'text-red-600' : ($budget->utilization_percent > 70 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $budget->utilization_percent }}%</div>
                        <div class="text-xs text-slate-400 uppercase mt-1">Utilized</div>
                    </div>
                </div>
                <div class="h-4 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $budget->utilization_percent > 90 ? 'bg-red-500' : ($budget->utilization_percent > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $budget->utilization_percent) }}%"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-6 text-sm">
                    <div class="p-3 rounded-lg bg-blue-50 border border-blue-100">
                        <div class="font-bold text-blue-800">₦ {{ number_format($budget->committed_amount, 2) }}</div>
                        <div class="text-xs text-blue-600 mt-0.5">Committed</div>
                    </div>
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                        <div class="font-bold text-amber-800">₦ {{ number_format($budget->spent_amount, 2) }}</div>
                        <div class="text-xs text-amber-600 mt-0.5">Spent</div>
                    </div>
                    <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                        <div class="font-bold text-emerald-800">₦ {{ number_format($budget->remaining, 2) }}</div>
                        <div class="text-xs text-emerald-600 mt-0.5">Remaining</div>
                    </div>
                </div>
            </div>

            <div class="hd-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900">Related procurement requests</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                            <th class="py-2 px-4">Reference</th>
                            <th class="py-2 px-4">Title</th>
                            <th class="py-2 px-4">Est. Cost</th>
                            <th class="py-2 px-4">Priority</th>
                            <th class="py-2 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($requests as $r)
                            <tr>
                                <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $r->reference }}</td>
                                <td class="py-3 px-4"><a href="{{ route('requests.show', $r) }}" class="text-slate-800 hover:text-emerald-700">{{ $r->title }}</a></td>
                                <td class="py-3 px-4">₦ {{ number_format($r->estimated_cost, 2) }}</td>
                                <td class="py-3 px-4 capitalize text-slate-600">{{ $r->priority }}</td>
                                <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($r->status) }}">{{ $r->statusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-slate-400">No requests linked yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Details</h3>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-slate-400 text-xs uppercase">Category</dt><dd class="font-medium capitalize text-slate-800">{{ $budget->category ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Department</dt><dd class="font-medium text-slate-800">{{ $budget->department?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Currency</dt><dd class="font-medium text-slate-800">{{ $budget->currency }}</dd></div>
                    <div><dt class="text-slate-400 text-xs uppercase">Status</dt><dd><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($budget->status) }}">{{ $budget->statusLabel() }}</span></dd></div>
                    @if ($budget->notes)
                        <div><dt class="text-slate-400 text-xs uppercase">Notes</dt><dd class="text-slate-600 whitespace-pre-line">{{ $budget->notes }}</dd></div>
                    @endif
                </dl>
            </div>

            @if (auth()->user()->isProcurement() && $budget->status === 'active')
                <div class="hd-card p-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Commit funds</h3>
                    <p class="text-xs text-slate-500 mb-3">Available: <strong>₦ {{ number_format($budget->remaining, 2) }}</strong></p>
                    <form action="{{ route('budgets.commit', $budget) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="number" name="amount" step="0.01" min="0.01" placeholder="Amount" required class="flex-1 rounded-lg border-gray-300 text-sm px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg">Commit</button>
                    </form>
                    @if ((float) $budget->committed_amount > 0)
                        <form action="{{ route('budgets.release', $budget) }}" method="POST" class="mt-2">
                            @csrf
                            <button class="w-full text-center text-xs text-slate-500 hover:text-slate-700 py-1">Release all commitments</button>
                        </form>
                    @endif
                </div>
            @endif

            @if ((float) $budget->committed_amount + (float) $budget->spent_amount == 0)
                <div class="bg-white rounded-xl border border-red-200 p-6">
                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Delete this budget? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full text-center px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100">Delete budget</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
