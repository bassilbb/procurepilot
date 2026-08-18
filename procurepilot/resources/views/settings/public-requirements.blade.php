<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Registration Requirements — {{ $org->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-300">
    <header class="sticky top-0 z-30 bg-slate-950/80 backdrop-blur border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-extrabold text-lg">P</div>
                <span class="text-white font-bold text-lg">{{ config('app.name') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <nav class="hidden md:flex items-center gap-6 text-sm mr-2">
                    <a href="{{ route('landing') }}" class="text-slate-300 hover:text-white">Home</a>
                    <a href="{{ route('about') }}" class="text-slate-300 hover:text-white">About</a>
                    <a href="{{ route('contact') }}" class="text-slate-300 hover:text-white">Contact</a>
                </nav>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-slate-300 hover:text-white">Sign in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">Get Started Free</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
        <div class="text-center mb-10">
            <span class="inline-block px-3 py-1 text-xs font-semibold text-emerald-400 bg-emerald-400/10 border border-emerald-400/30 rounded-full mb-4">Supplier Registration</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">Required Documents</h1>
            <p class="mt-4 text-slate-400 text-sm max-w-2xl mx-auto">
                {{ $org->name }} requires the following documents from suppliers during registration.
                Download the checklist below, prepare your documents, then complete your registration online.
            </p>
            <a href="{{ route('public.requirements.pdf') }}" target="_blank"
               class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Download Requirements Checklist (PDF)
            </a>
        </div>

        <div class="space-y-3">
            @foreach ($requirements as $idx => $requirement)
                <div class="rounded-xl border {{ $requirement->is_required ? 'border-slate-700 bg-slate-900/60' : 'border-dashed border-slate-700 bg-slate-900/30' }} p-5 flex items-start gap-4">
                    <span class="mt-0.5 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0 {{ $requirement->is_required ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700/50 text-slate-400' }}">{{ $idx + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-semibold text-white">{{ $requirement->name }}</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $requirement->is_required ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700/40 text-slate-400 border border-slate-600' }}">
                                {{ $requirement->is_required ? 'Required' : 'Optional' }}
                            </span>
                        </div>
                        @if ($requirement->description)
                            <p class="text-sm text-slate-400 mt-1">{{ $requirement->description }}</p>
                        @endif
                    </div>
                    <span class="text-slate-600 text-lg shrink-0">☐</span>
                </div>
            @endforeach
        </div>

        <div class="mt-10 rounded-xl border border-slate-800 bg-slate-900/50 p-6 text-center">
            <h3 class="font-semibold text-white">Ready to register?</h3>
            <p class="text-sm text-slate-400 mt-2">Prepare the documents above, then create your supplier account to start registration.</p>
            <a href="{{ route('register') }}" class="mt-4 inline-block px-6 py-2.5 bg-white text-emerald-700 font-semibold rounded-lg hover:bg-emerald-50">Get Started</a>
        </div>
    </main>

    <footer class="border-t border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} {{ config('app.name') }} · {{ $org->name }}
        </div>
    </footer>
</body>
</html>
