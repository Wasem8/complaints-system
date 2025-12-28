<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\Response;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return Response::success($notifications, 'Notifications fetched successfully');
    }


    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->get();

        return Response::success($notifications, 'Unread notifications fetched successfully');
    }

    public function unreadCount(Request $request)
    {
        $count = $request->user()
            ->unreadNotifications()
            ->count();

        return Response::success([
            'unread_count' => $count
        ], 'Unread notifications count');
    }


    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->find($id);
        if (!$notification) {
            return Response::Error(null, 'Notification not found');
        }
        if($notification->read_at === null) {
            $notification->markAsRead();

            return Response::success(null, 'Notification marked as read');
        }
        return Response::Error(false, 'Notification already marked as read');

    }


    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return Response::success(null, 'All notifications marked as read');
    }


    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->find($id);

        if (!$notification){
            return Response::Error(false, 'Notification not found');
        }
        $notification->delete();

        return Response::success(null, 'Notification deleted');
    }
}
