<!-- Workflow Tab - Matter-specific, mirrors Client Portal workflow UI -->
<div class="tab-pane" id="workflow-tab">
    <div class="card full-width workflow-tab-container">
        <?php
        // Get the selected matter based on URL parameter or latest matter (same logic as Client Portal)
        $workflowSelectedMatter = null;
        $workflowMatterName = '';
        $workflowMatterNumber = '';

        if (isset($id1) && $id1 != "") {
            $workflowSelectedMatter = DB::table('client_matters as cm')
                ->leftJoin('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                ->where('cm.client_id', $fetchedData->id)
                ->where('cm.client_unique_matter_no', $id1)
                ->select('cm.id', 'cm.client_unique_matter_no', 'm.title', 'cm.sel_matter_id', 'cm.workflow_stage_id', 'cm.matter_status')
                ->first();
        } else {
            $workflowSelectedMatter = DB::table('client_matters as cm')
                ->leftJoin('matters as m', 'cm.sel_matter_id', '=', 'm.id')
                ->where('cm.client_id', $fetchedData->id)
                ->select('cm.id', 'cm.client_unique_matter_no', 'm.title', 'cm.sel_matter_id', 'cm.workflow_stage_id', 'cm.matter_status')
                ->orderBy('cm.id', 'desc')
                ->first();
        }

        if ($workflowSelectedMatter) {
            if ($workflowSelectedMatter->sel_matter_id == 1 || empty($workflowSelectedMatter->title)) {
                $workflowMatterName = 'General Matter';
            } else {
                $workflowMatterName = $workflowSelectedMatter->title;
            }
            $workflowMatterNumber = $workflowSelectedMatter->client_unique_matter_no;
            $workflowCurrentStageId = $workflowSelectedMatter->workflow_stage_id;
        } else {
            $workflowCurrentStageId = null;
        }

        $workflowAllStages = DB::table('workflow_stages')->orderByRaw('COALESCE(sort_order, id) ASC')->get();

        // Get application for Documents tab
        $workflowApplicationData = null;
        $workflowApplicationId = null;
        if ($workflowSelectedMatter) {
            $workflowApplicationData = DB::table('applications')
                ->where('client_matter_id', $workflowSelectedMatter->id)
                ->where('client_id', $fetchedData->id)
                ->first();
            if ($workflowApplicationData) {
                $workflowApplicationId = $workflowApplicationData->id;
            }
        }
        ?>

        @if($workflowSelectedMatter)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="info-card in-progress-section">
                        <div class="in-progress-single-line">
                            <h5 class="in-progress-title">
                                @if(isset($workflowSelectedMatter->matter_status) && $workflowSelectedMatter->matter_status == 1)
                                    Active
                                @else
                                    In-active
                                @endif
                            </h5>
                            <div class="current-stage-info">
                                <label class="stage-label">Current Stage:</label>
                                <div class="stage-value-container">
                                    <span class="stage-value">
                                        @if($workflowCurrentStageId)
                                            @php
                                                $workflowCurrentStage = $workflowAllStages->where('id', $workflowCurrentStageId)->first();
                                            @endphp
                                            {{ $workflowCurrentStage ? $workflowCurrentStage->name : 'N/A' }}
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
                                        $workflowTotalStages = $workflowAllStages->count();
                                        $workflowCurrentStageRow = $workflowCurrentStageId ? $workflowAllStages->firstWhere('id', $workflowCurrentStageId) : null;
                                        $workflowCurrentSortVal = $workflowCurrentStageRow ? ($workflowCurrentStageRow->sort_order ?? $workflowCurrentStageRow->id) : null;
                                        $workflowCurrentStageIndex = $workflowCurrentSortVal !== null ? $workflowAllStages->where(fn($s) => ($s->sort_order ?? $s->id) <= $workflowCurrentSortVal)->count() : 0;
                                        $workflowProgressPercentage = $workflowTotalStages > 0 ? round(($workflowCurrentStageIndex / $workflowTotalStages) * 100) : 0;
                                    @endphp
                                    <div class="progress-circle" data-progress="{{ $workflowProgressPercentage }}">
                                        <svg class="progress-ring" width="80" height="80">
                                            <circle class="progress-ring-circle-bg" cx="40" cy="40" r="36" fill="transparent" stroke="#e9ecef" stroke-width="6"/>
                                            <circle class="progress-ring-circle" cx="40" cy="40" r="36" fill="transparent" stroke="#007bff" stroke-width="6" stroke-dasharray="{{ 2 * M_PI * 36 }}" stroke-dashoffset="{{ 2 * M_PI * 36 * (1 - $workflowProgressPercentage / 100) }}"/>
                                        </svg>
                                        <div class="progress-text">{{ $workflowProgressPercentage }}%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="stage-navigation-buttons">
                                @php
                                    $workflowIsFirstStage = false;
                                    $workflowNextStageName = null;
                                    $workflowNextStage = null;
                                    if ($workflowCurrentStageId && $workflowAllStages->count() > 0) {
                                        $workflowFirstStage = $workflowAllStages->first();
                                        $workflowIsFirstStage = ($workflowCurrentStageId == $workflowFirstStage->id);
                                        $workflowCurrentOrder = $workflowAllStages->firstWhere('id', $workflowCurrentStageId);
                                        $workflowCurrentSort = $workflowCurrentOrder ? ($workflowCurrentOrder->sort_order ?? $workflowCurrentOrder->id) : null;
                                        $workflowNextStage = $workflowCurrentSort !== null ? $workflowAllStages->first(fn($s) => ($s->sort_order ?? $s->id) > $workflowCurrentSort) : $workflowAllStages->where('id', '>', $workflowCurrentStageId)->first();
                                        $workflowNextStageName = $workflowNextStage ? $workflowNextStage->name : null;
                                    }
                                    $workflowLastStage = $workflowAllStages->last();
                                    $workflowIsLastStage = $workflowNextStage === null;
                                @endphp
                                <button class="btn btn-outline-primary btn-sm" id="workflow-tab-back-to-previous-stage" data-matter-id="{{ $workflowSelectedMatter->id }}" title="Back to Previous Stage" {{ $workflowIsFirstStage ? 'disabled' : '' }}>
                                    <i class="fas fa-angle-left"></i> Back to Previous Stage
                                </button>
                                <button class="btn btn-success btn-sm" id="workflow-tab-proceed-to-next-stage" data-matter-id="{{ $workflowSelectedMatter->id }}" data-next-stage-name="{{ $workflowNextStageName ?? '' }}" title="Proceed to Next Stage" {{ $workflowIsLastStage ? 'disabled' : '' }}>
                                    Proceed to Next Stage <i class="fas fa-angle-right"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" id="workflow-tab-discontinue" data-matter-id="{{ $workflowSelectedMatter->id }}" title="Discontinue Matter">
                                    <i class="fas fa-ban"></i> Discontinue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-folder-open"></i> {{ $workflowMatterName }} ({{ $workflowMatterNumber }})
                        </h5>

                        @if($workflowAllStages->count() > 0)
                            <div class="application-tabs-container mt-3">
                                <ul class="application-tabs-nav workflow-tab-nav" role="tablist">
                                    <li class="application-tab-item active" data-tab="workflow-stages">
                                        <a href="javascript:void(0);" class="application-tab-link">Stages</a>
                                    </li>
                                    <li class="application-tab-item" data-tab="workflow-documents">
                                        <a href="javascript:void(0);" class="application-tab-link">Documents</a>
                                    </li>
                                </ul>

                                <div class="application-tabs-content">
                                    <div class="application-tab-pane active" id="workflow-stages-tab">
                                        <div class="workflow-stages-container mt-3">
                                            <div class="workflow-stages-list">
                                                @foreach($workflowAllStages as $stage)
                                                    @php
                                                        $wfIsActive = ($workflowCurrentStageId && $workflowCurrentStageId == $stage->id);
                                                        $stageSort = $stage->sort_order ?? $stage->id;
                                                        $currentStageRow = $workflowAllStages->firstWhere('id', $workflowCurrentStageId);
                                                        $currentStageSort = $currentStageRow ? ($currentStageRow->sort_order ?? $currentStageRow->id) : null;
                                                        $wfIsCompleted = ($workflowCurrentStageId && $currentStageSort !== null && $stageSort < $currentStageSort);
                                                        $wfStageClass = $wfIsActive ? 'workflow-stage-active' : ($wfIsCompleted ? 'workflow-stage-completed' : 'workflow-stage-pending');
                                                    @endphp
                                                    <div class="workflow-stage-item {{ $wfStageClass }}">
                                                        <span class="stage-name">{{ $stage->name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="application-tab-pane" id="workflow-documents-tab">
                                        @if($workflowApplicationId)
                                            <div class="documents-checklist-container">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="stages-checklist-list">
                                                            <ul class="stages-list">
                                                                @foreach($workflowAllStages as $stage)
                                                                    @php
                                                                        $stageNameSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stage->name)));
                                                                        $isActiveStage = ($workflowCurrentStageId && $workflowCurrentStageId == $stage->id);
                                                                        $stageChecklists = DB::table('application_document_lists')
                                                                            ->where('application_id', $workflowApplicationId)
                                                                            ->where('type', $stageNameSlug)
                                                                            ->orderBy('id', 'asc')
                                                                            ->get();
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
                                                                                                           data-aid="{{ $workflowApplicationId }}"
                                                                                                           data-type="{{ $stageNameSlug }}"
                                                                                                           data-typename="{{ $stage->name }}"
                                                                                                           data-id="{{ $checklist->id }}">
                                                                                                            <i class="fa fa-plus"></i>
                                                                                                        </a>
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
                                                                           data-id="{{ $workflowApplicationId }}"
                                                                           data-typename="{{ $stage->name }}"
                                                                           data-type="{{ $stageNameSlug }}">
                                                                            <i class="fa fa-plus"></i> Add New Checklist
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>

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
                                                                        @php
                                                                            $workflowAllDocuments = DB::table('application_documents')
                                                                                ->where('application_id', $workflowApplicationId)
                                                                                ->orderBy('created_at', 'DESC')
                                                                                ->get();
                                                                        @endphp
                                                                        @foreach($workflowAllDocuments as $document)
                                                                            @php
                                                                                $docList = DB::table('application_document_lists')->where('id', $document->list_id)->first();
                                                                                $addedBy = DB::table('admins')->where('id', $document->user_id)->first();
                                                                                $status = isset($document->status) ? (int)$document->status : 0;
                                                                                $statusText = 'InProgress';
                                                                                $statusClass = 'warning';
                                                                                $rejectionReason = '';
                                                                                if ($status == 1) {
                                                                                    $statusText = 'Approved';
                                                                                    $statusClass = 'success';
                                                                                } elseif ($status == 2) {
                                                                                    $statusText = 'Rejected';
                                                                                    $statusClass = 'danger';
                                                                                    $rejectionReason = $document->doc_rejection_reason ?? $document->reject_reason ?? '';
                                                                                }
                                                                            @endphp
                                                                            <tr>
                                                                                <td>
                                                                                    <div style="margin-bottom: 5px;"><strong>{{ $docList->document_type ?? 'N/A' }}</strong></div>
                                                                                    @if($document->file_name)
                                                                                        <div><a href="{{ $document->myfile ?? '#' }}" target="_blank" style="color: #007bff;">{{ $document->file_name }}</a></div>
                                                                                    @else
                                                                                        <div><small class="text-muted">No file uploaded</small></div>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $document->typename ?? 'N/A' }}</td>
                                                                                <td>
                                                                                    <div style="margin-bottom: 5px;">{{ $addedBy->first_name ?? 'N/A' }}</div>
                                                                                    <div><small>{{ date('d/m/Y', strtotime($document->created_at)) }}</small></div>
                                                                                </td>
                                                                                <td>
                                                                                    @if($status == 0)
                                                                                        <span class="badge badge-warning">InProgress</span>
                                                                                    @elseif($status == 1)
                                                                                        <span class="badge badge-success">Approved</span>
                                                                                    @elseif($status == 2)
                                                                                        <span class="badge badge-danger" title="{{ $rejectionReason }}">Rejected</span>
                                                                                    @else
                                                                                        <span class="badge badge-warning">InProgress</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary download-document-btn" data-document-id="{{ $document->id }}" data-file-url="{{ $document->myfile ?? '#' }}" data-file-name="{{ $document->file_name ?? 'document' }}" title="Download"><i class="fa fa-download"></i></a>
                                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-document-by-list" data-list-id="{{ $document->list_id }}" data-document-id="{{ $document->id }}" title="Delete"><i class="fa fa-trash"></i></a>
                                                                                    @if($status == 0)
                                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-success approve-document-btn" data-document-id="{{ $document->id }}" title="Approve"><i class="fa fa-check-circle"></i></a>
                                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning reject-document-btn" data-document-id="{{ $document->id }}" title="Reject"><i class="fa fa-times-circle"></i></a>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                        @if(count($workflowAllDocuments) == 0)
                                                                            <tr><td colspan="5" class="text-center text-muted">No documents uploaded yet.</td></tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted">No application found for this matter. Create an application from the Client Portal tab first.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No workflow stages defined. Add stages from Admin Console → Workflow Stages.</p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="row mt-3">
                <div class="col-md-12">
                    <p class="text-muted">No matter selected. Please select a matter from the sidebar dropdown.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Workflow tab: Stages/Documents sub-tabs
        var workflowTabNav = document.querySelector('#workflow-tab .workflow-tab-nav');
        if (workflowTabNav) {
            workflowTabNav.querySelectorAll('.application-tab-item').forEach(function(item) {
                var link = item.querySelector('.application-tab-link');
                if (!link) return;
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var tab = item.getAttribute('data-tab');
                    workflowTabNav.querySelectorAll('.application-tab-item').forEach(function(i) { i.classList.remove('active'); });
                    document.querySelectorAll('#workflow-tab .application-tab-pane').forEach(function(p) { p.classList.remove('active'); });
                    item.classList.add('active');
                    var pane = document.getElementById(tab + '-tab');
                    if (pane) pane.classList.add('active');
                });
            });
        }

        // Workflow tab: Proceed to Next Stage
        var nextBtn = document.getElementById('workflow-tab-proceed-to-next-stage');
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                var matterId = this.getAttribute('data-matter-id');
                var nextStageName = (this.getAttribute('data-next-stage-name') || '').trim();
                if (!matterId) { alert('Error: Matter ID not found'); return; }

                // If next stage is "Decision Received", show outcome modal first
                if (nextStageName && nextStageName.toLowerCase() === 'decision received') {
                    document.getElementById('decision-received-matter-id').value = matterId;
                    document.getElementById('decision-outcome').value = '';
                    document.getElementById('decision-note').value = '';
                    document.querySelector('.decision-outcome-error strong').textContent = '';
                    document.querySelector('.decision-note-error strong').textContent = '';
                    $('#decision-received-modal').modal('show');
                    return;
                }

                if (!confirm('Are you sure you want to proceed to the next stage?')) return;

                doProceedToNextStage(matterId, null, null, nextBtn);
            });
        }

        // Shared: Proceed to next stage (with optional decision_outcome and decision_note for Decision Received)
        function doProceedToNextStage(matterId, decisionOutcome, decisionNote, btnEl) {
            var btn = btnEl || document.getElementById('workflow-tab-proceed-to-next-stage');
            var orig = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...'; }

            var payload = { matter_id: matterId };
            if (decisionOutcome) payload.decision_outcome = decisionOutcome;
            if (decisionNote) payload.decision_note = decisionNote;

            fetch('{{ route("clients.matter.update-next-stage") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify(payload)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.status) {
                    alert(data.message || 'Matter has been successfully moved to the next stage.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to move to next stage.');
                    if (btn) { btn.disabled = false; btn.innerHTML = orig; if (data.is_last_stage) btn.disabled = true; }
                }
            })
            .catch(function(err) {
                console.error(err);
                alert('An error occurred.');
                if (btn) { btn.disabled = false; btn.innerHTML = orig; }
            });
        }

        // Decision Received modal: Submit - handled by delegated handler in client_portal.blade.php

        // Workflow tab: Back to Previous Stage
        var prevBtn = document.getElementById('workflow-tab-back-to-previous-stage');
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                var matterId = this.getAttribute('data-matter-id');
                if (!matterId) { alert('Error: Matter ID not found'); return; }
                if (!confirm('Are you sure you want to move back to the previous stage?')) return;

                var btn = this;
                var orig = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                fetch('{{ route("clients.matter.update-previous-stage") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                    body: JSON.stringify({ matter_id: matterId })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status) {
                        alert(data.message || 'Matter has been successfully moved to the previous stage.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to move to previous stage.');
                        btn.disabled = false;
                        btn.innerHTML = orig;
                        if (data.is_first_stage) btn.disabled = true;
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    alert('An error occurred.');
                    btn.disabled = false;
                    btn.innerHTML = orig;
                });
            });
        }

        // Workflow tab: Discontinue button - opens modal
        var discontinueBtn = document.getElementById('workflow-tab-discontinue');
        if (discontinueBtn) {
            discontinueBtn.addEventListener('click', function() {
                var matterId = this.getAttribute('data-matter-id');
                if (!matterId) { alert('Error: Matter ID not found'); return; }
                document.getElementById('discontinue-matter-id').value = matterId;
                document.getElementById('discontinue-reason').value = '';
                document.getElementById('discontinue-notes').value = '';
                document.querySelector('.discontinue-reason-error strong').textContent = '';
                $('#discontinue-matter-modal').modal('show');
            });
        }

        // Discontinue Matter modal: Submit
        var discontinueSubmitBtn = document.getElementById('discontinue-matter-submit');
        if (discontinueSubmitBtn) {
            discontinueSubmitBtn.addEventListener('click', function() {
                var reasonSelect = document.getElementById('discontinue-reason');
                var reason = reasonSelect.value;
                var matterId = document.getElementById('discontinue-matter-id').value;
                var notes = document.getElementById('discontinue-notes').value;
                var errEl = document.querySelector('.discontinue-reason-error strong');

                if (!reason || reason.trim() === '') {
                    errEl.textContent = 'Please select a reason for discontinuing.';
                    return;
                }
                errEl.textContent = '';

                var btn = this;
                var orig = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                fetch('{{ route("clients.matter.discontinue") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '', 'Accept': 'application/json' },
                    body: JSON.stringify({ matter_id: matterId, discontinue_reason: reason, discontinue_notes: notes })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    if (data.status) {
                        $('#discontinue-matter-modal').modal('hide');
                        alert(data.message || 'Matter has been discontinued.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to discontinue matter.');
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    alert('An error occurred.');
                });
            });
        }
    });
})();
</script>
@endpush
