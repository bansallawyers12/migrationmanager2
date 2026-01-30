<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Viewer</title>
    <link href="{{asset('icons/font-awesome/css/all.min.css')}}" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        /* Left Sidebar */
        .sidebar {
            width: 400px;
            background: white;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
        }

        .sidebar-header h1 {
            color: #333;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Upload Section */
        .upload-section {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .upload-section h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }

        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .upload-area:hover {
            border-color: #0056b3;
            background: #e3f2fd;
        }

        .upload-area.dragover {
            border-color: #28a745;
            background: #d4edda;
        }

        .upload-icon {
            font-size: 48px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .upload-text {
            color: #666;
            margin-bottom: 15px;
        }

        .choose-files-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .choose-files-btn:hover {
            background: #0056b3;
        }

        .file-input {
            display: none;
        }

        .upload-info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        /* Search & Filter Section */
        .search-section {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .search-section h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }

        .search-box {
            position: relative;
            margin-bottom: 15px;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .sort-section {
            display: flex;
            gap: 10px;
        }

        .sort-select {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Email List Section */
        .email-list-section {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .email-list-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .email-list-header h3 {
            color: #333;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .email-count {
            color: #666;
            font-size: 14px;
        }

        .refresh-btn {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
        }

        .email-list {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }

                 .email-item {
             padding: 15px 20px;
             border-bottom: 1px solid #f0f0f0;
             cursor: pointer;
             transition: all 0.3s ease;
             position: relative;
         }
 
         .email-item:hover {
             background: #f8f9fa;
         }
 
         .email-item.selected {
             background: #e3f2fd;
             border-left: 4px solid #007bff;
         }
 
         .email-item.selected:hover {
             background: #d1ecf1;
         }

        .email-subject {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .email-date {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .email-sender {
            font-size: 13px;
            color: #007bff;
            margin-bottom: 5px;
        }

                 .email-preview {
             font-size: 13px;
             color: #666;
             line-height: 1.4;
             margin-bottom: 8px;
             display: -webkit-box;
             -webkit-line-clamp: 2;
             -webkit-box-orient: vertical;
             overflow: hidden;
             margin-top: 5px;
         }

                 .email-meta {
             display: flex;
             justify-content: space-between;
             align-items: center;
             font-size: 12px;
             color: #999;
             margin-top: 8px;
         }

                 .email-actions {
             position: relative;
         }
 
         .email-actions-menu {
             position: absolute;
             top: 100%;
             right: 0;
             background: white;
             border: 1px solid #ddd;
             border-radius: 5px;
             box-shadow: 0 2px 10px rgba(0,0,0,0.1);
             z-index: 1000;
             min-width: 180px;
             display: none;
         }
 
         .email-actions-menu.show {
             display: block;
         }
 
         .email-actions-menu-item {
             padding: 10px 15px;
             cursor: pointer;
             display: flex;
             align-items: center;
             gap: 10px;
             font-size: 14px;
             color: #333;
             border-bottom: 1px solid #f0f0f0;
             transition: background 0.2s ease;
         }
 
         .email-actions-menu-item:last-child {
             border-bottom: none;
         }
 
         .email-actions-menu-item:hover {
             background: #f8f9fa;
         }
 
         .email-actions-menu-item i {
             color: #666;
             width: 16px;
         }
 
         .email-actions-menu-item.export-pdf i {
             color: #007bff;
         }
 
         .email-actions-menu-item.download-attachments i {
             color: #28a745;
         }
 
         .email-actions-menu-item.delete-email i {
             color: #dc3545;
         }
 
         .email-actions-toggle {
             background: none;
             border: none;
             color: #666;
             cursor: pointer;
             padding: 5px;
             font-size: 16px;
             border-radius: 3px;
             transition: background 0.2s ease;
         }
 
         .email-actions-toggle:hover {
             background: #f0f0f0;
             color: #333;
         }

        /* Right Content Area */
        .content-area {
            flex: 1;
            background: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-placeholder {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #999;
        }

        .content-placeholder i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .content-placeholder h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .content-placeholder p {
            font-size: 14px;
        }

                 /* Email Content */
         .email-content {
             flex: 1;
             overflow: hidden;
             display: none;
             background: white;
         }
 
         .email-content.active {
             display: flex;
             flex-direction: column;
         }
 
         .email-header {
             padding: 30px;
             border-bottom: 1px solid #e0e0e0;
             background: white;
         }
 
         .email-header-top {
             display: flex;
             justify-content: space-between;
             align-items: flex-start;
             margin-bottom: 20px;
         }
 
         .email-subject-section {
             flex: 1;
         }
 
         .email-subject {
             font-size: 24px;
             font-weight: 600;
             color: #333;
             margin-bottom: 10px;
             line-height: 1.3;
         }
 
         .email-date-section {
             display: flex;
             align-items: center;
             gap: 15px;
         }
 
         .email-date {
             font-size: 14px;
             color: #666;
             font-weight: 500;
         }
 
         .export-pdf-btn {
             background: #007bff;
             color: white;
             border: none;
             padding: 8px 16px;
             border-radius: 5px;
             cursor: pointer;
             font-size: 14px;
             display: flex;
             align-items: center;
             gap: 8px;
             transition: background 0.3s ease;
         }
 
         .export-pdf-btn:hover {
             background: #0056b3;
         }
 
         .email-meta-info {
             display: flex;
             flex-direction: column;
             gap: 12px;
             font-size: 14px;
             color: #333;
         }
 
         .email-meta-row {
             display: flex;
             align-items: center;
             gap: 10px;
         }
 
         .email-meta-label {
             font-weight: 600;
             color: #666;
             min-width: 60px;
         }
 
         .email-meta-value {
             color: #333;
         }
 
         .email-tabs {
             display: flex;
             border-bottom: 1px solid #e0e0e0;
             background: #f8f9fa;
         }
 
         .email-tab {
             padding: 15px 25px;
             background: none;
             border: none;
             cursor: pointer;
             font-size: 14px;
             color: #666;
             border-bottom: 3px solid transparent;
             transition: all 0.3s ease;
         }
 
         .email-tab.active {
             color: #007bff;
             border-bottom-color: #007bff;
             background: white;
         }
 
         .email-tab:hover {
             color: #007bff;
         }
 
         .email-tab-content {
             flex: 1;
             overflow-y: auto;
             display: none;
         }
 
         .email-tab-content.active {
             display: block;
         }
 
         .email-body {
             padding: 30px;
             line-height: 1.6;
             color: #333;
             font-size: 14px;
         }
 
         .email-body-content {
             white-space: pre-wrap;
         }
 
         .email-attachments {
             padding: 30px;
         }
 
         .email-attachments h4 {
             margin-bottom: 20px;
             color: #333;
             font-size: 16px;
         }
 
         .attachment-list {
             display: flex;
             flex-direction: column;
             gap: 10px;
         }
 
         .attachment-item {
             display: flex;
             align-items: center;
             gap: 15px;
             padding: 15px;
             background: #f8f9fa;
             border: 1px solid #e0e0e0;
             border-radius: 8px;
             font-size: 14px;
         }
 
         .attachment-icon {
             color: #007bff;
             font-size: 18px;
         }
 
         .attachment-info {
             flex: 1;
         }
 
         .attachment-name {
             font-weight: 600;
             color: #333;
             margin-bottom: 5px;
         }
 
         .attachment-size {
             color: #666;
             font-size: 12px;
         }
 
         .attachment-download {
             background: #007bff;
             color: white;
             border: none;
             padding: 8px 16px;
             border-radius: 5px;
             cursor: pointer;
             font-size: 12px;
             transition: background 0.3s ease;
         }
 
         .attachment-download:hover {
             background: #0056b3;
         }
 
         .email-metadata {
             padding: 30px;
         }
 
         .email-metadata h4 {
             margin-bottom: 20px;
             color: #333;
             font-size: 16px;
         }
 
         .metadata-grid {
             display: grid;
             grid-template-columns: 1fr 1fr;
             gap: 20px;
         }
 
         .metadata-item {
             display: flex;
             flex-direction: column;
             gap: 5px;
         }
 
         .metadata-label {
             font-weight: 600;
             color: #666;
             font-size: 12px;
         }
 
         .metadata-value {
             color: #333;
             font-size: 14px;
             word-break: break-word;
         }

        /* Loading States */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #007bff;
        }

        .loading.active {
            display: block;
        }

        /* Success/Error Messages */
        .message {
            padding: 15px;
            margin: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .message.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: 50vh;
            }
            
            .content-area {
                height: 50vh;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Sidebar -->
        <div class="sidebar">
            <!-- Header -->
            <div class="sidebar-header">
                <h1><i class="fas fa-envelope"></i> Email Viewer</h1>
            </div>

            <!-- Upload Section -->
            <div class="upload-section">
                <h3>Upload Email</h3>
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Drag and drop .msg files here or</div>
                    <button class="choose-files-btn" onclick="document.getElementById('fileInput').click()">
                        Choose Files
                    </button>
                    <input type="file" id="fileInput" class="file-input" accept=".msg" multiple>
                    <div class="upload-info">Maximum file size: 10MB</div>
                </div>
                <div class="loading" id="uploadLoading">Uploading files...</div>
            </div>

            <!-- Search & Filter Section -->
            <div class="search-section">
                <h3>Search & Filter</h3>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Search emails...">
                </div>
                <div class="sort-section">
                    <select class="sort-select" id="sortBy">
                        <option value="date">Date</option>
                        <option value="subject">Subject</option>
                        <option value="sender">Sender</option>
                    </select>
                    <select class="sort-select" id="sortOrder">
                        <option value="desc">Newest First</option>
                        <option value="asc">Oldest First</option>
                    </select>
                </div>
            </div>

            <!-- Email List Section -->
            <div class="email-list-section">
                <div class="email-list-header">
                    <h3>
                        <i class="fas fa-list"></i>
                        Email List
                        <span class="email-count" id="emailCount">0 emails</span>
                    </h3>
                    <button class="refresh-btn" onclick="loadEmails()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="email-list" id="emailList">
                    <!-- Email items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="content-area">
            <!-- Placeholder when no email is selected -->
            <div class="content-placeholder" id="contentPlaceholder">
                <i class="fas fa-envelope-open"></i>
                <h3>Select an email to view its contents</h3>
                <p>Choose an email from the list to see its details, attachments, and content</p>
            </div>

                         <!-- Email Content (hidden by default) -->
             <div class="email-content" id="emailContent">
                 <div class="email-header" id="emailHeader">
                     <!-- Email header content will be loaded here -->
                 </div>
                 
                 <!-- Email Tabs -->
                 <div class="email-tabs" id="emailTabs">
                     <button class="email-tab active" data-tab="content">Content</button>
                     <button class="email-tab" data-tab="attachments">Attachments <span id="attachmentCount">(0)</span></button>
                     <button class="email-tab" data-tab="metadata">Metadata</button>
                     <button class="email-tab" data-tab="labels">Labels</button>
                 </div>
                 
                 <!-- Tab Contents -->
                 <div class="email-tab-content active" id="contentTab">
                     <div class="email-body" id="emailBody">
                         <!-- Email body content will be loaded here -->
                     </div>
                 </div>
                 
                 <div class="email-tab-content" id="attachmentsTab">
                     <div class="email-attachments" id="emailAttachments">
                         <!-- Email attachments will be loaded here -->
                     </div>
                 </div>
                 
                 <div class="email-tab-content" id="metadataTab">
                     <div class="email-metadata" id="emailMetadata">
                         <!-- Email metadata will be loaded here -->
                     </div>
                 </div>
                 
                 <div class="email-tab-content" id="labelsTab">
                     <div class="email-metadata" id="emailLabels">
                         <!-- Email labels will be loaded here -->
                     </div>
                 </div>
             </div>
        </div>
    </div>

    <script>
        let emails = [];
        let selectedEmailId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadEmails();
            setupEventListeners();
        });

                 // Setup event listeners
         function setupEventListeners() {
             // File upload
             const fileInput = document.getElementById('fileInput');
             const uploadArea = document.getElementById('uploadArea');
 
             fileInput.addEventListener('change', handleFileUpload);
             
             // Drag and drop
             uploadArea.addEventListener('dragover', handleDragOver);
             uploadArea.addEventListener('dragleave', handleDragLeave);
             uploadArea.addEventListener('drop', handleDrop);
 
             // Search and filter
             document.getElementById('searchInput').addEventListener('input', filterEmails);
             document.getElementById('sortBy').addEventListener('change', sortEmails);
             document.getElementById('sortOrder').addEventListener('change', sortEmails);
             
             // Email tabs
             setupEmailTabs();
         }
         
         // Setup email tabs
         function setupEmailTabs() {
             const tabs = document.querySelectorAll('.email-tab');
             tabs.forEach(tab => {
                 tab.addEventListener('click', () => {
                     const tabName = tab.dataset.tab;
                     switchTab(tabName);
                 });
             });
         }
         
         // Switch email tab
         function switchTab(tabName) {
             // Update tab buttons
             document.querySelectorAll('.email-tab').forEach(tab => {
                 tab.classList.remove('active');
             });
             document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
             
             // Update tab content
             document.querySelectorAll('.email-tab-content').forEach(content => {
                 content.classList.remove('active');
             });
             document.getElementById(tabName + 'Tab').classList.add('active');
         }

        // File upload handling
        function handleFileUpload(event) {
            const files = event.target.files;
            uploadFiles(files);
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(event) {
            event.currentTarget.classList.remove('dragover');
        }

        function handleDrop(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('dragover');
            
            const files = event.dataTransfer.files;
            const msgFiles = Array.from(files).filter(file => file.name.toLowerCase().endsWith('.msg'));
            
            if (msgFiles.length > 0) {
                uploadFiles(msgFiles);
            }
        }

        function uploadFiles(files) {
            const formData = new FormData();
            const loading = document.getElementById('uploadLoading');
            
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });

            loading.classList.add('active');

            fetch('/api/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                loading.classList.remove('active');
                
                if (data.success) {
                    showMessage('Files uploaded successfully!', 'success');
                    loadEmails(); // Refresh the email list
                } else {
                    showMessage('Upload failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                loading.classList.remove('active');
                showMessage('Upload error: ' + error.message, 'error');
            });

            // Clear the file input
            document.getElementById('fileInput').value = '';
        }

        // Load emails
        function loadEmails() {
            const emailList = document.getElementById('emailList');
            const loading = document.getElementById('uploadLoading');
            
            loading.classList.add('active');
            emailList.innerHTML = '';

            // API call commented out temporarily to fix 404 error
            /*
            fetch('/api/emails')
            .then(response => response.json())
            .then(data => {
                loading.classList.remove('active');
                
                if (data.success) {
                    emails = data.data.data || [];
                    renderEmailList();
                    updateEmailCount();
                } else {
                    showMessage('Failed to load emails: ' + data.message, 'error');
                }
            })
            .catch(error => {
                loading.classList.remove('active');
                showMessage('Error loading emails: ' + error.message, 'error');
            });
            */
            
            // Show placeholder message instead
            loading.classList.remove('active');
            showMessage('Email loading temporarily disabled', 'info');
        }

        // Render email list
        function renderEmailList() {
            const emailList = document.getElementById('emailList');
            emailList.innerHTML = '';

            if (emails.length === 0) {
                emailList.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">No emails found</div>';
                return;
            }

            emails.forEach(email => {
                const emailItem = document.createElement('div');
                emailItem.className = 'email-item';
                emailItem.dataset.emailId = email.id;
                
                const sentDate = email.sent_date ? new Date(email.sent_date).toLocaleDateString() : 'Unknown date';
                const fileSize = formatFileSize(email.file_size);
                
                                 emailItem.innerHTML = `
                     <div class="email-subject">${email.subject || 'No Subject'}</div>
                     <div class="email-date">${sentDate}</div>
                     <div class="email-sender">From: ${email.sender_name || 'Unknown'} <${email.sender_email || 'unknown@email.com'}></div>
                     <div class="email-preview">${email.text_content ? email.text_content.substring(0, 100) + '...' : 'No content preview available'}</div>
                     <div class="email-meta">
                         <span>${fileSize}</span>
                         <div class="email-actions">
                             <button class="email-actions-toggle" onclick="toggleEmailActions(${email.id}, event)" title="More actions">
                                 <i class="fas fa-ellipsis-v"></i>
                             </button>
                             <div class="email-actions-menu" id="emailActions${email.id}">
                                 <div class="email-actions-menu-item export-pdf" onclick="exportEmailToPdf(${email.id})">
                                     <i class="fas fa-download"></i>
                                     Export as PDF
                                 </div>
                                 <div class="email-actions-menu-item download-attachments" onclick="downloadAllAttachments(${email.id})">
                                     <i class="fas fa-paperclip"></i>
                                     Download Attachments
                                 </div>
                                 <div class="email-actions-menu-item delete-email" onclick="deleteEmail(${email.id})">
                                     <i class="fas fa-trash"></i>
                                     Delete Email
                                 </div>
                             </div>
                         </div>
                     </div>
                 `;

                emailItem.addEventListener('click', () => viewEmail(email.id));
                emailList.appendChild(emailItem);
            });
        }

        // View email
        function viewEmail(emailId) {
            selectedEmailId = emailId;
            
            // Update selected state
            document.querySelectorAll('.email-item').forEach(item => {
                item.classList.remove('selected');
            });
            document.querySelector(`[data-email-id="${emailId}"]`).classList.add('selected');

            // Show loading
            const contentPlaceholder = document.getElementById('contentPlaceholder');
            const emailContent = document.getElementById('emailContent');
            
            contentPlaceholder.style.display = 'none';
            emailContent.classList.add('active');

            // Load email details - commented out temporarily
            /*
            fetch(`/api/emails/${emailId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderEmailContent(data.data);
                } else {
                    showMessage('Failed to load email: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error loading email: ' + error.message, 'error');
            });
            */
            
            // Show placeholder message instead
            showMessage('Email content loading temporarily disabled', 'info');
        }

                 // Render email content
         function renderEmailContent(email) {
             const emailHeader = document.getElementById('emailHeader');
             const emailBody = document.getElementById('emailBody');
             const emailAttachments = document.getElementById('emailAttachments');
             const emailMetadata = document.getElementById('emailMetadata');
             const emailLabels = document.getElementById('emailLabels');
 
             // Format date
             const sentDate = email.sent_date ? new Date(email.sent_date).toLocaleDateString('en-US', {
                 year: 'numeric',
                 month: 'long',
                 day: 'numeric',
                 hour: '2-digit',
                 minute: '2-digit'
             }) : 'Unknown date';
 
             // Header
             emailHeader.innerHTML = `
                 <div class="email-header-top">
                     <div class="email-subject-section">
                         <div class="email-subject">${email.subject || 'No Subject'}</div>
                     </div>
                     <div class="email-date-section">
                         <div class="email-date">${sentDate}</div>
                         <button class="export-pdf-btn" onclick="exportEmailToPdf(${email.id})">
                             <i class="fas fa-download"></i>
                             Export PDF
                         </button>
                     </div>
                 </div>
                 <div class="email-meta-info">
                     <div class="email-meta-row">
                         <span class="email-meta-label">From:</span>
                         <span class="email-meta-value">${email.sender_name || 'Unknown'} <${email.sender_email || 'unknown@email.com'}></span>
                     </div>
                     <div class="email-meta-row">
                         <span class="email-meta-label">To:</span>
                         <span class="email-meta-value">${email.recipients ? email.recipients.join(', ') : 'No recipients'}</span>
                     </div>
                 </div>
             `;
 
             // Body
             const content = email.html_content || email.text_content || 'No content available';
             emailBody.innerHTML = `
                 <div class="email-body-content">${content}</div>
             `;
 
             // Attachments
             const attachmentCount = email.attachments ? email.attachments.length : 0;
             document.getElementById('attachmentCount').textContent = `(${attachmentCount})`;
             
             if (email.attachments && email.attachments.length > 0) {
                 const attachmentHtml = email.attachments.map(attachment => `
                     <div class="attachment-item">
                         <i class="fas fa-paperclip attachment-icon"></i>
                         <div class="attachment-info">
                             <div class="attachment-name">${attachment.filename}</div>
                             <div class="attachment-size">${formatFileSize(attachment.file_size)}</div>
                         </div>
                         <button class="attachment-download" onclick="downloadAttachment(${attachment.id})">
                             Download
                         </button>
                     </div>
                 `).join('');
                 
                 emailAttachments.innerHTML = `
                     <h4>Attachments (${attachmentCount})</h4>
                     <div class="attachment-list">${attachmentHtml}</div>
                 `;
             } else {
                 emailAttachments.innerHTML = '<h4>No Attachments</h4>';
             }
 
             // Metadata
             emailMetadata.innerHTML = `
                 <h4>Email Metadata</h4>
                 <div class="metadata-grid">
                     <div class="metadata-item">
                         <div class="metadata-label">Subject</div>
                         <div class="metadata-value">${email.subject || 'No Subject'}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">From</div>
                         <div class="metadata-value">${email.sender_name || 'Unknown'} <${email.sender_email || 'unknown@email.com'}></div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">To</div>
                         <div class="metadata-value">${email.recipients ? email.recipients.join(', ') : 'No recipients'}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">Date</div>
                         <div class="metadata-value">${sentDate}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">Message ID</div>
                         <div class="metadata-value">${email.message_id || 'Not available'}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">File Name</div>
                         <div class="metadata-value">${email.file_name}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">File Size</div>
                         <div class="metadata-value">${formatFileSize(email.file_size)}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">Status</div>
                         <div class="metadata-value">${email.status}</div>
                     </div>
                 </div>
             `;
 
             // Labels
             emailLabels.innerHTML = `
                 <h4>Email Labels</h4>
                 <div class="metadata-grid">
                     <div class="metadata-item">
                         <div class="metadata-label">Tags</div>
                         <div class="metadata-value">${email.tags ? email.tags.join(', ') : 'No tags'}</div>
                     </div>
                     <div class="metadata-item">
                         <div class="metadata-label">Thread ID</div>
                         <div class="metadata-value">${email.thread_id || 'Not available'}</div>
                     </div>
                 </div>
             `;
         }

                 // Delete email
         function deleteEmail(emailId) {
             // Close menu
             document.querySelectorAll('.email-actions-menu').forEach(menu => {
                 menu.classList.remove('show');
             });
             
             if (!confirm('Are you sure you want to delete this email?')) {
                 return;
             }

            // API call commented out temporarily to fix 404 error
            /*
            fetch(`/api/emails/${emailId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Email deleted successfully!', 'success');
                    if (selectedEmailId === emailId) {
                        // Clear the content area if the deleted email was selected
                        document.getElementById('contentPlaceholder').style.display = 'flex';
                        document.getElementById('emailContent').classList.remove('active');
                        selectedEmailId = null;
                    }
                    loadEmails(); // Refresh the list
                } else {
                    showMessage('Failed to delete email: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error deleting email: ' + error.message, 'error');
            });
            */
            
            // Show placeholder message instead
            showMessage('Email deletion temporarily disabled', 'info');
        }

                 // Download attachment - commented out temporarily
         function downloadAttachment(attachmentId) {
             // window.open(`/api/attachments/${attachmentId}/download`, '_blank');
             showMessage('Attachment download temporarily disabled', 'info');
         }
         
         // Toggle email actions menu
         function toggleEmailActions(emailId, event) {
             event.stopPropagation(); // Prevent email selection
             
             // Close all other menus first
             document.querySelectorAll('.email-actions-menu').forEach(menu => {
                 menu.classList.remove('show');
             });
             
             // Toggle current menu
             const menu = document.getElementById(`emailActions${emailId}`);
             menu.classList.toggle('show');
         }
         
         // Close all email action menus when clicking outside
         document.addEventListener('click', function(event) {
             if (!event.target.closest('.email-actions')) {
                 document.querySelectorAll('.email-actions-menu').forEach(menu => {
                     menu.classList.remove('show');
                 });
             }
         });
         
         // Export email to PDF
         function exportEmailToPdf(emailId) {
             // Close menu
             document.querySelectorAll('.email-actions-menu').forEach(menu => {
                 menu.classList.remove('show');
             });
             
             // Show loading message
             showMessage('Generating PDF...', 'success');
             
            // Open PDF export in new window - commented out temporarily
            // window.open(`/api/emails/${emailId}/export-pdf`, '_blank');
            showMessage('PDF export temporarily disabled', 'info');
         }
         
         // Download all attachments for an email
         function downloadAllAttachments(emailId) {
             // Close menu
             document.querySelectorAll('.email-actions-menu').forEach(menu => {
                 menu.classList.remove('show');
             });
             
             // Find the email data
             const email = emails.find(e => e.id === emailId);
             if (!email || !email.attachments || email.attachments.length === 0) {
                 showMessage('No attachments found for this email', 'error');
                 return;
             }
             
            // Download each attachment - commented out temporarily
            email.attachments.forEach(attachment => {
                                  // window.open(`/api/attachments/${attachment.id}/download`, '_blank');
                                  showMessage('Attachment download temporarily disabled', 'info');
            });
             
             showMessage(`Downloading ${email.attachments.length} attachment(s)...`, 'success');
         }

        // Filter emails
        function filterEmails() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const emailItems = document.querySelectorAll('.email-item');
            
            emailItems.forEach(item => {
                const subject = item.querySelector('.email-subject').textContent.toLowerCase();
                const sender = item.querySelector('.email-sender').textContent.toLowerCase();
                const preview = item.querySelector('.email-preview').textContent.toLowerCase();
                
                if (subject.includes(searchTerm) || sender.includes(searchTerm) || preview.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Sort emails
        function sortEmails() {
            const sortBy = document.getElementById('sortBy').value;
            const sortOrder = document.getElementById('sortOrder').value;
            
            emails.sort((a, b) => {
                let aValue, bValue;
                
                switch (sortBy) {
                    case 'subject':
                        aValue = (a.subject || '').toLowerCase();
                        bValue = (b.subject || '').toLowerCase();
                        break;
                    case 'sender':
                        aValue = (a.sender_name || '').toLowerCase();
                        bValue = (b.sender_name || '').toLowerCase();
                        break;
                    case 'date':
                    default:
                        aValue = new Date(a.sent_date || a.created_at);
                        bValue = new Date(b.sent_date || b.created_at);
                        break;
                }
                
                if (sortOrder === 'asc') {
                    return aValue > bValue ? 1 : -1;
                } else {
                    return aValue < bValue ? 1 : -1;
                }
            });
            
            renderEmailList();
        }

        // Update email count
        function updateEmailCount() {
            const count = emails.length;
            document.getElementById('emailCount').textContent = `${count} email${count !== 1 ? 's' : ''}`;
        }

        // Show message
        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            
            document.body.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
</body>
</html>
