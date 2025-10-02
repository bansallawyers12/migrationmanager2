@extends('layouts.admin')
@section('title', 'Edit Invoice')

@section('content')
<style>
.addbranch .error label{
	color: #9f3a38;
}
.addbranch .error input{
	background: #fff6f6;
    border-color: #e0b4b4;
    color: #9f3a38;
    border-radius: "";
    box-shadow: none;
}
.thumb-image {
    float:left;
    width:100px;
    position:relative;
    padding:5px;
}

</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/invoice/com-store', 'name'=>"invoiceform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			<input type="hidden" name="id" value="{{$invoicedetail->id}}">
			<input type="hidden" name="client_id" value="{{$invoicedetail->client_id}}">
			<input type="hidden" name="applicationid" value="{{$invoicedetail->application_id}}">
			<input type="hidden" name="type" value="{{$invoicedetail->type}}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
							<?php
							if($invoicedetail->type == 1){
								?>
								<h4>Edit Net Claim Invoice</h4>
								<?php
							}else{
								?>
								<h4>Edit Gross Claim Invoice</h4>
								<?php
							}
							?>
								
								<div class="card-header-action">
									<a href="{{URL::to('admin/invoice/unpaid')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4 col-lg-4">
						<div class="card">
							<div class="card-header">
								<h4>Partner Details</h4>
							</div>
							<div class="card-body">
								<div class="invoice_info">
								<?php
								
								?>
									<ul>
										<li><span>Name:</span> {{@$partnerdata->partner_name}}</li>
										<li><span>Address:</span> {{@$partnerdata->address}}</li>
										<li><span>Contact:</span> {{@$partnerdata->phone}}</li>
										<li><span>Service:</span> {{@$workflowdaa->name}}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4 col-lg-4">
						<div class="card">
							<div class="card-header">
								<h4>Client Details</h4>
							</div>
							<div class="card-body">
								<div class="invoice_info">
									<ul>
										<li><span>Name:</span> {{$clientdata->first_name}} {{$clientdata->last_name}}</li>
										<li><span>DOB:</span> {{date('d/m/Y', strtotime($clientdata->dob))}}</li>
										<li><span>Client ID:</span> {{$clientdata->client_id}}</li>
										<li><span>Partner:</span> {{@$partnerdata->partner_name}}</li>
										<li><span>Product:</span> {{@$productdata->name}}</li>
										<li><span>Branch:</span> {{@$branchdata->name}}</li>
										<li><span>Workflow:</span> {{@$workflowdaa->name}}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4 col-lg-4">
						<div class="card">							
							<div class="card-body">
								<div class="form-group"> 
									<label for="invoice_date">Invoice Date:</label>
									{{ Form::date('invoice_date', $invoicedetail->invoice_date, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
									<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
									@if ($errors->has('invoice_date'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('invoice_date') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group"> 
									<label for="invoice_due_date">Invoice Due Date:</label>
									{{ Form::date('invoice_due_date', $invoicedetail->due_date, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
									<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
									@if ($errors->has('invoice_due_date'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('invoice_due_date') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group"> 
								<?php
								$profile = json_decode($invoicedetail->profile);
								?>
									<label for="invoice_due_date">Select Profile:</label>
									<select class="form-control" name="profile">
										@foreach(\App\Models\Profile::all() as $profiles)
											<option @if(@$profile->id == $profiles->id)  selected @endif value="{{$profiles->id}}">{{$profiles->company_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table border="1" class="table text_wrap table-striped table-hover table-md vertical_align" id="productitemview">
								
									<?php
										$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicedetail->id)->orderby('id','ASC')->get();
										$coom_amt = 0;
										$total_fee = 0;
										$netamount = 0;
										$tax_amt = 0;
									?>
										<thead> 
											<tr>
										
												<th>Description</th>
												<th>Total Fee</th>
												<th>Commission Percent</th>
												<th>Commission Amount</th>
												<th>Bonus Amount</th>
												<th>Tax</th>
												<th>Tax Amount</th>
												<th>Net Amount</th>
												<th></th>
											</tr>
										</thead>
										<tbody class="productitem">
										<?php
										foreach($invoiceitemdetails as $invoiceitemdetail){
											$coom_amt += $invoiceitemdetail->comm_amt;
											$total_fee += $invoiceitemdetail->total_fee;
											$netamount += $invoiceitemdetail->netamount;
											$tax_amt += $invoiceitemdetail->tax_amount;
										?>
											<tr class="clonedrow">
												
												<td><input class="form-control" name="description[]" value="{{$invoiceitemdetail->description}}" type="text"  /></td>
												<td><span class="percentageinput">$
													<input class="form-control total_fee" value="{{$invoiceitemdetail->total_fee}}"  name="total_fee[]" type="number"  />
										</span>
												</td>
												<td>
												<span class="percentageinput">
													<input class="form-control comm_per" value="{{$invoiceitemdetail->comm_per}}"  type="number" name="comm_per[]"  />%</span>
												</td>
												<td>
												<span class="percentageinput">$
													<input class="form-control comm_amt" value="{{$invoiceitemdetail->comm_amt}}"  type="number" name="comm_amt[]" />
										</span>
												</td>
												<td>
												<span class="currencyinput">$
													<input class="form-control" type="number" name="bonus_amount[]" value="{{$invoiceitemdetail->bonus_amount}}"/>
												</span>
												</td>
												<td>
													<select name="tax[]" class="form-control tax_amt">
														<option value="0">Select A Tax Code</option>
														@foreach(\App\Models\TaxRate::all() as $taxlist)
															<option <?php if($invoiceitemdetail->tax == $taxlist->amount){ echo 'selected'; } ?> value="{{$taxlist->amount}}">{{$taxlist->name}}</option>
														@endforeach
													</select>
												</td>
												<td>
												<span class="percentageinput">$
													<input class="form-control taxamount" name="tax_amount[]" value="{{$invoiceitemdetail->tax_amount}}"  type="text" value="0.00" readonly />
										</span>
												</td>
												<td>
												<span class="percentageinput">$
													<input class="form-control netamount" value="{{$invoiceitemdetail->netamount}}"  name="netamount[]" type="text" value="0.00" readonly />
										</span>
												</td>
												<td>
												<a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
												</td>
											</tr>
										<?php } ?>
										</tbody>										
									</table>
								</div>
								<div class="add_new">
									<a href="javascript:;" class="openproductrinfo"><i class="fa fa-plus"></i> Add New Line</a>	
								</div>
								<div class="row bottom_aligned">
									<div class="col-md-7 cus_col_7">
										<div class="col_full">
											<label>Discount Given to Client:</label>
										</div>
										<div class="col_half col_discount_field">
											<div class="form-group inline_field">
												<div class="label_input">
												<span class="percentageinput">$
													<input class="form-control" name="discount" value="{{$invoicedetail->discount}}" type="text" id="discount" ></span>
													<div class="basic_label">AUD</div>
												</div> 
											</div>
										</div>
										<div class="col_half">
											<div class="form-group">
												<div class="field_col">
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-clock"></i>
															</div>
														</div>	
														<input value="{{$invoicedetail->discount_date}}" type="date" name="discount_date" placeholder="" class=" form-control" />
													</div>
													<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
												</div> 
											</div>
										</div>
									</div>
									<?php
									if($invoicedetail->type == 2){
										?>
										<div class="col-md-7 cus_col_7">
											<div class="invoiceNetResult"> 
												<span>Net Revenue</span>
												<p id="totalFee" class="invoiceNetRevenue"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_fee_rec}}</p>
												<input type="hidden" id="invoice_net_revenue" name="invoice_net_revenue" value="{{$invoicedetail->net_fee_rec}}" placeholder="" class="" />
											</div>
										</div>
										<?php
									}else{
									?>
									<div class="col-md-7 cus_col_7">
										<div class="invoiceNetResult"> 
											<span>Net Fee Received</span>
											<p id="totalFee" class="invoiceNetAmount"><span class="feecurrencyinput">${{$invoicedetail->net_fee_rec}}</span></p>
											<input type="hidden" value="{{$invoicedetail->net_fee_rec}}" id="invoice_net_amount" name="invoice_net_amount" placeholder="" class="" />
										</div>
										<div class="invoiceNetResult">
											<span>Net Income</span>
											<p class="invoiceNetAmount_2"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_incone}}</p>
											<input type="hidden" id="invoice_net_income" name="invoice_net_income" value="{{$invoicedetail->net_incone}}" placeholder="" class="" />
										</div>
									</div>
									<?php
									}
									?>
									<div class="col-md-5 cus_col_5">
									<?php
									$totaldue =0;
									$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->get();
											$amount_rec = 0;
											foreach($paymentdetails as $paymentdetail){
												$amount_rec += $paymentdetail->amount_rec;
											}
									if($invoicedetail->type == 2){
										?>
										<div class="invoiceInformationDiv">
											
											<div class="inline_field">
												<label>Commission Claimed:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total" value="{{$coom_amt}}">
													<input type="text" id="commissionClaimed" value="{{$coom_amt}}" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												<label>Tax:</label>
												<span class="percentageinput">%</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="{{$tax_amt}}">
													<input type="text" id="gst" readonly="readonly" value="{{$tax_amt}}"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Amount (incl Tax)</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_amt" value="{{$coom_amt}}">
													<input value="{{$coom_amt}}" type="text" id="total_amt" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Paid</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_paid" value="{{$amount_rec}}">
													<input value="{{$amount_rec}}" type="text" id="total_paid" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<?php
											$totaldue = $coom_amt - $amount_rec;
											?>
											<div class="inline_field">
												<label>Total Due</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_due" value="{{$totaldue}}">
													<input value="{{$totaldue}}" type="text" id="total_due" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
										</div>
										<?php
									}else{
										?>
										<div class="invoiceInformationDiv">
											<div class="inline_field">
												<label>Total Fee:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total" value="{{$total_fee}}">
													<input value="{{$total_fee}}" type="text" id="totalfee" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												<label>Commission Claimed:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input value="{{$coom_amt}}" type="hidden" name="total" value="0">
													<input value="{{$coom_amt}}" type="text" id="commissionClaimed" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												<label>Tax:</label>
												<span class="percentageinput">%</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="{{$tax_amt}}">
													<input type="text" id="gst" readonly="readonly" value="{{$tax_amt}}"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<?php
											$feepaid = $total_fee - ($coom_amt + $tax_amt) ;
											$totaldue = $feepaid;
											?>
											<div class="inline_field">
												<label>Net Fee Paid to Partner:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="invoice_amount" value="{{$feepaid}}">
													<input value="{{$feepaid}}" type="text" id="netFeePaid" readonly="readonly">
													<input type="hidden" name="total" value="{{$feepaid}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											
										</div>
									<?php } ?>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-7"></div>
									<div class="col-lg-5 cus_col_5"> 
										<div class="payment_detail">
											<label>Select Payment Option</label>
											<select class="form-control" name="paymentoption">
												<option value="">Select Income Type</option>
												<option <?php if($invoicedetail->payment_option == 'Income'){ echo 'selected'; } ?> value="Income">Income</option>
												<option <?php if($invoicedetail->payment_option == 'Payables'){ echo 'selected'; } ?> value="Payables">Payables</option>
											</select>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-lg-6">
										<div class="payment_check">
											<h4>Payments Received 
											<span class="float-right">
												<span class="pay_checkbox">
												<?php
									if($invoicedetail->type == 2){
										?>
										<input  id="invoicepaid" type="checkbox" name="payment_done" tabindex="0" class="hidden">
										<?php
									}else{
										?>
										<input checked id="invoicepaid" type="checkbox" name="payment_done" tabindex="0" class="hidden">
										<?php
									}
										?>
													
													<label>Mark this invoice as paid</label>
												</span>
											</span></h4>
											<div class="payment_field">
												<div class="payment_field_row">
												<?php
												$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->get();
												$totlacount = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->count();
												$ir = 0;
												if($totlacount !== 0){
													foreach($paymentdetails as $paymentdetail){
												?>
													<div class="payment_field_col <?php if($ir == 0){ ?>payment_first_step<?php }else{?>payment_field_clone<?php } ?>">
														<div class="field_col">
															<div class="label_input"><span class="currencyinput">$
																<input type="number" name="payment_amount[]" value="{{$paymentdetail->amount_rec}}" placeholder="" class="paymentAmount" /></span>
																<div class="basic_label">AUD</div>
															</div>
														</div>
														<div class="field_col">
															<div class="input-group">
																<div class="input-group-prepend">
																	<div class="input-group-text">
																		<i class="fas fa-clock"></i>
																	</div>
																</div>	
																<input type="date" name="payment_date[]" value="{{$paymentdetail->payment_date}}" placeholder="Received Amount" class=" form-control" />
															</div>
															<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
														</div>
														<div class="field_col">
															<select name="payment_mode[]" class="form-control">
																<option <?php if($paymentdetail->payment_mode == 'Cheque'){ echo 'selected'; } ?> value="Cheque">Cheque</option>
																<option <?php if($paymentdetail->payment_mode == 'Cash'){ echo 'selected'; } ?> value="Cash">Cash</option>
																<option <?php if($paymentdetail->payment_mode == 'Credit Card'){ echo 'selected'; } ?> value="Credit Card">Credit Card</option>
																<option <?php if($paymentdetail->payment_mode == 'Bank Transfers'){ echo 'selected'; } ?> value="Bank Transfers">Bank Transfers</option>
															</select>
														</div>
														<div class="field_remove_col">
															<a href="javascript:;" class="remove_col"><i class="fa fa-times"></i></a>
														</div>
													</div>
													<?php $ir++; } ?>
													<?php }else{
														?>
														<div class="payment_field_col payment_first_step">
														<div class="field_col">
															<div class="label_input">
																<input type="number" name="payment_amount[]" placeholder="" class="paymentAmount" />
																<div class="basic_label">AUD</div>
															</div>
														</div>
														<div class="field_col">
															<div class="input-group">
																<div class="input-group-prepend">
																	<div class="input-group-text">
																		<i class="fas fa-clock"></i>
																	</div>
																</div>	
																<input type="date" name="payment_date[]" placeholder="Received Amount" class=" form-control" />
															</div>
															<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
														</div>
														<div class="field_col">
															<select name="payment_mode[]" class="form-control">
																<option value="Cheque">Cheque</option>
																<option value="Cash">Cash</option>
																<option value="Credit Card">Credit Card</option>
																<option value="Bank Transfers">Bank Transfers</option>
															</select>
														</div>
														<div class="field_remove_col">
															<a href="javascript:;" class="remove_col"><i class="fa fa-times"></i></a>
														</div>
													</div>
														<?php
													} ?>
												</div>
												<div class="add_payment_field">
													<a href="javascript:;"><i class="fa fa-plus"></i> Add New Line</a>
												</div>
												<div class="clearfix"></div>
											</div>
										</div> 
									</div>
									<div class="col-lg-3">
										<div class="add_notes">
											<h4>Add Notes</h4>
											<textarea class="form-control" name="notes">{{$invoicedetail->notes}}</textarea>
										</div>
									</div>
									{{-- {{dd($invoicedetail->attachments)}} --}}
									<div class="col-lg-3">
										<div class="attachment">
											<h4>Attachments</h4>
											@php $images=explode(",",$invoicedetail->attachments);
												// dd(asset('img/invoice/'.$images));
											@endphp
											
											<input type="hidden" id="old_attachments" name="old_attachments" value="{{@$invoicedetail->attachments}}" />
											<div class="invoice_attach_file">
												<span>Click to upload</span>
												<input name="attachfile[]" class="attachfile" type="file" multiple><br/>
												{{-- <p class="showfilename">{{@$invoicedetail->attachments}}</p> --}}
												
												@foreach($images as $image)
											
													@if(substr($image, -4) == '.pdf' || substr($image, -4) == 'docx')
													<iframe src="https://docs.google.com/viewer?url={{asset('img/invoice')}}/{{@$image}}&embedded=true" style="width:50px;height:50px;" frameborder='0' ></iframe>
													@else
														<img src="{{asset('img/invoice')}}/{{@$image}}" style="width:50px;height:50px;"/>
													@endif
													@endforeach
												<div id="image-holder"></div> 
											</div>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="income_sharing">
									<div class="row">
										<div class="col-lg-12">
											<h4>Income Sharing</h4>
										</div>
										<?php
										$IncomeSharing = \App\Models\IncomeSharing::where('invoice_id',$invoicedetail->id)->first();
										?>
										<div class="col-lg-8">
											<div class="income_field">
												<div class="income_col">
													<select class="form-control" id="share_user" name="share_user">
													<option value="no">Select a receiver</option>
													<?php 
													$branches = \App\Models\AgentDetails::where('id','!=', '')->get();
													foreach($branches as $branch){
													?>
														<option data-v="{{$branch->income_sharing}}" <?php if($IncomeSharing && $IncomeSharing->rec_id == $branch->id){ echo 'selected'; } ?> value="{{$branch->id}}">{{$branch->full_name}}</option>
													<?php } ?>
														
													</select>
												</div>
												<div class="income_col">
													<div class="label_input"><span class="currencyinput">$
														<input disabled type="number" name="incomeshare_amount" placeholder="Amount" class="incomeAmount" />
													</span>
														<div class="basic_label">AUD</div>
													</div>
												</div>
												<div class="tax_col">
													<div class="form-check form-check-inline">
														<input disabled class="form-check-input" type="checkbox" id="taxval" value="1" name="taxval">
														<label class="form-check-label" for="taxval">Tax</label>
													</div>
													<div class="input_field ifcheckedtax" style="display:none;">
														<select id="taxget" class="form-control">
														    <option value="">Select Tax</option>
														 	@foreach(\App\Models\TaxRate::all() as $taxlist)
															<option value="{{$taxlist->amount}}">{{$taxlist->name}}</option>
														@endforeach   
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="tax_amount_field">
												<div class="tax_amount">
													<span>Tax Amount: </span>
													<span class="percentagenotinput">$</span>
													<span class="taxa">0.00</span>
												</div>
												<div class="tax_amount">
													<span>Total Including Tax: </span>
													<span class="percentagenotinput">$</span>
													<span class="total_includetax">0.00</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-md-12">
										<div class="invoice_btns text-right">
											<button class="btn btn-success" onclick="customValidate('invoiceform','savepreview')" type="button">Save & Preview</button>
											<button class="btn btn-primary" onclick="customValidate('invoiceform','save')" type="button">Update</button>
											<!--<button class="btn btn-primary">Save & Send</button>
											<button class="btn btn-secondary">Cancel</button>-->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('#share_user','change', function(){
		if($('#share_user option:checked').val() != ''){
			$('.incomeAmount').prop('disabled',false);
			$('#taxval').prop('disabled',false);
		}else{
			$('.incomeAmount').prop('disabled',true);
			$('#taxval').prop('disabled',true);
		}	
	});
		$(document).delegate('#taxval','change', function(){
		if($('#taxval').is(':checked')){
		$('.ifcheckedtax').show();
		}else{
			$('.ifcheckedtax').hide();
		}	
	});
	<?php
		if($invoicedetail->type == 2){
			?>
		$(document).delegate('#invoicepaid','change', function(){
			grandtotal();		
		});
		$(document).delegate('.comm_per','keyup', function(){
			var comm_per = $(this).val();
			var currentRow=$(this).closest("tr");
			var total_fee = currentRow.find('.total_fee').val();
			var cserv = 0.00;
			if(comm_per != ''){
				cserv = comm_per;
			}

			calamount = (total_fee * cserv) / 100;
			var netamount = total_fee - calamount;
			currentRow.find('.comm_amt').val(calamount);
			currentRow.find('.netamount').val(netamount.toFixed(2));
			grandtotal();										
		});	

$(document).delegate('.comm_amt','keyup', function(){
		var comm_amt = $(this).val(); 
		var currentRow=$(this).closest("tr");
		var total_fee = currentRow.find('.total_fee').val();
		var cserv = 0.00;
	
		if(comm_amt != ''){
			cserv = comm_amt;
		
		}
		calamount = (total_fee * cserv) / 100;
		var per = calamount / 100;
		currentRow.find('.comm_per').val(per);
		
		calamount = (total_fee * per) / 100;

		var netamount = total_fee - calamount;
		currentRow.find('.netamount').val(calamount.toFixed(2));
			
		
			grandtotal();
	});		
	$(document).delegate('.total_fee','keyup', function(){
		var total_fee = $(this).val(); 
		var currentRow=$(this).closest("tr");
		var comm_per = currentRow.find('.comm_per').val();
		var cserv = 0.00;
	
		if(comm_per != ''){
			cserv = comm_per;
		
		}
			calamount = (total_fee * cserv) / 100;
		var netamount = total_fee - calamount;
		currentRow.find('.comm_amt').val(calamount);
		currentRow.find('.netamount').val(calamount.toFixed(2));
			grandtotal();
		
	});	
	
	$(document).delegate('#discount','keyup', function(){
		var discount = $(this).val(); 
		
		grandtotal();
	});

function grandtotal(){
		var paymentAmount = 0;
		var pric = 0;
		var comm_amt = 0;
		$('.productitem tr').each(function(){
			
			if($(this).find('.netamount').val() != ''){
				var ss = $(this).find('.netamount').val();
			}else{
				var ss = 0;
			}
			
			
			pric += parseFloat(ss);
			
			if($(this).find('.comm_amt').val() != ''){
				var s = $(this).find('.comm_amt').val();
			}else{
				var s = 0;
			}
			comm_amt += parseFloat(s);
		});
		$('.payment_field_col .paymentAmount').each(function(){
			if($(this).val() != ''){
				var sss = $(this).val();
			}else{
				var sss = 0;
			}
			paymentAmount += parseFloat(sss);
		});
		var dic = $('#discount').val();
		var cserv = 0.00;
	
		if(dic != ''){
			cserv = dic;
		}
		if($('#invoicepaid').is(':checked')){
			$('.paymentAmount').val(pric.toFixed(2));
		}
		var totlnetfare = pric - cserv;
		//$('.invoiceNetAmount').html(totlnetfare.toFixed(2));
		//$('#invoice_net_amount').val(totlnetfare.toFixed(2));
		
		var totlcoommfare = comm_amt - cserv;
		var totaldue = pric - paymentAmount;
		
		$('.invoiceNetRevenue').html(totlnetfare.toFixed(2));
		$('#invoice_net_revenue').val(totlnetfare.toFixed(2));
		$('#total_amt').val(pric.toFixed(2));
		$('#total_due').val(totaldue.toFixed(2));
		$('#total_paid').val(paymentAmount.toFixed(2));
		$('#commissionClaimed').val(comm_amt.toFixed(2));
		$('#gst').val(0.00);
		var netpaid = pric - comm_amt;
		$('#netFeePaid').val(netpaid.toFixed(2));
		
	}		
			<?php
		}else{
			?>
	$(document).delegate('.total_fee','keyup', function(){
		var total_fee = $(this).val(); 
		var currentRow=$(this).closest("tr");
		var comm_per = currentRow.find('.comm_per').val();
		var tax_amt = currentRow.find('.tax_amt option:selected').val();
		var cserv = 0.00;
	
		if(comm_per != ''){
			cserv = comm_per;
		
		}
			calamount = (total_fee * cserv) / 100;
			var calculatetax = (calamount * tax_amt) / 100;
			
		var netamount = total_fee - calamount;
		currentRow.find('.comm_amt').val(calamount);
		currentRow.find('.netamount').val(netamount.toFixed(2));
		currentRow.find('.taxamount').val(calculatetax.toFixed(2));
			grandtotal();
		
	});
	$(document).delegate('.comm_per','keyup', function(){
		var comm_per = $(this).val(); 
		var currentRow=$(this).closest("tr");
		var total_fee = currentRow.find('.total_fee').val();
		var tax_amt = currentRow.find('.tax_amt option:selected').val() ?? 10;
		var cserv = 0.00;
	
		if(comm_per != ''){
			cserv = comm_per;
		
		}
		calamount = (total_fee * cserv) / 100;
		var calculatetax = (calamount * tax_amt) / 100;
		var netamount = total_fee - calamount;
		currentRow.find('.comm_amt').val(calamount);
		currentRow.find('.netamount').val(netamount.toFixed(2));
		currentRow.find('.taxamount').val(calculatetax.toFixed(2));
			grandtotal();
	});
	$(document).delegate('.comm_amt','keyup', function(){
		var comm_amt = $(this).val(); 
		var currentRow=$(this).closest("tr");
		var total_fee = currentRow.find('.total_fee').val();
		var tax_amt = currentRow.find('.tax_amt option:selected').val() ?? 10;
		var cserv = 0.00;
	
		if(comm_amt != ''){
			cserv = comm_amt;
		
		}
		calamount = (total_fee * cserv) / 100;
		var per = calamount / 100;
		currentRow.find('.comm_per').val(per);
		
		calamount = (total_fee * per) / 100;
		var calculatetax = (calamount * tax_amt) / 100;
		var netamount = total_fee - calamount;
		currentRow.find('.netamount').val(netamount.toFixed(2));
		currentRow.find('.taxamount').val(calculatetax.toFixed(2));
			
		
			grandtotal();
	});

	$(document).delegate('.tax_amt','change', function(){
		var currentRow=$(this).closest("tr"); 
			var comm_per = currentRow.find('.comm_per').val();
	
		var total_fee = currentRow.find('.total_fee').val();
		var tax_amt = currentRow.find('.tax_amt option:selected').val();
		var cserv = 0.00;
	
		if(comm_per != ''){
			cserv = comm_per;
		
		}
		calamount = (total_fee * cserv) / 100;
		var calculatetax = (calamount * tax_amt) / 100;
		var netamount = total_fee - (calamount + calculatetax);
			currentRow.find('.comm_amt').val(calamount);
			currentRow.find('.netamount').val(netamount.toFixed(2));
			currentRow.find('.taxamount').val(calculatetax.toFixed(2));
			grandtotal();
	});
	
	$(document).delegate('#discount','keyup', function(){
		var discount = $(this).val(); 
		
		var total_fee = $(this).parent().parent().find('.total_fee').val();
		var cserv = 0.00;
	
		if(discount != ''){
			cserv = discount;
		}
		grandtotal();
	});
	
	$(document).delegate('#share_user','change', function(){
		var share = $('#share_user option:selected').attr('data-v'); 
	
		var netincome = $('#invoice_net_income').val();
	
		var cserv = 0.00;
	
		if(share != ''){
		    var i = (parseFloat(netincome) * share) / 100; 
		}
		$('.incomeAmount').val(i.toFixed(2));
		$('.total_includetax').html(i.toFixed(2));
		

	});
		$(document).delegate('#taxget','change', function(){
		var taxget = $('#taxget option:selected').val(); 
	
		var netincome = $('.incomeAmount').val();
	
		var cserv = 0.00;
	
		if(taxget != ''){
		   cserv = taxget;
		}
		 var i = (parseFloat(netincome) * cserv) / 100; 
		$('.taxa').html(i.toFixed(2));
		var r = parseFloat(netincome) + parseFloat(i);
		$('.total_includetax').html(r.toFixed(2));
	    
	});
	
		$(document).delegate('.incomeAmount','keyup', function(){
		var taxget = $('#taxget option:selected').val(); 
	
		var netincome = $(this).val();
	    var n = 0.00;
	
		if(netincome != ''){
		   n = netincome;
		}
		var cserv = 0.00;
	
		if(taxget != ''){
		   cserv = taxget;
		}
		 var i = (parseFloat(n) * cserv) / 100; 
		 
		
		var r = parseFloat(n) + parseFloat(i);
		if(netincome != ''){
		     $('.taxa').html(i.toFixed(2));
		    $('.total_includetax').html(r.toFixed(2));
		}else{
		    $('.taxa').html(0.00);
		    $('.total_includetax').html(0.00);
		}
		
	    
	});
	function grandtotal(){
		var pric = 0;
		var comm_amt = 0;
		var tax_amt = 0;
		$('.productitem tr').each(function(){
			
			if($(this).find('.total_fee').val() != ''){
				var ss = $(this).find('.total_fee').val();
			}else{
				var ss = 0;
			}
			pric += parseFloat(ss);
			if($(this).find('.comm_amt').val() != ''){
				var s = $(this).find('.comm_amt').val();
			}else{
				var s = 0;
			}
			comm_amt += parseFloat(s);

			if($(this).find('.taxamount').val() != ''){
				var sc = $(this).find('.taxamount').val();
			}else{
				var sc = 0;
			}
			tax_amt += parseFloat(sc);
		});
		var dic = $('#discount').val();
		var cserv = 0.00;
	
		if(dic != ''){
			cserv = dic;
		}
		var totlnetfare = pric - cserv;
		$('.invoiceNetAmount').html(totlnetfare.toFixed(2));
		$('#invoice_net_amount').val(totlnetfare.toFixed(2));
		
		var totlcoommfare = comm_amt - cserv;
		
		$('.invoiceNetAmount_2').html(totlcoommfare.toFixed(2));
		$('#invoice_net_income').html(totlcoommfare.toFixed(2));
		$('#totalfee').val(pric.toFixed(2));
		$('#commissionClaimed').val(comm_amt.toFixed(2));
		$('#gst').val(tax_amt);
		var netpaid = pric - comm_amt;
		$('#netFeePaid').val(netpaid.toFixed(2));
		if($('#invoicepaid').is(':checked')){
			$('#paymentpaidamount').val(pric.toFixed(2));
		}
		
			
	}
									<?php } ?>
	$(document).delegate('#invoicepaid', 'change', function(){ 
		if($('#invoicepaid').is(':checked')){
			$('#paymentpaidamount').val($('#totalfee').val());
		}else{
			$('#paymentpaidamount').val(0);
		}
	});
	$('.add_payment_field a').on('click', function(){
		if($('#payment_done').is(':checked')){
			alert('Received amount exceeded total fee.');
		}else{
				var $tableBody = $('.payment_field_row'),
				$trLast = $tableBody.find(".payment_field_col:last"),
				$trNew = $trLast.clone();
				$trNew.find('input').val('');
				$trNew.find('select').val('');
				$trLast.after($trNew);
		}
	}); 
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){ 
		$(this).parent().parent().remove();
		
			grandtotal();
	});
	
	 
	$(document).delegate('.openproductrinfo', 'click', function(){
		var $tableBody = $('#productitemview').find("tbody"),
$trLast = $tableBody.find("tr:last"),
$trNew = $trLast.clone();
$trNew.find('input').val('');
$trNew.find('select').val('');
$trLast.after($trNew);
	}); 
	$(document).delegate('.removeitems', 'click', function(){ 
		$(this).parent().parent().remove();
		
			grandtotal();
	});
});
$('.attachfile').on('change',function(){
     
	      //Get count of selected files
		var countFiles = $(this)[0].files.length;

		var imgPath = $(this)[0].value;
		var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
		var image_holder = $("#image-holder");
		image_holder.empty();

		if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg" || extn == "pdf" || extn =="'docx'") {
			if (typeof (FileReader) != "undefined") {

				//loop for each file selected for uploaded.
				for (var i = 0; i < countFiles; i++) {

					var reader = new FileReader();
					console.log(extn);
					reader.onload = function (e) {
						if(extn == "pdf" || extn == "docx"){
							$("<iframe />", {
							"src": e.target.result,
								"class": "thumb-image"
							}).appendTo(image_holder);
						}else{
							$("<img />", {
							"src": e.target.result,
								"class": "thumb-image"
						}).appendTo(image_holder);
						}
						
					}

					image_holder.show();
					reader.readAsDataURL($(this)[0].files[i]);
				}

			} else {
				alert("This browser does not support FileReader.");
			}
		} else {
			alert("Pls select only images");
		}
    });
</script>
@endsection