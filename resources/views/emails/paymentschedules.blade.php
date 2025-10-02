<!doctype html>
<html>
	<head>
		<style>
			body{font-family: 'Open Sans', sans-serif;margin:0px;}
			.inv_table{vertical-align: top;}
			.invoice_table table{border-collapse: collapse;}
			.inv_table table thead{background:#eee;}
			.inv_table table thead tr th{padding:10px;text-align:left;}
			.inv_table table tbody tr td{padding:8px;}
			.inv_table table thead tr th, .inv_table table tbody tr td{font-size:14px;line-height: 18px;vertical-align: top;}
			.inv_table table tbody tr.total_val td{background:#ccc;padding: 20px 10px;}
			
		</style>
	</head>
	<body>
	<?php
	$admin = \App\Models\Admin::where('role',1)->first();
	
	?>
		<div class="invoice_table" style="padding: 10px;">
			<table width="100%" border="0">
				<tbody>
					<tr>
						<td><img src="" alt="Company Logo"/></td>
						<td>
							<span style="font-size:21px;line-height:24px;color:#000;"><b>{{$admin->company_name}}</b></span>
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;"><b>Address:</b> {{$admin->address}}<br/>
							<b>Email:</b> {{$admin->primary_email}}<br/>
							<b>Phone:</b> {{$admin->phone}}</p>
						</td>
						<td style="text-align: right;">
							<h2 style="color:#3abaf4">PAYMENT SCHEDULE</h2>
						</td>
					</tr>
					<tr>
						<td colspan="3"><hr style="border-color:#000;"/></td>
					</tr>
					<tr>
						<td colspan="2">
							<span style="font-size: 18px;line-height: 21px;color: #000;margin: 0px 0px 20px;display:block;"><b>Client Detail</b></span>
							<p style="font-size: 13px;line-height: 16px;color: #000;font-weight: normal;margin: 10px 0px 5px;"><b>{{@$cleintname->first_name}} {{@$cleintname->last_name}}</b></p>
							<p style="font-size: 14px;line-height: 21px;color: #000;font-weight: bold;margin: 0px;">{{@$cleintname->email}}</p>
							
						</td>
						<td>
							<span style="font-size: 18px;line-height: 21px;color: #000;margin: 0px 0px 20px;display:block;"><b>Application Details</b></span>
							<p style="font-size: 13px;line-height: 16px;color: #000;font-weight: normal;margin: 10px 0px 5px;"><b><?php echo @$productdetail->name;?></b></p>
							<p style="font-size: 13px;line-height: 16px;color: #000;font-weight: normal;margin: 0px;"><b><?php echo @$cleintname->partner_name;?></b></p>
							<p style="font-size: 13px;line-height: 16px;color: #000;font-weight: normal;margin: 0px;"><b><?php echo @$PartnerBranch->name;?></b></p>
							
						</td>
					</tr>
					<td colspan="3" style="padding:10px;"><hr></td>
					<tr>
					<td colspan="3"><h4>Payment Details</h4></td>
					</tr>
					<tr>
						
						<td class="inv_table" colspan="3">
							<table width="100%" border="1">
								<tbody>
									<tr>
										<td style="padding:0px;">
											<table width="100%" border="0">
												<thead>
													<tr>
														<th>DETAILS</th>
														<th>FEE TYPE</th>
														<th>AMT (AUD) </th>
														<th>TOTAL</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$nettotal = 0;
													foreach($invoiceschedules as $invoiceschedule){
														?>
														<tr >
															<td>
																<div style="">
																	<span style="" class="">{{@$invoiceschedule->installment_name}}</span><br>
																	<span style="line-height: 16px;" class="">{{@$invoiceschedule->installment_date}}</span>
																</div>
															</td>
															<td >
																<div style="">
																<?php
																foreach($scheduleitem as $scheduleite){
																	?>
																	<span style="line-height: 23px;" class="">{{@$scheduleite->fee_type}}</span><br>
																	<?php
																}
																?>
																</div>
															</td>
															<td>
																<?php
																$totlfee = 0;
																foreach($scheduleitem as $scheduleite){
																	$nettotal += $scheduleite->fee_amount;
																	$totlfee += $scheduleite->fee_amount;
																	?>
																	<span style="line-height: 23px;" class="">{{@$scheduleite->fee_amount}}</span><br>
																	<?php
																}
																?>
															</td>
															<td>{{$totlfee}}</td>
														</tr>
														<?php
													}
													?>
													
												</tbody>
												<tfoot>
													<tr style="background-color:rgba(0,0,0,0.04)">
														<td colspan="3"><b>Grand Total</b></td>
														<td ><b>AUD<?php echo @$nettotal; ?></b></td>
													</tr>
												</tfoot>
											</table>
										</td>										
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					
				</tbody>
			</table>
		</div>
	</body>
</html>
