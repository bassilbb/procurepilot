<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name', 'ProcurePilot') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-300">
    <header class="sticky top-0 z-30 bg-slate-950/80 backdrop-blur border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-extrabold text-lg">P</div>
                <span class="text-white font-bold text-lg">{{ config('app.name') }}</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="{{ route('about') }}" class="text-slate-300 hover:text-white {{ request()->routeIs('about') ? 'text-emerald-400' : '' }}">About</a>
                <a href="{{ route('features') }}" class="text-slate-300 hover:text-white {{ request()->routeIs('features') ? 'text-emerald-400' : '' }}">Features</a>
                <a href="{{ route('how-it-works') }}" class="text-slate-300 hover:text-white {{ request()->routeIs('how-it-works') ? 'text-emerald-400' : '' }}">How It Works</a>
                <a href="{{ route('faq') }}" class="text-slate-300 hover:text-white {{ request()->routeIs('faq') ? 'text-emerald-400' : '' }}">FAQ</a>
                <a href="{{ route('help') }}" class="text-slate-300 hover:text-white {{ request()->routeIs('help') ? 'text-emerald-400' : '' }}">Help</a>
                <a href="{{ route('contact') }}" class="text-slate-300 hover:text-white {{ request()->routeIs('contact') ? 'text-emerald-400' : '' }}">Contact</a>
            </nav>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-slate-300 hover:text-white">Sign in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">Get Started</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-extrabold">P</div>
                    <span class="text-white font-bold">{{ config('app.name') }}</span>
                </div>
                <p class="mt-3 text-sm text-slate-500">Global procurement, tendering and supplier management for port regulators, shippers and trading organizations.</p>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Product</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('features') }}" class="hover:text-emerald-400">Features</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="hover:text-emerald-400">How It Works</a></li>
                    <li><a href="{{ route('billing.plan') }}" class="hover:text-emerald-400">Pricing</a></li>
                    <li><a href="{{ route('help') }}" class="hover:text-emerald-400">User Guide</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Company</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('about') }}" class="hover:text-emerald-400">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-emerald-400">Contact / Support</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-emerald-400">FAQ</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-emerald-400">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-emerald-400">Terms &amp; Conditions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Compliance</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li>Public Procurement Act 2007</li>
                    <li>ISO 20400 Sustainable Procurement</li>
                    <li>World Bank Procurement Framework</li>
                    <li>Full audit trail &amp; transparency</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
            © {{ date('Y') }} {{ config('app.name') }} · Public Procurement Act 2007 compliant
        </div>
    </footer>
</body>
</html>
