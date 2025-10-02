@extends('layouts.admin')
@section('title', 'Client Detail')

@section('content')
<style>
.ag-space-between {justify-content: space-between;} 
.ag-align-center {align-items: center;}
.ag-flex {display: flex;}
.ag-align-start {align-items: flex-start;}
.ag-flex-column {flex-direction: column;}  
.col-hr-1 {margin-right: 5px!important;}
.text-semi-bold {font-weight: 600!important;}
.small, small {font-size: 85%;}
.ag-align-end { align-items: flex-end;}

.ui.yellow.label, .ui.yellow.labels .label {background-color: #fbbd08!important;border-color: #fbbd08!important;color: #fff!important;}
.ui.label:last-child {margin-right: 0;}
.ui.label:first-child { margin-left: 0;}
.field .ui.label {padding-left: 0.78571429em; padding-right: 0.78571429em;}
.ag-appointment-list__title{padding-left: 1rem; text-transform: uppercase;}
.zippyLabel{background-color: #e8e8e8; line-height: 1;display: inline-block;color: rgba(0,0,0,.6);font-weight: 700; border: 0 solid transparent; font-size: 10px;padding: 3px; }
.accordion .accordion-header.app_green{background-color: #54b24b;color: #fff;}
.accordion .accordion-header.app_green .accord_hover a{color: #fff!important;}
.accordion .accordion-header.app_blue{background-color: rgba(3,169,244,.1);color: #03a9f4;}
</style>
<?php
use App\Http\Controllers\Controller;
?>
<!-- Main Content -->
<div class="main-content">

	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
	
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Product Detail</h4>
							<div class="card-header-action">
								<a href="{{route('admin.products.index')}}" class="btn btn-primary">Product List</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-3 col-md-3 col-lg-3">
					<div class="card author-box">
						<div class="card-body">
							<div class="author-box-center">
							<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>{{substr($fetchedData->name, 0, 1)}}</b></span>
								<div class="clearfix"></div>
								<div class="author-box-name">
									<a href="#">{{$fetchedData->name}}</a>
								</div>
								<div class="author-mail_sms"> 
									<a href="{{URL::to('/admin/products/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<h4>General Information</h4>
						</div>
						<div class="card-body">
							<p class="clearfix"> 
								<span class="float-left">Partner:</span>
								<span class="float-right text-muted"><?php
								$partnerdetail = \App\Models\Partner::where('id', $fetchedData->partner)->first();
								echo $partnerdetail->partner_name;
								?></span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Branches:</span>
								<span class="float-right text-muted"><?php
								$branchesdetail = \App\Models\PartnerBranch::where('id', $fetchedData->branches)->first();
								echo @$branchesdetail->name.' ('.@$branchesdetail->city.')';
								?></span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Services:</span>
								<span class="float-right text-muted"></span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Duration:</span>
								<span class="float-right text-muted">{{$fetchedData->duration}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Intake Month:</span>
								<span class="float-right text-muted"><?php if($fetchedData->preferredIntake != '-- Select Intake Month --' || $fetchedData->preferredIntake != ''){ ?>{{$fetchedData->intake_month}}<?php } ?></span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Revenue Type: </span>
								<span class="float-right text-muted">{{$fetchedData->revenue_type}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Notes: </span>
								<span class="float-right text-muted">{{$fetchedData->note}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Description: </span>
								<span class="float-right text-muted">{!!$fetchedData->description!!}</span>
							</p> 
						</div>
					</div>
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="fees-tab" href="#fees" role="tab" aria-controls="fees" aria-selected="false">Fees</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="requirements-tab" href="#requirements" role="tab" aria-controls="requirements" aria-selected="false">Requirements</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="other_info-tab" href="#other_info" role="tab" aria-controls="other_info" aria-selected="false">Other Information</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="promotions-tab" href="#promotions" role="tab" aria-controls="promotions" aria-selected="false">Promotions</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								<div class="tab-pane fade show active" id="application" role="tabpanel" aria-labelledby="application-tab">
																		
									<div class="table-responsive if_applicationdetail"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Client Name</th>
													<th>Assignee</th>
													<th>Branch Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Status</th>
													<th>Added On</th>
													<th>Last Updated</th>
													
												</tr> 
											</thead>
											<tbody class="applicationtdata">
											<?php
											foreach(\App\Models\Application::where('product_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
												$clientdetail = \App\Models\Admin::where('id', $alist->client_id)->first();
												$admindetail = \App\Models\Admin::where('id', $alist->user_id)->first();
				$partnerdetail = \App\Models\Partner::where('id', $alist->partner_id)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
				$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();
												?>
												<tr id="id_{{$alist->id}}">
													<td><a class="" data-id="{{$alist->id}}" href="{{URL::to('/admin/clients/detail')}}/{{base64_encode(convert_uuencode(@$alist->id))}}" style="display:block;">{{$clientdetail->first_name}} {{$clientdetail->last_name}}</a> </td> 
													<td>{{$admindetail->first_name}} {{$admindetail->last_name}}</td>
													<td>{{$PartnerBranch->name}}</td>
													<td>
													{{$workflow->name}}
													</td> 
													<td>{{$alist->stage}}</td>
													<td>@if($alist->status == 0)
													<span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
													@else if($alist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Completed</span>
													@endif</td> 
													<td>{{date('Y-m-d', strtotime($alist->created_at))}}</td> 
													<td>
													{{date('Y-m-d', strtotime($alist->updated_at))}}
													</td>
												</tr>
												<?php
											}
											?>											
												
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
								<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<div class="upload_document" style="display:inline-block;">
										<form method="POST" enctype="multipart/form-data" id="upload_form">
											@csrf
											<input type="hidden" name="clientid" value="{{$fetchedData->id}}">
											<input type="hidden" name="type" value="product">
											<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
											
											<input type="file" name="document_upload"/>
											</form>
										</div>
									</div>
									<div class="list_data"> 
										<div class="table-responsive"> 
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>File Name</th>
														<th>Added By</th>
													
														<th>Added Date</th>
														<th></th>
													</tr> 
												</thead>
												<tbody class="tdata documnetlist">
										<?php 
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('type','product')->orderby('created_at', 'DESC')->get();
										foreach($fetchd as $fetch){ 
										$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
										?>												
													<tr class="drow" id="id_{{$fetch->id}}">
													<td  >
														<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
															<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
														</div>
													</td> 
													<td><?php echo $admin->first_name; ?></td>
													
													<td><?php echo date('Y-m-d', strtotime($fetch->created_at)); ?></td> 
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
																<a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
															</div>
														</div>								  
													</td>
												</tr>
												<?php } ?>
												</tbody>
												
											</table> 
										</div>
									</div>
									<div class="grid_data griddata">
									<?php
									foreach($fetchd as $fetch){ 
										$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
									?>
										<div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
											<div class="grid_col"> 
												<div class="grid_icon">
													<i class="fas fa-file-image"></i>
												</div> 
												<div class="grid_content">
													<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
														
																<a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="tab-pane fade" id="fees" role="tabpanel" aria-labelledby="fees-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary new_fee_option"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="feeslist">
									<?php
									$feeoptions = \App\Models\FeeOption::where('product_id', $fetchedData->id)->orderby('created_at', 'DESC')->get();
									
									foreach($feeoptions as $feeoption){
									?>
										<div class="feeitem">
											<div class="row">
												<div class="col-md-10">
													<h4 class="text-info"><?php echo $feeoption->name; ?></h4>
												</div>
												<div class="col-md-2">
													<a href="javascript:;" class="editfeeoption" data-id="<?php echo $feeoption->id; ?>"><i class="fa fa-edit"></i></a>
													<a href="javascript:;" class="deletenote" data-href="deletefee" data-id="<?php echo $feeoption->id; ?>"><i class="fa fa-trash"></i></a>
												</div>
												<div class="col-md-2">
													<div class="validfor">
														<span>Valid For</span><br>
														<div class=""><b><?php echo $feeoption->country; ?></b></div>
													</div>
													<div class="installmenttype">
														<span>Installment Type</span><br>
														<div class=""><b><?php echo $feeoption->installment_type; ?></b></div>
													</div>
												</div>
												<?php
												$feeoptiontype = \App\Models\FeeOptionType::where('fee_id', $feeoption->id)->get();
												
												?>
												<div class="col-md-8">
													<div class="validfor">
														<span>Fee Breakdown</span><br>
													<?php $totlfee = 0; foreach($feeoptiontype as $feeoptiontyp){
														$totlfee += $feeoptiontyp->total_fee;
														?>
														<div class="">
															<span><b><?php echo $feeoptiontyp->fee_type; ?></b></span><span> <?php echo $feeoptiontyp->installment; ?> Per Month @ AUD <?php echo $feeoptiontyp->inst_amt; ?></span><span style="margin-left: 24px;"><b>AUD <?php echo $feeoptiontyp->total_fee; ?></b></span>
															
														</div>
													<?php } ?>
													</div>
													
												</div>
												
												<div class="col-md-2">
													<div class="validfor">
														<span>Total Fees</span><br>
														<div class="text-info"><h4>AUD</h4></div>
													
														<div class="text-info"><h4><?php echo number_format($totlfee,2,'.',''); ?></h4></div>
													</div>
													
												</div>
											</div>
											<hr>
										</div>
									<?php } ?>
									</div>
								</div>
								<div class="tab-pane fade" id="requirements" role="tabpanel" aria-labelledby="requirements-tab">
									<div class="card-header-action" style="padding-bottom:15px;">
										<div class="float-left">
											<h5>Academic Requirements</h5> 
										</div>
										<?php
										$acreq = \App\Models\AcademicRequirement::where('product_id', $fetchedData->id)->first();
										?>
										<div class="float-right">
											<a href="javascript:;" <?php if($acreq){ ?>style="display:none;"<?php }else{ ?><?php } ?> class="btn btn-primary add_academic_requirement"><i class="fa fa-plus"></i> Add</a>
											<a data-academic_score_per="<?php echo @$acreq->academic_score_per; ?>" data-academic_score_type="<?php echo @$acreq->academic_score_type; ?>" data-degree="<?php echo @$acreq->degree; ?>" <?php if($acreq){ ?><?php }else{ ?>style="display:none;"<?php } ?> href="javascript:;" class="btn btn-primary editacademic"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									
									<div class="education_list">
									<?php if($acreq){ 
										echo '<div class="row"><div class="col-md-6"><strong>Degree Level</strong><p>'.$acreq->degree.'</p></div><div class="col-md-6"><strong>Academic Score</strong><p>'.$acreq->academic_score_per.' '.$acreq->academic_score_type.'</p></div></div>';
									}else{ ?>
									<div class="edu_note">
									
										<span>* Click add button to fill education background </span>
									
									</div>
									<?php } ?>
									</div>
									<div class="divider"></div>
									<div class="card-header-action" style="padding-top:15px;padding-bottom:10px;">
										<div class="float-left">
											<h5>English Test Scores</h5> 
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".edit_english_test" class="btn btn-primary"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_test_score edu_english_score">
										<div class="edu_test_row" style="text-align:center;">
											<div class="edu_test_col">&nbsp;</div>
											<div class="edu_test_col"><span>Listening</span></div>
											<div class="edu_test_col"><span>Reading</span></div>
											<div class="edu_test_col"><span>Writing</span></div>
											<div class="edu_test_col"><span>Speaking</span></div>
											<div class="edu_test_col"><span>Overall Scores</span></div>
											<div class="edu_test_col"><span>Date</span></div>
										</div>
										<?php
										$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'product')->first();
										?>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>TOEFL</span></div>
											<div class="edu_test_col"><strong class="tofl_lis"><?php if(@$testscores->toefl_Listening != ''){ echo @$testscores->toefl_Listening; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="tofl_reading"><?php if(@$testscores->toefl_Reading != ''){ echo @$testscores->toefl_Reading; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="tofl_writing"><?php if(isset($testscores->toefl_Writing) && $testscores->toefl_Writing != ''){ echo $testscores->toefl_Writing; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="tofl_speaking"><?php if(@$testscores->toefl_Speaking != ''){ echo @$testscores->toefl_Speaking; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="tofl_score"><?php if(@$testscores->score_1 != ''){ echo @$testscores->score_1; }else{ echo '0'; } ?></strong></div>
											<div class="edu_test_col"><strong class="toefl_date"><?php if(@$testscores->toefl_Date != ''){ echo @$testscores->toefl_Date; }else{ echo '-'; } ?></strong></div> 
										</div>  
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>IELTS</span></div>
											<div class="edu_test_col"><strong class="ilets_Listening"><?php if(@$testscores->ilets_Listening != ''){ echo @$testscores->ilets_Listening; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_Reading"><?php if(@$testscores->ilets_Reading != ''){ echo @$testscores->ilets_Reading; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_Writing"><?php if(@$testscores->ilets_Writing != ''){ echo @$testscores->ilets_Writing; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_speaking"><?php if(@$testscores->ilets_Speaking != ''){ echo $testscores->ilets_Speaking; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="ilets_score"><?php if(@$testscores->score_2 != ''){ echo @$testscores->score_2; }else{ echo '0'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_date"><?php if(@$testscores->ilets_Date != ''){ echo $testscores->ilets_Date; }else{ echo '-'; } ?></strong></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>PTE</span></div>
											<div class="edu_test_col"><strong class="pte_Listening"><?php if(@$testscores->pte_Listening != ''){ echo @$testscores->pte_Listening; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_Reading"><?php if(@$testscores->pte_Reading != ''){ echo @$testscores->pte_Reading; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_Writing"><?php if(@$testscores->pte_Writing != ''){ echo @$testscores->pte_Writing; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_Speaking"><?php if(@$testscores->pte_Speaking != ''){ echo @$testscores->pte_Speaking; }else{ echo '-'; } ?></strong></div> 
											<div class="edu_test_col overal_block"><strong class="pte_score"><?php if(@$testscores->score_3 != ''){ echo @$testscores->score_3; }else{ echo '0'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_date"><?php if(@$testscores->pte_Date != ''){ echo @$testscores->pte_Date; }else{ echo '-'; } ?></strong></div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="card-header-action" style="padding-top:15px;padding-bottom:10px;">
										<div class="float-left">
											<h5>Other Test Scores</h5> 
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".edit_other_test" class="btn btn-primary"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_test_score edu_othertest_score">
										<div class="edu_test_row" style="text-align:center;">
											<div class="edu_test_col"></div>
											<div class="edu_test_col"><span>SAT I</span></div>
											<div class="edu_test_col"><span>SAT II</span></div>
											<div class="edu_test_col"><span>GRE</span></div>
											<div class="edu_test_col"><span>GMAT</span></div>
										</div> 
										<div class="edu_test_row flex_row">
											<div class="edu_test_col">Overall Scores</div>
											<div class="edu_test_col overal_block"><strong class="sat_i"><?php if(@$testscores->sat_i != ''){ echo @$testscores->sat_i; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="sat_ii"><?php if(@$testscores->sat_ii != ''){ echo @$testscores->sat_ii; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="gre"><?php if(@$testscores->gre != ''){ echo @$testscores->gre; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="gmat"><?php if(@$testscores->gmat != ''){ echo @$testscores->gmat; }else{ echo '-'; } ?></strong></div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="other_info" role="tabpanel" aria-labelledby="other_info-tab">
								<?php
								$subjectareadata = \App\Models\ProductAreaLevel::where('product_id', $fetchedData->id)->first();
								?>
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" <?php if($subjectareadata){ ?>style="display:none;"<?php }else{ ?><?php } ?> class="btn btn-primary other_info_add"><i class="fa fa-plus"></i> Add</a>
										<a href="javascript:;" <?php if($subjectareadata){ ?><?php }else{ ?>style="display:none;"<?php } ?> class="btn btn-primary other_info_edit"><i class="fa fa-plus"></i> Edit</a>
									</div>
									<div class="otherinfolist">
										<?php 
										
										if($subjectareadata){ 
										$subjectarea = \App\Models\SubjectArea::where('id', $subjectareadata->subject_area)->first();
				$subject = \App\Models\Subject::where('id', $subjectareadata->subject)->first();
				echo '<div class="row"><div class="col-md-4"><strong>Subject Area</strong><p>'.$subjectarea->name.'</p></div><div class="col-md-4"><strong>Subject</strong><p>'.$subject->name.'</p></div><div class="col-md-4"><strong>Degree Level</strong><p>'.$subjectareadata->degree.'</p></div></div>';
									} ?>
									</div>
								</div>
								<div class="tab-pane fade" id="promotions" role="tabpanel" aria-labelledby="promotions-tab">
									<div class="promotionlists"> 
									<?php
									$promotionslist = \App\Models\Promotion::where('apply_to', 'All Products') ->orwhereRaw('FIND_IN_SET("'.$fetchedData->id.'", selectproduct)')->orderby('created_at','DESC')->get();
									foreach($promotionslist as $promotion){
										$countproducts = 0;
										$countbranches = 0;
										if($promotion->apply_to == 'All Products'){
											$countproducts = \App\Models\Product::where('partner', $fetchedData->id)->count();
											$countbranches = \App\Models\PartnerBranch::where('partner_id', $fetchedData->id)->count();
										}else{
											$selectproduct = explode(',',$promotion->selectproduct);
											$countproducts = count($selectproduct);
											$branch = \App\Models\Product::select('branches')->whereIn('id', $selectproduct)->get()->toArray();
											$output =  array_map("unserialize", array_unique(array_map("serialize", $branch)));
											$countbranches = count($output);
										}
									?>
										<div class="promotion_col" id="contact_<?php echo $promotion->id; ?>"> 
											<div class="promotion_content">
											@if($promotion->status == 1)
												<span class="text-success"><b>Active</b></span>
											@else
												<span class="text-danger"><b>Inactive</b></span>
											@endif
												<div class="" style="margin-top: 15px!important;">
													<h4>{{$promotion->promotion_title}}</h4>
													<p>{{ @$promotion->promotion_desc == "" ? config('constants.empty') : str_limit(@$promotion->promotion_desc, '50', '...') }}</p>
												</div>
												<div class="" style="margin-top: 15px!important;">
													<div class="row">
														<div class="col-md-6">
														<span class="text-semi-bold text-underline">For Branches</span>
														</div>
														<div class="col-md-6">
														<span  class="">{{$countbranches}}</span>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
														<span class="text-semi-bold text-underline">For Products</span>
														</div>
														<div class="col-md-6">
														<span  class="">{{$countproducts}}</span>
														</div>
													</div>
												</div>
												<div class="" style="margin-top: 15px!important;">
													<div class="row">
														<div class="col-md-6">
														<span ><b>Start Date</b></span>
														<p>{{$promotion->promotion_start_date}}</p>
														</div>
														<div class="col-md-6">
														<span ><b>Expiry Date</b></span>
														<p>{{$promotion->promotion_end_date}}</p>
														</div>
													</div>
												</div>
											</div>
											<div class="extra_content">
												<div class="view_btn text-right">
													<a href="#" class="btn btn-outline-primary">View</a>
												</div>  
											</div>
										</div>
									<?php } ?>
									</div>
								</div>	
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div> 

@include('Admin/products/addproductmodal')  
@include('Admin/products/editproductmodal')    

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
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								{{ Form::text('email_from', 'support@digitrex.live', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter From' )) }}
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
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
 

<div class="modal fade  custom_modal " id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog">
		<div class="modal-content showinterestedservice">
			
		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmEducationModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){
				
				$('.taskview').html(responses);
			}
		});
	});
	function getallnotes(){
	$.ajax({
		url: site_url+'/admin/get-notes',
		type:'GET',
		data:{clientid:'{{$fetchedData->id}}'},
		success: function(responses){
			
			$('.note_term_list').html(responses);
		}
	});
}

function getallactivities(){
	$.ajax({
					url: site_url+'/admin/get-activities',
					type:'GET',
					datatype:'json',
					data:{id:'{{$fetchedData->id}}'},
					success: function(responses){
						var ress = JSON.parse(responses);
						var html = '';
						$.each(ress.data, function(k, v) {
							html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
							if(v.message != null){
								html += '<p>'+v.message+'</p>';
							}
							html += '</div></div>';
						});
						$('.activities').html(html);
					}
				});
}
	var notid = '';
	var delhref = '';
	$(document).delegate('.deletenote', 'click', function(){
		$('#confirmModal').modal('show');
		notid = $(this).attr('data-id');
		delhref = $(this).attr('data-href');
	});
	$(document).delegate('#confirmModal .accept', 'click', function(){
	
		$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/')}}/'+delhref,
			type:'GET',
			datatype:'json',
			data:{note_id:notid},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				$('#confirmModal').modal('hide');
				if(res.status){
					$('#note_id_'+notid).remove();
					if(delhref == 'deletedocs'){
						$('.documnetlist #id_'+notid).remove();
					}
					if(delhref == 'deleteservices'){
						$.ajax({
						url: site_url+'/admin/get-services',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.interest_serv_list').html(responses);
						}
					});
					}if(delhref == 'deleteappointment'){
						$.ajax({
						url: site_url+'/admin/get-appointments',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.appointmentlist').html(responses);
						}
					});
					}if(delhref == 'deletefee'){
						$.ajax({
						url: site_url+'/admin/get-all-fees',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.feeslist').html(responses);
						}
					});
					}else{
						getallnotes();
					}
					
					//getallactivities();
				}
			}
		});
	});
	
	
	$(document).delegate('.create_note', 'click', function(){
		$('#create_note').modal('show');
		$('#create_note input[name="mailid"]').val(0);
		$('#create_note input[name="title"]').val('');
		$('#create_note #appliationModalLabel').html('Create Note');
		$('#create_note input[name="title"]').val('');
					$("#create_note .summernote-simple").val('');
					$('#create_note input[name="noteid"]').val('');                    
				$("#create_note .summernote-simple").summernote('code','');
		if($(this).attr('datatype') == 'note'){
			$('.is_not_note').hide();
		}else{ 
		var datasubject = $(this).attr('datasubject');
		var datamailid = $(this).attr('datamailid');
			$('#create_note input[name="title"]').val(datasubject);
			$('#create_note input[name="mailid"]').val(datamailid);
			$('.is_not_note').show();
		}
	});
	
	$(document).delegate('.opentaskmodal', 'click', function(){
		$('#opentaskmodal').modal('show');
		$('#opentaskmodal input[name="mailid"]').val(0);
		$('#opentaskmodal input[name="title"]').val('');
		$('#opentaskmodal #appliationModalLabel').html('Create Note');
		$('#opentaskmodal input[name="attachments"]').val('');
		$('#opentaskmodal input[name="title"]').val('');
		$('#opentaskmodal .showattachment').val('Choose file');
              
		var datasubject = $(this).attr('datasubject');
		var datamailid = $(this).attr('datamailid');
			$('#opentaskmodal input[name="title"]').val(datasubject);
			$('#opentaskmodal input[name="mailid"]').val(datamailid);
	});
	$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#create_note'),
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
$('.js-data-example-ajaxcontact').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#opentaskmodal'),
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
	$(document).delegate('.opennoteform', 'click', function(){
		$('#create_note').modal('show');
		$('#create_note #appliationModalLabel').html('Edit Note');
		var v = $(this).attr('data-id');
		$('#create_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/getnotedetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#create_note input[name="title"]').val(res.data.title);
					$("#create_note .summernote-simple").val(res.data.description);                    
				$("#create_note .summernote-simple").summernote('code',res.data.description);
				}
			}
		});
	});
	$(document).delegate('.add_appliation #workflow', 'change', function(){
	
				var v = $('.add_appliation #workflow option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpartnerbranch')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.add_appliation #partner').html(response);
				
				$(".add_appliation #partner").val('').trigger('change');
			$(".add_appliation #product").val('').trigger('change');
			$(".add_appliation #branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('.add_appliation #partner','change', function(){
		
				var v = $('.add_appliation #partner option:selected').val();
				var explode = v.split('_');
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getbranchproduct')}}',
			type:'GET',
			data:{cat_id:explode[0]},
			success:function(response){
				$('.popuploader').hide();
				$('.add_appliation #product').html(response);
				$(".add_appliation #product").val('').trigger('change');
			
			}
		});
				}
	});
	
	
	
	$(document).delegate('.clientemail', 'click', function(){ 

	$('#emailmodal').modal('show');
	var array = [];
	var data = [];

		
			var id = $(this).attr('data-id');
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
$(document).delegate('.change_client_status', 'click', function(e){
	
	var v = $(this).attr('rating');
	$('.change_client_status').removeClass('active');
	$(this).addClass('active');
	
	 $.ajax({
		url: '{{URL::to('/admin/change-client-status')}}',
		type:'GET',
		datatype:'json',
		data:{id:'{{$fetchedData->id}}',rating:v},
		success: function(response){
			var res = JSON.parse(response);
			if(res.status){
				
				$('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
				getallactivities();
			}else{
				$('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
			}
			
		}
	}); 
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
			 $("#emailmodal .summernote-simple").summernote('reset');  
                    $("#emailmodal .summernote-simple").summernote('code', res.description);  
					$("#emailmodal .summernote-simple").val(res.description); 
			
		}
	});
});

$(document).delegate('.selectapplicationtemplate', 'change', function(){
	var v = $(this).val();
	$.ajax({
		url: '{{URL::to('/admin/get-templates')}}',
		type:'GET',
		datatype:'json',
		data:{id:v},
		success: function(response){
			var res = JSON.parse(response);
			$('.selectedappsubject').val(res.subject);
			 $("#applicationemailmodal .summernote-simple").summernote('reset');  
                    $("#applicationemailmodal .summernote-simple").summernote('code', res.description);  
					$("#applicationemailmodal .summernote-simple").val(res.description); 
			
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
		dropdownParent: $('#create_note'),
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

$(".table-2").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ],
  order: [[1, "desc"]] //column indexes is zero based

});

$(".invoicetable").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ],
  order: [[1, "desc"]] //column indexes is zero based

});


$(document).delegate('#intrested_workflow', 'change', function(){
	
				var v = $('#intrested_workflow option:selected').val();
				
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpartner')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_partner').html(response);
				
				$("#intrested_partner").val('').trigger('change');
			$("#intrested_product").val('').trigger('change');
			$("#intrested_branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('#intrested_partner','change', function(){
		
				var v = $('#intrested_partner option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getproduct')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_product').html(response);
				$("#intrested_product").val('').trigger('change');
			$("#intrested_branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('#intrested_product','change', function(){
		
				var v = $('#intrested_product option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getbranch')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_branch').html(response);
				$("#intrested_branch").val('').trigger('change');
			}
		});
		}
	}); 
	$(document).delegate('input[name=document_upload]', 'click', function() {
    $(this).attr("value", "");
})  
	$(document).delegate('input[name=document_upload]', 'change', function() {
		$('.popuploader').show();	
var formData = new FormData($('#upload_form')[0]);		
		$.ajax({
			url: site_url+'/admin/upload-document',
			type:'POST',
			datatype:'json',
			 data: formData,
			contentType: false,
			processData: false,
			
			success: function(responses){
					$('.popuploader').hide();
var ress = JSON.parse(responses);
if(ress.status){
	$('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
	$('.documnetlist').html(ress.data);
	$('.griddata').html(ress.griddata);
}else{
$('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');	
}	
				
			}
		});
	});
	
	$(document).delegate('.converttoapplication','click', function(){
		
				var v = $(this).attr('data-id');
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/convertapplication')}}',
			type:'GET',
			data:{cat_id:v,clientid:'{{$fetchedData->id}}'},
			success:function(response){
				$('.popuploader').hide();
				$.ajax({
					url: site_url+'/admin/get-services',
					type:'GET',
					data:{clientid:'{{$fetchedData->id}}'},
					success: function(responses){
						
						$('.interest_serv_list').html(responses);
					}
				});
				getallactivities();
			}
		});
		}
	});

	$(document).on('click', '.documnetlist .renamedoc', function () {
			var parent = $(this).closest('.drow').find('.doc-row');

			parent.data('current-html', parent.html());
			var opentime = parent.data('name');

			parent.empty().append(
				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
				
				$('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
				$('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
			);

			return false;
	
	});
	
	$(document).on('click', '.documnetlist .drow .btn-danger', function () {
			var parent = $(this).closest('.drow').find('.doc-row');
			var hourid = parent.data('id');
			if (hourid) {
				parent.html(parent.data('current-html'));
			} else {
				parent.remove();
				
			}
		});
		
	$(document).delegate('.documnetlist .drow .btn-primary', 'click', function () {
		
			var parent = $(this).closest('.drow').find('.doc-row');
			parent.find('.opentime').removeClass('is-invalid');
			parent.find('.invalid-feedback').remove();

			var opentime = parent.find('.opentime').val();


			if (!opentime) {
				parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
				parent.append($("<div class='invalid-feedback'>This field is required</div>"));
				return false;
			}
			
			$.ajax({
			   type: "POST",
			   data: {"_token": $('meta[name="csrf-token"]').attr('content'),"filename": opentime, "id": parent.data('id')},
			   url: '{{URL::to('/admin/renamedoc')}}',
			   success: function(result){
				   var obj = JSON.parse(result);
				 if (obj.status) {
						parent.empty()
							.data('id', obj.Id)
							.data('name', opentime)
							.append(
								$('<span>').html('<i class="fas fa-file-image"></i> '+obj.filename+'.'+obj.filetype)
							);
							$('#grid_'+obj.Id).html(obj.filename+'.'+obj.filetype);
					} else {
						parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
						parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
					}
			   }
			});
			

			return false;
		});
		

$(document).delegate('.opencreate_task', 'click', function () {
	$('#tasktermform')[0].reset();
	$('#tasktermform select').val('').trigger('change');
	$('.create_task').modal('show');
	$('.ifselecttask').hide();
	$('.ifselecttask select').attr('data-valid', '');
	
});
	 var eduid = '';
    $(document).delegate('.deleteeducation', 'click', function(){
		eduid = $(this).attr('data-id');
		$('#confirmEducationModal').modal('show');
		
	});
	
	$(document).delegate('#confirmEducationModal .accepteducation', 'click', function(){
	
		$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/')}}/delete-education',
			type:'GET',
			datatype:'json',
			data:{edu_id:eduid},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				$('#confirmEducationModal').modal('hide');
				if(res.status){
					$('#edu_id_'+eduid).remove();
				}else{
					alert('Please try again')
				}
			}
		});
	});
    $(document).delegate('#other_info_add #subjectlist', 'change', function(){
	
				var v = $('#other_info_add #subjectlist option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getsubjects')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#other_info_add #subject').html(response);
				
				$("#other_info_add #subject").val('').trigger('change');
			
			}
		});
				}
	});
	
	$(document).delegate('.edit_appointment', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#edit_appointment').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getAppointmentdetail')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showappointmentdetail').html(response);
				 $(".datepicker").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        singleDatePicker: true,
        showDropdowns: true
      });
				$(".timepicker").timepicker({
      icons: {
        up: "fas fa-chevron-up",
        down: "fas fa-chevron-down"
      }
    });
			}
		});
	});
	
	$(document).delegate('.editeducation', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#edit_education').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getEducationdetail')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showeducationdetail').html(response);
				 $(".datepicker").daterangepicker({
					locale: { format: "YYYY-MM-DD" },
					singleDatePicker: true,
					showDropdowns: true
				  });
			
			}
		});
	});
	
	$(document).delegate('.interest_service_view', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#interest_service_view').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getintrestedservice')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showinterestedservice').html(response);
			}
		});
	});
	
	
	$(document).delegate('.openeditservices', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#interest_service_view').modal('hide');
		$('#eidt_interested_service').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getintrestedserviceedit')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showinterestedserviceedit').html(response);
				
				 $(".datepicker").daterangepicker({
					locale: { format: "YYYY-MM-DD" },
					singleDatePicker: true,
					showDropdowns: true
				  });
			}
		});
	});
	
	$(document).delegate('.opencommissioninvoice', 'click', function(){
		$('#opencommissionmodal').modal('show');
	});
	
	$(document).delegate('.opengeneralinvoice', 'click', function(){
		$('#opengeneralinvoice').modal('show');
	});
	 

$(document).delegate('.addpaymentmodal','click', function(){
		var v = $(this).attr('data-invoiceid');
		var netamount = $(this).attr('data-netamount');
		var dueamount = $(this).attr('data-dueamount');
		$('#invoice_id').val(v);
		$('.invoicenetamount').html(netamount+' AUD');
		$('.totldueamount').html(dueamount);
		$('.totldueamount').attr('data-totaldue', dueamount);
		$('#addpaymentmodal').modal('show');
		$('.payment_field_clone').remove();
		$('.paymentAmount').val('');
	});	

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
	$('.add_fee_type a.fee_type_btn').on('click', function(){ 
		var clonedval = $('.fees_type_sec .fee_type_row .fees_type_col').html();
		$('.fees_type_sec .fee_type_row').append('<div class="custom_type_col fees_type_clone">'+clonedval+'</div>');
	});
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){ 
		var $tr    = $(this).closest('.payment_field_clone');
		var trclone = $('.payment_field_clone').length;		
		if(trclone > 0){
			$tr.remove();
			grandtotal();
		} 
	});
	$(document).delegate('.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', 'click', function(){ 
		var $tr    = $(this).closest('.fees_type_clone');
		var trclone = $('.fees_type_clone').length;		
		if(trclone > 0){
			$tr.remove();
			grandtotal();
		} 
	});	
	
	
$(document).delegate('.openapplicationdetail', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/admin/getapplicationdetail')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('.ifapplicationdetailnot').html(response);
				$('.datepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$.ajax({
						url:"{{URL::to('/admin/application/updateintake')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				

			}
		});
	});
	
	$(document).delegate('#application-tab', 'click', function(){
		
		$('.if_applicationdetail').show();
		$('.ifapplicationdetailnot').hide();
		$('.ifapplicationdetailnot').html('<h4>Please wait ...</h4>');
	});
	
$(document).delegate('.openappnote', 'click', function(){
	var apptype = $(this).attr('data-app-type');
	var id = $(this).attr('data-id');
	$('#create_applicationnote #noteid').val(id);
	$('#create_applicationnote #type').val(apptype);
	$('#create_applicationnote').modal('show');
}); 
$(document).delegate('.openappappoint', 'click', function(){
	var id = $(this).attr('data-id');
	var apptype = $(this).attr('data-app-type');
	$('#create_applicationappoint #type').val(apptype);
	$('#create_applicationappoint #appointid').val(id);
	$('#create_applicationappoint').modal('show');
});

$(document).delegate('.openclientemail', 'click', function(){
	var id = $(this).attr('data-id');
	var apptype = $(this).attr('data-app-type');
	$('#applicationemailmodal #type').val(apptype);
	$('#applicationemailmodal #appointid').val(id);
	$('#applicationemailmodal').modal('show');
});

$(document).delegate('.openchecklist', 'click', function(){
	var id = $(this).attr('data-id'); 
	$('#create_checklist #checklistid').val(id);
	$('#create_checklist').modal('show');
});
$(document).delegate('.openpaymentschedule', 'click', function(){
	$('#create_paymentschedule').modal('show');
});

$(document).delegate('.createaddapointment', 'click', function(){
	$('#create_appoint').modal('show');
});
$(document).delegate('.due_date_sec a.due_date_btn', 'click', function(){
	$('.due_date_sec .due_date_col').show();
	$(this).hide();
});
$(document).delegate('.remove_col a.remove_btn', 'click', function(){
	$('.due_date_sec .due_date_col').hide();
	$('.due_date_sec a.due_date_btn').show();  
});
	
$(document).delegate('.nextstage', 'click', function(){
	var appliid = $(this).attr('data-id');
	var stage = $(this).attr('data-stage');
	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/admin/updatestage')}}',
		type:'GET',
		datatype:'json',
		data:{id:appliid, client_id:'{{$fetchedData->id}}'},
		success:function(response){
			$('.popuploader').hide();
			var obj = $.parseJSON(response);
			if(obj.status){
				$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
				$('.curerentstage').text(obj.stage);
				$.ajax({
					url: site_url+'/admin/get-applications-logs',
					type:'GET',
					data:{clientid:'{{$fetchedData->id}}',id: appliid},
					success: function(responses){
						 
						$('#accordion').html(responses);
					}
				});
			}else{
				$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
			}
		}
	});
});

$(document).delegate('.backstage', 'click', function(){
	var appliid = $(this).attr('data-id');
	var stage = $(this).attr('data-stage');
	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/admin/updatebackstage')}}',
		type:'GET',
		datatype:'json',
		data:{id:appliid, client_id:'{{$fetchedData->id}}'},
		success:function(response){
			var obj = $.parseJSON(response);
			$('.popuploader').hide();
			if(obj.status){
				$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
				$('.curerentstage').text(obj.stage);
				$.ajax({
					url: site_url+'/admin/get-applications-logs',
					type:'GET',
					data:{clientid:'{{$fetchedData->id}}',id: appliid},
					success: function(responses){
						 
						$('#accordion').html(responses);
					}
				});
			}else{
				$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
			}
		}
	});
});


$(document).delegate('.other_info_add', 'click', function(){
	$('#other_info_add').modal('show');
	$('#other_info_add #academiModalLabel').html('Add Subject Area & Level');

});

$(document).delegate('.new_fee_option', 'click', function(){
	$('#new_fee_option').modal('show');
});

$(document).delegate('.other_info_edit', 'click', function(){
	$('#other_info_edit').modal('show');
	$('#other_info_edit #academiModalLabel').html('Edit Subject Area & Level');
	$.ajax({
			url: '{{URL::to('/admin/product/getotherinfo')}}',
			type:'GET',
			data:{id:'{{$fetchedData->id}}'},
			success:function(response){
				$('.popuploader').hide();
				$('.showsubjecthtml').html(response);
				
			}
		});
});

$(document).delegate('.add_academic_requirement', 'click', function(){
	$('#add_academic_requirement').modal('show');
	$('#add_academic_requirement #academiModalLabel').html('Add Academic Requirements');
	$('#add_academic_requirement select').val('');
	$('#add_academic_requirement input[name="academic_score"]').val('');
});

$(document).delegate('.editacademic', 'click', function(){
	var v1 = $(this).attr('data-academic_score_per');
	var v2 = $(this).attr('data-academic_score_type');
	var v3 = $(this).attr('data-degree');

	$('#add_academic_requirement').modal('show');
	$('#add_academic_requirement #academiModalLabel').html('Edit Academic Requirements');
		$('#add_academic_requirement select').val(v3);
		$('#add_academic_requirement select').trigger('change');
	$('#add_academic_requirement input[name="academic_score"]').val(v1);
	if(v2 == '%'){
		$('#percentage').prop('checked', true);
	}else{
		$('#GPA').prop('checked', true);
	}
	$('#add_academic_requirement input[name="academic_score"]').val(v1);
});
$(document).delegate('#notes-tab', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/admin/getapplicationnotes')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('#notes').html(response);
				
			}
		});
	});
	
	$(".timezoneselect2").select2({
		dropdownParent: $("#create_appoint .modal-content")
	});
	$(".installment_type, .residencyelect2").select2({
		dropdownParent: $("#new_fee_option .modal-content")
	});
	
  
  $('#attachments').on('change',function(){
       // output raw value of file input
      $('.showattachment').html(''); 

        // or, manipulate it further with regex etc.
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        // .. do your magic

       $('.showattachment').html(filename);
    });
	
	$(document).delegate('#new_fee_option .installment_amount','keyup', function(){
		var installment_amount = $(this).val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		
		var installment = $(this).parent().parent().find('.installment').val();
		
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		$('#new_fee_option .net_totl').html(price.toFixed(2));
	});
	
	$(document).delegate('#new_fee_option .installment','keyup', function(){
		var installment = $(this).val();
		
		
		var installment_amount = $(this).parent().parent().find('.installment_amount').val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		$('#new_fee_option .net_totl').html(price.toFixed(2));
	});
	
	$(document).delegate('.editfeeoption', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('#editfeeoption').modal('show');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getfeeoptionedit')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('.showfeeoptionedit').html(response);
				$(".edit_installment_type, .residencyelect2").select2({ 
					dropdownParent: $("#editfeeoption .modal-content")
				});
			}
		});
	});
	
	
	$(document).delegate('#editfeeoption .installment_amount','keyup', function(){
		var installment_amount = $(this).val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		
		var installment = $(this).parent().parent().find('.installment').val();
		
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#editfeeoption .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		$('#editfeeoption .net_totl').html(price.toFixed(2));
	});
	
	$(document).delegate('#editfeeoption .installment','keyup', function(){
		var installment = $(this).val();
		
		
		var installment_amount = $(this).parent().parent().find('.installment_amount').val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#editfeeoption .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		$('#editfeeoption .net_totl').html(price.toFixed(2));
	});
	
	
	$(document).delegate('#new_fee_option .fee_option_addbtn a', 'click', function(){
	var htmldd = '';
	<?php foreach(\App\Models\FeeType::all() as $feetypes){ ?>
			htmldd +='<option @if($feetypes->name == 'Tution fees') selected @endif value="{{$feetypes->name}}">{{$feetypes->name}}</option>';
	<?php } ?>
		var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option>'+htmldd+'</select></td><td><input type="number" value="0" class="form-control installment_amount" name="installment_amount[]"></td><td><input type="number" value="1" class="form-control installment" name="installment[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_terms[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td><input value="1" class="add_quotation" type="checkbox" name="add_quotation[]"> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
		$('#new_fee_option #productitemview tbody').append(html);
				
			}); 
			
	$(document).delegate('#new_fee_option .removefeetype', 'click', function(){
		$(this).parent().parent().remove();
		
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		$('#new_fee_option .net_totl').html(price.toFixed(2));
	});
	
	$(document).delegate('#editfeeoption .fee_option_addbtn a', 'click', function(){
		var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Airline Ticket">Airline Ticket</option><option value="Airport Transfer Fee">Airport Transfer Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control installment_amount" name="installment_amount[]"></td><td><input type="number" value="1" class="form-control installment" name="installment[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_terms[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td><input value="1" class="add_quotation" type="checkbox" name="add_quotation[]"> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
		$('#editfeeoption #productitemview tbody').append(html);
				
			}); 
			
	$(document).delegate('#editfeeoption .removefeetype', 'click', function(){
		$(this).parent().parent().remove();
		
		var price = 0;
		$('#editfeeoption .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		$('#editfeeoption .net_totl').html(price.toFixed(2));
	});
	
	
	
	

	
});
</script>
@endsection