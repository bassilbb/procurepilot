<x-app-layout>
    <x-slot name="header">Procurement Plans</x-slot>

    <x-page-header title="Annual Procurement Plans" description="Planned procurement activities per fiscal year — PPA 2007 §27 compliance">
        <x-slot name="actions">
            <form method="GET" action="{{ route('plans.index') }}" class="flex gap-2">
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach (['draft', 'submitted', 'approved', 'rejected'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @if ($fiscalYears->isNotEmpty())
                    <select name="fiscal_year" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All years</option>
                        @foreach ($fiscalYears as $y)
                            <option value="{{ $y }}" @selected(request('fiscal_year') === $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                @endif
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('plans.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Plan</a>
        </x-slot>
    </x-page-header>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Fiscal Year</th>
                        <th class="py-3 px-4">Items</th>
                        <th class="py-3 px-4">Estimated Cost</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($plans as $plan)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4"><a href="{{ route('plans.show', $plan) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $plan->title }}</a></td>
                            <td class="py-3 px-4 text-slate-500">{{ $plan->fiscal_year }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $plan->items_count }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ currentOrganization()->currency }} {{ number_format($plan->totalEstimatedCost(), 0) }}</td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($plan->status) }}">{{ $plan->statusLabel() }}</span></td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('plans.show', $plan) }}" class="text-xs text-emerald-600 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No procurement plans yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
