<x-app-layout>
    <x-slot name="header">Tenders & RFQs</x-slot>

    <x-page-header title="Tenders / Requests for Quotation" description="Open competitive bidding lifecycle — PPA 2007 compliant">
        <x-slot name="actions">
            <form method="GET" action="{{ route('tenders.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tenders..." class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('tenders.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Tender</a>
        </x-slot>
    </x-page-header>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Closing</th>
                        <th class="py-3 px-4">Bids</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($tenders as $tender)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $tender->reference }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('tenders.show', $tender) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $tender->title }}</a>
                                <div class="text-xs text-slate-400">{{ $tender->typeLabel() }} · {{ $tender->budget ? currentOrganization()->currency . ' ' . number_format($tender->budget, 0) : 'Budget: —' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $tender->category?->name ?? '—' }}</td>
                            <td class="py-3 px-4 text-xs text-slate-500">{{ $tender->methodLabel() }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $tender->closing_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold {{ $tender->bids_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-400' }}">{{ $tender->bids_count }}</span>
                            </td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($tender->status) }}">{{ $tender->statusLabel() }}</span></td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('tenders.show', $tender) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No tenders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $tenders->links() }}</div>
</x-app-layout>
