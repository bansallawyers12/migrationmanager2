/**
 * Emails Module for CRM Client Email Tab
 * Handles upload, search, and display of .msg email files
 * Adapted from email-viewer app to work with migration manager backend
 */

(function() {
    'use strict';

    // =========================================================================
    // Module State
    // =========================================================================
    let currentPage = 1;
    let lastPage = 1;
    let isLoading = false;
    let isUploading = false;
    let currentMailType = 'inbox'; // 'inbox' or 'sent' - determines endpoint
    let currentLabelId = ''; // EmailLabel.id for filtering
    let currentSearch = '';
    let currentSort = 'date';
    let availableLabels = []; // Loaded from API

    // =========================================================================
    // Utility Functions
    // =========================================================================

    /**
     * Get client ID from the DOM (kept for backward compatibility)
     */
    function getClientId() {
        const container = document.querySelector('.email-interface-container');
        if (!container) {
            console.warn('Email interface container not found - this page may not support emails');
            return null;
        }
        
        if (!container.dataset.clientId) {
            console.error('Client ID not found in DOM - container exists but data-client-id attribute is missing');
            return null;
        }
        
        return container.dataset.clientId;
    }

    /**
     * Get matter ID from the DOM
     */
    function getMatterId() {
        const container = document.querySelector('.email-interface-container');
        if (!container) {
            console.warn('Email interface container not found - this page may not support emails');
            return null;
        }
        
        if (!container.dataset.matterId) {
            console.error('Matter ID not found in DOM - container exists but data-matter-id attribute is missing');
            return null;
        }
        
        return container.dataset.matterId;
    }

    /**
     * Get CSRF token from meta tag
     */
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    /**
     * Show notification message
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `email-notification email-notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            max-width: 350px;
            animation: slideIn 0.3s ease-out;
            font-size: 14px;
            ${type === 'success' ? 'background: #10b981; color: white;' : ''}
            ${type === 'error' ? 'background: #ef4444; color: white;' : ''}
            ${type === 'info' ? 'background: #3b82f6; color: white;' : ''}
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    /**
     * Format date to readable string
     */
    function formatDate(dateString) {
        if (!dateString) return 'Unknown';
        try {
            const date = new Date(dateString);
            return date.toLocaleString('en-AU', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    /**
     * Format file size to readable string
     */
    function formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    /**
     * Get attachment icon class based on content type
     */
    function getAttachmentIcon(contentType) {
        if (!contentType) return 'fas fa-paperclip';
        
        const type = contentType.toLowerCase();
        
        // Images
        if (type.includes('image')) {
            return 'fas fa-image';
        }
        
        // PDFs
        if (type.includes('pdf')) {
            return 'fas fa-file-pdf';
        }
        
        // Word documents
        if (type.includes('word') || type.includes('document') || type.includes('.docx')) {
            return 'fas fa-file-word';
        }
        
        // Excel spreadsheets
        if (type.includes('excel') || type.includes('spreadsheet') || type.includes('.xlsx')) {
            return 'fas fa-file-excel';
        }
        
        // PowerPoint
        if (type.includes('powerpoint') || type.includes('presentation')) {
            return 'fas fa-file-powerpoint';
        }
        
        // Archives
        if (type.includes('zip') || type.includes('rar') || type.includes('archive')) {
            return 'fas fa-file-archive';
        }
        
        // Code files
        if (type.includes('text/plain') || type.includes('code') || type.includes('javascript') || type.includes('html')) {
            return 'fas fa-file-code';
        }
        
        // Default
        return 'fas fa-paperclip';
    }

    /**
     * Get attachment icon color class based on content type
     */
    function getAttachmentIconColor(contentType) {
        if (!contentType) return '';
        
        const type = contentType.toLowerCase();
        
        if (type.includes('image')) return 'attachment-icon-image';
        if (type.includes('pdf')) return 'attachment-icon-pdf';
        if (type.includes('word') || type.includes('document')) return 'attachment-icon-word';
        if (type.includes('excel') || type.includes('spreadsheet')) return 'attachment-icon-excel';
        
        return '';
    }

    /**
     * Check if attachment can be previewed
     */
    function canPreviewAttachment(contentType) {
        if (!contentType) return false;
        
        const type = contentType.toLowerCase();
        return type.includes('image/') || type.includes('pdf');
    }

    /**
     * Sanitize filename for safe download
     */
    function sanitizeFilename(filename) {
        if (!filename) return 'download';
        
        // Remove invalid filename characters
        return filename
            .replace(/[/\\?%*:|"<>]/g, '-')  // Replace invalid chars
            .replace(/\s+/g, '_')             // Replace spaces with underscore
            .substring(0, 200);               // Limit length
    }

    /**
     * Filter to get only regular (non-inline) attachments
     */
    function getRegularAttachments(attachments) {
        if (!attachments || !Array.isArray(attachments)) {
            return [];
        }
        
        return attachments.filter(att => !att.is_inline);
    }

    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // =========================================================================
    // Upload Functionality
    // =========================================================================

    /**
     * Initialize upload functionality with drag & drop
     */
    window.initializeUpload = function() {
        console.log('Initializing upload module...');
        
        const fileInput = document.getElementById('emailFileInput');
        const uploadArea = document.getElementById('upload-area');
        const fileStatus = document.getElementById('fileStatus');
        const fileCountBadge = document.getElementById('file-count');
        const uploadProgress = document.getElementById('upload-progress');

        if (!fileInput || !uploadArea || !fileStatus) {
            console.warn('Upload elements not found - skipping email upload initialization (page may not have emails UI)');
            return;
        }

        let dragCounter = 0;

        // Prevent default drag behaviors on document
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when item is dragged over it
        uploadArea.addEventListener('dragenter', function(e) {
            dragCounter++;
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            dragCounter--;
            if (dragCounter === 0) {
                uploadArea.classList.remove('drag-over');
            }
        });

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        // Handle dropped files
        uploadArea.addEventListener('drop', function(e) {
            dragCounter = 0;
            uploadArea.classList.remove('drag-over');
            
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files && files.length > 0) {
                handleFiles(files);
            }
        });

        // Click to open file dialog
        uploadArea.addEventListener('click', function() {
            if (!isUploading) {
                fileInput.click();
            }
        });

        // Handle file input change
        fileInput.addEventListener('change', function() {
            const files = this.files;
            if (files && files.length > 0) {
                handleFiles(files);
            }
        });

        function handleFiles(files) {
            if (isUploading) {
                console.log('Upload already in progress');
                return;
            }

            console.log('Files selected:', files.length);

            // Filter to only .msg files
            const msgFiles = Array.from(files).filter(file => 
                file.name.toLowerCase().endsWith('.msg')
            );

            if (msgFiles.length === 0) {
                showNotification('Please select .msg files only', 'error');
                fileStatus.textContent = 'Only .msg files allowed';
                fileStatus.parentElement.className = 'upload-progress error';
                setTimeout(() => {
                    fileStatus.textContent = 'Ready to upload';
                    fileStatus.parentElement.className = 'upload-progress';
                }, 3000);
                return;
            }

            if (msgFiles.length !== files.length) {
                showNotification(`Only ${msgFiles.length} of ${files.length} files are .msg files`, 'info');
            }

            // Update file count badge
            updateFileCount(msgFiles.length);

            // Update status
            fileStatus.textContent = `${msgFiles.length} file(s) ready to upload`;
            fileStatus.parentElement.className = 'upload-progress';

            // Auto-upload immediately
            uploadFiles(msgFiles);
        }

        function updateFileCount(count) {
            if (fileCountBadge) {
                fileCountBadge.textContent = count;
                if (count > 0) {
                    fileCountBadge.classList.add('show');
                } else {
                    fileCountBadge.classList.remove('show');
                }
            }
        }

        console.log('Upload module initialized with drag & drop');
    };

    /**
     * Upload files to server
     */
    async function uploadFiles(files) {
        const clientId = getClientId();
        const matterId = getMatterId();
        
        if (!clientId) {
            showNotification('Client ID not found', 'error');
            return;
        }
        
        if (!matterId) {
            showNotification('Matter ID not found. Please select a matter.', 'error');
            return;
        }

        isUploading = true;
        
        const fileStatus = document.getElementById('fileStatus');
        const uploadProgress = document.getElementById('upload-progress');
        const fileCountBadge = document.getElementById('file-count');
        
        // Update UI - uploading state
        if (uploadProgress) {
            uploadProgress.className = 'upload-progress uploading';
        }
        fileStatus.textContent = `Uploading ${files.length} file(s)...`;

        try {
            const formData = new FormData();
            
            // Add files
            files.forEach(file => {
                formData.append('email_files[]', file);
            });

            // Add required fields based on current mail type (inbox or sent)
            formData.append('client_id', clientId);
            formData.append('type', 'client');
            
            // Add matter ID - this is now REQUIRED for matter-specific emails
            formData.append(
                currentMailType === 'sent' ? 'upload_sent_mail_client_matter_id' : 'upload_inbox_mail_client_matter_id',
                matterId
            );

            console.log('Uploading to:', currentMailType === 'sent' ? '/upload-sent-fetch-mail' : '/upload-fetch-mail');

            const response = await fetch(
                currentMailType === 'sent' ? '/upload-sent-fetch-mail' : '/upload-fetch-mail',
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                }
            );

            const data = await response.json();
            console.log('Upload response:', data);

            if (data.status || data.success) {
                // Success state
                if (uploadProgress) {
                    uploadProgress.className = 'upload-progress success';
                }
                fileStatus.textContent = 'Upload successful!';
                showNotification(data.message || 'Files uploaded successfully!', 'success');
                
                // Reset form after delay
                setTimeout(() => {
                    document.getElementById('emailFileInput').value = '';
                    fileStatus.textContent = 'Ready to upload';
                    if (uploadProgress) {
                        uploadProgress.className = 'upload-progress';
                    }
                    if (fileCountBadge) {
                        fileCountBadge.classList.remove('show');
                    }
                }, 2000);
                
                // Reload email list
                loadEmails();
            } else {
                // Error state
                if (uploadProgress) {
                    uploadProgress.className = 'upload-progress error';
                }
                fileStatus.textContent = 'Upload failed';
                showNotification(data.message || 'Upload failed', 'error');
                
                // Show errors if available
                if (data.errors) {
                    console.error('Upload errors:', data.errors);
                }
                
                // Reset after delay
                setTimeout(() => {
                    fileStatus.textContent = 'Ready to upload';
                    if (uploadProgress) {
                        uploadProgress.className = 'upload-progress';
                    }
                }, 3000);
            }

        } catch (error) {
            console.error('Upload error:', error);
            if (uploadProgress) {
                uploadProgress.className = 'upload-progress error';
            }
            fileStatus.textContent = 'Upload failed';
            showNotification('Upload failed: ' + error.message, 'error');
            
            // Reset after delay
            setTimeout(() => {
                fileStatus.textContent = 'Ready to upload';
                if (uploadProgress) {
                    uploadProgress.className = 'upload-progress';
                }
            }, 3000);
        } finally {
            isUploading = false;
        }
    }

    // =========================================================================
    // Search Functionality
    // =========================================================================

    /**
     * Initialize search functionality
     */
    window.initializeSearch = function() {
        console.log('Initializing search module...');

        const searchInput = document.getElementById('emailSearchInput');
        const labelFilter = document.getElementById('labelFilter');
        const applyBtn = document.getElementById('applyFiltersBtn');

        if (!searchInput) {
            console.warn('Search input not found - skipping search initialization');
            return;
        }
        
        if (!labelFilter) {
            console.warn('Label filter not found - search will work with limited functionality');
        }
        
        if (!applyBtn) {
            console.warn('Apply button not found - search will work with limited functionality');
        }

        // Real-time search (debounced)
        const debouncedSearch = debounce(function() {
            currentSearch = searchInput.value;
            currentPage = 1;
            loadEmails();
        }, 500);

        searchInput.addEventListener('input', debouncedSearch);

        // Label filter change
        if (labelFilter) {
            labelFilter.addEventListener('change', function() {
                currentLabelId = this.value;
                currentPage = 1;
                loadEmails();
            });
        }

        // Apply button (for immediate search)
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                currentSearch = searchInput.value;
                currentPage = 1;
                loadEmails();
            });
        }

        console.log('Search module initialized');
    };

    // =========================================================================
    // Email List Functionality
    // =========================================================================

    /**
     * Initialize email list and load initial emails
     */
    window.loadEmails = function() {
        console.log('Loading emails...');
        loadEmailsFromServer();
    };

    /**
     * Fetch and display emails from server
     */
    async function loadEmailsFromServer() {
        const clientId = getClientId();
        const matterId = getMatterId();
        
        if (!clientId) {
            console.warn('Cannot load emails - client ID not available');
            return;
        }
        
        if (!matterId) {
            console.warn('Cannot load emails - matter ID not available');
            renderEmptyState('Please select a matter to view emails');
            return;
        }

        if (isLoading) {
            console.log('Already loading emails');
            return;
        }

        isLoading = true;
        updateLoadingState(true);

        try {
            // Determine endpoint based on mail type
            const endpoint = currentMailType === 'sent' 
                ? '/clients/filter-sentemails' 
                : '/clients/filter-emails';

            const requestBody = {
                client_id: clientId,
                client_matter_id: matterId, // Add matter_id to filter emails
                search: currentSearch,
                status: '', // Keep for backward compatibility (mail_is_read)
                label_id: currentLabelId
            };

            console.log('Fetching emails from:', endpoint, requestBody);

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const emails = await response.json();
            console.log('Emails received:', emails);
            
            // Debug: Check attachments in received emails
            emails.forEach((email, index) => {
                if (email.attachments && email.attachments.length > 0) {
                    console.log(`Email ${index} (ID: ${email.id}) has ${email.attachments.length} attachments`);
                }
            });

            // Apply sorting
            const sortedEmails = sortEmails(emails);

            // Render emails
            renderEmails(sortedEmails);

            // Update counts
            updateEmailCounts(sortedEmails.length);

        } catch (error) {
            console.error('Error loading emails:', error);
            showNotification('Failed to load emails: ' + error.message, 'error');
            renderEmptyState('Error loading emails');
        } finally {
            isLoading = false;
            updateLoadingState(false);
        }
    }

    /**
     * Sort emails based on current sort option
     */
    function sortEmails(emails) {
        if (!Array.isArray(emails)) {
            console.error('Emails is not an array:', emails);
            return [];
        }

        return emails.slice().sort((a, b) => {
            switch (currentSort) {
                case 'subject':
                    return (a.subject || '').localeCompare(b.subject || '');
                case 'sender':
                    return (a.from_mail || '').localeCompare(b.from_mail || '');
                case 'date':
                default:
                    const dateA = new Date(a.created_at || 0);
                    const dateB = new Date(b.created_at || 0);
                    return dateB - dateA; // Newest first
            }
        });
    }

    /**
     * Render emails in the list
     */
    function renderEmails(emails) {
        const emailList = document.getElementById('emailList');
        if (!emailList) {
            console.error('Email list element not found');
            return;
        }

        // Clear existing content
        emailList.innerHTML = '';

        if (!emails || emails.length === 0) {
            renderEmptyState();
            return;
        }

        emails.forEach(email => {
            const emailItem = createEmailItem(email);
            emailList.appendChild(emailItem);
        });
    }

    /**
     * Create email list item element
     */
    function createEmailItem(email) {
        const div = document.createElement('div');
        div.className = 'email-item';
        div.dataset.emailId = email.id;

        const subject = email.subject || '(No subject)';
        const from = email.from_mail || 'Unknown sender';
        const to = cleanRecipients(email.to_mail) || 'Unknown recipient';
        const date = formatDate(email.created_at);
        const isRead = email.mail_is_read == 1;

        // NEW: Attachment indicator
        const hasAttachments = email.attachments && Array.isArray(email.attachments) && email.attachments.length > 0;
        const attachmentIcon = hasAttachments 
            ? `<i class="fas fa-paperclip attachment-indicator" title="${email.attachments.length} attachment(s)"></i>`
            : '';

        // NEW: Label badges
        const labelBadges = (email.labels && Array.isArray(email.labels)) 
            ? email.labels.map(label => 
                `<span class="label-badge" style="background-color: ${label.color}20; border-color: ${label.color}; color: ${label.color}">
                    <i class="${label.icon || 'fas fa-tag'}"></i> ${label.name}
                </span>`
            ).join('')
            : '';

        div.innerHTML = `
            <div class="email-item-header">
                <div class="email-subject" style="${!isRead ? 'font-weight: 700;' : ''}">
                    ${escapeHtml(subject)}
                    ${attachmentIcon}
                </div>
                <div class="email-date">${date}</div>
            </div>
            <div class="email-sender">From: ${escapeHtml(from)}</div>
            <div class="email-sender" style="font-size: 12px; color: #999;">To: ${escapeHtml(to)}</div>
            <div class="email-badges">
                ${labelBadges}
            </div>
        `;

        // Add click handler to view email
        div.addEventListener('click', function() {
            // Remove selection from other items
            document.querySelectorAll('.email-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Add selection to this item
            this.classList.add('selected');
            
            // Load email details
            loadEmailDetail(email);
        });

        return div;
    }

    /**
     * Render empty state
     */
    function renderEmptyState(message = null) {
        const emailList = document.getElementById('emailList');
        if (!emailList) return;

        emailList.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="empty-state-text">
                    <h3>${message || 'No emails found'}</h3>
                    <p>${message ? 'Please try again.' : 'Upload .msg files to get started with email management.'}</p>
                </div>
            </div>
        `;
    }

    /**
     * Update loading state visual indicator
     */
    function updateLoadingState(loading) {
        const emailList = document.getElementById('emailList');
        if (!emailList) return;

        if (loading) {
            emailList.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="empty-state-text">
                        <h3>Loading emails...</h3>
                        <p>Please wait</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Update email counts
     */
    function updateEmailCounts(total) {
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            resultsCount.textContent = `${total} result${total !== 1 ? 's' : ''}`;
        }
    }

    /**
     * Load and display email details with attachments
     */
    function loadEmailDetail(email) {
        const emailContentView = document.getElementById('emailContentView');
        const emailContentPlaceholder = document.getElementById('emailContentPlaceholder');

        if (!emailContentView || !emailContentPlaceholder) {
            console.error('Email detail elements not found');
            return;
        }

        // Hide placeholder, show content
        emailContentPlaceholder.style.display = 'none';
        emailContentView.style.display = 'block';

        const subject = email.subject || '(No subject)';
        const from = email.from_mail || 'Unknown';
        const to = cleanRecipients(email.to_mail) || 'Unknown';
        const date = formatDate(email.created_at);
        const message = email.message || '(No content)';

        // Get regular (non-inline) attachments
        const regularAttachments = getRegularAttachments(email.attachments);
        const hasAttachments = regularAttachments.length > 0;
        
        // Debug logging
        console.log('Loading email detail:', {
            id: email.id,
            subject: email.subject,
            attachments: email.attachments,
            regularAttachments: regularAttachments,
            hasAttachments: hasAttachments
        });

        // Build attachment list HTML
        let attachmentHtml = '';
        if (hasAttachments) {
            const attachmentItems = regularAttachments.map(att => `
                <div class="attachment-item" data-attachment-id="${att.id}">
                    <div class="attachment-info">
                        <i class="${getAttachmentIcon(att.content_type)} attachment-icon ${getAttachmentIconColor(att.content_type)}"></i>
                        <div class="attachment-details">
                            <div class="attachment-name">${escapeHtml(att.filename || att.display_name || 'Unknown')}</div>
                            <div class="attachment-size">${formatFileSize(att.file_size || 0)}</div>
                        </div>
                    </div>
                    <div class="attachment-actions">
                        <button class="download-btn download-attachment-btn" 
                                data-attachment-id="${att.id}" 
                                data-filename="${escapeHtml(att.filename || att.display_name || 'file')}"
                                title="Download ${escapeHtml(att.filename || 'file')}">
                            <i class="fas fa-download"></i> Download
                        </button>
                        ${canPreviewAttachment(att.content_type) ? `
                        <button class="preview-btn preview-attachment-btn" 
                                data-attachment-id="${att.id}" 
                                data-filename="${escapeHtml(att.filename || att.display_name || 'file')}"
                                title="Preview ${escapeHtml(att.filename || 'file')}">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');

            attachmentHtml = `
                <div class="attachment-list">
                    <div class="attachment-list-header">
                        <span class="attachment-list-title">
                            <i class="fas fa-paperclip"></i> 
                            ${regularAttachments.length} Attachment${regularAttachments.length !== 1 ? 's' : ''}
                        </span>
                        ${regularAttachments.length > 1 ? `
                        <button class="download-all-btn" 
                                data-mail-report-id="${email.id}"
                                data-email-subject="${escapeHtml(subject)}"
                                title="Download all attachments as ZIP">
                            <i class="fas fa-download"></i> Download All
                        </button>
                        ` : ''}
                    </div>
                    ${attachmentItems}
                </div>
            `;
        }

        // Original .msg file download section
        let previewSection = '';
        if (email.preview_url) {
            previewSection = `
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    <h4 style="margin-bottom: 10px; font-weight: 600;">Original Email File</h4>
                    <a href="${email.preview_url}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i> Download .msg File
                    </a>
                </div>
            `;
        }

        // Render complete email detail
        emailContentView.innerHTML = `
            <div class="email-content-header">
                <div class="email-content-subject">${escapeHtml(subject)}</div>
                <div class="email-content-meta">
                    <div><strong>From:</strong> ${escapeHtml(from)}</div>
                    <div><strong>To:</strong> ${escapeHtml(to)}</div>
                    <div><strong>Date:</strong> ${date}</div>
                </div>
            </div>
            <div class="email-content-body">
                ${message}
            </div>
            ${attachmentHtml}
            ${previewSection}
        `;
    }

    /**
     * Clean recipient strings by removing Python object representations
     */
    function cleanRecipients(recipientString) {
        if (!recipientString) return '';
        
        // Split by comma to handle multiple recipients
        const recipients = recipientString.split(',');
        
        // Filter out invalid recipients (Python object strings, malformed addresses)
        const validRecipients = recipients
            .map(r => r.trim())
            .filter(r => {
                // Remove entries that look like Python object representations
                if (r.includes('<extract_msg.') || r.includes('object at 0x')) {
                    return false;
                }
                // Remove entries that look like raw object references
                if (r.includes('Recipient') && r.includes('0x')) {
                    return false;
                }
                // Keep only entries that look like valid email addresses or names
                return r.length > 0 && !r.startsWith('<') && !r.includes('0x');
            });
        
        // Return cleaned recipient list or a placeholder if none are valid
        return validRecipients.length > 0 ? validRecipients.join(', ') : '';
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // =========================================================================
    // Pagination
    // =========================================================================

    function initializePagination() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadEmailsFromServer();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                if (currentPage < lastPage) {
                    currentPage++;
                    loadEmailsFromServer();
                }
            });
        }
    }

    // =========================================================================
    // Label Management
    // =========================================================================

    /**
     * Fetch all labels from API
     */
    async function fetchLabels() {
        try {
            const response = await fetch('/email-labels', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success && Array.isArray(data.labels)) {
                availableLabels = data.labels;
                populateLabelFilter();
            }
        } catch (error) {
            console.error('Error fetching labels:', error);
        }
    }

    /**
     * Populate label filter dropdown
     */
    function populateLabelFilter() {
        const labelFilter = document.getElementById('labelFilter');
        if (!labelFilter) return;

        // Clear existing options (except "All Labels")
        while (labelFilter.options.length > 1) {
            labelFilter.remove(1);
        }

        // Add label options
        availableLabels.forEach(label => {
            const option = document.createElement('option');
            option.value = label.id;
            option.textContent = label.name;
            labelFilter.appendChild(option);
        });
    }

    /**
     * Create new label
     */
    async function createLabel(name, color, icon) {
        try {
            const response = await fetch('/email-labels', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ name, color, icon })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Label created successfully', 'success');
                await fetchLabels(); // Reload labels
                return data.label;
            } else {
                throw new Error(data.message || 'Failed to create label');
            }
        } catch (error) {
            console.error('Error creating label:', error);
            showNotification('Error creating label: ' + error.message, 'error');
            return null;
        }
    }

    /**
     * Apply label to email
     */
    async function applyLabel(mailReportId, labelId) {
        try {
            const response = await fetch('/email-labels/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ mail_report_id: mailReportId, label_id: labelId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Label applied successfully', 'success');
                return true;
            } else {
                throw new Error(data.message || 'Failed to apply label');
            }
        } catch (error) {
            console.error('Error applying label:', error);
            showNotification('Error applying label: ' + error.message, 'error');
            return false;
        }
    }

    /**
     * Remove label from email
     */
    async function removeLabel(mailReportId, labelId) {
        try {
            const response = await fetch('/email-labels/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ mail_report_id: mailReportId, label_id: labelId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Label removed successfully', 'success');
                return true;
            } else {
                throw new Error(data.message || 'Failed to remove label');
            }
        } catch (error) {
            console.error('Error removing label:', error);
            showNotification('Error removing label: ' + error.message, 'error');
            return false;
        }
    }

    // =========================================================================
    // Attachment Handling
    // =========================================================================

    /**
     * Download individual attachment
     */
    async function downloadAttachment(attachmentId, filename) {
        try {
            const response = await fetch(`/mail-attachments/${attachmentId}/download`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/octet-stream'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showNotification(`Downloaded: ${filename}`, 'success');
        } catch (error) {
            console.error('Error downloading attachment:', error);
            showNotification('Error downloading attachment: ' + error.message, 'error');
        }
    }

    /**
     * Download all attachments as ZIP
     */
    async function downloadAllAttachments(mailReportId, emailSubject) {
        try {
            const response = await fetch(`/mail-attachments/email/${mailReportId}/download-all`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/octet-stream'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const sanitizedSubject = sanitizeFilename(emailSubject || 'email');
            a.download = `${sanitizedSubject}_attachments.zip`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showNotification('Attachments downloaded successfully', 'success');
        } catch (error) {
            console.error('Error downloading attachments:', error);
            showNotification('Error downloading attachments: ' + error.message, 'error');
        }
    }

    /**
     * Preview attachment
     */
    async function previewAttachment(attachmentId, filename) {
        try {
            const previewUrl = `/mail-attachments/${attachmentId}/preview`;
            const modal = document.getElementById('attachmentPreviewModal');
            const frame = document.getElementById('previewFrame');
            const filenameEl = document.getElementById('previewFileName');

            if (modal && frame && filenameEl) {
                filenameEl.textContent = filename;
                frame.src = previewUrl;
                modal.style.display = 'flex';
            }
        } catch (error) {
            console.error('Error previewing attachment:', error);
            showNotification('Error previewing attachment: ' + error.message, 'error');
        }
    }

    // =========================================================================
    // Initialization
    // =========================================================================

    // Initialize pagination on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePagination);
    } else {
        initializePagination();
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNewFeatures);
    } else {
        initializeNewFeatures();
    }

    /**
     * Initialize new filter and modal features
     */
    function initializeNewFeatures() {
        // Fetch labels on load
        fetchLabels();

        // Mail type filter (Inbox/Sent)
        const mailTypeFilter = document.getElementById('mailTypeFilter');
        if (mailTypeFilter) {
            mailTypeFilter.addEventListener('change', function() {
                currentMailType = this.value;
                loadEmailsFromServer();
            });
        }

        // Label filter
        const labelFilter = document.getElementById('labelFilter');
        if (labelFilter) {
            labelFilter.addEventListener('change', function() {
                currentLabelId = this.value;
            });
        }

        // Apply filters button
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', function() {
                loadEmailsFromServer();
            });
        }

        // Create label button
        const createLabelBtn = document.getElementById('createLabelBtn');
        if (createLabelBtn) {
            createLabelBtn.addEventListener('click', function() {
                showLabelModal();
            });
        }

        // Label modal close buttons
        const closeLabelModal = document.getElementById('closeLabelModal');
        const cancelLabelBtn = document.getElementById('cancelLabelBtn');
        if (closeLabelModal) {
            closeLabelModal.addEventListener('click', hideLabelModal);
        }
        if (cancelLabelBtn) {
            cancelLabelBtn.addEventListener('click', hideLabelModal);
        }

        // Label modal save button
        const saveLabelBtn = document.getElementById('saveLabelBtn');
        if (saveLabelBtn) {
            saveLabelBtn.addEventListener('click', handleLabelCreate);
        }

        // Color picker
        const colorPicker = document.getElementById('colorPicker');
        if (colorPicker) {
            colorPicker.querySelectorAll('.color-option').forEach(option => {
                option.addEventListener('click', function() {
                    colorPicker.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('selectedColor').value = this.dataset.color;
                });
            });
            // Select first color by default
            const firstColor = colorPicker.querySelector('.color-option');
            if (firstColor) firstColor.classList.add('selected');
        }

        // Icon picker
        const iconPicker = document.getElementById('iconPicker');
        if (iconPicker) {
            iconPicker.querySelectorAll('.icon-option').forEach(option => {
                option.addEventListener('click', function() {
                    iconPicker.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('selectedIcon').value = this.dataset.icon;
                });
            });
            // Select first icon by default
            const firstIcon = iconPicker.querySelector('.icon-option');
            if (firstIcon) firstIcon.classList.add('selected');
        }

        // Preview modal close
        const closePreviewBtn = document.getElementById('closePreviewBtn');
        const previewOverlay = document.getElementById('previewOverlay');
        if (closePreviewBtn) {
            closePreviewBtn.addEventListener('click', hidePreviewModal);
        }
        if (previewOverlay) {
            previewOverlay.addEventListener('click', hidePreviewModal);
        }

        // Initialize attachment handlers
        initializeAttachmentHandlers();
    }

    /**
     * Event delegation for attachment buttons
     * Handles all attachment-related clicks
     */
    function initializeAttachmentHandlers() {
        // Single delegated listener for all attachment actions
        document.addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;

            // Download individual attachment
            if (target.classList.contains('download-attachment-btn')) {
                e.preventDefault();
                const attachmentId = target.dataset.attachmentId;
                const filename = target.dataset.filename;
                
                if (attachmentId && filename) {
                    // Disable button during download
                    const originalHtml = target.innerHTML;
                    target.disabled = true;
                    target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
                    
                    downloadAttachment(attachmentId, filename).finally(() => {
                        target.disabled = false;
                        target.innerHTML = originalHtml;
                    });
                }
            }

            // Preview attachment
            if (target.classList.contains('preview-attachment-btn')) {
                e.preventDefault();
                const attachmentId = target.dataset.attachmentId;
                const filename = target.dataset.filename;
                
                if (attachmentId && filename) {
                    previewAttachment(attachmentId, filename);
                }
            }

            // Download all attachments as ZIP
            if (target.classList.contains('download-all-btn')) {
                e.preventDefault();
                const mailReportId = target.dataset.mailReportId;
                const emailSubject = target.dataset.emailSubject;
                
                if (mailReportId) {
                    // Disable button during download
                    const originalHtml = target.innerHTML;
                    target.disabled = true;
                    target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating ZIP...';
                    
                    downloadAllAttachments(mailReportId, emailSubject).finally(() => {
                        target.disabled = false;
                        target.innerHTML = originalHtml;
                    });
                }
            }
        });
    }

    /**
     * Show label creation modal
     */
    function showLabelModal() {
        const modal = document.getElementById('labelModal');
        if (modal) {
            document.getElementById('labelNameInput').value = '';
            modal.style.display = 'flex';
        }
    }

    /**
     * Hide label creation modal
     */
    function hideLabelModal() {
        const modal = document.getElementById('labelModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    /**
     * Handle label creation
     */
    async function handleLabelCreate() {
        const name = document.getElementById('labelNameInput').value.trim();
        const color = document.getElementById('selectedColor').value;
        const icon = document.getElementById('selectedIcon').value;

        if (!name) {
            showNotification('Please enter a label name', 'error');
            return;
        }

        const label = await createLabel(name, color, icon);
        if (label) {
            hideLabelModal();
        }
    }

    /**
     * Hide preview modal
     */
    function hidePreviewModal() {
        const modal = document.getElementById('attachmentPreviewModal');
        const frame = document.getElementById('previewFrame');
        if (modal && frame) {
            modal.style.display = 'none';
            frame.src = ''; // Stop loading
        }
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    console.log('Emails module loaded');

})();

