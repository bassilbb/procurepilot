<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $notification)
    {
        $request->user()->notifications()
            ->whereKey($notification)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function read(Request $request, string $notification)
    {
        $request->user()->notifications()
            ->whereKey($notification)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $notificationModel = auth()->user()->notifications()->whereKey($notification)->first();
        $url = data_get($notificationModel?->data, 'url');

        if ($url) {
            return redirect($url);
        }

        return redirect()->route('notifications.index');
    }
}
