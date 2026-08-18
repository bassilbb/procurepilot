<x-app-layout>
    <x-slot name="header">Bid Details</x-slot>

    <x-page-header :title="$bid->reference">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($bid->status) }}">{{ $bid->statusLabel() }}</span>
            <a href="{{ route('tenders.show', $bid->tender) }}" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Back to Tender</a>
            @if ($bid->status === 'evaluated' && ! $bid->award && auth()->user()->isApprover())
                <button onclick="document.getElementById('award-modal').showModal()" class="px-4 py-2 bg-violet-600 text-white text-sm rounded-lg hover:bg-violet-700">Recommend for Award</button>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Bid Summary</h3>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div class="sm:col-span-2"><dt class="text-slate-500">Tender</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $bid->tender->title }}</dd></div>
                    <div><dt class="text-slate-500">Supplier</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $bid->supplier?->name }}</dd></div>
                    <div><dt class="text-slate-500">Submitted</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $bid->submitted_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Total Amount</dt>
                        <dd class="text-2xl font-bold text-slate-900 mt-0.5">{{ $bid->currency }} {{ number_format($bid->total_amount, 2) }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Compliance Declaration</dt>
                        <dd class="font-medium text-slate-900 mt-0.5">{{ $bid->compliance_declaration ?? 'Not provided' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Bid Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                <th class="py-2 pr-3">Description</th>
                                <th class="py-2 pr-3">Qty</th>
                                <th class="py-2 pr-3">Unit</th>
                                <th class="py-2 pr-3">Unit Price</th>
                                <th class="py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($bid->items as $item)
                                <tr>
                                    <td class="py-2 pr-3 text-slate-800">{{ $item->description }}</td>
                                    <td class="py-2 pr-3">{{ $item->quantity }}</td>
                                    <td class="py-2 pr-3 text-slate-500">{{ $item->unit ?? '—' }}</td>
                                    <td class="py-2 pr-3">{{ $bid->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-2 font-medium">{{ $bid->currency }} {{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($bid->status !== 'withdrawn' && $bid->status !== 'awarded')
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">Evaluation Scores</h3>
                    @if ($bid->tender->criteria->isEmpty())
                        <p class="text-sm text-slate-500">No evaluation criteria defined for this tender.</p>
                    @elseif (auth()->user()->isApprover())
                        <form method="POST" action="{{ route('bids.score', $bid) }}" class="space-y-4">
                            @csrf
                            @foreach ($bid->tender->criteria as $criterion)
                                @php
                                    $existing = $bid->scores->firstWhere('criterion_id', $criterion->id);
                                @endphp
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <div class="col-span-6">
                                        <div class="text-sm font-medium text-slate-800">{{ $criterion->name }}</div>
                                        <div class="text-xs text-slate-400">Weight {{ $criterion->weight }}% · Max {{ $criterion->max_score }}</div>
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" name="scores[{{ $criterion->id }}]" min="0" max="{{ $criterion->max_score }}" step="0.01"
                                               value="{{ $existing?->score ?? '' }}" placeholder="Score"
                                               class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <div class="col-span-4">
                                        <input type="text" name="comments[{{ $criterion->id }}]" value="{{ $existing?->comment ?? '' }}" placeholder="Comment"
                                               class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex justify-end">
                                <button class="px-5 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700">Save Scores</button>
                            </div>
                        </form>
                    @else
                        <p class="text-sm text-slate-500">Scores are recorded by authorised evaluators.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($bid->scores->isNotEmpty())
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-3">Score Summary</h3>
                    <div class="space-y-2 text-sm">
                        @foreach ($bid->scores as $score)
                            <div class="flex justify-between items-center p-2 rounded-lg bg-slate-50">
                                <span class="text-slate-600">{{ $score->criterion?->name }}</span>
                                <span class="font-semibold">{{ $score->score }} / {{ $score->criterion?->max_score }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between pt-3 border-t border-slate-100">
                            <span class="font-medium text-slate-700">Weighted Technical Score</span>
                            <span class="font-bold text-violet-700">{{ number_format($bid->technical_score ?? 0, 2) }}%</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($bid->award)
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                    <h4 class="font-semibold text-emerald-800 text-sm mb-2">Award Record</h4>
                    <p class="text-sm text-emerald-700 mb-3">Status: <strong>{{ ucfirst($bid->award->status) }}</strong></p>
                    <a href="{{ route('awards.show', $bid->award) }}" class="inline-block px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg">View Award</a>
                </div>
            @endif

            @if ($bid->status === 'submitted' || $bid->status === 'evaluated')
                <form method="POST" action="{{ route('bids.withdraw', $bid) }}" onsubmit="return confirm('Withdraw this bid?')">
                    @csrf
                    <button class="w-full px-4 py-2 bg-red-50 text-red-600 text-sm rounded-lg border border-red-200 hover:bg-red-100">Withdraw Bid</button>
                </form>
            @endif
        </div>
    </div>

    @if ($bid->status === 'evaluated' && ! $bid->award && auth()->user()->isApprover())
        <dialog id="award-modal" class="rounded-xl p-6 w-full max-w-md">
            <h3 class="font-semibold text-slate-900 mb-4">Recommend for Award</h3>
            <form method="POST" action="{{ route('bids.recommend', $bid) }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="award_amount" value="Award Amount" />
                    <x-text-input id="award_amount" name="award_amount" type="number" step="0.01" min="0" class="mt-1 w-full" value="{{ $bid->total_amount }}" required />
                </div>
                <div>
                    <x-input-label for="justification" value="Justification" />
                    <textarea id="justification" name="justification" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('award-modal').close()" class="px-4 py-2 text-sm text-slate-600">Cancel</button>
                    <button class="px-4 py-2 bg-violet-600 text-white text-sm rounded-lg">Submit Recommendation</button>
                </div>
            </form>
        </dialog>
    @endif
</x-app-layout>
