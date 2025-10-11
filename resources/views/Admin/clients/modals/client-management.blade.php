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
</style>

<!-- Assign User Modal -->
<div class="modal fade custom_modal" id="create_action_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 20px;">
            <div class="modal-header" style="padding-bottom: 11px;">
                <h5 class="modal-title assignnn" id="create_action_popupLabel" style="margin: 0 -24px;">Assign User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="box-header with-border">
                <div class="form-group row" style="margin-bottom:12px;">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Select Assignee</label>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <div class="dropdown-multi-select" style="position: relative;display: inline-block;border: 1px solid #ccc;border-radius: 4px;padding: 8px;width: 336px;">
                                <button type="button" style="color: #34395e !important;border: none;width: 100%;text-align: left;" class="btn btn-default dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span id="selected-users-text">Assign User</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="min-width: 100%;max-height: 300px;overflow-y: auto;box-shadow: rgba(0, 0, 0, 0.2) 0px 8px 16px 0px;z-index: 1;padding: 8px;border-radius: 4px;border: 1px solid rgb(204, 204, 204);font-size: 14px;background-color: white;margin-left: -8px;">
                                    <!-- Search input -->
                                    <div style="margin-bottom: 10px;">
                                        <input type="text" id="user-search" class="form-control" placeholder="Search users..." style="font-size: 12px; padding: 5px;">
                                    </div>
                                    <!-- Select All/None buttons -->
                                    <div style="margin-bottom: 10px; text-align: center;">
                                        <button type="button" id="select-all-users" class="btn btn-sm btn-outline-primary" style="margin-right: 5px; font-size: 11px;">Select All</button>
                                        <button type="button" id="select-none-users" class="btn btn-sm btn-outline-secondary" style="font-size: 11px;">Select None</button>
                                    </div>
                                    <hr style="margin: 8px 0;">
                                    <!-- Users list -->
                                    <div id="users-list" style="margin-left: 0; padding-left: 0;">
                                        @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                        <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                        <div class="user-item" data-name="{{ strtolower($admin->first_name.' '.$admin->last_name.' '.@$branchname->office_name) }}" style="margin-bottom: 8px; padding-left: 0;">
                                            <label style="margin-bottom: 0; cursor: pointer; display: flex; align-items: center; padding-left: 0; margin-left: 0; text-align: left;">
                                                <input type="checkbox" class="checkbox-item" value="{{ $admin->id }}" data-name="{{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})" style="margin-right: 8px; margin-left: 0; flex-shrink: 0;">
                                                <span style="margin-left: 0; padding-left: 0; text-align: left; text-indent: 0;">{{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})</span>
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
                    <div class="clearfix"></div>
                </div>
            </div>

            <div id="popover-content">
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px;">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Note</label>
                        <div class="col-sm-9">
                            <textarea id="assignnote" class="form-control" placeholder="Enter a note..."></textarea>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px;">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Date</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control f13" placeholder="yyyy-mm-dd" id="popoverdatetime" value="{{ date('Y-m-d') }}" name="popoverdate">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="form-group row" style="margin-bottom:12px;">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Group</label>
                    <div class="col-sm-9">
                        <select class="assigneeselect2 form-control selec_reg" id="task_group" name="task_group">
                            <option value="">Select</option>
                            <option value="Call">Call</option>
                            <option value="Checklist">Checklist</option>
                            <option value="Review">Review</option>
                            <option value="Query">Query</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="form-group row note_deadline">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Note Deadline
                        <input class="note_deadline_checkbox" type="checkbox" id="note_deadline_checkbox" name="note_deadline_checkbox" value="">
                    </label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control f13" placeholder="yyyy-mm-dd" id="note_deadline" value="<?php echo date('Y-m-d');?>" name="note_deadline" disabled>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <input id="assign_client_id" type="hidden" value="{{ base64_encode(convert_uuencode(@$fetchedData->id)) }}">
                <div class="box-footer" style="padding:10px 0;">
                    <div class="row">
                        <input type="hidden" value="" id="popoverrealdate" name="popoverrealdate" />
                    </div>
                    <div class="row text-center">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-danger" id="assignUser" style="background-color: #0d6efd !important;">Assign User</button>
                        </div>
                    </div>
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

