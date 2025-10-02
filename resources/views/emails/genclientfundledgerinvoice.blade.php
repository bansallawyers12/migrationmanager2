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
							<h2 style="color:#3abaf4">Client Fund Ledger</h2>
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
								<b>ABN 70 958 120 428 </b><br/>
								<b>ISSUED:</b> {{date('d/m/Y')}}<br/>
								<b>DUE:</b> {{date('d/m/Y')}}<br/>
								<b>Reference:</b> {{$record_get->invoice_no}}
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
						<td colspan="3">Matter: {{@$clientname->first_name}} {{@$clientname->last_name}}</td>
					</tr>
					<tr>
						<td colspan="3"><h4>Reference - {{$record_get->invoice_no}}</h4></td>
					</tr>

                    <tr>
                        <td colspan="3"><h4>Client Fund Ledger</h4></td>
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
                                                        <th>Trans. Date</th>
                                                        <th>Entry Date</th>
                                                        <th>Type</th>
                                                        <th>Description</th>
                                                        <th>Funds In (+)</th>
                                                        <th>Funds Out (-)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if( $record_get){ ?>
                                                        <tr>
                                                            <td>{{@$record_get->trans_date}}</td>
                                                            <td>{{@$record_get->entry_date}}</td>
                                                            <td>{{@$record_get->client_fund_ledger_type}}</td>
                                                            <td>
                                                                {{@$record_get->description}}
                                                                @if(!empty($record_get->invoice_no))
                                                                    ({{ @$record_get->invoice_no }})
                                                                @endif
                                                            </td>
                                                            <td>${{number_format($record_get->deposit_amount,2)}}</td>
                                                            <td>${{number_format($record_get->withdraw_amount,2)}}</td>
                                                        </tr>
                                                    <?php
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>




					<tr><td colspan="3"></td></tr>
					<tr><td colspan="3"></td></tr>
					<tr><td colspan="3"></td></tr>

					<tr><td colspan="3"></td></tr>
					<tr>
						<td colspan="3">
							<div style="padding: 20px 0; font-size: 14px; line-height: 18px; color: #000;">
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
