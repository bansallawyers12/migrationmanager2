@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<style>
.defaultButton.ghostButton:hover{
	background-color: #9c9c9c!important;
    border-color: #9c9c9c!important;
    color: #fff!important;
    fill: #fff!important;
}

</style>
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
							<h4>All Services</h4>
							<div class="card-header-action">
								<a href="javascript:;" class="btn btn-primary openservicemodal"><i class="fa fa-plus"></i> Add New</a>
							</div>
						</div>
						<div class="card-body">
							<form class="search_form" action="{{URL::to('/admin/services')}}">
								<div class="service_search">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label>Choose Service Category</label>
												<select data-valid="required" id="getpartnertype" class="form-control servselect2" name="cat">
												<?php
												$servicecat = \App\Models\Partner::select('master_category')->distinct()->get()->toArray();
												$catid = array();
												?>
													@foreach(\App\Category::whereIn('id', $servicecat)->get() as $clist)
														<?php
														$catid[] = $clist->id;
														?>
														<option <?php if(isset($_GET['cat']) && $_GET['cat'] == $clist->id){ echo 'selected'; } ?> value="{{$clist->id}}">{{$clist->category_name}}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Search for</label>
												<select data-valid="required" id="partner_type" class="form-control servselect2 " name="sf">
													
													<?php
													if(isset($_GET['cat']) && $_GET['cat'] != ''){
														$c = $_GET['cat'];
													}else{
														$c = @$catid[0];
													}
												$serviceccat = \App\Models\SubCategory::where('cat_id', $c)->get();
												?>
													@foreach($serviceccat as $cslist)
														<option <?php if(isset($_GET['sf']) && $_GET['sf'] == 1){ echo 'selected'; } ?> value="{{$cslist->sub_id}}">{{$cslist->name}}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="search_field">
												<input type="text" name="s" value="{{@$_GET['s']}}" class="form-control" placeholder="Search" />
												<input type="submit" class="btn btn-primary btn-lg" value="Search" />
											</div>
										</div>
									</div>
								</div>
							</form>
							<div class="service_list">
								<?php 	
								if(!empty($lists)){
								if(isset($_GET['sf']) && $_GET['sf'] == '1'){
									foreach ($lists as $servlist){
										$PartnerBranch = \App\Models\PartnerBranch::select('name')->where('partner_id', $servlist->id)->where('is_headoffice', 1)->get()->first();	
										$workflow = \App\Models\Workflow::where('id', $servlist->service_workflow)->first(); 
										$PartnerType = \App\Models\PartnerType::where('id', $servlist->partner_type)->first(); 
										
										?>
										<div class="service_column">
												<div class="service_left">
													<div class="service_img">
														<a href="#"><i data-feather="shopping-cart"></i></a>
													</div>
													<div class="service_content"> 
														<h4><a target="_blank" href="{{URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$servlist->id)))}}"><?php echo @$servlist->partner_name; ?></a></h4>
														<h5><?php echo @$servlist->city.', '.@$servlist->country; ?></h5>
														<span class="serv_office"><?php  echo @$PartnerBranch['name'];  ?></span>
														<div class="course_details">
															<div class="course_row">
																<div class="course_column">
																	<span class="label">Category</span>
																	<span class="value">{{$PartnerType->name}}</span>
																</div>
																<div class="course_column">
																	<span class="label">Enrolled</span>
																	<span class="value">-</span>
																</div>
																<div class="course_column">
																	<span class="label">In Progress</span>
																	<span class="value">-</span>
																</div>
																<div class="course_column">
																	<span class="label">Email</span>
																	{{@$servlist->email}}
																</div>
															</div>
															<div class="course_row">	
																<div class="course_column">
																	<span class="label">Contact Person</span>
																	<span class="value"></span>
																</div>
																<div class="course_column">
																	<span class="label">Contact Email</span>
																	<span class="value"></span>
																</div>
																<div class="course_column">
																	<span class="label">Contact Phone</span>
																	<span class="value">{{$servlist->phone}}</span>
																</div>
																<div class="course_column">
																	<span class="label">Website</span>
																	<span class="value">{{$servlist->website}}</span>
																</div>
															</div>
															<div class="clearfix"></div>
														</div>
													</div>
												</div>
												<div class="service_right">
													<div class="active_promote">
														<span></span>
													</div>
													<div class="inner_serv_rgt">
														<div class="fee_btn">
														<?php
														$subcat = \App\Models\SubCategory::where('cat_id',@$_GET['cat'])->where('sub_id','=','0')->first();
														?>
															<h5 class="text-info">Total {{@$subcat->name}}</h5>
															<h4><?php echo \App\Models\Product::where('partner', $servlist->id)->count(); ?></h4>
														</div>
														<div class="">
															<a href="{{URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$servlist->id)))}}?tab=product">View All {{@$subcat->name}}</a>
														</div>
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="service_actions">
													
													<div class="serv_action_btns">
														<a target="_blank" href="{{URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$servlist->id)))}}" class="btn btn-outline-secondary view_full_detail">View Full Details</a>
														<a servicetype="partner" data-partner-name="{{@$servlist->partner_name}}" data-partner-id="{{@$servlist->id}}" href="javascript:;" data-branch-name="<?php echo @$PartnerBranch->name; ?>" data-branch-id="<?php echo @$PartnerBranch->id; ?>" data-workflow-name="<?php echo @$workflow->name; ?>" data-workflow-id="<?php echo @$workflow->id; ?>" href="javascript:;" class="btn btn-outline-secondary interest_btn add_interested_service">Add To Interested Services</a>
														
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
										<?php
									}
								}else{
									foreach ($lists as $servlist){
										$partnerdetail = \App\Models\Partner::where('id', $servlist->partner)->first();								
										$PartnerBranch = \App\Models\PartnerBranch::where('id', $servlist->branches)->first();	
										$workflow = \App\Models\Workflow::where('id', $partnerdetail->service_workflow)->first();  
										$acreq = \App\Models\AcademicRequirement::where('id', $servlist->id)->first();  
								?>
											<div class="service_column">
												<div class="service_left">
													<div class="service_img">
														<a href="#"><i data-feather="shopping-cart"></i></a>
													</div>
													<div class="service_content"> 
														<h4><a target="_blank" href="{{URL::to('/admin/products/detail/'.base64_encode(convert_uuencode(@$servlist->id)))}}"><?php echo @$servlist->name; ?></a></h4>
														<h5><a target="_blank" href="{{URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$partnerdetail->id)))}}"><?php echo @$partnerdetail->partner_name; ?></a></h5>
														<span class="serv_office"><?php echo @$PartnerBranch->name; ?></span>
														<div class="course_details">
															<div class="course_row">
																<div class="course_column">
																	<span class="label">Degree Level</span>
																	<span class="value"><?php echo @$acreq->degree; ?></span>
																</div>
																<div class="course_column">
																	<span class="label">Subject Area</span>
																	<span class="value">-</span>
																</div>
																<div class="course_column">
																	<span class="label">Requirements</span>
																	<a href="javascript:;" data-toggle="modal" data-target=".requiment_modal" class="">View Requirements</a>
																</div>
															</div>
															<div class="course_row">	
																<div class="course_column">
																	<span class="label">Duration</span>
																	<span class="value"><?php echo $servlist->duration; ?></span>
																</div>
																<div class="course_column">
																	<span class="label">Intake Months</span>
																	<span class="value"><?php echo $servlist->intake_month; ?></span>
																</div>
																<div class="course_column">
																	<span class="label">Workflow</span>
																	<span class="value"><?php echo $workflow->name; ?></span>
																</div>
															</div>
															<div class="clearfix"></div>
														</div>
													</div>
												</div>
												<div class="service_right">
													<div class="active_promote">
														<span></span>
													</div>
													<div class="inner_serv_rgt">
														<div class="fee_btn">
															<select class="form-control change_fee" name="parent">
															<?php
															$feeoptions = \App\Models\FeeOption::where('product_id', $servlist->id)->orderby('created_at', 'ASC')->get();
															foreach($feeoptions as $fee){
															?>
																<option value="{{$fee->id}}">{{$fee->name}}</option>
															<?php } ?>
															</select>
														</div>
														<?php $i=0; foreach($feeoptions as $fee){ 
														$feeoptiontype = \App\Models\FeeOptionType::where('fee_id', $fee->id)->get();
														$totlfee = 0; 
														foreach($feeoptiontype as $feeoptiontyp){
															$totlfee += $feeoptiontyp->total_fee;
														}
														?>
														<div id="show_<?php echo $fee->id; ?>" class="service_price" style="<?php if($i==0){}else{ echo 'display:none;'; } ?>">
															<div class="price">
																AUD <span><?php echo number_format($totlfee,2,'.',''); ?></span>
															</div>
														</div>
														<div id="showf_<?php echo $fee->id; ?>" class="fee_breakdown" style="<?php if($i==0){}else{ echo 'display:none;'; } ?>">
															<table class="table">
																<tbody>
																<?php
																$t = 0.00;
																foreach($feeoptiontype as $feeoptiontyp){
																	$t += $feeoptiontyp->total_fee;
																?>
																	<tr>
																		<td><?php echo $feeoptiontyp->fee_type; ?> <br/><span><?php echo $feeoptiontyp->installment; ?> <?php echo $fee->installment_type; ?> @ AUD <?php echo $feeoptiontyp->inst_amt; ?></span></td>
																		<td>AUD <?php echo $feeoptiontyp->total_fee; ?></td>
																	</tr>
																	<tr>
																		<td colspan="2" style="border-top:1px solid #ccc;padding:4px;"></td>
																	</tr>
																	<tr class="total">
																		<td>Total Amount</td>
																		<td style="color:#6777ef;">AUD <?php echo number_format($t,2,'.',''); ?></td>
																	</tr>
																<?php } ?>
																</tbody>
															</table>
														</div>
									<?php $i++; } ?>
														<div class="breakdown_hd_sh">
															<a href="javascript:;">View Fee Breakdown</a>
														</div>
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="service_actions">
													<div class="serv_process">
														<span>Processed via <span>Super Agent</span></span>
													</div>
													<div class="serv_action_btns">
														<a target="_blank" href="{{URL::to('/admin/products/detail/'.base64_encode(convert_uuencode(@$servlist->id)))}}" class="btn btn-outline-secondary view_full_detail">View Full Details</a>
														<a servicetype="product" data-partner-name="{{@$partnerdetail->partner_name}}" data-partner-id="{{@$partnerdetail->id}}" href="javascript:;" data-product-name="{{@$servlist->name}}" data-product-id="{{@$servlist->id}}" data-branch-name="<?php echo @$PartnerBranch->name; ?>" data-branch-id="<?php echo @$PartnerBranch->id; ?>" data-workflow-name="<?php echo @$workflow->name; ?>" data-workflow-id="<?php echo @$workflow->id; ?>" href="javascript:;" class="btn btn-outline-secondary interest_btn add_interested_service">Add To Interested Services</a>
														<a data-partner-name="{{@$partnerdetail->partner_name}}" data-partner-id="{{@$partnerdetail->id}}" href="javascript:;" data-product-name="{{@$servlist->name}}" data-product-id="{{$servlist->id}}" data-branch-name="<?php echo @$PartnerBranch->name; ?>" data-branch-id="<?php echo @$PartnerBranch->id; ?>" data-workflow-name="<?php echo @$workflow->name; ?>" data-workflow-id="<?php echo $workflow->id; ?>" href="javascript:;" class="btn btn-primary add_application">Add To Application</a>
														
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
								<?php }
								}
								}else{
									echo '<h5 style="text-align: center;">No Data to Display Right Now.
									<br>Search For Data To Be Displayed.</h5>';
								}
								?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade requiment_modal custom_modal" tabindex="-1" role="dialog" aria-labelledby="requiment_ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="requiment_ModalLabel">Requirements</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="academic_require_list custom_require_list">
					<h6>Academic Requirement</h6>
					<div class="require_row">
						<div class="require_col require_col3">
							<span class="span_label">Required Degree Level</span>
							<span class="span_value">-</span>
						</div>
						<div class="require_col require_col3">
							<span class="span_label">Required Score</span>
							<span class="span_value">-</span>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="englishtest_require_list custom_require_list">
					<h6>English Test Scores Requirements</h6>
					<div class="require_row">
						<div class="require_col require_col3">
							<span class="title">TOEFL</span>
							<div class="span_row">
								<span class="span_label">Listening</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Reading</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Writing</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Speaking</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row overall_score">
								<span class="span_label">Overall Score</span>
								<span class="span_value">-</span>
							</div>
						</div>
						<div class="require_col require_col3">
							<span class="title">IELTS</span>
							<div class="span_row">
								<span class="span_label">Listening</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Reading</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Writing</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Speaking</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row overall_score">
								<span class="span_label">Overall Score</span>
								<span class="span_value">-</span>
							</div>
						</div>
						<div class="require_col require_col3">
							<span class="title">PTE</span>
							<div class="span_row">
								<span class="span_label">Listening</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Reading</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Writing</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row">
								<span class="span_label">Speaking</span>
								<span class="span_value">-</span>
							</div>
							<div class="span_row overall_score">
								<span class="span_label">Overall Score</span>
								<span class="span_value">-</span>
							</div>
						</div>
					</div>
					<div class="clearfix"></div> 
				</div>
				<div class="othertest_require_list custom_require_list">
					<h6>Other Test Overall Requirement</h6>
					<div class="require_row">
						<div class="require_col require_col_sm">
							<span class="title">SAT I</span>
							<span class="span_value">-</span>
						</div>
						<div class="require_col require_col_sm">
							<span class="title">SAT II</span>
							<span class="span_value">-</span>
						</div>
						<div class="require_col require_col_sm">
							<span class="title">GRE</span>
							<span class="span_value">-</span>
						</div>
						<div class="require_col require_col_sm">
							<span class="title">GMAT</span>
							<span class="span_value">-</span>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div> 
</div>

<div class="modal fade  custom_modal" id="add_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Add To Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<div style="margin-bottom: 20px;" class="custom-error-popupmsg"></div>
				<form method="post" action="{{URL::to('/admin/savetoapplication')}}" name="addtoapplicationform" id="addtoapplicationform" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="contact">Select Contact <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control contact contactselect2" id="contact" name="contact">
									<option value="">Please Select Contact</option>
									@foreach(\App\Models\Admin::where('role',7)->get() as $wlist)
										<option value="{{$wlist->id}}">{{$wlist->first_name}} {{$wlist->last_name}}
											({{$wlist->email}} {{$wlist->phone}})</option>
									@endforeach
								</select>
								<span class="custom-error contact_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow " id="workflow" name="workflow">
									<option value="">Please Select Workflow</option>
									
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="partner">Select Partner <span class="span_req">*</span></label> 
								<input readonly type="text" name="partnername" class="partnername form-control" >
								<input type="hidden" name="partner_id" class="partner_id" >
								<span class="custom-error partner_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="product">Select Product</label> 
								<input readonly type="text" name="productname" class="productname form-control">
								<input type="hidden" name="product_id" class="product_id" >
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="branch">Select Branch <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control branch" id="branch" name="branch">
									<option value="">Please Select a Partner & Branch</option>
								</select> 
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('addtoapplicationform')" type="button" class="btn btn-primary">Add</button>
							<button onclick="customValidate('addtoapplicationform')" type="button" class="btn btn-primary">Add & Go To Application</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>

<!-- Interested Service Modal -->
<div class="modal fade custom_modal" id="add_interested_service" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add Interested Services</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showintrestedservice">
				 
			</div>
		</div>
	</div>
</div>
<!-- Interested Service Modal -->
<div class="modal fade custom_modal" id="openservicemodal" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add New</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				 <div class="col-md-12">	
					<h5>Partners</h5>
					<a href="{{URL::to('admin/partners/create')}}" style="background-color: transparent;color: #9c9c9c;fill: #9c9c9c;width: 48%;border: 1px solid #9c9c9c;display: inline-flex;" class="btn btn-info defaultButton ghostButton">Add New Partner</a>
				 </div>
				  <div class="col-md-12" style="margin-top: 20px!important;">
				  <h5>Products</h5>
					<a href="{{URL::to('admin/products/create')}}" style="background-color: transparent;color: #9c9c9c;fill: #9c9c9c;width: 48%;border: 1px solid #9c9c9c;display: inline-flex;" class="btn btn-info defaultButton ghostButton">Add New Product</a>
				 </div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('.openservicemodal','click', function(){
		$('#openservicemodal').modal('show');
	});
	$(document).delegate('.add_application','click', function(){
		$('.partnername').val('');
		$('.partner_id').val('');
		$('.productname').val('');
		$('.product_id').val('');
		$('#branch').html('<option value="">Please Select a Partner &amp; Branch</option>');
		$('#workflow').html('<option value="">Please Select Workflow</option>');
		var partnername = $(this).attr('data-partner-name');
		var partnerid = $(this).attr('data-partner-id');
		
		var productname = $(this).attr('data-product-name');
		var productid = $(this).attr('data-product-id');
		
		var branchname = $(this).attr('data-branch-name');
		var branchid = $(this).attr('data-branch-id');
		
		var workflowname = $(this).attr('data-workflow-name');
		var workflowid = $(this).attr('data-workflow-id');
		
		$('.partnername').val(partnername);
		$('.partner_id').val(partnerid);
		$('.productname').val(productname);
		$('.product_id').val(productid);
		
		$('#branch').append('<option value="'+branchid+'">'+branchname+'</option>');
		$('#workflow').append('<option value="'+workflowid+'">'+workflowname+'</option>');
		$('#add_application').modal('show');
	});	
	 $(".contactselect2").select2({
			dropdownParent: $("#add_application .modal-content")
		}); 
		
		$(".contact.select2").select2({
			dropdownParent: $("#add_interested_service .modal-content")
		}); 
	$(".servselect2").select2();
	$('.breakdown_hd_sh a').on('click', function(){
		$(this).parent().parent().toggleClass('active');
		//$('.inner_serv_rgt');
		if($(this).html()=="View Fee Breakdown"){
			$(this).html("Hide Fee Breakdown");
		}
		else{
			$(this).html("View Fee Breakdown");
		}
	});
		
	$(document).delegate('.change_fee','change', function(){	
		var v = $('.change_fee option:selected').val();
		$('#show_'+v).parent().find('.service_price').hide();
		$('#show_'+v).parent().find('.fee_breakdown').hide();
		$('#show_'+v).show();
		$('#showf_'+v).show();
	});
	$(document).delegate('#getpartnertype','change', function(){	
		$('.popuploader').show();
		var v = $('#getpartnertype option:selected').val();
		$.ajax({
			url: '{{URL::to('/admin/getsubcategories')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#partner_type').html(response);
			}
		});
	});
	$(document).delegate('.productselect2','change', function(){	
		$('.popuploader').show();
		var v = $('.productselect2 option:selected').val();
		$.ajax({
			url: '{{URL::to('/admin/getproductbranch')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_branch').html(response);
			}
		});
	});
	
	$(document).delegate('.add_interested_service','click', function(){
		$('.servpartnername').val('');
		$('.servpartner_id').val('');
		$('.servproductname').val('');
		$('.servproduct_id').val('');
		$('#intrested_branch').html('<option value="">Please Select a Branch</option>');
		$('#intrested_workflow').html('<option value="">Please Select Workflow</option>');
		var partnername = $(this).attr('data-partner-name');
		var partnerid = $(this).attr('data-partner-id');
		var servicetype = $(this).attr('servicetype');
		
		var productname = $(this).attr('data-product-name');
		var productid = $(this).attr('data-product-id');
		
		var branchname = $(this).attr('data-branch-name');
		var branchid = $(this).attr('data-branch-id');
		
		var workflowname = $(this).attr('data-workflow-name');
		var workflowid = $(this).attr('data-workflow-id');
		
		
		 $('#add_interested_service').modal('show');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getservicemodal')}}',
			type:'GET',
			data:{servicetype:servicetype,partnerid:partnerid},
			success:function(response){
				$('.popuploader').hide();
				$('.showintrestedservice').html(response);
				$('.servpartnername').val(partnername);
				$('.servpartner_id').val(partnerid);
				$('.servproductname').val(productname);
				$('.servproduct_id').val(productid);
				if(servicetype == 'product'){
					$('#intrested_branch').append('<option value="'+branchid+'">'+branchname+'</option>');
				}
				$('#intrested_workflow').append('<option value="'+workflowid+'">'+workflowname+'</option>');
				
				$(".productselect2").select2({
					dropdownParent: $("#add_interested_service .modal-content")
				}); 
				$(".contact.select2").select2({
			dropdownParent: $("#add_interested_service .modal-content")
		}); 
				 $(".datepicker").daterangepicker({
				locale: { format: "YYYY-MM-DD" },
				singleDatePicker: true,
				showDropdowns: true,
				drops : 'up',
				 container: '#add_interested_service modal-body',

			  });
			}
		});
	});	
});
</script> 
@endsection