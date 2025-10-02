@extends('layouts.admin')
@section('title', 'Income Sharing')

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
							<h4>Income Sharing</h4>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="payable_tabs" role="tablist">
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="payables-tab"  href="{{URL::to('/admin/income-sharing/payables/unpaid')}}" >Payables</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="receivables-tab"  href="{{URL::to('/admin/income-sharing/receivables/unpaid')}}" >Receivables</a>
								</li>
							</ul>
							<div class="tab-content" id="payableContent">
								<div class="tab-pane fade show active" id="payables" role="tabpanel" aria-labelledby="payables-tab">
									<ul class="nav nav-pills" id="paypaid_tabs" role="tablist">
										<li class="nav-item is_checked_clientn">
											<a class="nav-link" id="unpaid-tab"  href="{{URL::to('/admin/income-sharing/payables/unpaid')}}" >Unpaid</a>
										</li>
										<li class="nav-item is_checked_clientn">
											<a class="nav-link active" id="paid-tab"  href="{{URL::to('/admin/income-sharing/payables/paid')}}" >Paid</a>
										</li>
									</ul>
									<div class="tab-content" id="paypaidContent">
										<div class="tab-pane fade show active" id="paid" role="tabpanel" aria-labelledby="paid-tab">
											<div class="table-responsive common_table">
												<table class="table text_wrap">
													<thead>
														<tr>
															<th>Invoice No</th>
															<th>Office Name</th>
															<th>Sub Agent</th>
															<th>Client Name</th>
															<th>DOB</th>
															<th>Partner Name</th>
															<th>Product Name</th>
															<th>Amount</th>
															<th>Tax Amount</th>
															<th>Paid Date</th>
															<th>Paid By</th>
															<th>Action</th>
														</tr>
													</thead>
													@if(count($lists) >0)
													<tbody class="tdata">
													@foreach($lists as $list)
													<?php
																$applicationdata = \App\Models\Application::where('id', $list->invoice->application_id)->first();
																$partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
																$productdata = \App\Models\Product::where('id', @$applicationdata->product_id)->first();
																$admindata = \App\Models\Admin::where('id', @$list->user_id)->first();

																										?>
																										<tr id="id_{{$list->id}}">
																<td><a href="{{URL::to('/admin/invoice/view/')}}/{{$list->invoice_id}}">{{@$list->invoice_id}}</a></td>
																<td><a href="#">{{@$list->branch->office_name}}</a></td>
																<td>-</td>
																<td><a href="{{URL::to('/admin/clients/detail/')}}/{{base64_encode(convert_uuencode($list->invoice->customer->id))}}">{{@$list->invoice->customer->first_name}} {{@$list->invoice->customer->last_name}}</a></td>
																<td>{{date('d/m/Y', strtotime(@$list->invoice->customer->dob))}}</td>
																<td>{{@$partnerdata->partner_name}}</td>
																<td>{{@$productdata->name}}</td>
																<td>$ {{$list->amount}}</td>
																<td>$ 0.00</td>

																<td>{{date('d/m/Y', strtotime(@$list->payment_date))}}</td>
																<td>{{@$admindata->first_name}} {{@$admindata->last_name}}</td>
																<td style="text-align:right;">
																	<div class="dropdown d-inline">
																		<a class="dropdown-toggle" href="javascript:;" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
																		<div class="dropdown-menu">
																			<a  data-id="{{$list->id}}" class="dropdown-item revertinvoice" href="javascript:;">Revert Payment</a>

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
<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Are you sure you want revert payment?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Accept</button>
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
	$(document).delegate('.revertinvoice', 'click', function(){
		$('#confirmModal').modal('show');
		notid = $(this).attr('data-id');
	});

	$(document).delegate('#confirmModal .accept', 'click', function(){

		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/revert-payment',
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
});
</script>
@endsection
