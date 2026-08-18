<x-app-layout>
    <x-slot name="header">My Requests</x-slot>

    @php $org = currentOrganization(); @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="My Requests" :value="$counts['requests']" tone="sky" href="{{ route('requests.index') }}" />
        <x-stat-card label="Submitted" :value="$counts['submitted']" tone="violet" href="{{ route('requests.index') }}" />
        <x-stat-card label="Approved" :value="$counts['approved']" tone="emerald" href="{{ route('requests.index') }}" />
        <x-stat-card label="Drafts" :value="$counts['drafts']" tone="slate" href="{{ route('requests.index') }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="hd-card p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-900">My Procurement Requests</h3>
                <a href="{{ route('requests.create') }}" class="text-sm text-emerald-600 font-medium hover:text-emerald-700">New request →</a>
            </div>
            <div class="space-y-3">
                @forelse ($myRequests as $request)
                    <a href="{{ route('requests.show', $request) }}" class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0 group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold {{ statusBadgeClass($request->status) }} border">
                            {{ strtoupper(substr($request->status, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate group-hover:text-emerald-700">{{ $request->title }}</div>
                            <div class="text-xs text-slate-400">{{ $request->reference }} · {{ $org->currency }} {{ number_format((float) $request->estimated_cost, 0) }}</div>
                        </div>
                        <span class="text-xs text-emerald-600 font-medium shrink-0">View</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">You have not submitted any procurement requests yet.</p>
                @endforelse
            </div>
        </div>

        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Open Tenders</h3>
            <div class="space-y-3">
                @forelse ($recentTenders as $tender)
                    <a href="{{ route('tenders.show', $tender) }}" class="block p-3 rounded-lg bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50 transition-colors">
                        <div class="text-sm font-medium text-slate-800 truncate">{{ $tender->title }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ $tender->reference }} · closes {{ $tender->closing_at?->format('j M Y') }}</div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No open tenders right now.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-medium text-slate-600 mb-3">How approval works</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Requests route through the configured approval workflow based on cost. Track progress on each request page and you'll be notified at every step.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
