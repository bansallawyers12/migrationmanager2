<!-- Emails Interface -->
@php
    // Support both $client and $fetchedData variable names
    $clientData = $client ?? $fetchedData ?? null;
    
    // Get the matter ID from URL or most recent matter
    $matterId = null;
    if (isset($id1) && $id1 != "") {
        $clientMatter = \App\Models\ClientMatter::where('client_id', $clientData->id)
            ->where('client_unique_matter_no', $id1)
            ->first();
        $matterId = $clientMatter ? $clientMatter->id : null;
    } else {
        $clientMatter = \App\Models\ClientMatter::where('client_id', $clientData->id)
            ->where('matter_status', 1)
            ->orderBy('id', 'desc')
            ->first();
        $matterId = $clientMatter ? $clientMatter->id : null;
    }
@endphp
<div class="email-interface-container" data-client-id="{{ $clientData->id ?? '' }}" data-matter-id="{{ $matterId ?? '' }}">
    <!-- Top Control Bar (Search & Filters) -->
    <div class="email-control-bar">
        <div class="control-section search-section">
            <label for="emailSearchInput">Search:</label>
            <input type="text" id="emailSearchInput" class="search-input" placeholder="Search emails...">
        </div>
        
        <div class="control-section filter-section">
            <label for="mailTypeFilter">Type:</label>
            <select id="mailTypeFilter" class="filter-select">
                <option value="inbox">Inbox</option>
                <option value="sent">Sent</option>
            </select>
            
            <label for="labelFilter">Label:</label>
            <select id="labelFilter" class="filter-select">
                <option value="">All Labels</option>
                <!-- Populated dynamically -->
            </select>
        </div>
        
        <div class="control-section action-section">
            <button class="create-label-btn" id="createLabelBtn">
                <i class="fas fa-tag"></i> Create Label
            </button>
            <button class="apply-btn" id="applyFiltersBtn">Apply</button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="email-main-content">
        <!-- Left Email List Pane with Upload Area -->
        <div class="email-list-pane">
            <!-- Drag & Drop Upload Section -->
            <div class="upload-section-header">
                <span class="upload-title">Upload Emails</span>
            </div>
            <div class="upload-section-container">
                <div id="upload-area" class="drag-drop-zone">
                    <div class="drag-drop-content">
                        <i class="fas fa-cloud-upload-alt drag-drop-icon"></i>
                        <div class="drag-drop-text">Drag & drop .msg files here</div>
                        <div class="drag-drop-subtext">or click to browse</div>
                        <div id="file-count" class="file-count-badge">0</div>
                    </div>
                    <input type="file" id="emailFileInput" class="file-input" accept=".msg" multiple style="display: none;">
                </div>
                <div id="upload-progress" class="upload-progress">
                    <span id="fileStatus">Ready to upload</span>
                </div>
            </div>
            
            <!-- Email List Header -->
            <div class="email-list-header">
                <span class="results-count" id="resultsCount">0 results</span>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="prevBtn">Prev</button>
                    <span class="page-info" id="pageInfo">1/1</span>
                    <button class="pagination-btn" id="nextBtn">Next</button>
                </div>
            </div>
            
            <div class="email-list" id="emailList">
                <!-- Email items will be populated here by JavaScript -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="empty-state-text">
                        <h3>No emails found</h3>
                        <p>Upload .msg files above to get started.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Viewing Pane -->
        <div class="email-content-pane">
            <div class="email-content-placeholder" id="emailContentPlaceholder">
                <div class="placeholder-content">
                    <i class="fas fa-envelope-open"></i>
                    <h3>Select an email to view its contents</h3>
                </div>
            </div>
            
            <div class="email-content-view" id="emailContentView" style="display: none;">
                <!-- Email content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Label Creation Modal -->
<div id="labelModal" class="label-modal" style="display: none;">
    <div class="label-modal-content">
        <div class="label-modal-header">
            <h3 class="label-modal-title">Create New Label</h3>
            <button class="label-modal-close" id="closeLabelModal">&times;</button>
        </div>
        <div class="label-modal-body">
            <div class="label-form-group">
                <label class="label-form-label">Label Name</label>
                <input type="text" id="labelNameInput" class="label-form-input" placeholder="Enter label name">
            </div>
            <div class="label-form-group">
                <label class="label-form-label">Color</label>
                <div class="color-picker-container" id="colorPicker">
                    <div class="color-option" data-color="#3B82F6" style="background: #3B82F6;"></div>
                    <div class="color-option" data-color="#10B981" style="background: #10B981;"></div>
                    <div class="color-option" data-color="#EF4444" style="background: #EF4444;"></div>
                    <div class="color-option" data-color="#F59E0B" style="background: #F59E0B;"></div>
                    <div class="color-option" data-color="#8B5CF6" style="background: #8B5CF6;"></div>
                    <div class="color-option" data-color="#EC4899" style="background: #EC4899;"></div>
                    <div class="color-option" data-color="#14B8A6" style="background: #14B8A6;"></div>
                    <div class="color-option" data-color="#F97316" style="background: #F97316;"></div>
                </div>
                <input type="hidden" id="selectedColor" value="#3B82F6">
            </div>
            <div class="label-form-group">
                <label class="label-form-label">Icon</label>
                <div class="icon-picker-container" id="iconPicker">
                    <div class="icon-option" data-icon="fas fa-tag"><i class="fas fa-tag"></i></div>
                    <div class="icon-option" data-icon="fas fa-star"><i class="fas fa-star"></i></div>
                    <div class="icon-option" data-icon="fas fa-flag"><i class="fas fa-flag"></i></div>
                    <div class="icon-option" data-icon="fas fa-bookmark"><i class="fas fa-bookmark"></i></div>
                    <div class="icon-option" data-icon="fas fa-heart"><i class="fas fa-heart"></i></div>
                    <div class="icon-option" data-icon="fas fa-bell"><i class="fas fa-bell"></i></div>
                    <div class="icon-option" data-icon="fas fa-fire"><i class="fas fa-fire"></i></div>
                    <div class="icon-option" data-icon="fas fa-bolt"><i class="fas fa-bolt"></i></div>
                </div>
                <input type="hidden" id="selectedIcon" value="fas fa-tag">
            </div>
            <div class="label-form-actions">
                <button class="label-form-btn label-form-btn-cancel" id="cancelLabelBtn">Cancel</button>
                <button class="label-form-btn label-form-btn-create" id="saveLabelBtn">Create Label</button>
            </div>
        </div>
    </div>
</div>

<!-- Attachment Preview Modal -->
<div id="attachmentPreviewModal" class="preview-modal" style="display: none;">
    <div class="preview-modal-overlay" id="previewOverlay"></div>
    <div class="preview-modal-content">
        <div class="preview-modal-header">
            <h3 id="previewFileName">Preview</h3>
            <button class="preview-close" id="closePreviewBtn">&times;</button>
        </div>
        <div class="preview-modal-body">
            <iframe id="previewFrame" src=""></iframe>
        </div>
    </div>
</div>

<!-- Include necessary CSS and JavaScript -->
<link rel="stylesheet" href="{{ asset('css/email-handling.css') }}">
<script src="{{ asset('js/email-handling.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Emails interface loaded');
    
    // Debug: Check if elements exist
    const fileInput = document.getElementById('emailFileInput');
    const uploadArea = document.getElementById('upload-area');
    const fileStatus = document.getElementById('fileStatus');
    
    console.log('File input found:', !!fileInput);
    console.log('Upload area found:', !!uploadArea);
    console.log('File status found:', !!fileStatus);
    
    // Debug: Check if modules are available
    console.log('initializeUpload available:', typeof window.initializeUpload);
    console.log('initializeSearch available:', typeof window.initializeSearch);
    console.log('loadEmails available:', typeof window.loadEmails);
    
    // Initialize modules
    if (typeof window.initializeUpload === 'function') {
        console.log('Initializing upload module...');
        window.initializeUpload();
    } else {
        console.error('Upload module not available!');
    }
    
    if (typeof window.initializeSearch === 'function') {
        console.log('Initializing search module...');
        window.initializeSearch();
    } else {
        console.error('Search module not available!');
    }
    
    // Load emails on page load
    if (typeof window.loadEmails === 'function') {
        console.log('Loading initial emails...');
        window.loadEmails();
    } else {
        console.error('Load emails function not available!');
    }
});
</script> 