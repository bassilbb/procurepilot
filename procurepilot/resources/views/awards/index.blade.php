<x-app-layout>
    <x-slot name="header">Awards</x-slot>

    <x-page-header title="Tenders Board Awards" description="Award recommendations and approvals — PPA 2007 §40" />

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Tender</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Decided</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($awards as $award)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4"><a href="{{ route('awards.show', $award) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $award->tender?->title }}</a></td>
                            <td class="py-3 px-4 text-slate-700">{{ $award->supplier?->name }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $award->currency }} {{ number_format($award->award_amount, 0) }}</td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($award->status) }}">{{ ucfirst($award->status) }}</span></td>
                            <td class="py-3 px-4 text-slate-500">{{ $award->decided_at?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('awards.show', $award) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No award recommendations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
