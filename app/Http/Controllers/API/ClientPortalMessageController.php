<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Broadcast;
use App\Events\MessageSent;
use App\Events\MessageReceived;
use App\Events\MessageDeleted;
use App\Events\MessageUpdated;
use App\Events\UnreadCountUpdated;

class ClientPortalMessageController extends Controller
{
    /**
     * Send Message
     * POST /api/messages/send
     * 
     * Sends a message and broadcasts it in real-time
     * Supports multiple recipients via recipient_ids array
     */
    public function sendMessage(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate request
            $validator = Validator::make($request->all(), [
                'message' => 'nullable|string|max:5000',
                'client_matter_id' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = $request->input('message');
            $clientMatterId = $request->input('client_matter_id');

            // Get sender information
            $sender = null;
            if ($clientId) {
                $sender = DB::table('admins')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $clientId)
                    ->first();
            }

            // Get client matter info for notifications
            $clientMatter = DB::table('client_matters')
                ->where('id', $clientMatterId)
                ->first();

            // Determine target recipients - always broadcast to all matter users and superadmins
            $targetRecipients = [];
            
            // Get users associated with this matter
            $matterUsers = [];
            if ($clientMatter) {
                $matterUsers = [
                    $clientMatter->sel_migration_agent,
                    $clientMatter->sel_person_responsible,
                    $clientMatter->sel_person_assisting
                ];
                $matterUsers = array_filter($matterUsers, function($userId) {
                    return $userId !== null;
                });
            }

            // Get superadmin users
            $superadmins = DB::table('admins')
                ->where('role', 1)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            // Combine all target users (excluding sender)
            $targetRecipients = array_unique(array_merge($matterUsers, $superadmins));
            $targetRecipients = array_filter($targetRecipients, function($userId) use ($clientId) {
                return $userId != $clientId;
            });
            $targetRecipients = array_values($targetRecipients); // Re-index array

            // Validate we have recipients
            if (empty($targetRecipients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid recipients found for this message'
                ], 422);
            }

            // Create message record (without recipient info)
            $messageData = [
                'message' => $message,
                'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                'sender_id' => $clientId,
                'sent_at' => now(),
                'client_matter_id' => $clientMatterId,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $messageId = DB::table('messages')->insertGetId($messageData);

            if ($messageId) {
                // Get recipient names for all target recipients
                $recipientUsers = DB::table('admins')
                    ->whereIn('id', $targetRecipients)
                    ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
                    ->pluck('full_name', 'id')
                    ->toArray();

                // Insert recipients into pivot table
                $recipientRecords = [];
                foreach ($targetRecipients as $recipientId) {
                    $recipientRecords[] = [
                        'message_id' => $messageId,
                        'recipient_id' => $recipientId,
                        'recipient' => $recipientUsers[$recipientId] ?? null, // Store recipient name
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('message_recipients')->insert($recipientRecords);

                // Prepare message for broadcasting
                $messageForBroadcast = [
                    'id' => $messageId,
                    'message' => $message,
                    'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                    'sender_id' => $clientId,
                    'recipient_ids' => $targetRecipients, // Include all recipient IDs
                    'sent_at' => now()->toISOString(),
                    'client_matter_id' => $clientMatterId,
                    'recipient_count' => count($targetRecipients)
                ];

                // Broadcast to each recipient
                foreach ($targetRecipients as $recipientId) {
                    broadcast(new MessageSent($messageForBroadcast, $recipientId));
                }

                // Also broadcast to sender (so they see their own message)
                broadcast(new MessageSent($messageForBroadcast, $clientId));

                // Send notifications to recipients (excluding sender)
                foreach ($targetRecipients as $recipientId) {
                    if ($recipientId != $clientId) {
                        $notificationMessage = 'New message received by Client Portal Mobile App from ' . ($sender ? $sender->first_name . ' ' . $sender->last_name : 'Client') . ' for matter ' . ($clientMatter ? $clientMatter->client_unique_matter_no : 'ID: ' . $clientMatterId);

                        DB::table('notifications')->insert([
                            'sender_id' => $clientId,
                            'receiver_id' => $recipientId,
                            'module_id' => $clientMatterId,
                            'url' => '/admin/messages',
                            'notification_type' => 'message',
                            'message' => $notificationMessage,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'sender_status' => 1,
                            'receiver_status' => 0,
                            'seen' => 0
                        ]);
                    }
                }

                // Create activity log
                DB::table('activities_logs')->insert([
                    'client_id' => $clientId,
                    'created_by' => $clientId,
                    'subject' => 'Message sent',
                    'description' => 'Message sent by Client Portal Mobile App for matter ID: ' . $clientMatterId . ' to ' . count($targetRecipients) . ' recipient(s)',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => [
                        'message_id' => $messageId,
                        'message' => $messageForBroadcast,
                        'sent_at' => now()->toISOString(),
                        'recipient_count' => count($targetRecipients)
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Send Message API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $request->input('client_matter_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Messages
     * GET /api/messages
     * 
     * Retrieves messages for the authenticated user with recipients info
     */
    public function getMessages(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate required parameters
            $validator = Validator::make($request->all(), [
                'client_matter_id' => 'required|integer|min:1',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get query parameters
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $clientMatterId = $request->get('client_matter_id');

            // Build query to get messages where user is sender OR recipient
            $query = DB::table('messages')
                ->leftJoin('message_recipients', 'messages.id', '=', 'message_recipients.message_id')
                ->where('client_matter_id', $clientMatterId)
                ->where(function ($q) use ($clientId) {
                    $q->where('messages.sender_id', $clientId)
                      ->orWhere('message_recipients.recipient_id', $clientId);
                })
                ->select('messages.*')
                ->distinct();

            // Get total count
            $totalMessages = $query->count();

            // Get messages with pagination
            $messageIds = $query->orderBy('messages.created_at', 'asc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->pluck('messages.id');

            // Get full message details with recipients
            $messages = DB::table('messages')
                ->whereIn('messages.id', $messageIds)
                ->orderBy('messages.created_at', 'asc')
                ->get()
                ->map(function ($msg) use ($clientId) {
                    // Get all recipients for this message
                    $recipients = DB::table('message_recipients')
                        ->where('message_id', $msg->id)
                        ->get();

                    // Get current user's read status if they're a recipient
                    $currentUserRecipient = $recipients->firstWhere('recipient_id', $clientId);
                    
                    // Get recipient details
                    $recipientIds = $recipients->pluck('recipient_id')->toArray();
                    $recipientNames = [];
                    if (!empty($recipientIds)) {
                        $recipientNames = DB::table('admins')
                            ->whereIn('id', $recipientIds)
                            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"))
                            ->pluck('name', 'id')
                            ->toArray();
                    }

                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender' => $msg->sender,
                        'sender_id' => $msg->sender_id,
                        'is_sender' => $msg->sender_id == $clientId,
                        'is_recipient' => $currentUserRecipient !== null,
                        'recipient_ids' => $recipientIds,
                        'recipients' => $recipientNames,
                        'recipient_count' => count($recipientIds),
                        'sent_at' => $msg->sent_at,
                        'is_read' => $currentUserRecipient ? (bool)$currentUserRecipient->is_read : null,
                        'read_at' => $currentUserRecipient ? $currentUserRecipient->read_at : null,
                        'client_matter_id' => $msg->client_matter_id,
                        'created_at' => $msg->created_at,
                        'updated_at' => $msg->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total' => $totalMessages,
                        'last_page' => ceil($totalMessages / $limit)
                    ],
                    'filters' => [
                        'client_matter_id' => $clientMatterId
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Messages API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Message Details
     * GET /api/messages/{id}
     * 
     * Retrieves details of a specific message with all recipients
     */
    public function getMessageDetails(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Get message
            $message = DB::table('messages')
                ->where('id', $id)
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            // Check if user has access to this message (sender or recipient)
            $isRecipient = DB::table('message_recipients')
                ->where('message_id', $id)
                ->where('recipient_id', $clientId)
                ->exists();

            $isSender = $message->sender_id == $clientId;

            if (!$isSender && !$isRecipient) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this message'
                ], 403);
            }

            // Get all recipients with their read status
            $recipients = DB::table('message_recipients')
                ->join('admins', 'message_recipients.recipient_id', '=', 'admins.id')
                ->where('message_id', $id)
                ->select(
                    'message_recipients.recipient_id',
                    DB::raw("CONCAT(admins.first_name, ' ', admins.last_name) as recipient_name"),
                    'message_recipients.is_read',
                    'message_recipients.read_at'
                )
                ->get();

            // Get current user's read status if they're a recipient
            $currentUserRecipient = $recipients->firstWhere('recipient_id', $clientId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender' => $message->sender,
                    'sender_id' => $message->sender_id,
                    'is_sender' => $isSender,
                    'is_recipient' => $isRecipient,
                    'recipients' => $recipients->map(function ($recipient) {
                        return [
                            'recipient_id' => $recipient->recipient_id,
                            'recipient_name' => $recipient->recipient_name,
                            'is_read' => (bool)$recipient->is_read,
                            'read_at' => $recipient->read_at
                        ];
                    }),
                    'recipient_count' => $recipients->count(),
                    'sent_at' => $message->sent_at,
                    'is_read' => $currentUserRecipient ? (bool)$currentUserRecipient->is_read : null,
                    'read_at' => $currentUserRecipient ? $currentUserRecipient->read_at : null,
                    'client_matter_id' => $message->client_matter_id,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Message Details API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'message_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch message details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark Message as Read
     * PUT /api/messages/{id}/read
     * 
     * Marks a message as read for the current user (in pivot table)
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // First check if message exists
            $message = DB::table('messages')
                ->where('id', $id)
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            // Check if user is a recipient of this message
            $recipientRecord = DB::table('message_recipients')
                ->where('message_id', $id)
                ->where('recipient_id', $clientId)
                ->first();

            if (!$recipientRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to mark this message as read'
                ], 403);
            }

            // Mark as read if not already read
            if (!$recipientRecord->is_read) {
                DB::table('message_recipients')
                    ->where('message_id', $id)
                    ->where('recipient_id', $clientId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);

                // Get updated recipient record
                $updatedRecipient = DB::table('message_recipients')
                    ->where('message_id', $id)
                    ->where('recipient_id', $clientId)
                    ->first();

                // Prepare message for broadcast
                $messageForBroadcast = [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender' => $message->sender,
                    'sender_id' => $message->sender_id,
                    'recipient_id' => $clientId,
                    'is_read' => true,
                    'read_at' => $updatedRecipient->read_at,
                    'sent_at' => $message->sent_at,
                    'client_matter_id' => $message->client_matter_id
                ];

                // Broadcast message update to sender (so they know it was read)
                broadcast(new MessageUpdated($messageForBroadcast, $message->sender_id));

                // Broadcast read status to sender
                broadcast(new MessageReceived($id, $message->sender_id));
                
                // Broadcast unread count update for current user
                $unreadCount = DB::table('message_recipients')
                    ->where('recipient_id', $clientId)
                    ->where('is_read', false)
                    ->count();
                broadcast(new UnreadCountUpdated($clientId, $unreadCount));
            }

            return response()->json([
                'success' => true,
                'message' => 'Message marked as read'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mark Message as Read API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'message_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark message as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Unread Count
     * GET /api/messages/unread-count
     * 
     * Gets the count of unread messages for current user from pivot table
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Count unread messages from pivot table
            $unreadCount = DB::table('message_recipients')
                ->where('recipient_id', $clientId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Unread Count API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
