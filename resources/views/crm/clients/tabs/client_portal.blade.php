<!-- Client Portal Tab -->
<div class="tab-pane" id="application-tab">
    <div class="card full-width client-portal-container">
        <div class="portal-header">
            <h3><i class="fas fa-globe"></i> Client Portal Access</h3>
            <div class="portal-header-controls">
                <div class="portal-status-badge">
                    @if(isset($fetchedData->cp_status) && $fetchedData->cp_status == 1)
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                    @else
                        <span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactive</span>
                    @endif
                </div>
                
                <!-- Portal Toggle Switch -->
                <?php
                // Check if client has any records in client_matters table
                $client_matters_exist = DB::table('client_matters')
                    ->where('client_id', $fetchedData->id)
                    ->exists();
                ?>
                @if($client_matters_exist)
                <div class="portal-toggle-container">
                    <label class="portal-toggle-label">
                        <span class="toggle-text">Portal Access:</span>
                        <div class="toggle-switch">
                            <input type="checkbox" id="client-portal-toggle-tab" 
                                   data-client-id="{{ $fetchedData->id}}" 
                                   {{ isset($fetchedData->cp_status) && $fetchedData->cp_status == 1 ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </div>
                        <span class="portal-toggle-loader" id="portal-toggle-loader-tab" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </label>
                </div>
                @endif
            </div>
        </div>

        <div class="portal-content">
            @if(isset($fetchedData->cp_status) && $fetchedData->cp_status == 1)
                <!-- Portal is Active -->
                <?php
                // Get the selected matter based on URL parameter or latest active matter
                $selectedMatter = null;
                $matterName = '';
                $matterNumber = '';
                
                if(isset($id1) && $id1 != "") {
                    // If client unique reference id is present in URL
                    $selectedMatter = DB::table('client_matters as cm')
                        ->leftJoin('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                        ->where('cm.client_id', $fetchedData->id)
                        ->where('cm.client_unique_matter_no', $id1)
                        ->select('cm.id', 'cm.client_unique_matter_no', 'm.title', 'cm.sel_matter_id', 'cm.workflow_stage_id', 'cm.matter_status')
                        ->first();
                } else {
                    // Get the latest matter (active or inactive)
                    $selectedMatter = DB::table('client_matters as cm')
                        ->leftJoin('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                        ->where('cm.client_id', $fetchedData->id)
                        ->select('cm.id', 'cm.client_unique_matter_no', 'm.title', 'cm.sel_matter_id', 'cm.workflow_stage_id', 'cm.matter_status')
                        ->orderBy('cm.id', 'desc')
                        ->first();
                }
                
                if($selectedMatter) {
                    // Determine matter name (use "General Matter" if sel_matter_id is 1 or title is null)
                    if($selectedMatter->sel_matter_id == 1 || empty($selectedMatter->title)) {
                        $matterName = 'General Matter';
                    } else {
                        $matterName = $selectedMatter->title;
                    }
                    $matterNumber = $selectedMatter->client_unique_matter_no;
                    $currentWorkflowStageId = $selectedMatter->workflow_stage_id;
                } else {
                    $currentWorkflowStageId = null;
                }
                
                // Get all workflow stages
                $allWorkflowStages = DB::table('workflow_stages')
                    ->orderBy('id', 'asc')
                    ->get();
                ?>
                
                @if($selectedMatter)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="info-card in-progress-section">
                                <div class="in-progress-single-line">
                                    <h5 class="in-progress-title">
                                        @if($selectedMatter && isset($selectedMatter->matter_status) && $selectedMatter->matter_status == 1)
                                            Active
                                        @else
                                            In-active
                                        @endif
                                    </h5>
                                    <div class="current-stage-info">
                                        <label class="stage-label">Current Stage:</label>
                                        <div class="stage-value-container">
                                            <span class="stage-value">
                                                @if($currentWorkflowStageId)
                                                    @php
                                                        $currentStage = $allWorkflowStages->where('id', $currentWorkflowStageId)->first();
                                                    @endphp
                                                    {{ $currentStage ? $currentStage->name : 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overall-progress-container">
                                        <label class="progress-label">Overall Progress:</label>
                                        <div class="progress-circle-wrapper">
                                            @php
                                                $totalStages = $allWorkflowStages->count();
                                                $currentStageIndex = $currentWorkflowStageId ? $allWorkflowStages->where('id', '<=', $currentWorkflowStageId)->count() : 0;
                                                $progressPercentage = $totalStages > 0 ? round(($currentStageIndex / $totalStages) * 100) : 0;
                                            @endphp
                                            <div class="progress-circle" data-progress="{{ $progressPercentage }}">
                                                <svg class="progress-ring" width="80" height="80">
                                                    <circle class="progress-ring-circle-bg" cx="40" cy="40" r="36" fill="transparent" stroke="#e9ecef" stroke-width="6"/>
                                                    <circle class="progress-ring-circle" cx="40" cy="40" r="36" fill="transparent" stroke="#007bff" stroke-width="6" stroke-dasharray="{{ 2 * M_PI * 36 }}" stroke-dashoffset="{{ 2 * M_PI * 36 * (1 - $progressPercentage / 100) }}"/>
                                                </svg>
                                                <div class="progress-text">{{ $progressPercentage }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stage-navigation-buttons">
                                        @php
                                            // Check if we're at the first stage (can't go back from first stage)
                                            $isFirstStage = false;
                                            if($currentWorkflowStageId && $allWorkflowStages->count() > 0) {
                                                $firstStage = $allWorkflowStages->first();
                                                $isFirstStage = ($currentWorkflowStageId == $firstStage->id);
                                            }
                                        @endphp
                                        <button class="btn btn-outline-primary btn-sm" id="back-to-previous-stage" data-matter-id="{{ $selectedMatter->id }}" title="Back to Previous Stage" {{ $isFirstStage ? 'disabled' : '' }}>
                                            <i class="fas fa-angle-left"></i> Back to Previous Stage
                                        </button>
                                        <button class="btn btn-success btn-sm" id="proceed-to-next-stage" data-matter-id="{{ $selectedMatter->id }}" title="Proceed to Next Stage">
                                            Proceed to Next Stage <i class="fas fa-angle-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="info-card">
                            <h5>
                                <i class="fas fa-folder-open"></i> Selected Matter
                                @if($selectedMatter)
                                    - {{ $matterName }} ({{ $matterNumber }})
                                @endif
                            </h5>
                            
                            @if($selectedMatter && $allWorkflowStages->count() > 0)
                                <!-- Tabs Navigation -->
                                <div class="application-tabs-container mt-3">
                                    <ul class="application-tabs-nav" role="tablist">
                                        <li class="application-tab-item active" data-tab="activities">
                                            <a href="javascript:void(0);" class="application-tab-link">Activities</a>
                                        </li>
                                        <li class="application-tab-item" data-tab="documents">
                                            <a href="javascript:void(0);" class="application-tab-link">Documents</a>
                                        </li>
                                        <li class="application-tab-item" data-tab="messages">
                                            <a href="javascript:void(0);" class="application-tab-link">Messages</a>
                                        </li>
                                        <li class="application-tab-item" data-tab="details">
                                            <a href="javascript:void(0);" class="application-tab-link">Details</a>
                                        </li>
                                    </ul>
                                    
                                    <!-- Tab Contents -->
                                    <div class="application-tabs-content">
                                        <!-- Activities Tab (Default) -->
                                        <div class="application-tab-pane active" id="activities-tab">
                                            <div class="workflow-stages-container mt-3">
                                                <div class="workflow-stages-list">
                                                    @foreach($allWorkflowStages as $index => $stage)
                                                        @php
                                                            $isActive = ($currentWorkflowStageId && $currentWorkflowStageId == $stage->id);
                                                            $isCompleted = ($currentWorkflowStageId && $stage->id < $currentWorkflowStageId);
                                                            $isPending = (!$currentWorkflowStageId || $stage->id > $currentWorkflowStageId);
                                                            
                                                            // Determine stage class
                                                            if($isActive) {
                                                                $stageClass = 'workflow-stage-active';
                                                            } elseif($isCompleted) {
                                                                $stageClass = 'workflow-stage-completed';
                                                            } else {
                                                                $stageClass = 'workflow-stage-pending';
                                                            }
                                                        @endphp
                                                        <div class="workflow-stage-item {{ $stageClass }}">
                                                            <span class="stage-name">{{ $stage->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Documents Tab -->
                                        <div class="application-tab-pane" id="documents-tab">
                                            <?php
                                            // Get application for the selected matter
                                            $applicationData = null;
                                            $applicationId = null;
                                            if($selectedMatter) {
                                                $applicationData = DB::table('applications')
                                                    ->where('client_matter_id', $selectedMatter->id)
                                                    ->where('client_id', $fetchedData->id)
                                                    ->first();
                                                if($applicationData) {
                                                    $applicationId = $applicationData->id;
                                                }
                                            }
                                            ?>
                                            
                                            @if($selectedMatter && $applicationId && $allWorkflowStages->count() > 0)
                                                <div class="documents-checklist-container">
                                                    <div class="row">
                                                        <!-- Left Column: Stages -->
                                                        <div class="col-md-5">
                                                            <div class="stages-checklist-list">
                                                                <ul class="stages-list">
                                                                    @foreach($allWorkflowStages as $index => $stage)
                                                                        @php
                                                                            $stageNameSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stage->name)));
                                                                            $isActiveStage = ($currentWorkflowStageId && $currentWorkflowStageId == $stage->id);
                                                                            
                                                                            // Get checklists for this stage
                                                                            $stageChecklists = [];
                                                                            if($applicationId) {
                                                                                $stageChecklists = DB::table('application_document_lists')
                                                                                    ->where('application_id', $applicationId)
                                                                                    ->where('type', $stageNameSlug)
                                                                                    ->orderBy('id', 'asc')
                                                                                    ->get();
                                                                            }
                                                                        @endphp
                                                                        <li class="stage-checklist-item {{ $isActiveStage ? 'active' : '' }}" data-stage-slug="{{ $stageNameSlug }}" data-stage-name="{{ $stage->name }}">
                                                                            <div class="stage-header">
                                                                                <span class="stage-title">{{ $stage->name }}</span>
                                                                                <span class="stage-checklist-count">({{ count($stageChecklists) }})</span>
                                                                            </div>
                                                                            
                                                                            @if(count($stageChecklists) > 0)
                                                                                <div class="stage-checklists {{ $stageNameSlug }}-checklists">
                                                                                    <table class="table checklist-table">
                                                                                        <tbody>
                                                                                            @foreach($stageChecklists as $checklist)
                                                                                                @php
                                                                                                    $uploadCount = DB::table('application_documents')
                                                                                                        ->where('list_id', $checklist->id)
                                                                                                        ->count();
                                                                                                @endphp
                                                                                                <tr class="checklist-row">
                                                                                                    <td class="checklist-status">
                                                                                                        @if($uploadCount > 0)
                                                                                                            <span class="check"><i class="fa fa-check"></i></span>
                                                                                                        @else
                                                                                                            <span class="round"></span>
                                                                                                        @endif
                                                                                                    </td>
                                                                                                    <td class="checklist-name">{{ $checklist->document_type ?? 'N/A' }}</td>
                                                                                                    <td class="checklist-count">
                                                                                                        <div class="circular-box cursor-pointer">
                                                                                                            <button class="transparent-button paddingNone">{{ $uploadCount }}</button>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="checklist-action">
                                                                                                        @if($uploadCount > 1)
                                                                                                            <a href="javascript:void(0);" 
                                                                                                               class="openfileupload" 
                                                                                                               data-aid="{{ $applicationId }}" 
                                                                                                               data-type="{{ $stageNameSlug }}" 
                                                                                                               data-typename="{{ $stage->name }}" 
                                                                                                               data-id="{{ $checklist->id }}">
                                                                                                                <i class="fa fa-plus"></i>
                                                                                                            </a>
                                                                                                        @else
                                                                                                            {{-- Plus option hidden when checklist has 0 or 1 document --}}
                                                                                                        @endif
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @else
                                                                                <div class="stage-checklists {{ $stageNameSlug }}-checklists" style="display: none;">
                                                                                    <p class="no-checklists text-muted">No checklists added yet.</p>
                                                                                </div>
                                                                            @endif
                                                                            
                                                                            <a href="javascript:void(0);" 
                                                                               class="add-checklist-link openchecklist" 
                                                                               data-id="{{ $applicationId }}" 
                                                                               data-typename="{{ $stage->name }}" 
                                                                               data-type="{{ $stageNameSlug }}">
                                                                                <i class="fa fa-plus"></i> Add New Checklist
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Right Column: Checklist Details -->
                                                        <div class="col-md-7">
                                                            <div class="checklist-details-panel">
                                                                <h5 class="panel-title">Checklist Details</h5>
                                                                <div class="table-responsive">
                                                                    <table class="table text_wrap checklist-details-table">
                                                                        <thead>
                                                                            <tr>
                                                                                    <th style="white-space: normal; line-height: 1.4;">
                                                                                        <div>Checklist</div>
                                                                                        <div>Filename</div>
                                                                                    </th>
                                                                                    <th>Stage</th>
                                                                                    <th style="white-space: normal; line-height: 1.4;">
                                                                                        <div>Added By</div>
                                                                                        <div>Added At</div>
                                                                                    </th>
                                                                                    <th>Status</th>
                                                                                    <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="checklist-details-tbody">
                                                                            @if($applicationId)
                                                                                @php
                                                                                    $allDocuments = DB::table('application_documents')
                                                                                        ->where('application_id', $applicationId)
                                                                                        ->orderBy('created_at', 'DESC')
                                                                                        ->get();
                                                                                @endphp
                                                                                @foreach($allDocuments as $document)
                                                                                    @php
                                                                                        $docList = DB::table('application_document_lists')
                                                                                            ->where('id', $document->list_id)
                                                                                            ->first();
                                                                                        $addedBy = DB::table('admins')
                                                                                            ->where('id', $document->user_id)
                                                                                            ->first();
                                                                                        
                                                                                        // Get status and format display text
                                                                                        $status = isset($document->status) ? (int)$document->status : 0;
                                                                                        $statusText = 'InProgress';
                                                                                        $statusClass = 'warning';
                                                                                        $rejectionReason = '';
                                                                                        if($status == 1) {
                                                                                            $statusText = 'Approved';
                                                                                            $statusClass = 'success';
                                                                                        } elseif($status == 2) {
                                                                                            $statusText = 'Rejected';
                                                                                            $statusClass = 'danger';
                                                                                            // Get rejection reason for tooltip
                                                                                            $rejectionReason = isset($document->doc_rejection_reason) ? $document->doc_rejection_reason : (isset($document->reject_reason) ? $document->reject_reason : '');
                                                                                        }
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td>
                                                                                            <div style="margin-bottom: 5px;">
                                                                                                <strong>{{ $docList->document_type ?? 'N/A' }}</strong>
                                                                                            </div>
                                                                                            @if($document->file_name)
                                                                                                <div>
                                                                                                    <a href="{{ $document->myfile ?? '#' }}" target="_blank" style="color: #007bff; text-decoration: none; cursor: pointer;" title="Click to view document">
                                                                                                        <i class="fa fa-file"></i> {{ $document->file_name }}
                                                                                                    </a>
                                                                                                </div>
                                                                                            @else
                                                                                                <div>
                                                                                                    <small class="text-muted">No file uploaded</small>
                                                                                                </div>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td style="white-space: normal; word-wrap: break-word;">{{ $document->typename ?? 'N/A' }}</td>
                                                                                        <td>
                                                                                            <div style="margin-bottom: 5px;">
                                                                                                <div class="user-info">
                                                                                                    <span class="user-avatar">{{ substr($addedBy->first_name ?? 'N', 0, 1) }}</span>
                                                                                                    <span class="user-name">{{ $addedBy->first_name ?? 'N/A' }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div>
                                                                                                <small>{{ date('d/m/Y', strtotime($document->created_at)) }}</small>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td style="white-space: normal; word-wrap: break-word;">
                                                                                            @if($status == 0)
                                                                                                <span class="badge badge-warning">InProgress</span>
                                                                                            @elseif($status == 1)
                                                                                                <span class="badge badge-success">Approved</span>
                                                                                            @elseif($status == 2)
                                                                                                <span class="badge badge-danger rejected-status-badge" 
                                                                                                      data-toggle="tooltip" 
                                                                                                      data-placement="top" 
                                                                                                      title="{{ $rejectionReason ? htmlspecialchars($rejectionReason, ENT_QUOTES) : 'No rejection reason provided' }}"
                                                                                                      style="cursor: help;">
                                                                                                    Rejected
                                                                                                </span>
                                                                                            @else
                                                                                                <span class="badge badge-warning">InProgress</span>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td style="white-space: nowrap; text-align: center; position: relative;">
                                                                                            <div class="action-buttons">
                                                                                                <div class="action-row">
                                                                                                    <!-- Download -->
                                                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary download-document-btn" data-document-id="{{ $document->id }}" data-file-url="{{ $document->myfile ?? '#' }}" data-file-name="{{ $document->file_name ?? 'document' }}" title="Download">
                                                                                                        <i class="fa fa-download"></i>
                                                                                                    </a>
                                                                                                    <!-- Delete -->
                                                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-document-by-list" data-list-id="{{ $document->list_id }}" data-document-id="{{ $document->id }}" title="Delete">
                                                                                                        <i class="fa fa-trash"></i>
                                                                                                    </a>
                                                                                                </div>
                                                                                                
                                                                                                @if($status == 0)
                                                                                                    <div class="action-row">
                                                                                                        <!-- Approve Document -->
                                                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-success approve-document-btn" data-document-id="{{ $document->id }}" title="Approve Document">
                                                                                                            <i class="fa fa-check-circle"></i>
                                                                                                        </a>
                                                                                                        <!-- Reject Document -->
                                                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning reject-document-btn" data-document-id="{{ $document->id }}" title="Reject Document">
                                                                                                            <i class="fa fa-times-circle"></i>
                                                                                                        </a>
                                                                                                    </div>
                                                                                                @elseif($status == 1)
                                                                                                    <div class="action-row">
                                                                                                        <!-- Approve Document - Hidden for status 1 -->
                                                                                                        <span style="width: 32px; display: inline-block;"></span>
                                                                                                        <!-- Reject Document -->
                                                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning reject-document-btn" data-document-id="{{ $document->id }}" title="Reject Document">
                                                                                                            <i class="fa fa-times-circle"></i>
                                                                                                        </a>
                                                                                                    </div>
                                                                                                @elseif($status == 2)
                                                                                                    <div class="action-row">
                                                                                                        <!-- Approve Document -->
                                                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-success approve-document-btn" data-document-id="{{ $document->id }}" title="Approve Document">
                                                                                                            <i class="fa fa-check-circle"></i>
                                                                                                        </a>
                                                                                                        <!-- Reject Document - Hidden for status 2 -->
                                                                                                        <span style="width: 32px; display: inline-block;"></span>
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                @if(count($allDocuments) == 0)
                                                                                    <tr>
                                                                                        <td colspan="5" class="text-center text-muted">No documents uploaded yet.</td>
                                                                                    </tr>
                                                                                @endif
                                                                            @else
                                                                                <tr>
                                                                                    <td colspan="5" class="text-center text-muted">No application found for this matter.</td>
                                                                                </tr>
                                                                            @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="tab-content-placeholder">
                                                    <p class="text-muted">
                                                        @if(!$selectedMatter)
                                                            Please select a matter to view documents.
                                                        @elseif(!$applicationId)
                                                            No application found for this matter. Please create an application first.
                                                        @else
                                                            No workflow stages available.
                                                        @endif
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Messages Tab -->
                                        <div class="application-tab-pane" id="messages-tab">
                                            <div class="whatsapp-chat-container" id="whatsapp-chat-container">
                                                <div class="messages-loading" id="messages-loading" style="display: none;">
                                                    <div class="loading-spinner"></div>
                                                    <p>Loading messages...</p>
                                                </div>
                                                <div class="messages-empty" id="messages-empty" style="display: none;">
                                                    <p class="text-muted">No messages yet. Start a conversation!</p>
                                                </div>
                                                <div class="whatsapp-chat-messages" id="whatsapp-chat-messages">
                                                    <!-- Messages will be inserted here -->
                                                </div>
                                                <div class="whatsapp-chat-input-container" id="whatsapp-chat-input-container">
                                                    <div class="chat-input-wrapper">
                                                        <textarea id="message-input" class="message-input" placeholder="Type a message..." rows="1"></textarea>
                                                        <button id="send-message-btn" class="send-message-btn" title="Send message">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Details Tab -->
                                        <div class="application-tab-pane" id="details-tab">
                                            <div class="details-container" id="details-container">
                                                <div class="details-loading" id="details-loading" style="display: none;">
                                                    <div class="loading-spinner"></div>
                                                    <p>Loading details...</p>
                                                </div>
                                                <div class="details-content" id="details-content">
                                                    <?php
                                                    // Get client ID
                                                    $clientId = $fetchedData->id;
                                                    
                                                    // Start with values from admins table
                                                    $basicInfo = [
                                                        'first_name' => $fetchedData->first_name ?? null,
                                                        'last_name' => $fetchedData->last_name ?? null,
                                                        'client_id' => $fetchedData->client_id ?? null,
                                                        'dob' => $fetchedData->dob ?? null,
                                                        'age' => $fetchedData->age ?? null,
                                                        'gender' => $fetchedData->gender ?? null,
                                                        'marital_status' => $fetchedData->marital_status ?? null,
                                                    ];
                                                    
                                                    // Track audit entries for each field
                                                    $auditEntries = [];
                                                    
                                                    // Get latest audit values for basic fields and override admins table values
                                                    $basicFields = ['first_name', 'last_name', 'client_id', 'dob', 'age', 'gender', 'marital_status'];
                                                    
                                                    foreach ($basicFields as $field) {
                                                        // Get the latest audit entry for this field
                                                        $latestAudit = \App\Models\ClientPortalDetailAudit::where('client_id', $clientId)
                                                            ->where('meta_key', $field)
                                                            ->orderBy('updated_at', 'desc')
                                                            ->first();
                                                        
                                                        if ($latestAudit && $latestAudit->new_value !== null) {
                                                            // Check if audit value is different from admins table value
                                                            $adminValue = $basicInfo[$field];
                                                            $auditValue = $latestAudit->new_value;
                                                            
                                                            // For dob, compare dates
                                                            if ($field === 'dob') {
                                                                $adminValueStr = $adminValue ? date('Y-m-d', strtotime($adminValue)) : null;
                                                                $auditValueStr = $auditValue ? date('Y-m-d', strtotime($auditValue)) : null;
                                                                $hasChange = ($adminValueStr !== $auditValueStr);
                                                            } else {
                                                                $hasChange = ((string)$adminValue !== (string)$auditValue);
                                                            }
                                                            
                                                            if ($hasChange) {
                                                                // Store audit entry info
                                                                $auditEntries[$field] = [
                                                                    'id' => $latestAudit->id,
                                                                    'new_value' => $auditValue,
                                                                    'old_value' => $latestAudit->old_value
                                                                ];
                                                                
                                                                // Override with audit value
                                                                if ($field === 'dob') {
                                                                    $basicInfo['dob'] = $auditValue;
                                                                } else {
                                                                    $basicInfo[$field] = $auditValue;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Format date of birth
                                                    $dateOfBirth = 'Not set';
                                                    if ($basicInfo['dob']) {
                                                        try {
                                                            $dateOfBirth = date('d/m/Y', strtotime($basicInfo['dob']));
                                                        } catch(\Exception $e) {
                                                            $dateOfBirth = 'Not set';
                                                        }
                                                    }
                                                    
                                                    // Calculate age from date of birth
                                                    $age = 'Not calculated';
                                                    if ($basicInfo['dob']) {
                                                        try {
                                                            $dob = new \DateTime($basicInfo['dob']);
                                                            $now = new \DateTime();
                                                            $diff = $now->diff($dob);
                                                            $age = $diff->y . ' years ' . $diff->m . ' months';
                                                        } catch(\Exception $e) {
                                                            // If calculation fails, use stored age value
                                                            $age = $basicInfo['age'] ?? 'Not calculated';
                                                        }
                                                    } elseif ($basicInfo['age']) {
                                                        $age = $basicInfo['age'];
                                                    }
                                                    
                                                    // Build full name
                                                    $fullName = trim(($basicInfo['first_name'] ?? '') . ' ' . ($basicInfo['last_name'] ?? ''));
                                                    if(empty($fullName)) {
                                                        $fullName = $fetchedData->name ?? 'N/A';
                                                    }
                                                    
                                                    // Get display values
                                                    $displayClientId = $basicInfo['client_id'] ?? 'N/A';
                                                    $displayMaritalStatus = $basicInfo['marital_status'] ?? 'Not set';
                                                    $displayGender = $basicInfo['gender'] ?? 'Not set';
                                                    
                                                    // Field labels for messages
                                                    $fieldLabels = [
                                                        'first_name' => 'First Name',
                                                        'last_name' => 'Last Name',
                                                        'client_id' => 'Client ID',
                                                        'dob' => 'Date of Birth',
                                                        'age' => 'Age',
                                                        'gender' => 'Gender',
                                                        'marital_status' => 'Marital Status'
                                                    ];
                                                    ?>
                                                    
                                                    <!-- Basic Information Section -->
                                                    <div class="details-section-card">
                                                        <div class="details-section-header">
                                                            <h5><i class="fas fa-user"></i> Basic Information</h5>
                                                        </div>
                                                        <div class="summary-view">
                                                            <div class="summary-grid">
                                                                <div class="summary-item">
                                                                    <span class="summary-label">FIRST NAME:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['first_name']) ? 'audit-value' : '' }}">
                                                                        {{ $basicInfo['first_name'] ?? 'Not set' }}
                                                                        @if(isset($auditEntries['first_name']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['first_name']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['first_name']['id'] }}, 'first_name', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['first_name']['id'] }}, 'first_name', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="summary-item">
                                                                    <span class="summary-label">LAST NAME:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['last_name']) ? 'audit-value' : '' }}">
                                                                        {{ $basicInfo['last_name'] ?? 'Not set' }}
                                                                        @if(isset($auditEntries['last_name']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['last_name']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['last_name']['id'] }}, 'last_name', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['last_name']['id'] }}, 'last_name', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="summary-item">
                                                                    <span class="summary-label">CLIENT ID:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['client_id']) ? 'audit-value' : '' }}">
                                                                        {{ $displayClientId }}
                                                                        @if(isset($auditEntries['client_id']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['client_id']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['client_id']['id'] }}, 'client_id', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['client_id']['id'] }}, 'client_id', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="summary-item">
                                                                    <span class="summary-label">MARITAL STATUS:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['marital_status']) ? 'audit-value' : '' }}">
                                                                        {{ $displayMaritalStatus }}
                                                                        @if(isset($auditEntries['marital_status']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['marital_status']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['marital_status']['id'] }}, 'marital_status', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['marital_status']['id'] }}, 'marital_status', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="summary-item">
                                                                    <span class="summary-label">DATE OF BIRTH:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['dob']) ? 'audit-value' : '' }}">
                                                                        {{ $dateOfBirth }}
                                                                        @if(isset($auditEntries['dob']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['dob']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['dob']['id'] }}, 'dob', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['dob']['id'] }}, 'dob', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="summary-item">
                                                                    <span class="summary-label">AGE:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['age']) ? 'audit-value' : '' }}">
                                                                        {{ $age }}
                                                                        @if(isset($auditEntries['age']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['age']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['age']['id'] }}, 'age', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['age']['id'] }}, 'age', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="summary-item">
                                                                    <span class="summary-label">GENDER:</span>
                                                                    <span class="summary-value {{ isset($auditEntries['gender']) ? 'audit-value' : '' }}">
                                                                        {{ $displayGender }}
                                                                        @if(isset($auditEntries['gender']))
                                                                            <span class="audit-badge" title="Pending approval">
                                                                                <i class="fas fa-clock"></i>
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    @if(isset($auditEntries['gender']))
                                                                        <div class="audit-actions">
                                                                            <button type="button" class="btn-approve" onclick="approveAuditValue({{ $auditEntries['gender']['id'] }}, 'gender', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Approve">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                            <button type="button" class="btn-reject" onclick="rejectAuditValue({{ $auditEntries['gender']['id'] }}, 'gender', {{ $clientId }}, {{ $selectedMatter ? $selectedMatter->id : 'null' }})" title="Reject">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @else
                <!-- Portal is Inactive -->
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Client portal is currently inactive. Use the toggle in the sidebar to activate it.
                </div>

                <div class="info-card">
                    <h5><i class="fas fa-info-circle"></i> About Client Portal</h5>
                    <p>The client portal allows clients to:</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check text-success"></i> View their case status and progress</li>
                        <li><i class="fas fa-check text-success"></i> Access and download documents</li>
                        <li><i class="fas fa-check text-success"></i> Upload required documents</li>
                        <li><i class="fas fa-check text-success"></i> View appointments and deadlines</li>
                        <li><i class="fas fa-check text-success"></i> Communicate via secure messaging</li>
                        <li><i class="fas fa-check text-success"></i> View invoices and payment history</li>
                        <li><i class="fas fa-check text-success"></i> Update their profile information</li>
                    </ul>

                    <?php
                    // Check if client has required information for portal activation
                    $hasEmail = !empty($fetchedData->email);
                    $hasMatters = DB::table('client_matters')
                        ->where('client_id', $fetchedData->id)
                        ->where('matter_status', 1)
                        ->exists();
                    ?>

                    @if(!$hasEmail || !$hasMatters)
                        <div class="alert alert-danger mt-3">
                            <h6><i class="fas fa-exclamation-circle"></i> Portal Activation Requirements:</h6>
                            <ul class="mb-0">
                                @if(!$hasEmail)
                                    <li><i class="fas fa-times text-danger"></i> Client email address is required</li>
                                @endif
                                @if(!$hasMatters)
                                    <li><i class="fas fa-times text-danger"></i> At least one active matter is required</li>
                                @endif
                            </ul>
                            <p class="mt-2 mb-0"><strong>Please complete these requirements before activating the portal.</strong></p>
                        </div>
                    @else
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle"></i> All requirements met. You can activate the portal using the toggle in the sidebar.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.client-portal-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 0;
}

.portal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.portal-header-controls {
    display: flex;
    align-items: center;
    gap: 20px;
}

.portal-toggle-container {
    display: flex;
    align-items: center;
}

.portal-toggle-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    cursor: pointer;
}

.portal-toggle-loader {
    margin-left: 8px;
    color: white;
    font-size: 14px;
}

.portal-toggle-loader i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.toggle-text {
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.3);
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #28a745;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.portal-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.portal-status-badge .badge {
    font-size: 0.95rem;
    padding: 8px 15px;
}

.portal-content {
    padding: 25px;
}

.info-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.info-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.info-card h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
}

.credential-item {
    margin-bottom: 15px;
}

.credential-item label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    display: block;
}

.credential-value {
    display: flex;
    align-items: center;
    gap: 10px;
}

.credential-value span {
    flex: 1;
    padding: 8px 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.copy-btn {
    padding: 6px 12px;
    border-radius: 4px;
    transition: all 0.2s;
}

.copy-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.credential-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
}

.feature-list {
    list-style: none;
    padding-left: 0;
}

.feature-list li {
    padding: 8px 0;
    font-size: 1rem;
}

.feature-list li i {
    margin-right: 10px;
}

.matter-list {
    list-style: none;
    padding-left: 0;
}

.matter-list li {
    padding: 10px 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 8px;
}

.selected-matter-display {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.matter-info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
}

.matter-info-item label {
    font-weight: 600;
    color: #495057;
    min-width: 140px;
    margin: 0;
}

.matter-value {
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 500;
    flex: 1;
}

.workflow-stages-container {
    margin-top: 20px;
}

.workflow-stages-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.workflow-stage-item {
    padding: 12px 16px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    cursor: default;
}

.workflow-stage-item .stage-name {
    font-size: 0.95rem;
    font-weight: 500;
    color: #495057;
}

.workflow-stage-completed {
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.workflow-stage-completed .stage-name {
    color: #155724;
}

.workflow-stage-active {
    background-color: #cfe2ff;
    border-color: #9ec5fe;
}

.workflow-stage-active .stage-name {
    color: #084298;
    font-weight: 600;
}

.workflow-stage-pending {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.workflow-stage-pending .stage-name {
    color: #6c757d;
}

/* Application Tabs Styles */
.application-tabs-container {
    margin-top: 20px;
}

.application-tabs-nav {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-bottom: 2px solid #e9ecef;
    gap: 0;
}

.application-tab-item {
    margin: 0;
    padding: 0;
}

.application-tab-link {
    display: block;
    padding: 12px 24px;
    text-decoration: none;
    color: #495057;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
    background: transparent;
}

.application-tab-item.active .application-tab-link {
    color: #9333ea;
    border-bottom-color: #9333ea;
    background: rgba(147, 51, 234, 0.05);
}

.application-tab-link:hover {
    color: #9333ea;
    background: rgba(147, 51, 234, 0.05);
}

.application-tabs-content {
    position: relative;
    min-height: 200px;
}

.application-tab-pane {
    display: none;
    padding: 20px 0;
    animation: fadeIn 0.3s ease;
}

.application-tab-pane.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tab-content-placeholder {
    padding: 40px 20px;
    text-align: center;
    color: #6c757d;
}

/* WhatsApp Style Messages */
.whatsapp-chat-container {
    height: 600px;
    background: #e5ddd5;
    background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,0,0,.03) 10px, rgba(0,0,0,.03) 20px);
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
}

.whatsapp-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
    min-height: 0;
}

.whatsapp-message {
    display: flex;
    margin-bottom: 4px;
    animation: messageSlideIn 0.3s ease;
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-sent {
    justify-content: flex-end;
}

.message-received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 65%;
    padding: 8px 12px;
    border-radius: 7.5px;
    position: relative;
    word-wrap: break-word;
    box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
}

.message-sent .message-bubble {
    background-color: #dcf8c6;
    border-bottom-right-radius: 2px;
}

.message-received .message-bubble {
    background-color: #ffffff;
    border-bottom-left-radius: 2px;
}

.message-sender-info {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.message-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    flex-shrink: 0;
}

.sender-name {
    font-size: 13px;
    font-weight: 600;
    color: #667eea;
}

.message-content {
    font-size: 14px;
    line-height: 1.4;
    color: #303030;
    white-space: pre-wrap;
}

.message-timestamp {
    font-size: 11px;
    color: #667781;
    margin-top: 4px;
    text-align: right;
    padding-top: 2px;
}

.message-sent .message-timestamp {
    text-align: right;
}

.message-received .message-timestamp {
    text-align: left;
}

.messages-loading {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 40px;
    color: #667781;
    z-index: 20;
    background: rgba(229, 221, 213, 0.95);
    border-radius: 8px;
    width: auto;
    min-width: 200px;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5ddd5;
    border-top-color: #25d366;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.messages-empty {
    display: none;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #667781;
    font-size: 14px;
    z-index: 10;
    pointer-events: none;
    width: 100%;
    text-align: center;
}

/* Chat Input Area */
.whatsapp-chat-input-container {
    background: #f0f0f0;
    border-top: 1px solid #ddd;
    padding: 10px;
    flex-shrink: 0;
}

.chat-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    background: #fff;
    border-radius: 21px;
    padding: 8px 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.message-input {
    flex: 1;
    border: none;
    outline: none;
    resize: none;
    font-size: 14px;
    font-family: inherit;
    max-height: 100px;
    overflow-y: auto;
    padding: 4px 0;
}

.send-message-btn {
    background: #25d366;
    color: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.2s;
}

.send-message-btn:hover {
    background: #20ba5a;
}

.send-message-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Details Tab Styles */
.details-container {
    position: relative;
    min-height: 400px;
}

.details-loading {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 40px;
    color: #667781;
    z-index: 20;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 8px;
    width: auto;
    min-width: 200px;
}

.details-content {
    padding: 10px 0;
}

.details-section {
    margin-top: 15px;
}

.detail-item {
    margin-bottom: 15px;
}

.detail-label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.detail-value {
    display: block;
    color: #6c757d;
    font-size: 0.95rem;
    padding: 8px 0;
}

/* Details Section Card Styles */
.details-section-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.details-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.details-section-header h5 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.details-section-header h5 i {
    color: #667eea;
}

/* Summary View Styles (matching edit form design) */
.summary-view {
    margin-top: 10px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    min-width: 140px;
    display: inline-block;
}

.summary-value {
    color: #212529;
    font-weight: 500;
    font-size: 0.95rem;
    flex: 1;
}

/* Verify Button Styles */
.btn-verify-details {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 0.85rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: background 0.2s;
    margin-left: auto;
}

.btn-verify-details:hover {
    background: #0056b3;
}

.btn-verify-details i {
    font-size: 0.8rem;
}

/* Verified Badge Styles */
.verified-badge {
    background: #28a745;
    color: white;
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-left: auto;
}

.verified-badge i {
    font-size: 0.8rem;
}

/* Audit Value Styles */
.audit-value {
    position: relative;
    background-color: #fff3cd;
    padding: 4px 8px;
    border-radius: 4px;
    border-left: 3px solid #ffc107;
}

.audit-badge {
    display: inline-flex;
    align-items: center;
    margin-left: 8px;
    color: #ffc107;
    font-size: 0.85rem;
}

.audit-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
    margin-top: 5px;
}

.btn-approve {
    background: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 0.9rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    width: 36px;
    height: 36px;
    min-width: 36px;
}

.btn-approve:hover {
    background: #218838;
}

.btn-approve i {
    font-size: 1rem;
}

.btn-reject {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 0.9rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    width: 36px;
    height: 36px;
    min-width: 36px;
}

.btn-reject:hover {
    background: #c82333;
}

.btn-reject i {
    font-size: 1rem;
}

.summary-item {
    flex-wrap: wrap;
}

.summary-item .summary-value {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
}

/* Scrollbar styling for messages */
.whatsapp-chat-messages::-webkit-scrollbar {
    width: 6px;
}

.whatsapp-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.whatsapp-chat-messages::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 3px;
}

.whatsapp-chat-messages::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
}

/* Documents Tab Styles */
.documents-checklist-container {
    padding: 20px 0;
}

.stages-checklist-list {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    max-height: 600px;
    overflow-y: auto;
}

.stages-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.stage-checklist-item {
    margin-bottom: 15px;
    padding: 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.stage-checklist-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stage-checklist-item.active {
    border-color: #9333ea;
    background: rgba(147, 51, 234, 0.05);
}

.stage-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    font-weight: 600;
    color: #2c3e50;
}

.stage-title {
    flex: 1;
}

.stage-checklist-count {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: normal;
}

.stage-checklists {
    margin: 10px 0;
}

.checklist-table {
    width: 100%;
    margin: 0;
    font-size: 0.9rem;
}

.checklist-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
}

.checklist-table tbody tr:last-child {
    border-bottom: none;
}

.checklist-row td {
    padding: 8px 4px;
    vertical-align: middle;
}

.checklist-status {
    width: 30px;
    text-align: center;
}

.checklist-name {
    flex: 1;
    font-weight: 500;
}

.checklist-count {
    width: 50px;
    text-align: center;
}

.checklist-action {
    width: 40px;
    text-align: center;
}

.checklist-action a {
    color: #9333ea;
    text-decoration: none;
    font-size: 1rem;
}

.checklist-action a:hover {
    color: #7c3aed;
}

.add-checklist-link {
    display: inline-block;
    margin-top: 10px;
    color: #9333ea;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
}

.add-checklist-link:hover {
    color: #7c3aed;
    text-decoration: none;
}

.add-checklist-link i {
    margin-right: 5px;
}

.no-checklists {
    padding: 10px;
    font-size: 0.85rem;
    text-align: center;
}

/* Checklist Details Panel */
.checklist-details-panel {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    max-height: 600px;
    overflow-y: auto;
}

.panel-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.checklist-details-panel .table-responsive {
    overflow-x: hidden;
    overflow-y: hidden;
    max-width: 100%;
    position: relative;
    z-index: 1;
}

/* Allow dropdown menus to extend beyond container */
.checklist-details-panel .table-responsive .dropdown-menu {
    position: absolute !important;
    z-index: 9999 !important;
    max-height: none !important;
    height: auto !important;
    overflow: visible !important;
    overflow-y: visible !important;
    overflow-x: visible !important;
}

.checklist-details-panel .table-responsive .dropdown.show {
    z-index: 9999 !important;
}

.checklist-details-panel .table-responsive .dropdown.show .dropdown-menu {
    z-index: 9999 !important;
    display: block !important;
}

/* Prevent scrollbar when hovering over dropdown button */
.checklist-details-panel .table-responsive:has(.dropdown.show) {
    overflow-y: hidden !important;
}

.checklist-details-panel .table-responsive .table {
    margin-bottom: 0;
}

.checklist-details-table {
    background: white;
    border-radius: 6px;
    width: 100%;
    table-layout: fixed;
    max-width: 100%;
}

.checklist-details-table thead {
    background: #f8f9fa;
}

.checklist-details-table thead th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 8px 10px;
    vertical-align: middle;
    font-size: 0.9rem;
}

.checklist-details-table thead th div {
    line-height: 1.4;
}

.checklist-details-table thead th:nth-child(1) {
    width: 28%;
}

.checklist-details-table thead th:nth-child(2) {
    width: 15%;
}

.checklist-details-table thead th:nth-child(3) {
    width: 20%;
}

.checklist-details-table thead th:nth-child(4) {
    width: 22%;
}

.checklist-details-table thead th:nth-child(5) {
    width: 15%;
}

.checklist-details-table tbody td {
    padding: 8px 10px;
    vertical-align: top;
    word-wrap: break-word;
    font-size: 0.9rem;
}

.checklist-details-table tbody td:nth-child(4) {
    white-space: normal;
    word-break: break-word;
}

.checklist-details-table tbody td:nth-child(4) .badge {
    white-space: normal;
    display: inline-block;
    max-width: 100%;
    word-wrap: break-word;
    text-align: center;
    font-size: 0.85rem;
    padding: 4px 8px;
}

/* Tooltip styling for rejected status */
.rejected-status-badge {
    cursor: help;
    position: relative;
}

.rejected-status-badge:hover {
    opacity: 0.9;
}

/* Custom tooltip styling for rejection reason */
.tooltip {
    z-index: 1051;
}

.tooltip-inner {
    max-width: 300px;
    word-wrap: break-word;
    white-space: normal;
    text-align: left;
    padding: 8px 12px;
    font-size: 0.875rem;
    line-height: 1.4;
}

.checklist-details-table tbody td:nth-child(5) {
    white-space: nowrap;
    text-align: center;
    position: relative;
    overflow: visible !important;
}

/* Action buttons container */
.checklist-details-table tbody td:nth-child(5) .action-buttons {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.checklist-details-table tbody td:nth-child(5) .action-buttons .action-row {
    display: flex;
    gap: 6px;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.checklist-details-table tbody td:nth-child(5) .action-buttons .btn {
    padding: 4px 8px;
    min-width: 32px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1;
    margin: 0;
    flex: 0 0 32px;
}

.checklist-details-table tbody td:nth-child(5) .action-buttons .btn i {
    margin: 0;
    font-size: 14px;
}

.checklist-details-table tbody td:nth-child(5) .action-buttons .btn:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
    cursor: pointer;
}

.checklist-details-table tbody td:nth-child(5) .action-buttons .btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Action dropdown menu styling */
.checklist-details-table tbody td:nth-child(5) .dropdown-menu,
.checklist-details-table .action-dropdown-menu {
    position: absolute !important;
    right: 0 !important;
    left: auto !important;
    transform: none !important;
    top: 100% !important;
    bottom: auto !important;
    margin-top: 5px !important;
    margin-bottom: 0 !important;
    min-width: 170px !important;
    max-width: 250px !important;
    width: auto !important;
    z-index: 9999 !important;
    will-change: transform;
    max-height: none !important;
    height: auto !important;
    overflow: visible !important;
    overflow-y: visible !important;
    overflow-x: visible !important;
    padding: 0.25rem 0 !important;
    display: none;
    visibility: visible !important;
    opacity: 1 !important;
}

/* When dropdown is shown - Bootstrap adds .show class */
.checklist-details-table tbody td:nth-child(5) .dropdown.show .dropdown-menu,
.checklist-details-table .dropdown.show .action-dropdown-menu,
.checklist-details-table tbody td:nth-child(5) .dropdown-menu.show,
.checklist-details-table .action-dropdown-menu.show {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    max-height: none !important;
    height: auto !important;
    overflow: visible !important;
    overflow-y: visible !important;
    overflow-x: visible !important;
    transform: none !important;
    pointer-events: auto !important;
}

/* Ensure all dropdown items are visible */
.checklist-details-table tbody td:nth-child(5) .dropdown-menu .dropdown-item,
.checklist-details-table .action-dropdown-menu .dropdown-item {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    white-space: nowrap !important;
    padding: 0.5rem 1rem !important;
    overflow: visible !important;
    height: auto !important;
    min-height: 2.25rem !important;
    line-height: 1.5 !important;
    width: 100% !important;
}

.checklist-details-table tbody td:nth-child(5) .dropdown-menu .dropdown-divider,
.checklist-details-table .action-dropdown-menu .dropdown-divider {
    margin: 0.5rem 0 !important;
    height: 1px !important;
    display: block !important;
    visibility: visible !important;
}

/* Ensure dropdown items are always visible */
.checklist-details-table tbody td:nth-child(5) .dropdown.show .dropdown-menu .dropdown-item {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    height: auto !important;
    min-height: 2.5rem !important;
    line-height: 1.5 !important;
}

/* Fix for preventing scrollbar when dropdown opens */
.checklist-details-panel {
    overflow: visible !important;
}

.checklist-details-table {
    overflow: visible !important;
}

.checklist-details-table tbody {
    overflow: visible !important;
}

.checklist-details-table tbody tr {
    overflow: visible !important;
    position: relative;
}

.checklist-details-table tbody td {
    overflow: visible !important;
    position: relative;
}

/* Ensure dropdown menu doesn't trigger scrollbar */
.checklist-details-table tbody td:nth-child(5) .dropdown.show .dropdown-menu {
    position: absolute;
}

/* Prevent overflow scrollbar when dropdown menu is open */
.checklist-details-panel .table-responsive .dropdown.show ~ .dropdown-menu {
    position: absolute;
}

/* Ensure dropdown doesn't trigger scrollbar */
.checklist-details-table tbody td:nth-child(5) {
    overflow: visible !important;
}

.checklist-details-table tbody td:nth-child(5) .dropdown {
    overflow: visible !important;
}

/* Ensure text wraps properly in all columns */
.checklist-details-table tbody td:nth-child(1),
.checklist-details-table tbody td:nth-child(2),
.checklist-details-table tbody td:nth-child(3) {
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Make long filenames break */
.checklist-details-table tbody td:nth-child(1) a {
    word-break: break-all;
    display: inline-block;
    max-width: 100%;
}

/* Responsive adjustments */
@media (max-width: 1400px) {
    .checklist-details-table thead th {
        padding: 6px 8px;
        font-size: 0.85rem;
    }
    
    .checklist-details-table tbody td {
        padding: 6px 8px;
        font-size: 0.85rem;
    }
    
    .checklist-details-table tbody td:nth-child(4) .badge {
        font-size: 0.75rem;
        padding: 3px 6px;
    }
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-avatar {
    width: 24px;
    height: 24px;
    line-height: 24px;
    text-align: center;
    background: #03a9f4;
    color: white;
    border-radius: 50%;
    font-size: 0.8rem;
    font-weight: 600;
    flex-shrink: 0;
}

.user-name {
    font-weight: 500;
    color: #2c3e50;
}

/* Checklist status indicators */
.checklist .round {
    background: #fff;
    border: 1px solid #000;
    border-radius: 50%;
    font-size: 10px;
    line-height: 14px;
    padding: 2px 5px;
    width: 16px;
    height: 16px;
    display: inline-block;
}

.checklist span.check, .checklist-details-table span.check {
    background: #71cc53;
    color: #fff;
    border-radius: 50%;
    font-size: 10px;
    line-height: 14px;
    padding: 2px 3px;
    width: 18px;
    height: 18px;
    display: inline-block;
}

.circular-box {
    height: 24px;
    width: 24px;
    line-height: 24px;
    display: inline-block;
    text-align: center;
    box-shadow: 0 4px 6px 0 rgb(34 36 38 / 12%), 0 2px 12px 0 rgb(34 36 38 / 15%);
    background: #fff;
    border: 1px solid #d2d2d2;
    border-radius: 50%;
}

.transparent-button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    width: 100%;
    height: 100%;
    padding: 0;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Modal Opacity Fix for Create Checklist Modal */
#create_checklist.modal {
    opacity: 1 !important;
    z-index: 1060 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    padding-top: 70px !important; /* Account for page header height */
    /* DO NOT use pointer-events: none on modal container - it blocks all interactions */
}

#create_checklist.modal.show {
    display: block !important;
    opacity: 1 !important;
}

#create_checklist.modal:not(.show) {
    display: none !important;
    opacity: 0 !important;
}

#create_checklist.modal.fade {
    opacity: 0;
    transition: opacity 0.15s linear;
}

#create_checklist.modal.fade.show {
    opacity: 1 !important;
}

#create_checklist .modal-dialog {
    opacity: 1 !important;
    transform: translate(0, 0) !important;
    transition: transform 0.3s ease-out;
    z-index: 1061 !important;
    position: relative !important;
    pointer-events: auto !important;
    margin: 1.75rem auto !important;
    max-height: calc(100vh - 70px - 3.5rem) !important; /* Account for header and margin */
    overflow-y: auto !important;
}

#create_checklist.modal.show .modal-dialog {
    opacity: 1 !important;
    transform: translate(0, 0) !important;
}

#create_checklist .modal-content {
    opacity: 1 !important;
    background-color: #fff !important;
    color: #212529 !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    pointer-events: auto !important;
    position: relative !important;
    z-index: 1 !important;
}

#create_checklist .modal-header {
    opacity: 1 !important;
    background-color: #fff !important;
    color: #212529 !important;
    position: relative !important;
    z-index: 2 !important;
}

#create_checklist .modal-body {
    opacity: 1 !important;
    background-color: #fff !important;
    color: #212529 !important;
    pointer-events: auto !important;
}

#create_checklist .modal-body input,
#create_checklist .modal-body textarea,
#create_checklist .modal-body select,
#create_checklist .modal-body button {
    pointer-events: auto !important;
    opacity: 1 !important;
}

#create_checklist_submit_btn {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 1 !important;
    position: relative !important;
}

#create_checklist_submit_btn:hover {
    opacity: 0.9 !important;
}

#create_checklist_submit_btn:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
}

/* Ensure backdrop doesn't block modal interactions */
.modal-backdrop {
    z-index: 1059 !important;
}

#create_checklist.modal.show {
    z-index: 1060 !important;
}

#create_checklist .modal-dialog {
    pointer-events: auto !important;
    z-index: 1061 !important;
}

/* Ensure backdrop has proper opacity for create_checklist modal - set to 0.1 */
.modal-backdrop.create-checklist-backdrop,
.modal-backdrop.create-checklist-backdrop.show {
    opacity: 0.1 !important;
    background-color: rgba(0, 0, 0, 0.1) !important;
    z-index: 1059 !important;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.2s;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.feature-item i {
    font-size: 1.5rem;
}

.feature-item span {
    font-weight: 500;
    color: #2c3e50;
}

.text-purple {
    color: #9333ea !important;
}

.in-progress-section {
    padding: 20px;
}

.in-progress-single-line {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: space-between;
}

.in-progress-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    flex-shrink: 0;
}

.in-progress-actions {
    display: flex !important;
    gap: 10px;
    align-items: center;
    visibility: visible !important;
    opacity: 1 !important;
    flex-shrink: 0;
}

.in-progress-actions .btn {
    white-space: nowrap;
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.stage-navigation-buttons {
    display: flex !important;
    flex-direction: column;
    gap: 10px;
    align-items: flex-end;
    flex-shrink: 0;
    visibility: visible !important;
    opacity: 1 !important;
}

.stage-navigation-buttons .btn {
    white-space: nowrap;
    width: 100%;
    min-width: 180px;
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

#back-to-previous-stage {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 10 !important;
    width: 100% !important;
    min-width: 180px !important;
}

/* Override any global styles that might hide the button */
.stage-navigation-buttons #back-to-previous-stage,
.in-progress-section #back-to-previous-stage,
.info-card #back-to-previous-stage {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.current-stage-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.stage-label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    text-align: center;
}

.stage-value-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.stage-value {
    font-size: 1rem;
    color: #28a745;
    font-weight: 500;
    text-align: center;
}

.overall-progress-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.progress-label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    text-align: center;
}

.progress-circle-wrapper {
    position: relative;
}

.progress-circle {
    position: relative;
    width: 80px;
    height: 80px;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dashoffset 0.35s;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.9rem;
    font-weight: 600;
    color: #007bff;
}


@media (max-width: 768px) {
    .in-progress-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .in-progress-actions {
        width: 100%;
        flex-wrap: wrap;
    }
    
    .in-progress-details {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
                                          
<?php
 if(isset($id1) && $id1 != "") {
                    // If client unique reference id is present in URL
                    $selectedMatter = DB::table('client_matters as cm')
                        ->leftJoin('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                        ->where('cm.client_id', $fetchedData->id)
                        ->where('cm.client_unique_matter_no', $id1)
                        ->select('cm.id', 'cm.client_unique_matter_no', 'm.title', 'cm.sel_matter_id', 'cm.workflow_stage_id', 'cm.matter_status')
                        ->first();
                } else {
                    // Get the latest matter (active or inactive)
                    $selectedMatter = DB::table('client_matters as cm')
                        ->leftJoin('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                        ->where('cm.client_id', $fetchedData->id)
                        ->select('cm.id', 'cm.client_unique_matter_no', 'm.title', 'cm.sel_matter_id', 'cm.workflow_stage_id', 'cm.matter_status')
                        ->orderBy('cm.id', 'desc')
                        ->first();
                }
?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Portal toggle functionality (both sidebar and tab toggles)
    function handlePortalToggle(toggleElement) {
        const clientId = toggleElement.getAttribute('data-client-id');
        const isChecked = toggleElement.checked;
        const statusValue = isChecked ? 1 : 0;
        
        // Show loading state
        toggleElement.disabled = true;
        
        // Show loader based on which toggle was clicked
        const toggleId = toggleElement.id;
        let loaderElement = null;
        
        if (toggleId === 'client-portal-toggle-tab') {
            loaderElement = document.getElementById('portal-toggle-loader-tab');
        } else if (toggleId === 'client-portal-toggle') {
            loaderElement = document.getElementById('portal-toggle-loader-sidebar');
        }
        
        if (loaderElement) {
            loaderElement.style.display = 'inline-block';
        }
        
        fetch('{{ route("clients.toggleClientPortal") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                client_id: clientId,
                status: statusValue
            })
        })
        .then(response => response.json())
        .then(data => {
            toggleElement.disabled = false;
            
            // Hide loader
            if (loaderElement) {
                loaderElement.style.display = 'none';
            }
            
            if (data.success) {
                // Update both toggles to stay in sync
                const sidebarToggle = document.getElementById('client-portal-toggle');
                const tabToggle = document.getElementById('client-portal-toggle-tab');
                
                if (sidebarToggle) sidebarToggle.checked = !!statusValue;
                if (tabToggle) tabToggle.checked = !!statusValue;
                
                // Show success message
                alert(data.message);
                
                // Reload the page to update the tab content
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Revert toggle state on error
                toggleElement.checked = !isChecked;
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toggleElement.disabled = false;
            toggleElement.checked = !isChecked;
            
            // Hide loader on error
            if (loaderElement) {
                loaderElement.style.display = 'none';
            }
            
            alert('Error updating portal status. Please try again.');
        });
    }

    // Handle sidebar toggle
    const sidebarToggle = document.getElementById('client-portal-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('change', function() {
            handlePortalToggle(this);
        });
    }

    // Handle tab toggle
    const tabToggle = document.getElementById('client-portal-toggle-tab');
    if (tabToggle) {
        tabToggle.addEventListener('change', function() {
            handlePortalToggle(this);
        });
    }
    
    // Back to Previous Stage button handler
    function ensureBackButtonVisible() {
        const backStageBtn = document.getElementById('back-to-previous-stage');
        if (backStageBtn) {
            // Force button to be visible - override any conflicting styles
            backStageBtn.style.setProperty('display', 'inline-block', 'important');
            backStageBtn.style.setProperty('visibility', 'visible', 'important');
            backStageBtn.style.setProperty('opacity', '1', 'important');
            backStageBtn.style.setProperty('position', 'relative', 'important');
            backStageBtn.style.setProperty('z-index', '10', 'important');
            
            // Also ensure parent container is visible
            const parentContainer = backStageBtn.closest('.stage-navigation-buttons');
            if (parentContainer) {
                parentContainer.style.setProperty('display', 'flex', 'important');
                parentContainer.style.setProperty('visibility', 'visible', 'important');
                parentContainer.style.setProperty('opacity', '1', 'important');
            }
        }
    }
    
    // Ensure button is visible immediately and after a short delay
    ensureBackButtonVisible();
    setTimeout(ensureBackButtonVisible, 100);
    setTimeout(ensureBackButtonVisible, 500);
    
    const backStageBtn = document.getElementById('back-to-previous-stage');
    if (backStageBtn) {
        backStageBtn.addEventListener('click', function() {
            const matterId = this.getAttribute('data-matter-id');
            if (confirm('Are you sure you want to move back to the previous stage?')) {
                // TODO: Implement API call to move to previous stage
                alert('Back to previous stage functionality will be implemented');
            }
        });
    }
    
    // Proceed to Next Stage button handler
    const nextStageBtn = document.getElementById('proceed-to-next-stage');
    if (nextStageBtn) {
        nextStageBtn.addEventListener('click', function() {
            const matterId = this.getAttribute('data-matter-id');
            if (!matterId) {
                alert('Error: Matter ID not found');
                return;
            }
            
            if (confirm('Are you sure you want to proceed to the next stage?')) {
                // Disable button to prevent double clicks
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Make AJAX call to update stage
                fetch('{{ route("clients.matter.update-next-stage") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        matter_id: matterId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        // Success - reload the page to show updated stage
                        alert(data.message || 'Matter has been successfully moved to the next stage.');
                        window.location.reload();
                    } else {
                        // Error - show message and re-enable button
                        alert(data.message || 'Failed to move to next stage. Please try again.');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        
                        // If already at last stage, disable the button
                        if (data.is_last_stage) {
                            btn.disabled = true;
                            btn.classList.add('disabled');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the stage. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            }
        });
    }
    
    // Application Tabs Switching Functionality
    const applicationTabItems = document.querySelectorAll('.application-tab-item');
    const applicationTabPanes = document.querySelectorAll('.application-tab-pane');
  
    
    
    // Store client matter ID and user info for messages
    const clientMatterId = @json(($selectedMatter && isset($selectedMatter->id)) ? $selectedMatter->id : null);
    const currentUserId = @json(Auth::guard('admin')->id() ?? null);
    // Use Reverb configuration (compatible with Pusher protocol)
    const pusherAppKey = '{{ config("broadcasting.connections.reverb.key") ?: config("broadcasting.connections.pusher.key") }}';
    const pusherCluster = '{{ config("broadcasting.connections.reverb.options.cluster") ?: config("broadcasting.connections.pusher.options.cluster", "ap2") }}';
    const reverbHost = '{{ config("broadcasting.connections.reverb.options.host", "127.0.0.1") }}';
    const reverbPort = {{ config("broadcasting.connections.reverb.options.port", 8080) }};
    const reverbScheme = '{{ config("broadcasting.connections.reverb.options.scheme", "http") }}';
    
    // Debug logging
    console.log('Messages Tab Initialization:', {
        clientMatterId: clientMatterId,
        currentUserId: currentUserId,
        pusherAppKey: pusherAppKey ? 'Set' : 'Missing',
        pusherCluster: pusherCluster,
        selectedMatterExists: @json($selectedMatter ? true : false)
    });
    
    // Pusher variables
    let pusher = null;
    let subscribedChannel = null;
    let messagesLoaded = false;
    
    applicationTabItems.forEach(function(tabItem) {
        const tabLink = tabItem.querySelector('.application-tab-link');
        if (tabLink) {
            tabLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetTab = tabItem.getAttribute('data-tab');
                
                // Remove active class from all tabs and panes
                applicationTabItems.forEach(function(item) {
                    item.classList.remove('active');
                });
                applicationTabPanes.forEach(function(pane) {
                    pane.classList.remove('active');
                });
                
                // Add active class to clicked tab
                tabItem.classList.add('active');
                
                // Show corresponding tab pane
                const targetPane = document.getElementById(targetTab + '-tab');
                if (targetPane) {
                    targetPane.classList.add('active');
                    
                    // Initialize messages if Messages tab is clicked
                    if (targetTab === 'messages') {
                        console.log('Messages tab clicked', {
                            clientMatterId: clientMatterId,
                            currentUserId: currentUserId
                        });
                        
                        if (clientMatterId && currentUserId) {
                            initializeMessages();
                        } else {
                            const emptyDiv = document.getElementById('messages-empty');
                            if (emptyDiv) {
                                emptyDiv.style.display = 'block';
                                if (!clientMatterId) {
                                    emptyDiv.innerHTML = '<p class="text-muted">Please select a matter to view messages.</p>';
                                } else {
                                    emptyDiv.innerHTML = '<p class="text-muted">No messages yet. Start a conversation!</p>';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    
    // Check if Messages tab is already active on page load
    setTimeout(function() {
        const messagesTab = document.getElementById('messages-tab');
        const messagesTabLink = document.querySelector('.application-tab-item[data-tab="messages"]');
        
        // Check if Messages tab is active (either by class or by checking which tab link has active class)
        const isMessagesTabActive = (messagesTab && messagesTab.classList.contains('active')) || 
                                    (messagesTabLink && messagesTabLink.classList.contains('active'));
        
        if (isMessagesTabActive && clientMatterId && currentUserId) {
            console.log('Messages tab is active on page load, initializing...');
            initializeMessages();
        } else if (!clientMatterId) {
            console.warn('Cannot load messages: clientMatterId is missing');
            const emptyDiv = document.getElementById('messages-empty');
            if (emptyDiv) {
                emptyDiv.style.display = 'block';
                emptyDiv.innerHTML = '<p class="text-muted">Please select a matter to view messages.</p>';
            }
        } else if (!currentUserId) {
            console.warn('Cannot load messages: currentUserId is missing');
        } else {
            console.log('Messages tab not active on page load', {
                isMessagesTabActive: isMessagesTabActive,
                clientMatterId: clientMatterId,
                currentUserId: currentUserId
            });
        }
    }, 300); // Small delay to ensure DOM is fully loaded
    
    // Initialize Messages with Pusher
    function initializeMessages() {
        if (messagesLoaded) {
            console.log('Messages already loaded, skipping initialization');
            return;
        }
        
        if (!clientMatterId) {
            console.warn('Cannot initialize messages: clientMatterId is missing');
            return;
        }
        
        if (!currentUserId) {
            console.warn('Cannot initialize messages: currentUserId is missing');
            return;
        }
        
        console.log('Initializing messages...');
        messagesLoaded = true;
        
        // Load existing messages first
        loadMessages();
        
        // Then connect to Pusher for real-time updates (non-blocking)
        setTimeout(() => {
            connectToPusher();
        }, 500);
    }
    
    // Load existing messages from database
    async function loadMessages() {
        const messagesContainer = document.getElementById('whatsapp-chat-messages');
        const loadingDiv = document.getElementById('messages-loading');
        const emptyDiv = document.getElementById('messages-empty');
        
        if (!messagesContainer) {
            console.error('Messages container not found');
            return;
        }
        
        if (!clientMatterId) {
            console.warn('Client Matter ID is missing. Cannot load messages.');
            if (emptyDiv) {
                emptyDiv.style.display = 'block';
                emptyDiv.innerHTML = '<p class="text-muted">Please select a matter to view messages.</p>';
            }
            return;
        }
        
        console.log('Loading messages for client_matter_id:', clientMatterId);
        
        if (loadingDiv) loadingDiv.style.display = 'block';
        if (emptyDiv) emptyDiv.style.display = 'none';
        
        try {
            const url = `{{ URL::to('/clients/matter-messages') }}?client_matter_id=${clientMatterId}`;
            console.log('Fetching messages from:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Messages data received:', data);
            
            if (loadingDiv) loadingDiv.style.display = 'none';
            
            // Check if response is successful and has messages
            if (data.success !== false && data.data && data.data.messages && Array.isArray(data.data.messages)) {
                if (data.data.messages.length > 0) {
                    console.log(`Displaying ${data.data.messages.length} messages`);
                    if (emptyDiv) emptyDiv.style.display = 'none';
                    displayMessages(data.data.messages);
                } else {
                    console.log('No messages found for this matter');
                    if (emptyDiv) {
                        emptyDiv.style.display = 'block';
                        emptyDiv.innerHTML = '<p class="text-muted">No messages yet. Start a conversation!</p>';
                    }
                }
            } else if (data.success === false) {
                console.error('API returned error:', data.message || data.error);
                if (emptyDiv) {
                    emptyDiv.style.display = 'block';
                    emptyDiv.innerHTML = '<p class="text-danger">Error: ' + (data.message || 'Failed to load messages') + '</p>';
                }
            } else {
                console.warn('Unexpected response format:', data);
                if (emptyDiv) {
                    emptyDiv.style.display = 'block';
                    emptyDiv.innerHTML = '<p class="text-muted">No messages yet. Start a conversation!</p>';
                }
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            if (loadingDiv) loadingDiv.style.display = 'none';
            if (emptyDiv) {
                emptyDiv.style.display = 'block';
                emptyDiv.innerHTML = '<p class="text-danger">Error loading messages. Please try again.</p>';
            }
        }
    }
    
    // Connect to Pusher for real-time messaging
    function connectToPusher() {
        if (!pusherAppKey || !currentUserId) {
            console.warn('Pusher configuration missing - real-time updates will not work', {
                pusherAppKey: !!pusherAppKey,
                currentUserId: !!currentUserId
            });
            return;
        }
        
        console.log('Connecting to Pusher...');
        
        // Load Pusher JS if not already loaded
        if (typeof Pusher === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pusher/7.2.0/pusher.min.js';
            script.onload = function() {
                console.log('Pusher JS loaded, initializing...');
                initializePusher();
            };
            script.onerror = function() {
                console.error('Failed to load Pusher JS library');
            };
            document.head.appendChild(script);
        } else {
            initializePusher();
        }
    }
    
    function initializePusher() {
        try {
            // Check if using Reverb (local WebSocket server) or Pusher Cloud
            const isReverb = reverbHost && reverbPort && reverbScheme;
            
            const pusherConfig = {
                cluster: pusherCluster,
                forceTLS: reverbScheme === 'https',
                encrypted: reverbScheme === 'https',
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                }
            };
            
            // If using Reverb, configure custom host and port
            if (isReverb) {
                pusherConfig.wsHost = reverbHost;
                pusherConfig.wsPort = reverbPort;
                pusherConfig.wssPort = reverbPort;
                pusherConfig.disableStats = true;
                pusherConfig.enabledTransports = ['ws', 'wss'];
                console.log('🔌 Configuring for Laravel Reverb:', { host: reverbHost, port: reverbPort, scheme: reverbScheme });
            }
            
            pusher = new Pusher(pusherAppKey, pusherConfig);
            
            pusher.connection.bind('connected', () => {
                console.log('✅ Connected to ' + (isReverb ? 'Laravel Reverb' : 'Pusher'));
                subscribeToChannel();
            });
            
            pusher.connection.bind('error', (error) => {
                console.error('Pusher error:', error);
            });
            
        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
        }
    }
    
    function subscribeToChannel() {
        if (!pusher || !currentUserId) return;
        
        const channelName = `private-user.${currentUserId}`;
        
        try {
            subscribedChannel = pusher.subscribe(channelName);
            
            subscribedChannel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ Subscribed to channel:', channelName);
            });
            
            // Listen for new messages
            subscribedChannel.bind('message.sent', (data) => {
                console.log('📨 New message received:', data);
                if (data.message && data.message.client_matter_id == clientMatterId) {
                    addMessageToDisplay(data.message, data.message.sender_id == currentUserId);
                }
            });
            
            // Listen for unread count updates
            subscribedChannel.bind('unread.count.updated', (data) => {
                console.log('📊 Unread count updated:', data);
            });
            
        } catch (error) {
            console.error('Failed to subscribe:', error);
        }
    }
    
    // Display messages in WhatsApp style
    function displayMessages(messages) {
        const messagesContainer = document.getElementById('whatsapp-chat-messages');
        if (!messagesContainer) return;
        
        messagesContainer.innerHTML = '';
        
        messages.forEach(function(message) {
            const isSent = message.is_sent;
            addMessageToDisplay(message, isSent, false);
        });
        
        scrollToBottom();
    }
    
    // Add a single message to display
    function addMessageToDisplay(message, isSent, animate = true) {
        const messagesContainer = document.getElementById('whatsapp-chat-messages');
        const emptyDiv = document.getElementById('messages-empty');
        
        if (!messagesContainer) return;
        
        if (emptyDiv) emptyDiv.style.display = 'none';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `whatsapp-message ${isSent ? 'message-sent' : 'message-received'}`;
        if (!animate) {
            messageDiv.style.animation = 'none';
        }
        
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        // Sender info for received messages
        if (!isSent && message.sender) {
            const senderInfo = document.createElement('div');
            senderInfo.className = 'message-sender-info';
            
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.textContent = message.sender_initials || (message.sender.first_name ? message.sender.first_name.charAt(0).toUpperCase() : 'U');
            
            const senderName = document.createElement('span');
            senderName.className = 'sender-name';
            senderName.textContent = message.sender_name || (message.sender.full_name || (message.sender.first_name + ' ' + (message.sender.last_name || ''))) || 'Unknown';
            
            senderInfo.appendChild(avatar);
            senderInfo.appendChild(senderName);
            messageBubble.appendChild(senderInfo);
        }
        
        // Message content
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = message.message || '';
        messageBubble.appendChild(messageContent);
        
        // Timestamp
        const timestamp = document.createElement('div');
        timestamp.className = 'message-timestamp';
        if (message.sent_at || message.created_at) {
            const dateStr = message.sent_at || message.created_at;
            const date = new Date(dateStr);
            if (!isNaN(date.getTime())) {
                timestamp.textContent = formatMessageTime(date);
            }
        }
        messageBubble.appendChild(timestamp);
        
        messageDiv.appendChild(messageBubble);
        messagesContainer.appendChild(messageDiv);
        
        scrollToBottom();
    }
    
    // Format message time
    function formatMessageTime(date) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const messageDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        
        const hours = date.getHours();
        const minutes = date.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
        
        if (messageDate.getTime() === today.getTime()) {
            return `${displayHours}:${displayMinutes} ${ampm}`;
        } else {
            const day = date.getDate();
            const month = date.toLocaleString('default', { month: 'short' });
            return `${day} ${month}, ${displayHours}:${displayMinutes} ${ampm}`;
        }
    }
    
    // Scroll to bottom
    function scrollToBottom() {
        const messagesContainer = document.getElementById('whatsapp-chat-messages');
        if (messagesContainer) {
            setTimeout(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 100);
        }
    }
    
    // Send message functionality
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-message-btn');
    
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }
    
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });
    }
    
    async function sendMessage() {
        const input = document.getElementById('message-input');
        const messageText = input?.value.trim();
        
        if (!messageText || !clientMatterId) return;
        
        if (sendBtn) sendBtn.disabled = true;
        
        try {
            const response = await fetch('{{ route("clients.send-message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    message: messageText,
                    client_matter_id: clientMatterId
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                input.value = '';
                input.style.height = 'auto';
                
                // Immediately add the message to the UI (optimistic update) - no page refresh needed
                if (data.data && data.data.message) {
                    const messageData = data.data.message;
                    // Format message for display
                    const formattedMessage = {
                        id: messageData.id || data.data.message_id,
                        message: messageData.message || messageText,
                        sender: messageData.sender || messageData.sender_name || 'You',
                        sender_id: messageData.sender_id || currentUserId,
                        sender_shortname: messageData.sender_initials || messageData.sender_shortname || 'AD',
                        sent_at: messageData.sent_at || messageData.created_at || new Date().toISOString(),
                        client_matter_id: messageData.client_matter_id || clientMatterId,
                        is_sent: true
                    };
                    addMessageToDisplay(formattedMessage, true, true);
                    scrollToBottom();
                    console.log('✅ Message added to UI immediately');
                }
                // Message will also appear via Reverb/Pusher event (for other users and as backup)
            } else {
                alert('Failed to send message: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Error sending message. Please try again.');
        } finally {
            if (sendBtn) sendBtn.disabled = false;
        }
    }

});
</script>

<!-- Create Checklist Modal -->
<div class="modal fade custom_modal" id="create_checklist" tabindex="-1" role="dialog" aria-labelledby="createChecklistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createChecklistModalLabel">Add New Checklist</h5>
                <button type="button" class="close" id="create_checklist_close_btn" data-dismiss="modal" aria-label="Close" onclick="closeCreateChecklistModal(); return false;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{URL::to('/add-checklists')}}" name="create_checklist_form" id="create_checklist_form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="app_id" id="checklistapp_id" value="">
                    <input type="hidden" name="client_id" value="{{ $fetchedData->id }}">
                    <input type="hidden" name="type" id="checklist_type" value="">
                    <input type="hidden" name="typename" id="checklist_typename" value="">
                    
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="document_type">Checklist Name <span class="span_req">*</span></label>
                                <input type="text" name="document_type" id="document_type" class="form-control" data-valid="required" placeholder="Enter checklist name">
                                <span class="custom-error document_type_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter description (optional)"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="allow_upload_docu" value="1" checked> Allow clients to upload documents from client portal
                                </label>
                            </div>
                        </div>
                        
                        {{-- <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="proceed_next_stage" value="1"> Make Mandatory (Proceed to Next Stage)
                                </label>
                            </div>
                        </div> --}}
                        
                        {{-- <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="due_date" id="due_date_check" value="1"> Set Due Date
                                </label>
                            </div>
                        </div> --}}
                        
                        <div class="col-12 col-md-6 col-lg-6" id="appoint_date_container" style="display: none;">
                            <div class="form-group">
                                <label for="appoint_date">Due Date</label>
                                <input type="date" name="appoint_date" id="appoint_date" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-lg-6" id="appoint_time_container" style="display: none;">
                            <div class="form-group">
                                <label for="appoint_time">Due Time</label>
                                <input type="time" name="appoint_time" id="appoint_time" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-12 col-lg-12">
                            <button type="button" id="create_checklist_submit_btn" class="btn btn-primary">Add Checklist</button>
                            <button type="button" class="btn btn-secondary" id="create_checklist_close_btn_footer" data-dismiss="modal" onclick="closeCreateChecklistModal(); return false;">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Function to close the modal (global scope)
function closeCreateChecklistModal() {
    var modal = $('#create_checklist');
    if (!modal.length) return false;
    
    // Try Bootstrap modal hide first
    if (typeof $.fn.modal !== 'undefined' && typeof modal.modal === 'function') {
        try {
            modal.modal('hide');
            return false;
        } catch(e) {
            console.log('Bootstrap modal hide error:', e);
        }
    }
    
    // Fallback: Direct DOM manipulation
    modal.removeClass('show').addClass('fade');
    modal.css('display', 'none');
    
    // Remove backdrop
    $('.modal-backdrop').remove();
    
    // Reset body styles
    $('body').removeClass('modal-open');
    $('body').css({
        'overflow': '',
        'padding-right': ''
    });
    
    return false;
}

// Toggle due date fields
$(document).ready(function() {
    // Handle opening checklist modal - ensure hidden fields are populated
    $(document).on('click', '.openchecklist', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var typename = $(this).attr('data-typename');
        
        // Validate that required data attributes are present
        if (!id || !type || !typename) {
            console.error('Missing required data attributes for checklist modal:', {
                id: id,
                type: type,
                typename: typename
            });
            alert('Error: Missing required information. Please try again.');
            return false;
        }
        
        // Populate hidden fields
        $('#create_checklist #checklistapp_id').val(id);
        $('#create_checklist #checklist_type').val(type);
        $('#create_checklist #checklist_typename').val(typename);
        
        // Ensure button is enabled
        $('#create_checklist_submit_btn').prop('disabled', false);
        
        // Clear any previous errors
        $('#create_checklist_form').find('.custom-error').remove();
        
        // Show modal
        $('#create_checklist').modal('show');
        
        return false;
    });
    
    // Set backdrop opacity to 0.1 when create_checklist modal is shown
    $('#create_checklist').on('show.bs.modal', function() {
        setTimeout(function() {
            $('.modal-backdrop').css({'position': 'relative' });

            $('.modal-backdrop').addClass('create-checklist-backdrop').css({
                'opacity': '0.1',
                'background-color': 'rgba(0, 0, 0, 0.1)'
            });
        }, 10);
        
        // Ensure button is enabled when modal is shown
        $('#create_checklist_submit_btn').prop('disabled', false);
        // Clear any previous errors
        $('.custom-error').remove();
    });
    
    // Remove the class and reset form when modal is hidden
    $('#create_checklist').on('hidden.bs.modal', function() {
        $('.modal-backdrop').removeClass('create-checklist-backdrop');
        $('#create_checklist_form')[0].reset();
        $('#appoint_date_container, #appoint_time_container').hide();
        $('.custom-error').remove();
        // Re-enable button in case it was disabled
        $('#create_checklist_submit_btn').prop('disabled', false);
    });
    
    $('#due_date_check').on('change', function() {
        if ($(this).is(':checked')) {
            $('#appoint_date_container, #appoint_time_container').show();
        } else {
            $('#appoint_date_container, #appoint_time_container').hide();
        }
    });
    
    // Explicit close button handlers for create_checklist modal
    $(document).on('click', '#create_checklist_close_btn, #create_checklist_close_btn_footer', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeCreateChecklistModal();
    });
    
    // Also handle generic close buttons and data-dismiss
    $(document).on('click', '#create_checklist .close, #create_checklist [data-dismiss="modal"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeCreateChecklistModal();
    });
    
    // Vanilla JavaScript fallback
    document.addEventListener('DOMContentLoaded', function() {
        var closeBtn = document.getElementById('create_checklist_close_btn');
        var closeBtnFooter = document.getElementById('create_checklist_close_btn_footer');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeCreateChecklistModal();
            });
        }
        
        if (closeBtnFooter) {
            closeBtnFooter.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeCreateChecklistModal();
            });
        }
    });
    
    // Handle Add Checklist button click
    $(document).on('click', '#create_checklist_submit_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Add Checklist button clicked');
        
        var form = $('#create_checklist_form');
        
        if (!form.length) {
            console.error('Form not found!');
            alert('Form not found. Please refresh the page.');
            return false;
        }
        
        // Check if button is disabled (prevent double submission)
        if ($(this).prop('disabled')) {
            console.log('Button is disabled, ignoring click');
            return false;
        }
        
        var isValid = true;
        
        // Clear previous errors
        form.find('.custom-error').remove();
        
        // Validate hidden required fields first
        var applicationId = $.trim(form.find('#checklistapp_id').val());
        var checklistType = $.trim(form.find('#checklist_type').val());
        var checklistTypename = $.trim(form.find('#checklist_typename').val());
        
        if (!applicationId) {
            isValid = false;
            alert('Error: Application ID is missing. Please close and reopen the checklist modal.');
            console.error('Application ID (checklistapp_id) is missing');
            return false;
        }
        
        if (!checklistType) {
            isValid = false;
            alert('Error: Checklist type is missing. Please close and reopen the checklist modal.');
            console.error('Checklist type (checklist_type) is missing');
            return false;
        }
        
        if (!checklistTypename) {
            isValid = false;
            alert('Error: Checklist type name is missing. Please close and reopen the checklist modal.');
            console.error('Checklist type name (checklist_typename) is missing');
            return false;
        }
        
        // Validate Checklist Name field specifically (document_type)
        var checklistName = $.trim($('#document_type').val());
        if (!checklistName) {
            isValid = false;
            $('#document_type').after('<span class="custom-error" role="alert" style="color: red; display: block; margin-top: 5px;"><strong>Checklist Name is required.</strong></span>');
        }
        
        // Validate required fields
        form.find(':input[data-valid]').each(function() {
            var dataValidation = $(this).attr('data-valid');
            if (dataValidation && dataValidation.indexOf('required') !== -1) {
                if (!$.trim($(this).val())) {
                    isValid = false;
                    // Only add error if not already added for document_type
                    if ($(this).attr('id') !== 'document_type') {
                        $(this).after('<span class="custom-error" role="alert" style="color: red; display: block; margin-top: 5px;"><strong>This field is required.</strong></span>');
                    }
                }
            }
        });
        
        if (!isValid) {
            // Scroll to the first error field
            var firstError = form.find('.custom-error').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.parent().offset().top - 100
                }, 'slow');
                // Focus on the first error field
                firstError.parent().find('input, textarea, select').first().focus();
            } else {
                $('html, body').animate({scrollTop: $('#create_checklist').offset().top - 100}, 'slow');
            }
            return false;
        }
        
        // Disable button to prevent double submission
        var submitBtn = $(this);
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        
        // Store current active tab before submission
        var currentActiveTab = $('.application-tab-item.active').attr('data-tab') || 'documents';
        
        // Variables checklistType, checklistTypename, and applicationId are already defined above in validation section
        
        // Submit via AJAX
        var formData = new FormData(form[0]);
        
        // Ensure checkboxes are properly included (even if unchecked)
        var allowUpload = form.find('input[name="allow_upload_docu"]').is(':checked') ? '1' : '0';
        var proceedNext = form.find('input[name="proceed_next_stage"]').is(':checked') ? '1' : '0';
        var dueDate = form.find('input[name="due_date"]').is(':checked') ? '1' : '0';
        
        formData.set('allow_upload_docu', allowUpload);
        formData.set('proceed_next_stage', proceedNext);
        formData.set('due_date', dueDate);
        
        // Debug: Log form data
        console.log('Submitting checklist form:', {
            app_id: applicationId,
            client_id: form.find('input[name="client_id"]').val(),
            type: checklistType,
            typename: checklistTypename,
            document_type: form.find('#document_type').val(),
            allow_upload_docu: allowUpload,
            proceed_next_stage: proceedNext,
            due_date: dueDate
        });
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                
                var responseData = typeof response === 'string' ? JSON.parse(response) : response;
                if (responseData.status || responseData.success) {
                    // Close modal
                    $('#create_checklist').modal('hide');
                    
                    // Show success message
                    if (typeof iziToast !== 'undefined') {
                        iziToast.success({
                            title: 'Success',
                            message: responseData.message || 'Checklist added successfully',
                            position: 'topRight'
                        });
                    } else {
                        alert(responseData.message || 'Checklist added successfully');
                    }
                    
                    // Update checklist list dynamically without page reload
                    if (checklistType && responseData.data) {
                        // Find the stage checklist item for this type
                        var stageItem = $('.stage-checklist-item[data-stage-slug="' + checklistType + '"]');
                        
                        if (stageItem.length) {
                            // Extract tbody content from response data (controller returns full table)
                            var $responseTable = $(responseData.data);
                            var tbodyContent = $responseTable.find('tbody').html() || '';
                            
                            // Find or create the stage checklist container
                            var stageChecklistContainer = stageItem.find('.stage-checklists.' + checklistType + '-checklists');
                            
                            if (stageChecklistContainer.length) {
                                // Container exists, update the table
                                var checklistTable = stageChecklistContainer.find('table.checklist-table, table.table');
                                if (checklistTable.length) {
                                    // Update tbody content
                                    checklistTable.find('tbody').html(tbodyContent);
                                } else {
                                    // Table doesn't exist, create it
                                    stageChecklistContainer.html('<table class="table checklist-table"><tbody>' + tbodyContent + '</tbody></table>');
                                }
                                // Show the container
                                stageChecklistContainer.show();
                            } else {
                                // Container doesn't exist, create it (first checklist for this stage)
                                var checklistHtml = '<div class="stage-checklists ' + checklistType + '-checklists">' +
                                    '<table class="table checklist-table">' +
                                    '<tbody>' + tbodyContent + '</tbody>' +
                                    '</table>' +
                                    '</div>';
                                
                                // Insert before the "Add New Checklist" link
                                stageItem.find('.add-checklist-link').before(checklistHtml);
                            }
                            
                            // Update checklist count in stage header
                            if (tbodyContent) {
                                var checklistCount = $responseTable.find('tbody tr').length || $(tbodyContent).filter('tr').length || ($(tbodyContent).length > 0 ? 1 : 0);
                                stageItem.find('.stage-checklist-count').text('(' + checklistCount + ')');
                            }
                        }
                    }
                    
                    // Always ensure Documents tab is active after adding checklist
                    // (since checklist is added from Documents tab)
                    $('.application-tab-item').removeClass('active');
                    $('.application-tab-pane').removeClass('active');
                    
                    // Add active class to Documents tab
                    $('.application-tab-item[data-tab="documents"]').addClass('active');
                    $('#documents-tab').addClass('active');
                    
                    // Scroll to Documents tab if needed (smooth scroll)
                    var documentsTab = $('#documents-tab');
                    if (documentsTab.length) {
                        $('html, body').animate({
                            scrollTop: documentsTab.offset().top - 100
                        }, 300);
                    }
                } else {
                    alert('Error: ' + (responseData.message || 'Failed to add checklist'));
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                
                var errorMsg = 'An error occurred. Please try again.';
                var validationErrors = {};
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    // Handle Laravel validation errors
                    if (xhr.responseJSON.errors) {
                        validationErrors = xhr.responseJSON.errors;
                        // Clear previous errors
                        form.find('.custom-error').remove();
                        // Display validation errors
                        var firstErrorField = null;
                        $.each(validationErrors, function(field, messages) {
                            var fieldInput = form.find('[name="' + field + '"]');
                            if (fieldInput.length) {
                                var errorText = Array.isArray(messages) ? messages[0] : messages;
                                fieldInput.after('<span class="custom-error" role="alert" style="color: red; display: block; margin-top: 5px;"><strong>' + errorText + '</strong></span>');
                                if (!firstErrorField) {
                                    firstErrorField = fieldInput;
                                }
                            }
                        });
                        // Scroll to first error field
                        if (firstErrorField && firstErrorField.length) {
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 'slow');
                            firstErrorField.focus();
                        }
                        // Show general error message
                        if (validationErrors.document_type) {
                            errorMsg = validationErrors.document_type[0] || 'Checklist Name is required.';
                        }
                    }
                } else if (xhr.responseText) {
                    try {
                        var errorResponse = JSON.parse(xhr.responseText);
                        errorMsg = errorResponse.message || errorResponse.error || errorMsg;
                        // Handle validation errors in response text
                        if (errorResponse.errors) {
                            validationErrors = errorResponse.errors;
                            form.find('.custom-error').remove();
                            var firstErrorField = null;
                            $.each(validationErrors, function(field, messages) {
                                var fieldInput = form.find('[name="' + field + '"]');
                                if (fieldInput.length) {
                                    var errorText = Array.isArray(messages) ? messages[0] : messages;
                                    fieldInput.after('<span class="custom-error" role="alert" style="color: red; display: block; margin-top: 5px;"><strong>' + errorText + '</strong></span>');
                                    if (!firstErrorField) {
                                        firstErrorField = fieldInput;
                                    }
                                }
                            });
                            // Scroll to first error field
                            if (firstErrorField && firstErrorField.length) {
                                $('html, body').animate({
                                    scrollTop: firstErrorField.offset().top - 100
                                }, 'slow');
                                firstErrorField.focus();
                            }
                        }
                    } catch(e) {
                        // If not JSON, use default message
                    }
                }
                
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Error',
                        message: errorMsg,
                        position: 'topRight'
                    });
                } else {
                    alert(errorMsg);
                }
                console.error('Form submission error:', xhr);
            }
        });
        
        return false;
    });
    
    // Also handle direct form submission as fallback
    $(document).on('submit', '#create_checklist_form', function(e) {
        e.preventDefault();
        // Trigger the button click instead
        $('#create_checklist_submit_btn').click();
        return false;
    });
    
    // Confirm Approve Document
    $(document).on('click', '#confirm_approve_btn', function(e) {
        e.preventDefault();
        var documentId = $('#approve_document_id').val();
        
        $.ajax({
            url: '{{ URL::to("/application/approve-document") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                document_id: documentId
            },
            success: function(response) {
                if (response.status) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Document approved successfully!');
                    } else {
                        alert(response.message || 'Document approved successfully!');
                    }
                    // Close modal
                    $('#approve_document_modal').modal('hide');
                    // Reload the page to show updated status
                    location.reload();
                } else {
                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error approving document.');
                    } else {
                        alert(response.message || 'Error approving document.');
                    }
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error approving document. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        });
    });
    
    // First confirmation - Show reason textarea
    $(document).on('click', '#confirm_reject_question_btn', function(e) {
        e.preventDefault();
        $('#reject_reason_container').slideDown();
        $(this).hide();
        $('#confirm_reject_btn').show();
    });
    
    // Final Confirm Reject Document
    $(document).on('click', '#confirm_reject_btn', function(e) {
        e.preventDefault();
        var documentId = $('#reject_document_id').val();
        var rejectReason = $('#reject_reason').val().trim();
        
        if (!rejectReason) {
            alert('Please provide a reason for rejection.');
            $('#reject_reason').focus();
            return false;
        }
        
        $.ajax({
            url: '{{ URL::to("/application/reject-document") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                document_id: documentId,
                reject_reason: rejectReason
            },
            success: function(response) {
                if (response.status) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Document rejected successfully!');
                    } else {
                        alert(response.message || 'Document rejected successfully!');
                    }
                    // Close modal
                    $('#reject_document_modal').modal('hide');
                    // Reload the page to show updated status
                    location.reload();
                } else {
                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error rejecting document.');
                    } else {
                        alert(response.message || 'Error rejecting document.');
                    }
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error rejecting document. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        });
    });
    
    // Approve Document Handler
    $(document).on('click', '.approve-document-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var $btn = $(this);
        var documentId = $btn.data('document-id');
        
        if (!documentId) {
            alert('Document ID not found');
            return false;
        }
        
        // Close the dropdown menu first
        var $dropdown = $btn.closest('.dropdown-menu');
        $dropdown.removeClass('show');
        var $toggle = $dropdown.prev('.dropdown-toggle');
        $toggle.removeClass('show').attr('aria-expanded', 'false');
        
        // Set document ID
        $('#approve_document_id').val(documentId);
        
        // Small delay to ensure dropdown closes completely before showing modal
        setTimeout(function() {
            // Remove any existing backdrop
            $('.modal-backdrop').not('.modal-backdrop.show').remove();
            
            // Show modal
            var $modal = $('#approve_document_modal');
            if ($modal.length) {
                $modal.modal({
                    backdrop: true,
                    keyboard: true,
                    show: true
                });
            } else {
                alert('Approve modal not found. Please refresh the page.');
            }
        }, 150);
        
        return false;
    });

    // Reject Document Handler
    $(document).on('click', '.reject-document-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var $btn = $(this);
        var documentId = $btn.data('document-id');
        
        if (!documentId) {
            alert('Document ID not found');
            return false;
        }
        
        // Close the dropdown menu first
        var $dropdown = $btn.closest('.dropdown-menu');
        $dropdown.removeClass('show');
        var $toggle = $dropdown.prev('.dropdown-toggle');
        $toggle.removeClass('show').attr('aria-expanded', 'false');
        
        // Set document ID and reset modal state
        $('#reject_document_id').val(documentId);
        $('#reject_reason').val(''); // Clear previous reason
        $('#reject_reason_container').hide(); // Hide reason textarea initially
        $('#confirm_reject_question_btn').show(); // Show first confirm button
        $('#confirm_reject_btn').hide(); // Hide final confirm button
        
        // Small delay to ensure dropdown closes completely before showing modal
        setTimeout(function() {
            // Remove any existing backdrop
            $('.modal-backdrop').not('.modal-backdrop.show').remove();
            
            // Show modal
            var $modal = $('#reject_document_modal');
            if ($modal.length) {
                $modal.modal({
                    backdrop: true,
                    keyboard: true,
                    show: true
                });
            } else {
                alert('Reject modal not found. Please refresh the page.');
            }
        }, 150);
        
        return false;
    });
    
    // Initialize Bootstrap tooltips for rejected status badges
    function initializeRejectedTooltips() {
        $('.rejected-status-badge[data-toggle="tooltip"]').each(function() {
            if (!$(this).data('bs.tooltip')) {
                $(this).tooltip({
                    placement: 'top',
                    trigger: 'hover',
                    html: false,
                    container: 'body'
                });
            }
        });
    }
    
    // Initialize on page load
    initializeRejectedTooltips();
    
    // Reinitialize tooltips after AJAX updates (if needed)
    $(document).ajaxComplete(function() {
        setTimeout(initializeRejectedTooltips, 100);
    });
    
    // Delete Document by list_id Handler
    $(document).on('click', '.delete-document-by-list', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var listId = $(this).data('list-id');
        var documentId = $(this).data('document-id');
        
        if (!listId) {
            alert('List ID not found');
            return false;
        }
        
        // Show confirmation
        if (confirm('Are you sure you want to delete this document? All documents with the same checklist will be deleted.')) {
            $.ajax({
                url: '{{ URL::to("/deleteapplicationdocs") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    list_id: listId
                },
                success: function(response) {
                    // Parse response if it's a string
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                    }
                    
                    // Check if deletion was successful
                    if (response && (response.status === true || response.status === 'true' || response.status === 1)) {
                        // Show success message briefly
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Document deleted successfully!');
                        } else {
                            alert(response.message || 'Document deleted successfully!');
                        }
                    } else {
                        // Show warning message
                        if (typeof toastr !== 'undefined') {
                            toastr.warning(response && response.message ? response.message : 'Please verify if document was deleted.');
                        }
                    }
                    
                    // Always reload the page to show updated table after deletion attempt
                    location.reload();
                },
                error: function(xhr) {
                    // If status is 200, assume deletion was successful and reload
                    if (xhr.status === 200) {
                        location.reload();
                        return;
                    }
                    
                    var errorMsg = 'Error deleting document. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMsg = errorResponse.message;
                            }
                        } catch (e) {
                            // If can't parse, use default message
                        }
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                }
            });
        }
        
        return false;
    });
    
    // Ensure all dropdowns are properly initialized
    $('.checklist-details-table .dropdown-toggle').dropdown({
        boundary: 'viewport',
        flip: true
    });
    
    // Force show all dropdown items when opened
    $('.checklist-details-table .dropdown').on('show.bs.dropdown', function() {
        var $menu = $(this).find('.dropdown-menu');
        $menu.css({
            'display': 'block',
            'visibility': 'visible',
            'opacity': '1',
            'max-height': 'none',
            'height': 'auto',
            'overflow': 'visible'
        });
    });
});
</script>

<script>
    // Approve Audit Value Function - Must be globally accessible
    function approveAuditValue(auditId, fieldName, clientId, clientMatterId) {
        if (!confirm('Are you sure you want to approve this change? This will update the main record and remove the pending change.')) {
            return;
        }
        
        // Validate clientMatterId
        if (!clientMatterId || clientMatterId === 'null' || clientMatterId === null) {
            alert('Error: Client matter ID is required. Please select a matter first.');
            return;
        }
        
        $.ajax({
            url: '/api/client-portal-details/approve-audit',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                audit_id: auditId,
                field_name: fieldName,
                client_id: clientId,
                client_matter_id: clientMatterId
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Change approved successfully!');
                    } else {
                        alert(response.message || 'Change approved successfully!');
                    }
                    // Reload the page to show updated values
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to approve change.');
                    } else {
                        alert(response.message || 'Failed to approve change.');
                    }
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error approving change. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        });
    }
    
    // Reject Audit Value Function - Must be globally accessible
    function rejectAuditValue(auditId, fieldName, clientId, clientMatterId) {
        if (!confirm('Are you sure you want to reject this change? A message will be sent to the client.')) {
            return;
        }
        
        $.ajax({
            url: '/api/client-portal-details/reject-audit',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                audit_id: auditId,
                field_name: fieldName,
                client_id: clientId,
                client_matter_id: clientMatterId
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Change rejected. Message sent to client.');
                    } else {
                        alert(response.message || 'Change rejected. Message sent to client.');
                    }
                    // Reload the page to show updated values
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to reject change.');
                    } else {
                        alert(response.message || 'Failed to reject change.');
                    }
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error rejecting change. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        });
    }
</script>

<script>
    // Prevent scrollbar when dropdown opens/closes and ensure all options are visible
    $(document).on('show.bs.dropdown', '.checklist-details-table .dropdown', function(e) {
        // Prevent default to ensure our styling works
        var $dropdown = $(this);
        var $menu = $dropdown.find('.dropdown-menu');
        
        // Prevent overflow scrollbar
        $('.checklist-details-panel .table-responsive').css('overflow-y', 'hidden');
        
        // Ensure dropdown menu is visible and properly positioned
        $menu.css({
            'display': 'block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'max-height': 'none !important',
            'height': 'auto !important',
            'overflow': 'visible !important',
            'overflow-y': 'visible !important',
            'overflow-x': 'visible !important',
            'top': '100%',
            'bottom': 'auto',
            'margin-top': '5px',
            'margin-bottom': '0',
            'position': 'absolute',
            'z-index': '9999 !important'
        });
        
        // Ensure all dropdown items are visible
        $menu.find('.dropdown-item, .dropdown-divider').css({
            'display': 'block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'height': 'auto !important',
            'min-height': '2.25rem',
            'line-height': '1.5'
        });
        
        // Check if dropdown would go off screen at bottom
        setTimeout(function() {
            if ($menu.length && $menu.is(':visible')) {
                var menuTop = $menu.offset().top;
                var menuHeight = $menu.outerHeight();
                var windowHeight = $(window).height();
                var scrollTop = $(window).scrollTop();
                var container = $('.checklist-details-panel .table-responsive');
                var containerBottom = container.offset().top + container.outerHeight();
                
                // If dropdown would go off screen, position it above
                if (menuTop + menuHeight > containerBottom || menuTop + menuHeight > windowHeight + scrollTop) {
                    $menu.css({
                        'top': 'auto',
                        'bottom': '100%',
                        'margin-top': '0',
                        'margin-bottom': '5px'
                    });
                }
            }
        }, 50);
    });
    
    $(document).on('shown.bs.dropdown', '.checklist-details-table .dropdown', function() {
        var $menu = $(this).find('.dropdown-menu');
        $menu.css({
            'display': 'block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'max-height': 'none !important',
            'height': 'auto !important',
            'overflow': 'visible !important',
            'overflow-y': 'visible !important',
            'overflow-x': 'visible !important'
        });
        
        // Force all items to be visible
        $menu.find('.dropdown-item, .dropdown-divider').each(function() {
            $(this).css({
                'display': 'block !important',
                'visibility': 'visible !important',
                'opacity': '1 !important'
            });
        });
    });
    
    $(document).on('hide.bs.dropdown', '.checklist-details-table .dropdown', function() {
        $('.checklist-details-panel .table-responsive').css('overflow-y', 'hidden');
    });
    
    // Download Document Handler - Force download instead of preview
    $(document).on('click', '.download-document-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var documentId = $(this).data('document-id');
        var fileName = $(this).data('file-name');
        
        if (!documentId) {
            alert('Document ID not available.');
            return false;
        }
        
        // Use server-side download endpoint to force download
        var downloadUrl = '{{ URL::to("/application/download-document") }}?document_id=' + documentId;
        
        // Method 1: Try using fetch to get blob and trigger download
        fetch(downloadUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.blob();
            })
            .then(blob => {
                // Create object URL from blob
                var url = window.URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = fileName || 'document.pdf';
                link.style.display = 'none';
                
                // Append to body, click, and remove
                document.body.appendChild(link);
                link.click();
                
                // Clean up
                setTimeout(function() {
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                }, 100);
            })
            .catch(error => {
                console.error('Download error:', error);
                // Fallback: Use direct link with download attribute
                var link = document.createElement('a');
                link.href = downloadUrl;
                link.download = fileName || 'document.pdf';
                link.style.display = 'none';
                link.target = '_self';
                document.body.appendChild(link);
                link.click();
                setTimeout(function() {
                    document.body.removeChild(link);
                }, 100);
            });
        
        return false;
    });
</script>

<!-- Approve Document Modal -->
<div class="modal fade" id="approve_document_modal" tabindex="-1" role="dialog" aria-labelledby="approveDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveDocumentModalLabel">Approve Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this document?</p>
                <input type="hidden" id="approve_document_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirm_approve_btn">Yes, Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Document Modal -->
<div class="modal fade" id="reject_document_modal" tabindex="-1" role="dialog" aria-labelledby="rejectDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectDocumentModalLabel">Reject Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject this document?</p>
                <input type="hidden" id="reject_document_id" value="">
                <div class="form-group mt-3" id="reject_reason_container" style="display: none;">
                    <label for="reject_reason">Reason For Reject <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reject_reason" name="reject_reason" rows="4" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm_reject_question_btn">Yes, Reject</button>
                <button type="button" class="btn btn-danger" id="confirm_reject_btn" style="display: none;">Confirm Reject</button>
            </div>
        </div>
    </div>
</div>
