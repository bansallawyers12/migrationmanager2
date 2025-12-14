<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Events\MessageSent;

trait SendMessageToClientTrait
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
                DB::table('message_recipients')->insert([
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

                // Broadcast to the client only
                broadcast(new MessageSent($messageForBroadcast, $clientId));

                // Also broadcast to sender (so they see their own message)
                broadcast(new MessageSent($messageForBroadcast, $senderId));

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

                // Create activity log
                DB::table('activities_logs')->insert([
                    'client_id' => $senderId,
                    'created_by' => $senderId,
                    'subject' => 'Message sent to client',
                    'description' => 'Message sent from web page to client ' . $recipientUser->full_name . ' for matter ID: ' . $clientMatterId,
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
}

