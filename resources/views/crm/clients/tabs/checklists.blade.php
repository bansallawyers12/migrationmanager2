           <!-- Checklists Tab -->
           <div class="tab-pane" id="checklists-tab">
                <div class="card full-width checklists-container">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-tasks mr-2"></i>Checklists</h4>
                        <div class="checklist-add-wrapper position-relative">
                            <button type="button" class="btn btn-primary btn-add-checklist" id="btn-add-checklist">
                                <i class="fas fa-plus mr-2"></i>Create Checklist
                            </button>
                            <div class="checklist-create-dropdown" id="checklist-create-dropdown" style="display: none;">
                                <div class="dropdown-arrow"></div>
                                <div class="dropdown-body">
                                    <h6 class="dropdown-title mb-3">Create New Checklist</h6>
                                    <form id="checklist-create-form" class="checklist-create-form">
                                        <div class="row">
                                            <!-- Migration Agent - same design as Convert Lead To Client -->
                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="checklist_migration_agent">Migration Agent <span class="span_req">*</span></label>
                                                    <select data-valid="required" class="form-control select2 checklist-field" name="checklist_migration_agent" id="checklist_migration_agent">
                                                        <option value="">Select Migration Agent</option>
                                                        @foreach(\App\Models\Admin::where('role',16)->select('id','first_name','last_name','email')->where('status',1)->get() as $migAgntlist)
                                                            <option value="{{$migAgntlist->id}}">{{@$migAgntlist->first_name}} {{@$migAgntlist->last_name}} ({{@$migAgntlist->email}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Person Responsible -->
                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="checklist_person_responsible">Person Responsible <span class="span_req">*</span></label>
                                                    <select data-valid="required" class="form-control select2 checklist-field" name="checklist_person_responsible" id="checklist_person_responsible">
                                                        <option value="">Select Person Responsible</option>
                                                        @foreach(\App\Models\Admin::where('role',12)->select('id','first_name','last_name','email')->where('status',1)->get() as $perreslist)
                                                            <option value="{{$perreslist->id}}">{{@$perreslist->first_name}} {{@$perreslist->last_name}} ({{@$perreslist->email}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Person Assisting -->
                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="checklist_person_assisting">Person Assisting <span class="span_req">*</span></label>
                                                    <select data-valid="required" class="form-control select2 checklist-field" name="checklist_person_assisting" id="checklist_person_assisting">
                                                        <option value="">Select Person Assisting</option>
                                                        @foreach(\App\Models\Admin::where('role',13)->select('id','first_name','last_name','email')->where('status',1)->get() as $perassislist)
                                                            <option value="{{$perassislist->id}}">{{@$perassislist->first_name}} {{@$perassislist->last_name}} ({{@$perassislist->email}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Handling Office -->
                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="checklist_office">Handling Office <span class="span_req">*</span></label>
                                                    <select data-valid="required" class="form-control select2 checklist-field" name="checklist_office" id="checklist_office">
                                                        <option value="">Select Office</option>
                                                        @foreach(\App\Models\Branch::orderBy('office_name')->get() as $office)
                                                            <option value="{{$office->id}}" {{ Auth::user()->office_id == $office->id ? 'selected' : '' }}>{{$office->office_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-building"></i> This matter will be handled by the selected office
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Select Matter - same design as Convert Lead To Client -->
                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="checklist_matter_select">Select Matter <span class="span_req">*</span></label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="checklist_general_matter" id="checklist_general_matter_checkbox" value="1">
                                                        <label class="form-check-label" for="checklist_general_matter_checkbox">General Matter</label>
                                                    </div>
                                                    <label class="form-check-label">Or Select any option</label>
                                                    <select data-valid="required" class="form-control select2 checklist-field" name="checklist_matter" id="checklist_matter_select">
                                                        <option value="">Select Matter</option>
                                                        @php
                                                            $matterQuery = \App\Models\Matter::select('id','title')->where('status',1);
                                                            if (isset($fetchedData) && $fetchedData->is_company) {
                                                                $matterQuery->where('is_for_company', true);
                                                            } else {
                                                                $matterQuery->where(function($q) {
                                                                    $q->where('is_for_company', false)->orWhereNull('is_for_company');
                                                                });
                                                            }
                                                            $matterList = $matterQuery->get();
                                                        @endphp
                                                        @foreach($matterList as $matterlist)
                                                            <option value="{{$matterlist->id}}" data-matter-id="{{$matterlist->id}}">{{@$matterlist->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Action Buttons - same style as Convert Lead To Client -->
                                            <div class="col-9 col-md-9 col-lg-9 text-right">
                                                <button type="button" class="btn btn-primary btn-continue-cost-assignment">Save</button>
                                                <button type="button" class="btn btn-secondary btn-cancel-checklist">Close</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="checklists-sent-section">
                            <h5 class="font-weight-bold mb-3"><i class="fas fa-list mr-2"></i>Your Checklists</h5>
                            <div id="checklists-list-container">
                                <?php
                                $checklist_forms = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                    ->with(['client', 'agent', 'clientMatter'])
                                    ->orderBy('created_at', 'DESC')
                                    ->get();
                                ?>
                                @if($checklist_forms->isEmpty())
                                    <div class="alert alert-info" id="checklists-empty-state">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No checklists yet. Click <strong>Create Checklist</strong> to add one. You'll select matter, assign migration agent and team, complete cost assignment, and create cost agreement.
                                    </div>
                                    <div id="checklists-list" style="display: none;"></div>
                                @else
                                    <div id="checklists-empty-state" style="display: none;"></div>
                                    <div id="checklists-list" class="checklist-accordion">
                                        @foreach($checklist_forms as $form)
                                            @php
                                                $matterName = $form->clientMatter ? ($form->clientMatter->client_unique_matter_no . ($form->clientMatter->matter ? ' - ' . $form->clientMatter->matter->title : '')) : 'N/A';
                                                $clientMatter = $form->clientMatter;
                                                $migrationAgent = $clientMatter ? $clientMatter->migrationAgent : null;
                                                $personResponsible = $clientMatter ? $clientMatter->personResponsible : null;
                                                $personAssisting = $clientMatter ? $clientMatter->personAssisting : null;
                                                $office = $clientMatter ? $clientMatter->office : null;
                                                
                                                // Calculate costs
                                                $totalDeptCost = 
                                                    ($form->Dept_Base_Application_Charge ?? 0) +
                                                    ($form->Dept_Non_Internet_Application_Charge ?? 0) +
                                                    ($form->Dept_Additional_Applicant_Charge_18_Plus ?? 0) +
                                                    ($form->Dept_Additional_Applicant_Charge_Under_18 ?? 0) +
                                                    ($form->Dept_Subsequent_Temp_Application_Charge ?? 0) +
                                                    ($form->Dept_Second_VAC_Instalment_Charge_18_Plus ?? 0) +
                                                    ($form->Dept_Second_VAC_Instalment_Under_18 ?? 0) +
                                                    ($form->Dept_Nomination_Application_Charge ?? 0) +
                                                    ($form->Dept_Sponsorship_Application_Charge ?? 0);
                                                    
                                                $totalSurcharge = $form->TotalDoHASurcharges ?? 0;
                                                $totalOurCost = $form->TotalBLOCKFEE ?? 0;
                                                
                                                // Check if agreement document exists
                                                $agreementDoc = \App\Models\Document::where('client_matter_id', $form->client_matter_id)
                                                    ->where('doc_type', 'agreement')
                                                    ->latest()
                                                    ->first();
                                            @endphp
                                            <div class="checklist-item-wrapper" data-id="{{ $form->id }}">
                                                <div class="checklist-item-header" data-toggle="collapse" data-target="#checklist-detail-{{ $form->id }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-chevron-right checklist-toggle-icon mr-2"></i>
                                                            <div>
                                                                <strong class="checklist-matter-name">{{ $matterName }}</strong>
                                                                <span class="text-muted ml-2 small">{{ $form->created_at ? $form->created_at->format('d/m/Y') : '' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="checklist-summary">
                                                            <span class="badge badge-info mr-2">
                                                                <i class="fas fa-users"></i> {{ $office ? $office->office_name : 'No Office' }}
                                                            </span>
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-dollar-sign"></i> ${{ number_format($totalOurCost + $totalDeptCost + $totalSurcharge, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div id="checklist-detail-{{ $form->id }}" class="checklist-item-details collapse">
                                                    <div class="checklist-detail-content">
                                                        <div class="row">
                                                            <!-- Team Section -->
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="font-weight-bold mb-3"><i class="fas fa-users mr-2"></i>Team Members</h6>
                                                                <div class="team-member mb-2">
                                                                    <label class="mb-1">Migration Agent:</label>
                                                                    <div class="font-weight-500">
                                                                        {{ $migrationAgent ? $migrationAgent->first_name . ' ' . $migrationAgent->last_name : 'Not Assigned' }}
                                                                    </div>
                                                                </div>
                                                                <div class="team-member mb-2">
                                                                    <label class="mb-1">Person Responsible:</label>
                                                                    <div class="font-weight-500">
                                                                        {{ $personResponsible ? $personResponsible->first_name . ' ' . $personResponsible->last_name : 'Not Assigned' }}
                                                                    </div>
                                                                </div>
                                                                <div class="team-member mb-2">
                                                                    <label class="mb-1">Person Assisting:</label>
                                                                    <div class="font-weight-500">
                                                                        {{ $personAssisting ? $personAssisting->first_name . ' ' . $personAssisting->last_name : 'Not Assigned' }}
                                                                    </div>
                                                                </div>
                                                                <div class="team-member">
                                                                    <label class="mb-1">Handling Office:</label>
                                                                    <div class="font-weight-500">
                                                                        <i class="fas fa-building mr-1 text-primary"></i>{{ $office ? $office->office_name : 'No Office Assigned' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Cost Breakdown Section -->
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="font-weight-bold mb-3"><i class="fas fa-calculator mr-2"></i>Cost Breakdown</h6>
                                                                <div class="cost-item mb-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span>Our Cost (Block Fees):</span>
                                                                        <strong class="text-primary" style="font-size: 1.1rem;">${{ number_format($totalOurCost, 2) }}</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="cost-item mb-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span>Dept. Charges:</span>
                                                                        <strong class="text-info" style="font-size: 1.1rem;">${{ number_format($totalDeptCost, 2) }}</strong>
                                                                    </div>
                                                                </div>
                                                                @if($totalSurcharge > 0)
                                                                <div class="cost-item mb-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span>Surcharges:</span>
                                                                        <strong class="text-danger" style="font-size: 1.1rem;">${{ number_format($totalSurcharge, 2) }}</strong>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                <hr class="my-2" style="border-top: 2px solid #dee2e6;">
                                                                <div class="cost-item" style="background: #e8f5e9; border-left-color: #28a745; border-left-width: 4px;">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="font-weight-bold" style="color: #1b5e20; font-size: 1.05rem;">Total Cost:</span>
                                                                        <strong class="text-success" style="font-size: 1.25rem; font-weight: 700;">${{ number_format($totalOurCost + $totalDeptCost + $totalSurcharge, 2) }}</strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Actions Section -->
                                                        <div class="checklist-actions-section mt-3 pt-3 border-top">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h6 class="font-weight-bold mb-3"><i class="fas fa-tools mr-2"></i>Actions</h6>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a href="{{ route('forms.preview', $form) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="View Cost Assignment">
                                                                            <i class="fas fa-eye mr-1"></i>View
                                                                        </a>
                                                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-amend-checklist" data-id="{{ $form->id }}" data-client-matter-id="{{ $form->client_matter_id }}" title="Amend Cost Assignment">
                                                                            <i class="fas fa-edit mr-1"></i>Amend
                                                                        </button>
                                                                        <button type="button" class="btn btn-primary btn-sm visaAgreementCreateForm" data-id="{{ $form->id }}" data-client-matter-id="{{ $form->client_matter_id }}" title="Create Visa Agreement">
                                                                            <i class="fas fa-file-contract mr-1"></i>Create Visa Agreement
                                                                        </button>
                                                                        <button type="button" class="btn btn-success btn-sm finalizeAgreementConvertToPdf" data-id="{{ $form->id }}" data-client-matter-id="{{ $form->client_matter_id }}" title="Finalize & Upload for Signature">
                                                                            <i class="fas fa-lock mr-1"></i>Finalize
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            @if($agreementDoc && $agreementDoc->signature_doc_link)
                                                            @php
                                                                // Decode the JSON signature link
                                                                $signatureLinks = json_decode($agreementDoc->signature_doc_link, true);
                                                                $primaryLink = $signatureLinks[0] ?? null;
                                                                $signingUrl = $primaryLink['url'] ?? '';
                                                                $signerName = $primaryLink['name'] ?? '';
                                                                $signerEmail = $primaryLink['email'] ?? '';
                                                            @endphp
                                                            <!-- Signature Link Section -->
                                                            <div class="signature-section mt-3 p-3 bg-light rounded">
                                                                <h6 class="font-weight-bold mb-2"><i class="fas fa-signature mr-2"></i>Signature Link</h6>
                                                                @if($signingUrl)
                                                                <div class="mb-2">
                                                                    <small class="text-muted">
                                                                        <strong>Signer:</strong> {{ $signerName }} ({{ $signerEmail }})
                                                                    </small>
                                                                </div>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control signature-link-input" value="{{ $signingUrl }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary btn-copy-signature-link" type="button" data-link="{{ $signingUrl }}">
                                                                            <i class="fas fa-copy"></i> Copy
                                                                        </button>
                                                                        <a href="{{ $signingUrl }}" target="_blank" class="btn btn-outline-primary">
                                                                            <i class="fas fa-external-link-alt"></i> View
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted d-block mt-2">
                                                                    <i class="fas fa-info-circle"></i> Share this link with the client to sign the agreement
                                                                </small>
                                                                @else
                                                                <div class="alert alert-warning mb-0">
                                                                    <i class="fas fa-exclamation-triangle"></i> Signature link data is invalid. Please try placing signature fields again.
                                                                </div>
                                                                @endif
                                                            </div>
                                                            @elseif($agreementDoc)
                                                            <!-- Document Uploaded - Awaiting Signature Setup -->
                                                            <div class="signature-section mt-3 p-3 bg-warning-light rounded border border-warning">
                                                                <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Signature Setup Required</h6>
                                                                <p class="mb-2 small">The agreement has been uploaded but signature fields haven't been placed yet.</p>
                                                                <a href="{{ route('documents.edit', $agreementDoc->id) }}" target="_blank" class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-pen-nib mr-1"></i>Place Signature Fields
                                                                </a>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
           </div>

<style>
.checklist-add-wrapper { position: relative; }
.checklist-create-dropdown {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    min-width: 420px;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    border: 1px solid #e2e8f0;
    z-index: 1060;
}
.checklist-create-dropdown .dropdown-arrow { display: none; }
.checklist-create-dropdown .dropdown-body {
    padding: 24px;
}
.checklist-create-dropdown .dropdown-title { color: #334155; }

/* Checklist Accordion Styles */
.checklist-accordion {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.checklist-item-wrapper {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.checklist-item-wrapper:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.checklist-item-header {
    padding: 16px 20px;
    cursor: pointer;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
    border-bottom: 1px solid transparent;
}

.checklist-item-header:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
}

.checklist-item-header[aria-expanded="true"] {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border-bottom: 1px solid #e2e8f0;
}

.checklist-item-header[aria-expanded="true"] .checklist-matter-name,
.checklist-item-header[aria-expanded="true"] .text-muted,
.checklist-item-header[aria-expanded="true"] .checklist-toggle-icon {
    color: #fff !important;
}

.checklist-item-header[aria-expanded="true"] .badge {
    background-color: rgba(255,255,255,0.2) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.3);
}

.checklist-toggle-icon {
    transition: transform 0.3s ease;
    color: #6c757d;
}

.checklist-item-header[aria-expanded="true"] .checklist-toggle-icon {
    transform: rotate(90deg);
}

.checklist-matter-name {
    font-size: 1rem;
    color: #2c3e50;
}

.checklist-summary {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.checklist-item-details {
    border-top: 1px solid #e2e8f0;
}

.checklist-detail-content {
    padding: 20px;
    background: #ffffff;
}

.checklist-detail-content h6 {
    color: #212529;
    font-weight: 700;
}

.team-member, .cost-item {
    background: #fff;
    padding: 10px 14px;
    border-radius: 4px;
    border-left: 3px solid #4a90e2;
}

.team-member label {
    color: #495057 !important;
    font-weight: 600;
    font-size: 0.875rem;
}

.team-member .font-weight-500 {
    color: #212529;
    font-size: 0.95rem;
}

.cost-item {
    border-left-color: #28a745;
}

.cost-item .text-muted {
    color: #495057 !important;
    font-weight: 500;
}

.cost-item strong {
    color: #212529;
    font-size: 1rem;
}

.font-weight-500 {
    font-weight: 500;
}

.checklist-actions-section {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
}

.checklist-actions-section h6 {
    color: #212529;
    font-weight: 700;
}

.checklist-actions-section .gap-2 {
    gap: 8px;
}

.signature-section {
    animation: fadeIn 0.3s ease-in;
}

.signature-section h6 {
    color: #212529;
    font-weight: 700;
}

.signature-section p {
    color: #495057;
}

.bg-warning-light {
    background-color: #fff3cd;
}

.bg-warning-light p {
    color: #856404;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.signature-link-input {
    font-family: monospace;
    font-size: 0.85rem;
    background-color: #fff;
}

.btn-copy-signature-link:hover {
    background-color: #6c757d;
    color: #fff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .checklist-summary {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .checklist-item-header {
        padding: 12px 16px;
    }
    
    .checklist-detail-content {
        padding: 16px;
    }
    
    .checklist-actions-section .d-flex {
        flex-direction: column;
    }
    
    .checklist-actions-section .btn {
        width: 100%;
        margin-bottom: 8px;
    }
}
</style>

@push('scripts')
<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        var $btnAdd = $('#btn-add-checklist');
        var $dropdown = $('#checklist-create-dropdown');
        var $matterSelect = $('#checklist_matter_select');

        // Toggle dropdown on plus button click
        $btnAdd.on('click', function(e) {
            e.stopPropagation();
            $dropdown.toggle();
            if ($dropdown.is(':visible')) {
                initChecklistSelect2();
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$dropdown.is(e.target) && $dropdown.has(e.target).length === 0 && !$btnAdd.is(e.target)) {
                $dropdown.hide();
            }
        });

        // Cancel button
        $dropdown.on('click', '.btn-cancel-checklist', function() {
            $dropdown.hide();
        });

        // General Matter checkbox: when checked, use matter 1 (same as Convert Lead To Client)
        $('#checklist_general_matter_checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                $matterSelect.val('1').trigger('change');
            } else {
                $matterSelect.val('').trigger('change');
            }
        });

        // Continue / Save - uses Lead flow (matter type from admin list)
        $dropdown.on('click', '.btn-continue-cost-assignment', function() {
            var generalMatterChecked = $('#checklist_general_matter_checkbox').is(':checked');
            var matterId = generalMatterChecked ? '1' : $matterSelect.val();
            var clientId = window.ClientDetailConfig ? window.ClientDetailConfig.clientId : $('.crm-container').data('client-id');

            if (!clientId) {
                alert('Client ID not found. Please refresh the page.');
                return;
            }

            if (!matterId) {
                alert('Please select a Matter or check General Matter.');
                return;
            }

            var migrationAgent = $('#checklist_migration_agent').val();
            var personResponsible = $('#checklist_person_responsible').val();
            var personAssisting = $('#checklist_person_assisting').val();
            var officeId = $('#checklist_office').val();

            if (!migrationAgent || !personResponsible || !personAssisting || !officeId) {
                alert('Please fill Migration Agent, Person Responsible, Person Assisting, and Office.');
                return;
            }

            // Open Lead cost assignment modal (creates ClientMatter + CostAssignmentForm)
            $('#cost_assignment_lead_id').val(clientId);
            $('#sel_matter_id_lead').val(matterId).trigger('change');
            $('#sel_migration_agent_id_lead').val(migrationAgent).trigger('change');
            $('#sel_person_responsible_id_lead').val(personResponsible).trigger('change');
            $('#sel_person_assisting_id_lead').val(personAssisting).trigger('change');
            $('#sel_office_id_lead').val(officeId).trigger('change');
            $('#sel_migration_agent_id_lead,#sel_person_responsible_id_lead,#sel_person_assisting_id_lead,#sel_office_id_lead,#sel_matter_id_lead').select2({ dropdownParent: $('#costAssignmentCreateFormModelLead') });
            $('#costAssignmentCreateFormModelLead').modal('show');
            $dropdown.hide();
        });

        function initChecklistSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                var $fields = $('#checklist_matter_select,#checklist_migration_agent,#checklist_person_responsible,#checklist_person_assisting,#checklist_office');
                $fields.each(function() {
                    var $el = $(this);
                    if (!$el.hasClass('select2-hidden-accessible')) {
                        $el.select2({ dropdownParent: $dropdown, width: '100%' });
                    }
                });
            }
        }

        // Accordion toggle functionality
        $('.checklist-item-header').on('click', function() {
            var $this = $(this);
            var isExpanded = $this.attr('aria-expanded') === 'true';
            
            // Close all other accordions
            $('.checklist-item-header').not($this).attr('aria-expanded', 'false');
            $('.checklist-item-details').not($this.next()).removeClass('show');
            
            // Toggle current accordion
            $this.attr('aria-expanded', !isExpanded);
        });

        // Handle Bootstrap collapse events for proper aria-expanded state
        $('.checklist-item-details').on('shown.bs.collapse', function() {
            $(this).prev('.checklist-item-header').attr('aria-expanded', 'true');
        }).on('hidden.bs.collapse', function() {
            $(this).prev('.checklist-item-header').attr('aria-expanded', 'false');
        });

        // Copy signature link to clipboard
        $(document).on('click', '.btn-copy-signature-link', function() {
            var link = $(this).data('link');
            var $input = $(this).closest('.input-group').find('.signature-link-input');
            
            // Select and copy
            $input.select();
            document.execCommand('copy');
            
            // Visual feedback
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.html('<i class="fas fa-check"></i> Copied!');
            $btn.addClass('btn-success').removeClass('btn-outline-secondary');
            
            setTimeout(function() {
                $btn.html(originalHtml);
                $btn.removeClass('btn-success').addClass('btn-outline-secondary');
            }, 2000);
            
            // Deselect
            window.getSelection().removeAllRanges();
        });

        // Amend checklist - opens the cost assignment modal for editing
        $(document).on('click', '.btn-amend-checklist', function() {
            var formId = $(this).data('id');
            var clientMatterId = $(this).data('client-matter-id');
            
            // Set the matter in sidebar first
            if (clientMatterId && $('#sel_matter_id_client_detail').length) {
                $('#sel_matter_id_client_detail').val(clientMatterId).trigger('change');
            }
            
            // Open the cost assignment modal to view/edit
            alert('Opening cost assignment for editing. This will open the cost assignment form in a modal.');
            // You can implement the actual edit modal here
            // For now, we'll just redirect to the preview page
            window.open('/forms/' + formId + '/preview', '_blank');
        });

        // When clicking Visa Agreement or Finalize from checklist list, set sidebar matter first
        $('#checklists-tab').on('mousedown', '.visaAgreementCreateForm, .finalizeAgreementConvertToPdf', function() {
            var cmId = $(this).data('client-matter-id');
            if (cmId && $('#sel_matter_id_client_detail').length) {
                $('#sel_matter_id_client_detail').val(cmId).trigger('change');
            }
        });

        // When finalize button is clicked and agreement is uploaded, handle signature flow
        $(document).on('agreementUploaded', function(e, data) {
            if (data.signatureLink) {
                // Reload the checklist tab to show the signature link
                location.reload();
            }
        });
    });
})(jQuery);
</script>
@endpush
