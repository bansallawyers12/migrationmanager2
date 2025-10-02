@extends('layouts.admin')
@section('title', 'Unpaid Invoices')

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
							<h4>Unpaid Invoices</h4>
							<div class="card-header-action">


							</div>
						</div>
						<div class="card-body">

							<ul class="nav nav-pills" id="client_tabs" role="tablist">

								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="prospects-tab"  href="{{URL::to('/admin/invoice/unpaid')}}" >Unpaid Invoices</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link " id="clients-tab"  href="{{URL::to('/admin/invoice/paid')}}" >Paid Invoices</a>
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
													<th>Amount Due</th>
													<th>Due Date</th>
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
													$applicationdata = \App\Models\Application::where('id', $invoicelist->application_id)->first();
													$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
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
													<td><?php echo $invoicelist->invoice_no; ?></td>
													<td style="white-space: initial;"><a href="{{URL::to('admin/invoice/view/')}}/<?php echo $invoicelist->id; ?>">{{date('d/m/Y', strtotime($invoicelist->invoice_date))}} <?php //echo $invoicelist->invoice_date; ?></a></td>
													<td style="white-space: initial;"><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdata->id))}}">{{@$clientdata->first_name}} {{@$clientdata->last_name}}</a></td>
													<td style="white-space: initial;"><a href="">{{@$admindata->first_name}}</a></td>
													<td style="white-space: initial;">{{@$partnerdata->partner_name}}</td>
													<td style="white-space: initial;">{{@$workflowdaa->name}}</td>
													<td style="white-space: initial;"></td>
													<td>AUD <?php echo $invoicelist->net_fee_rec; ?></td>
													<td><?php echo $totaldue; ?></td>
													<td>{{date('d/m/Y', strtotime($invoicelist->due_date))}}  <?php //echo $invoicelist->due_date; ?></td>
													<td>
													<a href="{{URL::to('admin/invoice/view/')}}/<?php echo $invoicelist->id; ?>"><i class="fa fa-eye"></i></a>
													<a class="clientemail" data-id="{{$invoicelist->id}}" data-rec-name="invoice_{{$invoicelist->id}}.pdf" data-href="{{URL::to('admin/invoice/preview/')}}/{{@$invoicelist->id}}" data-cus-id="{{@$clientdata->id}}" data-email="{{@$clientdata->email}}" data-name="{{@$clientdata->first_name}} {{@$clientdata->last_name}}" href="javascript:;"><i class="fa fa-envelope"></i></a>
													<a href="{{URL::to('admin/invoice/edit/')}}/<?php echo $invoicelist->id; ?>"><i class="fa fa-edit"></i></a>
													<a href="javascript:;" class="openpaymentform" data-netamount="{{$netamount}}" data-dueamount="{{$totaldue}}" data-invoiceid="{{$invoicelist->id}}">$</a>
													<a data-id="{{$invoicelist->id}}" href="javascript:;" class="deleteinvoice"><i class="fa fa-trash"></i></a>
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
<div id="addpaymentmodal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
	{{ Form::open(array('url' => 'admin/invoice/payment-store', 'name'=>"invoicepaymentform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", "id"=>"invoicepaymentform")) }}
	<input type="hidden" value="" name="invoice_id" id="invoice_id">
	<input type="hidden" value="false" name="is_ajax" id="">

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
									<input type="date" name="payment_date[]" placeholder="" class=" form-control" value="{{date('Y-m-d')}}"/>
								</div>
								<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
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
				<button type="button" onclick="customValidate('invoicepaymentform')" class="btn btn-primary" >Save & Close</button>

			  </div>
		</div>
		</form>
	</div>
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
				<input type="hidden" name="type" value="client">
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
							<div class="form-group">
								<label for="invreceipt"><input type="checkbox" checked name="invreceipt" value=""> <a target="_blank" href="#" id="invreceipt"></a></label>
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
<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Are you sure you want to delete ?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	var notid = '';
	$(document).delegate('.deleteinvoice', 'click', function(){
		$('#confirmModal').modal('show');
		notid = $(this).attr('data-id');
	});

	$(document).delegate('#confirmModal .accept', 'click', function(){

		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/delete-invoice',
			type:'GET',
			datatype:'json',
			data:{id:notid},
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirmModal').modal('hide');
				if(res.status){
					$('#id_'+notid).remove();
					$('.custom-error-msg')('<span class="alert alert-success">'+res.message+'</span>');
				}else{
					$('.custom-error-msg')('<span class="alert alert-danger">'+res.message+'</span>');
				}
			}
		});
	});
	$(document).delegate('.openpaymentform','click', function(){
		var v = $(this).attr('data-invoiceid');
		var netamount = $(this).attr('data-netamount');
		var dueamount = $(this).attr('data-dueamount');
		$('#invoice_id').val(v);
		$('.invoicenetamount').html(netamount+' AUD');
		$('.totldueamount').html(dueamount);
		$('.totldueamount').attr('data-totaldue', dueamount);
		$('#addpaymentmodal').modal('show');
		$('.payment_field_clone').remove();
		$('.paymentAmount').val('');
	});
	$(document).delegate('.clientemail', 'click', function(){
		$('#emailmodal').modal('show');
		$('#invreceipt').html('');
		$('#invreceipt').attr('href', '#');
		$('input[name="invreceipt"]').val('');
		var recname = $(this).attr('data-rec-name');
		var recid = $(this).attr('data-id');
		var href = $(this).attr('data-href');
		$('#invreceipt').html(recname);
		$('input[name="invreceipt"]').val(recid);
		$('#invreceipt').attr('href', href);
		var array = [];
		var data = [];


				var id = $(this).attr('data-cus-id');
				 array.push(id);
				var email = $(this).attr('data-email');
				var name = $(this).attr('data-name');
				var status = 'Client';

				data.push({
					id: id,
	  text: name,
	  html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

		  "<div  class='ag-flex ag-align-start'>" +
			"<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
			"<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +

		  "</div>" +
		  "</div>" +
		   "<div class='ag-flex ag-flex-column ag-align-end'>" +

			"<span class='ui label yellow select2-result-repository__statistics'>"+ status +

			"</span>" +
		  "</div>" +
		"</div>",
	  title: name
					});

		$(".js-data-example-ajax").select2({
	  data: data,
	  escapeMarkup: function(markup) {
		return markup;
	  },
	  templateResult: function(data) {
		return data.html;
	  },
	  templateSelection: function(data) {
		return data.text;
	  }
	})
		$('.js-data-example-ajax').val(array);
			$('.js-data-example-ajax').trigger('change');

	});
	$(document).delegate('.selecttemplate', 'change', function(){
		var v = $(this).val();
		$.ajax({
			url: '{{URL::to('/admin/get-templates')}}',
			type:'GET',
			datatype:'json',
			data:{id:v},
			success: function(response){
				var res = JSON.parse(response);
				$('.selectedsubject').val(res.subject);
				 $(".summernote-simple").summernote('reset');
						$(".summernote-simple").summernote('code', res.description);
						$(".summernote-simple").val(res.description);

			}
		});
	});
	$('.js-data-example-ajax').select2({
		multiple: true,
		closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		ajax: {
		url: '{{URL::to('/admin/clients/get-recipients')}}',
		dataType: 'json',
		processResults: function (data) {
		  // Transforms the top-level key of the response object from 'items' to 'results'
		  return {
			results: data.items
		  };

		},
		 cache: true

		},
		templateResult: formatRepo,
		templateSelection: formatRepoSelection
	});
	$('.js-data-example-ajaxcc').select2({
		multiple: true,
		closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		ajax: {
		url: '{{URL::to('/admin/clients/get-recipients')}}',
		dataType: 'json',
		processResults: function (data) {
		  // Transforms the top-level key of the response object from 'items' to 'results'
		  return {
			results: data.items
		  };

		},
		 cache: true

		},
		templateResult: formatRepo,
		templateSelection: formatRepoSelection
	});
	function formatRepo (repo) {
	  if (repo.loading) {
		return repo.text;
	  }

	  var $container = $(
		"<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

		  "<div  class='ag-flex ag-align-start'>" +
			"<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
			"<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

		  "</div>" +
		  "</div>" +
		   "<div class='ag-flex ag-flex-column ag-align-end'>" +

			"<span class='ui label yellow select2-result-repository__statistics'>" +

			"</span>" +
		  "</div>" +
		"</div>"
	  );

	  $container.find(".select2-result-repository__title").text(repo.name);
	  $container.find(".select2-result-repository__description").text(repo.email);
	  $container.find(".select2-result-repository__statistics").append(repo.status);

	  return $container;
	}

	function formatRepoSelection (repo) {
	  return repo.name || repo.text;
	}
});
</script>
@endsection
