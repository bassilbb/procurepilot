@props(['label' => '', 'value' => 0, 'color' => 'bg-slate-500', 'href' => null, 'icon' => null, 'tone' => null])

@php
    $tones = [
        'emerald' => ['bg' => 'linear-gradient(135deg,#10b981,#059669)', 'glow' => 'rgba(16,185,129,.55)'],
        'blue'    => ['bg' => 'linear-gradient(135deg,#3b82f6,#2563eb)', 'glow' => 'rgba(59,130,246,.5)'],
        'violet'  => ['bg' => 'linear-gradient(135deg,#8b5cf6,#6d28d9)', 'glow' => 'rgba(139,92,246,.5)'],
        'amber'   => ['bg' => 'linear-gradient(135deg,#f59e0b,#d97706)', 'glow' => 'rgba(245,158,11,.5)'],
        'rose'    => ['bg' => 'linear-gradient(135deg,#f43f5e,#e11d48)', 'glow' => 'rgba(244,63,94,.5)'],
        'sky'     => ['bg' => 'linear-gradient(135deg,#0ea5e9,#0284c7)', 'glow' => 'rgba(14,165,233,.5)'],
        'slate'   => ['bg' => 'linear-gradient(135deg,#475569,#334155)', 'glow' => 'rgba(71,85,105,.5)'],
    ];
    $t = $tones[$tone] ?? $tones['slate'];
@endphp

<a href="{{ $href ?? '#' }}" class="hd-card p-4 block group">
    <div class="flex items-center justify-between relative z-[1]">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white"
             style="background: {{ $t['bg'] }}; box-shadow: 0 6px 14px -4px {{ $t['glow'] }}, inset 0 1px 0 rgba(255,255,255,.35);">
            @if ($icon)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
            @else
                <div class="w-6 h-6 rounded-full bg-white opacity-30"></div>
            @endif
        </div>
        <span class="text-xs text-slate-400 font-medium">{{ $label }}</span>
    </div>
    <div class="mt-3 text-2xl font-bold text-slate-900 relative z-[1]">{{ $value }}</div>
</a>
