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
						<td style="text-align: left;">
							<img width="80" style="height:auto;display:block;" src="{{URL::to('img/')}}/logo.png" alt="Logo"/>
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;">
								BANSAL IMMIGRATION<br/>
								Level 8,278 Collins Street<br/>
								Melbourne VIC 3000<br/>
								E-mail: invoice@bansalimmigration.com.au<br/>
								Phone: 03 96021330
							</p>
						</td>
						<td style="text-align: right;">
							<h2 style="color:#3abaf4">Tax Invoice</h2>
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
								<b>ABN 70 958 120 428 </b><br/>
								<b>ISSUED:</b> {{ $record_get[0]->trans_date }}<br/>
                                <b>DUE:</b> {{ \Carbon\Carbon::createFromFormat('d/m/Y', $record_get[0]->trans_date)->addDays(15)->format('d/m/Y') }}<br/>
								<b>INVOICE NO:</b> {{$record_get[0]->invoice_no}}
							</p>
						</td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td colspan="2">
							<span style="font-size: 18px;line-height: 21px;color: #000;margin: 20px 0px 0px;display:block;"><b>Bill To:</b></span>
							<p style="font-size: 13px;line-height: 16px;color: #000;font-weight: normal;margin: 0px 0px 5px;">
								{{@$clientname->first_name}} {{@$clientname->last_name}}<br/>
								{{$clientname->address}}<br/>
								{{$clientname->state}} {{$clientname->city}} {{$clientname->zip}}<br/>
								{{$clientname->country}}
							</p>
						</td>
					</tr>
					<td colspan="3" style="padding:10px;"></td>
					<tr>
						<td colspan="3">Matter: {{ $client_matter_no }}</td>
					</tr>

					<?php
					if($record_get_Professional_Fee_cnt >0 )
					{ ?>
						<tr>
							<td colspan="3"><h4>Professional Fee</h4></td>
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
															<th>Date</th>
															<th>Description</th>
															<th>Unit Price</th>
															<th>Gst Incl.</th>
															<th>Amount</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if( !empty($record_get_Professional_Fee) ) {
                                                            foreach($record_get_Professional_Fee as $Professional_Fee) {
                                                            ?>
                                                                <tr>
                                                                    <td>{{@$Professional_Fee->trans_date}}</td>
                                                                    <td>{{@$Professional_Fee->description}}</td>
                                                                    <td>
                                                                        ${{ @$Professional_Fee->gst_included === 'Yes'
                                                                            ? number_format((float) @$Professional_Fee->withdraw_amount - ((float) @$Professional_Fee->withdraw_amount / 11), 2)
                                                                            : number_format((float) @$Professional_Fee->withdraw_amount, 2)
                                                                        }}
                                                                    </td>
                                                                    <td>{{@$Professional_Fee->gst_included}}</td>
                                                                    <td>${{number_format((float) @$Professional_Fee->withdraw_amount, 2)}}</td>
                                                                </tr>
                                                            <?php
                                                            }
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
					} ?>
					<?php
					if($record_get_Department_Charges_cnt >0 )
					{ ?>
						<tr>
							<td colspan="3"><h4>Department Charges</h4></td>
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
															<th>Date</th>
															<th>Description</th>
															<th>Unit Price</th>
															<th>Gst Incl.</th>
															<th>Amount</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if( !empty($record_get_Department_Charges) ) {
                                                            foreach($record_get_Department_Charges as $Department_Charges) {
                                                            ?>
                                                                <tr>
                                                                    <td>{{@$Department_Charges->trans_date}}</td>
                                                                    <td>{{@$Department_Charges->description}}</td>
                                                                    <td>
                                                                        ${{ @$Department_Charges->gst_included === 'Yes'
                                                                            ? number_format((float) @$Department_Charges->withdraw_amount - ((float) @$Department_Charges->withdraw_amount / 11), 2)
                                                                            : number_format((float) @$Department_Charges->withdraw_amount, 2)
                                                                        }}
                                                                    </td>
                                                                    <td>{{@$Department_Charges->gst_included}}</td>
                                                                    <td>${{number_format((float) @$Department_Charges->withdraw_amount, 2)}}</td>
                                                                </tr>
                                                            <?php
                                                            }
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
					} ?>
					<?php
					if($record_get_Surcharge_cnt >0 )
					{ ?>
						<tr>
							<td colspan="3"><h4>Surcharge</h4></td>
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
															<th>Date</th>
															<th>Description</th>
															<th>Unit Price</th>
															<th>Gst Incl.</th>
															<th>Amount</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if( !empty($record_get_Surcharge) ) {
                                                            foreach($record_get_Surcharge as $surcharge) {
                                                            ?>
                                                                <tr>
                                                                    <td>{{@$surcharge->trans_date}}</td>
                                                                    <td>{{@$surcharge->description}}</td>
                                                                    <td>
                                                                        ${{ @$surcharge->gst_included === 'Yes'
                                                                            ? number_format((float) @$surcharge->withdraw_amount - ((float) @$surcharge->withdraw_amount / 11), 2)
                                                                            : number_format((float) @$surcharge->withdraw_amount, 2)
                                                                        }}
                                                                    </td>
                                                                    <td>{{@$surcharge->gst_included}}</td>
                                                                    <td>${{number_format((float) @$surcharge->withdraw_amount, 2)}}</td>
                                                                </tr>
                                                            <?php
                                                            }
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
					} ?>
					<?php
					if($record_get_Disbursements_cnt >0 )
					{ ?>
						<tr>
							<td colspan="3"><h4>Disbursements</h4></td>
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
														    <th>Date</th>
															<th>Description</th>
															<th>Unit Price</th>
															<th>Gst Incl.</th>
															<th>Amount</th>
                                                        </tr>
													</thead>
													<tbody>
														<?php
														if( !empty($record_get_Disbursements) ) {
                                                            foreach($record_get_Disbursements as $disbursements) {
                                                            ?>
                                                                <tr>
                                                                    <td>{{@$disbursements->trans_date}}</td>
                                                                    <td>{{@$disbursements->description}}</td>
                                                                    <td>
                                                                        ${{ @$disbursements->gst_included === 'Yes'
                                                                            ? number_format((float) @$disbursements->withdraw_amount - ((float) @$disbursements->withdraw_amount / 11), 2)
                                                                            : number_format((float) @$disbursements->withdraw_amount, 2)
                                                                        }}
                                                                    </td>
                                                                    <td>{{@$disbursements->gst_included}}</td>
                                                                    <td>${{number_format((float) @$disbursements->withdraw_amount, 2)}}</td>
                                                                </tr>
                                                            <?php
                                                            }
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
					} ?>

					<?php
					if($record_get_Other_Cost_cnt >0 )
					{ ?>
						<tr>
							<td colspan="3"><h4>Other Cost</h4></td>
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
														    <th>Date</th>
															<th>Description</th>
															<th>Unit Price</th>
															<th>Gst Incl.</th>
															<th>Amount</th>
                                                        </tr>
													</thead>
													<tbody>
														<?php
														if( !empty($record_get_Other_Cost) ) {
                                                            foreach($record_get_Other_Cost as $other_Cost) {
                                                            ?>
                                                                <tr>
                                                                    <td>{{@$other_Cost->trans_date}}</td>
                                                                    <td>{{@$other_Cost->description}}</td>
                                                                    <td>
                                                                        ${{ @$other_Cost->gst_included === 'Yes'
                                                                            ? number_format((float) @$other_Cost->withdraw_amount - ((float) @$other_Cost->withdraw_amount / 11), 2)
                                                                            : number_format((float) @$other_Cost->withdraw_amount, 2)
                                                                        }}
                                                                    </td>
                                                                    <td>{{@$other_Cost->gst_included}}</td>
                                                                    <td>${{number_format((float) @$other_Cost->withdraw_amount, 2)}}</td>
                                                                </tr>
                                                            <?php
                                                            }
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
					} ?>

                    <?php
					if($record_get_Discount_cnt >0 )
					{ ?>
						<tr>
							<td colspan="3"><h4>Discount</h4></td>
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
														    <th>Date</th>
															<th>Description</th>
															<th>Unit Price</th>
															<th>Gst Incl.</th>
															<th>Amount</th>
                                                        </tr>
													</thead>
													<tbody>
														<?php
														if( !empty($record_get_Discount) ) {
                                                            foreach($record_get_Discount as $Discount) {
                                                            ?>
                                                                <tr>
                                                                    <td>{{@$Discount->trans_date}}</td>
                                                                    <td>{{@$Discount->description}}</td>
                                                                    <td>
                                                                        ${{ @$Discount->gst_included === 'Yes'
                                                                            ? number_format((float) @$Discount->withdraw_amount - ((float) @$Discount->withdraw_amount / 11), 2)
                                                                            : number_format((float) @$Discount->withdraw_amount, 2)
                                                                        }}
                                                                    </td>
                                                                    <td>{{@$Discount->gst_included}}</td>
                                                                    <td>${{number_format((float) @$Discount->withdraw_amount, 2)}}</td>
                                                                </tr>
                                                            <?php
                                                            }
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
					} ?>
					<tr><td colspan="3"></td></tr>
					<tr><td colspan="3"></td></tr>
					<tr><td colspan="3"></td></tr>
					<tr>
						<td class="inv_table" colspan="3">
							<table width="100%" border="0">
								<tfoot>
									<tr>
										<td colspan="56" style="text-align:right;">Gross Amount:</td>
										<td style="text-align:right;padding-right:25px;">${{number_format($total_Gross_Amount,2)}}</td>
									</tr>
									<tr>
										<td colspan="56" style="text-align:right;">GST:</td>
										<td style="text-align:right;padding-right:25px;">${{number_format($total_GST_amount,2)}}</td>
									</tr>
									<tr>
										<td colspan="56" style="text-align:right;">Total Invoice Amount:</td>
										<td style="text-align:right;padding-right:25px;">${{number_format($total_Invoice_Amount,2)}}</td>
									</tr>
									<tr>
										<td colspan="56" style="text-align:right;">Total Pending Amount:</td>
										<td style="text-align:right;padding-right:25px;">${{number_format($total_Pending_amount,2)}}</td>
									</tr>
								</tfoot>
							</table>
						</td>
					</tr>
					<tr><td colspan="3"></td></tr>
					<tr>
						<td colspan="3">
							<div style="padding: 20px 0; font-size: 14px; line-height: 18px; color: #000;">
								<p><b>Payment Method: {{ $invoice_payment_method }}</b></p>
								<p>IF you wish to make payment by Direct Debit, use the following details in your Electronic Funds Transfer. Please remember to quote your MATTER NO {{ $client_matter_no }} and advise us by email when you have made the transfer:</p>
								<p>For Electronic Funds Transfer, our bank account details are as follows:</p>
								<p>
									Account Name: Bansal Immigration<br/>
									BSB: 083419<br/>
									Account Number: 362421793<br/>
									Swift Code: ____________________
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>
