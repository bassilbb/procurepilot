@props(['title' => '', 'subtitle' => '', 'eyebrow' => ''])

<section class="relative overflow-hidden border-b border-slate-800">
    <div class="absolute -top-32 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
    <div class="absolute top-0 -right-32 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16 lg:py-20 text-center">
        @if ($eyebrow)
            <span class="inline-block px-3 py-1 text-xs font-semibold text-emerald-400 bg-emerald-400/10 border border-emerald-400/30 rounded-full mb-5">{{ $eyebrow }}</span>
        @endif
        <h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto">{{ $subtitle }}</p>
        @endif
    </div>
</section>
