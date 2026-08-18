<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ProcurePilot') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">

            {{-- Brand / Hero panel --}}
            <div class="hidden lg:flex relative flex-col justify-between overflow-hidden bg-slate-950 text-white p-10 min-h-screen">
                {{-- gradient glow --}}
                <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 -right-24 w-80 h-80 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>

                {{-- dotted world map --}}
                <div class="absolute inset-0 opacity-30">
                    <svg class="w-full h-full" viewBox="0 0 900 700" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g fill="#e2e8f0">
                            <circle cx="120" cy="180" r="2"/><circle cx="140" cy="170" r="2"/><circle cx="160" cy="185" r="2"/><circle cx="180" cy="175" r="2"/><circle cx="200" cy="190" r="2"/><circle cx="220" cy="180" r="2"/><circle cx="240" cy="195" r="2"/><circle cx="260" cy="185" r="2"/><circle cx="280" cy="200" r="2"/><circle cx="300" cy="190" r="2"/><circle cx="320" cy="205" r="2"/><circle cx="340" cy="195" r="2"/><circle cx="360" cy="210" r="2"/><circle cx="380" cy="200" r="2"/><circle cx="400" cy="215" r="2"/><circle cx="420" cy="205" r="2"/><circle cx="440" cy="220" r="2"/><circle cx="460" cy="210" r="2"/><circle cx="480" cy="225" r="2"/><circle cx="500" cy="215" r="2"/><circle cx="520" cy="230" r="2"/><circle cx="540" cy="220" r="2"/><circle cx="560" cy="235" r="2"/><circle cx="580" cy="225" r="2"/><circle cx="600" cy="240" r="2"/><circle cx="620" cy="230" r="2"/><circle cx="640" cy="245" r="2"/><circle cx="660" cy="235" r="2"/><circle cx="680" cy="250" r="2"/><circle cx="700" cy="240" r="2"/><circle cx="720" cy="255" r="2"/><circle cx="740" cy="245" r="2"/><circle cx="760" cy="260" r="2"/><circle cx="780" cy="250" r="2"/>
                            <circle cx="130" cy="230" r="2"/><circle cx="150" cy="220" r="2"/><circle cx="170" cy="235" r="2"/><circle cx="190" cy="225" r="2"/><circle cx="210" cy="240" r="2"/><circle cx="230" cy="230" r="2"/><circle cx="250" cy="245" r="2"/><circle cx="270" cy="235" r="2"/><circle cx="290" cy="250" r="2"/><circle cx="310" cy="240" r="2"/><circle cx="330" cy="255" r="2"/><circle cx="350" cy="245" r="2"/><circle cx="370" cy="260" r="2"/><circle cx="390" cy="250" r="2"/><circle cx="410" cy="265" r="2"/><circle cx="430" cy="255" r="2"/><circle cx="450" cy="270" r="2"/><circle cx="470" cy="260" r="2"/><circle cx="490" cy="275" r="2"/><circle cx="510" cy="265" r="2"/><circle cx="530" cy="280" r="2"/><circle cx="550" cy="270" r="2"/><circle cx="570" cy="285" r="2"/><circle cx="590" cy="275" r="2"/><circle cx="610" cy="290" r="2"/><circle cx="630" cy="280" r="2"/><circle cx="650" cy="295" r="2"/><circle cx="670" cy="285" r="2"/><circle cx="690" cy="300" r="2"/><circle cx="710" cy="290" r="2"/><circle cx="730" cy="305" r="2"/><circle cx="750" cy="295" r="2"/><circle cx="770" cy="310" r="2"/><circle cx="790" cy="300" r="2"/>
                            <circle cx="120" cy="330" r="2"/><circle cx="140" cy="320" r="2"/><circle cx="160" cy="335" r="2"/><circle cx="180" cy="325" r="2"/><circle cx="200" cy="340" r="2"/><circle cx="220" cy="330" r="2"/><circle cx="240" cy="345" r="2"/><circle cx="260" cy="335" r="2"/><circle cx="280" cy="350" r="2"/><circle cx="300" cy="340" r="2"/><circle cx="320" cy="355" r="2"/><circle cx="340" cy="345" r="2"/><circle cx="360" cy="360" r="2"/><circle cx="380" cy="350" r="2"/><circle cx="400" cy="365" r="2"/><circle cx="420" cy="355" r="2"/><circle cx="440" cy="370" r="2"/><circle cx="460" cy="360" r="2"/><circle cx="480" cy="375" r="2"/><circle cx="500" cy="365" r="2"/><circle cx="520" cy="380" r="2"/><circle cx="540" cy="370" r="2"/><circle cx="560" cy="385" r="2"/><circle cx="580" cy="375" r="2"/><circle cx="600" cy="390" r="2"/><circle cx="620" cy="380" r="2"/><circle cx="640" cy="395" r="2"/><circle cx="660" cy="385" r="2"/><circle cx="680" cy="400" r="2"/><circle cx="700" cy="390" r="2"/><circle cx="720" cy="405" r="2"/><circle cx="740" cy="395" r="2"/><circle cx="760" cy="410" r="2"/><circle cx="780" cy="400" r="2"/>
                            <circle cx="130" cy="470" r="2"/><circle cx="150" cy="460" r="2"/><circle cx="170" cy="475" r="2"/><circle cx="190" cy="465" r="2"/><circle cx="210" cy="480" r="2"/><circle cx="230" cy="470" r="2"/><circle cx="250" cy="485" r="2"/><circle cx="270" cy="475" r="2"/><circle cx="290" cy="490" r="2"/><circle cx="310" cy="480" r="2"/><circle cx="330" cy="495" r="2"/><circle cx="350" cy="485" r="2"/><circle cx="370" cy="500" r="2"/><circle cx="390" cy="490" r="2"/><circle cx="410" cy="505" r="2"/><circle cx="430" cy="495" r="2"/><circle cx="450" cy="510" r="2"/><circle cx="470" cy="500" r="2"/><circle cx="490" cy="515" r="2"/><circle cx="510" cy="505" r="2"/><circle cx="530" cy="520" r="2"/><circle cx="550" cy="510" r="2"/><circle cx="570" cy="525" r="2"/><circle cx="590" cy="515" r="2"/><circle cx="610" cy="530" r="2"/><circle cx="630" cy="520" r="2"/><circle cx="650" cy="535" r="2"/><circle cx="670" cy="525" r="2"/><circle cx="690" cy="540" r="2"/><circle cx="710" cy="530" r="2"/><circle cx="730" cy="545" r="2"/><circle cx="750" cy="535" r="2"/><circle cx="770" cy="550" r="2"/><circle cx="790" cy="540" r="2"/>
                        </g>
                    </svg>
                </div>

                {{-- top brand --}}
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-emerald-500/30">P</div>
                    <div>
                        <div class="text-lg font-bold leading-tight">{{ config('app.name', 'ProcurePilot') }}</div>
                        <div class="text-xs text-slate-400">Global Procurement Suite</div>
                    </div>
                </div>

                {{-- animated scene --}}
                <div class="relative z-10 flex-1 flex items-center">
                    <div class="w-full space-y-10">

                        <div class="relative h-40">
                            {{-- plane flying over the map --}}
                            <div class="absolute top-0 left-8 animate-[fly_9s_ease-in-out_infinite]">
                                <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M2 16l10-5 10 5-10 5z"/><path d="M7 16l5 5 3-4" fill="none" stroke="currentColor" stroke-width="1"/></svg>
                            </div>
                            {{-- route arc --}}
                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 600 160" fill="none">
                                <path d="M20 130 Q 200 20 560 110" stroke="#34d399" stroke-width="2" stroke-dasharray="6 6" class="opacity-70"/>
                                <circle cx="20" cy="130" r="6" fill="#34d399" class="opacity-90"/>
                                <circle cx="560" cy="110" r="6" fill="#34d399" class="opacity-90"/>
                            </svg>
                        </div>

                        {{-- ship on waves --}}
                        <div class="relative h-24">
                            <svg class="absolute bottom-0 left-0 w-40 h-20" viewBox="0 0 160 80" fill="none">
                                <path d="M20 45 L45 20 L115 20 L140 45 Z" fill="#e2e8f0"/>
                                <rect x="58" y="10" width="44" height="14" rx="2" fill="#94a3b8"/>
                                <path d="M35 45 L125 45 L135 52 L25 52 Z" fill="#64748b"/>
                                <circle cx="55" cy="42" r="3" fill="#1e293b"/>
                                <circle cx="105" cy="42" r="3" fill="#1e293b"/>
                            </svg>
                            <div class="absolute bottom-0 left-32 w-64 h-4">
                                <svg class="w-full" viewBox="0 0 256 16" preserveAspectRatio="none">
                                    <path d="M0 8 Q 16 2 32 8 T 64 8 T 96 8 T 128 8 T 160 8 T 192 8 T 224 8 T 256 8" stroke="#38bdf8" stroke-width="3" fill="none" class="opacity-70"/>
                                </svg>
                            </div>
                            <div class="absolute bottom-0 right-0 animate-pulse">
                                <svg class="w-16 h-10" viewBox="0 0 64 40" fill="none">
                                    <path d="M6 18 L22 6 L44 6 L58 18 Z" fill="#cbd5e1"/>
                                    <path d="M10 18 L54 18 L56 22 L8 22 Z" fill="#475569"/>
                                    <circle cx="22" cy="18" r="2" fill="#1e293b"/>
                                    <circle cx="42" cy="18" r="2" fill="#1e293b"/>
                                </svg>
                            </div>
                        </div>

                        {{-- truck on road --}}
                        <div class="relative h-28">
                            <div class="absolute bottom-4 left-0 animate-[drive_10s_linear_infinite]">
                                <svg class="w-32 h-16" viewBox="0 0 128 64" fill="none">
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
                            <svg class="absolute bottom-0 left-0 w-full h-4" viewBox="0 0 600 16" preserveAspectRatio="none">
                                <line x1="0" y1="8" x2="600" y2="8" stroke="#64748b" stroke-width="2" stroke-dasharray="14 10" class="opacity-80"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- tagline --}}
                <div class="relative z-10 space-y-2">
                    <h1 class="text-2xl font-bold leading-snug">Connecting global trade.<br>Powered by transparent procurement.</h1>
                    <p class="text-sm text-slate-400 max-w-md">Ships, air freight and road haulage — every consignment, every tender and every payment, managed on one compliant platform.</p>
                </div>
            </div>

            {{-- Form panel --}}
            <div class="relative flex flex-col min-h-screen bg-gray-50">
                <div class="lg:hidden flex items-center gap-2 px-6 pt-8">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-extrabold text-lg">P</div>
                    <span class="font-bold text-slate-900">{{ config('app.name', 'ProcurePilot') }}</span>
                </div>

                <div class="flex-1 flex flex-col justify-center px-6 py-10 sm:px-12 lg:px-16">
                    <div class="w-full max-w-md mx-auto">
                        {{ $slot }}
                    </div>
                </div>

                <div class="px-6 sm:px-12 lg:px-16 py-5">
                    <p class="text-xs text-slate-400 text-center">© {{ date('Y') }} {{ config('app.name', 'ProcurePilot') }} · Public Procurement Act 2007 compliant</p>
                </div>
            </div>
        </div>

        <style>
            @keyframes fly {
                0%, 100% { transform: translate(0, 0); }
                25% { transform: translate(40px, -12px); }
                50% { transform: translate(90px, 6px); }
                75% { transform: translate(140px, -8px); }
            }
            @keyframes drive {
                0% { transform: translateX(-140px); }
                100% { transform: translateX(calc(100vw - 100px)); }
            }
        </style>
    </body>
</html>
