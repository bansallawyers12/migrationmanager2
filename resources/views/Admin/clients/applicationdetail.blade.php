<?php

$workflow = \App\Models\Workflow::where('id', $fetchData->workflow)->first();
?>
<style>
.checklist .round{background: #fff;border: 1px solid #000; border-radius: 50%;font-size: 10px;line-height: 14px; padding: 2px 5px;width: 16px; height: 16px; display: inline-block;}
.circular-box {height: 24px;width: 24px;line-height: 24px;display: inline-block; text-align: center; box-shadow: 0 4px 6px 0 rgb(34 36 38 / 12%), 0 2px 12px 0 rgb(34 36 38 / 15%);}
.circular-box{background: #fff;border: 1px solid #d2d2d2;border-radius: 50%;}
.transparent-button { background-color: transparent;border: none;cursor: pointer;}
.checklist span.check, .mychecklistdocdata span.check{background: #71cc53;color: #fff;border-radius: 50%;font-size: 10px;line-height: 14px;padding: 2px 3px;width: 18px;height: 18px;display: inline-block;}

.accordion .accordion-header.app_green {
    background-color: #54b24b;
    color: #fff;
}
.accordion .accordion-header.app_blue {
    background-color: rgba(3, 169, 244, .1);
    color: #03a9f4;
}
</style>
<div class="card-header-action" style="padding-bottom:15px;">
	<div class="float-left">
		<h5 class="applicationstatus">
            <?php
            if($fetchData->status == 0){ ?>In Progress<?php }
            else if($fetchData->status == 1){ echo 'Completed'; }
            else if($fetchData->status == 2){ echo 'Discontinued'; }
            else if($fetchData->status == 3){ echo 'Cancelled'; }
            else if($fetchData->status == 4){ echo 'Withdrawn'; }
            else if($fetchData->status == 5){ echo 'Deferred'; }
            else if($fetchData->status == 6){ echo 'Future'; }
            else if($fetchData->status == 7){ echo 'VOE'; }
            else if($fetchData->status == 8){ echo 'Refund'; }
            ?>
        </h5>
	</div>
	<div class="float-right">
		<div class="application_btns">
			<a target="_blank" href="{{URL::to('/admin/application/export/pdf/')}}/{{$fetchData->id}}" class="btn btn-primary"><i class="fa fa-print"></i></a>
          
			<a style="<?php if($fetchData->status == 2 || $fetchData->status == 1 || $fetchData->status == 8){ echo 'display:none;'; } ?>" href="javascript:;" data-id="{{$fetchData->id}}" class="btn btn-outline-danger discon_application ifdiscont"><i class="fa fa-times"></i> Discontinue</a>

            <a style="<?php if($fetchData->status == 2 || $fetchData->status == 1 || $fetchData->status == 8){ echo 'display:none;'; } ?>" href="javascript:;" data-id="{{$fetchData->id}}" class="btn btn-outline-danger refund_application ifdiscont"><i class="fa fa-undo"></i> Refund</a>

			<?php
			$displayback = false;
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();
		
			if($workflowstage->name == $fetchData->stage){
				$displayback = true;
			} 
			?>
			<a href="javascript:;" style="<?php if($fetchData->status == 2 || $fetchData->status == 1 || $fetchData->status == 8){ echo 'display:none;'; } ?>" data-stage="{{$fetchData->stage}}" data-id="{{$fetchData->id}}" class="btn btn-outline-primary backstage ifdiscont"><i class="fa fa-angle-left"></i> Back to Previous Stage</a>

			<a href="javascript:;" style="<?php if($fetchData->status == 2){ echo 'display:none;'; } ?> <?php if($displayback) { } else { echo 'display:none;'; } ?>" data-stage="{{$fetchData->stage}}" data-id="{{$fetchData->id}}" class="btn btn-success completestage ifdiscont">Complete Application</a>
			<a href="javascript:;" style="<?php if($displayback || $fetchData->status == 2 || $fetchData->status == 8){ echo 'display:none;'; } ?>" data-stage="{{$fetchData->stage}}" data-id="{{$fetchData->id}}" class="btn btn-success nextstage ifdiscont">Proceed to Next Stage <i class="fa fa-angle-right"></i></a>

			<a href="javascript:;" style="<?php if($fetchData->status == 1 || $fetchData->status == 2){ echo ''; }else{ echo 'display:none;'; } ?>"  data-id="{{$fetchData->id}}" class="btn btn-success revertapp">Revert <i class="fa fa-angle-right"></i></a>

			<span id="discontinue_reason" style="<?php if($fetchData->status == 2){ echo 'display:block;'; } else { echo 'display:none;'; } ?>">Discontinue Reason - <span id="discontinue_reason_text"><?php echo $fetchData->discontinue_reason; ?></span></span>

            <span id="discontinue_note" style="<?php if($fetchData->status == 2){ echo 'display:block;'; } else { echo 'display:none;'; } ?>">Discontinue Note - <span id="discontinue_note_text"><?php echo $fetchData->discontinue_note; ?></span></span>

            <span id="refund_note" style="<?php if($fetchData->status == 8){ echo 'display:block;'; } else { echo 'display:none;'; } ?>">Refund Note - <span id="refund_note_text"><?php echo $fetchData->refund_notes; ?></span></span>

		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="application_grid_col">
	<!--<div class="grid_column">
		<span>Course:</span>
		<p>{{@$productdetail->name}}</p>
	</div>
	<div class="grid_column">
		<span>School:</span>
		<p>{{@$partnerdetail->partner_name}}</p>
	</div>
	<div class="grid_column">
		<span>Branch:</span>
		<p>{{@$PartnerBranch->name}}</p>
	</div>
	<div class="grid_column">
		<span>Workflow:</span>
		<p>{{@$workflow->name}}</p>
	</div>-->
	<div class="grid_column">
		<span>Current Stage:</span>
		<p class="text-success curerentstage" style="font-weight: 600;font-size: 14px;">{{@$fetchData->stage}}</p>
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
			<?php
			if($fetchData->progresswidth == 0){
				$width = 0;
			}else{
				$width = $fetchData->progresswidth;
			}
			
			$over = '';
			if($width > 50){
				$over = '50';
			}
			?>
			 <div id="progresscir" class="progress-circle over_{{$over}} prgs_{{$width}}">
			   <span>{{$width}} %</span>
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
				<!--<li class="nav-item">
					<a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks">Tasks</a>
				</li> 
				<li class="nav-item">
					<a class="nav-link" id="paymentschedule-tab" data-toggle="tab" href="#paymentschedule">Payment Schedule</a>
				</li>-->
			</ul> 
			<div class="tab-content" id="applicationContent">
				<div class="tab-pane fade show active" id="applicate_activities" role="tabpanel" aria-labelledby="applicate_activities-tab">
					<div id="accordion">
						<?php
						if($fetchData->status == 1){
							$stage9 = 'app_green';
						}
						$stagesquery = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->get();
						foreach($stagesquery as $stages){
							$stage1 = '';
						?>
						<?php
							$workflowstagess = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
							$stagearray = array();
							if($workflowstagess){
								$prevdata = \App\Models\WorkflowStage::where('id', '<', @$workflowstagess->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->get();
								foreach($prevdata as $pre){
									$stagearray[] = $pre->id;
								}
							}

							if(in_array($stages->id, $stagearray)){
								$stage1 = 'app_green';
							}
							if($fetchData->status == 1){
								$stage1 = 'app_green';
							}
							$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
							?>
						<div class="accordion cus_accrodian">
							
							<div class="accordion-header collapsed <?php echo $stage1; ?> <?php if($fetchData->stage == $stages->name && $fetchData->status != 1){ echo  'app_blue'; }  ?>" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
								<h4><?php echo $stages->name; ?></h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="<?php echo $stages->name; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									<a title="Add Document" class="opendocnote" data-app-type="<?php echo $stagname; ?>" data-typename="<?php echo $stages->name; ?>" data-id="<?php echo $fetchData->id; ?>" data-appdocclientid="<?php echo $fetchData->client_id;?>" href="javascript:;"><i class="fa fa-file-image"></i></a>
                                  
                                   <!--<a data-course="<?php //echo $productdetail->name; ?>" data-school="<?php //echo $partnerdetail->partner_name; ?>" data-app-type="<?php //echo $stages->name; ?>" title="Actions" class="openappaction" data-id="<?php //echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
                                    
									<a data-app-type="<?php //echo $stages->name; ?>" title="Add Appointments" class="openappappoint" data-id="<?php //echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>-->
                                  
									<a data-app-type="<?php echo $stages->name; ?>" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<?php
							$applicationlists = \App\Models\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage',$stages->name)->orderby('created_at', 'DESC')->get();
							
							?>
							<div class="accordion-body collapse" id="<?php echo $stagname; ?>_accor" data-parent="#accordion" style="">
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
						<?php } ?>
						
					</div> 
				</div> 
				<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
					<div class="document_checklist">
						<?php
						$applicationdocumentco = \App\Models\ApplicationDocumentList::where('application_id', $fetchData->id)->count();
						$application_id =  $fetchData->id;
						$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
						$stagesquery = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->get();
						?>
						<h4>Document Checklist (<span class="checklistuploadcount">{{@$applicationuploadcount[0]->cnt}}</span>/<span class="checklistcount">{{@$applicationdocumentco}}</span>)</h4>
						<p>The changes & addition of the checklist will only be affected to current application only.</p>
						<div class="row">
							<div class="col-md-5">
								<div class="checklist">
									<ul>
									<?php
									foreach($stagesquery as $stages){
										$name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
										
									?>
											<li><span><?php echo $stages->name; ?></span>
											<div class="<?php echo $name; ?>_checklists">
												<?php
												$applicationdocumentsquery = \App\Models\ApplicationDocumentList::where('application_id', $fetchData->id)->where('type', $name);
												$applicationdocumentscount = $applicationdocumentsquery->count();
												$applicationdocuments = $applicationdocumentsquery->get();
												if($applicationdocumentscount !== 0){
													
												?>
												<table class="table">
													<tbody>
														<?php foreach($applicationdocuments as $applicationdocument){ 
														$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
														?>
														<tr>
															<td><?php if($appcount >0){ ?><span class="check"><i class="fa fa-check"></i></span><?php }else{ ?><span class="round"></span><?php } ?></td>
															<td>{{@$applicationdocument->document_type}}</td>
															<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">{{@$appcount}}</button></div></td>
															<td><a data-aid="{{$fetchData->id}}" data-typename="<?php echo $stages->name; ?>" data-type="<?php echo $name; ?>" data-id="{{$applicationdocument->id}}" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>
														</tr>
														<?php } ?>
													</tbody>
													
												</table>
												<?php } ?>
											</div>
											<a class="openchecklist" data-id="{{$fetchData->id}}" data-typename="<?php echo $stages->name; ?>" data-type="<?php echo $name; ?>" href="javascript:;"><i class="fa fa-plus"></i> Add New Checklist</a></li>
										<?php } ?>
											
										</ul> 
						</div>
							</div>
							<div class="col-md-7">
								<div class="table-responsive"> 
						<table class="table text_wrap">
							<thead>
								<tr>
									<th>Filename / Checklist</th>
									<th>Related Stage</th>
									<th>Added By</th>
									<th>Added On</th>
									<th></th>
								</tr> 
							</thead>
							<tbody class="tdata mychecklistdocdata">	
							<?php
							$doclists = \App\Models\ApplicationDocument::where('application_id',$fetchData->id)->orderby('created_at','DESC')->get();
							foreach($doclists as $doclist){
								$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
							?>
								<tr id="">
									<td><i class="fa fa-file"></i> <?php echo $doclist->file_name; ?><br><?php echo @$docdata->document_type; ?></td>
									<td>
										<?php
										echo $doclist->typename;
										?>
									</td>
									<td><?php
									$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();
									?><span style="position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;"><?php echo substr($admin->first_name, 0, 1); ?></span><?php echo $admin->first_name; ?></td>
									<td><?php echo date('Y-m-d',strtotime($doclist->created_at)); ?></td>
									<td>
										<?php if($doclist->status == 1){
											?>
											<span class="check"><i class="fa fa-eye"></i></span>
											<?php
										} ?>
										<div class="dropdown d-inline">
											<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
											<div class="dropdown-menu">
												
												<!--<a target="_blank" class="dropdown-item" href="{{--URL::to('/public/img/documents')--}}/<?php //echo $doclist->file_name; ?>">Preview</a>
												<a data-id="{{--$doclist->id--}}" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
												<a download class="dropdown-item" href="{{--URL::to('/public/img/documents')--}}/<?php //echo $doclist->file_name; ?>">Download</a>-->

                                                <a target="_blank" class="dropdown-item" href="<?php echo $doclist->myfile; ?>">Preview</a>
                                                <a data-id="{{$doclist->id}}" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
                                                <a download class="dropdown-item" href="<?php echo $doclist->myfile; ?>">Download</a>

												<?php
												if($doclist->status == 0){
												?>
												<a data-id="{{$doclist->id}}" href="javascript:;" class="dropdown-item publishdoc"  >Publish Document</a>
												<?php }else{ ?>
												<a data-id="{{$doclist->id}}" href="javascript:;" class="dropdown-item unpublishdoc"  >Unpublish Document</a>
												<?php } ?>
											</div>
										</div>								  
									</td>
								</tr>
							<?php } ?>
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
				
				<div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
				</div>

				<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
					<div id="taskaccordion">
					<?php
					$stagesquery = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->get();
					foreach($stagesquery as $stages){
						$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
					?>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed active" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
								<h4><?php echo $stages->name; ?></h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="application_accor" data-parent="#taskaccordion" style="">
							</div>-->
						</div>
					<?php } ?>
						
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
								<a class="openpaymentschedule btn btn-primary" data-id="{{$fetchData->id}}" href="javascript:;" ><i class="fa fa-plus"></i> Add Schedule</a>
								<div class="dropdown d-inline">
									<button class="btn btn-outline-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Schedule</button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="javascript:;">Email Schedule</a>
										<a target="_blank" class="dropdown-item" href="{{URL::to('/admin/applications/preview-schedules/')}}/{{$fetchData->id}}">Preview Schedule</a>
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
							<tbody class="tdata showpaymentscheduledata">	
							<?php
							$invoiceschedules = \App\Models\InvoiceSchedule::where('application_id', $fetchData->id)->get();
							foreach($invoiceschedules as $invoiceschedule){

							?>
								<tr id="{{@$invoiceschedule->id}}">
									<td>{{@$invoiceschedule->id}}</td> 
									<td >
										<div style="flex-direction: column;display: flex;">
										<span style="line-height: 23px;" class="text-info">{{@$invoiceschedule->installment_name}}</span>
										<span style="line-height: 16px;" class="">{{@$invoiceschedule->installment_date}}</span>
										<span style="line-height: 14px;" title="Non-Claimable" style="background-color: #2185d0!important;border-color: #2185d0!important;color: #fff!important; cursor: auto;font-size: 10px;padding: 3px;border-radius: 5px;">Non-Claimable</span>
										</div>
									</td>
									<td >
									<div style="flex-direction: column;display: flex;">
									<?php
									foreach($scheduleitem as $scheduleite){
										?>
										<span style="line-height: 23px;" class="">{{@$scheduleite->fee_type}}</span>
										<?php
									}
									?>
									</div>
									</td>
									<td >
									<div style="flex-direction: column;display: flex;">
									<?php
									$totlfee = 0;
									foreach($scheduleitem as $scheduleite){
										$totlfee += $scheduleite->fee_amount;
										?>
										<span style="line-height: 23px;" class="">{{@$scheduleite->fee_amount}}</span>
										<?php
									}
									?>
									</div>
									</td>
									<td>{{$totlfee}}</td>
									<td>{{@$invoiceschedule->discount}}</td>
									<td>-</td>
									<td><span title="Non Claimable" class="ui alignMiddle ag-label--circular truncate text-info">Non Claimable</span></td>
									<td>
										<div class="dropdown d-inline">
											<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
											<div class="dropdown-menu">
												<a class="dropdown-item editpaymentschedule" data-id="{{$invoiceschedule->id}}" href="javascript:;">Edit</a>
												<a class="dropdown-item deletenote" data-id="{{$invoiceschedule->id}}" data-href="deletepaymentschedule" href="javascript:;" >Delete</a>
												<a  data-cid="<?php echo $invoiceschedule->client_id; ?>" data-app-id="<?php echo @$invoiceschedule->application_id; ?>" data-id="<?php echo @$invoiceschedule->id; ?>" class="dropdown-item createapplicationnewinvoice" href="javascript:;" >Create Invoice</a>
											</div>
										</div>
									</td>
								</tr>
							<?php } ?>
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
	<!--<div class="col-md-3">
		<div class="cus_sidebar">
			<div class="form-group">
				<label for="applied_intake">Applied Intake:</label>
				<input type="text" name="applied_intake" id="applied_intake" value="{{ $fetchData->intakedate }}" class="form-control datepicker" data-valid="" autocomplete="off" placeholder="Select Date" />
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
						<input type="text" value="{{@$fetchData->start_date}}" class="startdatepicker" />
						<div class="apply_val">
						
							<span class="month"><?php if(@$fetchData->start_date != ''){ echo date('M',strtotime($fetchData->start_date)); }else{ echo '-'; }?></span>
							<span class="day"><?php if(@$fetchData->start_date != ''){ echo date('d',strtotime($fetchData->start_date)); }else{ echo '-'; }?></span>
							<span class="year"><?php if(@$fetchData->start_date != ''){ echo date('Y',strtotime($fetchData->start_date)); }else{ echo '-'; }?></span>
						</div>
					</div>
				</div>
				<div class="app_end_date common_apply_date">
					<span>End</span>
					<div class="date_col">
						<div class="add_date">
							<span><i class="fa fa-plus"></i> Add</span> 
						</div>
						<input type="text" value="{{@$fetchData->end_date}}" class="enddatepicker" />
						<div class="apply_val">
							<span class="month"><?php if(@$fetchData->end_date != ''){ echo date('M',strtotime($fetchData->end_date)); }else{ echo '-'; }?></span>
							<span class="day"><?php if(@$fetchData->end_date != ''){ echo date('d',strtotime($fetchData->end_date)); }else{ echo '-'; }?></span>
							<span class="year"><?php if(@$fetchData->end_date != ''){ echo date('Y',strtotime($fetchData->end_date)); }else{ echo '-'; }?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="divider"></div>
			<div class="setup_payment_sche">
				<?php
			$appfeeoption = \App\Models\ApplicationFeeOption::where('app_id', $fetchData->id)->first(); //dd($appfeeoption);
			$totl = 0.00;
            $commission_tot = 0.00;
			$discount = 0.00;
			if($appfeeoption){
				$appfeeoptiontype = \App\Models\ApplicationFeeOptionType::where('fee_id', $appfeeoption->id)->get();
				foreach($appfeeoptiontype as $fee){
					$totl += $fee->total_fee;
                    $commission_tot += $fee->commission;
				}
			}
	
			if(@$appfeeoption->total_discount != ''){
				$discount = @$appfeeoption->total_discount;
			}
			$net = $totl -  $discount;
			$invoiceschedule = \App\Models\InvoiceSchedule::where('application_id', $fetchData->id)->first();
			?>
				<a style="<?php if($invoiceschedule){ echo 'display:none;'; } ?>" href="javascript:;" data-id="{{$fetchData->id}}"  class="btn btn-outline-primary openpaymentschedule"><i class="fa fa-plus"></i> Setup Payment Schedule</a>
			</div>
          
          	<div class="divider"></div>
            <input type="text" name="student_id" data-applicationid="{{$fetchData->id}}" id="student_id" value="{{$fetchData->student_id}}" placeholder="Enter Student Id" style="width: 132px;">
            
			<div class="divider"></div>
			
			<div class="cus_prod_fees">
				<h5>Product Fees <span>AUD</span></h5>
				<a href="javascript:;" data-id="{{$fetchData->id}}" data-partnerid="{{$fetchData->partner_id}}" class="openpaymentfee"><i class="fa fa-edit"></i></a>
				<div class="clearfix"></div>
			</div>
			
            <p class="clearfix">
				<span class="float-left">Total Course Fee</span>
				<span class="float-right text-muted total_course_fee_amount"> 
                  <?php 
				if( isset($appfeeoption['total_course_fee_amount']) &&  $appfeeoption['total_course_fee_amount'] != ''){
				echo $appfeeoption['total_course_fee_amount'];
			} else { echo "0.00";} ?></span>
			</p>
          
             <p class="clearfix">
				<span class="float-left">Scholarship Fee</span>
				<span class="float-right text-muted scholarship_fee_amount">
                 <?php
				if( isset($appfeeoption['scholarship_fee_amount']) &&  $appfeeoption['scholarship_fee_amount'] != ''){
					echo $appfeeoption['scholarship_fee_amount'];
				} else { echo "0.00";} ?></span>
			</p>

            <p class="clearfix">
				<span class="float-left">Enrolment Fee</span>
				<span class="float-right text-muted enrolment_fee_amount"> 
                 <?php 
				if( isset($appfeeoption['enrolment_fee_amount']) &&  $appfeeoption['enrolment_fee_amount'] != ''){
					echo $appfeeoption['enrolment_fee_amount'];
				} else { echo "0.00";} ?></span>
			</p>

            <p class="clearfix">
				<span class="float-left">Material fees</span>
				<span class="float-right text-muted material_fees"><?php 
				if( isset($appfeeoption['material_fees']) &&  $appfeeoption['material_fees'] != ''){
					echo $appfeeoption['material_fees'];
				} else { echo "0.00";} ?></span>
			</p>

            <p class="clearfix">
				<span class="float-left">Tution Fee</span>
				<span class="float-right text-muted tution_fees"><?php 
				if( isset($appfeeoption['tution_fees']) &&  $appfeeoption['tution_fees'] != ''){
					echo $appfeeoption['tution_fees'];
				} else { echo "0.00";} ?></span>
			</p>

            <div class="divider"></div>
			<div class="cus_prod_fees ">
				<h5>Commission Status <span>AUD</span></h5>
				<?php
			$client_revenue = '0.00';
			if($fetchData->client_revenue != ''){
				$client_revenue = $fetchData->client_revenue;
			}
			$partner_revenue = '0.00';
			if($fetchData->partner_revenue != ''){
				$partner_revenue = $fetchData->partner_revenue;
			}
			$discounts = '0.00';
			if($fetchData->discounts != ''){
				$discounts = $fetchData->discounts;
			}
			$nettotal = $client_revenue + $partner_revenue - $discounts;
			?>
				<a href="javascript:;" data-id="{{$fetchData->id}}" class="openpaymentfeeLatest"><i class="fa fa-edit"></i></a>

				<div class="clearfix"></div>
			</div>

            <p class="clearfix appsaleforcast">
				<span class="float-left">Total Fee Paid</span>
				<span class="float-right text-muted fee_reported_by_college"><?php 
				if( isset($appfeeoption['fee_reported_by_college']) &&  $appfeeoption['fee_reported_by_college'] != ''){
					echo $appfeeoption['fee_reported_by_college'];
				} else { echo "0.00";} ?></span>
			</p>

			<div class="form-group">
				<label for="expect_win_date">Expected Win Date:</label>
				<input type="text" name="expect_win_date" id="expect_win_date" value="{{ $fetchData->expect_win_date }}" class="form-control expectdatepicker" data-valid="" autocomplete="off" placeholder="Select Date" />
				<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
				@if ($errors->has('expect_win_date'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('expect_win_date') }}</strong>
					</span> 
				@endif 
			</div>
			<?php
				$admin = \App\Models\Admin::where('id', $fetchData->user_id)->first();
			?>
			<div class="divider"></div>
			<div class="setup_payment_sche">
			<?php
			$ratio = '100';
			if($fetchData->ratio != ''){
				$ratio = $fetchData->ratio;
			}
			?>
				<a href="javascript:;" data-ration="{{$ratio}}" data-name="{{$admin->first_name}}" data-id="{{$fetchData->id}}" class="btn btn-primary application_ownership">View Appliation Ownership Ratio</a>
			</div>
			
			<div class="divider"></div> 
			<div class="client_added client_info_tags"> 
				<span class="">Started By:</span>
				<div class="client_info">
					<div class="cl_logo">{{substr($admin->first_name, 0, 1)}}</div>
					<div class="cl_name">
						<span class="name">{{$admin->first_name}}</span>
						<span class="email">{{$admin->email}}</span>
					</div>
				</div> 
			</div>
			<div class="client_assign client_info_tags"> 
				<span class="">Assignee:</span>
				<div class="client_info">
					<div class="cl_logo">{{substr($admin->first_name, 0, 1)}}</div>
					<div class="cl_name">
						<span class="name">{{$admin->first_name}}</span>
						<span class="email">{{$admin->email}}</span>
					</div>
				</div>
			</div>
			<div class="divider"></div> 
			<p class="clearfix"> 
				<span class="float-left">Super Agent:</span>
				<span class="float-right text-muted">
					<a href="javascript:;" data-id="{{$fetchData->id}}" class="btn btn-primary btn-sm opensuperagent"><i class="fa fa-plus"></i> Add</a>
					<?php
					$agent = \App\Models\AgentDetails::where('id',$fetchData->super_agent)->first();
					if($agent){
					?>
					<div class="supagent_data">
						<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;"><?php echo substr($agent->full_name, 0, 1); ?></div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name"><?php echo $agent->full_name; ?></span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  <?php echo $agent->struture; ?>
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href="javascript:;" data-href="superagent" data-id="{{$fetchData->id}}"  class="deletenote"><i class="fa fa-times"></i></a>
							</div>
						</div>
					</div>
					<?php } ?>
				</span>
			</p>
			<p class="clearfix"> 
				<span class="float-left">Sub Agent:</span>
				<span class="float-right text-muted">
					<a href="javascript:;" data-id="{{$fetchData->id}}" class="btn btn-primary btn-sm opensubagent"><i class="fa fa-plus"></i> Add</a>
					<div class="subagent_data">
						<?php
					$subagent = \App\Models\AgentDetails::where('id',$fetchData->sub_agent)->first();
					if($subagent){
					?>
					<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;"><?php echo substr($subagent->full_name, 0, 1); ?></div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name"><?php echo $subagent->full_name; ?></span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  <?php echo $subagent->struture; ?>
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href="javascript:;" data-href="subagent" data-id="{{$fetchData->id}}"  class="deletenote"><i class="fa fa-times"></i></a>
							</div>
						</div>
					<?php } ?>
					</div>
				</span>
			</p>
		</div> 
	</div>-->
</div>
<div class="feetypecopy" style="display:none;">
	<div class="fee_fields_row editfee_fields_row">
		<div class="field_col wd40">
			<select data-valid="required" class="form-control fee_type selectfee_type" id="fee_type" name="fee_type[]">
				<option value="">Select Fee Type</option>
				<option  value="Accommodation Fee">Accommodation Fee</option>
				<option  value="Administration Fee">Administration Fee</option>
				<option  value="Application Fee">Application Fee</option>
				<option  value="Tution Fee">Tution Fee</option>
			</select>
		</div>
		<div class="field_col wd25">
			<input data-valid="required" type="number" class="form-control fee_amount" name="fee_amount[]" value="" placeholder="0.00" />
		</div>
		<div class="field_col wd25">
			<input type="number" class="form-control comm_amount" name="comm_amount[]" value="" placeholder="0.00" />
		</div>
		<div class="field_col wd10">
			<a href="javascript:;" class="payremoveitems"><i class="fa fa-trash"></i></a> 
		</div>
		<div class="clearfix"></div> 
	</div>
</div>