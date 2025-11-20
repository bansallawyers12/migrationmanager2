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
                                                                                                        <a href="javascript:void(0);" 
                                                                                                           class="openfileupload" 
                                                                                                           data-aid="{{ $applicationId }}" 
                                                                                                           data-type="{{ $stageNameSlug }}" 
                                                                                                           data-typename="{{ $stage->name }}" 
                                                                                                           data-id="{{ $checklist->id }}">
                                                                                                            <i class="fa fa-plus"></i>
                                                                                                        </a>
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
                                                                                        <div>Filename</div>
                                                                                        <div>Checklist</div>
                                                                                    </th>
                                                                                    <th>Related Stage</th>
                                                                                    <th style="white-space: normal; line-height: 1.4;">
                                                                                        <div>Added By</div>
                                                                                        <div>Added On</div>
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
                                                                                        $statusText = 'Action Needed (Approval/Rejection)';
                                                                                        $statusClass = 'warning';
                                                                                        if($status == 1) {
                                                                                            $statusText = 'Approved';
                                                                                            $statusClass = 'success';
                                                                                        } elseif($status == 2) {
                                                                                            $statusText = 'Rejected';
                                                                                            $statusClass = 'danger';
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
                                                                                        <td>{{ $document->typename ?? 'N/A' }}</td>
                                                                                        <td>
                                                                                            <div style="margin-bottom: 5px;">
                                                                                                <div class="user-info">
                                                                                                    <span class="user-avatar">{{ substr($addedBy->first_name ?? 'N', 0, 1) }}</span>
                                                                                                    <span class="user-name">{{ $addedBy->first_name ?? 'N/A' }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div>
                                                                                                <small class="text-muted">{{ date('d/m/Y', strtotime($document->created_at)) }}</small>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td style="white-space: normal; word-wrap: break-word;">
                                                                                            @if($status == 0)
                                                                                                <span class="badge badge-warning" style="display: inline-block; white-space: normal; word-wrap: break-word; text-align: center; line-height: 1.3;">Action Needed (Approval/Rejection)</span>
                                                                                            @elseif($status == 1)
                                                                                                <span class="badge badge-success">Approved</span>
                                                                                            @elseif($status == 2)
                                                                                                <span class="badge badge-danger">Rejected</span>
                                                                                            @else
                                                                                                <span class="badge badge-warning" style="display: inline-block; white-space: normal; word-wrap: break-word; text-align: center; line-height: 1.3;">Action Needed (Approval/Rejection)</span>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td style="white-space: nowrap; text-align: center;">
                                                                                            <div class="dropdown d-inline">
                                                                                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                                                    Action
                                                                                                </button>
                                                                                                <div class="dropdown-menu">
                                                                                                    <a target="_blank" class="dropdown-item" href="{{ $document->myfile ?? '#' }}">Preview</a>
                                                                                                    <a class="dropdown-item deletenote" data-id="{{ $document->id }}" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
                                                                                                    <a download class="dropdown-item" href="{{ $document->myfile ?? '#' }}">Download</a>
                                                                                                </div>
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
                                            <div class="tab-content-placeholder">
                                                <p class="text-muted">Messages content will be displayed here.</p>
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
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.checklist-details-table {
    background: white;
    border-radius: 6px;
    width: 100%;
    min-width: 1000px;
    table-layout: fixed;
}

.checklist-details-table thead {
    background: #f8f9fa;
}

.checklist-details-table thead th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 12px;
    vertical-align: middle;
}

.checklist-details-table thead th div {
    line-height: 1.4;
}

.checklist-details-table thead th:nth-child(1) {
    width: 25%;
    min-width: 200px;
}

.checklist-details-table thead th:nth-child(2) {
    width: 15%;
    min-width: 150px;
}

.checklist-details-table thead th:nth-child(3) {
    width: 20%;
    min-width: 180px;
}

.checklist-details-table thead th:nth-child(4) {
    width: 25%;
    min-width: 200px;
}

.checklist-details-table thead th:nth-child(5) {
    width: 15%;
    min-width: 130px;
}

.checklist-details-table tbody td {
    padding: 12px;
    vertical-align: top;
    word-wrap: break-word;
}

.checklist-details-table tbody td:nth-child(4) {
    white-space: normal;
    word-break: break-word;
    min-width: 200px;
}

.checklist-details-table tbody td:nth-child(4) .badge {
    white-space: normal;
    display: inline-block;
    max-width: 100%;
    word-wrap: break-word;
    text-align: center;
}

.checklist-details-table tbody td:nth-child(5) {
    white-space: nowrap;
    min-width: 130px;
    text-align: center;
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
            if (confirm('Are you sure you want to proceed to the next stage?')) {
                // TODO: Implement API call to move to next stage
                alert('Proceed to next stage functionality will be implemented');
            }
        });
    }
    
    // Application Tabs Switching Functionality
    const applicationTabItems = document.querySelectorAll('.application-tab-item');
    const applicationTabPanes = document.querySelectorAll('.application-tab-pane');
    
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
                }
            });
        }
    });

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
    // Set backdrop opacity to 0.1 when create_checklist modal is shown
    $('#create_checklist').on('show.bs.modal', function() {
        setTimeout(function() {
            $('.modal-backdrop').css({'position': 'relative' });

            $('.modal-backdrop').addClass('create-checklist-backdrop').css({
                'opacity': '0.1',
                'background-color': 'rgba(0, 0, 0, 0.1)'
            });
        }, 10);
    });
    
    // Remove the class when modal is hidden
    $('#create_checklist').on('hidden.bs.modal', function() {
        $('.modal-backdrop').removeClass('create-checklist-backdrop');
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
    
    // Reset form when modal is hidden
    $('#create_checklist').on('hidden.bs.modal', function() {
        $('#create_checklist_form')[0].reset();
        $('#appoint_date_container, #appoint_time_container').hide();
        $('.custom-error').text('');
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
        var isValid = true;
        
        // Clear previous errors
        form.find('.custom-error').remove();
        
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
        
        // Get form values for updating the checklist
        var checklistType = form.find('#checklist_type').val();
        var checklistTypename = form.find('#checklist_typename').val();
        var applicationId = form.find('#checklistapp_id').val();
        
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
});
</script>
