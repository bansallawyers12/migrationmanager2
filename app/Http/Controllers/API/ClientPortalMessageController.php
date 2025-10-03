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
     */
    public function sendMessage(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate request
            $validator = Validator::make($request->all(), [
                'recipient_id' => 'nullable|integer|min:1',
                'message' => 'nullable|string|max:5000',
                'subject' => 'nullable|string|max:255',
                'message_type' => 'nullable|in:urgent,important,normal,low_priority',
                'client_matter_id' => 'required|integer|min:1',
                'client_matter_stage_id' => 'required|integer|min:1',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:10240' // 10MB max per file
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $recipientId = $request->input('recipient_id');
            $message = $request->input('message');
            $subject = $request->input('subject');
            $messageType = $request->input('message_type', 'normal');
            $clientMatterId = $request->input('client_matter_id');
            $clientMatterStageId = $request->input('client_matter_stage_id');

            // Get sender information (optional)
            $sender = null;
            if ($clientId) {
                $sender = DB::table('admins')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $clientId)
                    ->first();
            }

            // Get recipient information (optional)
            $recipient = null;
            if ($recipientId) {
                $recipient = DB::table('admins')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $recipientId)
                    ->first();
            }

            // Handle file attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = 'message_attachments/' . $clientId . '/' . $fileName;
                    
                    // Store file (you can use S3 or local storage)
                    $file->storeAs('message_attachments/' . $clientId, $fileName);
                    
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $filePath,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            // Create message record
            $messageData = [
                'subject' => $subject,
                'message' => $message,
                'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                'recipient' => $recipient ? $recipient->first_name . ' ' . $recipient->last_name : null,
                'sender_id' => $clientId,
                'recipient_id' => $recipientId,
                'sent_at' => now(),
                'is_read' => false,
                'message_type' => $messageType,
                'client_matter_id' => $clientMatterId,
                'client_matter_stage_id' => $clientMatterStageId,
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
                'metadata' => json_encode([
                    'sender_email' => $sender ? $sender->email : null,
                    'recipient_email' => $recipient ? $recipient->email : null,
                    'sent_from' => 'mobile_app'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ];

            $messageId = DB::table('messages')->insertGetId($messageData);

            if ($messageId) {
                // Prepare message for broadcasting
                $messageForBroadcast = [
                    'id' => $messageId,
                    'subject' => $subject,
                    'message' => $message,
                    'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                    'recipient' => $recipient ? $recipient->first_name . ' ' . $recipient->last_name : null,
                    'sender_id' => $clientId,
                    'recipient_id' => $recipientId,
                    'message_type' => $messageType,
                    'attachments' => $attachments,
                    'sent_at' => now()->toISOString(),
                    'is_read' => false
                ];

                // Broadcast message to recipient (website) if recipient exists
                if ($recipientId) {
                    broadcast(new MessageSent($messageForBroadcast, $recipientId));
                    
                    // Broadcast unread count update for recipient
                    $unreadCount = DB::table('messages')
                        ->where('recipient_id', $recipientId)
                        ->where('is_read', false)
                        ->count();
                    broadcast(new UnreadCountUpdated($recipientId, $unreadCount));
                }

                // Create activity log
                DB::table('activities_logs')->insert([
                    'client_id' => $clientId,
                    'created_by' => $clientId,
                    'subject' => 'Message sent',
                    'description' => 'Message sent' . ($recipient ? ' to ' . $recipient->first_name . ' ' . $recipient->last_name : ''),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => [
                        'message_id' => $messageId,
                        'message' => $messageForBroadcast,
                        'sent_at' => now()->toISOString()
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
                'recipient_id' => $request->input('recipient_id'),
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
     * Retrieves messages for the authenticated user
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
                'limit' => 'nullable|integer|min:1|max:100',
                'type' => 'nullable|in:all,sent,received',
                'message_type' => 'nullable|in:all,urgent,important,normal,low_priority',
                'client_matter_stage_id' => 'nullable|integer|min:1'
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
            $type = $request->get('type', 'all'); // all, sent, received
            $messageType = $request->get('message_type', 'all');
            $clientMatterId = $request->get('client_matter_id');
            $clientMatterStageId = $request->get('client_matter_stage_id');

            // Build query
            $query = DB::table('messages')
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                      ->orWhere('recipient_id', $clientId);
                })
                ->where('client_matter_id', $clientMatterId); // Required filter

            // Filter by type
            if ($type === 'sent') {
                $query->where('sender_id', $clientId);
            } elseif ($type === 'received') {
                $query->where('recipient_id', $clientId);
            }

            // Filter by message type
            if ($messageType !== 'all') {
                $query->where('message_type', $messageType);
            }

            // Filter by client matter stage
            if ($clientMatterStageId) {
                $query->where('client_matter_stage_id', $clientMatterStageId);
            }

            // Get total count
            $totalMessages = $query->count();

            // Get messages with pagination
            $messages = $query->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get()
                ->map(function ($msg) use ($clientId) {
                    $attachments = json_decode($msg->attachments, true) ?? [];
                    $metadata = json_decode($msg->metadata, true) ?? [];
                    
                    return [
                        'id' => $msg->id,
                        'subject' => $msg->subject,
                        'message' => $msg->message,
                        'sender' => $msg->sender,
                        'recipient' => $msg->recipient,
                        'sender_id' => $msg->sender_id,
                        'recipient_id' => $msg->recipient_id,
                        'is_sender' => $msg->sender_id == $clientId,
                        'is_recipient' => $msg->recipient_id == $clientId,
                        'sent_at' => $msg->sent_at,
                        'read_at' => $msg->read_at,
                        'is_read' => $msg->is_read,
                        'message_type' => $msg->message_type,
                        'client_matter_id' => $msg->client_matter_id,
                        'client_matter_stage_id' => $msg->client_matter_stage_id,
                        'attachments' => $attachments,
                        'metadata' => $metadata,
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
                        'type' => $type,
                        'message_type' => $messageType,
                        'client_matter_id' => $clientMatterId,
                        'client_matter_stage_id' => $clientMatterStageId
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
     * Retrieves details of a specific message
     */
    public function getMessageDetails(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $message = DB::table('messages')
                ->where('id', $id)
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                      ->orWhere('recipient_id', $clientId);
                })
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            $attachments = json_decode($message->attachments, true) ?? [];
            $metadata = json_decode($message->metadata, true) ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'sender' => $message->sender,
                    'recipient' => $message->recipient,
                    'sender_id' => $message->sender_id,
                    'recipient_id' => $message->recipient_id,
                    'is_sender' => $message->sender_id == $clientId,
                    'is_recipient' => $message->recipient_id == $clientId,
                    'sent_at' => $message->sent_at,
                    'read_at' => $message->read_at,
                    'is_read' => $message->is_read,
                    'message_type' => $message->message_type,
                    'client_matter_id' => $message->client_matter_id,
                    'client_matter_stage_id' => $message->client_matter_stage_id,
                    'attachments' => $attachments,
                    'metadata' => $metadata,
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
     * Marks a message as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $message = DB::table('messages')
                ->where('id', $id)
                ->where('recipient_id', $clientId)
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found or not authorized'
                ], 404);
            }

            if (!$message->is_read) {
                DB::table('messages')
                    ->where('id', $id)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);

                // Get updated message for broadcast
                $updatedMessage = DB::table('messages')
                    ->where('id', $id)
                    ->first();

                // Broadcast message update
                broadcast(new MessageUpdated([
                    'id' => $updatedMessage->id,
                    'subject' => $updatedMessage->subject,
                    'message' => $updatedMessage->message,
                    'sender' => $updatedMessage->sender,
                    'recipient' => $updatedMessage->recipient,
                    'is_read' => true,
                    'read_at' => $updatedMessage->read_at,
                    'message_type' => $updatedMessage->message_type,
                    'sent_at' => $updatedMessage->sent_at
                ], $clientId));

                // Broadcast read status to sender
                broadcast(new MessageReceived($id, $message->sender_id));
                
                // Broadcast unread count update for current user
                $unreadCount = DB::table('messages')
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
     * Gets the count of unread messages
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $unreadCount = DB::table('messages')
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

    /**
     * Delete Message
     * DELETE /api/messages/{id}
     * 
     * Deletes a message (soft delete by marking as deleted)
     */
    public function deleteMessage(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $message = DB::table('messages')
                ->where('id', $id)
                ->where('sender_id', $clientId) // Only allow deleting messages sent by the user
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found or you can only delete messages sent by yourself'
                ], 404);
            }

            // Hard delete the message (since only sender can delete)
            $deleted = DB::table('messages')
                ->where('id', $id)
                ->where('sender_id', $clientId)
                ->delete();

            if ($deleted) {
                // Broadcast message deletion to update real-time count
                broadcast(new MessageDeleted($id, $message->recipient_id));
                
                // Broadcast unread count update for both sender and recipient
                $senderUnreadCount = DB::table('messages')
                    ->where('recipient_id', $message->sender_id)
                    ->where('is_read', false)
                    ->count();
                broadcast(new UnreadCountUpdated($message->sender_id, $senderUnreadCount));
                
                $recipientUnreadCount = DB::table('messages')
                    ->where('recipient_id', $message->recipient_id)
                    ->where('is_read', false)
                    ->count();
                broadcast(new UnreadCountUpdated($message->recipient_id, $recipientUnreadCount));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Delete Message API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'message_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Available Recipients
     * GET /api/messages/recipients
     * 
     * Gets list of users that can receive messages
     */
    public function getRecipients(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $recipients = DB::table('admins')
                ->select('id', 'first_name', 'last_name', 'email', 'client_id')
                ->where('id', '!=', $clientId)
                ->where('status', 1) // Active users only
                ->orderBy('first_name', 'asc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'client_id' => $user->client_id
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'recipients' => $recipients,
                    'total' => $recipients->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Recipients API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recipients',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
