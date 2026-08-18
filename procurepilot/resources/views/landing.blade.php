<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Global Procurement Management System</title>
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
                    <a href="{{ route('about') }}" class="text-slate-300 hover:text-white">About</a>
                    <a href="{{ route('features') }}" class="text-slate-300 hover:text-white">Features</a>
                    <a href="{{ route('how-it-works') }}" class="text-slate-300 hover:text-white">How It Works</a>
                    <a href="{{ route('faq') }}" class="text-slate-300 hover:text-white">FAQ</a>
                    <a href="{{ route('public.requirements') }}" class="text-slate-300 hover:text-white">Supplier Requirements</a>
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

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-40 w-[500px] h-[500px] bg-sky-500/10 rounded-full blur-3xl"></div>

        {{-- dotted world map --}}
        <div class="absolute inset-0 opacity-20">
            <svg class="w-full h-full" viewBox="0 0 1200 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g fill="#e2e8f0">
                    <circle cx="90" cy="140" r="2"/><circle cx="115" cy="128" r="2"/><circle cx="140" cy="145" r="2"/><circle cx="165" cy="130" r="2"/><circle cx="190" cy="150" r="2"/><circle cx="215" cy="135" r="2"/><circle cx="240" cy="155" r="2"/><circle cx="265" cy="140" r="2"/><circle cx="290" cy="160" r="2"/><circle cx="315" cy="145" r="2"/><circle cx="340" cy="165" r="2"/><circle cx="365" cy="150" r="2"/><circle cx="390" cy="170" r="2"/><circle cx="415" cy="155" r="2"/><circle cx="440" cy="175" r="2"/><circle cx="465" cy="160" r="2"/><circle cx="490" cy="180" r="2"/><circle cx="515" cy="165" r="2"/><circle cx="540" cy="185" r="2"/><circle cx="565" cy="170" r="2"/><circle cx="590" cy="190" r="2"/><circle cx="615" cy="175" r="2"/><circle cx="640" cy="195" r="2"/><circle cx="665" cy="180" r="2"/><circle cx="690" cy="200" r="2"/><circle cx="715" cy="185" r="2"/><circle cx="740" cy="205" r="2"/><circle cx="765" cy="190" r="2"/><circle cx="790" cy="210" r="2"/><circle cx="815" cy="195" r="2"/><circle cx="840" cy="215" r="2"/><circle cx="865" cy="200" r="2"/><circle cx="890" cy="220" r="2"/><circle cx="915" cy="205" r="2"/><circle cx="940" cy="225" r="2"/><circle cx="965" cy="210" r="2"/><circle cx="990" cy="230" r="2"/><circle cx="1015" cy="215" r="2"/><circle cx="1040" cy="235" r="2"/><circle cx="1065" cy="220" r="2"/><circle cx="1090" cy="240" r="2"/><circle cx="1115" cy="225" r="2"/><circle cx="1140" cy="245" r="2"/>
                    <circle cx="100" cy="210" r="2"/><circle cx="125" cy="198" r="2"/><circle cx="150" cy="215" r="2"/><circle cx="175" cy="200" r="2"/><circle cx="200" cy="220" r="2"/><circle cx="225" cy="205" r="2"/><circle cx="250" cy="225" r="2"/><circle cx="275" cy="210" r="2"/><circle cx="300" cy="230" r="2"/><circle cx="325" cy="215" r="2"/><circle cx="350" cy="235" r="2"/><circle cx="375" cy="220" r="2"/><circle cx="400" cy="240" r="2"/><circle cx="425" cy="225" r="2"/><circle cx="450" cy="245" r="2"/><circle cx="475" cy="230" r="2"/><circle cx="500" cy="250" r="2"/><circle cx="525" cy="235" r="2"/><circle cx="550" cy="255" r="2"/><circle cx="575" cy="240" r="2"/><circle cx="600" cy="260" r="2"/><circle cx="625" cy="245" r="2"/><circle cx="650" cy="265" r="2"/><circle cx="675" cy="250" r="2"/><circle cx="700" cy="270" r="2"/><circle cx="725" cy="255" r="2"/><circle cx="750" cy="275" r="2"/><circle cx="775" cy="260" r="2"/><circle cx="800" cy="280" r="2"/><circle cx="825" cy="265" r="2"/><circle cx="850" cy="285" r="2"/><circle cx="875" cy="270" r="2"/><circle cx="900" cy="290" r="2"/><circle cx="925" cy="275" r="2"/><circle cx="950" cy="295" r="2"/><circle cx="975" cy="280" r="2"/><circle cx="1000" cy="300" r="2"/><circle cx="1025" cy="285" r="2"/><circle cx="1050" cy="305" r="2"/><circle cx="1075" cy="290" r="2"/><circle cx="1100" cy="310" r="2"/><circle cx="1125" cy="295" r="2"/><circle cx="1150" cy="315" r="2"/>
                    <circle cx="95" cy="330" r="2"/><circle cx="120" cy="318" r="2"/><circle cx="145" cy="335" r="2"/><circle cx="170" cy="320" r="2"/><circle cx="195" cy="340" r="2"/><circle cx="220" cy="325" r="2"/><circle cx="245" cy="345" r="2"/><circle cx="270" cy="330" r="2"/><circle cx="295" cy="350" r="2"/><circle cx="320" cy="335" r="2"/><circle cx="345" cy="355" r="2"/><circle cx="370" cy="340" r="2"/><circle cx="395" cy="360" r="2"/><circle cx="420" cy="345" r="2"/><circle cx="445" cy="365" r="2"/><circle cx="470" cy="350" r="2"/><circle cx="495" cy="370" r="2"/><circle cx="520" cy="355" r="2"/><circle cx="545" cy="375" r="2"/><circle cx="570" cy="360" r="2"/><circle cx="595" cy="380" r="2"/><circle cx="620" cy="365" r="2"/><circle cx="645" cy="385" r="2"/><circle cx="670" cy="370" r="2"/><circle cx="695" cy="390" r="2"/><circle cx="720" cy="375" r="2"/><circle cx="745" cy="395" r="2"/><circle cx="770" cy="380" r="2"/><circle cx="795" cy="400" r="2"/><circle cx="820" cy="385" r="2"/><circle cx="845" cy="405" r="2"/><circle cx="870" cy="390" r="2"/><circle cx="895" cy="410" r="2"/><circle cx="920" cy="395" r="2"/><circle cx="945" cy="415" r="2"/><circle cx="970" cy="400" r="2"/><circle cx="995" cy="420" r="2"/><circle cx="1020" cy="405" r="2"/><circle cx="1045" cy="425" r="2"/><circle cx="1070" cy="410" r="2"/><circle cx="1095" cy="430" r="2"/><circle cx="1120" cy="415" r="2"/><circle cx="1145" cy="435" r="2"/>
                </g>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 lg:py-28 text-center relative">
            <span class="inline-block px-3 py-1 text-xs font-semibold text-emerald-400 bg-emerald-400/10 border border-emerald-400/30 rounded-full mb-6">Public Procurement Act 2007 · ISO 20400 · World Bank Compliant</span>
            <h1 class="text-4xl sm:text-6xl font-extrabold text-white leading-tight max-w-4xl mx-auto">
                Procurement for a<br class="hidden sm:block">
                <span class="text-emerald-400">Connected World</span>
            </h1>
            <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
                An end-to-end procurement platform for port regulators, shippers and global trade — from annual planning to tendering, evaluation, award and contract management, with complete audit transparency.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">Start Free Trial</a>
                <a href="{{ route('login') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg">Sign In</a>
                <a href="{{ route('public.requirements.pdf') }}" target="_blank" class="px-6 py-3 border border-emerald-500/40 text-emerald-400 hover:bg-emerald-500/10 font-semibold rounded-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Download Requirements (PDF)
                </a>
            </div>
            <div class="mt-10 text-xs text-slate-500">No credit card required · 14-day free trial · Cancel anytime</div>
        </div>

        {{-- animated logistics strip --}}
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 pb-10">
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden p-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 items-end">
                    {{-- plane --}}
                    <div class="flex flex-col items-center gap-2">
                        <div class="animate-[fly_8s_ease-in-out_infinite]">
                            <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M2 16l10-5 10 5-10 5z"/><path d="M7 16l5 5 3-4" fill="none" stroke="currentColor" stroke-width="1"/></svg>
                        </div>
                        <span class="text-xs text-slate-400 uppercase tracking-wider">Air Freight</span>
                    </div>
                    {{-- ship --}}
                    <div class="flex flex-col items-center gap-2">
                        <div class="animate-pulse">
                            <svg class="w-24 h-20" viewBox="0 0 160 80" fill="none">
                                <path d="M20 45 L45 20 L115 20 L140 45 Z" fill="#e2e8f0"/>
                                <rect x="58" y="10" width="44" height="14" rx="2" fill="#94a3b8"/>
                                <path d="M35 45 L125 45 L135 52 L25 52 Z" fill="#64748b"/>
                                <circle cx="55" cy="42" r="3" fill="#1e293b"/>
                                <circle cx="105" cy="42" r="3" fill="#1e293b"/>
                            </svg>
                        </div>
                        <span class="text-xs text-slate-400 uppercase tracking-wider">Sea Freight</span>
                    </div>
                    {{-- truck --}}
                    <div class="flex flex-col items-center gap-2">
                        <div class="animate-[bob_5s_ease-in-out_infinite]">
                            <svg class="w-24 h-16" viewBox="0 0 128 64" fill="none">
                                <rect x="4" y="22" width="44" height="22" rx="2" fill="#fbbf24"/>
                                <rect x="52" y="14" width="52" height="30" rx="3" fill="#e2e8f0"/>
                                <rect x="56" y="18" width="44" height="14" rx="1" fill="#94a3b8"/>
                                <path d="M104 24 L116 24 L120 32 L116 44 L96 44 L96 24 Z" fill="#cbd5e1"/>
                                <circle cx="26" cy="46" r="7" fill="#1e293b"/>
                                <circle cx="26" cy="46" r="3" fill="#64748b"/>
                                <circle cx="90" cy="46" r="7" fill="#1e293b"/>
                                <circle cx="90" cy="46" r="3" fill="#64748b"/>
                                <circle cx="112" cy="46" r="7" fill="#1e293b"/>
                                <circle cx="112" cy="46" r="3" fill="#64748b"/>
                            </svg>
                        </div>
                        <span class="text-xs text-slate-400 uppercase tracking-wider">Road Haulage</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ['Plan & Approve', 'Annual procurement plans with Tenders Board approval workflow', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['Tender & RFQ', 'Open competitive bidding, restricted invitations, deadline enforcement', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['Evaluate & Award', 'Weighted scoring matrix, sealed bids, award recommendation & approval', 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 00-2 2h2zm5 2a5 5 0 01-10 0h10z'],
                    ['Contract & Fulfil', 'Contracts, milestones, purchase orders and goods receipts', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ];
            @endphp
            @foreach ($features as [$title, $desc, $icon])
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <div class="w-11 h-11 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="text-white font-semibold">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-slate-400">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- How it works --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-center text-2xl font-bold text-white mb-10">From request to delivery in one flow</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @php
                $steps = [
                    ['Submit', 'Procurement plan & request', 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                    ['Approve', 'Tenders Board sign-off', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Tender', 'Competitive bidding', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['Award', 'Evaluation & contract', 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 00-2 2h2zm5 2a5 5 0 01-10 0h10z'],
                    ['Fulfil', 'PO & goods receipt', 'M8 7h8m-8 5h8m-4 4h4M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z'],
                ];
            @endphp
            @foreach ($steps as $i => [$title, $desc, $icon])
                <div class="relative bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    @if (!$loop->last)
                        <div class="hidden md:block absolute top-8 -right-3 z-10 text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>
                    @endif
                    <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center text-slate-300 text-sm font-bold mb-4">{{ $i + 1 }}</div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="text-white font-semibold text-sm">{{ $title }}</h3>
                    <p class="mt-1 text-xs text-slate-400">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Plans --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-center text-2xl font-bold text-white mb-10">Subscription plans for every team</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach (\App\Models\Plan::where('is_active', true)->orderBy('price_monthly')->get() as $plan)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 {{ $plan->is_popular ? 'ring-2 ring-emerald-500' : '' }}">
                    @if ($plan->is_popular)<span class="text-xs font-semibold text-emerald-400">Most Popular</span>@endif
                    <h3 class="text-lg font-bold text-white mt-1">{{ $plan->name }}</h3>
                    <div class="mt-3 text-2xl font-bold text-white">₦{{ number_format($plan->price_monthly, 0) }}<span class="text-sm font-normal text-slate-500">/mo</span></div>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        @foreach (($plan->features ?? []) as $feature)
                            <li class="flex gap-2"><span class="text-emerald-400">✓</span>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="mt-6 block text-center px-4 py-2.5 rounded-lg text-sm font-medium {{ $plan->is_popular ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-white' }}">Start Free Trial</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl px-6 py-14 text-center">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-black/10 rounded-full blur-2xl"></div>
            <h2 class="relative text-3xl font-extrabold text-white">Ready to move your procurement worldwide?</h2>
            <p class="relative mt-3 text-emerald-100 max-w-xl mx-auto">Join port regulators and shippers running transparent, compliant procurement across air, sea and road.</p>
            <a href="{{ route('register') }}" class="relative mt-8 inline-block px-8 py-3 bg-white text-emerald-700 font-semibold rounded-lg shadow-lg hover:bg-emerald-50">Start Your Free Trial</a>
        </div>
    </section>

    <footer class="border-t border-slate-800 py-8 text-center text-xs text-slate-500">
        {{ config('app.name') }} · Built to the standard of the Nigerian Shippers' Council procurement framework · Public Procurement Act 2007 ·
        <a href="{{ route('privacy') }}" class="hover:text-slate-300">Privacy</a> ·
        <a href="{{ route('terms') }}" class="hover:text-slate-300">Terms</a>
    </footer>

    <style>
        @keyframes fly {
            0%, 100% { transform: translateY(0); }
            25% { transform: translateY(-14px); }
            50% { transform: translateY(4px); }
            75% { transform: translateY(-10px); }
        }
        @keyframes bob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
    </style>
</body>
</html>
