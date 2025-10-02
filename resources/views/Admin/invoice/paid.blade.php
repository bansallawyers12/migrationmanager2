@extends('layouts.admin')
@section('title', 'Paid Invoices')

@section('content')
<style>
.ag-space-between {
    justify-content: space-between;
}
.ag-align-center {
    align-items: center;
}
.ag-flex {
    display: flex;
}
.ag-align-start {
    align-items: flex-start;
}
.ag-flex-column {
    flex-direction: column;
}
.col-hr-1 {
    margin-right: 5px!important;
}
.text-semi-bold {
    font-weight: 600!important;
}
.small, small {
    font-size: 85%;
}
.ag-align-end {
    align-items: flex-end;
}

.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.label:last-child {
    margin-right: 0;
}
.ui.label:first-child {
    margin-left: 0;
}
.field .ui.label {
    padding-left: 0.78571429em;
    padding-right: 0.78571429em;
}

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
							<h4>Paid Invoices</h4>
							<div class="card-header-action">


							</div>
						</div>
						<div class="card-body">

							<ul class="nav nav-pills" id="client_tabs" role="tablist">

								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="prospects-tab"  href="{{URL::to('/admin/invoice/unpaid')}}" >Unpaid Invoices</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="clients-tab"  href="{{URL::to('/admin/invoice/paid')}}" >Paid Invoices</a>
								</li>

							</ul>
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="clients" role="tabpanel" aria-labelledby="clients-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>

													<th>No</th>
													<th>Issue Date</th>
													<th>Client Name</th>
													<th>Created By</th>
													<th>Partner Name</th>
													<th>Workflow</th>
													<th>Product</th>
													<th>Amount</th>

													<th>Action</th>

												</tr>
											</thead>
											@if(count($lists) >0)
											<tbody class="tdata">
												<?php
												foreach($lists as $invoicelist){
																				$clientdata = \App\Models\Admin::where('id', $invoicelist->client_id)->first();
																				$admindata = \App\Models\Admin::where('id', $invoicelist->user_id)->first();
																											if($invoicelist->type == 3){
																		$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
																	}else{
																		$applicationdata = \App\Models\Application::where('id', @$invoicelist->application_id)->first();
																		$workflowdaa = \App\Models\Workflow::where('id', @$invoicelist->application_id)->first();
																		$partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
																	}
																	$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicelist->id)->orderby('id','ASC')->get();
																	$netamount = 0;
																	$coom_amt = 0;
																	$total_fee = 0;
																	foreach($invoiceitemdetails as $invoiceitemdetail){
																		$netamount += $invoiceitemdetail->netamount;
																		$coom_amt += $invoiceitemdetail->comm_amt;
																		$total_fee += $invoiceitemdetail->total_fee;
																	}

																	$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicelist->id)->orderby('created_at', 'DESC')->get();
																	$amount_rec = 0;
																	foreach($paymentdetails as $paymentdetail){
																		$amount_rec += $paymentdetail->amount_rec;
																	}
																	if($invoicelist->type == 1){
																		$totaldue = $total_fee - $coom_amt;
																	} if($invoicelist->type == 2){
																		$totaldue = $netamount - $amount_rec;
																	}else{
																		$totaldue = $netamount - $amount_rec;
																	}
																	?>
												<tr id="id_<?php echo $invoicelist->id; ?>">
													<td><?php echo $invoicelist->id; ?></td>
													<td style="white-space: initial;"><a href="{{URL::to('admin/invoice/view/')}}/<?php echo $invoicelist->id; ?>">{{date('d/m/Y', strtotime($invoicelist->invoice_date))}}  <?php //echo $invoicelist->invoice_date; ?></a></td>
													<td style="white-space: initial;"><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdata->id))}}">{{$clientdata->first_name}} {{$clientdata->last_name}}</a></td>
													<td style="white-space: initial;"><a href="">{{$admindata->first_name}}</a></td>
													<td style="white-space: initial;">{{@$partnerdata->partner_name}}</td>
													<td style="white-space: initial;">{{@$workflowdaa->name}}</td>
													<td></td>
													<td>AUD <?php echo $invoicelist->net_fee_rec; ?></td>

													<td>
													<a href="{{URL::to('admin/invoice/view/')}}/<?php echo $invoicelist->id; ?>"><i class="fa fa-eye"></i></a>
												<!--	<a href=""><i class="fa fa-envelope"></i></a>-->

													<!--<a href=""><i class="fa fa-dollor"></i></a>-->
													<!--<a href=""><i class="fa fa-trash"></i></a>-->
													</td>
												</tr>
												<?php
												}
												?>
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
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/admin/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								{{ Form::text('email_from', 'support@digitrex.live', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter From' )) }}
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
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>

								@if ($errors->has('email_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_to') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxcc" name="email_cc[]"></select>

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
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
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
								{{ Form::text('subject', '', array('class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )) }}
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
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')

@endsection
