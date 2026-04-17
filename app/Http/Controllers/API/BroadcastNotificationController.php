<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\BroadcastNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BroadcastNotificationController extends Controller
{
    public function __construct(
        protected BroadcastNotificationService $broadcasts
    ) {
    }

    /**
     * Create a new broadcast notification batch.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'title' => ['nullable', 'string', 'max:255'],
            'scope' => ['required', Rule::in(['all', 'specific', 'team'])],
            'recipient_ids' => ['required_if:scope,specific', 'array'],
            'recipient_ids.*' => ['integer'],
        ]);

        try {
            $result = $this->broadcasts->createBroadcast([
                'sender' => $request->user(),
                'message' => $validated['message'],
                'title' => $validated['title'] ?? null,
                'scope' => $validated['scope'],
                'recipient_ids' => $validated['recipient_ids'] ?? [],
            ]);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'status' => 'sent',
            'data' => $result,
        ], Response::HTTP_CREATED);
    }

    /**
     * List the sender's broadcast history.
     */
    public function index(Request $request): JsonResponse
    {
        $history = $this->broadcasts->getBroadcastHistory((int) $request->user()->id);

        return response()->json([
            'data' => $history,
        ]);
    }

    /**
     * Show per-recipient details for a specific broadcast batch.
     */
    public function show(Request $request, string $batchUuid): JsonResponse
    {
        try {
            $details = $this->broadcasts->getBroadcastDetails($batchUuid, (int) $request->user()->id);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $details,
        ]);
    }

    /**
     * Mark a broadcast notification as read for the authenticated user.
     */
    public function markAsRead(Request $request, int $notificationId): JsonResponse
    {
        $result = $this->broadcasts->markAsRead($notificationId, (int) $request->user()->id);

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
        $result = $this->broadcasts->startReadTimer($notificationId, (int) $request->user()->id);

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
        $detail = $this->broadcasts->getReceiverBroadcastDetail($notificationId, (int) $request->user()->id);

        if (!$detail) {
            return response()->json([
                'message' => 'Broadcast notification not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $detail,
        ]);
    }

    /**
     * Return unread broadcasts for the authenticated user (polling fallback).
     */
    public function unread(Request $request): JsonResponse
    {
        $unread = $this->broadcasts->getUnreadBroadcasts((int) $request->user()->id);

        return response()->json([
            'data' => $unread,
        ]);
    }
}


