<?php

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
|
| These routes are for testing and development purposes only.
| They should only be loaded when APP_DEBUG is true.
|
*/

// Test route for messaging system
Route::get('/test-messaging', function () {
    // Create a test user session for development
    if (!auth()->check()) {
        // Use the Super1 Admin1 account for testing
        $admin = App\Models\Admin::find(1); // Super1 Admin1
        if ($admin) {
            auth()->login($admin);
        }
    }
    return view('messaging.integration');
});

// Test route for broadcasting
Route::get('/test-broadcast', function () {
    // Create a test user session for development
    if (!auth()->check()) {
        $admin = App\Models\Admin::find(1); // Super1 Admin1
        if ($admin) {
            auth()->login($admin);
        }
    }
    
    // Send a test broadcast
    broadcast(new App\Events\MessageSent([
        'id' => 999,
        'subject' => 'Test Broadcast Message',
        'message' => 'This is a test broadcast message to verify real-time functionality.',
        'sender' => 'System',
        'recipient' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
        'message_type' => 'normal',
        'sent_at' => now()->toISOString(),
        'is_read' => false
    ], auth()->id()));
    
    return response()->json([
        'success' => true,
        'message' => 'Test broadcast sent successfully',
        'user_id' => auth()->id()
    ]);
});

// Test route for deleting a message
Route::get('/test-delete-message/{id}', function ($id) {
    // Create a test user session for development
    if (!auth()->check()) {
        $admin = App\Models\Admin::find(1); // Super1 Admin1
        if ($admin) {
            auth()->login($admin);
        }
    }
    
    try {
        // Delete the message
        $deleted = DB::table('messages')
            ->where('id', $id)
            ->where('recipient_id', auth()->id()) // Only allow deleting messages received by the current user
            ->delete();
        
        if ($deleted) {
            // Broadcast the deletion
            broadcast(new App\Events\MessageDeleted($id, auth()->id()));
            
            return response()->json([
                'success' => true,
                'message' => "Message {$id} deleted successfully"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or not authorized to delete'
            ], 404);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting message: ' . $e->getMessage()
        ], 500);
    }
});

// Test route for creating a new message
Route::get('/test-create-message', function () {
    // Create a test user session for development
    if (!auth()->check()) {
        $admin = App\Models\Admin::find(1); // Super1 Admin1
        if ($admin) {
            auth()->login($admin);
        }
    }
    
    try {
        // Create a new test message
        $messageId = DB::table('messages')->insertGetId([
            'subject' => 'Test Message ' . time(),
            'message' => 'This is a test message created at ' . now()->toDateTimeString(),
            'sender' => 'System Test',
            'recipient' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'sender_id' => 36464, // Vipul Kumar
            'recipient_id' => auth()->id(),
            'sent_at' => now(),
            'is_read' => false,
            'message_type' => 'normal',
            'client_matter_id' => 9,
            'client_matter_stage_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Broadcast the new message
        broadcast(new App\Events\MessageSent([
            'id' => $messageId,
            'subject' => 'Test Message ' . time(),
            'message' => 'This is a test message created at ' . now()->toDateTimeString(),
            'sender' => 'System Test',
            'recipient' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'message_type' => 'normal',
            'sent_at' => now()->toISOString(),
            'is_read' => false
        ], auth()->id()));
        
        // Broadcast unread count update
        $unreadCount = DB::table('messages')
            ->where('recipient_id', auth()->id())
            ->where('is_read', false)
            ->count();
        broadcast(new App\Events\UnreadCountUpdated(auth()->id(), $unreadCount));
        
        return response()->json([
            'success' => true,
            'message' => "Test message {$messageId} created successfully",
            'unread_count' => $unreadCount
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating message: ' . $e->getMessage()
        ], 500);
    }
});

// Test route for marking a message as read
Route::get('/test-mark-read/{id}', function ($id) {
    // Create a test user session for development
    if (!auth()->check()) {
        $admin = App\Models\Admin::find(1); // Super1 Admin1
        if ($admin) {
            auth()->login($admin);
        }
    }
    
    try {
        // Get the message
        $message = DB::table('messages')
            ->where('id', $id)
            ->where('recipient_id', auth()->id())
            ->first();
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
        
        if (!$message->is_read) {
            // Mark as read
            DB::table('messages')
                ->where('id', $id)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
            
            // Get updated message
            $updatedMessage = DB::table('messages')->where('id', $id)->first();
            
            // Broadcast the update
            broadcast(new App\Events\MessageUpdated([
                'id' => $updatedMessage->id,
                'subject' => $updatedMessage->subject,
                'message' => $updatedMessage->message,
                'sender' => $updatedMessage->sender,
                'recipient' => $updatedMessage->recipient,
                'is_read' => true,
                'read_at' => $updatedMessage->read_at,
                'message_type' => $updatedMessage->message_type,
                'sent_at' => $updatedMessage->sent_at
            ], auth()->id()));
            
            // Broadcast unread count update
            $unreadCount = DB::table('messages')
                ->where('recipient_id', auth()->id())
                ->where('is_read', false)
                ->count();
            broadcast(new App\Events\UnreadCountUpdated(auth()->id(), $unreadCount));
            
            return response()->json([
                'success' => true,
                'message' => "Message {$id} marked as read",
                'unread_count' => $unreadCount
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Message is already read'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating message: ' . $e->getMessage()
        ], 500);
    }
});

