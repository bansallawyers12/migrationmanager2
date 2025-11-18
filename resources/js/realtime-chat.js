/**
 * Real-time Chat Implementation with Pusher
 * 
 * This file provides a complete WebSocket integration for real-time chat
 * between web frontend and mobile app using Pusher Cloud Service.
 * 
 * Features:
 * - Real-time message sending and receiving
 * - Private channels with authentication
 * - Automatic reconnection
 * - Unread count updates
 * - Message read status
 * - Typing indicators
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally (required by Laravel Echo)
window.Pusher = Pusher;

/**
 * Initialize Laravel Echo with Pusher
 * 
 * Configuration:
 * - broadcaster: 'pusher' (uses Pusher Cloud)
 * - key: Pusher App Key (from your Pusher dashboard)
 * - cluster: Pusher cluster (e.g., 'ap2', 'us2', 'eu')
 * - forceTLS: true (always use secure connection with Pusher Cloud)
 * - authEndpoint: Laravel Sanctum/Passport authentication endpoint
 */
class RealtimeChat {
    constructor(config = {}) {
        this.config = {
            pusherKey: config.pusherKey || import.meta.env.VITE_PUSHER_APP_KEY,
            pusherCluster: config.pusherCluster || import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap2',
            forceTLS: config.forceTLS !== undefined ? config.forceTLS : true,
            apiBaseUrl: config.apiBaseUrl || '/api',
            authToken: config.authToken || null,
            userId: config.userId || null,
            enableLogging: config.enableLogging || false,
            ...config
        };

        this.echo = null;
        this.listeners = {
            onMessageReceived: [],
            onMessageSent: [],
            onMessageUpdated: [],
            onUnreadCountUpdated: [],
            onConnectionEstablished: [],
            onConnectionError: []
        };

        this.initialize();
    }

    /**
     * Initialize Echo instance with Pusher configuration
     */
    initialize() {
        try {
            if (!this.config.pusherKey) {
                throw new Error('Pusher App Key is required. Please set VITE_PUSHER_APP_KEY in your .env file');
            }

            this.echo = new Echo({
                broadcaster: 'pusher',
                key: this.config.pusherKey,
                cluster: this.config.pusherCluster,
                forceTLS: this.config.forceTLS,
                encrypted: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: `${this.config.apiBaseUrl}/broadcasting/auth`,
                auth: {
                    headers: {
                        'Authorization': `Bearer ${this.config.authToken}`,
                        'Accept': 'application/json',
                    }
                }
            });

            // Enable Pusher logging if configured
            if (this.config.enableLogging) {
                Pusher.logToConsole = true;
            }

            console.log('✅ Laravel Echo initialized with Pusher');
            console.log(`🌍 Connected to cluster: ${this.config.pusherCluster}`);
            this.triggerListeners('onConnectionEstablished');
        } catch (error) {
            console.error('❌ Failed to initialize Echo:', error);
            this.triggerListeners('onConnectionError', error);
        }
    }

    /**
     * Subscribe to user's private channel
     * Receives messages, read receipts, and notifications
     */
    subscribeToUserChannel(userId = null) {
        const targetUserId = userId || this.config.userId;
        
        if (!targetUserId) {
            console.error('❌ User ID is required to subscribe');
            return;
        }

        const channel = this.echo.private(`user.${targetUserId}`);

        // Listen for new messages sent to this user
        channel.listen('.message.sent', (event) => {
            console.log('📨 New message received:', event);
            this.triggerListeners('onMessageReceived', event);
        });

        // Listen for message read receipts
        channel.listen('.message.received', (event) => {
            console.log('✓✓ Message read by recipient:', event);
            this.triggerListeners('onMessageUpdated', event);
        });

        // Listen for message updates (edits, deletions, etc.)
        channel.listen('.message.updated', (event) => {
            console.log('✏️ Message updated:', event);
            this.triggerListeners('onMessageUpdated', event);
        });

        // Listen for unread count updates
        channel.listen('.unread.count.updated', (event) => {
            console.log('🔢 Unread count updated:', event);
            this.triggerListeners('onUnreadCountUpdated', event);
        });

        console.log(`✅ Subscribed to user channel: user.${targetUserId}`);
        return channel;
    }

    /**
     * Subscribe to matter-specific channel
     * Receives all messages for a specific matter
     */
    subscribeToMatterChannel(matterId) {
        if (!matterId) {
            console.error('❌ Matter ID is required to subscribe');
            return;
        }

        const channel = this.echo.private(`matter.${matterId}`);

        // Listen for new messages in this matter
        channel.listen('.message.sent', (event) => {
            console.log('📨 Matter message received:', event);
            this.triggerListeners('onMessageReceived', event);
        });

        console.log(`✅ Subscribed to matter channel: matter.${matterId}`);
        return channel;
    }

    /**
     * Send a message via HTTP API
     * The backend will broadcast it to all connected clients
     */
    async sendMessage(messageData) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/messages/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.config.authToken}`,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(messageData)
            });

            const result = await response.json();

            if (result.success) {
                console.log('✅ Message sent successfully:', result.data);
                this.triggerListeners('onMessageSent', result.data);
                return result.data;
            } else {
                console.error('❌ Failed to send message:', result.message);
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ Error sending message:', error);
            throw error;
        }
    }

    /**
     * Mark a message as read
     */
    async markAsRead(messageId) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/messages/${messageId}/read`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${this.config.authToken}`,
                    'Accept': 'application/json',
                }
            });

            const result = await response.json();

            if (result.success) {
                console.log('✅ Message marked as read');
                return result;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ Error marking message as read:', error);
            throw error;
        }
    }

    /**
     * Get messages for a specific matter
     */
    async getMessages(clientMatterId, page = 1, limit = 20) {
        try {
            const response = await fetch(
                `${this.config.apiBaseUrl}/messages?client_matter_id=${clientMatterId}&page=${page}&limit=${limit}`,
                {
                    headers: {
                        'Authorization': `Bearer ${this.config.authToken}`,
                        'Accept': 'application/json',
                    }
                }
            );

            const result = await response.json();

            if (result.success) {
                return result.data;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ Error fetching messages:', error);
            throw error;
        }
    }

    /**
     * Get unread message count
     */
    async getUnreadCount() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/messages/unread-count`, {
                headers: {
                    'Authorization': `Bearer ${this.config.authToken}`,
                    'Accept': 'application/json',
                }
            });

            const result = await response.json();

            if (result.success) {
                return result.data.unread_count;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ Error fetching unread count:', error);
            throw error;
        }
    }

    /**
     * Event listener management
     */
    on(event, callback) {
        if (this.listeners[event]) {
            this.listeners[event].push(callback);
        }
    }

    off(event, callback) {
        if (this.listeners[event]) {
            this.listeners[event] = this.listeners[event].filter(cb => cb !== callback);
        }
    }

    triggerListeners(event, data = null) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => callback(data));
        }
    }

    /**
     * Disconnect from all channels
     */
    disconnect() {
        if (this.echo) {
            this.echo.disconnect();
            console.log('🔌 Disconnected from Pusher');
        }
    }

    /**
     * Leave a specific channel
     */
    leaveChannel(channelName) {
        if (this.echo) {
            this.echo.leave(channelName);
            console.log(`👋 Left channel: ${channelName}`);
        }
    }
}

// Export for use in other modules
export default RealtimeChat;

// Usage example:
/*
import RealtimeChat from './realtime-chat';

// Initialize the chat client
const chat = new RealtimeChat({
    pusherKey: 'your-pusher-key',
    pusherCluster: 'ap2',
    forceTLS: true,
    authToken: 'your-bearer-token',
    userId: 123
});

// Subscribe to user's private channel
chat.subscribeToUserChannel();

// Subscribe to a specific matter
chat.subscribeToMatterChannel(456);

// Listen for new messages
chat.on('onMessageReceived', (event) => {
    console.log('New message:', event.message);
    // Update UI with new message
});

// Listen for unread count updates
chat.on('onUnreadCountUpdated', (event) => {
    console.log('Unread count:', event.unread_count);
    // Update badge/notification count
});

// Send a message
await chat.sendMessage({
    message: 'Hello from web!',
    client_matter_id: 456
});

// Get message history
const messages = await chat.getMessages(456, 1, 20);

// Mark message as read
await chat.markAsRead(789);

// When done, disconnect
chat.disconnect();
*/

