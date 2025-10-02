@extends('layouts.admin')
@section('title', 'Invoice Schedules')

@section('content')
<style>
.dropdown a.dropdown-toggle:after{display:none;}
.dropdown a.dropdown-toggle{color:#000;}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">

				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Invoice Schedule</h4>
							<div class="card-header-action">
								<a href="javascript:;" data-toggle="modal" data-target=".add_payment_schedule" class="btn btn-primary"><i class="fa fa-plus"></i> Add Payment Schedule</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="invoice_tabs" role="tablist">
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="invoiceschedule-tab"  href="{{URL::to('/admin/invoice-schedules')}}" >Invoice Schedule</a>
								</li>
							</ul>
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="invoiceschedule" role="tabpanel" aria-labelledby="invoiceschedule-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>ID</th>
													<th>Client Name</th>
													<th>Application</th>
													<th>Stage (Workflow)</th>
													<th>Installment</th>
													<th>Total Fees</th>
													<th>Invoicing</th>
													<th>Invoice To</th>
													<th>Status</th>
													<th>Action</th>
												</tr>
											</thead>
											@if(count($lists) >0)
											<tbody class="tdata">
											@foreach($lists as $list)
												@php
													$clientdetail = \App\Models\Admin::where('id', $list->client_id)->first();
													$application = \App\Models\Application::where('id', @$list->application_id)->first();
													$productdetail = \App\Models\Product::where('id', @$application->product_id)->first();
													$partnerdetail = \App\Models\Partner::where('id', @$application->partner_id)->first();

													$PartnerBranch = \App\Models\PartnerBranch::where('id', @$application->branch)->first();
													$Workflow = \App\Models\Workflow::where('id', @$application->workflow)->first();

													$ScheduleItems = \App\Models\ScheduleItem::where('schedule_id',$list->id)->get();
													$amt = 0;
													foreach($ScheduleItems as $ScheduleItem){
														$amt += $ScheduleItem->fee_amount;
													}
												@endphp
												<tr id="id_{{@$list->id}}">
													<td style="white-space: initial;"><a href="{{URL::to('/admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}">{{@$clientdetail->first_name}} {{@$clientdetail->last_name}}</a><br>{{@$clientdetail->email}}</td>
													<td style="white-space: initial;"><?php echo @$productdetail->name.'<br>'.@$partnerdetail->partner_name.'<br>'; ?> <?php echo @$PartnerBranch->name; ?></td>
													<td style="white-space: initial;">{{@$application->stage}} <br/>({{@$Workflow->name}})</td>
													<td style="white-space: initial;">{{$list->installment_name}}</td>
													<td style="white-space: initial;">USD	{{number_format($amt,2,'.','')}}</td>
													<td style="white-space: initial;">{{date('d/m/Y', strtotime($list->invoice_sc_date))}}</td>
													<td style="white-space: initial;"></td>
													<td style="white-space: initial;">
													<?php
													if(strtotime(date('Y-m-d')) <  strtotime($list->invoice_sc_date) ){
														echo '<span class="text-success">Scheduled</span>';
													}else{
														echo '<span class="">Expired</span>';
													}
													?>
													</td>
													<td style="text-align:right;">
														<div class="dropdown d-inline">
															<a class="dropdown-toggle" href="javascript:;" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
															<div class="dropdown-menu">
																<a class="dropdown-item openeditform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
																<a onClick="deleteAction({{@$list->id}}, 'invoice_schedules')" class="dropdown-item" href="javascript:;">Delete</a>
																<a data-cid="{{$clientdetail->id ?? 'N/A'}}" data-app-id="{{@$list->application_id}}" data-id="{{@$list->id}}" class="dropdown-item createinvoice" href="javascript:;">Create Invoice</a>
															</div>
														</div>
													</td>
												</tr>
												@endforeach
											</tbody>
											@else
												<tbody>
													<tr>
														<td style="text-align:center;" colspan="12">
															No Record found
														</td>
													</tr>
												</tbody>
											@endif
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade add_payment_schedule custom_modal" id="add_payment_schedule" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Add Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/paymentschedule')}}" name="invpaymentschedule"  autocomplete="off" enctype="multipart/form-data">
				@csrf

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="clientname">Client Name <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control clientname" id="clientname" name="client_id">
									<option value="">Select Client Name</option>
									@foreach(\App\Models\Admin::where('role',7)->get() as $wlist)
										<option value="{{$wlist->id}}">{{$wlist->first_name}} {{$wlist->last_name}}</option>
									@endforeach
								</select>
								<span class="custom-error clientname_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control application select2" id="application" name="application">
									<option value="">Select Application</option>
								</select>
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_name">Installment Name <span class="span_req">*</span></label>
								<input data-valid="required" type="text" class="form-control" name="installment_name" placeholder="Installment Name" />
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
									<input data-valid="required" type="text" class="form-control datepicker" placeholder="Select Date" name="installment_date" />
								</div>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
						</div>
					</div>
					<div class="fee_type_sec">
						<div class="fee_label">
							<div class="label_col wd40">
								<label>Fee Type <span class="span_req">*</span></label>
							</div>
							<div class="label_col wd25">
								<label>Fee Amount <span class="span_req">*</span></label>
							</div>
							<div class="label_col wd25">
								<label>Commission %</label>
							</div>
							<div class="label_col wd10"></div>
						</div>
						<div class="clearfix"></div>
						<div class="fee_fields">
							<div class="fee_fields_row">
								<div class="field_col wd40">
									<select data-valid="required" class="form-control fee_type " id="fee_type" name="fee_type[]">
										<option value="">Select Fee Type</option>
										<option value="Accommodation Fee">Accommodation Fee</option>
										<option value="Administration Fee">Administration Fee</option>
										<option value="Airline Ticket">Airline Ticket</option>
										<option value="Airport Transfer Fee">Airport Transfer Fee</option>
										<option value="Application Fee">Application Fee</option>
									</select>
								</div>
								<div class="field_col wd25">
									<input data-valid="required" type="number" class="form-control fee_amount" name="fee_amount[]" placeholder="0.00" />
								</div>
								<div class="field_col wd25">
									<input type="number" class="form-control" name="comm_amount[]" placeholder="0.00" />
								</div>

								<div class="clearfix"></div>
							</div>
						</div>
						<div class="discount_fields">
							<div class="field_col wd40">
								<input type="text" class="form-control " name="" placeholder="Discount" readonly />
							</div>
							<div class="field_col wd25">
								<input type="number" class="form-control discount" name="discount_amount" placeholder="0.00" />
							</div>
							<div class="field_col wd25">
								<input type="text" class="form-control" name="discount_payable" placeholder="0.00" readonly />
							</div>
							<div class="field_col wd10"></div>
						</div>
						<div class="clearfix"></div>

						<div class="totalfee_addbtn">
							<div class="feetype_btn">
								<a href="javascript:;" class="btn btn-outline-primary addfee"><i class="fa fa-plus"></i> Add Fee</a>
							</div>
							<div class="total_fee">
								<div class="total">
									<h4>Total Fee<span class="totlfee">0.00</span></h4>
								</div>
								<div class="netfee">
									<h6>Net Fee<span class="netfeeamt">0.00</span></h6>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<hr/>
					<h4>Setup Invoice Scheduling</h4>
					<span class="note">Schedule your Invoices by selecting an Invoice date for this installment.</span>
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" class="form-control datepicker" placeholder="Select Date" name="invoice_sc_date" />
								</div>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('invpaymentschedule')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal" id="openeditform" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
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
<div class="addfeetypecopy" style="display:none;">
	<div class="fee_fields_row addfee_fields_row">
		<div class="field_col wd40">
			<select data-valid="required" class="form-control fee_type selectfee_type" id="fee_type" name="fee_type[]">
				<option value="">Select Fee Type</option>
				<option  value="Accommodation Fee">Accommodation Fee</option>
				<option  value="Administration Fee">Administration Fee</option>
				<option  value="Airline Ticket">Airline Ticket</option>
				<option > value="Airport Transfer Fee">Airport Transfer Fee</option>
				<option  value="Application Fee">Application Fee</option>
				<option  value="Tution Fee">Tution Fee</option>
			</select>
		</div>
		<div class="field_col wd25">
			<input data-valid="required" type="number" class="form-control fee_amount" name="fee_amount[]" value="" placeholder="0.00" />
		</div>
		<div class="field_col wd25">
			<input type="number" class="form-control comm_amount" name="comm_amount[]" value="" placeholder="0.00" />
		</div>
		<div class="field_col wd10">
			<a href="javascript:;" class="removeitems"><i class="fa fa-trash"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="feetypecopy" style="display:none;">
	<div class="fee_fields_row editfee_fields_row">
		<div class="field_col wd40">
			<select data-valid="required" class="form-control fee_type selectfee_type" id="fee_type" name="fee_type[]">
				<option value="">Select Fee Type</option>
				<option  value="Accommodation Fee">Accommodation Fee</option>
				<option  value="Administration Fee">Administration Fee</option>
				<option  value="Airline Ticket">Airline Ticket</option>
				<option > value="Airport Transfer Fee">Airport Transfer Fee</option>
				<option  value="Application Fee">Application Fee</option>
				<option  value="Tution Fee">Tution Fee</option>
			</select>
		</div>
		<div class="field_col wd25">
			<input data-valid="required" type="number" class="form-control edt_fee_amount" name="fee_amount[]" value="" placeholder="0.00" />
		</div>
		<div class="field_col wd25">
			<input type="number" class="form-control edit_comm_amount" name="comm_amount[]" value="" placeholder="0.00" />
		</div>
		<div class="field_col wd10">
			<a href="javascript:;" class="editremoveitems"><i class="fa fa-trash"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
@endsection
@section('scripts')

<script>
jQuery(document).ready(function($){
	 $(".clientname").select2({
		dropdownParent: $("#add_payment_schedule .modal-content")
	});
	$('.addfee').on('click', function(){
		var clonedval = $('.addfeetypecopy').html();
		$('.fee_type_sec .fee_fields').append('<div class="fee_fields_row field_clone">'+clonedval+'</div>');

	});
	$(document).delegate('.removeitems', 'click', function(){
		$(this).parent().parent().remove();
		calculatetotal();
	});

	$(document).delegate('.fee_amount', 'keyup', function(){
		calculatetotal();
	});
	$(document).delegate('.discount', 'keyup', function(){
		calculatetotal();
	});
	function calculatetotal(){
		var feeamount = 0;
		$('.fee_amount').each(function(){
			if($(this).val() != ''){
				feeamount += parseFloat($(this).val());
			}
		});
		var discount = 0;
		if($('.discount').val() != ''){
			 discount = $('.discount').val();
		}
		var netfee = feeamount - parseFloat(discount);
		$('.totlfee').html(feeamount.toFixed(2));
		$('.netfeeamt').html(netfee.toFixed(2));

	}

	$(document).delegate('.cloneaddfee', 'click', function(){
		var clonedval = $('.feetypecopy').html();
		$('.fee_type_sec .editfields').append('<div class="fee_fields_row field_clone">'+clonedval+'</div>');

	});
	$(document).delegate('.editremoveitems', 'click', function(){
		$(this).parent().parent().remove();
		editcalculatetotal();
	});

	$(document).delegate('.edt_fee_amount', 'keyup', function(){
		editcalculatetotal();
	});
	$(document).delegate('.edit_discount', 'keyup', function(){
		editcalculatetotal();
	});
	function editcalculatetotal(){
		var feeamount = 0;
		$('.edt_fee_amount').each(function(){
			if($(this).val() != ''){
				feeamount += parseFloat($(this).val());
			}
		});
		var discount = 0;
		if($('.edit_discount').val() != ''){
			 discount = $('.edit_discount').val();
		}
		var netfee = feeamount - parseFloat(discount);
		$('.edittotlfee').html(feeamount.toFixed(2));
		$('.editnetfeeamt').html(netfee.toFixed(2));

	}

	$(document).delegate('.createinvoice', 'click', function(){
		$('#opencreateinvoiceform').modal('show');
		var sid	= $(this).attr('data-id');
		var cid	= $(this).attr('data-cid');
		var aid	= $(this).attr('data-app-id');
		$('#client_id').val(cid);
		$('#app_id').val(aid);
		$('#schedule_id').val(sid);
	});
	$(document).delegate('.openeditform', 'click', function(){
		$('#openeditform').modal('show');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/scheduleinvoicedetail')}}',
			type: 'GET',
			data: {id: $(this).attr('data-id')},
			success: function(res){
				$('.popuploader').hide();
				$('.showeditmodule').html(res);
				$(".editclientname").select2({
					dropdownParent: $("#openeditform .modal-content")
				});
			}
		});
	});
	$(document).delegate('.clientname', 'change', function(){
	$('#application').html('<option value="">Select Client Name</option>');
	$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/application/getapplicationbycid')}}',
			type: 'GET',
			data: {clientid: $('#clientname option:selected').val()},
			success: function(res){
				$('.popuploader').hide();
				$('#application').html(res);
			}
		});
	});

	$(document).delegate('.editclientname', 'change', function(){
	$('#editapplication').html('<option value="">Select Client Name</option>');
	$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/application/getapplicationbycid')}}',
			type: 'GET',
			data: {clientid: $('#editclientname option:selected').val()},
			success: function(res){
				$('.popuploader').hide();
				$('#editapplication').html(res);
			}
		});
	});
});
</script>
@endsection
