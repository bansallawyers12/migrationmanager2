<!-- Appointment Modal -->
<div class="modal fade add_appointment custom_modal" id="create_appoint" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none;">
				<h5 class="modal-title" id="interestModalLabel" style="font-weight: 600;">
					<i class="fas fa-calendar-plus mr-2"></i>Schedule Appointment
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/add-appointment-book')}}" name="appointform" id="appointform" autocomplete="off" enctype="multipart/form-data">
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
										<option value="1">Permanent Residency Appointment</option>
										<option value="2">Temporary Residency Appointment</option>
										<option value="3">JRP/Skill Assessment</option>
										<option value="4">Tourist Visa</option>
										<option value="5">Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)</option>
										<option value="6">Complex matters: AAT, Protection visa, Federal Case</option>
										<option value="7">Visa Cancellation/ NOICC/ Visa refusals</option>
										<option value="8">INDIA/UK/CANADA/EUROPE TO AUSTRALIA</option>
									</select>
                                </div>
                            </div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12 services_row" id="services" style="display: none;">
							<div class="form-group">
								<label for="service_id" class="font-weight-bold text-dark mb-3">Services <span class="text-danger">*</span></label>
								<div class="row">
									
									<div class="col-md-6 mb-3 service-free-consultation">
										<div class="service-card-compact" style="border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; transition: all 0.3s ease; cursor: pointer;" data-service-id="1">
											<div class="d-flex align-items-start">
												<input type="radio" class="services_item mt-1" name="radioGroup" value="1" id="service_1">
												<div class="ml-3 flex-grow-1">
													<div class="d-flex justify-content-between align-items-start mb-2">
														<div class="flex-grow-1">
															<h6 class="mb-1 font-weight-bold text-dark">Free Consultation</h6>
															<small class="d-block mb-2">15 minutes</small>
															<p class="mb-0" style="font-size: 12px; line-height: 1.5;">
																Perfect for initial inquiries: Quick assessment of your immigration situation, basic visa pathway guidance, and preliminary advice. Available for clients currently within Australia only. Includes initial case evaluation and next steps recommendation.
															</p>
														</div>
														<span class="badge badge-success font-weight-bold ml-2" style="white-space: nowrap;">Free</span>
													</div>
												</div>
											</div>
										</div>
									</div>

										<div class="col-md-6 mb-3">
											<div class="service-card-compact" style="border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; transition: all 0.3s ease; cursor: pointer;" data-service-id="2">
												<div class="d-flex align-items-start">
													<input type="radio" class="services_item mt-1" name="radioGroup" value="2" id="service_2">
													<div class="ml-3 flex-grow-1">
														<div class="d-flex justify-content-between align-items-start mb-2">
															<div class="flex-grow-1">
																<h6 class="mb-1 font-weight-bold text-dark">Comprehensive Migration Advice</h6>
																<small class="d-block mb-2">30 minutes</small>
																<p class="mb-0" style="font-size: 12px; line-height: 1.5;">
																	In-depth professional consultation: Comprehensive case analysis, detailed migration strategy, complex visa applications, ART appeals, visa cancellations, protection visas, and personalized action plans. Suitable for overseas applicants and complex cases.
																</p>
															</div>
															<span class="badge badge-success font-weight-bold ml-2" style="white-space: nowrap;">$150</span>
														</div>
													</div>
												</div>
											</div>
										</div>


										<div class="col-md-6 mb-3">
											<div class="service-card-compact" style="border: 2px solid #e9ecef; border-radius: 8px; padding: 12px; transition: all 0.3s ease; cursor: pointer;" data-service-id="3">
												<div class="d-flex align-items-start">
													<input type="radio" class="services_item mt-1" name="radioGroup" value="3" id="service_3">
													<div class="ml-3 flex-grow-1">
														<div class="d-flex justify-content-between align-items-start mb-2">
															<div class="flex-grow-1">
																<h6 class="mb-1 font-weight-bold text-dark">Overseas Applicant Enquiry</h6>
																<small class="d-block mb-2">30 minutes</small>
																<p class="mb-0" style="font-size: 12px; line-height: 1.5;">
																	In-depth professional consultation: Comprehensive case analysis, detailed migration strategy, complex visa applications, ART appeals, visa cancellations, protection visas, and personalized action plans. Suitable for overseas applicants and complex cases.
																</p>
															</div>
															<span class="badge badge-success font-weight-bold ml-2" style="white-space: nowrap;">$150</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									
								</div>
                                <input type="hidden" id="service_id" name="service_id" value="">
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
                                        <option value="phone"> Phone Call</option>
                                        <option value="in_person">In person</option>
                                        <option value="video_call" id="video_call_option" style="display: none;">Video Call/Zoom</option>
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
                                        <!-- Date & Time Label at Top -->
                                        <div class="form-group">
                                            <label for="description" class="font-weight-bold text-dark mb-3" style="font-size: 16px;">
                                                <i class="fas fa-calendar-clock mr-2" style="color: #667eea;"></i>
                                                Date & Time <span class="span_req">*</span>
                                            </label>

                                            <!-- Modern DateTime Container (Wider) -->
                                            <div class="modern-datetime-container-wrapper">
                                                <div class="modern-datetime-container">
                                                    <div class="datetime-content">
                                                        <div class="calendar-section">
                                                            <div class="section-header">
                                                                <i class="fas fa-calendar-check"></i>
                                                                <span>Select Date</span>
                                                            </div>
                                                            <div class="calendar-wrapper">
                                                                <div id='datetimepicker' class="datePickerCls"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="timeslot-section">
                                                            <div class="section-header">
                                                                <i class="fas fa-clock"></i>
                                                                <span>Available Time Slots</span>
                                                            </div>
                                                            <div class="timeslot-wrapper">
                                                                <!-- Hidden old container for existing JS -->
                                                                <div class="showselecteddate" style="display: none;"></div>
                                                                <div class="timeslots" style="display: none;"></div>
                                                                
                                                                <!-- New Modern UI -->
                                                                <div class="selected-date-display">
                                                                    <div class="date-icon">
                                                                        <i class="fas fa-calendar-day"></i>
                                                                    </div>
                                                                    <div class="date-info">
                                                                        <div class="modern-selected-date">Select a date</div>
                                                                        <div class="modern-selected-day">from the calendar</div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="timeslots-grid">
                                                                    <!-- Time slots will be populated here -->
                                                                </div>
                                                                
                                                                <div class="no-slots-message" style="display: none;">
                                                                    <div class="no-slots-icon">
                                                                        <i class="fas fa-calendar-times"></i>
                                                                    </div>
                                                                    <div class="no-slots-text">
                                                                        <h6>No Available Slots</h6>
                                                                        <p>Please select another date</p>
                                                                    </div>
                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Slot Overwrite at Bottom -->
                                            <div class="slot-overwrite-section mt-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="slot_overwrite" id="slot_overwrite" value="0">
                                                    <label class="custom-control-label" for="slot_overwrite">
                                                        <i class="fas fa-unlock-alt mr-2"></i>Slot Overwrite
                                                    </label>
                                                    <input type="hidden" name="slot_overwrite_hidden" id="slot_overwrite_hidden" value="0">
                                                    </div>
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

                                                <input type="hidden"  id="timeslot_col_date" name="appoint_date" value=""  >
                                                <input type="hidden"  id="timeslot_col_time" name="appoint_time" value=""  >
                                                <span class="timeslot_col_date_time" role="alert" style="display: none;color:#f00;">Date and Time is required.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 text-right pt-3" style="border-top: 1px solid #e9ecef;">
							<button onclick="customValidate('appointform')" type="button" class="btn btn-primary btn-lg px-4" id="appointform_save">
								<i class="fas fa-calendar-check mr-2"></i>Schedule Appointment
							</button>
							<button type="button" class="btn btn-outline-secondary btn-lg px-4 ml-2" data-dismiss="modal">
								<i class="fas fa-times mr-2"></i>Cancel
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
/* Enhanced Appointment Modal Styling */
.add_appointment .modal-content {
	border: none;
	border-radius: 12px;
	box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.add_appointment .modal-body {
	padding: 25px;
	background: #fafbfc;
}

.add_appointment .form-group {
	margin-bottom: 20px;
}

.add_appointment .form-control {
	border: 1px solid #d1d5db;
	border-radius: 8px;
	padding: 10px 15px;
	transition: all 0.3s ease;
}

.add_appointment .form-control:focus {
	border-color: #667eea;
	box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Service Selection Cards */
.service-card-compact {
	transition: all 0.3s ease;
}

.service-card-compact:hover {
	border-color: #667eea !important;
	box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
	transform: translateY(-2px);
}

.service-card-compact.selected {
	border-color: #667eea !important;
	background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

/* Location Selection */
.inperson_address_title {
	padding: 12px 16px;
	border: 2px solid #e9ecef;
	border-radius: 8px;
	margin-right: 15px;
	cursor: pointer;
	transition: all 0.3s ease;
}

.inperson_address_title:hover {
	border-color: #667eea;
	background: rgba(102, 126, 234, 0.05);
}

.inperson_address:checked + .inperson_address_title_span {
	color: #667eea;
	font-weight: 600;
}

/* Form Labels */
.add_appointment .col-form-label {
	font-weight: 600;
	color: #374151;
}

.add_appointment .span_req {
	color: #ef4444;
}

/* Modern DateTime Container - Wider Version */
.modern-datetime-container-wrapper {
	width: 100%;
	margin: 0 auto;
}

.modern-datetime-container {
	background: white;
	border-radius: 8px;
	overflow: hidden;
	border: 1px solid #e2e8f0;
	width: 100%;
}

.datetime-content {
	display: flex;
	min-height: 350px;
	width: 100%;
}

.calendar-section {
	flex: 0 0 45%;
	padding: 8px;
	border-right: 1px solid #e2e8f0;
	background: white;
	min-width: 0;
	display: flex;
	flex-direction: column;
}

.timeslot-section {
	flex: 0 0 55%;
	padding: 8px;
	background: white;
}

.section-header {
	display: flex;
	align-items: center;
	gap: 6px;
	margin-bottom: 8px;
	padding-bottom: 6px;
	border-bottom: 1px solid #e2e8f0;
}

.section-header i {
	color: #667eea;
	font-size: 16px;
}

.section-header span {
	font-weight: 600;
	color: #374151;
	font-size: 16px;
}

.calendar-wrapper {
	background: white;
	border-radius: 6px;
	padding: 6px;
	border: 1px solid #e2e8f0;
	min-height: 300px;
	width: 100%;
	display: block;
	overflow: visible;
}

/* Modern Bootstrap Datepicker Styling */
.calendar-wrapper .datepicker {
	border: none !important;
	box-shadow: none !important;
	background: transparent !important;
	padding: 0 !important;
	margin: 0 !important;
	position: relative !important;
	display: block !important;
}

.calendar-wrapper .datepicker.datepicker-inline {
	width: 100% !important;
	display: block !important;
	min-height: 280px !important;
}

.calendar-wrapper .datepicker table {
	width: 100% !important;
	border-collapse: separate !important;
	border-spacing: 4px !important;
	margin: 0 !important;
	table-layout: fixed !important;
	display: table !important;
}

/* Ensure table header row stays in single line */
.calendar-wrapper .datepicker table thead {
	display: table-header-group !important;
	width: 100% !important;
}

.calendar-wrapper .datepicker table thead tr {
	display: table-row !important;
	width: 100% !important;
	white-space: nowrap !important;
}

.calendar-wrapper .datepicker table thead {
	display: table-header-group !important;
	width: 100% !important;
}

.calendar-wrapper .datepicker table thead tr {
	display: table-row !important;
	width: 100% !important;
}

.calendar-wrapper .datepicker table thead {
	background: white !important;
	border-radius: 8px !important;
	display: table-header-group !important;
	border: 1px solid #e2e8f0 !important;
}

.calendar-wrapper .datepicker table thead tr {
	display: table-row !important;
}

.calendar-wrapper .datepicker table thead tr th {
	display: table-cell !important;
}

/* Ensure month/year text is always visible */
.calendar-wrapper .datepicker table thead tr:first-child th,
.calendar-wrapper .datepicker table thead tr:first-child th * {
	color: #2563eb !important;
	opacity: 1 !important;
	visibility: visible !important;
}

/* Force single-row layout for calendar header */
.calendar-wrapper .datepicker table thead tr:first-child {
	display: table-row !important;
	width: 100% !important;
	table-layout: fixed !important;
	white-space: nowrap !important;
}

.calendar-wrapper .datepicker table thead tr:first-child th {
	background: white !important;
	color: #2563eb !important;
	border: none !important;
	border-radius: 8px 8px 0 0 !important;
	padding: 12px 8px !important;
	font-weight: 700 !important;
	font-size: 15px !important;
	opacity: 1 !important;
	visibility: visible !important;
	display: table-cell !important;
	position: relative !important;
	vertical-align: middle !important;
	white-space: nowrap !important;
	float: none !important;
	line-height: 1.5 !important;
}

/* Previous button - Left side */
.calendar-wrapper .datepicker table thead tr:first-child th.prev {
	text-align: left !important;
	width: 15% !important;
	min-width: 50px !important;
	padding-left: 12px !important;
	padding-right: 4px !important;
}

/* Month/Year name - Center */
.calendar-wrapper .datepicker table thead tr:first-child th.datepicker-switch {
	text-align: center !important;
	width: 70% !important;
	padding: 12px 4px !important;
	white-space: nowrap !important;
	overflow: hidden !important;
	text-overflow: ellipsis !important;
}

/* Next button - Right side */
.calendar-wrapper .datepicker table thead tr:first-child th.next {
	text-align: right !important;
	width: 15% !important;
	min-width: 50px !important;
	display: table-cell !important;
	visibility: visible !important;
	opacity: 1 !important;
	padding-right: 12px !important;
	padding-left: 4px !important;
}

.calendar-wrapper .datepicker table thead tr:first-child th.next,
.calendar-wrapper .datepicker table thead tr:first-child th.next * {
	display: inline-block !important;
	visibility: visible !important;
	opacity: 1 !important;
}

.calendar-wrapper .datepicker table thead tr:first-child th .datepicker-switch,
.calendar-wrapper .datepicker table thead tr:first-child th.datepicker-switch {
	color: #2563eb !important;
	opacity: 1 !important;
	visibility: visible !important;
	display: block !important;
	text-shadow: none !important;
	font-weight: 700 !important;
	text-align: center !important;
	width: 100% !important;
}

.calendar-wrapper .datepicker table thead tr:last-child th {
	background: #667eea !important;
	color: white !important;
	border: none !important;
	padding: 10px 8px !important;
	font-weight: 600 !important;
	font-size: 13px !important;
}

.calendar-wrapper .datepicker table tbody {
	display: table-row-group !important;
}

.calendar-wrapper .datepicker table tbody td {
	border: none !important;
	padding: 2px !important;
	text-align: center !important;
	display: table-cell !important;
	vertical-align: middle !important;
}

.calendar-wrapper .datepicker table tbody td.day {
	width: 36px !important;
	height: 36px !important;
	line-height: 36px !important;
	font-weight: 500 !important;
	color: #374151 !important;
	background: #f8fafc !important;
	border: 1px solid #e2e8f0 !important;
	border-radius: 8px !important;
	transition: all 0.3s ease !important;
	cursor: pointer !important;
}

.calendar-wrapper .datepicker table tbody td.day:hover {
	background: #667eea !important;
	color: white !important;
	border-color: #667eea !important;
	transform: scale(1.1) !important;
}

.calendar-wrapper .datepicker table tbody td.active,
.calendar-wrapper .datepicker table tbody td.active.active,
.calendar-wrapper .datepicker table tbody td.selected {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
	color: white !important;
	border-color: #667eea !important;
	font-weight: 700 !important;
	transform: scale(1.1) !important;
	box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
}

.calendar-wrapper .datepicker table tbody td.old,
.calendar-wrapper .datepicker table tbody td.new {
	color: #cbd5e1 !important;
	background: #f1f5f9 !important;
	opacity: 0.6 !important;
}

.calendar-wrapper .datepicker table tbody td.disabled,
.calendar-wrapper .datepicker table tbody td.disabled.day {
	color: #cbd5e1 !important;
	background: #f1f5f9 !important;
	cursor: not-allowed !important;
	opacity: 0.4 !important;
	position: relative !important;
}

.calendar-wrapper .datepicker table tbody td.disabled::after {
	content: '✕' !important;
	position: absolute !important;
	top: 50% !important;
	left: 50% !important;
	transform: translate(-50%, -50%) !important;
	color: #ef4444 !important;
	font-weight: bold !important;
	font-size: 14px !important;
}

.calendar-wrapper .datepicker table tbody td.disabled:hover {
	background: #f1f5f9 !important;
	color: #cbd5e1 !important;
	transform: none !important;
}

.calendar-wrapper .datepicker .prev,
.calendar-wrapper .datepicker .next {
	color: #2563eb !important;
	font-size: 18px !important;
	font-weight: bold !important;
	opacity: 1 !important;
	visibility: visible !important;
	transition: all 0.3s !important;
	cursor: pointer !important;
	display: inline-block !important;
	position: relative !important;
	vertical-align: middle !important;
	background: transparent !important;
	border: none !important;
	width: auto !important;
	height: auto !important;
	padding: 4px 8px !important;
	line-height: 1.5 !important;
	float: none !important;
	clear: none !important;
	white-space: nowrap !important;
}

.calendar-wrapper .datepicker table thead tr:first-child th.prev,
.calendar-wrapper .datepicker table thead tr:first-child th.next {
	display: table-cell !important;
	visibility: visible !important;
	opacity: 1 !important;
}

.calendar-wrapper .datepicker table thead tr:first-child th.prev .prev,
.calendar-wrapper .datepicker table thead tr:first-child th.next .next {
	display: inline-block !important;
	visibility: visible !important;
	opacity: 1 !important;
}

.calendar-wrapper .datepicker .prev:hover,
.calendar-wrapper .datepicker .next:hover {
	opacity: 1 !important;
	visibility: visible !important;
	background: rgba(37, 99, 235, 0.1) !important;
	color: #1e40af !important;
	transform: scale(1.1) !important;
}

/* Ensure next button is always visible and properly positioned */
.calendar-wrapper .datepicker table thead tr:first-child th.next,
.calendar-wrapper .datepicker table thead tr:first-child th.next *,
.calendar-wrapper .datepicker table thead tr:first-child th.next::before,
.calendar-wrapper .datepicker table thead tr:first-child th.next::after {
	visibility: visible !important;
	opacity: 1 !important;
	display: inline-block !important;
}

.calendar-wrapper .datepicker table thead tr:first-child th.next {
	min-width: 40px !important;
}

.calendar-wrapper .datepicker .datepicker-switch {
	color: #2563eb !important;
	font-weight: 700 !important;
	font-size: 15px !important;
	opacity: 1 !important;
	visibility: visible !important;
	display: block !important;
	text-shadow: none !important;
}

.calendar-wrapper .datepicker .datepicker-switch:hover {
	background: rgba(37, 99, 235, 0.1) !important;
	opacity: 1 !important;
	color: #1e40af !important;
}

.timeslot-wrapper {
	background: white;
	border-radius: 6px;
	padding: 6px;
	border: 1px solid #e2e8f0;
	min-height: 250px;
}

.selected-date-display {
	background: #667eea;
	color: white;
	padding: 8px;
	border-radius: 4px;
	margin-bottom: 8px;
	display: flex;
	align-items: center;
	gap: 8px;
}

.date-icon {
	width: 30px;
	height: 30px;
	background: rgba(255, 255, 255, 0.3);
	border-radius: 4px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 12px;
}

.date-info .modern-selected-date {
	font-weight: 600;
	font-size: 14px;
	margin: 0;
	line-height: 1.2;
}

.date-info .modern-selected-day {
	font-size: 12px;
	opacity: 0.9;
	margin: 0;
	line-height: 1.2;
}

.timeslots-grid {
	display: grid !important;
	grid-template-columns: repeat(2, 1fr) !important;
	grid-template-rows: repeat(auto-fit, 35px) !important;
	gap: 8px !important;
	max-height: 180px;
	overflow-y: auto;
	padding-right: 4px;
}

.timeslots-grid::-webkit-scrollbar {
	width: 4px;
}

.timeslots-grid::-webkit-scrollbar-track {
	background: #f1f5f9;
	border-radius: 2px;
}

.timeslots-grid::-webkit-scrollbar-thumb {
	background: #cbd5e1;
	border-radius: 2px;
}

.timeslots-grid::-webkit-scrollbar-thumb:hover {
	background: #94a3b8;
}

.timeslots-grid .timeslot {
	background: white;
	color: #374151;
	border: 1px solid #e2e8f0;
	border-radius: 4px;
	padding: 6px 8px;
	font-size: 12px;
	font-weight: 500;
	cursor: pointer;
	transition: border-color 0.2s ease;
	text-align: center;
	position: relative;
	overflow: hidden;
	white-space: nowrap;
	height: 30px;
	line-height: 18px;
	display: flex;
	align-items: center;
	justify-content: center;
}

.timeslots-grid .timeslot::before {
	content: '';
	position: absolute;
	top: 0;
	left: -100%;
	width: 100%;
	height: 100%;
	background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
	transition: left 0.5s;
}

.timeslots-grid .timeslot:hover::before {
	left: 100%;
}

.timeslots-grid .timeslot:hover {
	border-color: #667eea;
	background: #667eea;
	color: white;
}

.timeslots-grid .timeslot.selected {
	border-color: #667eea;
	background: #667eea;
	color: white;
	font-weight: 600;
}

.timeslots-grid .timeslot.disabled {
	background: #f8fafc;
	color: #94a3b8;
	border-color: #e2e8f0;
	cursor: not-allowed;
	opacity: 0.6;
	position: relative;
}

.timeslots-grid .timeslot.disabled::after {
	content: '✕';
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	color: #ef4444;
	font-weight: bold;
	font-size: 16px;
}

.timeslots-grid .timeslot.disabled:hover {
	border-color: #e2e8f0;
	background: white;
}

.no-slots-message {
	text-align: center;
	padding: 40px 20px;
	display: none;
	background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
	border-radius: 12px;
	border: 2px dashed #cbd5e1;
}

.no-slots-icon {
	color: #94a3b8;
	font-size: 48px;
	margin-bottom: 15px;
}

.no-slots-text h6 {
	color: #64748b;
	font-weight: 600;
	margin-bottom: 5px;
	font-size: 18px;
}

.no-slots-text p {
	color: #94a3b8;
	font-size: 14px;
	margin: 0;
}

/* Ensure proper spacing */
.timeslot-wrapper {
	position: relative;
	min-height: 300px;
}

.timeslot-wrapper .no-slots-message {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: calc(100% - 40px);
	margin: 0;
}

/* Slot Overwrite Section Styling */
.slot-overwrite-section {
	padding: 6px 10px;
	background: #f8fafc;
	border-radius: 4px;
	border: 1px solid #e2e8f0;
	margin-top: 10px;
}

.slot-overwrite-section .custom-control-label {
	font-weight: 500;
	color: #374151;
	cursor: pointer;
}

.slot-overwrite-section .custom-control-label i {
	color: #667eea;
}

.slot-overwrite-section .custom-control-input:checked ~ .custom-control-label {
	color: #667eea;
	font-weight: 600;
}

/* Date & Time Label Styling */
.form-group > label[style*="font-size: 16px"] {
	display: block;
	padding-bottom: 6px;
	border-bottom: 1px solid #e2e8f0;
	margin-bottom: 10px !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
	.modern-datetime-container {
		margin: 10px;
		border-radius: 12px;
	}
	
	.datetime-content {
		flex-direction: column;
		min-height: auto;
	}
	
	.calendar-section {
		flex: 1 1 auto;
		border-right: none;
		border-bottom: 1px solid #e2e8f0;
		padding: 20px;
	}
	
	.timeslot-section {
		flex: 1 1 auto;
		padding: 20px;
	}
	
	.timeslots-grid {
		grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
		gap: 10px;
	}
	
	.timeslots-grid .timeslot {
		font-size: 13px;
		padding: 10px 12px;
	}
	
	.datetime-header {
		padding: 15px;
	}
	
	.datetime-icon {
		width: 40px;
		height: 40px;
		font-size: 16px;
	}
	
	.datetime-title h6 {
		font-size: 16px;
	}
	
	.datetime-title p {
		font-size: 13px;
	}
	
	.section-header {
		margin-bottom: 15px;
		padding-bottom: 10px;
	}
	
	.section-header span {
		font-size: 15px;
	}
	
	.calendar-wrapper, .timeslot-wrapper {
		padding: 15px;
		min-height: auto;
	}
	
	.calendar-wrapper .datepicker.datepicker-inline {
		min-height: auto !important;
	}
	
	.selected-date-display {
		padding: 12px;
		margin-bottom: 15px;
	}
	
	.date-icon {
		width: 35px;
		height: 35px;
	}
	
	.add_appointment .modal-dialog {
		margin: 10px;
		max-width: calc(100% - 20px);
	}
	
	.service-card-compact {
		margin-bottom: 15px;
	}
	
	.inperson_address_header {
		flex-direction: column;
		gap: 10px;
	}
	
	.inperson_address_title {
		margin-right: 0;
		margin-bottom: 10px;
		width: 100%;
	}
}

@media (max-width: 480px) {
	.datetime-content {
		padding: 15px;
	}
	
	.calendar-section, .timeslot-section {
		padding: 15px;
	}
	
	.timeslots-grid {
		grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
		gap: 8px;
	}
	
	.timeslots-grid .timeslot {
		font-size: 12px;
		padding: 8px 10px;
	}
	
	.datetime-header {
		padding: 12px;
		flex-direction: column;
		text-align: center;
		gap: 10px;
	}
	
	.datetime-title h6 {
		font-size: 15px;
	}
	
	.datetime-title p {
		font-size: 12px;
	}
}
</style>

<script>
// Service selection functionality
function selectService(serviceId) {
	// Remove selected class from all cards
	document.querySelectorAll('.service-card-compact').forEach(card => {
		card.classList.remove('selected');
	});
	
	// Add selected class to clicked card
	event.currentTarget.classList.add('selected');
	
	// Check the radio button
	document.getElementById('service_' + serviceId).checked = true;
	document.getElementById('service_id').value = serviceId;
	
	// Show appointment details section
	if (typeof $ !== 'undefined') {
	$('#appointment_details').show();
	} else {
		document.getElementById('appointment_details').style.display = 'block';
	}
}

// Function to toggle Video Call option visibility based on service selection
function toggleVideoCallOption(serviceId) {
	const videoCallOption = document.getElementById('video_call_option');
	const appointmentDetailsSelect = document.querySelector('.appointment_item');
	
	if (videoCallOption && appointmentDetailsSelect) {
		// Service ID 1 = Free Consultation - hide Video Call
		// Service ID 2 = Comprehensive Migration Advice - show Video Call
		// Service ID 3 = Overseas Applicant Enquiry - show Video Call
		if (serviceId == '1') {
			// Hide Video Call option for Free Consultation
			videoCallOption.style.display = 'none';
			// If Video Call is currently selected, reset to empty
			if (appointmentDetailsSelect.value === 'video_call') {
				appointmentDetailsSelect.value = '';
			}
		} else if (serviceId == '2' || serviceId == '3') {
			// Show Video Call option for Comprehensive Migration Advice and Overseas Applicant Enquiry
			videoCallOption.style.display = 'block';
		}
	}
}

// Auto-select radio when card is clicked using event delegation
document.addEventListener('DOMContentLoaded', function() {
	// Initialize Video Call option as hidden when modal opens
	$(document).on('shown.bs.modal', '#create_appoint', function() {
		const videoCallOption = document.getElementById('video_call_option');
		if (videoCallOption) {
			videoCallOption.style.display = 'none';
		}
		// Reset appointment details dropdown
		const appointmentDetailsSelect = document.querySelector('.appointment_item');
		if (appointmentDetailsSelect) {
			appointmentDetailsSelect.value = '';
		}
		// Reset Nature of Enquiry and show all services by default
		const enquirySelect = document.querySelector('.enquiry_item');
		if (enquirySelect) {
			enquirySelect.value = '';
		}
		// Show Free Consultation service by default when modal opens
		const freeConsultationService = document.querySelector('.service-free-consultation');
		if (freeConsultationService) {
			freeConsultationService.style.display = 'block';
		}
		// Reset service selection
		document.querySelectorAll('.services_item').forEach(radio => {
			radio.checked = false;
		});
		document.getElementById('service_id').value = '';
		// Remove selected class from all service cards
		document.querySelectorAll('.service-card-compact').forEach(card => {
			card.classList.remove('selected');
		});
		// Hide services and appointment sections
		document.getElementById('services').style.display = 'none';
		document.getElementById('appointment_details').style.display = 'none';
		document.getElementById('info').style.display = 'none';
	});
	
	// Reset form when modal is hidden
	$(document).on('hidden.bs.modal', '#create_appoint', function() {
		// Show Free Consultation service by default when modal is closed (for next open)
		const freeConsultationService = document.querySelector('.service-free-consultation');
		if (freeConsultationService) {
			freeConsultationService.style.display = 'block';
		}
	});
	
	// Use event delegation to handle clicks on service cards
	document.addEventListener('click', function(e) {
		if (e.target.closest('.service-card-compact')) {
			const card = e.target.closest('.service-card-compact');
			const serviceId = card.getAttribute('data-service-id');
			
			if (serviceId) {
				// Remove selected class from all cards
				document.querySelectorAll('.service-card-compact').forEach(c => {
					c.classList.remove('selected');
				});
				
				// Add selected class to clicked card
				card.classList.add('selected');
				
				// Check the radio button
				const radio = card.querySelector('input[type="radio"]');
			if (radio) {
				radio.checked = true;
				document.getElementById('service_id').value = radio.value;
			}
				
				// Toggle Video Call option visibility based on service
				toggleVideoCallOption(serviceId);
				
				// Show appointment details section
				if (typeof $ !== 'undefined') {
					$('#appointment_details').show();
				} else {
					document.getElementById('appointment_details').style.display = 'block';
				}
			}
		}
	});

	// Handle Nature of Enquiry selection
	document.addEventListener('change', function(e) {
		if (e.target.classList.contains('enquiry_item')) {
			var selectedValue = e.target.value;
			if (selectedValue) {
				document.getElementById('services').style.display = 'block';
				
				// Hide Free Consultation if Nature of Enquiry is "INDIA/UK/CANADA/EUROPE TO AUSTRALIA" (value="8")
				// Show Free Consultation for all other options
				const freeConsultationService = document.querySelector('.service-free-consultation');
				if (freeConsultationService) {
					if (selectedValue === '8') {
						// Hide Free Consultation for INDIA/UK/CANADA/EUROPE TO AUSTRALIA
						freeConsultationService.style.display = 'none';
						// Uncheck Free Consultation if it was selected
						const freeConsultationRadio = document.getElementById('service_1');
						if (freeConsultationRadio && freeConsultationRadio.checked) {
							freeConsultationRadio.checked = false;
							document.getElementById('service_id').value = '';
							// Remove selected class from Free Consultation card
							const freeConsultationCard = freeConsultationService.querySelector('.service-card-compact');
							if (freeConsultationCard) {
								freeConsultationCard.classList.remove('selected');
							}
							// Hide appointment details if Free Consultation was selected
							document.getElementById('appointment_details').style.display = 'none';
							document.getElementById('info').style.display = 'none';
						}
					} else {
						// Show Free Consultation for all other options
						freeConsultationService.style.display = 'block';
					}
				}
			} else {
				document.getElementById('services').style.display = 'none';
				document.getElementById('appointment_details').style.display = 'none';
				document.getElementById('info').style.display = 'none';
			}
		}
		
		if (e.target.classList.contains('services_item')) {
			if (e.target.checked) {
				const serviceId = e.target.value;
				// Toggle Video Call option visibility based on service
				toggleVideoCallOption(serviceId);
				document.getElementById('appointment_details').style.display = 'block';
			}
		}
		
		if (e.target.classList.contains('appointment_item')) {
			var selectedValue = e.target.value;
		if (selectedValue) {
				document.getElementById('info').style.display = 'block';
		} else {
				document.getElementById('info').style.display = 'none';
			}
		}
	});

	// Modern Appointment Booking Enhancement System
	(function() {
		'use strict';
		
		let timeslotObserver = null;
		let dateObserver = null;
		
		// Function to enhance timeslots with modern design
		function enhanceTimeslots() {
			const oldTimeslots = document.querySelector('.timeslots');
			const modernGrid = document.querySelector('.timeslots-grid');
			const noSlotsMsg = document.querySelector('.no-slots-message');
			
			if (!oldTimeslots || !modernGrid) return;
			
			// Get all timeslot_col elements (correct class name from detail-main.js)
			const oldSlots = oldTimeslots.querySelectorAll('.timeslot_col');
			
			if (oldSlots.length > 0) {
				// Clear modern grid
				modernGrid.innerHTML = '';
				modernGrid.style.display = 'grid';
				modernGrid.style.gridTemplateColumns = 'repeat(2, 1fr)';
				modernGrid.style.gap = '10px';
				if (noSlotsMsg) noSlotsMsg.style.display = 'none';
				
				// Copy each slot to modern grid
				oldSlots.forEach((oldSlot) => {
					const modernSlot = document.createElement('div');
					modernSlot.className = 'timeslot';
					
					// Extract time text
					const timeText = oldSlot.querySelector('span') ? 
						oldSlot.querySelector('span').textContent : 
						oldSlot.textContent;
					
					modernSlot.textContent = timeText;
					modernSlot.dataset.fromtime = oldSlot.dataset.fromtime;
					modernSlot.dataset.totime = oldSlot.dataset.totime;
					
					// Add click handler
					modernSlot.addEventListener('click', function() {
						// Remove selected from all modern slots
						modernGrid.querySelectorAll('.timeslot').forEach(s => 
							s.classList.remove('selected')
						);
						
						// Add selected to this slot
						this.classList.add('selected');
						
						// Click the corresponding old slot
						oldSlot.click();
					});
					
					// Check if old slot has active class
					if (oldSlot.classList.contains('active') || 
					    oldSlot.classList.contains('selected')) {
						modernSlot.classList.add('selected');
					}
					
					modernGrid.appendChild(modernSlot);
				});
			} else {
				// No slots available
				modernGrid.innerHTML = '';
				modernGrid.style.display = 'none';
				if (noSlotsMsg) noSlotsMsg.style.display = 'block';
			}
		}
		
		// Function to update modern date display
		function updateDateDisplay() {
			const oldDateDisplay = document.querySelector('.showselecteddate');
			const modernDate = document.querySelector('.modern-selected-date');
			const modernDay = document.querySelector('.modern-selected-day');
			
			if (!oldDateDisplay || !modernDate || !modernDay) return;
			
			const dateText = oldDateDisplay.textContent.trim();
			
			if (dateText) {
				modernDate.textContent = dateText;
				modernDay.textContent = 'Selected Date';
			} else {
				modernDate.textContent = 'Select a date';
				modernDay.textContent = 'from the calendar';
			}
		}
		
		// Initialize MutationObserver for timeslots
		function initTimeslotObserver() {
			const oldTimeslots = document.querySelector('.timeslots');
			if (!oldTimeslots) return;
			
			timeslotObserver = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					if (mutation.type === 'childList') {
						enhanceTimeslots();
					}
				});
			});
			
			timeslotObserver.observe(oldTimeslots, {
				childList: true,
				subtree: true
			});
		}
		
		// Initialize MutationObserver for date display
		function initDateObserver() {
			const oldDateDisplay = document.querySelector('.showselecteddate');
			if (!oldDateDisplay) return;
			
			dateObserver = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					if (mutation.type === 'childList' || mutation.type === 'characterData') {
						updateDateDisplay();
					}
				});
			});
			
			dateObserver.observe(oldDateDisplay, {
				childList: true,
				characterData: true,
				subtree: true
			});
		}
		
		// Initialize everything when DOM is ready
		function init() {
			// Initial enhancement
			enhanceTimeslots();
			updateDateDisplay();
			
			// Start observing
			initTimeslotObserver();
			initDateObserver();
			
			// Also enhance on AJAX complete (catches datepicker init)
			if (typeof $ !== 'undefined') {
				$(document).ajaxSuccess(function(event, xhr, settings) {
					if (settings.url && (
						settings.url.includes('getDateTimeBackend') || 
						settings.url.includes('getDisabledDateTime')
					)) {
						setTimeout(function() {
							enhanceTimeslots();
							updateDateDisplay();
						}, 100);
					}
				});
			}
		}
		
		// Start when DOM is ready
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init);
		} else {
			init();
		}
		
		// Also try init after a short delay (for modal load)
		setTimeout(init, 500);
		
	})();
});
</script>

