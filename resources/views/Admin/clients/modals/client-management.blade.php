<!-- Convert Lead to Client Modal -->
<div class="modal fade custom_modal" id="convertLeadToClientModal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Convert Lead To Client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method="get" action="{{URL::to('/admin/clients/changetype/'.base64_encode(convert_uuencode($fetchedData->id)).'/client')}}" name="convert_lead_to_client" autocomplete="off" id="convert_lead_to_client">
				    @csrf
                    <div class="row">
                        <input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                        <input type="hidden" name="user_id" value="{{@Auth::user()->id}}">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="migration_agent">Migration Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="migration_agent" id="sel_migration_agent_id">
                                    <option value="">Select Migration Agent</option>
                                    @foreach(\App\Models\Admin::where('role',16)->select('id','first_name','last_name','email')->where('status',1)->get() as $migAgntlist)
                                        <option value="{{$migAgntlist->id}}">{{@$migAgntlist->first_name}} {{@$migAgntlist->last_name}} ({{@$migAgntlist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="person_responsible">Person Responsible <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="person_responsible" id="sel_person_responsible_id">
                                    <option value="">Select Person Responsible</option>
                                    @foreach(\App\Models\Admin::where('role',12)->select('id','first_name','last_name','email')->where('status',1)->get() as $perreslist)
                                        <option value="{{$perreslist->id}}">{{@$perreslist->first_name}} {{@$perreslist->last_name}} ({{@$perreslist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="person_assisting">Person Assisting <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="person_assisting" id="sel_person_assisting_id">
                                    <option value="">Select Person Assisting</option>
                                    @foreach(\App\Models\Admin::where('role',13)->select('id','first_name','last_name','email')->where('status',1)->get() as $perassislist)
                                        <option value="{{$perassislist->id}}">{{@$perassislist->first_name}} {{@$perassislist->last_name}} ({{@$perassislist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="matter_id">Select Matter <span class="span_req">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="matter_id" value="1" id="general_matter_checkbox_new">
                                    <label class="form-check-label" for="general_matter_checkbox_new">General Matter</label>
                                </div>

                                <label class="form-check-label" for="">Or Select any option</label>

                                <select data-valid="required" class="form-control select2" name="matter_id" id="sel_matter_id">
                                    <option value="">Select Matter</option>
                                    @foreach(\App\Models\Matter::select('id','title')->where('status',1)->get() as $matterlist)
                                        <option value="{{$matterlist->id}}">{{@$matterlist->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">
                            <button onclick="customValidate('convert_lead_to_client')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
    .dropdown-toggle::after {
        margin-left: 16.255em !important;
    }

    /* Custom styles for the assignee dropdown */
    #create_action_popup .dropdown-menu {
        padding: 12px !important;
    }

    #create_action_popup .user-item {
        margin-bottom: 8px !important;
        padding-left: 0 !important;
    }

    #create_action_popup .user-item label {
        margin-bottom: 0 !important;
        padding-left: 0 !important;
        margin-left: 0 !important;
        display: flex !important;
        align-items: center !important;
        text-align: left !important;
    }

    #create_action_popup .checkbox-item {
        margin-right: 8px !important;
        margin-left: 0 !important;
        flex-shrink: 0 !important;
    }

    #create_action_popup .user-item span {
        margin-left: 0 !important;
        padding-left: 0 !important;
        flex: 1 !important;
        text-align: left !important;
        text-indent: 0 !important;
    }

    #create_action_popup #users-list {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }

    /* ========================================
       MODERN ASSIGN USER MODAL STYLES
       ======================================== */

    /* Modal Container */
    .assign-user-modal {
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
    }

    /* Header Styling */
    .assign-user-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title-section {
        display: flex;
        align-items: center;
    }

    .modal-title-section .modal-title {
        font-weight: 600;
        font-size: 1.4rem;
    }

    .modal-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-actions .close {
        color: white;
        opacity: 0.8;
        font-size: 1.5rem;
        padding: 0;
        margin: 0;
    }

    .modal-actions .close:hover {
        opacity: 1;
    }

    /* Body Styling */
    .assign-user-body {
        padding: 30px 25px;
        background: #fafbfc;
    }

    /* Enhanced Form Groups */
    .enhanced-form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-label i {
        font-size: 0.9rem;
    }

    /* Input Groups */
    .input-group-text {
        background: #f7fafc;
        border-color: #e2e8f0;
        color: #718096;
        border-radius: 8px 0 0 8px;
        padding: 12px 15px;
    }

    .enhanced-input,
    .enhanced-select,
    .enhanced-textarea {
        border-radius: 0 8px 8px 0;
        border-color: #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .enhanced-input:focus,
    .enhanced-select:focus,
    .enhanced-textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .enhanced-textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* Modern Dropdown Styling */
    .modern-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .enhanced-dropdown-btn {
        width: 100%;
        text-align: left;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 15px;
        background: white;
        color: #2d3748;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 48px;
        position: relative;
    }

    .enhanced-dropdown-btn #selected-users-text {
        flex: 1;
        margin: 0 10px;
        word-wrap: break-word;
        white-space: normal;
    }

    /* Only apply truncation when text is actually too long */
    .enhanced-dropdown-btn.has-long-text #selected-users-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .enhanced-dropdown-btn:hover,
    .enhanced-dropdown-btn:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Button styling when users are selected */
    .enhanced-dropdown-btn.has-selection {
        border-color: #667eea;
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        color: #667eea;
        font-weight: 500;
    }

    .enhanced-dropdown-btn.has-selection:hover {
        background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 100%);
        border-color: #5a67d8;
    }

    /* Selection indicator */
    .enhanced-dropdown-btn.has-selection::before {
        content: '';
        position: absolute;
        top: 8px;
        right: 40px;
        width: 8px;
        height: 8px;
        background: #667eea;
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
    }

    .modern-dropdown-menu {
        min-width: 100%;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        background: white;
        margin-top: 5px;
    }

    .dropdown-search {
        margin-bottom: 15px;
    }

    .dropdown-search .form-control {
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        font-size: 0.9rem;
        color: #2d3748;
        background: white;
    }

    .dropdown-search .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        outline: none;
        color: #2d3748;
    }

    .dropdown-actions {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
    }

    .dropdown-actions .btn {
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #2d3748;
    }

    .dropdown-actions .btn:hover {
        color: #2d3748;
    }

    .dropdown-divider {
        margin: 15px 0;
        border-color: #e2e8f0;
    }

    .users-list-container {
        max-height: 200px;
        overflow-y: auto;
    }

    /* Modern User Items */
    .modern-user-item {
        margin-bottom: 8px;
        padding: 8px;
        border-radius: 6px;
        transition: background-color 0.2s ease;
    }

    .modern-user-item:hover {
        background-color: #f7fafc;
    }

    .modern-user-label {
        margin: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 0;
        font-size: 0.9rem;
        color: #2d3748;
    }

    .modern-user-label .text-muted {
        color: #2d3748 !important;
    }

    .modern-checkbox {
        margin-right: 10px;
        margin-left: 0;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
    }

    .user-name {
        font-weight: 500;
        color: #2d3748;
        margin-right: 5px;
    }

    .user-branch {
        font-size: 0.85rem;
        color: #2d3748 !important;
        font-weight: 400;
    }

    /* Footer Buttons */
    .modal-footer-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .btn-assign-user {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-assign-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    .btn-outline-secondary {
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1rem;
        border-width: 2px;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        transform: translateY(-1px);
    }

    /* Custom Error Styling */
    .custom-error {
        color: #e53e3e;
        font-size: 0.85rem;
        margin-top: 5px;
        font-weight: 500;
    }

    /* Note Deadline Styling */
    .note_deadline .form-label {
        margin-bottom: 0;
    }

    .note_deadline_checkbox {
        width: 18px;
        height: 18px;
        margin-right: 8px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .assign-user-body {
            padding: 20px 15px;
        }
        
        .modal-footer-buttons {
            flex-direction: column;
            gap: 10px;
        }
        
        .btn-assign-user,
        .btn-outline-secondary {
            width: 100%;
            padding: 15px 20px;
        }
        
        .modal-title-section .modal-title {
            font-size: 1.2rem;
        }
    }

    /* Loading State */
    .popuploader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .popuploader > div {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .popuploader i {
        font-size: 32px;
        color: #667eea;
        margin-bottom: 15px;
    }

    .popuploader p {
        margin: 0;
        font-weight: 500;
        color: #2d3748;
    }

    /* Enhanced Tooltip Styling */
    .tooltip-inner {
        background: #2d3748;
        color: white;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 0.85rem;
        line-height: 1.4;
        max-width: 300px;
        text-align: left;
        white-space: pre-line;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .tooltip.bs-tooltip-bottom .arrow::before {
        border-bottom-color: #2d3748;
    }

    /* Selection count badge */
    .selection-count-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Assign User Modal -->
<div class="modal fade custom_modal" id="create_action_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content assign-user-modal">
            <div class="modal-header assign-user-header">
                <div class="modal-title-section">
                    <i class="fas fa-user-plus text-white mr-2"></i>
                    <h5 class="modal-title mb-0" id="create_action_popupLabel">Assign User</h5>
                </div>
                <div class="modal-actions">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body assign-user-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group enhanced-form-group">
                            <label for="dropdownMenuButton" class="form-label">
                                <i class="fas fa-users text-muted mr-1"></i>
                                Select Assignee <span class="text-danger">*</span>
                            </label>
                            <div class="dropdown-multi-select modern-dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle enhanced-dropdown-btn" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    <span id="selected-users-text">Assign User</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu modern-dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <!-- Search input -->
                                    <div class="dropdown-search">
                                        <input type="text" id="user-search" class="form-control" placeholder="Search users...">
                                    </div>
                                    <!-- Select All/None buttons -->
                                    <div class="dropdown-actions">
                                        <button type="button" id="select-all-users" class="btn btn-sm btn-outline-primary">Select All</button>
                                        <button type="button" id="select-none-users" class="btn btn-sm btn-outline-secondary">Select None</button>
                                    </div>
                                    <hr class="dropdown-divider">
                                    <!-- Users list -->
                                    <div id="users-list" class="users-list-container">
                                        @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                        <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                        <div class="user-item modern-user-item" data-name="{{ strtolower($admin->first_name.' '.$admin->last_name.' '.@$branchname->office_name) }}">
                                            <label class="modern-user-label">
                                                <input type="checkbox" class="checkbox-item modern-checkbox" value="{{ $admin->id }}" data-name="{{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})">
                                                <i class="fas fa-user-circle mr-2 text-muted"></i>
                                                <span class="user-name">{{ $admin->first_name }} {{ $admin->last_name }}</span>
                                                <span class="user-branch text-muted">({{ @$branchname->office_name }})</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input to store selected values -->
                        <select class="d-none" id="rem_cat" name="rem_cat[]" multiple="multiple">
                            @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                            <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                            <option value="{{ $admin->id }}">{{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-group enhanced-form-group">
                            <label for="assignnote" class="form-label">
                                <i class="fas fa-sticky-note text-muted mr-1"></i>
                                Note <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                </div>
                                <textarea id="assignnote" class="form-control enhanced-textarea" placeholder="Enter a note..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group enhanced-form-group">
                            <label for="popoverdatetime" class="form-label">
                                <i class="fas fa-calendar text-muted mr-1"></i>
                                Date <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" class="form-control enhanced-input" placeholder="yyyy-mm-dd" id="popoverdatetime" value="{{ date('Y-m-d') }}" name="popoverdate">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group enhanced-form-group">
                            <label for="task_group" class="form-label">
                                <i class="fas fa-tag text-muted mr-1"></i>
                                Group <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                                </div>
                                <select class="form-control enhanced-select" id="task_group" name="task_group">
                                    <option value="">Select Group</option>
                                    <option value="Call">📞 Call</option>
                                    <option value="Checklist">✅ Checklist</option>
                                    <option value="Review">📋 Review</option>
                                    <option value="Query">❓ Query</option>
                                    <option value="Urgent">⚠️ Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group enhanced-form-group note_deadline">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-4">
                                    <label class="form-label d-flex align-items-center">
                                        <input class="note_deadline_checkbox mr-2" type="checkbox" id="note_deadline_checkbox" name="note_deadline_checkbox" value="">
                                        <i class="fas fa-clock text-muted mr-1"></i>
                                        Note Deadline
                                    </label>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                        </div>
                                        <input type="date" class="form-control enhanced-input" placeholder="yyyy-mm-dd" id="note_deadline" value="<?php echo date('Y-m-d');?>" name="note_deadline" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input id="assign_client_id" type="hidden" value="{{ base64_encode(convert_uuencode(@$fetchedData->id)) }}">
                <input type="hidden" value="" id="popoverrealdate" name="popoverrealdate" />
                
                <div class="modal-footer-buttons">
                    <button class="btn btn-primary btn-lg btn-assign-user" id="assignUser">
                        <i class="fas fa-user-plus mr-2"></i>Assign User
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading overlay for the modal -->
<div class="popuploader" style="display: none;">
    <div style="background: white; padding: 20px; border-radius: 5px; display: inline-block;">
        <i class="fa fa-spinner fa-spin" style="font-size: 24px; color: #007bff;"></i>
        <p style="margin-top: 10px; margin-bottom: 0;">Processing...</p>
    </div>
</div>

<!-- Change Matter Assignee Modal -->
<div class="modal fade custom_modal" id="changeMatterAssigneeModal" tabindex="-1" role="dialog" aria-labelledby="change_MatterModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="change_MatterModalLabel">Change Matter Assignee</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method="post" action="{{URL::to('/admin/clients/updateClientMatterAssignee')}}" name="change_matter_assignee" autocomplete="off" id="change_matter_assignee">
				    @csrf
                    <div class="row">
                        <input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                        <input type="hidden" name="user_id" value="{{@Auth::user()->id}}">
                        <input type="hidden" name="selectedMatterLM" id="selectedMatterLM" value="">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="migration_agent">Migration Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="migration_agent" id="change_sel_migration_agent_id">
                                    <option value="">Select Migration Agent</option>
                                    @foreach(\App\Models\Admin::where('role',16)->select('id','first_name','last_name','email')->where('status',1)->get() as $migAgntlist)
                                        <option value="{{$migAgntlist->id}}">{{@$migAgntlist->first_name}} {{@$migAgntlist->last_name}} ({{@$migAgntlist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="person_responsible">Person Responsible <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="person_responsible" id="change_sel_person_responsible_id">
                                    <option value="">Select Person Responsible</option>
                                    @foreach(\App\Models\Admin::where('role',12)->select('id','first_name','last_name','email')->where('status',1)->get() as $perreslist)
                                        <option value="{{$perreslist->id}}">{{@$perreslist->first_name}} {{@$perreslist->last_name}} ({{@$perreslist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="person_assisting">Person Assisting <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="person_assisting" id="change_sel_person_assisting_id">
                                    <option value="">Select Person Assisting</option>
                                    @foreach(\App\Models\Admin::where('role',13)->select('id','first_name','last_name','email')->where('status',1)->get() as $perassislist)
                                        <option value="{{$perassislist->id}}">{{@$perassislist->first_name}} {{@$perassislist->last_name}} ({{@$perassislist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-9 col-md-9 col-lg-9 text-right">
                            <button onclick="customValidate('change_matter_assignee')" type="button" class="btn btn-primary">Change</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>

