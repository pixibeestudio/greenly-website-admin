<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     * Lấy danh sách thông báo của user đang đăng nhập (mới nhất trước).
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $notifications = Notification::with('order')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($notification) {
                return [
                    'id'         => $notification->id,
                    'order_id'   => $notification->order_id,
                    'order_code' => $notification->order ? $notification->order->order_code : null,
                    'type'       => $notification->type,
                    'title'      => $notification->title,
                    'message'    => $notification->message,
                    'is_read'    => $notification->is_read,
                    'time_ago'   => $notification->created_at->diffForHumans(),
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $notifications,
        ]);
    }

    /**
     * GET /api/notifications/unread-count
     * Đếm số thông báo chưa đọc (dùng cho badge count).
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * POST /api/notifications/{id}/read
     * Đánh dấu 1 thông báo đã đọc.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu đã đọc.',
        ]);
    }

    /**
     * POST /api/notifications/read-all
     * Đánh dấu tất cả thông báo đã đọc.
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả đã đọc.',
        ]);
    }
}
