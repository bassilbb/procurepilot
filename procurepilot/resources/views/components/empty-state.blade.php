@props(['title' => 'Nothing here yet', 'message' => 'There are no records to display.', 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'])

<div class="hd-card p-12 text-center">
    <div class="relative z-[1]">
        <div class="mx-auto w-16 h-16 rounded-2xl flex items-center justify-center text-emerald-600 mb-4" style="background: linear-gradient(135deg,#ecfdf5,#d1fae5); border:1px solid rgba(16,185,129,.25); box-shadow:0 10px 24px -10px rgba(16,185,129,.45);">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
        </div>
        <h3 class="font-semibold text-slate-900">{{ $title }}</h3>
        <p class="text-sm text-slate-500 mt-1">{{ $message }}</p>
        @if (isset($action))
            <div class="mt-4">{{ $action }}</div>
        @endif
    </div>
</div>
