<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()
            ?->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return back()->with('status', 'Notifikasi ditandai sudah dibaca.');
    }
}

