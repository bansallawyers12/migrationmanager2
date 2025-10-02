@extends('layouts.admin_client_detail')
@section('title', 'Edit Appointment')


@section('content')
<style>
/* Force prevent horizontal scroll on this page */
html, body {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
    max-width: 100% !important;
}

/* Fix horizontal scrollbar issue - Force override */
.card-body {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
    max-width: 100% !important;
}

.main-content {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
    max-width: 100% !important;
}

.section-body {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
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

/* Select2 container fixes */
.select2-container {
    max-width: 100% !important;
    width: 100% !important;
}

.select2-container .select2-selection--single {
    max-width: 100% !important;
    width: 100% !important;
}

/* Fix Update button visibility */
.form-group.float-right {
    position: relative !important;
    z-index: 1000 !important;
    margin-bottom: 20px !important;
}

.form-group.float-right .btn {
    position: relative !important;
    z-index: 1001 !important;
}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section" style="margin-top: 56px;">
		<div class="section-body">
            <div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
		<form action="{{ route('appointments.update',$appointment->id) }}" method="POST">
        @csrf
        @method('PUT')
		<input
            type="hidden"
            name="id"
            value="{{ @$appointment->id }}">

				<!-- <div class="row"> -->
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
                    <div class="col-12 col-md-12 col-lg-12">
						<!-- <div class="card"> -->
							<div class="card-header">
								<h4 style="color:#000000;">Edit Appointment</h4>
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
												<label for="client" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Client name</label>
												<div class="cus_field_input">
													<input
                                                        type="text"
                                                        name="client_id"
                                                        value="{{ @$appointment->clients->first_name . ' ' . @$appointment->clients->last_name }}"
                                                        class="form-control"
                                                        data-valid=""
                                                        readonly
                                                        autocomplete="off"
                                                        placeholder="Enter Client Name">

													@if ($errors->has('client_id'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('client_id') }}</strong>
														</span>
													@endif
													<input class="form-control" id="client_id" type="hidden" name="client_id" value="{{$appointment->client_id}}" >
												</div>
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">


                                            <div class="form-group">
                                                <input type="hidden" name="route" value="{{url()->previous()}}">
												<label for="user" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Added By</label>
                                                @if($appointment->user)
                                                <input
                                                type="text"
                                                name="user_id"
                                                value="{{ @$appointment->user->first_name . ' ' . @$appointment->user->last_name }}"
                                                class="form-control"
                                                autocomplete="off"
                                                placeholder="Enter User Name"
                                                readonly>

                                                @else
                                                <input
                                                    type="text"
                                                    name="user_id"
                                                    value="N/A"
                                                    class="form-control"
                                                    autocomplete="off"
                                                    placeholder="Enter User Name"
                                                    readonly>

                                                @endif
											</div>
										</div>
										<input class="form-control" id="user_id" type="hidden" name="user_id" value="{{$appointment->user_id}}" >
                                    </div>

                                    <div class="row">

									    <!-- dd('dfsdfg'); -->
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="date" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Date <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="country_code">
													</div>
                                                    <?php
                                                    if( isset($appointment->date) && $appointment->date != "") {
                                                        $dateArr = explode('-', $appointment->date);
                                                        $datey = $dateArr[2].'/'.$dateArr[1].'/'.$dateArr[0];
                                                    } else {
                                                        $datey = '';
                                                    }?>
													<input
                                                        type="text"
                                                        name="date"
                                                        value="{{ @$datey }}"
                                                        class="form-control date"
                                                        data-valid="required"
                                                        autocomplete="off"
                                                        placeholder="Select date">
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
												<label for="time" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Time <span class="span_req">*</span></label>
												<input
                                                type="time"
                                                name="time"
                                                value="{{ @$appointment->time }}"
                                                class="form-control"
                                                id="followup_time"
                                                data-valid="required"
                                                autocomplete="off"
                                                placeholder="Select time">

                                                @if ($errors->has('time'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('time') }}</strong>
													</span>
												@endif
											</div>
										</div>
									</div>


                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
											    <label for="noe_id" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Nature of Enquiry<span class="span_req">*</span></label>
											    <select class="form-control  select2" name="noe_id" disabled>
												    <option value="" >Select Nature of Enquiry</option>
											        <?php foreach(\App\Models\NatureOfEnquiry::all() as $list) { ?>
                                                        <option <?php if(@$list->id == $appointment->noe_id){ echo 'selected'; } ?> value="{{@$list->id}}" >{{@$list->title}}</option>
                                                    <?php } ?>
											    </select>

                                                @if ($errors->has('noe_id'))
                                                <span class="custom-error" role="alert">
                                                    <strong>{{ @$errors->first('noe_id') }}</strong>
                                                </span>
                                                @endif
										    </div>
                                            <input type="hidden" name="noe_id_hidden" value="{{ $appointment->noe_id }}">
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="status" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Service </label>
                                                <input
                                                type="text"
                                                name="service"
                                                value="{{ @$appointment->service->title }}"
                                                class="form-control"
                                                autocomplete="off"
                                                readonly>

                                            </div>
                                        </div>



									</div>
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="description" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Description <span class="span_req">*</span></label>
												<input
                                                    type="text"
                                                    name="description"
                                                    value="{{ @$appointment->description }}"
                                                    class="form-control"
                                                    data-valid="required"
                                                    autocomplete="off"
                                                    placeholder="Enter description">

                                                @if ($errors->has('description'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('description') }}</strong>
													</span>
												@endif
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="status" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Status <span class="span_req">*</span></label>
												<select class="form-control" name="status" data-valid="required">
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
												<label for="appointment_details" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Appointment details <span class="span_req">*</span></label>

                                                <select data-valid="required" class="form-control" name="appointment_details">
                                                    <option value="">Select</option>
                                                    <option value="phone" <?php echo ($appointment->appointment_details == 'phone') ? 'selected' : ''; ?>> Phone</option>
                                                    <option value="in_person" <?php echo ($appointment->appointment_details == 'in_person') ? 'selected' : ''; ?>>In person</option>
                                                  <?php if( isset($appointment->service_id) && $appointment->service_id == 1) { //Paid?>
                                                        <option value="zoom_google_meeting" <?php echo ($appointment->appointment_details == 'zoom_google_meeting') ? 'selected' : ''; ?>>Zoom / Google Meeting</option>
                                                    <?php } ?>
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
                                                <label for="preferred_language" style="color: #000000; font-weight: bold; display: block; margin-bottom: 5px;">Preferred Language <span class="span_req">*</span></label>
                                                <select data-valid="required" class="form-control" name="preferred_language">
                                                    <option value="">Select</option>
                                                    <option value="Hindi" <?php echo ($appointment->preferred_language == 'Hindi') ? 'selected' : ''; ?>> Hindi</option>
                                                    <option value="English" <?php echo ($appointment->preferred_language == 'English') ? 'selected' : ''; ?>>English</option>
                                                    <option value="Punjabi" <?php echo ($appointment->preferred_language == 'Punjabi') ? 'selected' : ''; ?>>Punjabi</option>
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
							<input
                            type="submit"
                            value="Update"
                            class="btn btn-primary" style="margin-right:25px;">

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
@section('scripts')
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


    document.getElementById('followup_time').addEventListener('input', function () {
        let time = this.value.split(':');
        let hours = parseInt(time[0]);
        let minutes = parseInt(time[1]);

        // Round the minutes to the nearest 15-minute increment
        let roundedMinutes = Math.round(minutes / 15) * 15;

        // Handle the edge case where rounding exceeds 60 minutes
        if (roundedMinutes === 60) {
            roundedMinutes = 0;
            hours = (hours + 1) % 24;  // Wrap around the hours if necessary
        }

        // Format the hours and minutes with leading zeros if needed
        this.value = String(hours).padStart(2, '0') + ':' + String(roundedMinutes).padStart(2, '0');
    });

</script>

@endsection



