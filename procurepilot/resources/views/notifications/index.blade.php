<x-app-layout>
    <x-slot name="header">Notifications</x-slot>

    <x-page-header title="Notifications" :description="'You have ' . $notifications->total() . ' notification(s)'">
        <x-slot name="actions">
            @if (auth()->user()->unreadNotifications()->count())
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Mark all as read</button>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="hd-card divide-y divide-slate-100">
        @forelse ($notifications as $notification)
            @php
                $payload = $notification->data;
                $type = data_get($payload, 'type', 'info');
                $iconColors = [
                    'success' => 'bg-emerald-100 text-emerald-600',
                    'warning' => 'bg-amber-100 text-amber-600',
                    'danger'  => 'bg-red-100 text-red-600',
                    'info'    => 'bg-blue-100 text-blue-600',
                ];
            @endphp
            <div class="flex items-start gap-4 px-6 py-4 {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 {{ $iconColors[$type] ?? $iconColors['info'] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-sm font-semibold text-slate-900">{{ data_get($payload, 'title') }}</h4>
                        <span class="text-xs text-slate-400 shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-slate-600 mt-0.5">{{ data_get($payload, 'body') }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        @if (data_get($payload, 'url'))
                            <a href="{{ route('notifications.read', $notification->getKey()) }}" class="text-xs text-emerald-600 font-medium">Open</a>
                        @endif
                        @if (! $notification->read_at)
                            <form action="{{ route('notifications.read-mark', $notification->getKey()) }}" method="POST">
                                @csrf
                                <button class="text-xs text-slate-400 hover:text-slate-600 font-medium">Mark as read</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                No notifications yet.
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $notifications->links() }}</div>
</x-app-layout>
