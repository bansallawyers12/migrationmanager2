<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ClientPortalNotificationController extends Controller
{
    /**
     * Notification types that are considered client-portal related (web or mobile).
     * Only these types are listed/visible in the Client Portal Notification API.
     */
    private const CLIENT_PORTAL_NOTIFICATION_TYPES = ['message', 'stage_change', 'matter_discontinued', 'matter_reopened', 'checklist', 'checklist_added', 'document_approved', 'document_rejected', 'document_deleted', 'document_downloaded', 'detail_approved', 'detail_rejected', 'invoice_sent_to_client_app', 'action_completed', 'lead_converted_to_client'];

    /**
     * Get unread notifications count for authenticated user (not matter specific).
     *
     * GET /api/notifications/unread-count (auth:sanctum)
     */
    public function unreadCount(Request $request)
    {
        try {
            $user = $request->user();

            $unreadCount = DB::table('notifications')
                ->where('receiver_id', $user->id)
                ->whereIn('notification_type', self::CLIENT_PORTAL_NOTIFICATION_TYPES)
                ->where('receiver_status', 0)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Notification UnreadCount Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unread notification count',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Five most recent unread notifications for the authenticated user (client-portal types only).
     *
     * GET /api/notifications/recent-unread (auth:sanctum)
     */
    public function recentUnread(Request $request)
    {
        try {
            $clientId = $request->user()->id;

            $notifications = DB::table('notifications')
                ->leftJoin('staff as sender_staff', 'notifications.sender_id', '=', 'sender_staff.id')
                ->leftJoin('admins as sender_admin', 'notifications.sender_id', '=', 'sender_admin.id')
                ->where('notifications.receiver_id', $clientId)
                ->whereIn('notifications.notification_type', self::CLIENT_PORTAL_NOTIFICATION_TYPES)
                ->where('notifications.receiver_status', 0)
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
                    DB::raw("(COALESCE(sender_staff.first_name, sender_admin.first_name, '') || ' ' || COALESCE(sender_staff.last_name, sender_admin.last_name, '')) as sender_name")
                )
                ->orderBy('notifications.created_at', 'desc')
                ->limit(1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Notification RecentUnread Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent unread notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all notifications for the authenticated client (client-portal related only).
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
                ->leftJoin('staff as sender_staff', 'notifications.sender_id', '=', 'sender_staff.id')
                ->leftJoin('admins as sender_admin', 'notifications.sender_id', '=', 'sender_admin.id')
                ->where('notifications.receiver_id', $clientId)
                ->whereIn('notifications.notification_type', self::CLIENT_PORTAL_NOTIFICATION_TYPES)
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
                    DB::raw("(COALESCE(sender_staff.first_name, sender_admin.first_name, '') || ' ' || COALESCE(sender_staff.last_name, sender_admin.last_name, '')) as sender_name")
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
                ->leftJoin('staff as sender_staff', 'notifications.sender_id', '=', 'sender_staff.id')
                ->leftJoin('admins as sender_admin', 'notifications.sender_id', '=', 'sender_admin.id')
                ->where('notifications.id', $id)
                ->where('notifications.receiver_id', $clientId)
                ->whereIn('notifications.notification_type', self::CLIENT_PORTAL_NOTIFICATION_TYPES)
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
                    DB::raw("(COALESCE(sender_staff.first_name, sender_admin.first_name, '') || ' ' || COALESCE(sender_staff.last_name, sender_admin.last_name, '')) as sender_name")
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
                ->whereIn('notification_type', self::CLIENT_PORTAL_NOTIFICATION_TYPES)
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

    /**
     * List unread action-required rows (seen = 0) for the authenticated client across all matters (paginated).
     *
     * GET /api/action-required
     * Query: page (default 1), limit (default 20, max 100)
     */
    public function actionRequiredIndex(Request $request)
    {
        try {
            if (! Schema::hasTable('cp_action_requires')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'action_required' => [],
                        'pagination' => [
                            'current_page' => 1,
                            'per_page' => 20,
                            'total' => 0,
                            'last_page' => 0,
                        ],
                    ],
                ], 200);
            }

            $validator = Validator::make($request->all(), [
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

            $clientId = $request->user()->id;
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 20);

            $query = DB::table('cp_action_requires as car')
                ->leftJoin('staff as sender_staff', 'car.sender_id', '=', 'sender_staff.id')
                ->leftJoin('admins as sender_admin', 'car.sender_id', '=', 'sender_admin.id')
                ->where('car.client_id', $clientId)
                ->where('car.seen', 0);

            $total = (clone $query)->count();

            $select = [
                'car.id',
                'car.type',
                'car.client_id',
                'car.client_matter_id',
                'car.sender_id',
                'car.receiver_id',
                'car.module_id',
                'car.url',
                'car.notification_type',
                'car.message',
                'car.sender_status',
                'car.receiver_status',
                'car.seen',
                'car.created_at',
                'car.updated_at',
                DB::raw("(COALESCE(sender_staff.first_name, sender_admin.first_name, '') || ' ' || COALESCE(sender_staff.last_name, sender_admin.last_name, '')) as sender_name"),
            ];
            if (Schema::hasColumn('cp_action_requires', 'checklist_id')) {
                array_splice($select, 4, 0, ['car.checklist_id']);
            }

            $rows = $query
                ->select($select)
                ->orderBy('car.created_at', 'desc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();

            $lastPage = $limit > 0 ? (int) ceil($total / $limit) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'action_required' => $rows,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total' => $total,
                        'last_page' => $lastPage,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Action Required List Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch action required list',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unread action-required count (seen = 0) and the latest single unread row.
     *
     * GET /api/action-required/unread
     */
    public function actionRequiredUnread(Request $request)
    {
        try {
            if (! Schema::hasTable('cp_action_requires')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'unread_count' => 0,
                        'latest_unread' => null,
                    ],
                ], 200);
            }

            $clientId = $request->user()->id;

            $unreadCount = DB::table('cp_action_requires')
                ->where('client_id', $clientId)
                ->where('seen', 0)
                ->count();

            $select = [
                'car.id',
                'car.type',
                'car.client_id',
                'car.client_matter_id',
                'car.sender_id',
                'car.receiver_id',
                'car.module_id',
                'car.url',
                'car.notification_type',
                'car.message',
                'car.sender_status',
                'car.receiver_status',
                'car.seen',
                'car.created_at',
                'car.updated_at',
                DB::raw("(COALESCE(sender_staff.first_name, sender_admin.first_name, '') || ' ' || COALESCE(sender_staff.last_name, sender_admin.last_name, '')) as sender_name"),
            ];
            if (Schema::hasColumn('cp_action_requires', 'checklist_id')) {
                array_splice($select, 4, 0, ['car.checklist_id']);
            }

            $latestUnread = DB::table('cp_action_requires as car')
                ->leftJoin('staff as sender_staff', 'car.sender_id', '=', 'sender_staff.id')
                ->leftJoin('admins as sender_admin', 'car.sender_id', '=', 'sender_admin.id')
                ->where('car.client_id', $clientId)
                ->where('car.seen', 0)
                ->select($select)
                ->orderBy('car.created_at', 'desc')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount,
                    'latest_unread' => $latestUnread,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('ClientPortal Action Required Unread Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch action required unread summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
