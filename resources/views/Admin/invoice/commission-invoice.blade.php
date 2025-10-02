@extends('layouts.admin')
@section('title', 'Create General Invoice')

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
			{{ Form::open(array('url' => 'admin/invoice/store', 'name'=>"invoiceform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			<input type="hidden" name="client_id" value="{{$clientid}}">
			<input type="hidden" name="applicationid" value="{{$applicationid}}">
			<input type="hidden" name="type" value="{{$type}}">

				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
							<?php
							if($type == 1){
								?>
								<h4>Create Net Claim Invoice</h4>
								<?php
							}else{
								?>
								<h4>Create Gross Claim Invoice</h4>
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
										<li><span>Name:</span> {{$partnerdata->partner_name ?? 'N/A'}}</li>
										<li><span>Address:</span> {{$partnerdata->address ?? 'N/A'}}</li>
										<li><span>Contact:</span> {{$partnerdata->phone ?? 'N/A'}}</li>
										<li><span>Service:</span> {{$workflowdaa->name ?? 'N/A'}}</li>
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
										<li><span>Name:</span> <input style="width: 85px;" type="text" name="first_name" value="{{$clientdata->first_name ?? 'N/A'}}"> <input style="width: 85px;" type="text" name="last_name" value="{{$clientdata->last_name ?? 'N/A'}}"></li>
										<li style="margin-top: 5px;"><span>DOB:</span> <input style="width: 173px;display: inline-block;" class="form-control dobdatepickers" data-valid="" autocomplete="off" placeholder="" name="dob" type="text" value="{{date('d/m/Y', strtotime($clientdata->dob)) ?? 'N/A' }}"></li>
										<li><span>Client ID:</span> {{$clientdata->client_id ?? 'N/A'}}</li>
										<li><span>Partner:</span> {{$partnerdata->partner_name ?? 'N/A'}}</li>
										<li><span>Product:</span> {{$productdata->name ?? 'N/A'}}</li>
										<li><span>Branch:</span> {{$branchdata->name ?? 'N/A'}}</li>
										<li><span>Workflow:</span> {{$workflowdaa->name ?? 'N/A'}}</li>
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
									{{ Form::date('invoice_date', date('Y-m-d'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
									<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
									@if ($errors->has('invoice_date'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('invoice_date') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="invoice_due_date">Invoice Due Date:</label>
									{{ Form::date('invoice_due_date', date('Y-m-d'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
									<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
									@if ($errors->has('invoice_due_date'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('invoice_due_date') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="invoice_due_date">Select Profile:</label>
									<select class="form-control" name="profile">
										@foreach(\App\Models\Profile::all() as $profiles)
											<option value="{{$profiles->id}}">{{$profiles->company_name}}</option>
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
									<table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
										<thead>
											<tr>

												<th>Description</th>
												<th>Total Fee</th>
												<th style="width:100px">Commission Percent</th>
												<th style="width:100px">Commission Amount	</th>
												<th>Bonus Amount	</th>
												<th style="width:100px">Tax</th>
												<th>Tax Amount</th>
												<th>Net Amount</th>
												<th></th>
											</tr>
										</thead>
										<tbody class="productitem">
											<tr class="clonedrow">
												<td>
													<!-- <select name="description[]" class="form-control">
														<option value="0">Select Description type</option>
															<option value="Tution Fee">Tution Fee</option>
															<option value="Application Fee">Application Fee</option>
															<option value="Service Fee">Service Fee</option>
															<option value="Bonus Charges">Bonus Charges</option>
															<option value="Bonus Charges">Other</option>
													</select> -->
													<input class="form-control" name="description[]" type="text" value="Tuition Fee" />
												</td>
												<td>
													<span class="currencyinput">$
														<input class="form-control total_fee" name="total_fee[]" type="number"  />
													</span>
												</td>
												<td>
													<span class="percentageinput">
														<input class="form-control comm_per" type="number" name="comm_per[]" value="{{$partnerdata->commission_percentage ?? '0'}}" />%
													</span>
												</td>
												<td>
													<span class="currencyinput">$
														<input class="form-control comm_amt" type="number" name="comm_amt[]" />
													</span>
												</td>
												<td>
													<span class="currencyinput">$
														<input class="form-control bonus_amount" type="number" name="bonus_amount[]" value="0.00"/>
													</span>
												</td>
												<td>
													<select name="tax[]" class="form-control tax_amt">
														<option value="0">Select A Tax Code</option>
														@foreach(\App\Models\TaxRate::all() as $taxlist)
															<option value="{{$taxlist->amount}}" @if ($taxlist->amount == '10') Selected @endif>{{$taxlist->name}}</option>
														@endforeach
													</select>
												</td>
												<td>
													<span class="currencyinput">$
														<input class="form-control taxamount" name="tax_amount[]" type="text" value="0.00" readonly />
													</span>
												</td>
												<td>
													<span class="currencyinput">$
														<input class="form-control netamount" name="netamount[]" type="text" value="0.00" readonly />
													</span>
												</td>
												<td>
													<a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="add_new">
									<a href="javascript:;" class="openproductrinfo"><i class="fa fa-plus"></i> Add New Line</a>
								</div>
								<div class="row bottom_aligned">
									<div class="col-md-6 cus_col_6">
										<div class="form-group">
											<label>Discount Given to Client:</label>
											<div class="label_input">
												<span class="currencyinput">$
												<input class="form-control" name="discount" type="text" id="discount" ></span>
												<div class="basic_label">AUD</div>
											</div>
										</div>
										<div class="form-group">
											<div class="field_col">
												<div class="input-group">
													<div class="input-group-prepend">
														<div class="input-group-text">
															<i class="fas fa-clock"></i>
														</div>
													</div>
													<input type="date" name="discount_date" placeholder="" class="form-control" />
												</div>
												<!-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> -->
											</div>
										</div>
									</div>
									<?php
									if($type == 2){
										?>
										<div class="col-md-7 cus_col_7">
										<div class="invoiceNetResult">
											<span>Net Revenue</span>
											<p id="totalFee" class="invoiceNetRevenue"><span class="feecurrencyinput">$</span>0.00</p>
											<input type="hidden" id="invoice_net_revenue" name="invoice_net_revenue" placeholder="" class="" />
										</div>
									</div>
										<?php
									}else{
									?>
									<div class="col-md-7 cus_col_7">
										<div class="invoiceNetResult">
											<span>Net Fee Received</span>
											<p id="totalFee" class="invoiceNetAmount"><span class="feecurrencyinput">$</span>0.00</p>
											<input type="hidden" id="invoice_net_amount" name="invoice_net_amount" placeholder="" class="" />
										</div>
										<div class="invoiceNetResult">
											<span>Net Income</span>
											<p class="invoiceNetAmount_2"><span class="feecurrencyinput">$</span>0.00</p>
											<input type="hidden" id="invoice_net_income" name="invoice_net_income" placeholder="" class="" />
										</div>
									</div>
									<?php
									}
									?>
									<div class="col-md-5 cus_col_5">
									<?php
									if($type == 2){
										?>
										<div class="invoiceInformationDiv">

											<div class="inline_field">
												<label>Commission Claimed:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total" value="0">
													<input type="text" id="commissionClaimed" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>

											<div class="inline_field">
                                            	<label>Bonus Amount:</label>
                                            	<span class="currencyinput">$</span>
                                            	<div class="label_input">
                                            		<input type="hidden" name="bonus_amount1" value="0">
                                            		<input type="text" id="bonusAmount" readonly="readonly">
                                            		<div class="basic_label">AUD</div>
                                            	</div>
                                            </div>


											<div class="inline_field">
												<label>Tax:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="0">
													<input type="text" id="gst" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Amount (incl Tax)</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_amt" value="0">
													<input type="text" id="total_amt" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Paid</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_paid" value="0">
													<input type="text" id="total_paid" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Due</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_due" value="0">
													<input type="text" id="total_due" readonly="readonly"> <div class="basic_label">AUD</div>
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
													<input type="hidden" name="total" value="0">
													<input type="text" id="totalfee" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Commission Claimed:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total" value="0">
													<input type="text" id="commissionClaimed" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>

											<div class="inline_field">
												<label>Bonus Amount:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="bonus_amount1" value="0">
													<input type="text" id="bonusAmount" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>

											<div class="inline_field">
												<label>Tax:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="0">
													<input type="text" id="gst" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Net Fee Paid to Partner:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input type="hidden" name="invoice_amount" value="0">
													<input type="text" id="netFeePaid" readonly="readonly">
													<input type="hidden" name="total" value="0">
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
												<option value="Income">Income</option>
												<option value="Payables"></option>
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
									if($type == 2){
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
													<div class="payment_field_col payment_first_step">
														<div class="field_col">
															<div class="label_input">
															<span class="currencyinput">$
																<input id="paymentpaidamount" type="number" name="payment_amount[]" placeholder="" class="paymentAmount" />
																<div class="basic_label">AUD</div>
															</span>
															</div>
														</div>
														<div class="field_col">
															<div class="input-group">
																<div class="input-group-prepend">
																	<div class="input-group-text">
																		<i class="fas fa-clock"></i>
																	</div>
																</div>
																<input type="date" name="payment_date[]" placeholder="Date" class=" form-control" value="{{date('Y-m-d')}}"/>
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
											<textarea class="form-control" name="notes"></textarea>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="attachment">
											<h4>Attachments</h4>
											<div class="invoice_attach_file">
												<span>Click to upload</span>
												<input name="attachfile[]" class="attachfile" type="file" accept="image/*,application/pdf" multiple><br/>
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
										<div class="col-lg-8">
											<div class="income_field">
												<div class="income_col">
													<select class="form-control" id="share_user" name="share_user">
													<option value="no">Select a receiver</option>
													<?php
													$branches = \App\Models\AgentDetails::where('id','!=', '')->get();
													foreach($branches as $branch){
													?>
														<option data-v="{{$branch->income_sharing}}"  value="{{$branch->id}}">{{$branch->full_name}}</option>
													<?php } ?>

													</select>
												</div>
												<div class="income_col">
													<div class="label_input">
													<span class="currencyinput">$
														<input disabled type="number" name="incomeshare_amount" placeholder="Amount" class="incomeAmount" />
														<div class="basic_label">AUD</div>
													</span>
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
													<span class="currencynotinput">$</span>
													<span class="taxa">0.00</span>
												</div>
												<div class="tax_amount">
													<span>Total Including Tax: </span>
													<span class="currencynotinput">$</span>
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
										<input type="hidden" name="btn" value="save">
											<button name="btntype" value="savepreview" class="btn btn-success clickbtn" onclick="customValidate('invoiceform','savepreview')" type="button">Save & Preview</button>
											<button name="btntype" value="save" class="btn btn-primary clickbtn" onclick="customValidate('invoiceform','save')" type="button">Save</button>

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
    	$(document).delegate('#taxval','change', function(){
		if($('#taxval').is(':checked')){
		$('.ifcheckedtax').show();
		}else{
			$('.ifcheckedtax').hide();
		}
	});
	$(document).delegate('#share_user','change', function(){
		if($('#share_user option:checked').val() != ''){
			$('.incomeAmount').prop('disabled',false);
			$('#taxval').prop('disabled',false);
		}else{
			$('.incomeAmount').prop('disabled',true);
			$('#taxval').prop('disabled',true);
		}
	});


	<?php
		if($type == 2){

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

    function grandtotal(){ //alert('2');
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

		var netamount = total_fee - (calamount + calculatetax);
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
		// console.log(total_fee);
		calamount = (total_fee * cserv) / 100;
		var calculatetax = (calamount * tax_amt) / 100;

		var netamount = total_fee - (calamount + calculatetax);
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
		var netamount = total_fee - (calamount + calculatetax);

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

		var bonus_amount = currentRow.find('.bonus_amount').val(); //bonus amount

	    var netamount = total_fee - (parseFloat(calamount) + parseFloat(calculatetax) + parseFloat(bonus_amount) ); //alert('netamount=='+netamount);

		currentRow.find('.comm_amt').val(calamount);
		currentRow.find('.netamount').val(netamount.toFixed(2));
		currentRow.find('.taxamount').val(calculatetax.toFixed(2));

		grandtotal();
	});

	//Function For onchnage at bonus amount
	$(document).delegate('.bonus_amount','change', function(){
		var currentRow=$(this).closest("tr");

		//total fee amount
		var total_fee = currentRow.find('.total_fee').val();
		if(total_fee != ''){
			total_fee = total_fee;
		} else {
		    total_fee = 0;
		}
		total_fee = Number(total_fee).toFixed(2);  //alert('total_fee==='+total_fee);

		//commission amount
		var comm_amt = currentRow.find('.comm_amt').val();
		if(comm_amt != ''){
			comm_amt = comm_amt;
		} else {
		    comm_amt = 0;
		}
		comm_amt = Number(comm_amt).toFixed(2); //alert('comm_amt==='+comm_amt);

		//bonus amount
		var bonus_amount = $(this).val();
	    if(bonus_amount != ''){
			bonus_amount = bonus_amount;
		} else {
		    bonus_amount = 0;
		}
		bonus_amount = Number(bonus_amount).toFixed(2);  //alert('bonus_amount==='+bonus_amount);


		//tax amount
		var taxamount = currentRow.find('.taxamount').val();
		if(taxamount != ''){
			taxamount = taxamount;
		} else {
		    taxamount = 0;
		}
		taxamount = Number(taxamount).toFixed(2);  //alert('taxamount==='+taxamount);

		var otherAmount = parseFloat(comm_amt) + parseFloat(bonus_amount) + parseFloat(taxamount); //alert('otherAmount==='+otherAmount);
		if(otherAmount != ''){
			otherAmount = otherAmount;
		} else {
		    otherAmount = 0;
		}
		otherAmount = Number(otherAmount).toFixed(2);  //alert('otherAmount==='+otherAmount);

		var netamount_updated = parseFloat(total_fee) - parseFloat(otherAmount);
		if(netamount_updated != ''){
			netamount_updated = netamount_updated;
		} else {
		    netamount_updated = 0;
		}
		netamount_updated = Number(netamount_updated).toFixed(2);  //alert('netamount_updated==='+netamount_updated);
		currentRow.find('.netamount').val(netamount_updated);

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

	function grandtotal(){ //alert('1');
		var pric = 0;
		var comm_amt = 0;
		var tax_amt = 0;
		var bonus_amt = 0;
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


			if($(this).find('.bonus_amount').val() != ''){
				var sb = $(this).find('.bonus_amount').val();
			}else{
				var sb = 0;
			}
			bonus_amt += parseFloat(sb);
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
		$('#invoice_net_income').val(totlcoommfare.toFixed(2));
		$('#totalfee').val(pric.toFixed(2));
		$('#commissionClaimed').val(comm_amt.toFixed(2));
		$('#gst').val(tax_amt);

		$('#bonusAmount').val(bonus_amt);


		var netpaid = pric - (comm_amt + tax_amt + bonus_amt);
		$('#netFeePaid').val(netpaid.toFixed(2)); //,.netamount
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
		var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
		$('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');
	});
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){
		var $tr    = $(this).closest('.payment_field_clone');
		var trclone = $('.payment_field_clone').length;
		if(trclone > 0){
			$tr.remove();
		}
	});

	$(document).delegate('.openproductrinfo', 'click', function(){
		var clonedval = $('.clonedrow').html();
		$('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');
	});
	$(document).delegate('.removeitems', 'click', function(){
		var $tr    = $(this).closest('.product_field_clone');
		var trclone = $('.product_field_clone').length;
		if(trclone > 0){
			$tr.remove();
		}
		grandtotal();
	});

	$('.attachfile').on('change',function(){
              //Get count of selected files
		var countFiles = $(this)[0].files.length;

var imgPath = $(this)[0].value;
var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();  //alert('extn=='+extn);
var image_holder = $("#image-holder");
image_holder.empty();

if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg" || extn == "pdf") {
	if (typeof (FileReader) != "undefined") {

		//loop for each file selected for uploaded.
		for (var i = 0; i < countFiles; i++) {

			var reader = new FileReader();
			reader.onload = function (e) {
				$("<img />", {
					"src": e.target.result,
						"class": "thumb-image"
				}).appendTo(image_holder);
			}

			image_holder.show();
			reader.readAsDataURL($(this)[0].files[i]);
		}

	} else {
		alert("This browser does not support FileReader.");
	}
} else {
	alert("Pls select only images or pdf");
}
    });
});
</script>
@endsection
