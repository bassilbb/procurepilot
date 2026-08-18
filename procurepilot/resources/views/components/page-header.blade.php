@props(['title' => '', 'description' => ''])

<div class="hd-hero mb-6">
    <div class="relative px-6 py-6 sm:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="hd-icon-tile">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h2 class="hd-page-title">{{ $title }}</h2>
                @if ($description)
                    <p class="hd-page-desc">{{ $description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{ $actions ?? '' }}
        </div>
    </div>
</div>
