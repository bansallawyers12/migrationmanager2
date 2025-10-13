@extends('layouts.admin')
@section('title', 'Edit Appointment')


@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
            <div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
		<form action="{{ route('appointments.update',$appointment['id']) }}" method="POST">
        @csrf
        @method('PUT')
		<input type="hidden" name="id" value="{{ @$appointment['id'] }}">
				<!-- <div class="row"> -->
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
                    <div class="col-12 col-md-12 col-lg-12">
						<!-- <div class="card"> -->
							<div class="card-header">
								<h4>Edit Appointment</h4>
								<div class="card-header-action">
									<a href="{{route('appointments.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						<!-- </div> -->
					</div>
						<div id="accordion">
							<div class="accordion">
								<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
									<div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="client">Client name</label>
												<div class="cus_field_input">
													<input type="text" name="client_name" value="{{ isset($appointment['client']['name']) ? $appointment['client']['name'] : 'N/A' }}" class="form-control" readonly>
												</div>
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
												<label for="user">Added By</label>
                                                <input type="text" name="user_name" value="{{ isset($appointment['user']['name']) ? $appointment['user']['name'] : 'N/A' }}" class="form-control" readonly>
											</div>
										</div>
                                    </div>

                                    <div class="row">
										<!--<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="timezone">Timezone </label>
												{{-- <input type="text" name="timezone" value="{{ @$appointment->timezone }}" class="form-control" readonly data-valid="required" autocomplete="off" placeholder="Select timezone"> --}}
												{{--@if ($errors->has('timezone'))--}}
													<span class="custom-error" role="alert">
														<strong>{{--@$errors->first('timezone')--}}</strong>
													</span>
												{{--@endif--}}
											</div>
										</div>-->
									    <!-- dd('dfsdfg'); -->
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="date">Date <span class="span_req">*</span></label>
												<div class="cus_field_input">
													@php
														$dateValue = '';
														if(isset($appointment['date']) && $appointment['date'] != "") {
															$dateArr = explode('-', $appointment['date']);
															$dateValue = $dateArr[2].'/'.$dateArr[1].'/'.$dateArr[0];
														}
													@endphp
													<input type="text" name="date" value="{{ $dateValue }}" class="form-control date" data-valid="required" autocomplete="off" placeholder="Select date">
													@if ($errors->has('date'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('date') }}</strong>
														</span>
													@endif
												</div>
											</div>
										</div>
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="time">Time <span class="span_req">*</span></label>
												<select class="form-control" name="time" id="followup_time" data-valid="required">
													<option value="">Select Time</option>
													@for($hour = 8; $hour <= 18; $hour++)
														@for($minute = 0; $minute < 60; $minute += 15)
															@php
																$timeValue = sprintf('%02d:%02d', $hour, $minute);
																$timeDisplay = sprintf('%02d:%02d', $hour, $minute);
																$isSelected = '';
																if (isset($appointment['time'])) {
																	// Handle both string and array formats
																	$appointmentTime = is_array($appointment['time']) ? $appointment['time'] : $appointment['time'];
																	
																	// Convert API time format (15:45:00) to dropdown format (15:45)
																	$apiTimeFormatted = '';
																	if (strpos($appointmentTime, ':') !== false) {
																		$timeParts = explode(':', $appointmentTime);
																		$apiTimeFormatted = $timeParts[0] . ':' . $timeParts[1]; // Remove seconds
																	}
																	
																	$isSelected = ($apiTimeFormatted == $timeValue) ? 'selected' : '';
																}
															@endphp
															<option value="{{ $timeValue }}" {{ $isSelected }}>{{ $timeDisplay }}</option>
														@endfor
													@endfor
												</select>
												@if ($errors->has('time'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('time') }}</strong>
													</span>
												@endif
											</div>
										</div>
									</div>

                                    <!--<div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="title">Title <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="title"></div>
													{{-- <input type="text" name="title" value="{{ @$appointment->title }}" class="form-control" data-valid="" autocomplete="off" placeholder="Enter title"> --}}
													{{--@if ($errors->has('title'))--}}
														<span class="custom-error" role="alert">
															<strong>{{--@$errors->first('title')--}}</strong>
														</span>
													{{--@endif--}}
												</div>
											</div>
										</div>
									</div>-->

									<!--<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="time">Full name <span class="span_req">*</span></label>
												{{-- <input type="text" name="full_name" value="{{ @$appointment->full_name }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter full name"> --}}
												{{--@if ($errors->has('full_name'))--}}
													<span class="custom-error" role="alert">
														<strong>{{--@$errors->first('full_name')--}}</strong>
													</span>
												{{--@endif--}}
											</div>
										</div>
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="title">Email <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="title">

													</div>
													{{-- <input type="email" name="email" value="{{ @$appointment->email }}" class="form-control" data-valid="" autocomplete="off" placeholder="Enter email"> --}}
													{{--@if ($errors->has('email'))--}}
														<span class="custom-error" role="alert">
															<strong>{{--@$errors->first('email')--}}</strong>
														</span>
													{{--@endif--}}
												</div>
											</div>
										</div>
									</div>-->

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
											    <label for="noe_id">Nature of Enquiry</label>
											    <input type="text" name="nature_of_enquiry" value="{{ isset($appointment['nature_of_enquiry']['title']) ? $appointment['nature_of_enquiry']['title'] : 'N/A' }}" class="form-control" readonly>
										    </div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="service">Service</label>
                                                <input type="text" name="service" value="{{ isset($appointment['service']['title']) ? $appointment['service']['title'] : 'N/A' }}" class="form-control" readonly>
                                            </div>
                                        </div>


										<!--<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="invites">Invites <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="invites">
														<input class="invites" id="invites" type="text" name="invites" readonly >
													</div>
													{{-- <input type="number" name="invites" value="{{ @$appointment->invites }}" class="form-control" data-valid="" autocomplete="off" placeholder="Enter invites"> --}}
													{{--@if ($errors->has('invites'))--}}
														<span class="custom-error" role="alert">
															<strong>{{--@$errors->first('invites')--}}</strong>
														</span>
													{{--@endif--}}
												</div>
											</div>
										</div>-->
									</div>
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="description">Description <span class="span_req">*</span></label>
												<input type="text" name="description" value="{{ isset($appointment['description']) ? $appointment['description'] : '' }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter description">
												@if ($errors->has('description'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('description') }}</strong>
													</span>
												@endif
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="status">Status <span class="span_req">*</span></label>
												<select class="form-control" name="status" data-valid="required">
                                                    <option value="0" <?php echo (isset($appointment['status']) && $appointment['status'] == '0') ? 'selected' : ''; ?>>Pending/Not confirmed</option>
                                                    <option value="2" <?php echo (isset($appointment['status']) && $appointment['status'] == '2') ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="4" <?php echo (isset($appointment['status']) && $appointment['status'] == '4') ? 'selected' : ''; ?>>N/P</option>
                                                    <option value="6" <?php echo (isset($appointment['status']) && $appointment['status'] == '6') ? 'selected' : ''; ?>>Did Not Come</option>
                                                    <option value="7" <?php echo (isset($appointment['status']) && $appointment['status'] == '7') ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="8" <?php echo (isset($appointment['status']) && $appointment['status'] == '8') ? 'selected' : ''; ?>>Missed</option>
                                                    <option value="9" <?php echo (isset($appointment['status']) && $appointment['status'] == '9') ? 'selected' : ''; ?>>Payment Pending</option>
                                                    <option value="10" <?php echo (isset($appointment['status']) && $appointment['status'] == '10') ? 'selected' : ''; ?>>Payment Success</option>
                                                    <option value="11" <?php echo (isset($appointment['status']) && $appointment['status'] == '11') ? 'selected' : ''; ?>>Payment Failed</option>
                                                </select>
												@if ($errors->has('status'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('status') }}</strong>
													</span>
												@endif
											</div>
										</div>
									</div>
                                  
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="appointment_details">Appointment details <span class="span_req">*</span></label>
                                                <select data-valid="required" class="form-control" name="appointment_details">
                                                    <option value="">Select</option>
                                                    <option value="phone" <?php echo (isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'phone') ? 'selected' : ''; ?>>Phone</option>
                                                    <option value="in_person" <?php echo (isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'in_person') ? 'selected' : ''; ?>>In person</option>
                                                    @if(isset($appointment['service']['title']) && $appointment['service']['title'] == 'Migration Advice')
                                                        <option value="zoom_google_meeting" <?php echo (isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'zoom_google_meeting') ? 'selected' : ''; ?>>Zoom / Google Meeting</option>
                                                    @endif
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
												<label for="preferred_language">Preferred Language <span class="span_req">*</span></label>
                                                <select class="form-control" name="preferred_language" data-valid="required">
                                                    <option value="">Select</option>
                                                    <option value="Hindi" <?php echo (isset($appointment['preferred_language']) && $appointment['preferred_language'] == 'Hindi') ? 'selected' : ''; ?>>Hindi</option>
                                                    <option value="English" <?php echo (isset($appointment['preferred_language']) && $appointment['preferred_language'] == 'English') ? 'selected' : ''; ?>>English</option>
                                                    <option value="Punjabi" <?php echo (isset($appointment['preferred_language']) && $appointment['preferred_language'] == 'Punjabi') ? 'selected' : ''; ?>>Punjabi</option>
                                                </select>
												@if ($errors->has('preferred_language'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('preferred_language') }}</strong>
													</span>
												@endif
											</div>
										</div>
                                    </div>
                                  
								</div>
							</div>
						</div>
						<div class="form-group float-right">
							<button type="submit" class="btn btn-primary">Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
		</div>
	</section>


</div>
@endsection
@push('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function () {
        $('.date').datepicker({
            //inline: true,
            //startDate: new Date(),
            //datesDisabled: datesForDisable,
            //daysOfWeekDisabled: daysOfWeek,
            format: 'dd/mm/yyyy',
            daysOfWeekDisabled: [0, 6] // 0 = Sunday, 6 = Saturday
        })
    })


    // Time dropdown with 24-hour format and 15-minute intervals - no additional JavaScript needed

</script>

@endpush



