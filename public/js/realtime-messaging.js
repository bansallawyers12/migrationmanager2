/**
 * Real-time Messaging System for Website
 * Handles WebSocket connections and message broadcasting
 */

class RealtimeMessaging {
    constructor() {
        this.pusher = null;
        this.channel = null;
        this.userId = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000; // 1 second
        
        this.init();
    }

    init() {
        // Get user ID from meta tag or global variable
        this.userId = this.getUserId();
        
        if (!this.userId) {
            console.error('User ID not found. Cannot initialize messaging.');
            return;
        }

        this.initializePusher();
        this.setupEventListeners();
        this.loadInitialMessages();
    }

    getUserId() {
        // Try to get from meta tag
        const metaUserId = document.querySelector('meta[name="user-id"]');
        if (metaUserId) {
            return metaUserId.getAttribute('content');
        }

        // Try to get from global variable
        if (typeof window.userId !== 'undefined') {
            return window.userId;
        }

        // Try to get from Laravel session (if available)
        if (typeof window.Laravel !== 'undefined' && window.Laravel.user) {
            return window.Laravel.user.id;
        }

        return null;
    }

    initializePusher() {
        try {
            // Initialize Pusher with your credentials
            const pusherKey = window.PUSHER_CONFIG ? window.PUSHER_CONFIG.key : '0410ad08e960563173b5';
            const pusherCluster = window.PUSHER_CONFIG ? window.PUSHER_CONFIG.cluster : 'ap2';
            
            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true
            });

            // Subscribe to public channel for testing (avoiding private channel auth issues)
            this.channel = this.pusher.subscribe('public-messages');
            console.log('Subscribed to public channel: public-messages');

            // Bind to message events
            this.channel.bind('message.sent', (data) => {
                console.log('WebSocket: message.sent event received:', data);
                this.handleNewMessage(data);
            });

            this.channel.bind('message.received', (data) => {
                console.log('WebSocket: message.received event received:', data);
                this.handleMessageReceived(data);
            });
            
            // Bind to unread count updates
            this.channel.bind('unread.count.updated', (data) => {
                console.log('WebSocket: unread.count.updated event received:', data);
                this.updateUnreadBadge(data.unread_count);
            });
            
            // Bind to message deletion events
            this.channel.bind('message.deleted', (data) => {
                console.log('WebSocket: message.deleted event received:', data);
                this.handleMessageDeleted(data);
            });
            
            // Bind to message update events
            this.channel.bind('message.updated', (data) => {
                console.log('WebSocket: message.updated event received:', data);
                this.handleMessageUpdated(data);
            });
            
            // Bind to unread count update events
            this.channel.bind('unread.count.updated', (data) => {
                console.log('WebSocket: unread.count.updated event received:', data);
                this.handleUnreadCountUpdated(data);
            });
            
            // Test channel connection
            this.channel.bind('pusher:subscription_succeeded', () => {
                console.log('WebSocket: Successfully subscribed to public-messages channel');
            });
            
            this.channel.bind('pusher:subscription_error', (error) => {
                console.error('WebSocket: Subscription error:', error);
            });

            // Connection events
            this.pusher.connection.bind('connected', () => {
                console.log('Connected to messaging service');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.showConnectionStatus('connected');
            });

            this.pusher.connection.bind('disconnected', () => {
                console.log('Disconnected from messaging service');
                this.isConnected = false;
                this.showConnectionStatus('disconnected');
                this.attemptReconnect();
            });

            this.pusher.connection.bind('error', (error) => {
                console.error('Pusher connection error:', error);
                this.showConnectionStatus('error');
            });

        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
            this.showConnectionStatus('error');
        }
    }

    getAuthToken() {
        // Get authentication token from localStorage or cookie
        return localStorage.getItem('auth_token') || 
               this.getCookie('auth_token') || 
               this.getMetaContent('auth-token');
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               this.getCookie('XSRF-TOKEN');
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    getMetaContent(name) {
        const meta = document.querySelector(`meta[name="${name}"]`);
        return meta ? meta.getAttribute('content') : null;
    }

    setupEventListeners() {
        // Listen for page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });

        // Listen for window focus/blur
        window.addEventListener('focus', () => {
            this.resumeUpdates();
        });

        window.addEventListener('blur', () => {
            this.pauseUpdates();
        });
    }

    handleNewMessage(data) {
        console.log('New message received:', data);
        
        const message = data.message;
        
        // Update UI with new message
        this.addMessageToUI(message);
        
        // Show notification
        this.showNotification(message);
        
        // Update unread count
        this.updateUnreadCount();
        
        // Play notification sound
        this.playNotificationSound();
    }

    handleMessageReceived(data) {
        console.log('Message read confirmation:', data);
        
        // Update message status in UI
        this.updateMessageStatus(data.message_id, 'read');
    }

    handleMessageDeleted(data) {
        console.log('Message deleted event:', data);
        
        // Remove message from UI
        this.removeMessageFromUI(data.message_id);
        
        // Update unread count
        this.updateUnreadCount();
        
        // Show notification
        this.showNotification({
            subject: 'Message Deleted',
            message: 'A message has been deleted',
            sender: 'System'
        });
    }

    removeMessageFromUI(messageId) {
        // Remove message from messages list if visible
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            messageElement.remove();
            console.log(`Removed message ${messageId} from UI`);
        }
        
        // Also check in the messages list container
        const messagesContainer = document.getElementById('messages-list');
        if (messagesContainer) {
            const messageDiv = messagesContainer.querySelector(`[onclick*="${messageId}"]`);
            if (messageDiv) {
                messageDiv.remove();
                console.log(`Removed message ${messageId} from messages list`);
            }
        }
    }

    handleMessageUpdated(data) {
        console.log('Message updated event:', data);
        
        // Update message in UI
        this.updateMessageInUI(data.message);
        
        // Update unread count
        this.updateUnreadCount();
        
        // Show notification
        this.showNotification({
            subject: 'Message Updated',
            message: 'A message has been updated',
            sender: 'System'
        });
    }

    handleUnreadCountUpdated(data) {
        console.log('Unread count updated event:', data);
        
        // Only update if this is for the current user
        if (data.user_id == this.userId) {
            this.updateUnreadBadge(data.unread_count);
            console.log(`✓ Real-time unread count updated to: ${data.unread_count}`);
        }
    }

    updateMessageInUI(message) {
        // Find message element in UI and update it
        const messageElement = document.querySelector(`[onclick*="${message.id}"]`);
        if (messageElement) {
            // Update the message styling based on read status
            if (message.is_read) {
                messageElement.classList.remove('unread');
                messageElement.classList.add('read');
                
                // Remove "Mark as Read" button
                const markReadBtn = messageElement.querySelector('.btn-success');
                if (markReadBtn) {
                    markReadBtn.remove();
                }
            }
            
            console.log(`Updated message ${message.id} in UI`);
        }
        
        // Also update in messages list if visible
        const messagesContainer = document.getElementById('messages-list');
        if (messagesContainer) {
            const messageDiv = messagesContainer.querySelector(`[onclick*="${message.id}"]`);
            if (messageDiv) {
                // Update styling
                if (message.is_read) {
                    messageDiv.classList.remove('unread');
                    messageDiv.classList.add('read');
                    messageDiv.style.background = '#f9f9f9';
                }
                
                console.log(`Updated message ${message.id} in messages list`);
            }
        }
    }

    addMessageToUI(message) {
        // Find or create messages container
        let messagesContainer = document.getElementById('messages-list');
        if (!messagesContainer) {
            messagesContainer = this.createMessagesContainer();
        }

        // Create message element
        const messageElement = this.createMessageElement(message);
        
        // Add to container
        messagesContainer.insertBefore(messageElement, messagesContainer.firstChild);
        
        // Scroll to top to show new message
        messagesContainer.scrollTop = 0;
        
        // Animate new message
        messageElement.classList.add('new-message');
        setTimeout(() => {
            messageElement.classList.remove('new-message');
        }, 3000);
    }

    createMessagesContainer() {
        const container = document.createElement('div');
        container.id = 'messages-list';
        container.className = 'messages-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            width: 350px;
            max-height: 500px;
            overflow-y: auto;
            z-index: 9999;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        document.body.appendChild(container);
        return container;
    }

    createMessageElement(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-item';
        messageDiv.style.cssText = `
            padding: 12px;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
            transition: all 0.3s ease;
        `;
        
        const isUrgent = message.message_type === 'urgent';
        const isImportant = message.message_type === 'important';
        
        if (isUrgent) {
            messageDiv.style.background = '#fff3cd';
            messageDiv.style.borderLeft = '4px solid #ffc107';
        } else if (isImportant) {
            messageDiv.style.background = '#d1ecf1';
            messageDiv.style.borderLeft = '4px solid #17a2b8';
        }

        messageDiv.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <strong style="color: #333;">${message.sender}</strong>
                <span style="font-size: 12px; color: #666;">${this.formatTime(message.sent_at)}</span>
            </div>
            <div style="margin-bottom: 8px;">
                <strong style="color: #555;">${message.subject}</strong>
            </div>
            <div style="color: #666; line-height: 1.4;">
                ${this.truncateText(message.message, 100)}
            </div>
            <div style="margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 11px; color: #999;">Type: ${message.message_type}</span>
                <button onclick="realtimeMessaging.markAsRead(${message.id})" 
                        style="background: #007bff; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;">
                    Mark as Read
                </button>
            </div>
        `;

        // Add click handler to view full message
        messageDiv.addEventListener('click', () => {
            this.showMessageModal(message);
        });

        return messageDiv;
    }

    showMessageModal(message) {
        // Create modal for full message view
        const modal = document.createElement('div');
        modal.className = 'message-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        `;

        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        `;

        modalContent.innerHTML = `
            <button onclick="this.closest('.message-modal').remove()" 
                    style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
            <h3 style="margin-top: 0;">${message.subject}</h3>
            <p><strong>From:</strong> ${message.sender}</p>
            <p><strong>To:</strong> ${message.recipient}</p>
            <p><strong>Time:</strong> ${this.formatDateTime(message.sent_at)}</p>
            <p><strong>Type:</strong> ${message.message_type}</p>
            <hr>
            <div style="white-space: pre-wrap; line-height: 1.6;">${message.message}</div>
            ${message.attachments && message.attachments.length > 0 ? `
                <hr>
                <h4>Attachments:</h4>
                <ul>
                    ${message.attachments.map(att => `<li><a href="${att.path}" target="_blank">${att.name}</a></li>`).join('')}
                </ul>
            ` : ''}
        `;

        modal.appendChild(modalContent);
        document.body.appendChild(modal);

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    showNotification(message) {
        // Check if browser supports notifications
        if ('Notification' in window) {
            if (Notification.permission === 'granted') {
                const notification = new Notification(`New Message from ${message.sender}`, {
                    body: message.subject,
                    icon: '/favicon.ico',
                    tag: 'message-' + message.id
                });

                notification.onclick = () => {
                    window.focus();
                    this.showMessageModal(message);
                    notification.close();
                };

                // Auto close after 5 seconds
                setTimeout(() => {
                    notification.close();
                }, 5000);
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        this.showNotification(message);
                    }
                });
            }
        }

        // Show browser notification fallback
        this.showBrowserNotification(message);
    }

    showBrowserNotification(message) {
        // Create a temporary notification element
        const notification = document.createElement('div');
        notification.className = 'browser-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            background: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            max-width: 300px;
            animation: slideIn 0.3s ease;
        `;

        notification.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 5px;">New Message</div>
            <div style="font-size: 14px;">From: ${message.sender}</div>
            <div style="font-size: 12px; opacity: 0.9;">${message.subject}</div>
        `;

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(-100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);

        // Click to view message
        notification.addEventListener('click', () => {
            this.showMessageModal(message);
            notification.remove();
        });
    }

    updateUnreadCount() {
        // Load unread count from API
        fetch('/api/messages/unread-count', {
            headers: {
                'Authorization': 'Bearer ' + this.getAuthToken(),
                'X-CSRF-TOKEN': this.getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateUnreadBadge(data.data.unread_count);
                console.log('Loaded real unread count from API:', data.data.unread_count);
            } else {
                console.error('Failed to load unread count:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading unread count:', error);
        });
    }

    updateUnreadBadge(count) {
        console.log('Updating unread badge to:', count);
        
        // Update the top-left indicator
        const indicator = document.getElementById('unread-indicator');
        const countSpan = document.getElementById('unread-count');
        const chatBadge = document.getElementById('unread-badge');
        
        if (indicator && countSpan) {
            countSpan.textContent = count;
            if (count > 0) {
                indicator.style.display = 'block';
            } else {
                indicator.style.display = 'none';
            }
        }
        
        // Also update the chat button badge
        if (chatBadge) {
            chatBadge.textContent = count;
            if (count > 0) {
                chatBadge.style.display = 'inline-block';
            } else {
                chatBadge.style.display = 'none';
            }
        }
        
        console.log('Unread badge updated successfully');
    }

    showMessagesList() {
        // Create messages list modal
        const modal = document.createElement('div');
        modal.className = 'messages-list-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        `;

        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        `;

        modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Messages</h2>
                <button onclick="this.closest('.messages-list-modal').remove()" 
                        style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div id="messages-list-content">Loading messages...</div>
        `;

        modal.appendChild(modalContent);
        document.body.appendChild(modal);

        // Load messages
        this.loadMessagesList();
    }

    loadMessagesList() {
        // Load messages from API
        fetch('/api/messages?client_matter_id=9&limit=50&type=all&message_type=all', {
            headers: {
                'Authorization': 'Bearer ' + this.getAuthToken(),
                'X-CSRF-TOKEN': this.getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.displayMessagesList(data.data.messages);
                console.log('Loaded real messages list from API');
            } else {
                console.error('Failed to load messages list:', data.message);
                this.displayMessagesList([]);
            }
        })
        .catch(error => {
            console.error('Error loading messages list:', error);
            this.displayMessagesList([]);
        });
    }

    displayMessagesList(messages) {
        const content = document.getElementById('messages-list-content');
        if (!content) return;

        if (messages.length === 0) {
            content.innerHTML = '<p>No messages found.</p>';
            return;
        }

        const messagesHtml = messages.map(message => `
            <div class="message-list-item" style="
                padding: 15px;
                border: 1px solid #eee;
                border-radius: 8px;
                margin-bottom: 10px;
                cursor: pointer;
                background: ${message.read ? '#f9f9f9' : '#fff3cd'};
            " onclick="realtimeMessaging.showMessageModal(${JSON.stringify(message).replace(/"/g, '&quot;')})">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong>${message.sender}</strong>
                    <span style="font-size: 12px; color: #666;">${this.formatTime(message.sent_at)}</span>
                </div>
                <div style="margin: 5px 0; font-weight: bold;">${message.subject}</div>
                <div style="color: #666; font-size: 14px;">${this.truncateText(message.message, 150)}</div>
                <div style="margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: #999;">Type: ${message.message_type}</span>
                    ${!message.read ? '<span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px;">UNREAD</span>' : ''}
                </div>
            </div>
        `).join('');

        content.innerHTML = messagesHtml;
    }

    markAsRead(messageId) {
        // Mark message as read via API
        fetch(`/api/messages/${messageId}/read`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + this.getAuthToken(),
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateMessageStatus(messageId, 'read');
                this.updateUnreadCount();
                console.log(`Marked message ${messageId} as read via API`);
            } else {
                console.error('Failed to mark message as read:', data.message);
            }
        })
        .catch(error => {
            console.error('Error marking message as read:', error);
        });
    }

    updateMessageStatus(messageId, status) {
        // Update message status in UI
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            if (status === 'read') {
                messageElement.style.background = '#f9f9f9';
                messageElement.querySelector('.unread-badge')?.remove();
            }
        }
    }

    playNotificationSound() {
        // Play notification sound
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(error => {
            console.log('Could not play notification sound:', error);
        });
    }

    showConnectionStatus(status) {
        // Show connection status indicator
        let statusIndicator = document.getElementById('connection-status');
        if (!statusIndicator) {
            statusIndicator = document.createElement('div');
            statusIndicator.id = 'connection-status';
            statusIndicator.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 9997;
            `;
            document.body.appendChild(statusIndicator);
        }

        switch (status) {
            case 'connected':
                statusIndicator.textContent = '🟢 Connected';
                statusIndicator.style.background = '#d4edda';
                statusIndicator.style.color = '#155724';
                break;
            case 'disconnected':
                statusIndicator.textContent = '🔴 Disconnected';
                statusIndicator.style.background = '#f8d7da';
                statusIndicator.style.color = '#721c24';
                break;
            case 'error':
                statusIndicator.textContent = '⚠️ Connection Error';
                statusIndicator.style.background = '#fff3cd';
                statusIndicator.style.color = '#856404';
                break;
            case 'demo':
                statusIndicator.textContent = '🟡 Demo Mode';
                statusIndicator.style.background = '#fff3cd';
                statusIndicator.style.color = '#856404';
                break;
        }
    }

    attemptReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                this.initializePusher();
            }, this.reconnectDelay * this.reconnectAttempts);
        } else {
            console.log('Max reconnection attempts reached. Connection failed.');
            this.showConnectionStatus('error');
        }
    }


    pauseUpdates() {
        // Pause real-time updates when page is not visible
        console.log('Pausing real-time updates');
    }

    resumeUpdates() {
        // Resume real-time updates when page becomes visible
        console.log('Resuming real-time updates');
        if (!this.isConnected) {
            this.initializePusher();
        }
    }

    loadInitialMessages() {
        // Load initial messages from API
        fetch('/api/messages?client_matter_id=9&limit=10', {
            headers: {
                'Authorization': 'Bearer ' + this.getAuthToken(),
                'X-CSRF-TOKEN': this.getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateUnreadCount();
                console.log('Loaded initial messages from API');
            } else {
                console.error('Failed to load initial messages:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading initial messages:', error);
        });
        
        // WebSocket approach only - no polling
    }
    

    // Utility functions
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) { // Less than 1 minute
            return 'Just now';
        } else if (diff < 3600000) { // Less than 1 hour
            return Math.floor(diff / 60000) + 'm ago';
        } else if (diff < 86400000) { // Less than 1 day
            return Math.floor(diff / 3600000) + 'h ago';
        } else {
            return date.toLocaleDateString();
        }
    }

    formatDateTime(timestamp) {
        return new Date(timestamp).toLocaleString();
    }

    truncateText(text, maxLength) {
        if (text.length <= maxLength) {
            return text;
        }
        return text.substring(0, maxLength) + '...';
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.realtimeMessaging = new RealtimeMessaging();
});

// Initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.realtimeMessaging = new RealtimeMessaging();
    });
} else {
    window.realtimeMessaging = new RealtimeMessaging();
}
