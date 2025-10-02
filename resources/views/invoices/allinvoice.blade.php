<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php use App\Http\Controllers\Controller; ?>
<style media="all">
	body{font-family:Arial;}
	.invoice_template .inv-template-bodysection {border: 1px solid #9e9e9e;}
	.invoice_template .inv-template {padding: 30px 20px;}
	.invoice_template .inv-template .inv-entity-title{font-size: 28px;color: #000;line-height: 32px;}
	table.invoice-detailstable tr th, table.invoice-detailstable tr td {padding: 2px 5px;}
	table.invoice-detailstable tr td, .invoice_template table.inv-itemtable tr td {font-size: 14px; line-height: 18px;}
	table.inv-addresstable tr td{font-size:14px;line-height:21px;color:#000;}
	.invoice_template table.inv-itemtable tr th, .invoice_template table.inv-itemtable tr td {
    padding: 7px;}
	.invoice_template table.inv-totaltable tr td{font-size:16px;line-height:24px;padding-bottom: 5px;padding-left: 10px;}
</style>
</head>
<body class="invoice_template">
	<div class="inv-template"> 
	<?php 
		for($i=0; $i<count($explode);$i++){
		$invoicedetail = \App\Models\Invoice::where('id', '=', $explode[$i])->where('user_id', '=', Auth::user()->id)->with(['company'])->first(); 
		?>
		<div class="inv-template-body" style="<?php if($i==0){ }else{ ?>page-break-before: always;<?php } ?>">
			<div class="inv-template-bodysection" style="margin-top:20px;">
				<table style="width: 100%;border-bottom:1px solid #000;" class="invoice-detailstable">
					<tbody>
						<tr>
							<td style="width:50%;padding: 2px 10px;vertical-align: middle;">
							  <div>
								<span style="font-weight: bold;" class="inv-orgname">{{@$invoicedetail->company->company_name}}<br></span>
								<span style="white-space: pre-wrap;font-size: 14px;line-height:21px;" id="tmp_org_address">{{@$invoicedetail->company->address}} <br>{{@$invoicedetail->company->city}} {{@$invoicedetail->company->state}} {{@$invoicedetail->company->zip}} <br/>{{@$invoicedetail->company->country}}</span>
							  </div>
							</td>
							<td style="width:40%;padding: 5px;vertical-align: bottom;" align="right">
								<div class="inv-entity-title">TAX INVOICE</div>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="width: 100%;border-bottom:1px solid #000;">
					<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;table-layout: fixed;word-wrap: break-word;" class="invoice-detailstable">
						<thead>
							<tr>
								<th style="width: 50%"></th>
								<th style="width: 50%"></th>
							</tr>
						</thead>
						<tbody>  
							<tr>
								<td style="border-right: 1px solid #9e9e9e;padding-bottom: 10px;width: 50%">
									<div style="display:table;width:100%;">
										<div style="display:table-row;">
											<div style="width: 50%;display:table-cell;" class="inv-label">#</div>
											<div style="font-weight: 600;width: 50%;display:table-cell;" id="tmp_entity_number">: {{$invoicedetail->invoice}}</div>
										</div>	
										<div style="display:table-row;">
											<div style="width: 50%;display:table-cell;" class="inv-label">Invoice Date</div>
											<div style="font-weight: 600;width: 50%;display:table-cell;" id="tmp_entity_date">: {{date('d/m/Y',strtotime($invoicedetail->invoice_date))}}</div>
										</div>	
										<div style="display:table-row;">	
											<div style="width: 50%;display:table-cell;" class="inv-label">Terms</div>
											<div style="font-weight: 600;width: 50%;display:table-cell;" id="tmp_payment_terms">: {{$invoicedetail->terms}}</div>
										</div>	
										<div style="display:table-row;">	
											<div style="width: 50%;display:table-cell;" class="inv-label">Due Date</div>
											<div style="font-weight: 600;width: 50%;display:table-cell;" id="tmp_due_date">: {{date('d/m/Y',strtotime($invoicedetail->due_date))}}</div>
										</div>	
									</div> 
								</td>    
								<td style="padding-bottom: 10px;width: 50%">
								</td>
							</tr> 
						</tbody>
					</table>
				</div>
				<div style="clear:both;"></div>
				<div style="background:#f3f3f3;padding:10px 5px;">
					<table style="" class="inv-addresstable" border="0" cellspacing="0" cellpadding="0">
						<thead style="text-align: left;">
							<tr>
								<th style=""><label style="margin-bottom: 10px;display: block;" id="tmp_billing_address_label" class="inv-label"><b>Bill To:</b></label></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="" valign="top">
									<span style="white-space: pre-wrap;line-height: 15px;color:#0080ec;" id="tmp_billing_address"><strong><span class="inv-customer-name" id="zb-pdf-customer-detail"><a style="color:#0080ec;" href="#">{{@$invoicedetail->customer->company_name}}</a></span></strong></span>
								</td>
							</tr>	
							<tr>
								<td valign="top">
									<span id="tmp_billing_address"><span class="inv-customer-name" id="zb-pdf-customer-detail">{{@$invoicedetail->customer->first_name}} {{@$invoicedetail->customer->last_name}}</span></span>
								</td>
							</tr>	
							<tr>
								<td valign="top">
									<span id="tmp_billing_address"><span class="inv-customer-name" id="zb-pdf-customer-detail">
									<span style="display:block;">{{@$invoicedetail->customer->address}}</span>
									<span style="display:block;">{{@$invoicedetail->customer->city}}</span>
									<span style="display:block;">{{@$invoicedetail->customer->zip}}</span>
									<span style="display:block;">{{@$invoicedetail->customer->country}}</span></span></span>
								</td>        
							</tr>
						</tbody>
					</table>
				</div>	
				<div style="clear:both;"></div>
				<?php $currencydata = \App\Models\Currency::where('id',$invoicedetail->currency_id)->first(); ?>
				<table style="width: 100%;table-layout:fixed;clear: both;" class="inv-itemtable" id="itemTable" cellspacing="0" cellpadding="0" border="1">
					<thead>
						  <tr>
							<th style="text-align: center;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword">#</th>
							<th style="text-align: left;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Item &amp; Description</b>
							</th>
							<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Qty</b></th>
							<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Rate</b></th>
							<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Amount</b></th>
						</tr>
					</thead>
					<tbody class="itemBody">
						<?php $ist = 1; $subtotal = 0; ?>
						@foreach($invoicedetail->invoicedetail as $lst)
						<?php $ntotal = $lst->quantity * $lst->rate; ?>
						<tr class="breakrow-inside breakrow-after">
							<td valign="top" style="text-align: center;" class="inv-item-row">{{$ist}}</td>
							<td valign="top" class="inv-item-row" id="tmp_item_name">
								<div><span style="white-space: pre-wrap;word-wrap: break-word;" class="inv-item-desc" id="tmp_item_description">{{$lst->item_name}}</span><br></div>
							</td>
							<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_qty">{{number_format($lst->quantity,$currencydata->decimal)}} </td>
							<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_rate">{{number_format($lst->rate,$currencydata->decimal)}}</td>
							<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_amount">{{number_format($ntotal,$currencydata->decimal)}}</td>
						</tr>
						<?php 
						$subtotal += $ntotal;
						$ist++; ?>
						@endforeach
					</tbody>
				</table>
				<?php 
					if($invoicedetail->discount_type == 'fixed'){ 
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
						  <div style="padding-right: 10px;margin-bottom:10px;">Total In Words</div>
						  <span><b><i>Rupees <?php echo Controller::convert_number_to_words($finaltotal); ?>  Only</i></b></span>
						</div>
						  <div style="padding-top: 10px;">
							<p style="white-space: pre-wrap;word-wrap: break-word;margin:0px;" class="inv-notes">Thanks for your business.</p>
						  </div> 
					</div>
					<div style="width: 43.6%;float:right;padding:10px 0px;border-left: 1px solid #9e9e9e;" class="inv-totals">
						<table class="inv-totaltable" id="itemTable" cellspacing="0" cellspacing="0" border="0" width="100%">
							<tbody>
								<tr>
									<td valign="middle">Sub Total</td>
									<td id="tmp_subtotal" valign="middle" style="width:110px;">{{number_format($subtotal,$currencydata->decimal)}}</td>
								</tr>  
							<?php if($invoicedetail->discount != 0){ ?>
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
							</tbody>
						</table>	
						<table width="100%" style="border-top: 1px solid #9e9e9e;">	
							<tbody> 
								<tr>
									<td style="text-align: center;padding-top: 5px;" colspan="2">
										<div style="height: 75px;"> </div>
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
		<?php } ?>
	</div>
 </body>
</html>