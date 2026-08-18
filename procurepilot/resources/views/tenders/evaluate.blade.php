<x-app-layout>
    <x-slot name="header">Evaluation Room</x-slot>

    <x-page-header :title="$tender->title" :description="'Reference: ' . $tender->reference . ' · ' . $tender->bids->count() . ' bids received'">
        <x-slot name="actions">
            <a href="{{ route('tenders.show', $tender) }}" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Back to Tender</a>
        </x-slot>
    </x-page-header>

    @if ($tender->bids->isEmpty())
        <x-empty-state title="No bids to evaluate" message="This tender received no bids." />
    @else
        <div class="hd-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-4">Supplier</th>
                        @foreach ($tender->bids as $bid)
                            <th class="py-3 px-4 text-center">{{ $bid->supplier?->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-700">Total Amount</td>
                        @foreach ($tender->bids as $bid)
                            <td class="py-3 px-4 text-center font-semibold text-slate-900">{{ $tender->currency }} {{ number_format($bid->total_amount, 2) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-700">Status</td>
                        @foreach ($tender->bids as $bid)
                            <td class="py-3 px-4 text-center"><span class="text-xs px-2 py-1 rounded-full border {{ statusBadgeClass($bid->status) }}">{{ $bid->statusLabel() }}</span></td>
                        @endforeach
                    </tr>
                    @foreach ($tender->criteria as $criterion)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-700">{{ $criterion->name }}</div>
                                <div class="text-xs text-slate-400">Weight {{ $criterion->weight }}%</div>
                            </td>
                            @foreach ($tender->bids as $bid)
                                @php $score = $bid->scores->firstWhere('criterion_id', $criterion->id); @endphp
                                <td class="py-3 px-4 text-center">
                                    @if ($score)
                                        <span class="font-semibold">{{ $score->score }}<span class="text-slate-400 text-xs">/{{ $criterion->max_score }}</span></span>
                                        @if ($score->comment)
                                            <div class="text-xs text-slate-400 mt-1">{{ $score->comment }}</div>
                                        @endif
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr class="bg-violet-50">
                        <td class="py-3 px-4 font-semibold text-violet-800">Weighted Technical Score</td>
                        @foreach ($tender->bids as $bid)
                            <td class="py-3 px-4 text-center font-bold text-violet-700">{{ number_format($bid->technical_score ?? 0, 2) }}%</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <h3 class="font-semibold text-slate-900 mb-3">Evaluate Individual Bids</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($tender->bids as $bid)
                    <a href="{{ route('bids.show', $bid) }}" class="hd-card p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-800">{{ $bid->supplier?->name }}</span>
                            <span class="text-xs px-2 py-1 rounded-full border {{ statusBadgeClass($bid->status) }}">{{ $bid->statusLabel() }}</span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-slate-900">{{ $tender->currency }} {{ number_format($bid->total_amount, 0) }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ $bid->reference }}</div>
                        <div class="mt-3 text-xs text-violet-600 font-medium">Score & evaluate →</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
