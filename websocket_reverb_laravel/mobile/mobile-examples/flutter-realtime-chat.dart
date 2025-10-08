/**
 * Flutter Real-time Chat Implementation with Laravel Reverb
 * 
 * Installation (add to pubspec.yaml):
 * dependencies:
 *   pusher_client: ^2.0.0
 *   http: ^1.1.0
 * 
 * This implementation connects to Laravel Reverb WebSocket server
 */

import 'dart:convert';
import 'package:pusher_client/pusher_client.dart';
import 'package:http/http.dart' as http;

class RealtimeChatFlutter {
  final String wsHost;
  final int wsPort;
  final String apiBaseUrl;
  final String authToken;
  final int userId;
  final String reverbKey;
  final bool enableLogging;

  PusherClient? pusher;
  Channel? userChannel;
  Map<int, Channel> matterChannels = {};

  // Callbacks
  Function(Map<String, dynamic>)? onMessageReceived;
  Function(Map<String, dynamic>)? onMessageSent;
  Function(Map<String, dynamic>)? onMessageUpdated;
  Function(Map<String, dynamic>)? onUnreadCountUpdated;
  Function()? onConnectionEstablished;
  Function(dynamic)? onConnectionError;

  RealtimeChatFlutter({
    required this.wsHost,
    required this.wsPort,
    required this.apiBaseUrl,
    required this.authToken,
    required this.userId,
    required this.reverbKey,
    this.enableLogging = false,
  });

  /**
   * Initialize Pusher connection
   */
  Future<void> initialize() async {
    try {
      final options = PusherOptions(
        host: wsHost,
        wsPort: wsPort,
        wssPort: wsPort,
        encrypted: false,
        cluster: 'mt1', // required but not used by Reverb
        auth: PusherAuth(
          '$apiBaseUrl/broadcasting/auth',
          headers: {
            'Authorization': 'Bearer $authToken',
            'Accept': 'application/json',
          },
        ),
      );

      pusher = PusherClient(
        reverbKey,
        options,
        enableLogging: enableLogging,
        autoConnect: false,
      );

      // Connection state listeners
      pusher!.onConnectionStateChange((state) {
        print('Connection state: ${state?.currentState}');
        
        if (state?.currentState == 'CONNECTED') {
          print('✅ Connected to Reverb WebSocket');
          if (onConnectionEstablished != null) {
            onConnectionEstablished!();
          }
        }
      });

      pusher!.onConnectionError((error) {
        print('❌ Connection error: ${error?.message}');
        if (onConnectionError != null) {
          onConnectionError!(error);
        }
      });

      // Connect
      await pusher!.connect();
      print('✅ Pusher initialized');
    } catch (error) {
      print('❌ Failed to initialize Pusher: $error');
      rethrow;
    }
  }

  /**
   * Subscribe to user's private channel
   */
  void subscribeToUserChannel({int? targetUserId}) {
    final uid = targetUserId ?? userId;

    try {
      userChannel = pusher!.subscribe('private-user.$uid');

      // Bind to events
      userChannel!.bind('message.sent', (event) {
        final data = jsonDecode(event?.data ?? '{}');
        print('📨 New message received: $data');
        if (onMessageReceived != null) {
          onMessageReceived!(data);
        }
      });

      userChannel!.bind('message.received', (event) {
        final data = jsonDecode(event?.data ?? '{}');
        print('✓✓ Message read: $data');
        if (onMessageUpdated != null) {
          onMessageUpdated!(data);
        }
      });

      userChannel!.bind('message.updated', (event) {
        final data = jsonDecode(event?.data ?? '{}');
        print('✏️ Message updated: $data');
        if (onMessageUpdated != null) {
          onMessageUpdated!(data);
        }
      });

      userChannel!.bind('unread.count.updated', (event) {
        final data = jsonDecode(event?.data ?? '{}');
        print('🔢 Unread count: $data');
        if (onUnreadCountUpdated != null) {
          onUnreadCountUpdated!(data);
        }
      });

      print('✅ Subscribed to user.$uid');
    } catch (error) {
      print('❌ Subscription error: $error');
    }
  }

  /**
   * Subscribe to matter-specific channel
   */
  void subscribeToMatterChannel(int matterId) {
    try {
      final channel = pusher!.subscribe('private-matter.$matterId');

      channel.bind('message.sent', (event) {
        final data = jsonDecode(event?.data ?? '{}');
        print('📨 Matter message: $data');
        if (onMessageReceived != null) {
          onMessageReceived!(data);
        }
      });

      matterChannels[matterId] = channel;
      print('✅ Subscribed to matter.$matterId');
    } catch (error) {
      print('❌ Matter subscription error: $error');
    }
  }

  /**
   * Send a message
   */
  Future<Map<String, dynamic>?> sendMessage(Map<String, dynamic> messageData) async {
    try {
      final response = await http.post(
        Uri.parse('$apiBaseUrl/messages/send'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $authToken',
          'Accept': 'application/json',
        },
        body: jsonEncode(messageData),
      );

      final result = jsonDecode(response.body);

      if (result['success'] == true) {
        print('✅ Message sent: ${result['data']}');
        if (onMessageSent != null) {
          onMessageSent!(result['data']);
        }
        return result['data'];
      } else {
        throw Exception(result['message']);
      }
    } catch (error) {
      print('❌ Send error: $error');
      rethrow;
    }
  }

  /**
   * Mark message as read
   */
  Future<Map<String, dynamic>> markAsRead(int messageId) async {
    try {
      final response = await http.put(
        Uri.parse('$apiBaseUrl/messages/$messageId/read'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Accept': 'application/json',
        },
      );

      return jsonDecode(response.body);
    } catch (error) {
      print('❌ Mark as read error: $error');
      rethrow;
    }
  }

  /**
   * Get messages
   */
  Future<Map<String, dynamic>?> getMessages(
    int clientMatterId, {
    int page = 1,
    int limit = 20,
  }) async {
    try {
      final response = await http.get(
        Uri.parse(
          '$apiBaseUrl/messages?client_matter_id=$clientMatterId&page=$page&limit=$limit',
        ),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Accept': 'application/json',
        },
      );

      final result = jsonDecode(response.body);
      return result['success'] == true ? result['data'] : null;
    } catch (error) {
      print('❌ Get messages error: $error');
      rethrow;
    }
  }

  /**
   * Get unread count
   */
  Future<int> getUnreadCount() async {
    try {
      final response = await http.get(
        Uri.parse('$apiBaseUrl/messages/unread-count'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Accept': 'application/json',
        },
      );

      final result = jsonDecode(response.body);
      return result['success'] == true ? result['data']['unread_count'] : 0;
    } catch (error) {
      print('❌ Get unread count error: $error');
      rethrow;
    }
  }

  /**
   * Disconnect
   */
  void disconnect() {
    if (pusher != null) {
      pusher!.disconnect();
      print('🔌 Disconnected');
    }
  }

  /**
   * Unsubscribe from matter channel
   */
  void unsubscribeFromMatter(int matterId) {
    if (matterChannels.containsKey(matterId)) {
      pusher!.unsubscribe('private-matter.$matterId');
      matterChannels.remove(matterId);
      print('👋 Unsubscribed from matter.$matterId');
    }
  }
}

/**
 * Usage Example in Flutter:
 * 
 * import 'package:flutter/material.dart';
 * 
 * class ChatScreen extends StatefulWidget {
 *   final String authToken;
 *   final int userId;
 *   final int matterId;
 * 
 *   ChatScreen({required this.authToken, required this.userId, required this.matterId});
 * 
 *   @override
 *   _ChatScreenState createState() => _ChatScreenState();
 * }
 * 
 * class _ChatScreenState extends State<ChatScreen> {
 *   late RealtimeChatFlutter chat;
 *   List<Map<String, dynamic>> messages = [];
 *   TextEditingController messageController = TextEditingController();
 * 
 *   @override
 *   void initState() {
 *     super.initState();
 *     initializeChat();
 *   }
 * 
 *   Future<void> initializeChat() async {
 *     chat = RealtimeChatFlutter(
 *       wsHost: 'your-server.com',
 *       wsPort: 8080,
 *       apiBaseUrl: 'https://your-server.com/api',
 *       authToken: widget.authToken,
 *       userId: widget.userId,
 *       reverbKey: 'your-reverb-key',
 *       enableLogging: true,
 *     );
 * 
 *     // Set callbacks
 *     chat.onMessageReceived = (data) {
 *       setState(() {
 *         messages.add(data['message']);
 *       });
 *     };
 * 
 *     await chat.initialize();
 *     chat.subscribeToUserChannel();
 *     chat.subscribeToMatterChannel(widget.matterId);
 * 
 *     // Load existing messages
 *     final data = await chat.getMessages(widget.matterId);
 *     if (data != null) {
 *       setState(() {
 *         messages = List<Map<String, dynamic>>.from(data['messages']);
 *       });
 *     }
 *   }
 * 
 *   Future<void> sendMessage() async {
 *     if (messageController.text.trim().isNotEmpty) {
 *       await chat.sendMessage({
 *         'message': messageController.text,
 *         'client_matter_id': widget.matterId,
 *       });
 *       messageController.clear();
 *     }
 *   }
 * 
 *   @override
 *   Widget build(BuildContext context) {
 *     return Scaffold(
 *       appBar: AppBar(title: Text('Chat')),
 *       body: Column(
 *         children: [
 *           Expanded(
 *             child: ListView.builder(
 *               itemCount: messages.length,
 *               itemBuilder: (context, index) {
 *                 final message = messages[index];
 *                 return ListTile(
 *                   title: Text(message['sender'] ?? 'Unknown'),
 *                   subtitle: Text(message['message'] ?? ''),
 *                 );
 *               },
 *             ),
 *           ),
 *           Padding(
 *             padding: EdgeInsets.all(8.0),
 *             child: Row(
 *               children: [
 *                 Expanded(
 *                   child: TextField(
 *                     controller: messageController,
 *                     decoration: InputDecoration(
 *                       hintText: 'Type a message...',
 *                     ),
 *                   ),
 *                 ),
 *                 IconButton(
 *                   icon: Icon(Icons.send),
 *                   onPressed: sendMessage,
 *                 ),
 *               ],
 *             ),
 *           ),
 *         ],
 *       ),
 *     );
 *   }
 * 
 *   @override
 *   void dispose() {
 *     chat.disconnect();
 *     messageController.dispose();
 *     super.dispose();
 *   }
 * }
 */

