@extends('layouts.admin')
@section('title', 'Invoices')

@section('content')
<?php use App\Http\Controllers\Controller; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Invoices</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Invoices</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error"> 
						@include('../Elements/flash-message')
					</div>
					<div class="custom-error-msg"></div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-4">					
					<div class="card">
						<div class="card-body p-0" style="display: block;">
							<div class="mailbox-controls">
								<!-- Check all button -->
								<button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
								</button>
								<!--<a href="javascript:;" class="btn btn-inline-block btn-default btn-sm is_selected_invoice">Mark as Sent</a>  -->
								<div class="more_btn_group">
									<a class="dropdown-toggle btn btn--inline-block btn-default btn-sm is_selected_invoice" data-toggle="dropdown" href="#" aria-expanded="false">
									 More <span class="caret"></span>
									</a>
									<div class="dropdown-menu" x-placement="top-start">
									  <!--<a class="dropdown-item" tabindex="-1" href="#">Bulk Update</a>-->
									  <a class="dropdown-item" tabindex="-1" href="#">Send Emails</a>
									  <a class="dropdown-item print_allinvoice" tabindex="-1" href="javascript:;">Print</a>
									  <a class="dropdown-item exportpdf" tabindex="-1" href="javascript:;">Export as PDF</a>
									  <a class="dropdown-item" tabindex="-1" href="#">Delete</a>
									</div> 
					
								<span class="is_selected_invoice">	<span class="selected_no"></span> Selected</span> 
								</div> 
								
								<div class="float-right new_invoice ">
									<ul>
										<li class="nav-item d-none d-sm-inline-block quick_link">
											<a class="dropdown-toggle btn btn-block btn-outline-primary btn-sm is_not_selected_invoice" data-toggle="dropdown" href="#" aria-expanded="false">
											 <i class="fa fa-plus"></i> New <span class="caret"></span>
											</a> 
											<div class="dropdown-menu" x-placement="top-start">
											  <a class="dropdown-item" tabindex="-1" href="#">New Invoice</a>
											  <a class="dropdown-item" tabindex="-1" href="#">New Invoice (Pre GST)</a>
											  <a class="dropdown-item" tabindex="-1" href="#">New Recurring Invoice</a> 
											  <a class="dropdown-item" tabindex="-1" href="#">New Debit Note</a>
											  <a class="dropdown-item" tabindex="-1" href="#">New Credit Note</a>
											  <a class="dropdown-item" tabindex="-1" href="#">New Credit Note (Pre GST)</a>
											</div>
										</li>
									</ul>	
								</div>
							<?php	$today = date('Y-m-d');
										
										?>
								<!-- /.float-right -->
								<div class="table-responsive invoice_table mailbox-messages">
									<table class="table table-hover table-striped">
										<tbody>
										@foreach($lists as $list)
											<tr>
												<td class="check_td">
												  <div class="icheck-primary">
													<input type="checkbox" name="invoicelist" class="seleccheckbox" value="{{@$list->id}}" id="check{{@$list->id}}">
													<label for="check{{@$list->id}}"></label>
												  </div>
												</td>
												<td class="list_td" invoiceid="{{base64_encode(convert_uuencode(@$list->id))}}">
													<div class="list_primary">
													<?php $currncydata = \App\Models\Currency::where('id',$list->currency_id)->first(); ?>
														<span class="float-right amount"> {{$currncydata->currency_symbol}}{{number_format($list->amount, $currncydata->decimal)}} </span>
														<div class="name" title="{{$list->customer->first_name}} {{$list->customer->last_name}}">{{$list->customer->first_name}} {{$list->customer->last_name}}</div>
													</div>
													<div class="list_secondary">
														<?php
										$count = 2;
										$datetime1 = new DateTime($today);
										$datetime2 = new DateTime($list->due_date);
										$interval = $datetime1->diff($datetime2);
										$diff = $interval->format('%a');
										if(strtotime($today) > strtotime($list->due_date) && $list->status != 1){
											echo '<span class="float-right text-overdue text-uppercase">OVERDUE BY '.$diff.' DAYS<i class="fa fa-envelope-o"></i></span>';
										}else{	
											if($list->status == 1){
												echo '<span class="float-right text-accepted text-uppercase">Paid<i class="fa fa-envelope-o"></i></span>';
											}else if($list->status == 2){
												echo '<span class="float-right text-sent text-uppercase">Sent<i class="fa fa-envelope-o"></i></span>';
											}else if($list->status == 3){
												echo '<span class="float-right text-accepted text-uppercase">Partially Paid<i class="fa fa-envelope-o"></i></span>';
											}else{
												echo '<span class="float-right text-sent text-uppercase">Draft<i class="fa fa-envelope-o"></i></span>';
											}
										}
									?> 
														<div class="number">{{$list->invoice}} </div>
														<span class="text-muted separationline">{{date('d/m/Y',strtotime($list->invoice_date))}}</span>
													</div>
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>		
								</div>
								
							</div>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<?php $currencydata = \App\Models\Currency::where('id',$invoicedetail->currency_id)->first(); ?>
				<div class="col-md-8 showinvoicedata">
					<div class="card card-primary card-outline invoice_list">
						<div class="card-header">
							<h3 class="card-title">{{$invoicedetail->invoice}}</h3>
							<div class="card-tools attach_comment">
								<ul class="">
									<li><a href="javascript:;" dataid="{{base64_encode(convert_uuencode(@$invoicedetail->id))}}" class="attach_filemodel"><i class="fa fa-paperclip"></i>Attach File(s)</a></li> 
									<li><a href="javascript:;" class="commenthistory" dataid="{{$invoicedetail->id}}"><i class="fa fa-comment"></i>Comments & History</a></li> 
									<li><a href="javascript:;"><i class="fa fa-close"></i></a></li>
								</ul>
							</div>
						  <!-- /.card-tools -->
						</div>  
						<!-- /.card-header --> 
						<div class="card-body p-0">
							<div class="custom_card_header">
								<a href="{{URL::to('/admin/invoice/edit/'.base64_encode(convert_uuencode(@$invoicedetail->id)))}}" class="btn btn-inline-block btn-sm cus_btn"><i class="fa fa-edit"></i> Edit</a>  
								<div class="more_btn_group">
									<a class="dropdown-toggle btn btn--inline-block btn-sm cus_btn" data-toggle="dropdown" href="#" aria-expanded="false">
									 <i class="fa fa-envelop-o"></i> Mail <span class="caret"></span>
									</a>
									<div class="dropdown-menu" x-placement="top-start">
									  <a class="dropdown-item" tabindex="-1" href="{{URL::to('/admin/invoice/email/'.base64_encode(convert_uuencode(@$invoicedetail->id)))}}">Send Mail</a>
									</div> 
								</div>
								<a href="javascript:;" dataid="{{base64_encode(convert_uuencode(@$invoicedetail->id))}}" class="btn btn-inline-block btn-sm sharelinkbtn cus_btn"><i class="fa fa-share"></i> Share</a>
								<div class="more_btn_group">
									<a class="dropdown-toggle btn btn--inline-block btn-sm cus_btn" data-toggle="dropdown" href="#" aria-expanded="false">
									 <i class="fa fa-envelop-o"></i> PDF / Print <span class="caret"></span>
									</a>
									<div class="dropdown-menu" x-placement="top-start">
									  <a class="dropdown-item" tabindex="-1" href="{{URL::to('/admin/invoice/download/'.base64_encode(convert_uuencode(@$invoicedetail->id)))}}">PDF</a>
									  <a class="dropdown-item print_invoice" tabindex="-1" dataid="{{base64_encode(convert_uuencode(@$invoicedetail->id))}}" href="javascript:;">Print</a>
									</div> 
								</div>
								<div class="more_btn_group">
									<a class="dropdown-toggle btn btn--inline-block btn-sm cus_btn" data-toggle="dropdown" href="#" aria-expanded="false">
									 <i class="fa fa-envelop-o"></i> Reminders <span class="caret"></span>
									</a>
									<div class="dropdown-menu" x-placement="top-start">
									  <a class="dropdown-item" tabindex="-1" href="{{URL::to('/admin/invoice/reminder/'.base64_encode(convert_uuencode(@$invoicedetail->id)))}}">Send Now</a>
									  <a class="dropdown-item" tabindex="-1" href="#">Stop Reminders</a>
									</div> 
								</div>
								<div class="more_btn_group">
									<a class="dropdown-toggle btn btn--inline-block btn-sm cus_btn" data-toggle="dropdown" href="#" aria-expanded="false">
									 <i class="fa fa-ellipsis-h"></i>
									</a>
									<div class="dropdown-menu" x-placement="top-start">
									  <a class="dropdown-item" tabindex="-1" href="#">Make Recurring</a>
									  <a class="dropdown-item" tabindex="-1" href="#">Create Credit Note</a>
									  <a class="dropdown-item" tabindex="-1" href="#">Create Debit Note</a>
									  <a class="dropdown-item" tabindex="-1" href="#">Clone</a>
									  <div class="dropdown-divider"></div>
									  <a class="dropdown-item" tabindex="-1" href="#">Delete</a>
									</div> 
								</div>
							</div>
							
						</div>
						<!-- /.card-body -->
						<?php
							$paymentdetail = \App\Models\InvoicePayment::where('invoice_id',$invoicedetail->id)->get();

							$paymentcount = \App\Models\InvoicePayment::where('invoice_id',$invoicedetail->id)->count();
						?>
						@if($invoicedetail->status == 1)
						<div id="accordion" class="cus_accordian">
							<div class="card-header">
								<h4 class="card-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="collapsed" aria-expanded="false">Payments Received <span>{{$paymentcount}}</span>
									</a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse in collapse" style="">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-hover table-striped">
											<thead>
												<tr>
													<th>Date</th>
													<th>Payment #</th>
													<th>Payment Mode</th>
													<th>Amount</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												@foreach($paymentdetail as $pd)
												<tr>
													<td>{{date('d/m/Y',strtotime($pd->payment_date))}}</td>
													<td><a href="#">{{@$pd->id}}</a></td>
										
													<td>{{@$pd->payment_mode}}</td>
													<td>₹{{number_format(@$pd->amount_rec)}}</td>
													<td>
														<div class="nav-item dropdown action_dropdown">
															<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
															<div class="dropdown-menu">
															 <a href="javascript:;" dataid="{{@$pd->id}}" class="editpaymentmodel"><i class="fa fa-edit"></i> Edit</a>
															 <a href="javascript:;" onclick=""><i class="fa fa-trash"></i> Delete</a>
															</div> 
														</div>
													</td>
												</tr> 
											@endforeach
											</tbody>
										</table>	
									</div>
								</div>
							</div>  
						</div>
						@elseif($invoicedetail->status == 3)
							<div id="acdion" class="cusaccordian">
								<div class="card-header">
									<a href="javascript:;" dataid="{{$invoicedetail->id}}" class="btn bg-purple margin payment_modal">Record Payment</a>
								</div>
							</div>
							
							<div id="accordion" class="cus_accordian">
							<div class="card-header">
								<h4 class="card-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="collapsed" aria-expanded="false">Payments Received <span>{{$paymentcount}}</span>
									</a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse in collapse" style="">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-hover table-striped">
											<thead>
												<tr>
													<th>Date</th>
													<th>Payment #</th>
													
													<th>Payment Mode</th>
													<th>Amount</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												@foreach($paymentdetail as $pd)
												<tr>
													<td>{{date('d/m/Y',strtotime($pd->payment_date))}}</td>
													<td><a href="#">{{@$pd->id}}</a></td>
										
													<td>{{@$pd->payment_mode}}</td>
													<td>{{$currencydata->currency_symbol}}{{number_format(@$pd->amount_rec,$currencydata->decimal)}}</td>
													<td>
														<div class="nav-item dropdown action_dropdown">
															<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
															<div class="dropdown-menu">
															 <a href="javascript:;" dataid="{{@$pd->id}}" class="editpaymentmodel"><i class="fa fa-edit"></i> Edit</a>
															 <a href="javascript:;" onclick=""><i class="fa fa-trash"></i> Delete</a>
															</div> 
														</div>
													</td>
												</tr> 
											@endforeach  
											</tbody>
										</table>	
									</div>
								</div>
							</div>  
						</div>
						@else
							
							<div id="accordion" class="cus_accordian">
								<div class="card-header">
									<a href="javascript:;" dataid="{{$invoicedetail->id}}" class="btn bg-purple margin payment_modal">Record Payment</a>
								</div>
							</div>
						@endif
					</div>
				  <!-- /.card -->
					<div class="card card-primary">						
						<div id="invoice_085" class="ember-view invoice_template">
						<?php if(strtotime($today) > strtotime($invoicedetail->due_date) && $invoicedetail->status != 1){
							$stattyp = 'Overdue';
							$classty = 'danger';
						}else{	
							if($invoicedetail->status == 1){
								$stattyp = 'Paid';
								$classty = 'success';
							}else if($invoicedetail->status == 2){
								$stattyp = 'Sent';
								$classty = 'open';
							}else if($invoicedetail->status == 3){
								$stattyp = 'Partially Paid';
								$classty = 'success';
							}else{
								$stattyp = 'Draft';
								$classty = 'danger';
							}
						} 
						?>
							<div id="invoice_ribbben" class="ribbon text-ellipsis tooltip-container ember-view"><div class="ribbon-inner ribbon-<?php  echo $classty; ?>"><?php echo $stattyp;  ?></div></div>
							<div class="inv-template">  
								<div class="inv-template-header inv-header-content" id="header">
									<div class="inv-template-fill-emptydiv"></div>
								</div>
								<div class="inv-template-body">
									<div class="inv-template-bodysection">
										<table style="width: 100%;border-bottom:1px solid #000;">
											<tbody>
												<tr>
													<td style="width:50%;vertical-align: middle;padding: 2px 8px;">
														<div>
															<span style="font-weight: bold;" class="inv-orgname">{{@Auth::user()->company_name}}<br></span>
															<span style="white-space: pre-wrap;font-size: 14px;" id="tmp_org_address">{{@Auth::user()->address}} {{@Auth::user()->city}} {{@Auth::user()->zip}} <br/>{{@Auth::user()->country}}</span>
														</div>
													</td>
													<td style="width:40%;padding: 5px;vertical-align: bottom;" align="right">
														<div class="inv-entity-title">TAX INVOICE</div>
													</td>  
												</tr>
											</tbody>
										</table>

										<div style="border-bottom:1px solid #000;">
											<table cellspacing="0" cellpadding="0" border="0" style="width: 50%;border-right:1px solid #000;" class="invoice-detailstable">
												<thead>
													<tr>													
														<th>#</th>
														<th>{{$invoicedetail->invoice}}</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Invoice Date</td>
														<td>{{date('d/m/Y',strtotime($invoicedetail->invoice_date))}}</td>
													</tr>
													<tr>
														<td>Terms</td>
														<td>{{$invoicedetail->terms}}</td>
													</tr>
													<tr>
														<td>Due Date</td>
														<td>{{date('d/m/Y',strtotime($invoicedetail->due_date))}}</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div style="background:#f3f3f3;padding:5px;">
											<table style="width: 100%;" class="inv-addresstable" border="0" cellspacing="0" cellpadding="0">
												<thead>
													  <tr>
															<th style=""><label style="margin-bottom: 0px;display: block;" id="tmp_billing_address_label" class="inv-label"><b>Bill To:</b></label></th>
													  </tr>
												</thead>
												<tbody> 
													<tr>
														<td style="" valign="top">
															<span style="white-space: pre-wrap;line-height: 15px;color:#0080ec;" id="tmp_billing_address"><strong><span class="inv-customer-name" id="zb-pdf-customer-detail"><a style="color:#0080ec;" href="#">{{@$invoicedetail->customer->company_name}}</a></span></strong></span>
														</td>
													</tr>
													<tr>
														<td  valign="top">
															<span  id="tmp_billing_address"><span class="inv-customer-name" id="zb-pdf-customer-detail">{{@$invoicedetail->customer->first_name}} {{@$invoicedetail->customer->last_name}}</span></span>
														</td>
													</tr><tr>
														<td  valign="top">
															<span  id="tmp_billing_address"><span class="inv-customer-name" id="zb-pdf-customer-detail">{{@$invoicedetail->customer->address}}</span></span>
															<br> {{@$invoicedetail->customer->city}} {{@$invoicedetail->customer->zip}} <br>
															{{@$invoicedetail->customer->country}}
														</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div style="clear:both;"></div>
										<table style="width: 100%;" class="inv-itemtable" id="itemTable" cellspacing="0" cellpadding="0" border="1">
											<thead>
												<tr>
													<th style="text-align: center;" valign="bottom"  id="" class="inv-itemtable-header inv-itemtable-breakword"></th>
													<th style="text-align: left;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Item &amp; Description</b></th>
													<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Qty</b></th>
													<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Rate</b></th>
													<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Amount</b></th>
												</tr> 
											</thead>
											<tbody class="itemBody">
											<?php $ist = 1; $subtotal = 0; ?>
											@foreach($invoicedetail->invoicedetail as $lst)
											<?php $ntotal = $lst->quantity * $lst->rate; ?>
												<tr class="breakrow-inside breakrow-after" style="height:20px;">
													<td valign="top" style="text-align: center;" class="inv-item-row">{{$ist}}</td>
													<td valign="top" class="inv-item-row" id="tmp_item_name">
														<div><span style="white-space: pre-wrap;word-wrap: break-word;" class="inv-item-desc" id="tmp_item_description">{{$lst->item_name}}</span><br></div>
													</td>
													<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_qty">{{number_format($lst->quantity,$currencydata->decimal)}}</td>
													<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_rate">{{number_format($lst->rate,$currencydata->decimal)}}</td>
													<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_amount">{{number_format($ntotal,$currencydata->decimal)}}</td>
												</tr>
												<?php 
												$subtotal += $ntotal;
												$ist++; ?>
												@endforeach
											</tbody>
										</table> 
										<?php if($invoicedetail->discount_type == 'fixed'){ 
											$discoun = $invoicedetail->discount;
											$finaltotal = $subtotal - $invoicedetail->discount;
										  }else{
											 $discoun = ($subtotal * $invoicedetail->discount) / 100; 
											 $finaltotal = $subtotal - $discoun;
											 
										  } 
											if(@$invoicedetail->tax != 0)
											{
												$cure = \App\Models\TaxRate::where('id',@$invoicedetail->tax)->first(); 
												$taxcal = ($finaltotal * $cure->rate) / 100;
												$finaltotal = $finaltotal + $taxcal;
											}
											$amount_rec = \App\Models\InvoicePayment::where('invoice_id',$invoicedetail->id)->get()->sum("amount_rec");

											$ispaymentexist = \App\Models\InvoicePayment::where('invoice_id',$invoicedetail->id)->exists();
											
										  ?>
										<div style="width: 100%;">
											<div style="width: 50%;padding: 4px 4px 3px 7px;float: left;">
												<div style="margin:10px 0 5px">
													<div style="padding-right: 10px;">Total In Words</div>
													<span><b><i>Rupees <?php echo Controller::convert_number_to_words($finaltotal); ?>  Only</i></b></span>
												</div>
												<div style="padding-top: 10px;">
													<p style="white-space: pre-wrap;word-wrap: break-word;" class="inv-notes">Thanks for your business.</p>
												</div>
											</div>
											<div style="width: 43.6%;float:right;" class="inv-totals">
												<table style="border-left: 1px solid #9e9e9e;" class="inv-totaltable" id="itemTable" cellspacing="0" border="0" width="100%">
													<tbody>
														<tr>
															<td valign="middle">Sub Total</td>
															<td id="tmp_subtotal" valign="middle" style="width:110px;">{{number_format($subtotal,$currencydata->decimal)}}</td>
														</tr>
														
														<?php 
														
														  if($invoicedetail->discount != 0){
														?>
														<tr>
															<td valign="middle">Discount(<?php if($invoicedetail->discount_type == 'fixed'){ echo $currencydata->currency_symbol; } ?>{{$invoicedetail->discount}} <?php if($invoicedetail->discount_type == 'percentage'){ echo '%'; } ?>)</td>
															<td id="tmp_total" valign="middle" style="width:110px;">(-) <?php echo $discoun; ?></td>
															</tr>
														  <?php } ?>
														  @if(@$invoicedetail->tax != 0)
															<?php
																$isex = \App\Models\TaxRate::where('id',@$invoicedetail->tax)->exists(); 
																if($isex){
																	
															?>
														<tr>
															<td valign="middle"><b>{{@$cure->name}} [{{@$cure->rate}}%]</b></td>
															<td id="tmp_total" valign="middle" style="width:110px;"><b>{{number_format($taxcal,$currencydata->decimal)}}</b></td>
														</tr>
														<?php } ?>
														@endif 
														<tr>
															<td valign="middle"><b>Total</b></td>
															<td id="tmp_total" valign="middle" style="width:110px;"><b>{{$currencydata->currency_symbol}}{{number_format($finaltotal,$currencydata->decimal)}}</b></td>
														</tr>
														
														@if($ispaymentexist)
															<?php $baldue = $finaltotal - $amount_rec; ?>
														<tr style="height:10px;">
															<td valign="middle">Payment Made</td>
															<td valign="middle" style="width:110px;color: red;">(-) {{number_format($amount_rec, $currencydata->decimal)}}</td>
														</tr>
														<tr style="height:10px;" class="inv-balance">
															<td valign="middle"><b>Balance Due</b></td>
															<td id="tmp_balance_due" valign="middle" style="width:110px;;"><strong>{{$currencydata->currency_symbol}}{{number_format($baldue, $currencydata->decimal)}}</strong></td>
														</tr>
														@endif
														<tr>
															<td style="border-bottom: 1px solid #9e9e9e;" colspan="2"></td>
														</tr>
													</tbody>
													<tbody>
														<tr>
															<td style="text-align: center;padding-top: 5px;" colspan="2">
																<div style="min-height: 75px;">

																</div>
															</td>
														</tr>
														<tr>
															<td style="text-align: center;" colspan="2">
															  <label style="margin-bottom: 0px;" class="inv-totals">Authorized Signature</label>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
											<div style="clear: both;"></div>
										</div>
									</div>
								</div>
								<div class="inv-template-footer">
									<div></div>  
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<div class="modal fade" id="payment_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title"></h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			{{ Form::open(array('url' => 'admin/invoice/paymentsave', 'name'=>"add-paymentsave", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>'updatepaymentsave')) }}
			<div class="modal-body">
				<input type="hidden" value="" name="invoice_id" id="invoice_id">
				<div class="customerror"></div>
				
					<div class="form-group row">
						<label for="subject" class="col-form-label col-sm-2">Customer Name</label>
						<div class="col-sm-10">
						<input type="text" name="" value="" disabled id="customer_name">
						</div>
					</div>
					
					<div class="form-group row">
						<label for="amount_rec" class="col-form-label col-sm-2">Amount Received (INR)</label>
						<div class="col-sm-10">
						{{ Form::text('amount_rec', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'amount_rec' )) }}
						</div>
					</div>
					<div class="form-group row">
						<label for="bank_charges" class="col-form-label col-sm-2">Bank Charges (if any)</label>
						<div class="col-sm-10">
						{{ Form::text('bank_charges', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'','id'=>'bank_charges' )) }}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-6">
						<label for="payment_date" class="col-form-label col-sm-2">Payment Date</label>
						<div class="col-sm-10">
						{{ Form::text('payment_date', date('Y-m-d'), array('class' => 'form-control commodate', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'payment_date' )) }}
						</div>
						</div>
						<div class="col-md-6">
						<label for="payment_mode" class="col-form-label col-sm-2">Payment Mode</label>
						<div class="col-sm-10">
						<select class="form-control select2bs4" name="payment_mode">
							<option value="Cash">Cash</option>
							<option value="Bank Transfer">Bank Transfer</option>
							<option value="Bank Remittance">Bank Remittance</option>
							<option value="Check">Check</option>
							<option value="Credit Card">Credit Card</option>
						</select>
						</div>
						</div>
					</div>
					
						<div class="form-group row">
							<label for="reference" class="col-form-label col-sm-2">Reference#</label>
							<div class="col-sm-10">
							{{ Form::text('reference', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'','id'=>'reference' )) }}
							</div>
						</div>
				
						<div class="form-group">
							<label for="notes" class="col-form-label col-sm-2">Notes</label>
							<div class="col-sm-10">
							<textarea name="notes" id="notes" class="form-control" placeholder="" style="width: 100%; height:80px;padding: 10px;margin-bottom:10px;"></textarea>
							</div>
						</div>
					
					
						<div class="form-group">
							<label for="email_payment" class="col-form-label"><input checked value="1" type="checkbox" name="email_payment"> Email a "thank you" note for this payment</label>
						</div>
				
			</div>
			<div class="modal-footer justify-content-between">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="button" id="payment_save" class="btn btn-primary">Save Payment</button>
				</div>
				{{ Form::close() }}
		</div>
	</div>
</div>
<div class="modal fade" id="editpaymentmodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Edit Payment</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			{{ Form::open(array('url' => 'admin/invoice/editpaymentsave', 'name'=>"add-paymentsave", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>'editpaymentsave')) }}
			<div class="modal-body">
				<input type="hidden" value="" name="payment_id" id="payment_id">
				<div class="customerror"></div>
				
					<div class="form-group row">
						<label for="subject" class="col-form-label col-sm-2">Customer Name</label>
						<div class="col-sm-10">
						<input type="text" name="" value="" disabled id="customer_name">
						</div>
					</div>
					
					<div class="form-group row">
						<label for="amount_rec" class="col-form-label col-sm-2">Amount Received (INR)</label>
						<div class="col-sm-10">
						{{ Form::text('amount_rec', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'amount_rec' )) }}
						</div>
					</div>
					<div class="form-group row">
						<label for="bank_charges" class="col-form-label col-sm-2">Bank Charges (if any)</label>
						<div class="col-sm-10">
						{{ Form::text('bank_charges', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'','id'=>'bank_charges' )) }}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-6">
						<label for="payment_date" class="col-form-label col-sm-2">Payment Date</label>
						<div class="col-sm-10">
						{{ Form::text('payment_date', date('Y-m-d'), array('class' => 'form-control commodate', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'payment_date' )) }}
						</div>
						</div>
						<div class="col-md-6">
						<label for="payment_mode" class="col-form-label col-sm-2">Payment Mode</label>
						<div class="col-sm-10">
						<select class="form-control select2bs4" id="payment_mode" name="payment_mode">
							<option value="Cash">Cash</option>
							<option value="Bank Transfer">Bank Transfer</option>
							<option value="Bank Remittance">Bank Remittance</option>
							<option value="Check">Check</option>
							<option value="Credit Card">Credit Card</option>
						</select>
						</div>
						</div>
					</div>
					
						<div class="form-group row">
							<label for="reference" class="col-form-label col-sm-2">Reference#</label>
							<div class="col-sm-10">
							{{ Form::text('reference', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'','id'=>'reference' )) }}
							</div>
						</div>
				
						<div class="form-group">
							<label for="notes" class="col-form-label col-sm-2">Notes</label>
							<div class="col-sm-10">
							<textarea name="notes" id="notes" class="form-control" placeholder="" style="width: 100%; height:80px;padding: 10px;margin-bottom:10px;"></textarea>
							</div>
						</div>
				
			</div>
			<div class="modal-footer justify-content-between">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="button" id="edit_payment_save" class="btn btn-primary">Save Payment</button>
				</div>
				{{ Form::close() }}
		</div>
	</div>
</div>
<div class="modal fade" id="commenthistorymodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Comment & History</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>

			<div class="modal-body">
				
			</div>
			
		</div>
	</div>
</div>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="pdfmodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Print Invoice</h4>
			   <button type="button" onclick="print()" class="btn btn-primary" >
				<span aria-hidden="true">Print</span>
			  </button>
			  <button type="button" class="btn btn-default closepri">
				<span aria-hidden="true">Close</span>
			  </button>
			</div>

			<div class="modal-body">
				<iframe frameborder="0" src="" style="width:100%;height:80vh;" id="myFrame" name="printframe"></iframe>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="pdfallmodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Print Invoice</h4>
			   <button type="button" onclick="print()" class="btn btn-primary" >
				<span aria-hidden="true">Print</span>
			  </button>
			  <button type="button" class="btn btn-default closepri">
				<span aria-hidden="true">Close</span>
			  </button>
			</div>

			<div class="modal-body">
				<iframe frameborder="0" src="" style="width:100%;height:80vh;" id="myFrame" name="printframe"></iframe>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="attach_filemodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Attach File</h4>
			  <button type="button" class="btn btn-default"  data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">Close</span>
			  </button>
			</div>

			<div class="modal-body">
				<div class="form-group row">
					<table id="allfiles">
					</table>
				</div>
					<div class="form-group row">
					<p>You can upload a maximum of 5 files, 5MB each</p>
					<input type="file" name="attach_file" id="attach_file" class="form-control">
					<input type="hidden" name="" id="attach_file_id" class="form-control">
				</div>
					<div class="form-group row">
					<label>Display attachment(s) emails 
					<input data-id="" class="display_attach" value="1" type="checkbox" name="is_active" data-bootstrap-switch></label>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="sharelinkmodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Share Invoice Link</h4>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>

			<div class="modal-body">
				<div class="form-group row">
					<p>Select an expiration date and generate the link to share it with your customer. Remember that anyone who has access to this link can view, print or download it.</p>
				</div>
				<div class="form-group row">
				<input type="hidden" id="invoiceid" name="invoiceid">
					<label for="expire_date" class="col-form-label col-sm-2">Link Expiration Date <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-10">
					{{ Form::text('expire_date', date('Y-m-d', strtotime('+30 days')), array('class' => 'form-control disableifgenreate commodate', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'expire_date' )) }}
					<p><i class="fa fa-info-circle"></i> By default, the link is set to expire 30 days from the invoice due date.</p>
					</div>
				</div>
				<div class="form-group row showifgenrate" style="display:none;">
					<label for="expire_date" class="col-form-label col-sm-2">Link Expiration Date <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-10">
					{{ Form::text('sharelink', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'sharelink' )) }}
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="button" id="share_linksave" class="btn btn-primary hideifshare">Generate Link</button>
				  <button type="button" id="share_linkdisable" class="btn btn-default hideifshare">Disable All Activate Links</button>
				  <button data-text="" type="button" id="copydata" style="display:none;" class="btn btn-primary copyboard showifgenrate">Copy Link</button>
			</div>
		</div>
	</div>
</div>
<?php
if(isset($_GET['print_invoice']) && $_GET['print_invoice'] == true){
?>
<script>
jQuery(document).ready(function($){
	var val = '{{base64_encode(convert_uuencode(@$invoicedetail->id))}}';
	$('#pdfmodel').modal('show');
		
		$("#pdfmodel .modal-body iframe").attr('src', site_url+'/admin/invoice/print/'+val)
});
</script>
<?php
}else if(isset($_GET['share_invoice']) && $_GET['share_invoice'] == true){
	?>
<script>
jQuery(document).ready(function($){
	var val = '{{base64_encode(convert_uuencode(@$invoicedetail->id))}}';
	$('.custom-error').remove();
		$('#sharelink').val('');
		$('.showifgenrate').hide();
		$('.hideifshare').show();
		$('.disableifgenreate').prop('disabled', false);
		$('#invoiceid').val(val);
		$('#sharelinkmodel').modal('show');
});
</script>
	<?php
}	
?>
@endsection