<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Broadcast;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Events\MessageSent;
use App\Events\MessageReceived;
use App\Events\MessageDeleted;
use App\Events\MessageUpdated;
use App\Events\UnreadCountUpdated;
use App\Services\FCMService;

class ClientPortalMessageController extends Controller
{
    /**
     * Send Message to Client Only (Web Page Use)
     * POST /api/messages/send-to-client
     * 
     * Sends a message only to the client associated with the client_matter_id
     * Used by the web page to send messages directly to the client/customer
     */
    public function sendMessageToClient(Request $request)
    {
        try {
            $admin = $request->user();
            $senderId = $admin->id;

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

            // Get client matter info to find the client_id
            $clientMatter = DB::table('client_matters')
                ->where('id', $clientMatterId)
                ->first();

            if (!$clientMatter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client matter not found'
                ], 404);
            }

            // Get the client_id from the matter
            $clientId = $clientMatter->client_id;
            
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No client associated with this matter'
                ], 422);
            }

            // Get sender information
            $sender = null;
            $senderShortname = null;
            if ($senderId) {
                $sender = DB::table('admins')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $senderId)
                    ->first();
                
                // Generate 2-character shortname from sender's name
                if ($sender) {
                    $firstInitial = $sender->first_name ? strtoupper(substr($sender->first_name, 0, 1)) : '';
                    $lastInitial = $sender->last_name ? strtoupper(substr($sender->last_name, 0, 1)) : '';
                    $senderShortname = $firstInitial . $lastInitial;
                }
            }

            // Get client/recipient information
            $recipientUser = DB::table('admins')
                ->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
                ->where('id', $clientId)
                ->first();

            if (!$recipientUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client user not found'
                ], 404);
            }

            // Generate recipient shortname
            $firstInitial = $recipientUser->first_name ? strtoupper(substr($recipientUser->first_name, 0, 1)) : '';
            $lastInitial = $recipientUser->last_name ? strtoupper(substr($recipientUser->last_name, 0, 1)) : '';
            $recipientShortname = $firstInitial . $lastInitial;

            // Create message record
            $messageData = [
                'message' => $message,
                'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                'sender_id' => $senderId,
                'sent_at' => now(),
                'client_matter_id' => $clientMatterId,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $messageId = DB::table('messages')->insertGetId($messageData);

            if ($messageId) {
                // Insert single recipient into pivot table
                MessageRecipient::insert([
                    'message_id' => $messageId,
                    'recipient_id' => $clientId,
                    'recipient' => $recipientUser->full_name,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Build recipient info for broadcast
                $recipientIdsWithDetails = [
                    [
                        'recipient_id' => $clientId,
                        'recipient' => $recipientUser->full_name,
                        'recipient_shortname' => $recipientShortname
                    ]
                ];

                // Prepare message for broadcasting
                $messageForBroadcast = [
                    'id' => $messageId,
                    'message' => $message,
                    'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                    'sender_id' => $senderId,
                    'sender_shortname' => $senderShortname,
                    'recipient_ids' => $recipientIdsWithDetails,
                    'sent_at' => now()->toISOString(),
                    'client_matter_id' => $clientMatterId,
                    'recipient_count' => 1
                ];

                // Broadcast to the client only (with error handling)
                try {
                    broadcast(new MessageSent($messageForBroadcast, $clientId));
                } catch (\Exception $e) {
                    Log::warning('Failed to broadcast message to client', [
                        'client_id' => $clientId,
                        'message_id' => $messageId,
                        'error' => $e->getMessage(),
                        'broadcast_driver' => config('broadcasting.default'),
                        'hint' => 'Check BROADCAST_DRIVER in .env and ensure Reverb server is running: php artisan reverb:start'
                    ]);
                }

                // Also broadcast to sender (so they see their own message)
                try {
                    broadcast(new MessageSent($messageForBroadcast, $senderId));
                } catch (\Exception $e) {
                    Log::warning('Failed to broadcast message to sender', [
                        'sender_id' => $senderId,
                        'message_id' => $messageId,
                        'error' => $e->getMessage()
                    ]);
                }

                // Send notification to client
                $notificationMessage = 'New message from ' . ($sender ? $sender->first_name . ' ' . $sender->last_name : 'Agent') . ' for matter ' . ($clientMatter->client_unique_matter_no ?? 'ID: ' . $clientMatterId);

                DB::table('notifications')->insert([
                    'sender_id' => $senderId,
                    'receiver_id' => $clientId,
                    'module_id' => $clientMatterId,
                    'url' => '/messages',
                    'notification_type' => 'message',
                    'message' => $notificationMessage,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'sender_status' => 1,
                    'receiver_status' => 0,
                    'seen' => 0
                ]);

                // Send push notification to client
                try {
                    $fcmService = new FCMService();
                    $senderName = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Agent';
                    $matterNo = $clientMatter ? ($clientMatter->client_unique_matter_no ?? 'ID: ' . $clientMatterId) : 'ID: ' . $clientMatterId;
                    
                    // Prepare notification title and body
                    $notificationTitle = 'New Message';
                    $notificationBody = $message ? (strlen($message) > 100 ? substr($message, 0, 100) . '...' : $message) : 'You have a new message';
                    
                    // Prepare notification data payload
                    $notificationData = [
                        'type' => 'chat',
                        'userId' => (string)$senderId,
                        'messageId' => (string)$messageId,
                        'clientMatterId' => (string)$clientMatterId,
                        'senderName' => $senderName,
                        'matterNo' => $matterNo
                    ];
                    
                    // Send push notification to client
                    try {
                        $fcmService->sendToUser($clientId, $notificationTitle, $notificationBody, $notificationData);
                    } catch (\Exception $e) {
                        // Log error but don't fail the message sending
                        Log::warning('Failed to send push notification to client', [
                            'client_id' => $clientId,
                            'message_id' => $messageId,
                            'error' => $e->getMessage()
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the message sending
                    Log::error('Failed to send push notification', [
                        'message_id' => $messageId,
                        'client_id' => $clientId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                // Create activity log
                DB::table('activities_logs')->insert([
                    'client_id' => $senderId,
                    'created_by' => $senderId,
                    'subject' => 'Message sent to client',
                    'description' => 'Message sent from web page to client ' . $recipientUser->full_name . ' for matter ID: ' . $clientMatterId,
                    'task_status' => 0, // Required NOT NULL field (0 = activity, 1 = task)
                    'pin' => 0, // Required NOT NULL field (0 = not pinned, 1 = pinned)
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully to client',
                    'data' => [
                        'message_id' => $messageId,
                        'message' => $messageForBroadcast,
                        'sent_at' => now()->toISOString(),
                        'recipient' => [
                            'id' => $clientId,
                            'name' => $recipientUser->full_name
                        ]
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Send Message to Client API Error: ' . $e->getMessage(), [
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
            $senderShortname = null;
            if ($clientId) {
                $sender = DB::table('admins')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $clientId)
                    ->first();
                
                // Generate 2-character shortname from sender's name
                if ($sender) {
                    $firstInitial = $sender->first_name ? strtoupper(substr($sender->first_name, 0, 1)) : '';
                    $lastInitial = $sender->last_name ? strtoupper(substr($sender->last_name, 0, 1)) : '';
                    $senderShortname = $firstInitial . $lastInitial;
                }
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
                // Get recipient details for all target recipients
                $recipientUsers = DB::table('admins')
                    ->whereIn('id', $targetRecipients)
                    ->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
                    ->get()
                    ->keyBy('id');

                // Build recipient_ids array with detailed info
                $recipientIdsWithDetails = [];
                foreach ($targetRecipients as $recipientId) {
                    $recipientUser = $recipientUsers->get($recipientId);
                    if ($recipientUser) {
                        $firstInitial = $recipientUser->first_name ? strtoupper(substr($recipientUser->first_name, 0, 1)) : '';
                        $lastInitial = $recipientUser->last_name ? strtoupper(substr($recipientUser->last_name, 0, 1)) : '';
                        $recipientShortname = $firstInitial . $lastInitial;
                        
                        $recipientIdsWithDetails[] = [
                            'recipient_id' => $recipientId,
                            'recipient' => $recipientUser->full_name,
                            'recipient_shortname' => $recipientShortname
                        ];
                    }
                }

                // Insert recipients into pivot table
                $recipientRecords = [];
                foreach ($targetRecipients as $recipientId) {
                    $recipientUser = $recipientUsers->get($recipientId);
                    $recipientRecords[] = [
                        'message_id' => $messageId,
                        'recipient_id' => $recipientId,
                        'recipient' => $recipientUser ? $recipientUser->full_name : null, // Store recipient name
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                MessageRecipient::insert($recipientRecords);

                // Prepare message for broadcasting
                $messageForBroadcast = [
                    'id' => $messageId,
                    'message' => $message,
                    'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                    'sender_id' => $clientId,
                    'sender_shortname' => $senderShortname,
                    'recipient_ids' => $recipientIdsWithDetails,
                    'sent_at' => now()->toISOString(),
                    'client_matter_id' => $clientMatterId,
                    'recipient_count' => count($targetRecipients)
                ];

                // Broadcast to each recipient (with error handling)
                try {
                    foreach ($targetRecipients as $recipientId) {
                        try {
                            broadcast(new MessageSent($messageForBroadcast, $recipientId));
                        } catch (\Exception $e) {
                            // Log error but continue with other recipients
                            Log::warning('Failed to broadcast message to recipient', [
                                'recipient_id' => $recipientId,
                                'message_id' => $messageId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    // Also broadcast to sender (so they see their own message)
                    try {
                        broadcast(new MessageSent($messageForBroadcast, $clientId));
                    } catch (\Exception $e) {
                        Log::warning('Failed to broadcast message to sender', [
                            'sender_id' => $clientId,
                            'message_id' => $messageId,
                            'error' => $e->getMessage()
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the message sending
                    Log::error('Broadcast error (message still saved)', [
                        'message_id' => $messageId,
                        'error' => $e->getMessage(),
                        'broadcast_driver' => config('broadcasting.default'),
                        'hint' => 'Check BROADCAST_DRIVER in .env and ensure Reverb server is running: php artisan reverb:start'
                    ]);
                }

                // Send notifications to recipients (excluding sender)
                foreach ($targetRecipients as $recipientId) {
                    if ($recipientId != $clientId) {
                        $notificationMessage = 'New message received by Client Portal Mobile App from ' . ($sender ? $sender->first_name . ' ' . $sender->last_name : 'Client') . ' for matter ' . ($clientMatter ? $clientMatter->client_unique_matter_no : 'ID: ' . $clientMatterId);

                        DB::table('notifications')->insert([
                            'sender_id' => $clientId,
                            'receiver_id' => $recipientId,
                            'module_id' => $clientMatterId,
                            'url' => '/messages',
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

                // Send push notifications to recipients (excluding sender)
                try {
                    $fcmService = new FCMService();
                    $senderName = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Client';
                    $matterNo = $clientMatter ? $clientMatter->client_unique_matter_no : 'ID: ' . $clientMatterId;
                    
                    // Prepare notification title and body
                    $notificationTitle = 'New Message';
                    $notificationBody = $message ? (strlen($message) > 100 ? substr($message, 0, 100) . '...' : $message) : 'You have a new message';
                    
                    // Prepare notification data payload
                    $notificationData = [
                        'type' => 'chat',
                        'userId' => (string)$clientId,
                        'messageId' => (string)$messageId,
                        'clientMatterId' => (string)$clientMatterId,
                        'senderName' => $senderName,
                        'matterNo' => $matterNo
                    ];
                    
                    // Send push notification to each recipient
                    foreach ($targetRecipients as $recipientId) {
                        if ($recipientId != $clientId) {
                            try {
                                $fcmService->sendToUser($recipientId, $notificationTitle, $notificationBody, $notificationData);
                            } catch (\Exception $e) {
                                // Log error but don't fail the message sending
                                Log::warning('Failed to send push notification to recipient', [
                                    'recipient_id' => $recipientId,
                                    'message_id' => $messageId,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the message sending
                    Log::error('Failed to send push notifications', [
                        'message_id' => $messageId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                // Create activity log
                DB::table('activities_logs')->insert([
                    'client_id' => $clientId,
                    'created_by' => $clientId,
                    'subject' => 'Message sent',
                    'description' => 'Message sent by Client Portal Mobile App for matter ID: ' . $clientMatterId . ' to ' . count($targetRecipients) . ' recipient(s)',
                    'task_status' => 0, // Required NOT NULL field (0 = activity, 1 = task)
                    'pin' => 0, // Required NOT NULL field (0 = not pinned, 1 = pinned)
                    'source' => 'client_portal',
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
                    $recipients = MessageRecipient::where('message_id', $msg->id)
                        ->get();

                    // Get current user's read status if they're a recipient
                    $currentUserRecipient = $recipients->firstWhere('recipient_id', $clientId);
                    
                    // Get recipient details with names
                    $recipientIds = $recipients->pluck('recipient_id')->toArray();
                    $recipientIdsWithDetails = [];
                    if (!empty($recipientIds)) {
                        $recipientUsers = DB::table('admins')
                            ->whereIn('id', $recipientIds)
                            ->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
                            ->get()
                            ->keyBy('id');
                        
                        foreach ($recipientIds as $recipientId) {
                            $recipientUser = $recipientUsers->get($recipientId);
                            if ($recipientUser) {
                                $firstInitial = $recipientUser->first_name ? strtoupper(substr($recipientUser->first_name, 0, 1)) : '';
                                $lastInitial = $recipientUser->last_name ? strtoupper(substr($recipientUser->last_name, 0, 1)) : '';
                                $recipientShortname = $firstInitial . $lastInitial;
                                
                                $recipientIdsWithDetails[] = [
                                    'recipient_id' => $recipientId,
                                    'recipient' => $recipientUser->full_name,
                                    'recipient_shortname' => $recipientShortname
                                ];
                            }
                        }
                    }

                    // Get sender's shortname
                    $senderShortname = null;
                    if ($msg->sender_id) {
                        $senderInfo = DB::table('admins')
                            ->where('id', $msg->sender_id)
                            ->select('first_name', 'last_name')
                            ->first();
                        
                        if ($senderInfo) {
                            $firstInitial = $senderInfo->first_name ? strtoupper(substr($senderInfo->first_name, 0, 1)) : '';
                            $lastInitial = $senderInfo->last_name ? strtoupper(substr($senderInfo->last_name, 0, 1)) : '';
                            $senderShortname = $firstInitial . $lastInitial;
                        }
                    }

                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender' => $msg->sender,
                        'sender_id' => $msg->sender_id,
                        'sender_shortname' => $senderShortname,
                        'is_sender' => $msg->sender_id == $clientId,
                        'is_recipient' => $currentUserRecipient !== null,
                        'recipient_ids' => $recipientIdsWithDetails,
                        'recipient_count' => count($recipientIdsWithDetails),
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
            $isRecipient = MessageRecipient::where('message_id', $id)
                ->where('recipient_id', $clientId)
                ->exists();

            $isSender = $message->sender_id == $clientId;

            if (!$isSender && !$isRecipient) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this message'
                ], 403);
            }

            // Get all recipients with their read status and names
            $recipients = MessageRecipient::join('admins', 'message_recipients.recipient_id', '=', 'admins.id')
                ->where('message_id', $id)
                ->select(
                    'message_recipients.recipient_id',
                    'admins.first_name',
                    'admins.last_name',
                    DB::raw("(COALESCE(admins.first_name, '') || ' ' || COALESCE(admins.last_name, '')) as recipient_name"),
                    'message_recipients.is_read',
                    'message_recipients.read_at'
                )
                ->get();

            // Get current user's read status if they're a recipient
            $currentUserRecipient = $recipients->firstWhere('recipient_id', $clientId);

            // Get sender's shortname
            $senderShortname = null;
            if ($message->sender_id) {
                $senderInfo = DB::table('admins')
                    ->where('id', $message->sender_id)
                    ->select('first_name', 'last_name')
                    ->first();
                
                if ($senderInfo) {
                    $firstInitial = $senderInfo->first_name ? strtoupper(substr($senderInfo->first_name, 0, 1)) : '';
                    $lastInitial = $senderInfo->last_name ? strtoupper(substr($senderInfo->last_name, 0, 1)) : '';
                    $senderShortname = $firstInitial . $lastInitial;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender' => $message->sender,
                    'sender_id' => $message->sender_id,
                    'sender_shortname' => $senderShortname,
                    'is_sender' => $isSender,
                    'is_recipient' => $isRecipient,
                    'recipients' => $recipients->map(function ($recipient) {
                        $firstInitial = $recipient->first_name ? strtoupper(substr($recipient->first_name, 0, 1)) : '';
                        $lastInitial = $recipient->last_name ? strtoupper(substr($recipient->last_name, 0, 1)) : '';
                        $recipientShortname = $firstInitial . $lastInitial;
                        
                        return [
                            'recipient_id' => $recipient->recipient_id,
                            'recipient_name' => $recipient->recipient_name,
                            'recipient_shortname' => $recipientShortname,
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
            $recipientRecord = MessageRecipient::where('message_id', $id)
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
                MessageRecipient::where('message_id', $id)
                    ->where('recipient_id', $clientId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);

                // Get updated recipient record
                $updatedRecipient = MessageRecipient::where('message_id', $id)
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

                // Broadcast message update to sender (so they know it was read) - with error handling
                try {
                    broadcast(new MessageUpdated($messageForBroadcast, $message->sender_id));
                } catch (\Exception $e) {
                    Log::warning('Failed to broadcast message update to sender', [
                        'sender_id' => $message->sender_id,
                        'message_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Broadcast read status to sender - with error handling
                try {
                    broadcast(new MessageReceived($id, $message->sender_id));
                } catch (\Exception $e) {
                    Log::warning('Failed to broadcast read status to sender', [
                        'sender_id' => $message->sender_id,
                        'message_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Broadcast unread count update for current user - with error handling
                try {
                    $unreadCount = MessageRecipient::where('recipient_id', $clientId)
                        ->where('is_read', false)
                        ->count();
                    broadcast(new UnreadCountUpdated($clientId, $unreadCount));
                } catch (\Exception $e) {
                    Log::warning('Failed to broadcast unread count update', [
                        'user_id' => $clientId,
                        'error' => $e->getMessage()
                    ]);
                }
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

            // Validate request
            $validator = Validator::make($request->all(), [
                'client_matter_id' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clientMatterId = $request->input('client_matter_id');

            // Get messages where current user is the sender for specific client matter
            $sentMessages = DB::table('messages')
                ->where('sender_id', $clientId)
                ->where('client_matter_id', $clientMatterId)
                ->pluck('id')
                ->toArray();

            if (empty($sentMessages)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'unread_count' => 0
                    ]
                ], 200);
            }

            // Count messages where at least one recipient has read the message
            $readMessagesCount = MessageRecipient::whereIn('message_id', $sentMessages)
                ->where('is_read', true)
                ->distinct('message_id')
                ->count('message_id');

            // Calculate unread count: Total sent messages - Read messages count
            $totalSentMessages = count($sentMessages);
            $unreadCount = $totalSentMessages - $readMessagesCount;

            // Log for debugging
            Log::info('Get Unread Count API', [
                'client_id' => $clientId,
                'client_name' => $admin->first_name . ' ' . $admin->last_name,
                'client_matter_id' => $clientMatterId,
                'total_sent_messages' => $totalSentMessages,
                'read_messages_count' => $readMessagesCount,
                'unread_count' => $unreadCount
            ]);

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
