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

});
</script>
