<!-- Add Appointment Modal -->
<div class="modal fade add_appointment custom_modal" id="create_applicationappoint" tabindex="-1" role="dialog" aria-labelledby="create_appointModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appointModalLabel">Add Appointment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-appointment')}}" name="appliappointform" id="appliappointform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" id="type" name="type" value="application">
				<input type="hidden" id="appointid" name="noteid" value="">
				<input type="hidden"  name="atype" value="application">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
						$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span>{{@Auth::user()->first_name}}</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<input type="text" name="client_name" value="{{ @$fetchedData->first_name.' '.@$fetchedData->last_name }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Name" readonly="readonly">
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselect2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									foreach($timelist as $tlist){
									?>
									<option value="<?php echo $tlist; ?>" <?php if($tlist == 'Australia/Melbourne'){ echo "selected"; } ?>><?php echo $tlist; ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group">
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{!! html()->text('appoint_date')->class('form-control datepicker')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Select Date') !!}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group">
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									{!! html()->time('appoint_time')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Select Time') !!}
								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								{!! html()->text('title')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Title') !!}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invitees">Invitees</label>
								<select class="form-control select2" name="invitees">
									<option value="">Select Invitees</option>
									<?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="{{$holist->id}}">{{$holist->first_name}} {{$holist->last_name}} ({{$holist->email}})</option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appliappointform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Edit Date & Time Modal -->
<div class="modal fade custom_modal" id="edit_datetime_modal" tabindex="-1" role="dialog" aria-labelledby="editDatetimeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editDatetimeModalLabel">Edit Date & Time</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/update-note-datetime')}}" name="edit_datetime_form" id="edit_datetime_form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="note_id" id="edit_note_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="edit_datetime">Date & Time <span class="span_req">*</span></label>
								<input type="text" class="form-control" id="edit_datetime" name="datetime" data-valid="required" readonly>
								<span class="custom-error datetime_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" id="save_datetime_btn" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Send Text Message Confirmation Modal -->
<div class="modal fade" id="notPickedCallModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure want to send text message to this user?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="messageText" rows="10" style="height: 130px !important;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary sendMessage">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Convert Activity to Note Modal -->
<div id="convertActivityToNoteModal" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="convertActivityToNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convertActivityToNoteModalLabel">Convert Activity Into Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" name="convertActivityToNoteForm" id="convertActivityToNoteForm" action="{{URL::to('/admin/convert-activity-to-note')}}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="activity_id" id="convert_activity_id" value="">
                    <input type="hidden" name="client_id" id="convert_client_id" value="">
                    
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="client_matter_id">Select Client Matter <span class="span_req">*</span></label>
                                <select class="form-control" name="client_matter_id" id="convert_client_matter_id" data-valid="required">
                                    <option value="">Select Client Matter</option>
                                    <!-- Client matters will be populated dynamically via JavaScript -->
                                </select>
                                @if ($errors->has('client_matter_id'))
                                    <span class="custom-error" role="alert">
                                        <strong>{{ @$errors->first('client_matter_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="note_type">Type <span class="span_req">*</span></label>
                                <select class="form-control" name="note_type" id="convert_note_type" data-valid="required">
                                    <option value="">Please Select</option>
                                    <option value="Call">Call</option>
                                    <option value="Email">Email</option>
                                    <option value="In-Person">In-Person</option>
                                    <option value="Others">Others</option>
                                    <option value="Attention">Attention</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="note_description">Note Description</label>
                                <textarea class="form-control summernote-simple" name="note_description" id="convert_note_description" rows="4" readonly></textarea>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-12 col-lg-12">
                            <button type="submit" class="btn btn-primary">Convert to Note</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

