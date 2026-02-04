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
                                    <div id="checklists-list" class="list-group">
                                        @foreach($checklist_forms as $form)
                                            @php
                                                $matterName = $form->clientMatter ? ($form->clientMatter->client_unique_matter_no . ($form->clientMatter->matter ? ' - ' . $form->clientMatter->matter->title : '')) : 'N/A';
                                            @endphp
                                            <div class="checklist-item list-group-item d-flex justify-content-between align-items-center flex-wrap" data-id="{{ $form->id }}">
                                                <div>
                                                    <strong>{{ $matterName }}</strong>
                                                    <span class="text-muted ml-2 small">{{ $form->created_at ? $form->created_at->format('d/m/Y') : '' }}</span>
                                                </div>
                                                <div class="checklist-item-actions mt-2 mt-md-0">
                                                    <a href="{{ route('forms.preview', $form) }}" target="_blank" class="btn btn-sm btn-outline-primary mr-1" title="Preview"><i class="fas fa-eye"></i></a>
                                                    <button type="button" class="btn btn-sm btn-outline-primary visaAgreementCreateForm mr-1" data-id="{{ $form->id }}" data-client-matter-id="{{ $form->client_matter_id }}" title="Create Visa Agreement"><i class="fas fa-plus"></i> Visa Agreement</button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary finalizeAgreementConvertToPdf mr-1" data-id="{{ $form->id }}" data-client-matter-id="{{ $form->client_matter_id }}" title="Finalize Agreement"><i class="fas fa-lock"></i> Finalize</button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-send-checklist-item" disabled title="Send checklist (coming soon)"><i class="fas fa-paper-plane"></i> Send</button>
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
.checklist-item-actions .btn { white-space: nowrap; }
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

        // When clicking Visa Agreement or Finalize from checklist list, set sidebar matter first
        $('#checklists-tab').on('mousedown', '.visaAgreementCreateForm, .finalizeAgreementConvertToPdf', function() {
            var cmId = $(this).data('client-matter-id');
            if (cmId && $('#sel_matter_id_client_detail').length) {
                $('#sel_matter_id_client_detail').val(cmId).trigger('change');
            }
        });
    });
})(jQuery);
</script>
@endpush
