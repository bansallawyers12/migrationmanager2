@extends('layouts.admin_client_detail')
@section('title', 'Show Appointment')

@section('content')
<style>
/* Force prevent horizontal scroll on this page */
html, body {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

/* Fix horizontal scrollbar issue - Force override */
.card-body {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

.main-content {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

.section-body {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

/* Form specific fixes */
.form-group {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

.cus_field_input {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

/* Ensure form controls don't overflow */
.form-control {
    max-width: 100% !important;
    box-sizing: border-box !important;
}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section" style="margin-top: 56px;">
		<div class="section-body">
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
                    <div class="col-12 col-md-12 col-lg-12">
							<div class="card-header">
								<h4 style="color:#000000;">Show Appointment</h4>
								<div class="card-header-action">
									<a href="{{route('appointments.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
					</div>
						<div id="accordion">
							<div class="accordion">

								<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
									<div class="row">

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="client" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Client name</label>
												<div class="cus_field_input">
													<input type="text" name="client_id" value="{{ @$appointment->clients->first_name.' '.@$appointment->clients->last_name }}" class="form-control" autocomplete="off" readonly>
												</div>
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="user" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Added By</label>
												{{--@if($appointment->user)--}}
												{{--Form::text('user_id', @$appointment->user->first_name.' '.@$appointment->user->last_name, array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))--}}
												{{--@else--}}
												{{--Form::text('user_id', 'N/A', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))--}}
												{{--@endif--}}

                                                @if($appointment->user)
                                                <input type="text" name="user_id" value="{{ @$appointment->user->first_name.' '.$appointment->user->last_name }}" class="form-control" autocomplete="off" placeholder="Enter User Name" readonly>
												@else
                                                <input type="text" name="user_id" value="N/A" class="form-control" autocomplete="off" placeholder="Enter User Name" readonly>

												@endif
											</div>
										</div>

									</div>

                                    <div class="row">

										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="date" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Date</label>
												<div class="cus_field_input">
													<input type="text" name="date" value="{{ isset($appointment->date) ? date('d/m/Y', strtotime($appointment->date)) : 'N/A' }}" class="form-control" data-valid="" autocomplete="off" readonly>
												</div>
											</div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="time" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Time </label>
												<input type="text" name="time" value="{{ isset($appointment->timeslot_full) ? $appointment->timeslot_full : 'N/A' }}" class="form-control" autocomplete="off" readonly>
											</div>
										</div>
									</div>



									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="invites" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Nature of Enquiry</label>
													<div class="cus_field_input">
														<input type="text" name="nature_of_enquiry" value="{{ isset($appointment->natureOfEnquiry->title) ? $appointment->natureOfEnquiry->title : 'N/A' }}" class="form-control" autocomplete="off" readonly>
                                                    </div>
												</div>
											</div>
											<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="status" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Service </label>
													<input type="text" name="service" value="{{ isset($appointment->service->title) ? $appointment->service->title : 'N/A' }}" class="form-control" autocomplete="off" readonly>
												</div>
											</div>
										</div>
									</div>

                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="description" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Description </label>
												<textarea name="description" class="form-control" autocomplete="off" readonly>{{ isset($appointment->description) ? $appointment->description : 'N/A' }}</textarea>
											</div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="status" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Status <span class="span_req">*</span></label>
                                                <select class="form-control" name="status" data-valid="required" disabled>
                                                    <option value="0" <?php echo ($appointment->status == '0') ? 'selected' : ''; ?>>Pending/Not confirmed</option>
                                                    <option value="2" <?php echo ($appointment->status == '2') ? 'selected' : ''; ?>>Completed</option>

                                                    <option value="4" <?php echo ($appointment->status == '4') ? 'selected' : ''; ?>>N/P</option>

                                                    <option value="6" <?php echo ($appointment->status == '6') ? 'selected' : ''; ?>>Did Not Come</option>
                                                    <option value="7" <?php echo ($appointment->status == '7') ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="8" <?php echo ($appointment->status == '8') ? 'selected' : ''; ?>>Missed</option>
                                                    <option value="9" <?php echo ($appointment->status == '9') ? 'selected' : ''; ?>>Payment Pending</option>
                                                    <option value="10" <?php echo ($appointment->status == '10') ? 'selected' : ''; ?>>Payment Success</option>
                                                    <option value="11" <?php echo ($appointment->status == '11') ? 'selected' : ''; ?>>Payment Failed</option>
                                                </select>
                                            </div>
										</div>
									</div>

                                    <div class="row">
                                      	<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
                                                <label for="appointment_details" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Appointment details <span class="span_req">*</span></label>
                                                <select data-valid="required" class="form-control" name="appointment_details" disabled>
                                                    <option value="">Select</option>
                                                    <option value="phone" <?php echo ($appointment->appointment_details == 'phone') ? 'selected' : ''; ?>> Phone</option>
                                                    <option value="in_person" <?php echo ($appointment->appointment_details == 'in_person') ? 'selected' : ''; ?>>In person</option>
                                                  <option value="zoom_google_meeting" <?php echo ($appointment->appointment_details == 'zoom_google_meeting') ? 'selected' : ''; ?>>Zoom / Google Meeting</option>
                                                </select>

                                                @if ($errors->has('appointment_details'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('appointment_details') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="preferred_language" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Preferred Language </label>
												<select class="form-control" name="preferred_language" disabled>
                                                    <option value="">Select</option>
                                                    <option value="Hindi" <?php echo ($appointment->preferred_language == 'Hindi') ? 'selected' : ''; ?>> Hindi</option>
                                                    <option value="English" <?php echo ($appointment->preferred_language == 'English') ? 'selected' : ''; ?>>English</option>
                                                    <option value="Punjabi" <?php echo ($appointment->preferred_language == 'Punjabi') ? 'selected' : ''; ?>>Punjabi</option>
                                                </select>
                                            </div>
										</div>

                                    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</section>
</div>
@endsection
