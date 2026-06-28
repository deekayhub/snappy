<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->userNotifications()
            ->latest()
            ->paginate(20);

        return view('supplier-panel.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, UserNotification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return redirect()->route('supplier-panel.notifications.index')
                ->with('error', 'Notification not found.');
        }

        $notification->update(['is_read' => true]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->route('supplier-panel.notifications.index');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->userNotifications()
            ->unread()
            ->update(['is_read' => true]);

        return redirect()->route('supplier-panel.notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    public function customerIndex(Request $request): View
    {
        $notifications = $request->user()
            ->userNotifications()
            ->latest()
            ->paginate(20);

        return view('customer-panel.notifications.index', compact('notifications'));
    }

    public function customerMarkAsRead(Request $request, UserNotification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return redirect()->route('customer-panel.notifications.index')
                ->with('error', 'Notification not found.');
        }

        $notification->update(['is_read' => true]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->route('customer-panel.notifications.index');
    }

    public function customerMarkAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->userNotifications()
            ->unread()
            ->update(['is_read' => true]);

        return redirect()->route('customer-panel.notifications.index')
            ->with('success', 'All notifications marked as read.');
    }
}
