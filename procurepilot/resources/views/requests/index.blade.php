<x-app-layout>
    <x-slot name="header">Procurement Requests</x-slot>

    <x-page-header title="Procurement Requests" description="Departmental requests routed through the approval workflow">
        <x-slot name="actions">
            <form method="GET" action="{{ route('requests.index') }}" class="flex gap-2">
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords($status) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('requests.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Request</a>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Requests" :value="$counts['total']" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        <x-stat-card label="Pending Approval" :value="$counts['pending']" icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-stat-card label="Approved" :value="$counts['approved']" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-stat-card label="Rejected" :value="$counts['rejected']" icon="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </div>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Department</th>
                        <th class="py-3 px-4">Requester</th>
                        <th class="py-3 px-4">Est. Cost</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($requests as $pr)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $pr->reference }}</td>
                            <td class="py-3 px-4"><a href="{{ route('requests.show', $pr) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $pr->title }}</a></td>
                            <td class="py-3 px-4 text-slate-700">{{ $pr->department?->name ?? '—' }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $pr->requester?->name ?? '—' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $pr->currency }} {{ number_format($pr->estimated_cost, 0) }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $pr->priority === 'critical' ? 'bg-red-100 text-red-800 border-red-200' : ($pr->priority === 'high' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-700 border-slate-200') }}">{{ ucfirst($pr->priority) }}</span>
                            </td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($pr->status) }}">{{ $pr->statusLabel() }}</span></td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('requests.show', $pr) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No procurement requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $requests->links() }}</div>
</x-app-layout>
