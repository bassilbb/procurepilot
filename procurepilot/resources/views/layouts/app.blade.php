<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name', 'ProcurePilot') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans antialiased text-slate-800" style="background: linear-gradient(160deg,#eef2f7 0%,#e6edf5 45%,#eef7f3 100%);">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col lg:pl-64">
                <header class="sticky top-0 z-20" style="background: rgba(255,255,255,.88); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(148,163,184,.25); box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 6px 20px -8px rgba(15,23,42,.14);">
                    <div class="px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <h1 class="text-lg font-semibold text-slate-900">{{ isset($header) ? $header : '' }}</h1>
                        </div>
                        <div class="flex items-center gap-3">
                            @php
                                $org = currentOrganization();
                                $sub = $org?->subscription;
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                            @endphp
                            <a href="{{ route('notifications.index') }}" class="relative text-slate-500 hover:text-slate-700 p-1.5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @if ($unreadCount)
                                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </a>
                            @if ($sub && $sub->plan)
                                <a href="{{ route('billing.plan') }}" class="hidden sm:flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $sub->status === 'cancelled' ? 'bg-red-50 text-red-700' : ($sub->status === 'trialing' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700') }}">
                                    <span>{{ $sub->plan->name }}</span>
                                    <span class="opacity-60">{{ ucfirst($sub->status) }}</span>
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </a>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 sm:px-6 py-6 max-w-7xl w-full mx-auto">
                    @if (session('success'))
                        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                            <span>✓</span><span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 flex items-start gap-2">
                            <span>⚠</span><span>{{ session('warning') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                <footer class="px-4 sm:px-6 py-4 text-center text-xs text-slate-500 border-t" style="background: rgba(255,255,255,.7); border-color: rgba(148,163,184,.25); backdrop-filter: blur(8px);">
                    {{ config('app.name') }} · Procurement Management System — compliant with the Public Procurement Act 2007 & international best practice
                </footer>
            </div>
        </div>
    </body>
</html>
