@extends('layouts.admin_client_detail')
@section('title', 'Show Appointment')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
                    <div class="col-12 col-md-12 col-lg-12">
							<div class="card-header">
								<h4>Show Appointment</h4>
								<div class="card-header-action">
									<a href="{{route('appointments.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
					</div>
						<div id="accordion">
							<div class="accordion">

								<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
									<div class="row">
                                        {{-- Appointment data is already processed in controller --}}
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="client" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Client name</label>
												<div class="cus_field_input">
													<input type="text" name="client_id" value="{{ isset($appointment['client']['name']) ? $appointment['client']['name'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
												</div>
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="user" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Added By</label>
												@if(isset($appointment['user']) && $appointment['user'])
                                                <input type="text" name="user_id" value="{{ $appointment['user']['name'] ?? 'N/A' }}" class="form-control" autocomplete="off" placeholder="Enter User Name" readonly>
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
													<input type="text" name="date" value="{{ isset($appointment['date']) ? date('d/m/Y', strtotime($appointment['date'])) : 'N/A' }}" class="form-control" data-valid="" autocomplete="off" readonly>
												</div>
											</div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="time" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Time </label>
												<input type="text" name="time" value="{{ isset($appointment['timeslot_full']) ? $appointment['timeslot_full'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="invites" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Nature of Enquiry</label>
													<div class="cus_field_input">
														<input type="text" name="nature_of_enquiry" value="{{ isset($appointment['nature_of_enquiry']['title']) ? $appointment['nature_of_enquiry']['title'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
													</div>
												</div>
											</div>
											<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="status" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Service </label>
													<input type="text" name="service" value="{{ isset($appointment['service']['title']) ? $appointment['service']['title'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
												</div>
											</div>
										</div>
									</div>

                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="description" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Description </label>
												<textarea name="description" class="form-control" autocomplete="off" readonly>{{ isset($appointment['description']) ? $appointment['description'] : 'N/A' }}</textarea>
											</div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="status" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Status <span class="span_req">*</span></label>
                                                <select class="form-control" name="status" data-valid="required" disabled>
                                                    <option value="0" <?php echo (isset($appointment['status']) && $appointment['status'] == 0) ? 'selected' : ''; ?>>Pending/Not confirmed</option>
                                                    <option value="2" <?php echo (isset($appointment['status']) && $appointment['status'] == 2) ? 'selected' : ''; ?>>Completed</option>

                                                    <option value="4" <?php echo (isset($appointment['status']) && $appointment['status'] == 4) ? 'selected' : ''; ?>>N/P</option>

                                                    <option value="6" <?php echo (isset($appointment['status']) && $appointment['status'] == 6) ? 'selected' : ''; ?>>Did Not Come</option>
                                                    <option value="7" <?php echo (isset($appointment['status']) && $appointment['status'] == 7) ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="8" <?php echo (isset($appointment['status']) && $appointment['status'] == 8) ? 'selected' : ''; ?>>Missed</option>
                                                    <option value="9" <?php echo (isset($appointment['status']) && $appointment['status'] == 9) ? 'selected' : ''; ?>>Payment Pending</option>
                                                    <option value="10" <?php echo (isset($appointment['status']) && $appointment['status'] == 10) ? 'selected' : ''; ?>>Payment Success</option>
                                                    <option value="11" <?php echo (isset($appointment['status']) && $appointment['status'] == 11) ? 'selected' : ''; ?>>Payment Failed</option>
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
                                                    <option value="phone" <?php echo (isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'phone') ? 'selected' : ''; ?>> Phone</option>
                                                    <option value="in_person" <?php echo (isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'in_person') ? 'selected' : ''; ?>>In person</option>
                                                  <option value="zoom_google_meeting" <?php echo (isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'zoom_google_meeting') ? 'selected' : ''; ?>>Zoom / Google Meeting</option>
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
                                                <label for="email" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Email</label>
                                                <input type="text" name="email" value="{{ isset($appointment['email']) ? $appointment['email'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
                                            </div>
                                        </div>
									</div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
                                                <label for="full_name" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Full Name</label>
                                                <input type="text" name="full_name" value="{{ isset($appointment['full_name']) ? $appointment['full_name'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
                                                <label for="preferred_language" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Preferred Language</label>
                                                <input type="text" name="preferred_language" value="{{ isset($appointment['preferred_language']) ? $appointment['preferred_language'] : 'N/A' }}" class="form-control" autocomplete="off" readonly>
                                            </div>
                                        </div>
									</div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
                                                <label for="inperson_address" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">In-Person Address</label>
                                                @php
                                                    $addressValue = isset($appointment['inperson_address']) ? $appointment['inperson_address'] : null;
                                                    $displayAddress = 'N/A';
                                                    
                                                    if ($addressValue == '1') {
                                                        $displayAddress = 'ADELAIDE (Unit 5 5/55 Gawler Pl, Adelaide SA 5000)';
                                                    } elseif ($addressValue == '2') {
                                                        $displayAddress = 'MELBOURNE (Next to Flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)';
                                                    }
                                                @endphp
                                                <input type="text" name="inperson_address" value="{{ $displayAddress }}" class="form-control" autocomplete="off" readonly>
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
