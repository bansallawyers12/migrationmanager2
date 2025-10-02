<!-- Appliation Modal -->
<div class="modal fade add_appliation custom_modal"  tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Add Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/saveapplication')}}" name="applicationform" id="addapplicationformform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                <input type="hidden" name="client_matter_id" id="hidden_client_matter_id_latest" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow applicationselect2" id="workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									@foreach(\App\Models\Workflow::all() as $wlist)
										<option value="{{$wlist->id}}">{{$wlist->name}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="partner_branch">Select Partner & Branch <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control partner_branch partner_branchselect2" id="partner" name="partner_branch">
									<option value="">Please Select a Partner & Branch</option>
								</select>
								<span class="custom-error partner_branch_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="product">Select Product</label>
								<select data-valid="required" class="form-control product approductselect2" id="product" name="product">
									<option value="">Please Select a Product</option>

								</select>
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('applicationform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Appliation Modal -->
<div class="modal fade custom_modal" id="discon_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Discontinue Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/discontinue_application')}}" name="discontinue_application" id="discontinue_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="diapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Discontinue Reason <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow" id="workflow" name="workflow">
									<option value="">Please Select</option>
									<option value="Change of Application">Change of Application</option>
									<option value="Error by Team Member">Error by Team Member</option>
									<option value="Financial Difficulties">Financial Difficulties</option>
									<option value="Loss of competitor">Loss of competitor</option>
									<option value="Other Reasons">Other Reasons</option>

								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="">Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="note" id=""></textarea>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('discontinue_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="revert_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Revert Discontinued Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/revert_application')}}" name="revertapplication" id="revertapplication" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="revapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="">Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="note" id=""></textarea>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('revertapplication')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Interested Service Modal -->
<div class="modal fade add_interested_service custom_modal" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add Interested Services</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/interested-service')}}" name="inter_servform" autocomplete="off" id="inter_servform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflowselect2" id="intrested_workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									@foreach(\App\Models\Workflow::all() as $wlist)
										<option value="{{$wlist->id}}">{{$wlist->name}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_partner">Select Partner</label>
								<select data-valid="required" class="form-control partnerselect2" id="intrested_partner" name="partner">
									<option value="">Please Select a Partner</option>

								</select>
								<span class="custom-error partner_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_product">Select Product</label>
								<select data-valid="required" class="form-control productselect2" id="intrested_product" name="product">
									<option value="">Please Select a Product</option>

								</select>
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch">Select Branch</label>
								<select data-valid="required" class="form-control branchselect2" id="intrested_branch" name="branch">
									<option value="">Please Select a Branch</option>

								</select>
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="expect_start_date">Expected Start Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="expect_start_date" class="form-control datepicker" autocomplete="off" placeholder="Select Date">
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_start_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="expect_win_date">Expected Win Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="expect_win_date" class="form-control datepicker" data-valid="" autocomplete="off" placeholder="Select Date">
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_win_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('inter_servform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Appointment Modal -->
<div class="modal fade add_appointment custom_modal" id="create_appoint" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add Appointment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-appointment-book')}}" name="appointform" id="appointform" autocomplete="off" enctype="multipart/form-data">
				    @csrf
				    <input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                    <input type="hidden" name="client_unique_id" value="{{$fetchedData->client_id}}">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">

						</div>



						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group row align-items-center">
								<label for="client_name" class="col-sm-3 col-form-label">Client Reference No<span class="span_req">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="client_name" value="{{ @$fetchedData->client_id }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Reference" readonly>
                                </div>
                            </div>
						</div>


                        <input type="hidden" name="timezone" value="Australia/Melbourne">

                        <div class="col-12 col-md-12 col-lg-12 nature_of_enquiry_row" id="nature_of_enquiry">
							<div class="form-group row align-items-center">
								<label for="noe_id" class="col-sm-3 col-form-label">Nature of Enquiry<span class="span_req">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control enquiry_item" name="noe_id" data-valid="required">
                                        <option value="">Select</option>
                                        @foreach(\App\Models\NatureOfEnquiry::where('status',1)->get() as $enquiry)
                                            <option value="{{$enquiry->id}}">{{$enquiry->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12 services_row" id="services" style="display: none;">
							<div class="form-group">
								<label for="service_id">Services <span class="span_req">*</span></label>
                                @foreach(\App\Models\BookService::where('status',1)->get() as $bookservices)
                                    <div class="services_item_header" id="serviceval_{{$bookservices->id}}">
                                        <div class="services_item_title">
                                            <input type="radio" class="services_item" name="radioGroup"  value="{{$bookservices->id}}">
                                            <div class="services_item_img" style="display:inline-block;margin-left: 10px;">
                                                <img class="" style="width: 80px;height:80px;" src="{{asset('img/service_img')}}/{{$bookservices->image}}">
                                            </div>
                                            <span class="services_item_title_span">{{$bookservices->title}} <br/>{{$bookservices->duration}} minutes</span>
                                            <span class="services_item_price"> {{$bookservices->price}}</span>
                                            <span class="services_item_description">{{$bookservices->description}}</span>
                                        </div>
                                    </div>
                                @endforeach
                                <input type="hidden"  id="service_id" name="service_id" value="">
                            </div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12 appointment_row" id="appointment_details" style="display: none;">
                            <div class="form-group inperson_address_cls">
                                <label for="inperson_address" class="heading_title">Location</label>
                                <div class="inperson_address_header" id="inperson_address_1">
                                    <label class="inperson_address_title">
                                        <input type="radio" class="inperson_address" name="inperson_address" data-val="1" value="1">
                                        <div class="inperson_address_title_span">
                                            ADELAIDE<br/><span style="font-size: 10px;">(Unit 5 5/55 Gawler Pl, Adelaide SA 5000)</span>
                                        </div>
                                    </label>

                                    <label class="inperson_address_title">
                                        <input type="radio" class="inperson_address" name="inperson_address" data-val="2" value="2">
                                        <div class="inperson_address_title_span">
                                            MELBOURNE<br/><span style="font-size: 10px;">(Next to Flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)</span>
                                        </div>
                                    </label>
                                </div>

                                <style>
                                    .inperson_address_header {
                                        display: flex;
                                        align-items: center;
                                        gap: 20px; /* Adjust spacing between radio options */
                                        flex-wrap: nowrap; /* Ensures everything stays in one line */
                                    }

                                    .inperson_address_title {
                                        display: flex;
                                        align-items: center;
                                        gap: 8px; /* Space between radio button and text */
                                        white-space: nowrap; /* Prevents text from breaking into multiple lines */
                                    }

                                    .inperson_address_title_span {
                                        display: inline-block;
                                        color: #828F9A;
                                    }
                                    /* Mobile Devices: Stack items vertically */
                                    @media (max-width: 768px) {
                                        .inperson_address_header {
                                            display: inline;
                                        }
                                    }
                                </style>
                            </div>

                            <div class="form-group row align-items-center appointment_details_cls" style="display: none;">
								<div class="col-12 col-md-6 col-lg-6">
									<label for="appointment_details" class="heading_title">Appointment details <span class="span_req">*</span></label>
									<select class="form-control appointment_item" name="appointment_details" data-valid="required">
										<option value="">Select</option>
										<option value="phone"> Phone</option>
										<option value="in_person">In person</option>
										<option value="zoom_google_meeting" style="display: none;">Zoom / Google Meeting</option>
									</select>
								</div>

								<div class="col-12 col-md-6 col-lg-6">
									<label for="preferred_language" class="heading_title">Preferred Language <span class="span_req">*</span></label>
									<select class="form-control preferred_language" name="preferred_language" data-valid="required">
										<option value="">Select</option>
										<option value="Hindi"> Hindi</option>
										<option value="English">English</option>
										<option value="Punjabi">Punjabi</option>
									</select>
								</div>
                            </div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12 row info_row" id="info" style="display: none;">
							<div class="tab_body">
                                <div class="row">
									<div class="col-12 col-md-12 col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="description" class="col-sm-3 col-form-label">Details Of Enquiry <span class="span_req">*</span></label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control description" placeholder="Enter Details Of Enquiry" name="description" data-valid="required"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="description" class="col-sm-3 col-form-label">Date & Time <span class="span_req">*</span></label>
                                            <div class="col-sm-9">
                                                <span style="float:right;">
                                                    <input type="checkbox" name="slot_overwrite" id="slot_overwrite" value="0"> Slot Overwrite
                                                    <input type="hidden" name="slot_overwrite_hidden" id="slot_overwrite_hidden" value="0">
                                                </span>

                                                <div style="height:205px;">
                                                    <div style="width:37%;float: left;">
                                                        <div id='datetimepicker' class="datePickerCls"></div>
                                                    </div>
                                                    <div class="timeslotDivCls" style="width: 60%;float: right;/*border-left: 1px solid #E3EAF3;*/">
                                                        <div class="showselecteddate" style="font-size: 14px;text-align: center; padding: 5px 0 3px;border-bottom: 1px solid #E3EAF3;color: #0d0d0f !important;font-weight: bold;"></div>
                                                        <div class="timeslots" style="overflow:scroll !important;height:166px;"></div>
                                                    </div>


                                                    <div class="slotTimeOverwriteDivCls" style="display: none;">
														<?php
                                                        if (!function_exists('generateTimeDropdown')) {
                                                            function generateTimeDropdown($interval = 15) {
                                                                $start = new DateTime('00:00');
                                                                $end = new DateTime('23:45'); // Set the end time to 11:45 PM

                                                                $intervalDuration = new DateInterval('PT' . $interval . 'M');
                                                                $times = new DatePeriod($start, $intervalDuration, $end);

                                                                echo '<select class="slot_overwrite_time_dropdown" style="margin-left: 50px;margin-top: 50px;">';
                                                                echo '<option value="">Select Time</option>';
                                                                foreach ($times as $time) {
                                                                    // Calculate the end time for each option
                                                                    $endTime = clone $time;
                                                                    $endTime->add($intervalDuration);

                                                                    // Format both start and end times for display
                                                                    echo '<option value="' . $time->format('g:i A') . ' - ' . $endTime->format('g:i A') . '">';
                                                                    echo $time->format('g:i A') . ' - ' . $endTime->format('g:i A');
                                                                    echo '</option>';

                                                                    //echo '<option value="' . $time->format('g:i A') . '">' . $time->format('g:i A') . '</option>';
                                                                }
                                                                echo '</select>';
                                                            }
                                                        }

                                                        generateTimeDropdown(15); // 15-minute interval
                                                        ?>
                                                    </div>
                                                </div>
                                                <input type="hidden"  id="timeslot_col_date" name="appoint_date" value=""  >
                                                <input type="hidden"  id="timeslot_col_time" name="appoint_time" value=""  >
                                                <span class="timeslot_col_date_time" role="alert" style="display: none;color:#f00;">Date and Time is required.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col-12 col-md-12 col-lg-12" id="promo_code_used" style="display: none;">
								<div class="form-group row align-items-center">
									<label for="promo_code" class="col-sm-3 col-form-label">Promo Code</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="promo_code" placeholder="Enter Promo Code" name="promocode_used" />
										<input type="hidden" name="promocode_id" id="promocode_id" value="">
									</div>
								</div>
								<span id="promo_msg" style="display: none;"></span>
							</div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appointform')" type="button" class="btn btn-primary" id="appointform_save">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Update note Modal -->
<div class="modal fade custom_modal" id="create_note" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-note')}}" name="notetermform" autocomplete="off" id="notetermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="noteid" value="">
				<input type="hidden" name="mailid" value="0">
				<input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="task_group">Type <span class="span_req">*</span></label>
								<select name="task_group" class="form-control" data-valid="required" id="noteType">
								    <option value="">Please Select Note</option>
								    <option value="Call">Call</option>
								    <option value="Email">Email</option>
								    <option value="In-Person">In-Person</option>
								    <option value="Others">Others</option>
								    <option value="Attention">Attention</option>
								</select>
								<!-- Container for additional inputs -->
						        <div id="additionalFields"></div>
								<span class="custom-error task_group_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea  class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<!--<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label class="d-block" for="">Related To</label>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_contact" value="Contact" name="related_to_note" checked>
									<label style="padding-left: 6px;" class="form-check-label" for="note_contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_partner" value="Partner" name="related_to_note">
									<label style="padding-left: 6px;" class="form-check-label" for="note_partner">Partner</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_application" value="Application" name="related_to_note">
									<label style="padding-left: 6px;" class="form-check-label" for="note_application">Application</label>
								</div>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control contact_name js-data-example-ajaxcc" name="contact_name[]">


								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>-->
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Create note Modal -->
<div class="modal fade custom_modal" id="create_note_d" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
                <button type="button" class="btn btn-primary btn-block float-right btn-assignuser" data-container="body" data-role="popover" data-placement="bottom" data-html="true" style="border-radius:4px !important;width:70px;display:inline-block;margin-left:30px;margin-top:-5px;"> Action</button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-note')}}" name="notetermform_n" autocomplete="off" id="notetermform_n" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id" value="{{$fetchedData->id}}">
                    <input type="hidden" name="noteid" value="">
                    <input type="hidden" name="mailid" value="0">
                    <input type="hidden" name="vtype" value="client">
					<div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<?php
                                $general_matter_list_arr = DB::table('client_matters')
                                ->select('client_matters.id','client_matters.client_unique_matter_no')
                                ->where('client_matters.client_id',@$fetchedData->id)
                                ->where('client_matters.sel_matter_id',1)
                                ->get();
                                //dd( $general_matter_list_arr);
                                if( !empty($general_matter_list_arr) && count($general_matter_list_arr)>0 ){ ?>
                                    <label for="matter_id">Select Matter <span class="span_req">*</span></label>
                                    @foreach($general_matter_list_arr as $generallist)
                                        <div class="form-check">
                                            <input class="form-check-input general_matter_checkbox" type="checkbox" name="matter_id" id="general_matter_checkbox_{{$generallist->id}}" value="{{$generallist->id}}">
                                            <label class="form-check-label" for="general_matter_checkbox_{{$generallist->id}}">General Matter ({{@$generallist->client_unique_matter_no}})</label>
                                        </div>
                                    @endforeach
                                <?php
                                }
                                ?>

                                <label class="form-check-label" for="">Select any client matter</label>
                                <select name="matter_id" id="matter_id" class="form-control">
								    <option value="">Select Client Matters</option>
                                    <?php
                                    $matter_list_arr = DB::table('client_matters')
                                    ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                    ->select('client_matters.id','client_matters.client_unique_matter_no','matters.title')
                                    ->where('client_matters.matter_status',1)
                                    ->where('client_matters.client_id',@$fetchedData->id)
                                    ->where('matters.status',1)
                                    ->where('client_matters.sel_matter_id','!=',1)
                                    ->get();
                                    ?>
								    @foreach($matter_list_arr as $matterlist)
                                        <option value="{{$matterlist->id}}">{{@$matterlist->title}}({{@$matterlist->client_unique_matter_no}})</option>
                                    @endforeach
								</select>
								<span class="custom-error matter_id_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <input type="hidden" name="title" value="Matter Discussion">

                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="task_group">Type<span class="span_req">*</span></label>
								<select name="task_group" class="form-control" data-valid="required" id="noteType">
                                    <option value="">Please Select</option>
                                    <option value="Call">Call</option>
                                    <option value="Email">Email</option>
                                    <option value="In-Person">In-Person</option>
                                    <option value="Others">Others</option>
                                    <option value="Attention">Attention</option>
                                </select>

                                <!-- Container for additional inputs -->
						        <div id="additionalFields"></div>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea  class="summernote-simple" id="note_description" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notetermform_n')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="view_note" tabindex="-1" role="dialog" aria-labelledby="view_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="note_col">
					<div class="note_content">
						<h5></h5>
						<p></p>
						<div class="extra_content">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="view_application_note" tabindex="-1" role="dialog" aria-labelledby="view_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="note_col">
					<div class="note_content">
						<h5></h5>
						<p></p>
						<div class="extra_content">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Task Modal -->
<div class="modal fade custom_modal" id="opentaskview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content taskview">

		</div>
	</div>
</div>
<div class="modal fade create_task custom_modal" id="opentaskmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="#" name="taskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="mailid" value="">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Title">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="category">Category <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control category select2" name="category">
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
								<label for="assignee">Assignee</label>
								<select data-valid="" class="form-control assignee select2" name="assignee">
									<option value="">Select</option>
									<?php
									$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="{{$holist->id}}">{{$holist->first_name}} ({{$holist->email}})</option>
										<?php
									}
									?>
								</select>
								<span class="custom-error assignee_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="priority">Priority</label>
								<select data-valid="" class="form-control priority select2" name="priority">
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
									<input type="text" name="due_date" value="" class="form-control datepicker" data-valid="" autocomplete="off" placeholder="Select Date">
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
									<input type="time" name="due_time" class="form-control" data-valid="" autocomplete="off" placeholder="Select Time">
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
						<div class="col-12 col-md-12 col-lg-12 ifselecttask">
							<div class="form-group">
								<label class="d-block" for="related_to">Related To</label>
								<div class="form-check form-check-inline">
									<input  type="radio" id="contact" value="Contact" name="related_to" checked>
									<label style="padding-left:6" class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="partner" value="Partner" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="partner">Partner</label>
								</div>
							{{--	<div class="form-check form-check-inline">
									<input class="" type="radio" id="application" value="Application" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="application">Application</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="internal" value="Internal" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="internal">Internal</label>
								</div>--}}
								@if ($errors->has('related_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('related_to') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_contact ifselecttask">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control contact_name js-data-example-ajaxcontact" name="contact_name[]">

								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_partner ifselecttask">
							<div class="form-group">
								<label for="partner_name">Partner Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control partner_name select2" name="partner_name">
									<option value="">Choose Partner</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error partner_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control client_name select2" name="client_name">
									<option value="">Choose Client</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<select data-valid="" class="form-control application select2" name="application">
									<option value="">Choose Application</option>
									<option value="Demo">Demo</option>
								</select>
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="stage">Stage <span class="span_req">*</span></label>
								<select data-valid="" class="form-control stage select2" name="stage">
									<option value="">Choose Stage</option>
									<option value="Stage 1">Stage 1</option>
								</select>
								<span class="custom-error stage_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="followers">Followers <span class="span_req">*</span></label>
								<select data-valid="" multiple class="form-control followers  select2" name="followers">

									<?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="{{$holist->id}}">{{$holist->first_name}} {{$holist->last_name}} ({{$holist->email}})</option>
										<?php
									}
									?>
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
									<input type="file" name="attachments" class="custom-file-input" id="attachments">
									<label class="custom-file-label showattachment" for="attachments">Choose file</label>
								</div>
								<span class="custom-error attachments_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
							<button onclick="customValidate('taskform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Education Modal -->
<div class="modal fade create_education custom_modal" tabindex="-1" role="dialog" aria-labelledby="create_educationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Education</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/saveeducation')}}" name="educationform" id="educationform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="degree_title">Degree Title <span class="span_req">*</span></label>
								<input type="text" name="degree_title" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Degree Title">
								<span class="custom-error degree_title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="degree_level">Degree Level <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control degree_level select2" name="degree_level">
									<option value="">Please Select Degree Level</option>
									<option value="Bachelor">Bachelor</option>
									<option value="Certificate">Certificate</option>
									<option value="Diploma">Diploma</option>
									<option value="High School">High School</option>
									<option value="Master">Master</option>
								</select>
								<span class="custom-error degree_level_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="institution">Institution <span class="span_req">*</span></label>
								<input type="text" name="institution" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Institution">
								<span class="custom-error institution_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="course_start">Course Start</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="course_start" class="form-control datepicker" data-valid="" autocomplete="off" placeholder="Select Date">
									@if ($errors->has('course_start'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('course_start') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="course_end">Course End</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="course_end" class="form-control datepicker" data-valid="" autocomplete="off" placeholder="Select Date">
									@if ($errors->has('course_end'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('course_end') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject_area">Subject Area</label>
								<select data-valid="" class="form-control subject_area select2" id="subjectlist" name="subject_area">
									<option value="">Please Select Subject Area</option>
									<?php
									foreach(\App\SubjectArea::all() as $sublist){
										?>
										<option value="{{$sublist->id}}">{{$sublist->name}}</option>
										<?php
									}
									?>
								</select>
								<span class="custom-error subject_area_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject">Subject</label>
								<select data-valid="" class="form-control subject select2" id="subject" name="subject">
									<option value="">Please Select Subject</option>
								</select>
								<span class="custom-error subject_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label class="d-block" for="academic_score">Academic Score</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="percentage" value="%" name="academic_score_type" checked>
									<label class="form-check-label" for="percentage">Percentage</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="GPA" value="GPA" name="academic_score_type">
									<label class="form-check-label" for="GPA">GPA</label>
								</div>
								<input type="number" name="academic_score" class="form-control" data-valid="" autocomplete="off" step="0.01">
								<span class="custom-error academic_score_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('educationform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="opencommissionmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Commission Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-invoice')}}" name="noteinvform" autocomplete="off" id="noteinvform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
						$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="invoice_type">Choose invoice:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="net_invoice" value="1" name="invoice_type" checked>
									<label class="form-check-label" for="net_invoice">Net Claim Invoice</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="gross_invoice" value="2" name="invoice_type">
									<label class="form-check-label" for="gross_invoice">Gross Claim Invoice</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<input type="text" name="client" value="{{ @$fetchedData->first_name.' '.@$fetchedData->last_name }}" class="form-control" data-valid="required" autocomplete="off" placeholder="">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Application <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="application">
									<option value="">Select</option>
									@foreach(\App\Models\Application::where('client_id',$fetchedData->id)->get() as $aplist)
									<?php
									$productdetail = \App\Models\Product::where('id', $aplist->product_id)->first();
				$partnerdetail = \App\Models\Partner::where('id', $aplist->partner_id)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $aplist->branch)->first();
				$workflow = \App\Models\Workflow::where('id', $aplist->workflow)->first();
									?>
										<option value="{{$aplist->id}}">{{@$productdetail->name}} ({{@$partnerdetail->partner_name}})</option>
									@endforeach
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('noteinvform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="opengeneralinvoice" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">General Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-invoice')}}" name="notegetinvform" autocomplete="off" id="notegetinvform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
						$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="invoice_type">Choose invoice:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="net_invoice" value="3" name="invoice_type" checked>
									<label class="form-check-label" for="net_invoice">Client Invoice</label>
								</div>

								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<input type="text" name="client" value="{{ @$fetchedData->first_name.' '.@$fetchedData->last_name }}" class="form-control" data-valid="required" autocomplete="off" placeholder="">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Service <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="application">
									<option value="">Select</option>
									@foreach(\App\Models\Application::where('client_id',$fetchedData->id)->groupby('workflow')->get() as $aplist)
									<?php

				$workflow = \App\Models\Workflow::where('id', $aplist->workflow)->first();
									?>
										<option value="{{$workflow->id}}">{{$workflow->name}}</option>
									@endforeach
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notegetinvform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addpaymentmodal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
	<form method="post" action="{{ URL::to('admin/invoice/payment-store') }}" name="ajaxinvoicepaymentform" autocomplete="off" enctype="multipart/form-data" id="ajaxinvoicepaymentform">
	@csrf
	<input type="hidden" value="" name="invoice_id" id="invoice_id">
	<input type="hidden" value="true" name="is_ajax" id="">
	<input type="hidden" value="{{$fetchedData->id}}" name="client_id" id="">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Payment Details</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">

				<div class="payment_field">
					<div class="payment_field_row">
						<div class="payment_field_col payment_first_step">
							<div class="field_col">
								<div class="label_input">
									<input data-valid="required" type="number" name="payment_amount[]" placeholder="" class="paymentAmount" />
									<div class="basic_label">AUD</div>
								</div>
							</div>

							<div class="field_col">
								<select name="payment_mode[]" class="form-control">
									<option value="Cheque">Cheque</option>
									<option value="Cash">Cash</option>
									<option value="Credit Card">Credit Card</option>
									<option value="Bank Transfers">Bank Transfers</option>
								</select>
							</div>
							<div class="field_col">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="text" name="payment_date[]" placeholder="" class="datepicker form-control" />
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
							<div class="field_remove_col">
								<a href="javascript:;" class="remove_col"><i class="fa fa-times"></i></a>
							</div>
						</div>
					</div>
					<div class="add_payment_field">
						<a href="javascript:;"><i class="fa fa-plus"></i> Add New Line</a>
					</div>
					<div class="clearfix"></div>
					<div class="invoiceamount">
						<table class="table">
							<tr>
								<td><b>Invoice Amount:</b></td>
								<td class="invoicenetamount"></td>
								<td><b>Total Due:</b></td>
								<td class="totldueamount" data-totaldue=""></td>
							</tr>

						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="customValidate('ajaxinvoicepaymentform')" class="btn btn-primary" >Save & Close</button>
				<button type="button" class="btn btn-primary">Save & Send Receipt</button>
			  </div>
		</div>
		</form>
	</div>
</div>

<div class="modal fade custom_modal" id="create_applicationnote" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-app-note')}}" name="appnotetermform" autocomplete="off" id="appnotetermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="noteid" id="noteid" value="">
				<input type="hidden" name="type" id="type" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Title">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appnotetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Appointment Modal -->
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

<!-- Checklist Modal -->
<div class="modal fade custom_modal" id="create_checklist" tabindex="-1" role="dialog" aria-labelledby="create_checklistModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="checklistModalLabel">Add New Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-checklists')}}" name="checklistform" id="checklistform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" id="checklistapp_id" name="app_id" value="{{$fetchedData->id}}">
				<input type="hidden" id="checklist_type" name="type" value="">
				<input type="hidden" id="checklist_typename" name="typename" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="document_type">Document Type <span class="span_req">*</span></label>
								<select class="form-control " id="document_type" name="document_type" data-valid="required">
									<option value="">Please Select Document Type</option>
									<?php foreach(\App\Models\Checklist::all() as $checklist){ ?>
									<option value="{{$checklist->name}}">{{$checklist->name}}</option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" id="checklistdesc" name="description" placeholder="Description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="form-check form-check-inline">
									<input value="1" class="" type="checkbox" value="Allow clients to upload documents from client portal" name="allow_upload_docu">
									<label style="padding-left: 8px;" class="form-check-label" for="allow_upload_docu">Allow clients to upload documents from client portal</label>
								</div>
								<div class="form-check form-check-inline">
									<input value="1" class="" type="checkbox" value="Make this as mandatory inorder to proceed next stage" name="proceed_next_stage">
									<label style="padding-left: 8px;" class="form-check-label" for="proceed_next_stage">Make this as mandatory inorder to proceed next stage</label>
								</div>
							</div>
						</div>
					</div>
					<div class="due_date_sec">
						<a href="javascript:;" class="btn btn-primary due_date_btn"><i class="fa fa-plus"></i> Add Due Date</a>
						<input type="hidden" value="0" class="checklistdue_date" name="due_date">
						<div class="due_date_col">
							<div class="row">
								<div class="col-12 col-md-6 col-lg-6">
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
								<div class="col-12 col-md-1 col-lg-1 remove_col">
									<a href="javascript:;" class="remove_btn"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('checklistform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal paymentschedule" id="create_paymentschedule" tabindex="-1" role="dialog" aria-labelledby="create_paymentscheduleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheduleModalLabel">Add Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-appointment')}}" name="paymentform" id="paymentform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name</label>
								{!! html()->text('client_name')->class('form-control')->attribute('autocomplete', 'off')->attribute('data-valid', '')->attribute('placeholder', 'Enter Client Name') !!}
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="application">Application</label>
								{!! html()->text('application')->class('form-control')->attribute('autocomplete', 'off')->attribute('data-valid', '')->attribute('placeholder', 'Enter Application') !!}
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_name">Installment Name <span class="span_req">*</span></label>
								{!! html()->text('installment_name')->class('form-control')->attribute('autocomplete', 'off')->attribute('data-valid', 'required')->attribute('placeholder', 'Enter Installment Name') !!}
								<span class="custom-error installment_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_date">Installment Date <span class="span_req">*</span></label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{!! html()->text('installment_date')->class('form-control datepicker')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Select Date') !!}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="fees_type_sec">
								<div class="fee_type_row">
									<div class="custom_type_col">
										<div class="feetype_field">
											<div class="form-group">
												<label for="fee_type">Fee Type <span class="span_req">*</span></label>
											</div>
										</div>
										<div class="feeamount_field">
											<div class="form-group">
												<label for="fee_amount">Fee Amount <span class="span_req">*</span></label>
											</div>
										</div>
										<div class="commission_field">
											<div class="form-group">
												<label for="commission_percent">Commission %</label>
											</div>
										</div>
										<div class="remove_field">
											<div class="form-group">
											</div>
										</div>
									</div>
									<div class="fees_type_col custom_type_col">
										<div class="feetype_field">
											<div class="form-group">
												<select class="form-control select2" name="fee_type" data-valid="required">
													<option value="">Select Fee Type</option>
													<option value="Accommodation Fee">Accommodation Fee</option>
													<option value="Administration Fee">Administration Fee</option>
													<option value="Airline Ticket">Airline Ticket</option>
													<option value="Airport Transfer Fee">Airport Transfer Fee</option>
													<option value="Application Fee">Application Fee</option>
													<option value="Bond">Bond</option>
													<option   value="Tution Fee">Tution Fee</option>
													<option   value="Tution Fee">Tution Fee</option>
												</select>
											</div>
										</div>
										<div class="feeamount_field">
											<div class="form-group">
												{!! html()->text('fee_amount')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '0.00') !!}
											</div>
										</div>
										<div class="commission_field">
											<div class="form-group">
												{!! html()->text('commission_percent')->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', '0.00') !!}
											</div>
										</div>
										<div class="remove_field">
											<a href="javascript:;" class="remove_btn"><i class="fa fa-trash"></i></a>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="discount_row">
									<div class="discount_col custom_type_col">
										<div class="feetype_field">
											<div class="form-group">
												<input class="form-control" placeholder="Discount" disabled />
											</div>
										</div>
										<div class="feeamount_field">
											<div class="form-group">
												{!! html()->text('discount_amount')->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', '0.00') !!}
											</div>
										</div>
										<div class="commission_field">
											<div class="form-group">
												{!! html()->text('dispcunt_commission_percent')->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', '0.00') !!}
											</div>
										</div>
										<div class="remove_field">
											<a href="javascript:;" class="remove_btn"><i class="fa fa-trash"></i></a>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="divider"></div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="add_fee_type">
								<a href="javascript:;" class="btn btn-outline-primary fee_type_btn"><i class="fa fa-plus"></i> Add Fee</a>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 text-right">
							<div class="total_fee">
								<h4>Total Fee (USD)</h4>
								<span>11.00</span>
							</div>
							<div class="net_fee">
								<span class="span_label">Net Fee</span>
								<span class="span_value">0.00</span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="divider"></div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="schedule_title">
								<h4>Setup Invoice Scheduling</h4>
							</div>
							<span class="schedule_note"><i class="fa fa-explanation-circle"></i> Schedule your Invoices by selecting an Invoice date for this installment.</span>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{!! html()->text('invoice_date')->class('form-control datepicker')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Select Date') !!}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" value="Allow clients to upload documents from client portal" name="allow_upload_docu">
									<label class="form-check-label" for="allow_upload_docu">Auto Invoicing</label>
								</div>
								<span class="schedule_note"><i class="fa fa-explanation-circle"></i> Enabling Auto Invoicing will automatically create unpaid invoices at above stated Invoice Date.</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="fee_type">Invoice Type <span class="span_req">*</span></label>
								<select class="form-control select2" name="fee_type" data-valid="required">
									<option value="">Select Invoice Type</option>
									<option value="Net Claim">Net Claim</option>
									<option value="Gross Claim">Gross Claim</option>
								</select>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="divider"></div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('paymentform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="applicationemailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="appkicationsendmail" id="appkicationsendmail" action="{{URL::to('/admin/application-sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" id="type" name="type" value="application">
				<input type="hidden" id="appointid" name="noteid" value="">
				<input type="hidden"  name="atype" value="application">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}

									?>
								</select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To <span class="span_req">*</span></label>
								<input type="text" readonly class="form-control" name="to" value="{{$fetchedData->first_name}} {{$fetchedData->last_name}}">
								<input type="hidden" class="form-control" name="to" value="{{$fetchedData->email}}">
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxccapp" name="email_cc[]"></select>

								@if ($errors->has('email_cc'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_cc') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
								<select data-valid="" class="form-control select2 selectapplicationtemplate" name="template">
									<option value="">Select</option>
									@foreach(\App\Models\CrmEmailTemplate::all() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								{!! html()->text('subject')->class('form-control selectedappsubject')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Subject') !!}
								@if ($errors->has('subject'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subject') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appkicationsendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal paymentschedule" id="create_apppaymentschedule" tabindex="-1" role="dialog" aria-labelledby="create_paymentscheduleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheduleModalLabel">Payment Schedule Setup</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/setup-paymentschedule')}}" name="setuppaymentschedule" id="setuppaymentschedule" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="application_id" id="application_id" value="">
				<input type="hidden" name="is_ajax" id="is_ajax" value="true">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_date">Installment Date <span class="span_req">*</span></label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{!! html()->text('installment_date')->class('form-control datepicker')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Select Date') !!}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="row">
								<div class="col-md-12">
									<label for="installment_date">Installment Interval  <span class="span_req">*</span></label>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										{!! html()->text('installment_no')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '') !!}
									</div>
								</div>
								<div class="col-md-8">
									<div class="input-group">
										<select class="form-control" name="installment_intervel">
											<option value="">Select Intervel</option>
											<option value="Day">Day</option>
											<option value="Week">Week</option>
											<option value="Month">Month</option>
											<option value="Year">Year</option>

										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="clearfix"></div>
						<div class="divider"></div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="schedule_title">
								<h4>Setup Invoice Scheduling</h4>
							</div>
							<span class="schedule_note"><i class="fa fa-explanation-circle"></i> Schedule your Invoices by selecting an Invoice date for this installment.</span>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{!! html()->text('invoice_date')->class('form-control datepicker')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', 'Select Date') !!}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

					</div>
					<div class="clearfix"></div>
					<div class="divider"></div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('setuppaymentschedule')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal" id="editpaymentschedule" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Edit Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showeditmodule">

			</div>
		</div>
	</div>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade add_payment_schedule custom_modal" id="addpaymentschedule" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Add Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showpoppaymentscheduledata">

			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="opencreateinvoiceform" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Select Invoice Type:</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<form method="post" action="{{URL::to('/admin/create-invoice')}}" name="createinvoive"  autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="client_id">
				<input type="hidden" name="application" id="app_id">
				<input type="hidden" name="schedule_id" id="schedule_id">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="netclaim"><input id="netclaim" value="1" type="radio" name="invoice_type" > Net Claim</label>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="grossclaim"><input value="2" id="grossclaim" type="radio" name="invoice_type" > Gross Claim</label>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="geclaim"><input value="3" id="geclaim" type="radio" name="invoice_type" > Client General</label>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<button onclick="customValidate('createinvoive')" class="btn btn-info" type="button">Create</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="uploadmail" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Mail:</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<form method="post" action="{{URL::to('/admin/upload-mail')}}" name="uploadmail"  autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="maclient_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="">From <span class="span_req">*</span></label>
								<input type="text" data-valid="required" name="from" class="form-control">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="">To <span class="span_req">*</span></label>
								<input type="text" data-valid="required" name="to" class="form-control">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="">Subject <span class="span_req">*</span></label>
								<input type="text" data-valid="required" name="subject" class="form-control">
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea data-valid="required" class="summernote-simple selectedmessage" name="message"></textarea>

							</div>
						</div>

                        <div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<button onclick="customValidate('uploadmail')" class="btn btn-info" type="button">Create</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="openfileuploadmodal" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
				<style>
                #ddArea {height: 200px;border: 2px dashed #ccc;line-height: 200px;font-size: 20px;background: #f9f9f9;margin-bottom: 15px;}
                .drag_over {color: #000;border-color: #000;}
                .thumbnail {width: 100px;height: 100px;padding: 2px;margin: 2px;border: 2px solid lightgray;border-radius: 3px;float: left;}
                .d-none {display: none;}
				</style>
					<div class="col-md-8">
					<input type="hidden" class="checklisttype" value="">
					<input type="hidden" class="checklisttypename" value="">
					<input type="hidden" class="checklistid" value="">
					<input type="hidden" class="application_id" value="">
						<div id="ddArea" style="text-align: center;">
							Click or drag to upload new file from your device

							<a style="display: none;" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent ">

							</a>
						</div>

						<input type="file" class="d-none" id="selectfile" multiple />
					</div>
					</div class="col-md-4">
						<div id="showThumb">
							<ul>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Create Personal Docs Modal -->
<div class="modal fade create_education_docs custom_modal" id="openeducationdocsmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Add Personal Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-edudocchecklist')}}" name="edu_upload_form" id="edu_upload_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="doctype" value="personal">
                    <input type="hidden" name="doccategory" id="doccategory" value="">
                    <input type="hidden" name="folder_name" id="folder_name" value="">


                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="checklist">Select Checklist<span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="checklist[]" id="checklist" multiple>
									<option value="">Select</option>
									<?php
									$eduChkList = \App\Models\DocumentChecklist::where('status',1)->where('doc_type',1)->get();
									foreach($eduChkList as $edulist){
									?>
										<option value="{{$edulist->name}}">{{$edulist->name}}</option>
									<?php
									}
									?>
								</select>
								<span class="custom-error checklist_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
                    </div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('edu_upload_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Create Migration Docs Modal -->
<div class="modal fade create_migration_docs custom_modal" id="openmigrationdocsmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Add Visa Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-visadocchecklist')}}" name="mig_upload_form" id="mig_upload_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="doctype" value="visa">
                    <input type="hidden" name="client_matter_id" id="hidden_client_matter_id" value="">
                    <input type="hidden" name="folder_name" id="visa_folder_name" value="">

					<div class="row">
                        <div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="visa_checklist">Select Checklist<span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="visa_checklist[]" id="visa_checklist" multiple>
									<option value="">Select</option>
									<?php
									$visaChkList = \App\Models\DocumentChecklist::where('status',1)->where('doc_type',2)->get();
									foreach($visaChkList as $visalist){
									?>
										<option value="{{$visalist->name}}">{{$visalist->name}}</option>
									<?php
									}
									?>
								</select>
								<span class="custom-error visa_checklist_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
                    </div>

                    <div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('mig_upload_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Create Receipt  -->
<div class="modal fade custom_modal" id="createreceiptmodal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		  	<div class="modal-header">
				<h5 class="modal-title">Create Receipt</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
		    </div>

		  	<div class="modal-body">
				<!-- Radio Button Selection -->
				<div class="form-group">
			  		<label><strong>Select Report Type:</strong></label><br>
			  		<label class="mr-3">
						<input type="radio" name="receipt_type" value="client_receipt" checked> Client Funds Ledger
			  		</label>

			  		<label class="mr-3">
						<input type="radio" name="receipt_type" value="invoice_receipt"> Invoices Issued
			  		</label>

			  		<label class="mr-3">
						<input type="radio" name="receipt_type" value="office_receipt"> Direct Office Receipts
			  		</label>
				</div>

				<!-- Client Funds Ledger Form -->
				<form class="form-type" method="post" action="{{URL::to('/admin/clients/saveaccountreport')}}" name="client_receipt_form" autocomplete="off" id="client_receipt_form" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
					<input type="hidden" name="receipt_type" value="1">
                    <input type="hidden" name="client_ledger_balance_amount" id="client_ledger_balance_amount" value="">
                    <input type="hidden" name="client_matter_id" id="client_matter_id_ledger" value="">
					<div class="row">
						<div class="col-3 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<input type="text" name="client" class="form-control" data-valid="required" autocomplete="off" placeholder="" value="{{ $fetchedData->first_name.' '.$fetchedData->last_name }}">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                       	<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:12%;color: #34395e;">Trans. Date</th>
                                            <th style="width:12%;color: #34395e;">Entry Date</th>
                                            <th style="width:12%;color: #34395e;">Type</th>
                                            <th style="width:30%;color: #34395e;">Description</th>
                                            <th style="width:10%;color: #34395e;">Funds In (+)</th>
											<th style="width:10%;color: #34395e;">Funds Out (-)</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem">
                                        <tr class="clonedrow">
                                            <td>
                                                <input data-valid="required"  class="form-control report_date_fields" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control client_fund_ledger_type" name="client_fund_ledger_type[]" data-valid="required">
                                                    <option value="">Select</option>
                                                    <option value="Deposit">Deposit</option>
                                                    <option value="Fee Transfer">Fee Transfer</option>
                                                    <option value="Disbursement">Disbursement</option>
													<option value="Refund">Refund</option>
                                                </select>

                                                <select class="form-control invoice_no_cls"  name="invoice_no[]" style="display:none;margin-top: 5px;">
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control deposit_amount_per_row" name="deposit_amount[]" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\.\d{2}).*/g, '$1')" value="" readonly/>
                                            </td>

											<td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_per_row" name="withdraw_amount[]" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\.\d{2}).*/g, '$1')" value="" readonly/>
                                            </td>

                                            <td>
                                                <a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="width:72.5%;text-align:right;color: #34395e;">Totals</td>
                                            <td>
                                                <span class="total_deposit_amount_all_rows" style="color: #34395e;"></span>
                                            </td>
											<td colspan="2">
                                                <span class="total_withdraw_amount_all_rows" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">

                            <div class="upload_client_receipt_document" style="display:inline-block;">
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="doctype" value="client_receipt">
                                <span class="file-selection-hint" style="margin-left: 10px; color: #34395e;"></span>
                                <a href="javascript:;" class="btn btn-primary add-document-btn"><i class="fa fa-plus"></i> Add Document</a>
                                <input class="docclientreceiptupload" type="file" name="document_upload[]"/>

                            </div>
							<button onclick="customValidate('client_receipt_form')" type="button" class="btn btn-primary" style="margin:0px !important;">Save Report</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>

				<!-- Invoice Receipt Form -->
				<form class="form-type" method="post" action="{{URL::to('/admin/clients/saveinvoicereport')}}" name="invoice_receipt_form" autocomplete="off" id="invoice_receipt_form" style="display:none;">
					@csrf
					<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
					<input type="hidden" name="receipt_type" value="3">
					<input type="hidden" name="receipt_id" id="receipt_id" value="">
					<input type="hidden" name="function_type" id="function_type" value="">
                    <input type="hidden" name="client_matter_id" id="client_matter_id_invoice" value="">

					<div class="row">
						<div class="col-3 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<input type="text" name="client" class="form-control" data-valid="required" autocomplete="off" placeholder="" value="{{ $fetchedData->first_name.' '.$fetchedData->last_name }}">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <!--<div class="Invoic_no_cls" style="text-align: center;">
                                <b>Invoice No -
                                    <span class="unique_invoice_no"></span>
                                </b>
                                <input type="hidden" name="invoice_no" class="invoice_no" value="">
                            </div>-->
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:13%;color: #34395e;">Gst Incl.</th>
                                            <th style="width:5%;color: #34395e;">Payment Type</th>
                                            <th style="width:25%;color: #34395e;">Description</th>
                                            <th style="width:14%;color: #34395e;">Amount</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem_invoice">
                                        <tr class="clonedrow_invoice">
                                            <td>
                                                <input name="id[]" type="hidden" value="" />
                                                <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields_invoice" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control" name="gst_included[]">
                                                    <option value="">Select</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <select class="form-control payment_type_invoice_per_row" name="payment_type[]">
                                                    <option value="">Select</option>
                                                    <option value="Professional Fee">Professional Fee</option>
                                                    <option value="Department Charges">Department Charges</option>
                                                    <option value="Surcharge">Surcharge</option>
                                                    <option value="Disbursements">Disbursements</option>
                                                    <option value="Other Cost">Other Cost</option>
                                                    <option value="Discount">Discount</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\.\d{2}).*/g, '$1')" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_withdraw_amount_all_rows_invoice" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo_invoice"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">
                            <input type="hidden" name="save_type" class="save_type" value="">
                            <button onclick="customValidate('invoice_receipt_form','draft')" type="button" class="btn btn-primary" style="margin:0px !important;">Save Draft</button>
							<button onclick="customValidate('invoice_receipt_form','final')" type="button" class="btn btn-primary" style="margin:0px !important;">Save and Finalised</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>

				<!-- Office Receipt Form -->
				<form class="form-type"  method="post" action="{{URL::to('/admin/clients/saveofficereport')}}" name="office_receipt_form" autocomplete="off" id="office_receipt_form" style="display:none;">
					@csrf
					<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
					<input type="hidden" name="receipt_type" value="2">
                    <input type="hidden" name="client_matter_id" id="client_matter_id_office" value="">
					<div class="row">
						<div class="col-3 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<input type="text" name="client" class="form-control" data-valid="required" autocomplete="off" placeholder="" value="{{ $fetchedData->first_name.' '.$fetchedData->last_name }}">
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:15%;color: #34395e;">Invoice No</th>
                                            <th style="width:5%;color: #34395e;">Payment method</th>
                                            <th style="width:25%;color: #34395e;">Description</th>
                                            <th style="width:14%;color: #34395e;">Received</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem_office">
                                        <tr class="clonedrow_office">
                                            <td>
                                                <input data-valid="required"  class="form-control report_date_fields_office" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields_office" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control invoice_no_cls"  name="invoice_no[]" data-valid="required">
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" name="payment_method[]" data-valid="required" >
                                                    <option value="">Select</option>
													<option value="Cash">Cash</option>
                                                    <option value="Bank transfer">Bank transfer</option>
                                                    <option value="EFTPOS">EFTPOS</option>
                                                    <option value="Refund">Refund</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control total_deposit_amount_office" name="deposit_amount[]" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\.\d{2}).*/g, '$1')" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems_office" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_deposit_amount_all_rows_office" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo_office"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">
                            <div class="upload_office_receipt_document" style="display:inline-block;">
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="doctype" value="office_receipt">
                                <span class="file-selection-hint1" style="margin-right: 10px; color: #34395e;"></span>
                                <a href="javascript:;" class="btn btn-primary add-document-btn1"><i class="fa fa-plus"></i> Add Document</a>
                                <input class="docofficereceiptupload"  type="file" name="document_upload[]"/>
                            </div>

                            <button onclick="customValidate('office_receipt_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Save Report</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
		  	</div>
		</div>
	</div>
</div>

<!-- Create Adjust Invoice Receipt  -->
<div class="modal fade custom_modal" id="createadjustinvoicereceiptmodal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		  	<div class="modal-header">
				<h5 class="modal-title">Adjust Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
		    </div>

		  	<div class="modal-body">
				<!-- Invoice Receipt Form -->
				<form class="form-type" method="post" action="{{URL::to('/admin/clients/saveadjustinvoicereport')}}" name="adjust_invoice_receipt_form" autocomplete="off" id="adjust_invoice_receipt_form">
					@csrf
					<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
					<input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
					<input type="hidden" name="receipt_type" value="3">
					<input type="hidden" name="receipt_id" id="receipt_id" value="">
					<input type="hidden" name="function_type" id="function_type" value="add">

					<div class="row">
						<div class="col-3 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								{!! html()->text('client')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '') !!}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="Invoic_no_cls" style="text-align: center;">
                                <b>Invoice No -
                                    <span class="unique_invoice_no"></span>
                                </b>
                                <input type="hidden" name="invoice_no" class="invoice_no" value="">
                            </div>
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:13%;color: #34395e;">Gst Incl.</th>
                                            <th style="width:5%;color: #34395e;">Payment Type</th>
                                            <th style="width:25%;color: #34395e;">Description</th>
                                            <th style="width:14%;color: #34395e;">Amount</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem_invoice">
                                        <tr class="clonedrow_invoice">
                                            <td>
                                                <input name="id[]" type="hidden" value="" />
                                                <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields_invoice" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control" name="gst_included[]">
                                                    <option value="">Select</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <select class="form-control" name="payment_type[]">
                                                    <option value="">Select</option>
                                                    <option value="Adjust">Adjust/Discount</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_withdraw_amount_all_rows_invoice" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12 text-right">
                            <input type="hidden" name="save_type" class="save_type" value="">
                            <button onclick="customValidate('adjust_invoice_receipt_form','final')" type="button" class="btn btn-primary" style="margin:0px !important;">Save and Finalised</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Create Client Receipt Modal -->
<div class="modal fade custom_modal" id="createclientreceiptmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Client Receipt</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <input type="hidden"  id="top_value_db" value="">
				<form method="post" action="{{URL::to('/admin/clients/saveaccountreport')}}" name="create_client_receipt" autocomplete="off" id="create_client_receipt" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                <input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
                <input type="hidden" name="receipt_type" value="1">
					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								{!! html()->text('client')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '') !!}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="agent_id">Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="agent_id" id="sel_client_agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach(\App\Agent::where('status',1)->get() as $aplist)
                                        <option value="{{$aplist->id}}">{{@$aplist->full_name}} ({{@$aplist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:15%;color: #34395e;">Trans. No</th>
                                            <th style="width:5%;color: #34395e;">Payment Method</th>
                                            <th style="width:35%;color: #34395e;">Description</th>
                                            <th style="width:14%;color: #34395e;">Deposit</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem">
                                        <tr class="clonedrow">
                                            <td>
                                                <input data-valid="required"  class="form-control report_date_fields" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input class="form-control unique_trans_no" type="text" value="" readonly/>
                                                <input class="unique_trans_no_hidden" name="trans_no[]" type="hidden" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control" name="payment_method[]">
                                                    <option value="">Select</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Bank tansfer">Bank tansfer</option>
                                                    <option value="EFTPOS">EFTPOS</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control deposit_amount_per_row" name="deposit_amount[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_deposit_amount_all_rows" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">

                            <div class="upload_client_receipt_document" style="display:inline-block;">
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="doctype" value="client_receipt">
                                <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                <input class="docclientreceiptupload" type="file" name="document_upload[]"/>
                            </div>

                            <button onclick="customValidate('create_client_receipt')" type="button" class="btn btn-primary" style="margin:0px !important;">Save Report</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>



			</div>
		</div>
	</div>
</div>

<!-- Create Invoice Receipt Modal -->
<div class="modal fade custom_modal" id="createinvoicereceiptmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <input type="hidden"  id="invoice_top_value_db" value="">
				<form method="post" action="{{URL::to('/admin/clients/saveinvoicereport')}}" name="create_invoice_receipt" autocomplete="off" id="create_invoice_receipt" >
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                <input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
                <input type="hidden" name="receipt_type" value="3">
                <input type="hidden" name="receipt_id" id="receipt_id" value="">
                <input type="hidden" name="function_type" id="function_type" value="">

					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								{!! html()->text('client')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '') !!}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="agent_id">Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="agent_id" id="sel_invoice_agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach(\App\Agent::where('status',1)->get() as $aplist)
                                        <option value="{{$aplist->id}}">{{@$aplist->full_name}} ({{@$aplist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

						<div class="col-12 col-md-12 col-lg-12">
                            <div class="Invoic_no_cls" style="text-align: center;">
                                <b>Invoice No -
                                    <span class="unique_invoice_no"></span>
                                </b>
                                <input type="hidden" name="invoice_no" class="invoice_no" value="">
                            </div>
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:15%;color: #34395e;">Trans. No</th>
                                            <th style="width:13%;color: #34395e;">Gst Incl.</th>
                                            <th style="width:5%;color: #34395e;">Payment Type</th>
                                            <th style="width:25%;color: #34395e;">Description</th>
                                            <th style="width:14%;color: #34395e;">Amount</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem_invoice">
                                        <tr class="clonedrow_invoice">
                                            <td>
                                                <input name="id[]" type="hidden" value="" />
                                                <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields_invoice" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input class="form-control unique_trans_no_invoice" type="text" value="" readonly/>
                                                <input class="unique_trans_no_hidden_invoice" name="trans_no[]" type="hidden" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control" name="gst_included[]">
                                                    <option value="">Select</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <select class="form-control" name="payment_type[]">
                                                    <option value="">Select</option>
                                                    <option value="Professional Fee">Professional Fee</option>
                                                    <option value="Department Charges">Department Charges</option>
                                                    <option value="Surcharge">Surcharge</option>
                                                    <option value="Disbursements">Disbursements</option>
                                                    <option value="Other Cost">Other Cost</option>
                                                    <option value="Discount">Discount</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control deposit_amount_invoice_per_row" name="deposit_amount[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_deposit_amount_all_rows_invoice" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo_invoice"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">
                            <input type="hidden" name="save_type" class="save_type" value="">
                            <button onclick="customValidate('create_invoice_receipt','draft')" type="button" class="btn btn-primary" style="margin:0px !important;">Save Draft</button>
							<button onclick="customValidate('create_invoice_receipt','final')" type="button" class="btn btn-primary" style="margin:0px !important;">Save and Finalised</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Create Office Receipt Modal -->
<div class="modal fade custom_modal" id="createofficereceiptmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Office Receipt</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <input type="hidden"  id="office_top_value_db" value="">
				<form method="post" action="{{URL::to('/admin/clients/saveofficereport')}}" name="create_office_receipt" autocomplete="off" id="create_office_receipt" >
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                <input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
                <input type="hidden" name="receipt_type" value="2">
					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								{!! html()->text('client')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '') !!}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="agent_id">Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="agent_id" id="sel_office_agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach(\App\Agent::where('status',1)->get() as $aplist)
                                        <option value="{{$aplist->id}}">{{@$aplist->full_name}} ({{@$aplist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:15%;color: #34395e;">Receipt No</th>
                                            <th style="width:15%;color: #34395e;">Invoice No</th>
                                            <th style="width:5%;color: #34395e;">Payment method</th>
                                            <th style="width:25%;color: #34395e;">Description</th>
                                            <th style="width:14%;color: #34395e;">Received</th>
                                            <th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem_office">
                                        <tr class="clonedrow_office">
                                            <td>
                                                <input data-valid="required"  class="form-control report_date_fields_office" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields_office" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input class="form-control unique_trans_no_office" type="text" value="" readonly/>
                                                <input class="unique_trans_no_hidden_office" name="trans_no[]" type="hidden" value="" />
                                            </td>
                                            <td>
                                                <select class="form-control invoice_no_cls"  name="invoice_no[]">
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" name="payment_method[]" data-valid="required" >
                                                    <option value="">Select</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Bank tansfer">Bank tansfer</option>
                                                    <option value="EFTPOS">EFTPOS</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control total_withdrawal_amount_office" name="withdrawal_amount[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems_office" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_withdraw_amount_all_rows_office" style="color: #34395e;"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo_office"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">
                            <div class="upload_office_receipt_document" style="display:inline-block;">
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="doctype" value="office_receipt">
                                <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                <input class="docofficereceiptupload"  type="file" name="document_upload[]"/>
                            </div>

                            <button onclick="customValidate('create_office_receipt')" type="button" class="btn btn-primary" style="margin: 0px !important;">Save Report</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>


			</div>
		</div>
	</div>
</div>


<!-- Create Journal Modal -->
<div class="modal fade custom_modal" id="createjournalreceiptmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Journal</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <input type="hidden"  id="journal_top_value_db" value="">
				<form method="post" action="{{URL::to('/admin/clients/savejournalreport')}}" name="create_journal_receipt" autocomplete="off" id="create_journal_receipt" >
				@csrf
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                <input type="hidden" name="loggedin_userid" value="{{@Auth::user()->id}}">
                <input type="hidden" name="receipt_type" value="4">
					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								{!! html()->text('client')->class('form-control')->attribute('data-valid', 'required')->attribute('autocomplete', 'off')->attribute('placeholder', '') !!}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="agent_id">Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="agent_id" id="sel_journal_agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach(\App\Agent::where('status',1)->get() as $aplist)
                                        <option value="{{$aplist->id}}">{{@$aplist->full_name}} ({{@$aplist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;color: #34395e;">Trans. Date</th>
                                            <th style="width:15%;color: #34395e;">Entry Date</th>
                                            <th style="width:12%;color: #34395e;">Trans. No</th>
                                            <th style="width:13%;color: #34395e;">Invoice No</th>
                                            <th style="width:25%;color: #34395e;">Description</th>
                                            <th style="width:15%;color: #34395e;">Transfer</th>
											<th style="width:1%;color: #34395e;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem_journal">
                                        <tr class="clonedrow_journal">
                                            <td>
                                                <input data-valid="required"  class="form-control report_date_fields_journal" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields_journal" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input class="form-control unique_trans_no_journal" type="text" value="" readonly/>
                                                <input class="unique_trans_no_hidden_journal" name="trans_no[]" type="hidden" value="" />
                                            </td>

                                            <td>
                                                <select data-valid="required" class="form-control invoice_no_cls"  name="invoice_no[]">
                                                </select>
                                            </td>

                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control total_withdrawal_amount_journal" name="withdrawal_amount[]" type="text" value="" />
                                            </td>

					                        <td>
                                                <a class="removeitems_journal" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:48.99%;text-align:right;color: #34395e;">Totals</td>
                                            <td colspan="2" style="width:10.99%;">
                                                <span class="total_withdraw_amount_all_rows_journal" style="color: #34395e;"></span>
                                            </td>
										</tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <a href="javascript:;" class="openproductrinfo_journal"><i class="fa fa-plus"></i> Add New Line</a>
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">

                            <div class="upload_journal_receipt_document" style="display:inline-block;">
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="doctype" value="journal_receipt">
                                <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>

                                <input class="docjournalreceiptupload" type="file" name="document_upload[]"/>
                            </div>

                            <button onclick="customValidate('create_journal_receipt')" type="button" class="btn btn-primary" style="margin:0px !important;">Save Report</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
            </div>
		</div>
	</div>
</div>



<!-- Convert Lead to Client Popup -->
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



<!-- Upload inbox email Modal -->
<div class="modal fade custom_modal" id="uploadAndFetchMailModel" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Inbox Mail And Fetch Content:</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<form method="post" action="{{URL::to('/admin/upload-fetch-mail')}}" name="uploadAndFetchMail" id="uploadAndFetchMail" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="maclient_id_fetch">
                <input type="hidden" name="upload_inbox_mail_client_matter_id" id="upload_inbox_mail_client_matter_id" value="">
                <input type="hidden" name="type" value="client">
                      <!-- Error Message Container -->
                    <div class="custom-error-msg"></div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                               <label>Upload Outlook Email (.msg)<span class="span_req">*</span></label>
                               <input type="file" name="email_files[]" id="email_files" class="form-control" accept=".msg" multiple >
                            </div>
                       </div>

						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<button onclick="customValidate('uploadAndFetchMail')" class="btn btn-primary" type="button">Save</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Upload sent email Modal -->
<div class="modal fade custom_modal" id="uploadSentAndFetchMailModel" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Sent Mail And Fetch Content:</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<form method="post" action="{{URL::to('/admin/upload-sent-fetch-mail')}}" name="uploadSentAndFetchMail" id="uploadSentAndFetchMail" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="maclient_id_fetch_sent" value="">
                <input type="hidden" name="upload_sent_mail_client_matter_id" id="upload_sent_mail_client_matter_id" value="">
                <input type="hidden" name="type" value="client">
                  <!-- Error Message Container -->
                  <div class="custom-error-msg"></div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                               <label>Upload Outlook Email (.msg)<span class="span_req">*</span></label>
                               <input type="file" name="email_files[]" id="email_files1" class="form-control" accept=".msg" multiple >
                            </div>
                       </div>

						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<button onclick="customValidate('uploadSentAndFetchMail')" class="btn btn-primary" type="button">Save</button>
							</div>
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

    #create_action_popup #user-search {
        margin-bottom: 10px !important;
        width: 100% !important;
    }

    #create_action_popup .btn-sm {
        padding: 4px 8px !important;
        font-size: 11px !important;
    }
    
    .custom-error {
        color: #dc3545 !important;
        font-size: 12px !important;
        display: block !important;
        margin-top: 5px !important;
        font-weight: normal !important;
    }
    
    .popuploader {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        text-align: center;
        padding-top: 20%;
    }
</style>
<!-- Action Popup Modal -->
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


<!-- Edit Ledger Entry Modal -->
<div class="modal fade" id="editLedgerModal" tabindex="-1" role="dialog" aria-labelledby="editLedgerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLedgerModalLabel">Edit Client Funds Ledger Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLedgerForm">
                    <input type="hidden" name="id">
                    <input type="hidden" name="client_id" value="{{$fetchedData->id}}">
                    <div class="form-group">
                        <label for="trans_date">Transaction Date</label>
                        <input type="text" class="form-control" name="trans_date" required>
                    </div>
                    <div class="form-group">
                        <label for="entry_date">Entry Date</label>
                        <input type="text" class="form-control" name="entry_date" required>
                    </div>
                    <div class="form-group">
                        <label for="client_fund_ledger_type">Type</label>
                        <input type="text" class="form-control" name="client_fund_ledger_type" readonly>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" name="description">
                    </div>
                    <div class="form-group">
                        <label for="deposit_amount">Funds In (+)</label>
                        <input type="number" class="form-control" name="deposit_amount" step="0.01" value="0.00">
                    </div>
                    <div class="form-group">
                        <label for="withdraw_amount">Funds Out (-)</label>
                        <input type="number" class="form-control" name="withdraw_amount" step="0.01" value="0.00">
                    </div>

            </div>
            <div class="modal-footer">
                <div class="upload_client_receipt_document" style="display:inline-block;">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="doctype" value="client_receipt">
                    <span class="file-selection-hint" style="margin-left: 10px; color: #34395e;"></span>
                    <a href="javascript:;" class="btn btn-primary add-document-btn"><i class="fa fa-plus"></i> Add Document</a>
                    <input class="docclientreceiptupload" type="file" name="document_upload[]"/>
                </div>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateLedgerEntryBtn">Update Entry</button>
            </div>
        </div>
    </div>
</div>


<!-- Form 956 -->
<div class="modal fade custom_modal" id="form956CreateFormModel" tabindex="-1" role="dialog" aria-labelledby="form956ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="form956ModalLabel">Create Form 956</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('forms.store') }}" name="createForm956" id="createForm956" autocomplete="off">
                    @csrf
                    <!-- Hidden Fields for Client and Client Matter ID -->
                    <input type="hidden" name="client_id" id="form956_client_id">
                    <input type="hidden" name="client_matter_id" id="form956_client_matter_id">

                    <!-- Error Message Container -->
                    <div class="custom-error-msg"></div>

                    <!-- Agent Details (Read-only, assuming agent is pre-fetched) -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-medium text-gray-900">Agent Details</h6>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Agent Name - <span id="agent_name_label"></span></label>
                                        <input type="hidden" name="agent_id" id="agent_id">
                                        <input type="hidden" name="agent_name" id="agent_name">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Business Name - <span id="business_name_label"></span></label>
                                        <input type="hidden" name="business_name" id="business_name" class="form-control bg-gray-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

					<!-- Application Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-medium text-gray-900">Application Details</h6>
                            <div class="row mt-2">
                                <!-- Application Type -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Type of Application</label>
                                        <br/><span id="application_type_label"></span>
                                        <input type="hidden" name="application_type" id="application_type">
                                    </div>
                                </div>
                                <!-- Date Lodged -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="text-sm font-medium text-gray-700">Date Lodged</label>
                                        <input type="date" name="date_lodged" id="date_lodged" class="form-control">
                                    </div>
                                </div>
                                <!-- Not Lodged Checkbox -->
                                <div class="col-12">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="not_lodged" value="1" class="form-check-input">
                                            <span class="ml-2 text-sm text-gray-700">Application not yet lodged</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Type (Hidden - Always Appointment) -->
                    <input type="hidden" name="form_type" value="appointment">

                    <!-- Part A: New Appointment -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-medium text-gray-900">Part A: New Appointment</h6>
                            <div class="row mt-2">
                                <!-- Agent Type -->
                                <div class="col-12">
                                    <label class="text-sm font-medium text-gray-700">Agent Type</label>
                                    <div class="mt-2">
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="is_registered_migration_agent" value="1" checked class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Registered Migration Agent</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="is_legal_practitioner" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Legal Practitioner</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Type of Assistance -->
                                <div class="col-12 mt-3">
                                    <label class="text-sm font-medium text-gray-700">Type of Assistance</label>
                                    <div class="mt-2">
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_visa_application" value="1" checked class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Visa Application</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_sponsorship" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Sponsorship</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_nomination" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Nomination</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_cancellation" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Cancellation</span>
                                            </label>
                                        </div>
                                        <div class="form-group" style="margin-left: 20px;">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="assistance_other" value="1" class="form-check-input">
                                                <span class="ml-2 text-sm text-gray-700">Other</span>
                                            </label>
                                            <input type="text" name="assistance_other_details" placeholder="Specify other assistance" class="form-control mt-1">
                                        </div>
                                    </div>
                                </div>
                                <!-- Question 5 - Business Address -->
                                <div class="col-12 mt-3">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="text-sm font-medium text-gray-700">Question 5 - Business Address</label>
                                        <input type="text" name="business_address" value="As Above" readonly class="form-control bg-gray-100">
                                    </div>
                                </div>
                                <!-- Question 7 -->
                                <div class="col-12">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="question_7" value="1" checked class="form-check-input">
                                            <span class="ml-2 text-sm text-gray-700">Question 7 - Registered Migration Agent</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Question 17 -->
                                <div class="col-12">
                                    <div class="form-group" style="margin-left: 20px;">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_authorized_recipient" value="1" checked class="form-check-input">
                                            <span class="ml-2 text-sm text-gray-700">Authorized Recipient (Question 17)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
							<div class="row mt-2">
                                <div class="col-12 col-md-6">
                                    <input type="date" name="agent_declaration_date" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="date" name="client_declaration_date" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
							</div>
							<!-- Submit Button -->
							<div class="row mt-4">
								<div class="col-12">
									<button type="submit" class="btn btn-primary">Create Form</button>
								</div>
							</div>
						</div>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Visa agreement Form -->
<div class="modal fade custom_modal" id="visaAgreementCreateFormModel" tabindex="-1" role="dialog" aria-labelledby="visaAgreementModalLabel11" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="visaAgreementModalLabel">Create Visa Agreement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('clients.generateagreement')}}" name="visaagreementform11" id="visaagreementform11" autocomplete="off">
					@csrf
					<!-- Hidden Fields for Client and Client Matter ID -->
					<input type="hidden" name="client_id" id="visa_agreement_client_id">
					<input type="hidden" name="client_matter_id" id="visa_agreement_client_matter_id">

					<!-- Error Message Container -->
					<div class="custom-error-msg"></div>

					<!-- Agent Details (Read-only, assuming agent is pre-fetched) -->
					<div class="row">
						<div class="col-12">
							<h6 class="font-medium text-gray-900">Agent Details</h6>
							<div class="row mt-2">
								<div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Agent Name - <span id="visaagree_agent_name_label"></span></label>
										<input type="hidden" name="agent_id" id="visaagree_agent_id">
										<input type="hidden" name="agent_name" id="visaagree_agent_name">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Business Name - <span id="visaagree_business_name_label"></span></label>
										<input type="hidden" name="business_name" id="visaagree_business_name" class="form-control bg-gray-100">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Submit Button -->
					<div class="row mt-4">
						<div class="col-12">
							<button type="submit" class="btn btn-primary">Generate Agreement</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Cost assignment Form -->
<div class="modal fade custom_modal" id="costAssignmentCreateFormModel" tabindex="-1" role="dialog" aria-labelledby="costAssignmentModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="costAssignmentModalLabel">Create Cost Assignment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('clients.savecostassignment')}}" name="costAssignmentform" id="costAssignmentform" autocomplete="off">
					@csrf
					<!-- Hidden Fields for Client and Client Matter ID -->
					<input type="hidden" name="client_id" id="cost_assignment_client_id">
					<input type="hidden" name="client_matter_id" id="cost_assignment_client_matter_id">
                    <input type="hidden" name="agent_id" id="costassign_agent_id">
					<!-- Error Message Container -->
					<div class="custom-error-msg"></div>

					<!-- Agent Details (Read-only, assuming agent is pre-fetched) -->
					<div class="row">
						<div class="col-12">
							<h6 class="font-medium text-gray-900">Agent Details</h6>
							<div class="row mt-2">
								<div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Agent Name - <span id="costassign_agent_name_label"></span></label>
                                    </div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Business Name - <span id="costassign_business_name_label"></span></label>
									</div>
								</div>

                                <div class="col-6">
									<div class="form-group">
										<label class="text-sm font-medium text-gray-700">Client Matter Name - <span id="costassign_client_matter_name_label"></span></label>
									</div>
								</div>
                            </div>
						</div>
					</div>

                    <div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">


						<div style="margin-bottom: 15px;" class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
							<h4>Block Fee</h4>
						</div>

						<div class="row">
							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="Block_1_Ex_Tax">Block 1 Incl. Tax</label>
									{!! html()->text('Block_1_Ex_Tax')->class('form-control')->id('Block_1_Ex_Tax')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 1 Incl. Tax' ) !!}
									@if ($errors->has('Block_1_Ex_Tax'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('Block_1_Ex_Tax') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="Block_2_Ex_Tax">Block 2 Incl. Tax</label>
									{!! html()->text('Block_2_Ex_Tax')->class('form-control')->id('Block_2_Ex_Tax')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 2 Incl. Tax' ) !!}
									@if ($errors->has('Block_2_Ex_Tax'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('Block_2_Ex_Tax') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="Block_3_Ex_Tax">Block 3 Incl. Tax</label>
									{!! html()->text('Block_3_Ex_Tax')->class('form-control')->id('Block_3_Ex_Tax')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 3 Incl. Tax' ) !!}
									@if ($errors->has('Block_3_Ex_Tax'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('Block_3_Ex_Tax') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="TotalBLOCKFEE">Total Block Fee</label>
									{!! html()->text('TotalBLOCKFEE')->class('form-control')->id('TotalBLOCKFEE')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total Block Fee')->attribute('readonly', 'readonly' ) !!}
								</div>
							</div>
						</div>

                        <div style="margin-bottom: 15px;" class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
                            <h4>Department Fee</h4>
							<div class="col-3">
								<label for="surcharge">Surcharge</label>
								<select class="form-control" name="surcharge" id="surcharge">
									<option value="">Select</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
								</select>
							</div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="Dept_Base_Application_Charge">Dept Base Application Charge</label>
                                            {!! html()->text('Dept_Base_Application_Charge')->class('form-control')->id('Dept_Base_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Base Application Charge' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Base_Application_Charge_no_of_person">Person</label>
                                            <input type="number" name="Dept_Base_Application_Charge_no_of_person" id="Dept_Base_Application_Charge_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>

                                    @if ($errors->has('Dept_Base_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Base_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
				                        <div class="col-9">
                                            <label for="Dept_Non_Internet_Application_Charge">Dept Non Internet Application Charge</label>
                                            {!! html()->text('Dept_Non_Internet_Application_Charge')->class('form-control')->id('Dept_Non_Internet_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Non Internet Application Charge' ) !!}
                                        </div>
				                        <div class="col-3">
                                            <label for="Dept_Non_Internet_Application_Charge_no_of_person">Person</label>
                                            <input type="number" name="Dept_Non_Internet_Application_Charge_no_of_person" id="Dept_Non_Internet_Application_Charge_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Non_Internet_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Non_Internet_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="Dept_Additional_Applicant_Charge_18_Plus">Dept Additional Applicant Charge 18 +</label>
                                            {!! html()->text('Dept_Additional_Applicant_Charge_18_Plus')->class('form-control')->id('Dept_Additional_Applicant_Charge_18_Plus')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Additional Applicant Charge 18 Plus' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Additional_Applicant_Charge_18_Plus_no_of_person">Person</label>
                                            <input type="number" name="Dept_Additional_Applicant_Charge_18_Plus_no_of_person" id="Dept_Additional_Applicant_Charge_18_Plus_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Additional_Applicant_Charge_18_Plus'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Additional_Applicant_Charge_18_Plus') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Additional_Applicant_Charge_Under_18">Dept Add. Applicant Charge Under 18</label>
                                            {!! html()->text('Dept_Additional_Applicant_Charge_Under_18')->class('form-control')->id('Dept_Additional_Applicant_Charge_Under_18')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Additional Applicant Charge Under 18' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Additional_Applicant_Charge_Under_18_no_of_person">Person</label>
                                            <input type="number" name="Dept_Additional_Applicant_Charge_Under_18_no_of_person" id="Dept_Additional_Applicant_Charge_Under_18_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Additional_Applicant_Charge_Under_18'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Additional_Applicant_Charge_Under_18') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Subsequent_Temp_Application_Charge">Dept Subsequent Temp App Charge</label>
                                            {!! html()->text('Dept_Subsequent_Temp_Application_Charge')->class('form-control')->id('Dept_Subsequent_Temp_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Subsequent Temp Application Charge' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Subsequent_Temp_Application_Charge_no_of_person">Person</label>
                                            <input type="number" name="Dept_Subsequent_Temp_Application_Charge_no_of_person" id="Dept_Subsequent_Temp_Application_Charge_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Subsequent_Temp_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Subsequent_Temp_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Second_VAC_Instalment_Charge_18_Plus">Dept Second VAC Instalment 18+</label>
                                            {!! html()->text('Dept_Second_VAC_Instalment_Charge_18_Plus')->class('form-control')->id('Dept_Second_VAC_Instalment_Charge_18_Plus')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Second VAC Instalment Charge 18 Plus' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person">Person</label>
                                            <input type="number" name="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person" id="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Second_VAC_Instalment_Charge_18_Plus'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Second_VAC_Instalment_Charge_18_Plus') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Second_VAC_Instalment_Under_18">Dept Second VAC Instalment Under 18</label>
                                            {!! html()->text('Dept_Second_VAC_Instalment_Under_18')->class('form-control')->id('Dept_Second_VAC_Instalment_Under_18')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Second VAC Instalment Under 18' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Second_VAC_Instalment_Under_18_no_of_person">Person</label>
                                            <input type="number" name="Dept_Second_VAC_Instalment_Under_18_no_of_person" id="Dept_Second_VAC_Instalment_Under_18_no_of_person"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Second_VAC_Instalment_Under_18'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Second_VAC_Instalment_Under_18') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="Dept_Nomination_Application_Charge">Dept Nomination Application Charge</label>
                                    {!! html()->text('Dept_Nomination_Application_Charge')->class('form-control')->id('Dept_Nomination_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Nomination Application Charge' ) !!}
                                    @if ($errors->has('Dept_Nomination_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Nomination_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="Dept_Sponsorship_Application_Charge">Dept Sponsorship Application Charge</label>
                                    {!! html()->text('Dept_Sponsorship_Application_Charge')->class('form-control')->id('Dept_Sponsorship_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Sponsorship Application Charge' ) !!}
                                    @if ($errors->has('Dept_Sponsorship_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Sponsorship_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="TotalDoHACharges">Total DoHA Charges</label>
                                    {!! html()->text('TotalDoHACharges')->class('form-control')->id('TotalDoHACharges')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total DoHA Charges')->attribute('readonly', 'readonly' ) !!}
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="TotalDoHASurcharges">Total DoHA Surcharges</label>
                                    {!! html()->text('TotalDoHASurcharges')->class('form-control')->id('TotalDoHASurcharges')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total DoHA Surcharges' )->attribute('readonly', 'readonly') !!}
                                </div>
                            </div>
                        </div>


						<div style="margin-bottom: 15px;" class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
                            <h4>Additional Fee</h4>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="additional_fee_1">Additional Fee1</label>
                                    {!! html()->text('additional_fee_1')->class('form-control')->id('additional_fee_1')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Additional Fee' ) !!}
                                    @if ($errors->has('additional_fee_1'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('additional_fee_1') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

					<!-- Submit Button -->
					<div class="row mt-4">
						<div class="col-12">
							<button type="submit" class="btn btn-primary">Save Cost Assignment</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Change matter assignee -->
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


<!-- Create Personal Document category Modal -->
<div class="modal fade addpersonaldoccatmodel custom_modal" id="addpersonaldoccatmodel" tabindex="-1" role="dialog" aria-labelledby="addPersDocCatModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addPersDocCatModalLabel">Add Personal Document Category</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-personaldoccategory')}}" name="add_pers_doc_cat_form" id="add_pers_doc_cat_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">

					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="personal_doc_category">Category<span class="span_req">*</span></label>
								<input type="text" class="form-control" name="personal_doc_category" id="personal_doc_category" data-valid="required">

								<span class="custom-error personal_doc_category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('add_pers_doc_cat_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Create visa Document category Modal -->
<div class="modal fade addvisadoccatmodel custom_modal" id="addvisadoccatmodel" tabindex="-1" role="dialog" aria-labelledby="addVisaDocCatModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addVisaDocCatModalLabel">Add Visa Document Category</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-visadoccategory')}}" name="add_visa_doc_cat_form" id="add_visa_doc_cat_form" autocomplete="off"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
					<input type="hidden" name="clientmatterid" id="visaclientmatterid" value="">

					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="visa_doc_category">Category<span class="span_req">*</span></label>
								<input type="text" class="form-control" name="visa_doc_category" id="visa_doc_category" data-valid="required">

								<span class="custom-error visa_doc_category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('add_visa_doc_cat_form')" type="button" class="btn btn-primary" style="margin: 0px !important;">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Lead Cost assignment Form -->
<div class="modal fade custom_modal" id="costAssignmentCreateFormModelLead" tabindex="-1" role="dialog" aria-labelledby="costAssignmentModalLabelLead" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="costAssignmentModalLabelLead">Create Cost Assignment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('clients.savecostassignmentlead')}}" name="costAssignmentformlead" id="costAssignmentformlead" autocomplete="off">
					@csrf
					<!-- Hidden Fields for Client and Client Matter ID -->
					<input type="hidden" name="client_id" id="cost_assignment_lead_id">
					<!-- Error Message Container -->
					<div class="custom-error-msg"></div>
					<div class="row">
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="migration_agent">Select Migration Agent <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="migration_agent" id="sel_migration_agent_id_lead">
                                    <option value="">Select Migration Agent</option>
                                    @foreach(\App\Models\Admin::where('role',16)->select('id','first_name','last_name','email')->where('status',1)->get() as $migAgntlist)
                                        <option value="{{$migAgntlist->id}}">{{@$migAgntlist->first_name}} {{@$migAgntlist->last_name}} ({{@$migAgntlist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="person_responsible">Select Person Responsible <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="person_responsible" id="sel_person_responsible_id_lead">
                                    <option value="">Select Person Responsible</option>
                                    @foreach(\App\Models\Admin::where('role',12)->select('id','first_name','last_name','email')->where('status',1)->get() as $perreslist)
                                        <option value="{{$perreslist->id}}">{{@$perreslist->first_name}} {{@$perreslist->last_name}} ({{@$perreslist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="person_assisting">Select Person Assisting <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="person_assisting" id="sel_person_assisting_id_lead">
                                    <option value="">Select Person Assisting</option>
                                    @foreach(\App\Models\Admin::where('role',13)->select('id','first_name','last_name','email')->where('status',1)->get() as $perassislist)
                                        <option value="{{$perassislist->id}}">{{@$perassislist->first_name}} {{@$perassislist->last_name}} ({{@$perassislist->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="matter_id">Select Matter <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control select2" name="matter_id" id="sel_matter_id_lead">
                                    <option value="">Select Matter</option>
                                    @foreach(\App\Models\Matter::select('id','title')->where('status',1)->get() as $matterlist)
                                        <option value="{{$matterlist->id}}">{{@$matterlist->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
					</div>

					<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
                        <div style="margin-bottom: 15px;" class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
							<h4>Block Fee</h4>
						</div>

						<div class="row">
							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="Block_1_Ex_Tax">Block 1 Incl. Tax</label>
									{!! html()->text('Block_1_Ex_Tax')->class('form-control')->id('Block_1_Ex_Tax_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 1 Incl. Tax' ) !!}
									@if ($errors->has('Block_1_Ex_Tax'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('Block_1_Ex_Tax') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="Block_2_Ex_Tax">Block 2 Incl. Tax</label>
									{!! html()->text('Block_2_Ex_Tax')->class('form-control')->id('Block_2_Ex_Tax_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 2 Incl. Tax' ) !!}
									@if ($errors->has('Block_2_Ex_Tax'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('Block_2_Ex_Tax') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="Block_3_Ex_Tax">Block 3 Incl. Tax</label>
									{!! html()->text('Block_3_Ex_Tax')->class('form-control')->id('Block_3_Ex_Tax_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 3 Incl. Tax' ) !!}
									@if ($errors->has('Block_3_Ex_Tax'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('Block_3_Ex_Tax') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="col-12 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="TotalBLOCKFEE">Total Block Fee</label>
									{!! html()->text('TotalBLOCKFEE')->class('form-control')->id('TotalBLOCKFEE_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total Block Fee')->attribute('readonly', 'readonly' ) !!}
								</div>
							</div>
						</div>

                        <div style="margin-bottom: 15px;" class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
                            <h4>Department Fee</h4>
							<div class="col-3">
								<label for="surcharge">Surcharge</label>
								<select class="form-control" name="surcharge" id="surcharge_lead">
									<option value="">Select</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
								</select>
							</div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="Dept_Base_Application_Charge">Dept Base Application Charge</label>
                                            {!! html()->text('Dept_Base_Application_Charge')->class('form-control')->id('Dept_Base_Application_Charge_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Base Application Charge' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Base_Application_Charge_no_of_person">Person</label>
                                            <input type="number" name="Dept_Base_Application_Charge_no_of_person" id="Dept_Base_Application_Charge_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>

                                    @if ($errors->has('Dept_Base_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Base_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
				                        <div class="col-9">
                                            <label for="Dept_Non_Internet_Application_Charge">Dept Non Internet Application Charge</label>
                                            {!! html()->text('Dept_Non_Internet_Application_Charge')->class('form-control')->id('Dept_Non_Internet_Application_Charge_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Non Internet Application Charge' ) !!}
                                        </div>
				                        <div class="col-3">
                                            <label for="Dept_Non_Internet_Application_Charge_no_of_person">Person</label>
                                            <input type="number" name="Dept_Non_Internet_Application_Charge_no_of_person" id="Dept_Non_Internet_Application_Charge_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Non_Internet_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Non_Internet_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="Dept_Additional_Applicant_Charge_18_Plus">Dept Additional Applicant Charge 18 +</label>
                                            {!! html()->text('Dept_Additional_Applicant_Charge_18_Plus')->class('form-control')->id('Dept_Additional_Applicant_Charge_18_Plus_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Additional Applicant Charge 18 Plus' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Additional_Applicant_Charge_18_Plus_no_of_person">Person</label>
                                            <input type="number" name="Dept_Additional_Applicant_Charge_18_Plus_no_of_person" id="Dept_Additional_Applicant_Charge_18_Plus_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Additional_Applicant_Charge_18_Plus'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Additional_Applicant_Charge_18_Plus') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Additional_Applicant_Charge_Under_18">Dept Add. Applicant Charge Under 18</label>
                                            {!! html()->text('Dept_Additional_Applicant_Charge_Under_18')->class('form-control')->id('Dept_Additional_Applicant_Charge_Under_18_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Additional Applicant Charge Under 18' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Additional_Applicant_Charge_Under_18_no_of_person">Person</label>
                                            <input type="number" name="Dept_Additional_Applicant_Charge_Under_18_no_of_person" id="Dept_Additional_Applicant_Charge_Under_18_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Additional_Applicant_Charge_Under_18'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Additional_Applicant_Charge_Under_18') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Subsequent_Temp_Application_Charge">Dept Subsequent Temp App Charge</label>
                                            {!! html()->text('Dept_Subsequent_Temp_Application_Charge')->class('form-control')->id('Dept_Subsequent_Temp_Application_Charge_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Subsequent Temp Application Charge' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Subsequent_Temp_Application_Charge_no_of_person">Person</label>
                                            <input type="number" name="Dept_Subsequent_Temp_Application_Charge_no_of_person" id="Dept_Subsequent_Temp_Application_Charge_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Subsequent_Temp_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Subsequent_Temp_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Second_VAC_Instalment_Charge_18_Plus">Dept Second VAC Instalment 18+</label>
                                            {!! html()->text('Dept_Second_VAC_Instalment_Charge_18_Plus')->class('form-control')->id('Dept_Second_VAC_Instalment_Charge_18_Plus_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Second VAC Instalment Charge 18 Plus' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person">Person</label>
                                            <input type="number" name="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person" id="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Second_VAC_Instalment_Charge_18_Plus'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Second_VAC_Instalment_Charge_18_Plus') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="row">
			                            <div class="col-9">
                                            <label for="Dept_Second_VAC_Instalment_Under_18">Dept Second VAC Instalment Under 18</label>
                                            {!! html()->text('Dept_Second_VAC_Instalment_Under_18')->class('form-control')->id('Dept_Second_VAC_Instalment_Under_18_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Second VAC Instalment Under 18' ) !!}
                                        </div>
                                        <div class="col-3">
                                            <label for="Dept_Second_VAC_Instalment_Under_18_no_of_person">Person</label>
                                            <input type="number" name="Dept_Second_VAC_Instalment_Under_18_no_of_person" id="Dept_Second_VAC_Instalment_Under_18_no_of_person_lead"
                                                class="form-control" placeholder="1" value="1" min="0" step="any" />
                                        </div>
                                    </div>
                                    @if ($errors->has('Dept_Second_VAC_Instalment_Under_18'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Second_VAC_Instalment_Under_18') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="Dept_Nomination_Application_Charge">Dept Nomination Application Charge</label>
                                    {!! html()->text('Dept_Nomination_Application_Charge')->class('form-control')->id('Dept_Nomination_Application_Charge_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Nomination Application Charge' ) !!}
                                    @if ($errors->has('Dept_Nomination_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Nomination_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="Dept_Sponsorship_Application_Charge">Dept Sponsorship Application Charge</label>
                                    {!! html()->text('Dept_Sponsorship_Application_Charge')->class('form-control')->id('Dept_Sponsorship_Application_Charge_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Sponsorship Application Charge' ) !!}
                                    @if ($errors->has('Dept_Sponsorship_Application_Charge'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('Dept_Sponsorship_Application_Charge') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="TotalDoHACharges">Total DoHA Charges</label>
                                    {!! html()->text('TotalDoHACharges')->class('form-control')->id('TotalDoHACharges_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total DoHA Charges')->attribute('readonly', 'readonly' ) !!}
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="TotalDoHASurcharges">Total DoHA Surcharges</label>
                                    {!! html()->text('TotalDoHASurcharges')->class('form-control')->id('TotalDoHASurcharges_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total DoHA Surcharges' )->attribute('readonly', 'readonly') !!}
                                </div>
                            </div>
                        </div>


						<div style="margin-bottom: 15px;" class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
                            <h4>Additional Fee</h4>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="additional_fee_1">Additional Fee1</label>
                                    {!! html()->text('additional_fee_1')->class('form-control')->id('additional_fee_1_lead')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Additional Fee' ) !!}
                                    @if ($errors->has('additional_fee_1'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('additional_fee_1') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

					<!-- Submit Button -->
					<div class="row mt-4">
						<div class="col-12">
							<button onclick="customValidate('costAssignmentformlead')" type="button" class="btn btn-primary">Save Cost Assignment</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Agreement Model Open -->
<div class="modal fade custom_modal" id="agreementModal" tabindex="-1" role="dialog" aria-labelledby="agreementModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form id="agreementUploadForm" enctype="multipart/form-data">
			<input type="hidden" name="clientmatterid" id="agreemnt_clientmatterid" value="">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="agreementModalLabel">Upload Agreement (PDF)</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="file" name="agreement_doc" class="form-control" accept=".pdf" required>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</div>
		</form>
	</div>
</div>


<!-- Edit Date Time Modal -->
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


<!-- NP message -->
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

<script>
$(document).ready(function() {
    // Initialize the multi-select dropdown functionality
    initializeMultiSelectDropdown();

    // Handle checkbox changes
    $('.checkbox-item').on('change', function() {
        updateSelectedUsers();
        updateHiddenSelect();
    });

    // Handle search functionality
    $('#user-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        filterUsers(searchTerm);
    });

    // Handle select all button
    $('#select-all-users').on('click', function() {
        $('.checkbox-item:visible').prop('checked', true).trigger('change');
    });

    // Handle select none button
    $('#select-none-users').on('click', function() {
        $('.checkbox-item:visible').prop('checked', false).trigger('change');
    });

    // Prevent dropdown from closing when clicking inside
    $('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });
});

function initializeMultiSelectDropdown() {
    // Update the button text initially
    updateSelectedUsers();
}

function updateSelectedUsers() {
    var selectedUsers = [];
    $('.checkbox-item:checked').each(function() {
        selectedUsers.push($(this).data('name'));
    });

    var buttonText = selectedUsers.length > 0 ?
        (selectedUsers.length === 1 ? selectedUsers[0] : selectedUsers.length + ' users selected') :
        'Assign User';

    $('#selected-users-text').text(buttonText);
}

function updateHiddenSelect() {
    var selectedValues = [];
    $('.checkbox-item:checked').each(function() {
        selectedValues.push($(this).val());
    });
    $('#rem_cat').val(selectedValues).trigger('change');
}

function filterUsers(searchTerm) {
    $('.user-item').each(function() {
        var userName = $(this).data('name');
        if (userName.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });

    // Update select all/none buttons to only affect visible items
    var visibleCheckboxes = $('.checkbox-item:visible');
    var checkedVisibleCheckboxes = $('.checkbox-item:visible:checked');

    if (checkedVisibleCheckboxes.length === visibleCheckboxes.length && visibleCheckboxes.length > 0) {
        $('#select-all-users').text('Select None').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    } else {
        $('#select-all-users').text('Select All').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }
}

// Enhanced select all/none functionality
$('#select-all-users').on('click', function() {
    var $button = $(this);
    if ($button.text() === 'Select All') {
        $('.checkbox-item:visible').prop('checked', true).trigger('change');
        $button.text('Select None').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    } else {
        $('.checkbox-item:visible').prop('checked', false).trigger('change');
        $button.text('Select All').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }
});

// Clear search when dropdown is closed
$('#dropdownMenuButton').on('click', function() {
    setTimeout(function() {
        $('#user-search').val('');
        $('.user-item').show();
        $('#select-all-users').text('Select All').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }, 100);
});

// Clear validation errors when modal is opened
$('#create_action_popup').on('show.bs.modal', function() {
    $('.custom-error').remove();
    $('.popuploader').hide();
});

// Clear validation errors when modal is closed
$('#create_action_popup').on('hidden.bs.modal', function() {
    $('.custom-error').remove();
    $('.popuploader').hide();
    // Reset form fields
    $('#assignnote').val('');
    $('#task_group').val('');
    $('.checkbox-item').prop('checked', false);
    updateSelectedUsers();
    updateHiddenSelect();
});

//Start Convert Activity to Note functionality
document.querySelectorAll('.convert-activity-to-note').forEach(function(icon) {
    icon.addEventListener('click', function() {
        const activityId = this.getAttribute('data-activity-id');
        const activitySubject = this.getAttribute('data-activity-subject');
        const activityDescription = this.getAttribute('data-activity-description');
        const activityCreatedBy = this.getAttribute('data-activity-created-by');
        const activityCreatedAt = this.getAttribute('data-activity-created-at');
        const clientId = this.getAttribute('data-client-id');
        
        // Show confirmation dialog
        if (confirm('Are you sure you want to convert this activity into Notes?')) {
            // Populate modal fields
            document.getElementById('convert_activity_id').value = activityId;
            document.getElementById('convert_client_id').value = clientId;
            
            // Process description: remove HTML tags and first span
            let cleanDescription = processDescription(activityDescription);
            
            // Set value for Summernote editor
            $('#convert_note_description').summernote('code', cleanDescription);
            
            // Set Type dropdown based on activity subject and description
            setNoteType(activitySubject, activityDescription);
            
            // Populate client matters dropdown
            populateClientMatters(clientId);
            
            // Show modal
            $('#convertActivityToNoteModal').modal('show');
        }
    });
});

// Function to process description: remove HTML tags and first span
function processDescription(description) {
    // Create a temporary div to parse HTML
    let tempDiv = document.createElement('div');
    tempDiv.innerHTML = description;
    
    // Remove the first span if it exists
    let firstSpan = tempDiv.querySelector('span');
    if (firstSpan) {
        firstSpan.remove();
    }
    
    // Get text content (removes all HTML tags)
    let cleanText = tempDiv.textContent || tempDiv.innerText || '';
    
    // Clean up extra whitespace
    cleanText = cleanText.replace(/\s+/g, ' ').trim();
    
    return cleanText;
}

// Function to set note type based on activity subject and description
function setNoteType(subject, description = '') {
    const typeSelect = document.getElementById('convert_note_type'); 
    
    // Reset to default
    typeSelect.value = '';
    
    // First check if description contains type information (like "Email", "Call", etc.)
    if (description) {
        const descText = description.toLowerCase();
        if (descText.includes('email')) {
            typeSelect.value = 'Email';
            return;
        } else if (descText.includes('call')) {
            typeSelect.value = 'Call';
            return;
        } else if (descText.includes('person') || descText.includes('in-person')) {
            typeSelect.value = 'In-Person';
            return;
        } else if (descText.includes('attention')) {
            typeSelect.value = 'Attention';
            return;
        }
    }
    
    // If no type found in description, check subject content
    if (subject.includes('call') || subject.includes('Call')) {
        typeSelect.value = 'Call';
    } else if (subject.includes('email') || subject.includes('Email')) {
        typeSelect.value = 'Email';
    } else if (subject.includes('person') || subject.includes('Person')) {
        typeSelect.value = 'In-Person';
    } else if (subject.includes('attention') || subject.includes('Attention')) {
        typeSelect.value = 'Attention';
    } else {
        typeSelect.value = 'Others';
    }
}

// Function to populate client matters dropdown
function populateClientMatters(clientId) {
    // Clear existing options
    const select = document.getElementById('convert_client_matter_id');
    select.innerHTML = '<option value="">Select Client Matter</option>';
    
    // Fetch client matters via AJAX
    fetch(`/admin/get-client-matters/${clientId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matters) {
                data.matters.forEach(matter => {
                    const option = document.createElement('option');
                    option.value = matter.id;
                    
                    // Check if it's a general matter (starts with GN_)
                    if (matter.client_unique_matter_no && 
                        matter.client_unique_matter_no.startsWith('GN_')) {
                        option.textContent = 'General Matter - ' + matter.client_unique_matter_no;
                    } else {
                        option.textContent = matter.display_name;
                    }
                    
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching client matters:', error);
        });
}

// Handle form submission
document.getElementById('convertActivityToNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const clientMatterId = document.getElementById('convert_client_matter_id').value;
    const noteType = document.getElementById('convert_note_type').value;
    
    if (!clientMatterId) {
        alert('Please select a Client Matter');
        return;
    }
    
    if (!noteType) {
        alert('Please select a Type');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('/admin/convert-activity-to-note', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Activity successfully converted to note!');
            $('#convertActivityToNoteModal').modal('hide');
            // Optionally refresh the page or update the UI
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to convert activity to note'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while converting activity to note');
    });
});
//End Convert Activity to Note functionality
</script>
