<x-app-layout>
    <x-slot name="header">Award</x-slot>

    <x-page-header :title="'Award for ' . ($award->tender?->title ?? '')">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($award->status) }}">{{ ucfirst($award->status) }}</span>
            @if ($award->status === 'recommended' && auth()->user()->isApprover())
                <button onclick="document.getElementById('decline-modal').showModal()" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Decline</button>
                <form method="POST" action="{{ route('awards.approve', $award) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Approve Award</button>
                </form>
            @endif
            @if ($award->status === 'approved' && ! $award->contract)
                <form method="POST" action="{{ route('awards.contract', $award) }}">
                    @csrf
                    <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Create Contract</button>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Award Summary</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-slate-500">Tender</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $award->tender?->title }}</dd></div>
                <div><dt class="text-slate-500">Tender Reference</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $award->tender?->reference }}</dd></div>
                <div><dt class="text-slate-500">Supplier</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $award->supplier?->name }}</dd></div>
                <div><dt class="text-slate-500">Bid Reference</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $award->bid?->reference }}</dd></div>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Award Amount</dt>
                    <dd class="text-2xl font-bold text-slate-900 mt-0.5">{{ $award->currency }} {{ number_format($award->award_amount, 2) }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Justification</dt>
                    <dd class="font-medium text-slate-900 mt-0.5 whitespace-pre-line">{{ $award->justification ?? 'Not provided' }}</dd>
                </div>
                <div><dt class="text-slate-500">Recommended by</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $award->decisionMaker?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Decided at</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $award->decided_at?->format('d M Y H:i') ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6">
            @if ($award->contract)
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                    <h4 class="font-semibold text-emerald-800 text-sm mb-2">Contract Created</h4>
                    <a href="{{ route('contracts.show', $award->contract) }}" class="inline-block px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg">View Contract</a>
                </div>
            @endif

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Bid Scores</h3>
                @if ($award->bid?->scores->isNotEmpty())
                    <div class="space-y-2 text-sm">
                        @foreach ($award->bid->scores as $score)
                            <div class="flex justify-between">
                                <span class="text-slate-600">{{ $score->criterion?->name }}</span>
                                <span class="font-medium">{{ $score->score }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between pt-2 border-t border-slate-100">
                            <span class="font-medium">Technical Score</span>
                            <span class="font-bold text-violet-700">{{ number_format($award->bid->technical_score ?? 0, 2) }}%</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-500">No scores recorded.</p>
                @endif
            </div>
        </div>
    </div>

    <dialog id="decline-modal" class="rounded-xl p-6 w-full max-w-md">
        <h3 class="font-semibold text-slate-900 mb-4">Decline Award</h3>
        <form method="POST" action="{{ route('awards.decline', $award) }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="reason" value="Reason for declining" />
                <textarea id="reason" name="reason" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('decline-modal').close()" class="px-4 py-2 text-sm text-slate-600">Cancel</button>
                <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg">Decline Award</button>
            </div>
        </form>
    </dialog>
</x-app-layout>
