<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Services\BroadcastNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BroadcastNotificationAjaxController extends Controller
{
    public function __construct(
        protected BroadcastNotificationService $broadcasts
    ) {
        $this->middleware('auth:admin');
    }

    protected function sender(Request $request)
    {
        return $request->user('admin');
    }

    public function store(Request $request): JsonResponse
    {
        \Log::info('📢 Broadcast send request received', [
            'user_id' => $this->sender($request)?->id,
            'payload' => $request->except(['_token'])
        ]);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'title' => ['nullable', 'string', 'max:255'],
            'scope' => ['required', Rule::in(['all', 'specific', 'team'])],
            'recipient_ids' => ['required_if:scope,specific', 'array'],
            'recipient_ids.*' => ['integer'],
        ]);

        \Log::info('✅ Broadcast validation passed', ['validated' => $validated]);

        try {
            $result = $this->broadcasts->createBroadcast([
                'sender' => $this->sender($request),
                'message' => $validated['message'],
                'title' => $validated['title'] ?? null,
                'scope' => $validated['scope'],
                'recipient_ids' => $validated['recipient_ids'] ?? [],
            ]);
            
            \Log::info('✅ Broadcast created successfully', [
                'batch_uuid' => $result['batch_uuid'],
                'recipient_count' => $result['recipient_count']
            ]);
        } catch (\InvalidArgumentException $exception) {
            \Log::error('❌ Broadcast creation failed', [
                'error' => $exception->getMessage()
            ]);
            
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'status' => 'sent',
            'data' => $result,
        ], Response::HTTP_CREATED);
    }

    public function history(Request $request): JsonResponse
    {
        // CHANGED: Now returns ALL broadcasts globally, not just sender's broadcasts
        $history = $this->broadcasts->getAllBroadcastHistory();
        $currentUserId = (int) $this->sender($request)->id;
        $isSuperAdmin = $this->sender($request)->role == 1;

        return response()->json([
            'data' => $history,
            'current_staff_id' => $currentUserId,
            'current_user_id' => $currentUserId, // deprecated, use current_staff_id
            'is_super_admin' => $isSuperAdmin,
        ]);
    }

    public function myHistory(Request $request): JsonResponse
    {
        // Get only broadcasts sent by the current user
        $senderId = (int) $this->sender($request)->id;
        $history = $this->broadcasts->getBroadcastHistory($senderId);

        return response()->json([
            'data' => $history,
        ]);
    }

    public function readHistory(Request $request): JsonResponse
    {
        // Get broadcasts that the user has already read
        $receiverId = (int) $this->sender($request)->id;
        $readBroadcasts = $this->broadcasts->getReadBroadcasts($receiverId);

        return response()->json([
            'data' => $readBroadcasts,
        ]);
    }

    public function details(Request $request, string $batchUuid): JsonResponse
    {
        try {
            $details = $this->broadcasts->getBroadcastDetails($batchUuid, (int) $this->sender($request)->id);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $details,
        ]);
    }

    public function unread(Request $request): JsonResponse
    {
        $unread = $this->broadcasts->getUnreadBroadcasts((int) $this->sender($request)->id);

        return response()->json([
            'data' => $unread,
        ]);
    }

    public function markAsRead(Request $request, int $notificationId): JsonResponse
    {
        $result = $this->broadcasts->markAsRead($notificationId, (int) $this->sender($request)->id);

        if ($result['status'] === 'delay_not_elapsed') {
            return response()->json([
                'message' => 'Please read the full broadcast message before marking it as read.',
                'remaining_seconds' => (int) ($result['remaining_seconds'] ?? 0),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!in_array($result['status'], ['ok', 'already_read'], true)) {
            return response()->json([
                'message' => 'Notification not found or already read.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function startReadTimer(Request $request, int $notificationId): JsonResponse
    {
        $result = $this->broadcasts->startReadTimer($notificationId, (int) $this->sender($request)->id);

        if ($result['status'] === 'not_found') {
            return response()->json([
                'message' => 'Broadcast notification not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => $result['status'],
            'remaining_seconds' => (int) ($result['remaining_seconds'] ?? 0),
        ]);
    }

    public function receiverDetail(Request $request, int $notificationId): JsonResponse
    {
        $detail = $this->broadcasts->getReceiverBroadcastDetail($notificationId, (int) $this->sender($request)->id);

        if (!$detail) {
            return response()->json([
                'message' => 'Broadcast notification not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $detail,
        ]);
    }

    public function delete(Request $request, string $batchUuid): JsonResponse
    {
        $sender = $this->sender($request);
        
        // Check if user is super admin (role == 1)
        if ($sender->role != 1) {
            \Log::warning('❌ Non-super admin attempted to delete broadcast', [
                'user_id' => $sender->id,
                'role' => $sender->role,
                'batch_uuid' => $batchUuid
            ]);
            
            return response()->json([
                'message' => 'Only super administrators can delete broadcasts.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Validate UUID format
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $batchUuid)) {
            return response()->json([
                'message' => 'Invalid broadcast ID format.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $deleted = $this->broadcasts->deleteBroadcast($batchUuid, (int) $sender->id);
            
            if (!$deleted) {
                return response()->json([
                    'message' => 'Broadcast not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            \Log::info('✅ Broadcast deleted successfully', [
                'batch_uuid' => $batchUuid,
                'deleted_by' => $sender->id,
                'deleted_by_name' => trim("{$sender->first_name} {$sender->last_name}")
            ]);

            return response()->json([
                'status' => 'deleted',
                'message' => 'Broadcast deleted successfully.',
            ]);
        } catch (\Exception $exception) {
            \Log::error('❌ Error deleting broadcast', [
                'batch_uuid' => $batchUuid,
                'error' => $exception->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Failed to delete broadcast. Please try again.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}


