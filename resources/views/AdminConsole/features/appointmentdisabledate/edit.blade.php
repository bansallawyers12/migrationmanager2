@extends('layouts.admin_client_detail')
@section('title', 'Edit Block Slot')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="{{ route('adminconsole.features.appointmentdisabledate.update') }}" name="edit-block-slot" autocomplete="off" enctype="multipart/form-data" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{ base64_encode(convert_uuencode($fetchedData->id)) }}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Block Slot</h4>
								<div class="card-header-action">
									<a href="{{route('adminconsole.features.appointmentdisabledate.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					 <div class="col-3 col-md-3 col-lg-3">
			        	@include('../Elements/Admin/setting')
    		        </div>
    				<div class="col-9 col-md-9 col-lg-9">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="card" style="background-color: #6777ef;color: #fff;">
											<div class="card-body">
												<h5 class="card-title">
													<?php
													// Get person name
													$personNames = [
														1 => 'Arun',
														2 => 'Shubam', 
														3 => 'Tourist',
														4 => 'Education',
														5 => 'Adelaide'
													];
													$personName = $personNames[$fetchedData->slotPerPerson->person_id] ?? "User{$fetchedData->slotPerPerson->person_id}";
													?>
													<b>{{ $personName }} - {{ $fetchedData->slotPerPerson->bookService->title }}</b>
													<br/>
													<small>Daily Timings: {{ date('H:i', strtotime($fetchedData->slotPerPerson->start_time)) }} - {{ date('H:i', strtotime($fetchedData->slotPerPerson->end_time)) }}</small>
													<br/>
													<small>Weekend: {{ $fetchedData->slotPerPerson->weekend }}</small>
												</h5>
											</div>
										</div>

										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group">
														<label for="block_date">Block Date <span class="span_req">*</span></label>
														<input type="text" name="block_date" class="form-control datepicker" data-valid="required" autocomplete="off" value="{{ date('d/m/Y', strtotime($fetchedData->disabledates)) }}" placeholder="dd/mm/yyyy">
														@if ($errors->has('block_date'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('block_date') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group">
														<label for="block_type">Block Type <span class="span_req">*</span></label>
														<select class="form-control" data-valid="required" name="block_type" id="block_type">
															<option value="time_slots" {{ $fetchedData->block_all == 0 ? 'selected' : '' }}>Specific Time Slots</option>
															<option value="full_day" {{ $fetchedData->block_all == 1 ? 'selected' : '' }}>Full Day</option>
														</select>
														@if ($errors->has('block_type'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('block_type') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6" id="time_slots_field" style="{{ $fetchedData->block_all == 0 ? 'display: block;' : 'display: none;' }}">
													<div class="form-group">
														<label for="time_slots">Time Slots <span class="span_req">*</span></label>
														<input type="text" name="time_slots" class="form-control" autocomplete="off" value="{{ $fetchedData->slots }}" placeholder="e.g., 11:00 AM - 5:00 PM or 11:00 AM,11:30 AM,12:00 PM">
														<small class="form-text text-muted">
															<strong>Options:</strong><br/>
															• <strong>Time Range:</strong> 11:00 AM - 5:00 PM<br/>
															• <strong>Individual Slots:</strong> 11:00 AM,11:30 AM,12:00 PM<br/>
															• <strong>Mixed:</strong> 11:00 AM - 1:00 PM,2:00 PM,3:30 PM - 5:00 PM
														</small>
														@if ($errors->has('time_slots'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('time_slots') }}</strong>
															</span>
														@endif
													</div>
												</div>
                                            </div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary">Update Block Slot</button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script>
jQuery(document).ready(function($){
    // Initialize datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        startDate: new Date(),
        autoclose: true
    });

    // Show/hide time slots field based on block type
    $('#block_type').change(function() {
        if ($(this).val() === 'time_slots') {
            $('#time_slots_field').show();
        } else {
            $('#time_slots_field').hide();
        }
    });

    // Add helper text for time format
    $('input[name="time_slots"]').on('focus', function() {
        $(this).attr('title', 'Examples:\n• 11:00 AM - 5:00 PM (time range)\n• 11:00 AM,12:00 PM,1:00 PM (individual slots)\n• 11:00 AM - 1:00 PM,2:00 PM,3:00 PM - 5:00 PM (mixed)');
    });
});
</script>
@endsection

