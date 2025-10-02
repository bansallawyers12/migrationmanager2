<!doctype html>
<html>
	<head>
	<title>Document</title>
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
						<td><img src="" title="Company Logo" alt="Company Logo"/></td>
						<td></td>
						<td style="text-align: right;">
							<span style="font-size:21px;line-height:24px;color:#000;"><b>{{$admin->company_name}}</b></span>
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;"><b>Address:</b> {{$admin->address}}<br/>
							<b>Email:</b> {{$admin->primary_email}}<br/>
							<b>Phone:</b> {{$admin->phone}}</p>
						</td>
					</tr>
					<tr>
						<td colspan="3"><hr style="border-color:#000;"/></td>
					</tr>
					
					<tr>
					<td class="inv_table" colspan="3">
							<table width="100%" >
								<tbody>
									<tr>
										<td style="padding:0px;">
											<table width="100%" border="0">
												<thead>
													<tr style="background-color:#03a9f4;color:#fff;">
														<th>{{@$cleintname->first_name}} {{@$cleintname->last_name}}</th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th>FILENOTE</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$admin = \App\Models\Admin::where('id', $applications->user_id)->first();
												?>
													<tr>
														<td><b>DOB:</b></td>
														<td>{{@$cleintname->dob}}</td>
														<td></td>
														<td><b>Assignee:</b></td>
														<td>{{@$admin->first_name}}</td>
														<td></td>
													</tr>
													<tr>
														<td><b>City:</b></td>
														<td>{{@$cleintname->city}}</td>
														<td></td>
														<td><b>Branch:</b></td>
														<td></td>
														<td></td>
													</tr>
													<tr style="background-color:rgba(0,0,0,0.04)">
														<td><b>Product:</b></td>
														<td>{{@$productdetail->name}}</td>
														<td></td>
														<td><b>Applied Date:</b></td>
														<td>{{date('Y-m-d', strtotime(@$applications->created_at))}}</td>
														<td></td>
													</tr>
													<tr style="background-color:rgba(0,0,0,0.04)">
														<td><b>Partner:</b></td>
														<td>{{@$partnerdetail->partner_name}}</td>
														<td></td>
														<td><b>Branch:</b></td>
														<td>{{@$PartnerBranch->name}}</td>
														<td></td>
													</tr>
												</tbody>
												<tfoot>
													<tr style="background-color:#03a9f4;color:#fff;">
														<td colspan="6">Stages</td>
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
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Application</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Application')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Acceptance</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Acceptance')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Payment</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Payment')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Form I 20</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Form I 20')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Visa Application</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Visa Application')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Interview</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Interview')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Enrolment</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Enrolment')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table width="100%" border="0" style="margin-top:20px;">
				<thead>
					<tr >
						<td>
							<td colspan="5" style="color:#3abaf4 ;">Course Ongoing</td>
						</td>
					</tr>
					<tr>
						<td colspan="5"><hr style="border-color:#3abaf4;"/></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $applications->id)->where('stage','Course Ongoing')->orderby('created_at', 'DESC')->get();
					foreach($applicationlists as $applicationlist){ 
					$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
					?>
						<tr style="font-size:12px;line-height:18px;margin-top:50px;">
							<td colspan="5">
							<b ><?php if (strpos($applicationlist->comment, 'moved the stage') !== false) { ?>Application Stage Moved <?php }else if (strpos($applicationlist->comment, 'sent an email') !== false) { echo 'Application Mail sent'; }else if (strpos($applicationlist->comment, 'added a note') !== false) { echo 'Application Note added'; } ?></b><br>
							<span >{{$admin->first_name}} {!! $applicationlist->comment !!}</span></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</body>
</html>
