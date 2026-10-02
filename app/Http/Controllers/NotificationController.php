<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function studentIndex()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('student.notifications', compact('notifications'));
    }

    public function guidanceIndex()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('guidance.notifications', compact('notifications'));
    }

    public function adminIndex()
    {
        $notifications = Notification::with('user')
            ->whereIn('type', ['appointment_request', 'appointment_request_high', 'feedback', 'case_high_severity'])
            ->latest()
            ->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function show(Notification $notification)
    {
        if ($notification->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }
}