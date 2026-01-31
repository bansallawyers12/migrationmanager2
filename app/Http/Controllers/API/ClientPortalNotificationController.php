<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientPortalNotificationController extends Controller
{
    /**
     * List all notifications for the authenticated client.
     * Optionally filter by client_matter_id (matter).
     *
     * GET /api/notifications
     * Query: client_matter_id (optional), page, limit
     */
    public function index(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $validator = Validator::make($request->all(), [
                'client_matter_id' => 'nullable|integer|min:1',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $clientMatterId = $request->get('client_matter_id');

            $query = DB::table('notifications')
                ->leftJoin('admins as sender', 'notifications.sender_id', '=', 'sender.id')
                ->where('notifications.receiver_id', $clientId)
                ->select(
                    'notifications.id',
                    'notifications.sender_id',
                    'notifications.module_id as client_matter_id',
                    'notifications.url',
                    'notifications.notification_type',
                    'notifications.message',
                    'notifications.receiver_status as is_read',
                    'notifications.seen',
                    'notifications.created_at',
                    'notifications.updated_at',
                    DB::raw("(COALESCE(sender.first_name, '') || ' ' || COALESCE(sender.last_name, '')) as sender_name")
                );

            if ($clientMatterId) {
                $query->where('notifications.module_id', $clientMatterId);
            }

            $total = $query->count();

            $notifications = $query->orderBy('notifications.created_at', 'desc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                    'pagination' => [
                        'current_page' => (int) $page,
                        'per_page' => (int) $limit,
                        'total' => $total,
                        'last_page' => (int) ceil($total / $limit),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Notifications List Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single notification by id (only if it belongs to the client).
     *
     * GET /api/notifications/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $notification = DB::table('notifications')
                ->leftJoin('admins as sender', 'notifications.sender_id', '=', 'sender.id')
                ->where('notifications.id', $id)
                ->where('notifications.receiver_id', $clientId)
                ->select(
                    'notifications.id',
                    'notifications.sender_id',
                    'notifications.module_id as client_matter_id',
                    'notifications.url',
                    'notifications.notification_type',
                    'notifications.message',
                    'notifications.receiver_status as is_read',
                    'notifications.seen',
                    'notifications.created_at',
                    'notifications.updated_at',
                    DB::raw("(COALESCE(sender.first_name, '') || ' ' || COALESCE(sender.last_name, '')) as sender_name")
                )
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $notification,
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Notification Show Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'notification_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a notification as read.
     *
     * POST /api/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $updated = DB::table('notifications')
                ->where('id', $id)
                ->where('receiver_id', $clientId)
                ->update([
                    'receiver_status' => 1,
                    'seen' => 1,
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Notification MarkAsRead Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'notification_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
