<?php
$productdetail = \App\Models\Product::where('id', $fetchData->product_id)->first();
$partnerdetail = \App\Models\Partner::where('id', $fetchData->partner_id)->first();
$PartnerBranch = \App\Models\PartnerBranch::where('id', $fetchData->branch)->first();
$workflow = \App\Models\Workflow::where('id', $fetchData->workflow)->first();
?>
<div class="card-header-action" style="padding-bottom:15px;">
	<div class="float-left">
		<h5><?php if($fetchData->status == 0){ ?>In Progress<?php } ?></h5> 
	</div>
	<div class="float-right">
		<div class="application_btns">
			<a href="javascript:;" class="btn btn-primary"><i class="fa fa-print"></i></a>
			<a href="javascript:;" class="btn btn-outline-danger"><i class="fa fa-times"></i> Discontinue</a>
			<a href="javascript:;" data-stage="{{$fetchData->stage}}" data-id="{{$fetchData->id}}" class="btn btn-outline-primary backstage"><i class="fa fa-angle-left"></i> Back to Previous Stage</a>
			<a href="javascript:;" data-stage="{{$fetchData->stage}}" data-id="{{$fetchData->id}}" class="btn btn-success nextstage">Proceed to Next Stage <i class="fa fa-angle-right"></i></a>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="application_grid_col">
	<div class="grid_column">
		<span>Course:</span>
		<p>{{$productdetail->name}}</p>
	</div>
	<div class="grid_column">
		<span>School:</span>
		<p>{{$partnerdetail->partner_name}}</p>
	</div>
	<div class="grid_column">
		<span>Branch:</span>
		<p>{{$PartnerBranch->name}}</p>
	</div>
	<div class="grid_column">
		<span>Workflow:</span>
		<p>{{$workflow->name}}</p>
	</div>
	<div class="grid_column">
		<span>Current Stage:</span>
		<p class="text-success curerentstage" style="font-weight: 600;font-size: 14px;">{{$fetchData->stage}}</p>
	</div>
	<div class="grid_column">
		<span>Application Id:</span>
		<p>{{$fetchData->id}}</p>
	</div>
	<!--<div class="grid_column">
		<span>Partner's Client Id:</span>
		<p>22</p>
	</div>-->
	<div class="grid_column">
		<span>Started at:</span>
		<p>{{date('Y-m-d', strtotime($fetchData->created_at))}}</p>
	</div>
	<div class="grid_column">
		<span>Last Updated:</span>
		<p>{{date('Y-m-d', strtotime($fetchData->updated_at))}}</p>
	</div>
	<div class="grid_column">
		<div class="overall_progress">
			<span>Overall Progress:</span>
			<div class="progress-circle over_ prgs_25">
			   <span>10%</span>
			   <div class="left-half-clipper"> 
				  <div class="first50-bar"></div>
				  <div class="value-bar"></div>
			   </div>
			</div>
		</div>
	</div>
	<div class="grid_column last_grid_column">
		<!--<div class="view_other_detail">
			<a href="#" class="btn btn-outline-primary">View Other Details</a>
		</div>-->
	</div>
</div>
<div class="clearfix"></div>
<div class="divider"></div>
<div class="row">
	<div class="col-md-9">
		<div class="application_other_info">
			<ul class="nav nav-pills" id="applicat_detail_tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="applicate_activities-tab" data-toggle="tab" href="#applicate_activities">Activities</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents">Documents</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="notes-tab" data-id="{{$fetchData->id}}" data-toggle="tab"  href="#notes">Notes</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks">Tasks</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="paymentschedule-tab" data-toggle="tab" href="#paymentschedule">Payment Schedule</a>
				</li>
			</ul> 
			<div class="tab-content" id="applicationContent">
				<div class="tab-pane fade show active" id="applicate_activities" role="tabpanel" aria-labelledby="applicate_activities-tab">
					<div id="accordion">
					<?php
						if($fetchData->stage == 'Application'){
							$stage1 = '';
							$curstage1 = 'app_blue';
						}else if($fetchData->stage == 'Acceptance'){
							$stage2 = 'app_green';
							$curstage2 = 'app_blue';
						}else if($fetchData->stage == 'Payment'){
							$stage3 = 'app_green';
							$stage2 = 'app_green';
							$curstage3 = 'app_blue';
						}else if($fetchData->stage == 'Form I 20'){
							$stage4 = 'app_green';
							$stage3 = 'app_green';
							$stage2 = 'app_green';
							$curstage4 = 'app_blue';
						}else if($fetchData->stage == 'Visa Application'){
							$stage5 = 'app_green';
							$stage4 = 'app_green';
							$stage3 = 'app_green';
							$stage2 = 'app_green';
							$curstage5 = 'app_blue';
						}else if($fetchData->stage == 'Interview'){
							$stage6 = 'app_green';
							$stage5 = 'app_green';
							$stage4 = 'app_green';
							$stage3 = 'app_green';
							$stage2 = 'app_green';
							$curstage6 = 'app_blue';
						}else if($fetchData->stage == 'Enrolment'){
							$stage7 = 'app_green';
							$stage6 = 'app_green';
							$stage5 = 'app_green';
							$stage4 = 'app_green';
							$stage3 = 'app_green';
							$stage2 = 'app_green';
							$curstage7 = 'app_blue';
						}else if($fetchData->stage == 'Enrolment'){
							$stage8 = 'app_green';
							$stage7 = 'app_green';
							$stage6 = 'app_green';
							$stage5 = 'app_green';
							$stage4 = 'app_green';
							$stage3 = 'app_green';
							$stage2 = 'app_green';
							$curstage8 = 'app_blue';
						}
						if($fetchData->status == 1){
							$stage9 = 'app_green';
						}
					?>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage2; ?> <?php echo @$curstage1; ?>" role="button" data-toggle="collapse" data-target="#application_accor" aria-expanded="false">
								<h4>Application</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Application" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Application" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Application" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<?php
							$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Application')->orderby('created_at', 'DESC')->get();
							
							?>
							<div class="accordion-body collapse" id="application_accor" data-parent="#accordion" style="">
								<div class="activity_list">
								<?php foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description"> 
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>	
										<?php } ?> 
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage3; ?> <?php echo @$curstage2; ?>" role="button" data-toggle="collapse" data-target="#acceptance">
								<h4>Acceptance</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Acceptance" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Acceptance" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Acceptance" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="acceptance" data-parent="#accordion">
								<div class="activity_list">
								<?php
							$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Acceptance')->orderby('created_at', 'DESC')->get();
							
							 foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?> 
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage4; ?> <?php echo @$curstage3; ?>" role="button" data-toggle="collapse" data-target="#payment">
								<h4>Payment</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Payment" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Payment" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Payment" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="payment" data-parent="#accordion">
								<div class="activity_list">
									<?php 
									$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Payment')->orderby('created_at', 'DESC')->get();
									foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>	
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage5; ?> <?php echo @$curstage4; ?>" role="button" data-toggle="collapse" data-target="#formi20">
								<h4>Form I 20</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Form I 20" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Form I 20" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Form I 20" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="formi20" data-parent="#accordion">
								<div class="activity_list">
									<?php 
									$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Form I 20')->orderby('created_at', 'DESC')->get();
									foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>	
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div> 
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage6; ?> <?php echo @$curstage5; ?>" role="button" data-toggle="collapse" data-target="#visa_application">
								<h4>Visa Application</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Visa Application" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Visa Application" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Visa Application" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="visa_application" data-parent="#accordion">
								<div class="activity_list">
									<?php 
									$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Visa Application')->orderby('created_at', 'DESC')->get();
									foreach($applicationlists as $applicationlist){ 
									$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>	
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage7; ?> <?php echo @$curstage6; ?>" role="button" data-toggle="collapse" data-target="#interview">
								<h4>Interview</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Interview" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Interview" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Interview" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="interview" data-parent="#accordion">
								<div class="activity_list">
									<?php 
									$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Interview')->orderby('created_at', 'DESC')->get();
									foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>	
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage8; ?> <?php echo @$curstage7; ?>" role="button" data-toggle="collapse" data-target="#enrolment">
								<h4>Enrolment</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Enrolment" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Enrolment" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Enrolment" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="enrolment" data-parent="#accordion">
								<div class="activity_list">
									<?php 
									$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Enrolment')->orderby('created_at', 'DESC')->get();
									foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>	 
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed <?php echo @$stage9; ?> <?php echo @$curstage8; ?>" role="button" data-toggle="collapse" data-target="#course_ongoing">
								<h4>Course Ongoing</h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="Course Ongoing" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									
									<a data-app-type="Course Ongoing" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="Course Ongoing" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<div class="accordion-body collapse" id="course_ongoing" data-parent="#accordion">
								<div class="activity_list">
									<?php 
									$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage','Course Ongoing')->orderby('created_at', 'DESC')->get();
									foreach($applicationlists as $applicationlist){ 
								$admin = \App\Models\Admin::where('id',$applicationlist->user_id)->first();
								
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>	
										<?php if($applicationlist->title != ''){ ?>	
										<div class="app_description">
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div> 
				</div> 
				<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
					<div class="document_checklist">
						<h4>Document Checklist (0/0)</h4>
						<p>The changes & addition of the checklist will only be affected to current application only.</p>
						<div class="checklist">
							<ul>
								<li><span>Application</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Acceptance</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Payment</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Form I 20</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Visa Application</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Interview</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Enrolment</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
								<li><span>Course Ongoing</span><a class="openchecklist" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
							</ul> 
						</div>
					</div>
					<div class="table-responsive"> 
						<table class="table text_wrap">
							<thead>
								<tr>
									<th>Filename / Checklist</th>
									<th>Related Stage</th>
									<th>Added By</th>
									<th>Added On</th>
								</tr> 
							</thead>
							<tbody class="tdata">	
								<tr id="">
									<td></td> 
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
							<!--<tbody>
								<tr>
									<td style="text-align:center;" colspan="10">
										No Record found
									</td>
								</tr>
							</tbody>-->
						</table> 
					</div>
				</div>
				<div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
				</div>
				<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
					<div id="taskaccordion">
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed active" role="button" data-toggle="collapse" data-target="#application_accor" aria-expanded="false">
								<h4>Application</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="application_accor" data-parent="#taskaccordion" style="">
							</div>-->
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#acceptance">
								<h4>Acceptance</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="acceptance" data-parent="#taskaccordion">
							</div>-->
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#payment">
								<h4>Payment</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="payment" data-parent="#accordion">
							</div>-->
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#formi20">
								<h4>Form I 20</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="formi20" data-parent="#accordion">
							</div>-->
						</div> 
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#visa_application">
								<h4>Visa Application</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="visa_application" data-parent="#accordion">
							</div>-->
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#interview">
								<h4>Interview</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="interview" data-parent="#accordion">
							</div>-->
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#enrolment">
								<h4>Enrolment</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="enrolment" data-parent="#accordion">
							</div>-->
						</div>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#course_ongoing">
								<h4>Course Ongoing</h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="course_ongoing" data-parent="#accordion">
							</div>-->
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="paymentschedule" role="tabpanel" aria-labelledby="paymentschedule-tab">
					<div class="row">
						<div class="col-md-6">
							<div class="schedule_box">
								<div class="schedule_col">
									<span>Scheduled</span>
									<h4>0.00</h4>
								</div>
								<div class="schedule_col">
									<span>Invoiced</span>
									<h4>0.00</h4>
								</div>
								<div class="schedule_col">
									<span>Pending</span>
									<h4>0.00</h4>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="schedule_btns">
								<a class="openpaymentschedule" href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Schedule</a>
								<div class="dropdown d-inline">
									<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Schedule</button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="javascript:;">Email Schedule</a>
										<a class="dropdown-item" href="javascript:;">Preview Schedule</a>
									</div> 
								</div>
							</div>
						</div>
					</div> 
					<div class="table-responsive"> 
						<table class="table text_wrap">
							<thead>
								<tr>
									<th>ID</th>
									<th>Installment</th>
									<th>Fee Type</th>
									<th>Fee</th>
									<th>Total Fees</th>
									<th>Discounts</th>
									<th>Invoicing</th>
									<th>Status</th>
									<th></th>
								</tr> 
							</thead>
							<tbody class="tdata">	
								<tr id="">
									<td>12</td> 
									<td>43556</td>
									<td>Bank</td>
									<td>45453</td>
									<td>45453</td>
									<td>1300</td>
									<td>45453</td>
									<td>Pending</td>
									<td>
										<div class="dropdown d-inline">
											<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="javascript:;">Edit</a>
												<a class="dropdown-item" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')">Delete</a>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
							<!--<tbody>
								<tr>
									<td style="text-align:center;" colspan="10">
										No Record found
									</td>
								</tr>
							</tbody>-->
						</table> 
					</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="cus_sidebar">
			<div class="form-group">
				<label for="applied_intake">Applied Intake:</label>
				{{ Form::text('applied_intake', $fetchData->date, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
				<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
				@if ($errors->has('applied_intake'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('applied_intake') }}</strong>
					</span> 
				@endif 
			</div>
			
			<div class="app_date_sec">
				<div class="app_start_date common_apply_date">
					<span>Start</span>
					<div class="date_col">
						<div class="add_date">
							<span><i class="fa fa-plus"></i> Add</span>
						</div>  
						<input type="text" class="datepicker" />
						<div class="apply_val">
							<span class="month">Mar</span>
							<span class="day">31</span>
							<span class="year">2022</span>
						</div>
					</div>
				</div>
				<div class="app_end_date common_apply_date">
					<span>End</span>
					<div class="date_col">
						<div class="add_date">
							<span><i class="fa fa-plus"></i> Add</span> 
						</div>
						<input type="text" class="datepicker" />
						<div class="apply_val">
							<span class="month">Jun</span>
							<span class="day">24</span>
							<span class="year">2022</span>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="divider"></div>
			<div class="setup_payment_sche">
				<a href="#" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Setup Payment Schedule</a>
			</div>
			<div class="divider"></div>
			<div class="cus_prod_fees">
				<h5>Product Fees <span>USD</span></h5>
				<a href="#"><i class="fa fa-edit"></i></a>
				<div class="clearfix"></div>
			</div>
			<p class="clearfix"> 
				<span class="float-left">Total Fee</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<p class="clearfix" style="color:#ff0000;"> 
				<span class="float-left">Discount</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<p class="clearfix" style="color:#6777ef;"> 
				<span class="float-left">Net Fee</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<div class="divider"></div>
			<div class="cus_prod_fees">
				<h5>Sales Forecast <span>USD</span></h5>
				<a href="#"><i class="fa fa-edit"></i></a>
				<div class="clearfix"></div>
			</div>
			<p class="clearfix"> 
				<span class="float-left">Partner Revenue</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<p class="clearfix"> 
				<span class="float-left">Client Revenue</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<p class="clearfix" style="color:#ff0000;"> 
				<span class="float-left">Discount</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<p class="clearfix" style="color:#6777ef;"> 
				<span class="float-left">Net Revenue</span>
				<span class="float-right text-muted">0.00</span>
			</p>
			<div class="form-group">
				<label for="expect_win_date">Expected Win Date:</label>
				{{ Form::text('expect_win_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
				<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
				@if ($errors->has('expect_win_date'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('expect_win_date') }}</strong>
					</span> 
				@endif 
			</div>
			<div class="divider"></div>
			<div class="setup_payment_sche">
				<a href="#" class="btn btn-primary">View Appliation Ownership Ratio</a>
			</div>
			<div class="divider"></div> 
			<div class="client_added client_info_tags"> 
				<span class="">Started By:</span>
				<div class="client_info">
					<div class="cl_logo">A</div>
					<div class="cl_name">
						<span class="name">Arun</span>
						<span class="email">arun@gmail.com</span>
					</div>
				</div> 
			</div>
			<div class="client_assign client_info_tags"> 
				<span class="">Assignee:</span>
				<div class="client_info">
					<div class="cl_logo">A</div>
					<div class="cl_name">
						<span class="name">Arun</span>
						<span class="email">arun@gmail.com</span>
					</div>
				</div>
			</div>
			<div class="divider"></div> 
			<p class="clearfix"> 
				<span class="float-left">Super Agent:</span>
				<span class="float-right text-muted">
					<a href="javascript:;" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add</a>
				</span>
			</p>
			<p class="clearfix"> 
				<span class="float-left">Sub Agent:</span>
				<span class="float-right text-muted">
					<a href="javascript:;" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add</a>
				</span>
			</p>
		</div> 
	</div>
</div>