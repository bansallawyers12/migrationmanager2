{{-- ========================================
    ALL PAYMENT SCHEDULE & INVOICE MODALS
    This file contains all payment schedule and invoice creation modals
    Total: 4 modals for payment management
    ======================================== --}}

{{-- 1. Payment Schedule Setup Modal --}}
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
				<form method="post" action="{{URL::to('/setup-paymentschedule')}}" name="setuppaymentschedule" id="setuppaymentschedule" autocomplete="off" enctype="multipart/form-data">
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

{{-- 2. Edit Payment Schedule Modal --}}
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

{{-- 3. Add Payment Schedule Modal (Dynamic) --}}
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

{{-- 4. Create Invoice Form Modal --}}
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
			<form method="post" action="{{URL::to('/create-invoice')}}" name="createinvoive"  autocomplete="off" enctype="multipart/form-data">
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

