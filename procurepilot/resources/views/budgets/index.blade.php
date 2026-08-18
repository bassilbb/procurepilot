<x-app-layout>
    <x-slot name="header">Budgets</x-slot>

    <x-page-header title="Budget Management" description="Allocations, commitments and spend per fiscal year">
        <x-slot name="actions">
            <form method="GET" action="{{ route('budgets.index') }}" class="flex gap-2">
                <select name="fiscal_year" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All years</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(request('fiscal_year') === $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords($status) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('budgets.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Budget</a>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Allocated" value="₦ {{ number_format($totals['allocated'], 2) }}" tone="emerald" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-stat-card label="Committed" value="₦ {{ number_format($totals['committed'], 2) }}" tone="blue" icon="M12 6v6m0 0l3-3m-3 3l-3-3m3 3v6m6 3H6a3 3 0 01-3-3V6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3z" />
        <x-stat-card label="Spent" value="₦ {{ number_format($totals['spent'], 2) }}" tone="amber" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        <x-stat-card label="Remaining" value="₦ {{ number_format($totals['remaining'], 2) }}" tone="slate" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </div>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Budget</th>
                        <th class="py-3 px-4">Department</th>
                        <th class="py-3 px-4">Year</th>
                        <th class="py-3 px-4">Allocated</th>
                        <th class="py-3 px-4">Committed</th>
                        <th class="py-3 px-4">Spent</th>
                        <th class="py-3 px-4">Utilization</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($budgets as $budget)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4"><a href="{{ route('budgets.show', $budget) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $budget->name }}</a></td>
                            <td class="py-3 px-4 text-slate-500">{{ $budget->department?->name ?? '—' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $budget->fiscal_year }}</td>
                            <td class="py-3 px-4 font-semibold">₦ {{ number_format($budget->allocated_amount, 2) }}</td>
                            <td class="py-3 px-4 text-blue-600 font-medium">₦ {{ number_format($budget->committed_amount, 2) }}</td>
                            <td class="py-3 px-4 text-amber-600 font-medium">₦ {{ number_format($budget->spent_amount, 2) }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full {{ $budget->utilization_percent > 90 ? 'bg-red-500' : ($budget->utilization_percent > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $budget->utilization_percent) }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500">{{ $budget->utilization_percent }}%</span>
                                </div>
                            </td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($budget->status) }}">{{ $budget->statusLabel() }}</span></td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('budgets.show', $budget) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">No budgets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $budgets->links() }}</div>
</x-app-layout>
