<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get user notifications (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        $notifications = $request->user()
            ->notifications()
            ->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->id)
                    ->whereRaw('"is_read" = false')
                    ->count();

        return response()->json(['unread_count' => $count]);
    }



    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        // Find the unread notification safely for PostgreSQL
        $notification = $request->user()
            ->notifications()
            ->whereRaw('"is_read" = false')
            ->findOrFail($id);

        // Mark it as read with PostgreSQL-safe boolean
        $notification->update([
            'is_read' => DB::raw('true'),
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }


    /**
     * Mark ALL notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->notifications()
            ->whereRaw('"is_read" = false') // PostgreSQL-safe boolean check
            ->update([
                'is_read' => DB::raw('true'), // PostgreSQL-safe boolean set
                'read_at' => now(),
            ]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted',
        ]);
    }
}
