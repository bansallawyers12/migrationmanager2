@extends('layouts.admin')
@section('title', 'Invoice')

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

</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
							<?php
							if($invoicedetail->type == 1){
								?>
								<h4>Net Claim Invoice</h4>
								<?php
							}else{
								?>
								<h4>Gross Claim Invoice</h4>
								<?php
							}
							?>
								
								<div class="card-header-action">
									
								</div>
							</div>
						</div>
					</div>
					<?php if($invoicedetail->type == 3){
						?>
						<div class="col-12 col-md-8 col-lg-8">
						<div class="card">
							<div class="card-header">
								<h4>Client Details</h4>
							</div>
							<div class="card-body">
								<div class="invoice_info">
									<ul>
										<li><span>Name:</span> {{$clientdata->first_name}} {{$clientdata->last_name}}</li>
										<li><span>DOB:</span> {{$clientdata->dob}}</li>
										<li><span>Assignee:</span> {{Auth::user()->first_name}}</li>
										<li><span>Service:</span> {{@$workflowdaa->name}}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
						<?php
					}else{ ?>
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
										<li><span>DOB:</span>{{date('d/m/Y', strtotime($clientdata->dob))}}</li>
										<li><span>Client ID:</span>{{$clientdata->client_id}}</li>
										<li><span>Partner:</span> {{@$partnerdata->partner_name}}</li>
										<li><span>Product:</span> {{@$productdata->name}}</li>
										<li><span>Branch:</span> {{@$branchdata->name}}</li>
										<li><span>Workflow:</span> {{@$workflowdaa->name}}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="col-12 col-md-4 col-lg-4">
						<div class="card">							
							<div class="card-body">
								<table>
									<tr>
										<td><b>Invoice:</b></td>
										<td>{{$invoicedetail->invoice_no}}</td>
									</tr>
									<tr>
										<td><b>Invoice Date:</b></td>
										<td>{{date('d/m/Y', strtotime($invoicedetail->invoice_date))}}</td> 
									</tr>
									<tr>
										<td><b>Invoice Due Date:</b></td>
										<td>{{date('d/m/Y', strtotime($invoicedetail->due_date))}}</td>
									</tr>
									<tr>
										<td><b>Created By:</b></td>
										<td>{{@$admindata->first_name}}</td>
									</tr>
								</table>
								
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
										<?php if($invoicedetail->type == 3){
											?>
											<th>Description</th>
												<th>Income Type</th>
												<th>Amount</th>
												<th>Bonus Amount</th>
												<th>Tax (%)</th>
												<th>Tax Amount</th>
												<th>Total Amount</th>
											<?php
										}else{
										?>
												<th>Description</th>
												<th>Total Fee</th>
												<th>Commission Percent</th>
												<th>Commission Amount	</th>
												<th>Bonus Amount</th>
												<th>Tax</th>
												<th>Tax Amount</th>
												<th>Net Amount</th>
										<?php } ?>	
											</tr>
										</thead>
										<tbody class="productitem">
										<?php
										$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicedetail->id)->orderby('id','ASC')->get();
										$coom_amt = 0;
										$tax_amt = 0;
										$total_fee = 0;
										$netamount = 0;
										foreach($invoiceitemdetails as $invoiceitemdetail){
											$coom_amt += $invoiceitemdetail->comm_amt;
											$total_fee += $invoiceitemdetail->total_fee;
											$netamount += $invoiceitemdetail->netamount;
											$tax_amt += $invoiceitemdetail->tax_amount;
											if($invoicedetail->type == 3){
												?>
												<tr class="clonedrow">
												<td>{{$invoiceitemdetail->description}}</td>
												<td>{{$invoiceitemdetail->income_type}}</td>
												<td>
												<span class="currencyinput">$
													<input class="form-control" type="number" name="bonus_amount[]" value="{{$invoiceitemdetail->bonus_amount}}" />
												</span>
												</td>
												<td><span class="percentageinput">${{$invoiceitemdetail->total_fee}}</span></td>
												<td><span class="percentageinput">{{$invoiceitemdetail->tax}}%</span></td>
												<td><span class="percentageinput">${{$invoiceitemdetail->tax_amount}}</span></td>
												<td><span class="percentageinput">${{$invoiceitemdetail->netamount}}</span></td>
											</tr>
												<?php
											}else{
												?>
												<tr class="clonedrow">
												<td>{{$invoiceitemdetail->description}}</td>
												<td><span class="percentageinput">${{$invoiceitemdetail->total_fee}}</span></td>
												<td><span class="percentageinput">{{$invoiceitemdetail->comm_per}}%</span></td>
												<td><span class="percentageinput">${{$invoiceitemdetail->comm_amt}}</span></td>
												<td>
												<span class="currencyinput">$
													<input class="form-control" type="number" name="bonus_amount[]" value="{{$invoiceitemdetail->bonus_amount}}"/>
												</span>
												</td>
												<td><span class="percentageinput">{{$invoiceitemdetail->tax}}%</span></td>
												<td><span class="percentageinput">${{$invoiceitemdetail->tax_amount}}</span></td>
												<td><span class="percentageinput">${{$invoiceitemdetail->netamount}}</span></td>
											</tr>
												<?php
											}
										?>
											
										<?php } ?>
										</tbody>										
									</table>
								</div>
								
								<div class="row bottom_aligned">
								<?php if($invoicedetail->type != 3){ ?>
									<div class="col-md-7 cus_col_7">
										<div class="col_full">
											<label>Discount Given to Client:</label>
										</div>
										<div class="col_half col_discount_field">
											<div class="form-group inline_field">
												<div class="label_input">
												<span class="percentageinput">$
													<input class="form-control" readonly type="text" id="discount" value="{{$invoicedetail->discount}}" ></span>
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
														<input type="text" name="" placeholder="" value="{{date('d/m/Y', strtotime($invoicedetail->discount_date))}}" class=" form-control" />
													</div>
													
												</div> 
											</div>
										</div>
									</div>
								<?php } ?>
									<div class="col-md-7 cus_col_7">
										<?php
										if($invoicedetail->type == 2){
											?>
											<div class="invoiceNetResult"> 
												<span>Net Revenue</span>
												<p id="totalFee" class="invoiceNetAmount"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_fee_rec}}</p>
											</div>
											<?php
										}else if($invoicedetail->type == 3){
											?>
											<div class="invoiceNetResult"> 
												<span>Total Payables</span>
												<p id="totalFee" class="invoiceNetAmount"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_fee_rec}}</p>
											</div>
											<div class="invoiceNetResult">
												<span>Total Income</span>
												<p class="invoiceNetAmount_2"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_incone}}</p>
											</div>
											<?php
										}else{
											?>
											<div class="invoiceNetResult"> 
												<span>Net Fee Received</span>
												<p id="totalFee" class="invoiceNetAmount"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_fee_rec}}</p>
											</div>
											<div class="invoiceNetResult">
												<span>Net Income</span>
												<p class="invoiceNetAmount_2"><span class="feecurrencyinput">$</span>{{$invoicedetail->net_incone}}</p>
											</div>
											<?php
										}
										?>
										
									</div>
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
												
													<input type="text" id="" value="{{$coom_amt}}" readonly="readonly">
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
												<label>Total Amount (incl Tax):</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													
													<input readonly type="text" name="" value="{{$coom_amt}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Paid:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													
													<input readonly type="text" name="" value="{{$amount_rec}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<?php
											$totaldue = $coom_amt - $amount_rec;
											?>
											<div class="inline_field">
												<label>Total Due:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													
										<input readonly type="text" name="" value="{{$totaldue}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
										</div>
										<?php }
										else if($invoicedetail->type == 3){
											?>
											<div class="invoiceInformationDiv">
											<div class="inline_field">
												<label>Total Amount:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
												
													<input type="text" id="" value="{{$netamount}}" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											
											<div class="inline_field">
												<label>Tax:</label>
												<span class="percentageinput">%</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="0">
													<input type="text" id="gst" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											
											<div class="inline_field">
												<label>Total Amount (incl Tax):</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													
													<input readonly type="text" name="" value="{{$netamount}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Paid:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													
													<input readonly type="text" name="" value="{{$amount_rec}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<?php
											$totaldue = $netamount - $amount_rec;
											?>
											<div class="inline_field">
												<label>Total Due:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													
										<input readonly type="text" name="" value="{{$totaldue}}">
													<div class="basic_label">AUD</div>
												</div>
											</div>
										</div>
											<?php
										}
										else{ ?>
										<div class="invoiceInformationDiv">
											<div class="inline_field">
												<label>Total Fee:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
													<input readonly type="text" name="" value="{{$total_fee}}">
													
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												<label>Commission Claimed:</label>
												<span class="currencyinput">$</span>
												<div class="label_input">
												
													<input type="text" id="" value="{{$coom_amt}}" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												<label>Tax:</label>
												<span class="percentageinput">%</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="0">
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
													
													<input type="text" name="" value="{{$feepaid}}">
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
											<table>
												<tr>
													<td><b>Payment Option</b></td>
													<td>{{$invoicedetail->payment_option}}</td>
												</tr>
											</table>
											
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-lg-8">
										<div class="payment_check">
											<h4>Payments Received</h4>
											<div class="">
												<div class="payment_field_row">
													<table class="table">
														<thead>
															<tr>
																<th>Date</th>
																<th>Pament By</th>
																<th>Amount</th>
																<th>Amount Type</th>
																<th></th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php
															$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->get();
															$totlacount = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->count();
															if($totlacount !== 0){
															foreach($paymentdetails as $paymentdetail){
																?>
																<tr>
																<td>{{date('d/m/Y', strtotime($paymentdetail->payment_date))}}</td>
																	<td>{{$clientdata->first_name}}</td>
																	<td><span class="currencyinput">${{$paymentdetail->amount_rec}}</span></td>
																	<td>{{$paymentdetail->payment_mode}}</td>
																	<td><!--<a href=""><i class="fa fa-envelope"></i></a>&nbsp;<a href=""><i class="fa fa-inbox"></i></a>--></td>
																	<td><a data-id="{{$paymentdetail->id}}" class="revertpopup btn btn-danger" href="javascript:;">Revert</a></td>
																</tr>
																<?php
															}
															}else{
																?>
																<tr>
																	<td colspan="6">There is no payment done for this invoice</td>
																</tr>
																<?php
															}
															?>
														</tbody>
													</table>
												</div>
												
												<div class="clearfix"></div>
											</div>
										</div> 
									</div>
									<div class="col-lg-4">
										<div class="add_notes" style="margin-bottom:15px;">
											<h4>Add Notes</h4>
											<textarea class="form-control" readonly name="notes">{{$invoicedetail->notes}}</textarea>
										</div>
										<div class="attachment">
											<h4>Attachments</h4>
											<div class="invoice_attach_file">
											@if(@$invoicedetail->attachments != '')
												<span><a download href="{{URL::to('/public/img/invoice')}}/{{@$invoicedetail->attachments}}">{{@$invoicedetail->attachments}}</a></span>
												@else
												No Attachment Found
											@endif
												
											</div>
										</div>
									</div>
								</div>
								<?php
								if(\App\Models\IncomeSharing::where('invoice_id',$invoicedetail->id)->exists()){
									$IncomeSharing = \App\Models\IncomeSharing::where('invoice_id',$invoicedetail->id)->first();
									$IncomeBranch = \App\Models\Branch::where('id',$IncomeSharing->rec_id)->first();
								?>
								<div class="divider"></div>
								<div class="income_sharing">
								
									<div class="row">
										<div class="col-lg-12">
											<h4>Income Sharing</h4>
										</div>
										<div class="col-lg-8">
											
												<div class="income_field">
													<div class="income_col">
														<b>{{@$IncomeBranch->office_name}}</b>
													</div>
													<div class="income_col">
														<div class="label_input">
														<span class="currencyinput">$
															<input value="{{$IncomeSharing->amount}}" readonly type="text"  /></span>
															<div class="basic_label">AUD</div>
														</div>
													</div>
													
												</div>
											</div>
									</div>
								</div>
								<?php } ?>
								<div class="divider"></div>
								<div class="row">
									<div class="col-md-12">
										<div class="invoice_btns text-right">
											<a target="_blank" href="{{URL::to('admin/invoice/preview/')}}/{{$invoicedetail->id}}" class="btn btn-success">Preview & Print</a>
											
											<button class="btn btn-primary addpaymentmodal" >Add Payment</button>
											<a class="btn btn-secondary" href="{{URL::to('admin/invoice/edit/')}}/<?php echo $invoicedetail->id; ?>">Edit</a>
											
											<button data-id="{{$invoicedetail->id}}" data-rec-name="invoice_{{$invoicedetail->id}}.pdf" data-href="{{URL::to('admin/invoice/preview/')}}/{{@$invoicedetail->id}}" data-cus-id="{{@$clientdata->id}}" data-email="{{@$clientdata->email}}" data-name="{{@$clientdata->first_name}} {{@$clientdata->last_name}}" class="btn btn-primary clientemail">Send</button>
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
<div id="confirmEducationModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Are your sure you want revert?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Accept</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="addpaymentmodal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
	{{ Form::open(array('url' => 'admin/invoice/payment-store', 'name'=>"invoicepaymentform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
	<input type="hidden" value="{{$invoicedetail->id}}" name="invoice_id">
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
								<span class="currencyinput">$
									<input data-valid="required" type="number" name="payment_amount[]" placeholder="" class="paymentAmount" /></span>
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
									<input type="date" name="payment_date[]" placeholder="" class=" form-control" />
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
								<td ><span class="percentagenotinput">$</span>{{$netamount}} AUD</td>
								<td><b>Total Due:</b><span class="percentagenotinput" style="margin-left: 30px;">$</span></td>
								<td class="totldueamount" data-totaldue="{{@$totaldue}}">{{@$totaldue}}</td>
								<td>
							</tr>
						
						</table> 
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="customValidate('invoicepaymentform')" class="btn btn-primary" >Save & Close</button>
				<button type="button" class="btn btn-primary">Save & Send Receipt</button>
			  </div>
		</div>
		</form>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('.paymentAmount','keyup', function(){
		grandtotal();	
			
		});
		function grandtotal(){
			var p =0;
			$('.paymentAmount').each(function(){
				if($(this).val() != ''){
					p += parseFloat($(this).val());
				}
			});
		
			var tamount = $('.totldueamount').attr('data-totaldue');
			
			var am = parseFloat(tamount) - parseFloat(p);
			$('.totldueamount').html(am.toFixed(2));
		}
	$('.add_payment_field a').on('click', function(){
		var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
		$('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');
	}); 
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){ 
		var $tr    = $(this).closest('.payment_field_clone');
		var trclone = $('.payment_field_clone').length;		
		if(trclone > 0){
			$tr.remove();
			grandtotal();
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
	});
	var pid = 0;
	$(document).delegate('.revertpopup','click', function(){
		pid = $(this).attr('data-id');
		$('#confirmEducationModal').modal('show');
	});
$(document).delegate('.accepteducation', 'click', function(){
	
		$('.popuploader').show(); 
		$('#confirmEducationModal').modal('hide');
		$.ajax({
			url: '{{URL::to('/admin/')}}/invoice/delete-payment',
			type:'GET',
			datatype:'json',
			data:{pay_id:pid},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					location.reload();
				}else{
					alert('Please try again')
				}
			}
		});
	});
	$(document).delegate('.addpaymentmodal','click', function(){
		
		$('#addpaymentmodal').modal('show');
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