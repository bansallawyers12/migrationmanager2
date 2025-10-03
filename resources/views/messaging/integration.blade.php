<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() ?? '1' }}">
    <meta name="auth-token" content="{{ auth()->check() ? auth()->user()->createToken('messaging')->plainTextToken : 'test-token' }}">
    <title>Real-time Messaging Integration</title>
    
    <!-- Unread Messages Indicator -->
    <div id="unread-indicator" style="position: fixed; top: 20px; left: 20px; background: #dc3545; color: white; padding: 10px 15px; border-radius: 20px; font-weight: bold; z-index: 9999; cursor: pointer; display: none;" onclick="toggleMessaging()">
        <span id="unread-count">0</span> Unread Messages
    </div>
    
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <!-- Pusher Configuration -->
    <script>
        window.PUSHER_CONFIG = {
            key: '{{ env('PUSHER_APP_KEY', '0410ad08e960563173b5') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER', 'ap2') }}'
        };
    </script>
    
    <!-- Custom CSS -->
    <style>
        .messaging-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 400px;
            height: 500px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .messaging-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .messaging-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .messaging-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }
        
        .message-item {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        
        .message-item.unread {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        
        .message-item.urgent {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .message-sender {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .message-subject {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }
        
        .message-content {
            color: #666;
            line-height: 1.4;
            margin-bottom: 8px;
        }
        
        .message-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #999;
        }
        
        .message-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 8px;
        }
        
        .connection-status {
            position: fixed;
            bottom: 20px;
            left: 20px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 999;
        }
        
        .status-connected {
            background: #d4edda;
            color: #155724;
        }
        
        .status-disconnected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-error {
            background: #fff3cd;
            color: #856404;
        }
        
        .new-message {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .modal {
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
        }
        
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Connection Status Indicator -->
    <div id="connection-status" class="connection-status status-disconnected">
        🔴 Disconnected
    </div>
    
    <!-- Messaging Container -->
    <div id="messaging-container" class="messaging-container" style="display: none;">
        <div class="messaging-header">
            <h3 style="margin: 0;">Messages</h3>
            <button onclick="toggleMessaging()" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer;">&times;</button>
        </div>
        
        <div class="messaging-body" id="messages-list">
            <div style="text-align: center; color: #666; padding: 20px;">
                Loading messages...
            </div>
        </div>
        
        <div class="messaging-footer">
            <button onclick="showSendMessageModal()" class="btn btn-primary" style="width: 100%;">
                Send New Message
            </button>
        </div>
    </div>
    
    <!-- Floating Message Button -->
    <div id="message-button" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        z-index: 999;
        font-size: 24px;
    " onclick="toggleMessaging()">
        💬
        <span id="unread-badge" class="notification-badge" style="display: none;">0</span>
    </div>
    
    <!-- Send Message Modal -->
    <div id="send-message-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeSendMessageModal()">&times;</button>
            <h3>Send New Message</h3>
            <form id="send-message-form">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">To:</label>
                    <select id="recipient-select" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                        <option value="">Select recipient...</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Subject:</label>
                    <input type="text" id="message-subject" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Message Type:</label>
                    <select id="message-type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="normal">Normal</option>
                        <option value="important">Important</option>
                        <option value="urgent">Urgent</option>
                        <option value="low_priority">Low Priority</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Message:</label>
                    <textarea id="message-content" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; height: 100px;" required></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeSendMessageModal()" class="btn" style="background: #6c757d; color: white;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Include the real-time messaging script -->
    <script src="/js/realtime-messaging.js"></script>
    
    <script>
        // Global variables
        let messagingVisible = false;
        let recipients = [];
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== Messaging System Debug ===');
            console.log('User ID:', getUserId());
            console.log('Auth Token:', getAuthToken());
            console.log('CSRF Token:', getCsrfToken());
            
            loadRecipients();
            loadMessages();
            
            // Set up real-time messaging event listeners
            setupRealtimeListeners();
            
            // WebSocket approach only - no polling
        });
        
        // Set up real-time event listeners
        function setupRealtimeListeners() {
            // Listen for all real-time events
            if (window.realtimeMessaging && window.realtimeMessaging.pusher) {
                const channel = window.realtimeMessaging.channel;
                if (channel) {
                    // Message deletion events
                    channel.bind('message.deleted', function(data) {
                        console.log('Real-time: Message deleted event received:', data);
                        updateUnreadBadge();
                        setTimeout(() => {
                            loadMessages();
                        }, 100);
                    });
                    
                    // Message update events
                    channel.bind('message.updated', function(data) {
                        console.log('Real-time: Message updated event received:', data);
                        updateUnreadBadge();
                        setTimeout(() => {
                            loadMessages();
                        }, 100);
                    });
                    
                    // New message events
                    channel.bind('message.sent', function(data) {
                        console.log('Real-time: New message event received:', data);
                        updateUnreadBadge();
                        setTimeout(() => {
                            loadMessages();
                        }, 100);
                    });
                    
                    // Unread count update events
                    channel.bind('unread.count.updated', function(data) {
                        console.log('Real-time: Unread count updated:', data);
                        updateUnreadBadge();
                    });
                }
            }
        }
        
        // Add utility function to get user ID
        function getUserId() {
            const metaUserId = document.querySelector('meta[name="user-id"]');
            return metaUserId ? metaUserId.getAttribute('content') : null;
        }
        
        // Toggle messaging container
        function toggleMessaging() {
            const container = document.getElementById('messaging-container');
            const button = document.getElementById('message-button');
            
            if (messagingVisible) {
                container.style.display = 'none';
                button.innerHTML = '💬<span id="unread-badge" class="notification-badge" style="display: none;">0</span>';
                messagingVisible = false;
            } else {
                container.style.display = 'flex';
                button.innerHTML = '✕';
                messagingVisible = true;
                loadMessages();
            }
        }
        
        // Load recipients for message form
        function loadRecipients() {
            // Use real API to load recipients
            fetch('/api/messages/recipients', {
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    recipients = data.data.recipients;
                    updateRecipientSelect();
                    console.log('Loaded real recipients from API');
                } else {
                    console.error('Failed to load recipients:', data.message);
                    recipients = [];
                    updateRecipientSelect();
                }
            })
            .catch(error => {
                console.error('Error loading recipients:', error);
                recipients = [];
                updateRecipientSelect();
            });
        }
        
        // Update recipient select dropdown
        function updateRecipientSelect() {
            const select = document.getElementById('recipient-select');
            select.innerHTML = '<option value="">Select recipient...</option>';
            
            recipients.forEach(recipient => {
                const option = document.createElement('option');
                option.value = recipient.id;
                option.textContent = recipient.name;
                select.appendChild(option);
            });
        }
        
        // Load messages
        function loadMessages() {
            console.log('Loading messages...');
            const url = '/api/messages?client_matter_id=9&limit=20&type=all&message_type=all';
            console.log('API URL:', url);
            
            // Use real API to load messages with a valid client_matter_id
            fetch(url, {
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);
                if (data.success) {
                    console.log('Messages loaded:', data.data.messages);
                    displayMessages(data.data.messages);
                    updateUnreadBadge();
                    console.log('✓ Loaded real messages from API');
                } else {
                    console.error('✗ Failed to load messages:', data.message);
                    displayMessages([]);
                }
            })
            .catch(error => {
                console.error('✗ Error loading messages:', error);
                displayMessages([]);
            });
        }
        
        // Display messages in the container
        function displayMessages(messages) {
            console.log('Displaying messages:', messages);
            const container = document.getElementById('messages-list');
            
            if (!container) {
                console.error('Messages container not found!');
                return;
            }
            
            if (messages.length === 0) {
                console.log('No messages to display');
                container.innerHTML = '<div style="text-align: center; color: #666; padding: 20px;">No messages found.</div>';
                return;
            }
            
            const messagesHtml = messages.map(message => {
                const isUnread = !message.is_read; // Fixed: should be is_read, not read
                const isUrgent = message.message_type === 'urgent';
                const isImportant = message.message_type === 'important';
                
                console.log('Processing message:', message.id, 'is_read:', message.is_read, 'isUnread:', isUnread);
                
                let messageClass = 'message-item';
                if (isUnread) messageClass += ' unread';
                if (isUrgent) messageClass += ' urgent';
                
                return `
                    <div class="${messageClass}" onclick="showMessageDetails(${message.id})">
                        <div class="message-sender">${message.sender}</div>
                        <div class="message-subject">${message.subject}</div>
                        <div class="message-content">${truncateText(message.message, 100)}</div>
                        <div class="message-meta">
                            <span>${formatTime(message.sent_at)} - ${message.message_type}</span>
                            <div class="message-actions">
                                ${!message.is_read ? `<button onclick="markAsRead(${message.id}, event)" class="btn btn-success">Mark Read</button>` : ''}
                                <button onclick="deleteMessage(${message.id}, event)" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = messagesHtml;
            console.log('✓ Messages displayed in container');
        }
        
        // Show message details modal
        function showMessageDetails(messageId) {
            fetch(`/api/messages/${messageId}`, {
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessageModal(data.data);
                }
            })
            .catch(error => {
                console.error('Failed to load message details:', error);
            });
        }
        
        // Show message modal
        function showMessageModal(message) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'flex';
            
            modal.innerHTML = `
                <div class="modal-content">
                    <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
                    <h3>${message.subject}</h3>
                    <p><strong>From:</strong> ${message.sender}</p>
                    <p><strong>To:</strong> ${message.recipient}</p>
                    <p><strong>Time:</strong> ${formatDateTime(message.sent_at)}</p>
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
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        ${!message.is_read ? `<button onclick="markAsRead(${message.id})" class="btn btn-success">Mark as Read</button>` : ''}
                        <button onclick="deleteMessage(${message.id})" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }
        
        // Show send message modal
        function showSendMessageModal() {
            document.getElementById('send-message-modal').style.display = 'flex';
        }
        
        // Close send message modal
        function closeSendMessageModal() {
            document.getElementById('send-message-modal').style.display = 'none';
            document.getElementById('send-message-form').reset();
        }
        
        // Handle send message form
        document.getElementById('send-message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('recipient_id', document.getElementById('recipient-select').value);
            formData.append('subject', document.getElementById('message-subject').value);
            formData.append('message', document.getElementById('message-content').value);
            formData.append('message_type', document.getElementById('message-type').value);
            
            fetch('/api/messages/send', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    closeSendMessageModal();
                    loadMessages();
                } else {
                    alert('Failed to send message: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Failed to send message:', error);
                alert('Failed to send message. Please try again.');
            });
        });
        
        // Mark message as read
        function markAsRead(messageId, event) {
            if (event) event.stopPropagation();
            
            fetch(`/api/messages/${messageId}/read`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadMessages();
                    updateUnreadBadge();
                }
            })
            .catch(error => {
                console.error('Failed to mark message as read:', error);
            });
        }
        
        // Delete message
        function deleteMessage(messageId, event) {
            if (event) event.stopPropagation();
            
            if (confirm('Are you sure you want to delete this message?')) {
                console.log('Deleting message:', messageId);
                fetch(`/api/messages/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + getAuthToken(),
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Delete response:', data);
                    if (data.success) {
                        console.log('Message deleted successfully, reloading messages and updating count...');
                        loadMessages();
                        updateUnreadBadge();
                    } else {
                        console.error('Failed to delete message:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Failed to delete message:', error);
                });
            }
        }
        
        // Update unread badge
        function updateUnreadBadge() {
            console.log('Updating unread badge...');
            // Use real API to get unread count
            fetch('/api/messages/unread-count', {
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Unread count API response:', data);
                if (data.success) {
                    const badge = document.getElementById('unread-badge');
                    const indicator = document.getElementById('unread-indicator');
                    const countSpan = document.getElementById('unread-count');
                    
                    const unreadCount = data.data.unread_count;
                    console.log('New unread count:', unreadCount);
                    
                    // Update the floating chat button badge
                    if (badge) {
                        if (unreadCount > 0) {
                            badge.textContent = unreadCount;
                            badge.style.display = 'inline';
                        } else {
                            badge.style.display = 'none';
                        }
                        console.log('✓ Updated chat button badge');
                    }
                    
                    // Update the top-left indicator
                    if (indicator && countSpan) {
                        if (unreadCount > 0) {
                            countSpan.textContent = unreadCount;
                            indicator.style.display = 'block';
                        } else {
                            indicator.style.display = 'none';
                        }
                        console.log('✓ Updated top-left indicator');
                    }
                    
                    console.log('✓ Unread count updated successfully:', unreadCount);
                } else {
                    console.error('Failed to load unread count:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
        }
        
        // Utility functions
        function getAuthToken() {
            return document.querySelector('meta[name="auth-token"]').getAttribute('content');
        }
        
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
        
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) {
                return 'Just now';
            } else if (diff < 3600000) {
                return Math.floor(diff / 60000) + 'm ago';
            } else if (diff < 86400000) {
                return Math.floor(diff / 3600000) + 'h ago';
            } else {
                return date.toLocaleDateString();
            }
        }
        
        function formatDateTime(timestamp) {
            return new Date(timestamp).toLocaleString();
        }
        
        function truncateText(text, maxLength) {
            if (text.length <= maxLength) {
                return text;
            }
            return text.substring(0, maxLength) + '...';
        }
    </script>
</body>
</html>
