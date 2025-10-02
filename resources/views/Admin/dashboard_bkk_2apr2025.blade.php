@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')
<style>
.totalEmailCntToClientMatter{ background: #FCCD02;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;"}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Client Matters</h4>
                        <div class="card-header-action">
                            <!-- Additional header actions can be added here -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Matter</th>
                                        <th>Client ID</th>
                                        <th>Client Name</th>
                                        <th>Migration Agent</th>
                                        <th>Person Responsible</th>
                                        <th>Person Assisting</th>
                                        <th>Stage</th>
                                    </tr>
                                </thead>
                                <tbody>
									<?php //dd($data);?>
									@foreach($data as $index => $item)
										<?php
										$admin = \App\Models\Admin::select('first_name')->where('id', $item->client_id)->first();
										if($item->sel_matter_id == 1) {
											$matter_name = 'Genral matter';
										} else {
											$matter = \App\Models\Matter::select('title')->where('id', $item->sel_matter_id)->first();
											$matter_name=$matter->title;
										}
										$client_info = \App\Models\Admin::select('client_id','first_name','last_name')->where('id', $item->client_id)->first();
										$mig_agent_info = \App\Models\Admin::select('first_name','last_name')->where('id', $item->sel_migration_agent)->first();
										$person_responsible = \App\Models\Admin::select('first_name','last_name')->where('id', $item->sel_person_responsible)->first();
										$person_assisting = \App\Models\Admin::select('first_name','last_name')->where('id', $item->sel_person_assisting)->first();

										//Get Total mail assign to any specific client matter
										$total_email_assign_cnt = \App\Models\MailReport::where('client_matter_id', $item->id)
										->where('client_id', $item->client_id)
										->where('conversion_type', 'conversion_email_fetch')
										->whereNull('mail_is_read')
										->where(function($query) {
											$query->orWhere('mail_body_type','inbox')
											->orWhere('mail_body_type','sent');
										})->count();
										?>
										<tr>
											<td>
												<a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$item->client_id)).'/'.$item->client_unique_matter_no )}}">{{ $matter_name}} ({{$item->client_unique_matter_no }}) </a>
												<span class="totalEmailCntToClientMatter">{{$total_email_assign_cnt}}</span>
											</td>
											<td><a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$item->client_id)) )}}">{{ @$client_info->client_id == "" ? config('constants.empty') : str_limit(@$client_info->client_id, '50', '...') }}</a></td>
											<td>{{ @$client_info->first_name == "" ? config('constants.empty') : str_limit(@$client_info->first_name, '50', '...') }} {{ @$client_info->last_name == "" ? config('constants.empty') : str_limit(@$client_info->last_name, '50', '...') }}</td>
											<td>{{ @$mig_agent_info->first_name == "" ? config('constants.empty') : str_limit(@$mig_agent_info->first_name, '50', '...') }} {{ @$mig_agent_info->last_name == "" ? config('constants.empty') : str_limit(@$mig_agent_info->last_name, '50', '...') }}</td>
											<td>{{ @$person_responsible->first_name == "" ? config('constants.empty') : str_limit(@$person_responsible->first_name, '50', '...') }} {{ @$person_responsible->last_name == "" ? config('constants.empty') : str_limit(@$person_responsible->last_name, '50', '...') }}</td>
											<td>{{ @$person_assisting->first_name == "" ? config('constants.empty') : str_limit(@$person_assisting->first_name, '50', '...') }} {{ @$person_assisting->last_name == "" ? config('constants.empty') : str_limit(@$person_assisting->last_name, '50', '...') }}</td>
											<td>
												<select class="form-select stageCls" id="stage_<?php echo $item->id;?>">
													@foreach(\App\Models\WorkflowStage::where('id','!=','')->orderby('id','ASC')->get() as $stage)
													<option value="<?php echo $stage->id; ?>" <?php echo $item->workflow_stage_id == $stage->id ? 'selected' : ''; ?>><?php echo $stage->name; ?></option>
													@endforeach
												</select>
											</td>
										</tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Note Lists With Deadline</h4>
                        <div class="card-header-action">
                            <!-- Additional header actions can be added here -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Client ID</th>
                                        <th>Client Name</th>
                                        <th>Description</th>
                                        <th>Deadline</th>
                                        <th>Created at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notesData as $note)
                                    <?php
                                    $note_client = \App\Models\Admin::select('id','first_name','last_name','client_id')->where('id', $note->client_id)->first();
                                    ?>
                                    <tr>
                                        <td><a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$note_client->id)) )}}">{{ @$note_client->client_id == "" ? config('constants.empty') : str_limit(@$note_client->client_id, '50', '...') }}</a></td>
                                        <td>{{ @$note_client->first_name == "" ? config('constants.empty') : str_limit(@$note_client->first_name, '50', '...') }} {{ @$note_client->last_name == "" ? config('constants.empty') : str_limit(@$note_client->last_name, '50', '...') }}</td>
                                        <td><?php echo preg_replace('/<\/?p>/', '', $note->description ); ?></td>
                                        <td>{{ date('d/m/Y',strtotime($note->note_deadline)) }}</td>
                                        <td>{{ date('d/m/Y',strtotime($note->created_at)) }}</td>
                                        <td style="white-space: initial;">
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item has-icon" href="javascript:;" onclick="closeNotesDeadlineAction({{$note->id}},{{$note->unique_group_id}})">Close</a>
                                                    <a class="dropdown-item has-icon btn-extend_note_deadline"  data-noteid="{{$note->id}}" data-uniquegroupid="{{$note->unique_group_id}}" data-assignnote="{{$note->description}}" data-deadlinedate="{{$note->note_deadline}}" href="javascript:;">Extend</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                     @endforeach
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <!-- Footer content can be added here -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



<div class="modal fade custom_modal" id="create_task_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="#" name="newtaskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="is_ajax" value="0">
				<input type="hidden" name="is_dashboard" value="true">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								{{ Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )) }}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="category">Category <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control cleintselect2 select2" name="category">
									<option value="">Choose Category</option>
									<option value="Reminder">Reminder</option>
									<option value="Call">Call</option>
									<option value="Follow Up">Follow Up</option>
									<option value="Email">Email</option>
									<option value="Meeting">Meeting</option>
									<option value="Support">Support</option>
									<option value="Others">Others</option>
								</select>
								<span class="custom-error category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
							<?php
								$assignee = \App\Models\Admin::select('id','first_name','email')->where('role','!=',1)->get();
								?>
								<label for="assignee">Assignee</label>
								<select data-valid="" class="form-control cleintselect2 select2" name="assignee">
									<option value="">Select</option>
									@foreach($assignee as $assigne)
										<option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
									@endforeach
								</select>
								<span class="custom-error assignee_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="priority">Priority</label>
								<select data-valid="" class="form-control cleintselect2 select2" name="priority">
									<option value="">Choose Priority</option>
									<option value="Low">Low</option>
									<option value="Normal">Normal</option>
									<option value="High">High</option>
									<option value="Urgent">Urgent</option>
								</select>
								<span class="custom-error priority_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_date">Due Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{{ Form::text('due_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error due_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_time">Due Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									{{ Form::time('due_time', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off', 'placeholder'=>'Select Time' )) }}
								</div>
								<span class="custom-error due_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label class="d-block" for="related_to">Related To</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="contact" value="Contact" name="related_to" checked>
									<label class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								{{--<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="application" value="Application" name="related_to">
									<label class="form-check-label" for="application">Application</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="internal" value="Internal" name="related_to">
									<label class="form-check-label" for="internal">Internal</label>
								</div>--}}
								@if ($errors->has('related_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('related_to') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_contact">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control cleintselect2 select2" name="contact_name[]">
									<option value="">Choose Contact</option>
									<?php
									$clients = \App\Models\Admin::select('id','first_name','email')->where('is_archived', '=', '0')->where('role', '=', '7')->get();
									foreach($clients as $client){
									?>
									<option value="{{$client->id}} ">{{$client->first_name}} ({{$client->email}})</option>
									<?php } ?>
								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_partner">
							<div class="form-group">
								<label for="partner_name">Partner Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control cleintselect2 select2" name="partner_name">
									<option value="">Choose Partner</option>
									<?php
									$Partners = \App\Models\Partner::select('id','partner_name','email')->where('id', '!=', '')->get();
									foreach($Partners as $Partner){
									?>
									<option value="{{$Partner->id}} ">{{$Partner->partner_name}} ({{$Partner->email}})</option>
									<?php } ?>
								</select>
								<span class="custom-error partner_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_application">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<select data-valid="" id="getapplications" class="form-control client_name cleintselect2" name="client_name">
									<option value="">Choose Client</option>
									<?php
								//$clientsss = \App\Models\Admin::where('is_archived', '0')->where('role', '7')->get();
								/*	foreach($clientsss as $clientsssss){
									?>
									<option value="{{@$clientsssss->id}}">{{@$clientsssss->first_name}} ({{@$clientsssss->email}})</option>
									<?php }*/ ?>
								</select>
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_application">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<select data-valid="" id="allaplication" class="form-control cleintselect2 select2" name="application">
									<option value="">Choose Application</option>

								</select>
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application">
							<div class="form-group">
								<label for="stage">Stage <span class="span_req">*</span></label>
								<select data-valid="" class="form-control cleintselect2 select2" name="stage">
									<option value="">Choose Stage</option>
									<option value="Application">Application</option>
									<option value="Acceptance">Acceptance</option>
									<option value="Payment">Payment</option>
									<option value="Form | 20">Form | 20</option>
									<option value="Visa Application">Visa Application</option>
									<option value="Interview">Interview</option>
									<option value="Enrolment">Enrolment</option>
									<option value="Course Ongoing">Course Ongoing</option>

								</select>
								<span class="custom-error stage_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="followers">Followers <span class="span_req">*</span></label>
								<select data-valid="" class="form-control cleintselect2 select2" name="followers">
									<option value="">Choose Followers</option>
									<?php
									$followers = \App\Models\Admin::select('id','first_name','email')->where('role', '!=', '7')->get();
									foreach($followers as $follower){
									?>
									<option value="{{$follower->id}} ">{{$follower->first_name}} ({{$follower->email}})</option>
									<?php } ?>
								</select>
								<span class="custom-error followers_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="attachments">Attachments</label>
								<div class="custom-file">
									<input type="file" class="form-control" name="attachments">

								</div>
								<span class="custom-error attachments_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('newtaskform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Action Popup Modal -->
<div class="modal fade custom_modal" id="extend_note_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 20px;">
            <div class="modal-header" style="padding-bottom: 11px;">
                <h5 class="modal-title assignnn" id="create_action_popupLabel" style="margin: 0 -24px;">Extend Notes Deadline</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input id="note_id" type="hidden" value="">
            <input id="unique_group_id" type="hidden" value="">
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

                <div class="form-group row note_deadline">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">
                        Note Deadline
                    </label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control f13" placeholder="yyyy-mm-dd" id="note_deadline" value="<?php echo date('Y-m-d');?>" name="note_deadline">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="box-footer" style="padding:10px 0;">
                    <div class="row text-center">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-danger" id="extend_deadline">Extend Deadline</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')

<script src="{{URL::to('/')}}/js/popover.js"></script>
<script>
$(document).ready(function() {
    //Ajax change on workflow status change
    $(document).on('change', '.stageCls', function () {
        let stageId = $(this).val();
        let itemId = $(this).attr('id').split('_')[1];
        if (stageId) {
            $.ajax({
                url: "{{URL::to('/')}}/admin/update-stage",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: 'POST',
                data: { item_id: itemId, stage_id: stageId },
                success: function (response) {
                    if (response.success) {
                        alert('Client matter stage updated successfully!');
                    } else {
                        alert('Failed to update client matter stage.');
                    }
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while updating status.');
                }
            });
        }
    });

    $(document).on('click', '#extend_deadline', function() {
        $(".popuploader").show();
        let flag = true;
        let error = "";
        $(".custom-error").remove();

        if ($('#assignnote').val() === '') {
            $('.popuploader').hide();
            error = "Note field is required.";
            $('#assignnote').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }
        if ($('#note_deadline').val() === '') {
            $('.popuploader').hide();
            error = "Note Deadline is required.";
            $('#task_group').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

        if (flag) {
            $.ajax({
                type: 'POST',
                url: "{{URL::to('/')}}/admin/extenddeadlinedate",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    note_id: $('#note_id').val(),
                    unique_group_id: $('#unique_group_id').val(),
                    description: $('#assignnote').val(),
                    note_deadline: $('#note_deadline').val()
                },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#extend_note_popup').modal('hide');
                    location.reload();
                    //var obj = $.parseJSON(response);
                }
            });
        } else {
            $('.popuploader').hide();
        }
    });

    // Handle click event on the action button
    $(document).delegate('.btn-extend_note_deadline', 'click', function(){
        var noteid = $(this).attr("data-noteid");
        var uniquegroupid = $(this).attr("data-uniquegroupid");
        var assignnote = $(this).attr("data-assignnote");
        var deadlinedate = $(this).attr("data-deadlinedate");
        $('#note_id').val(noteid);
        $('#unique_group_id').val(uniquegroupid);
        $('#assignnote').val(assignnote);
        $('#note_deadline').val(deadlinedate);
        $('#extend_note_popup').modal('show');
    });

    // Listen for changes on the status dropdown
    $('.status-dropdown').change(function() {
        var status = $(this).val(); // Get the selected status
        var rowNumber = $(this).closest('tr').find('td:first').text(); // Get the row number (first cell value)

        // Display an alert or perform an action
        alert('Status for row ' + rowNumber + ' changed to ' + status);

        // Here, you could send an AJAX request to save the status change, for example
    });
});


//close Notes Deadline Action
function closeNotesDeadlineAction( noteid, noteuniqueid) {
    var conf = confirm('Are you sure, you want to close this note deadline.');
    if(conf){
        if(noteid == '' && noteuniqueid == '') {
            alert('Please select note to close the deadline.');
            return false;
        } else {
            $('.popuploader').show();
            $.ajax({
                type:'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:"{{URL::to('/')}}/admin/update-task-completed",
                data:{'id': noteid,'unique_group_id':noteuniqueid},
                success:function(resp) {
                    $('.popuploader').hide();
                    location.reload();
                }
            });
        }
    } else{
        $('.popuploader').hide();
    }
}
</script>

<style>
.custom-select {
    border-radius: 10px; /* Adjust the value for the desired curvature */
    padding: 8px; /* Optional: Add padding for better visual appearance */
    border: 1px solid #ced4da; /* Optional: Adjust the border color if needed */
}

</style>

<script>

jQuery(document).ready(function($){


    $(".invitesselects2").select2({
        dropdownParent: $("#create_appoint")
    });

	$(document).delegate('#create_task', 'click', function(){
		$('#create_task_modal').modal('show');
		$('.cleintselect2').select2({
			dropdownParent: $('#create_task_modal .modal-content'),
		});
	});


	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){

				$('.taskview').html(responses);
			}
		});
	});




});
</script>
@endsection
