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
							<h4>Partner Detail</h4>
							<div class="card-header-action">
								<a href="{{route('admin.partners.index')}}" class="btn btn-primary">Partner List</a>
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
							<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>{{substr($fetchedData->partner_name, 0, 1)}}</b></span>
								<div class="clearfix"></div>
								<div class="author-box-name">
									<a href="#">{{$fetchedData->partner_name}}</a>
								</div>
								<div class="author-mail_sms">
									<a href="#" title="Compose SMS"><i class="fas fa-comment-alt"></i></a>
									<a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->partner_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>
									<a href="{{URL::to('/admin/partners/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>

									@if($fetchedData->is_archived == 0)
										<a class="arcivedval" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'partners')" title="Archive"><i class="fas fa-archive"></i></a>
									@else
										<a class="arcivedval" style="background-color:red;" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'partners')" title="UnArchive"><i style="color: #fff;" class="fas fa-archive"></i></a>
									@endif
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
								<span class="float-left">Phone No:</span>
								<span class="float-right text-muted">{{$fetchedData->phone}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Fax:</span>
								<span class="float-right text-muted">{{$fetchedData->fax}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Email:</span>
								<span class="float-right text-muted">{{$fetchedData->email}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Address:</span>
								<span class="float-right text-muted">{{$fetchedData->address}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Website:</span>
								<span class="float-right text-muted">{{$fetchedData->website}}</span>
							</p>
							<?php

							$workflows = \App\Models\Workflow::where('id', $fetchedData->service_workflow)->first();
							?>

							<p class="clearfix">
								<span class="float-left">Services:</span>
								<span class="float-right text-muted">{{@$workflows->name}}</span>
							</p>

							<p class="clearfix">
								<span class="float-left">Added On:</span>
								<span class="float-right text-muted">{{date('Y-m-d', strtotime($fetchedData->created_at))}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Business Registration Number:</span>
								<span class="float-right text-muted">{{$fetchedData->business_reg_no}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Currency code:</span>
								<span class="float-right text-muted">{{$fetchedData->currency}}</span>
							</p>

						</div>
					</div>
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link <?php if(!isset($_GET['tab'])){ echo 'active'; } ?>" data-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link <?php if(isset($_GET['tab']) && $_GET['tab'] == 'product'){ echo 'active'; } ?>" data-toggle="tab" id="products-tab" href="#products" role="tab" aria-controls="products" aria-selected="false">Products</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="branches-tab" href="#branches" role="tab" aria-controls="branches" aria-selected="false">Branches</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="agreements-tab" href="#agreements" role="tab" aria-controls="agreements" aria-selected="false">Agreements</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="contacts-tab" href="#contacts" role="tab" aria-controls="contacts" aria-selected="false">Contacts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="accounts-tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="false">Accounts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="conversations-tab" href="#conversations" role="tab" aria-controls="conversations" aria-selected="false">Conversations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="tasks-tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">Tasks</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="other_info-tab" href="#other_info" role="tab" aria-controls="other_info" aria-selected="false">Other Information</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="promotions-tab" href="#promotions" role="tab" aria-controls="promotions" aria-selected="false">Promotions</a>
								</li>
							</ul>
							<div class="tab-content" id="partnerContent" style="padding-top:15px;">
								<div class="tab-pane fade  <?php if(!isset($_GET['tab']) ){ echo 'show active'; } ?>" id="application" role="tabpanel" aria-labelledby="application-tab">


									<?php
									$appprogresscount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',0)->count();
									$appcompletecount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',1)->count();
									$appdisccount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',2)->count();
									$appenrolcount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',3)->count();
									?>
									<div class="row">
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">IN PROGRESS</h5>
															<h2 class="mb-3 font-18">{{$appprogresscount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">COMPLETED</h5>
															<h2 class="mb-3 font-18">{{$appcompletecount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">DISCONTINUED</h5>
															<h2 class="mb-3 font-18">{{$appdisccount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">ENROLLED</h5>
															<h2 class="mb-3 font-18">{{$appenrolcount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-12 col-sm-12 col-lg-12">
											<div class="card">
												<div class="card-body">
													<div class="summary">
														<div class="summary-chart active" data-tab-group="summary-tab" id="summary-chart">
															<canvas id="myChart" height="180"></canvas>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="table-responsive if_applicationdetail">
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Assignee</th>
													<th>Product Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Status</th>
													<th>Added On</th>
													<th>Last Updated</th>

												</tr>
											</thead>
											<tbody class="applicationtdata">
											<?php
											 foreach(\App\Models\Application::where('partner_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
				$partnerdetail = \App\Models\Admin::where('id', $alist->client_id)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
				$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();
												?>
												<tr id="id_{{$alist->id}}">
													<td><a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$partnerdetail->id)))}}">{{$partnerdetail->first_name}} {{$partnerdetail->last_name}}</a></td>
													<td></td>
													<td>{{$productdetail->name}}</td>
													<td>{{$workflow->name}}</td>
													<td>{{$alist->stage}}</td>
													<td>
													@if($alist->status == 0)
														<span class="badge badge-info">In Progress</span>
													@elseif($alist->status == 1)
														<span class="badge badge-success">Completed</span>
													@elseif($alist->status == 2)
														<span class="badge badge-success">Discontinued</span>
													@else
													@endif
													</td>
											 <td>{{date('Y-m-d', strtotime($alist->created_at))}}</td>
											 <td>{{date('Y-m-d', strtotime($alist->updated_at))}}</td>
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
									<div class="ifapplicationdetailnot" style="display:none;">
										<h4>Please wait ...</h4>
									</div>
								</div>
								<div class="tab-pane fade <?php if(isset($_GET['tab']) && $_GET['tab'] == 'product'){ echo 'show active'; } ?>" id="products" role="tabpanel" aria-labelledby="products-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="{{route('admin.products.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive">
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>Product Name</th>
													<th>Sync</th>
													<th>Branches</th>
													<th>In Progress</th>
													<th></th>
												</tr>
											</thead>
											<tbody class="applicationtdata">
											<?php
											$products = \App\Models\Product::where('partner', $fetchedData->id)->orderby('created_at', 'DESC')->get();
											foreach($products as $product){
											?>
												<tr id="id_{{@$product->id}}">
													<td>{{$product->name}}</td>
													<td></td>
													<?php
													$bname = array();
													if($product->branches != ''){
														$branches = \App\Models\PartnerBranch::whereIn('id', explode(',',$product->branches))->get();
														foreach($branches as $b){
															$bname[] = $b->name;
														}
													}
													?>
													<td>{{implode(', ', $bname)}}</td>

													<?php
													$countapplication = \App\Models\Application::where('product_id', $product->id)->where('status', 0)->count();
													?>
													<td>{{$countapplication}}</td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="{{URL::to('/admin/products/detail/'.base64_encode(convert_uuencode(@$product->id)))}}"><i class="far fa-eye"></i> View</a>
																<a class="dropdown-item has-icon" href="{{URL::to('/admin/products/edit/'.base64_encode(convert_uuencode(@$product->id)))}}"><i class="far fa-edit"></i> Edit</a>
																<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$product->id}}, 'products')"><i class="fas fa-trash"></i> Delete</a>
															</div>
														</div>
													</td>
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade" id="branches" role="tabpanel" aria-labelledby="branches-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" class="btn btn-primary openbranchnew"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="branch_term_list">
									<?php
										$branchesquery = \App\Models\PartnerBranch::where('partner_id', $fetchedData->id)->orderby('created_at', 'DESC');
										$branchescount = $branchesquery->count();
										$branches = $branchesquery->get();
										if($branchescount !== 0){
										foreach($branches as $branch){
									?>
										<div class="branch_col" id="contact_">
											<div class="branch_content">
												<h4><?php echo $branch->name; ?></h4>
												<div class="" style="margin-top: 15px!important;">
													<p><i class="fa fa-map-marker-alt" style="margin-right: 10px!important;"></i> <?php echo $branch->city; ?>, <?php echo $branch->a; ?></p>
												</div>
											</div>
											<div class="extra_content">
												<div class="left">
													<p><i class="fa fa-phone" style="margin-right: 20px!important;"></i> <?php if($branch->phone != ''){ echo $branch->phone; }else{ echo '-'; } ?></p>
													<p><i class="fa fa-envelope-o" style="margin-right: 20px!important;"></i> <?php if($branch->email != ''){ echo $branch->email; }else{ echo '-'; } ?></p>
												</div>
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item openbranchform" data-id="{{$branch->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$branch->id}}" data-href="deletebranch" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php }
										}else{
											?>
											<h4>No Record Found</h4>
											<?php
										}
										?>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade" id="agreements" role="tabpanel" aria-labelledby="agreements-tab">
									<div class="agreement_info">
										<h4>Contact Information</h4>
										<form method="post"  action="{{URL::to('/admin/partner/saveagreement')}}" autocomplete="off" name="saveagreement" id="saveagreement" enctype="multipart/form-data">
										@csrf
										<input type="hidden" name="partner_id" value="{{$fetchedData->id}}">
										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label for="contract_expiry">Contract Expiry Date</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{{ Form::text('contract_expiry', @$fetchedData->contract_expiry, array('class' => 'form-control contract_expiry', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
														@if ($errors->has('contract_expiry'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('contract_expiry') }}</strong>
															</span>
														@endif
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="represent_region">Representing Regions</label>
													<select class="form-control represent_region select2" multiple name="represent_region[]" >
														<option value="">Select</option>
														<?php
														$represent_region = explode(',',$fetchedData->represent_region);
														foreach(\App\Models\Country::all() as $list){
															?>
															<option <?php if(in_array($list->name, $represent_region)){ echo 'selected'; } ?> value="{{@$list->name}}">{{@$list->name}}</option>
															<?php
														}
														?>
													</select>
													@if ($errors->has('represent_region'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('represent_region') }}</strong>
														</span>
													@endif
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="commission_percentage">Commission Percentage</label>
													{{ Form::number('commission_percentage', @$fetchedData->commission_percentage, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Commission Percentage', 'step'=>'0.01' )) }}
													@if ($errors->has('commission_percentage'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('commission_percentage') }}</strong>
														</span>
													@endif
												</div>
											</div>
											<div class="col-md-1">
												<div class="form-group">
													<label for="gst">GST</label>
													<input type="checkbox" <?php if(@$fetchedData->gst == 1){ echo 'checked'; } ?> name="gst" value="1">
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="default_super_agent">Default Super Agent</label>
													<select class="form-control default_super_agent select2" name="default_super_agent" >
														<option value="">Select</option>
													</select>
													@if ($errors->has('default_super_agent'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('default_super_agent') }}</strong>
														</span>
													@endif
												</div>
											</div>

                                            <div class="col-md-4">
												<div class="form-group">
													<label for="default_super_agent">Document upload</label>
													<input type="file" name="file_upload"/>
												</div>

                                                <?php
                                                if( isset($fetchedData->file_upload ) && $fetchedData->file_upload != ""){
                                                    $file_url = "http://127.0.0.1:8000/img/documents/".$fetchedData->file_upload;
                                                ?>
                                                    <a href="<?php echo $file_url;?>" target="_blank"><?php echo $fetchedData->file_upload;?></a>
                                                <?php
                                                }
                                                ?>
											</div>

											<div class="col-12 col-md-12 col-lg-12">
												<div class="form-group float-right">
													<button onclick="customValidate('saveagreement')" type="button" class="btn btn-primary">Save Changes</button>
												</div>
											</div>
										</div>
										</form>
									</div>

									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary add_clientcontact"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="contact_term_list">
									<?php

									$querycontactlist = \App\Models\Contact::where('user_id', $fetchedData->id)->orderby('created_at', 'DESC');
									$contactlistcount = $querycontactlist->count();
									$contactlist = $querycontactlist->get();
									if($contactlistcount !== 0){
									foreach($contactlist as $clist){
										$branch = \App\Models\PartnerBranch::where('id', $clist->branch)->first();
									?>
										<div class="note_col" id="contact_{{$clist->id}}" style="width:33.33333333%">
											<div class="note_content">
												<h4>{{$clist->name}}</h4>
												<p><span class="text-semi-bold"><?php if($clist->position != ''){ echo $clist->position; }else{ echo '-'; } ?></span> In <span class="text-semi-bold"><?php if($clist->department != ''){ echo $clist->department; }else{ echo '-'; } ?></span></p>
												<div class="" style="margin-top: 15px!important;">
													<p><i class="fa fa-phone"></i> <?php if($clist->contact_phone != ''){ echo $clist->contact_phone; }else{ echo '-'; } ?></p>
													<p style="margin-top: 5px!important;"><i class="fa fa-fax"></i> <?php if($clist->fax != ''){ echo $clist->fax; }else{ echo '-'; } ?></p>
													<p style="margin-top: 5px!important;"><i class="fa fa-mail"></i> <?php if($clist->contact_email != ''){ echo $clist->contact_email; }else{ echo '-'; } ?></p>
												</div>
											</div>
											<div class="extra_content">
												<div class="left">
													<i class="fa fa-map-marker" style="margin-right: 20px!important;"></i>
													<?php echo $branch->city; ?>, <?php echo $branch->country; ?>
												</div>
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opencontactform" data-id="{{$clist->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$clist->id}}" data-href="deletecontact" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php }
									}else{
										echo '<h4>Record not found</h4>';
									}									?>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade" id="noteterm" role="tabpanel" aria-labelledby="noteterm-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" datatype="note" class="create_note btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="note_term_list">

									<?php

									$querynotelist = \App\Models\Note::where('client_id', $fetchedData->id)->where('type', 'partner')->orderby('pin', 'DESC');
									$notelistcount = $querynotelist->count();
									$notelist = $querynotelist->get();
									if($notelistcount !== 0){
									foreach($notelist as $list){
										$admin = \App\Models\Admin::where('id', $list->user_id)->first();

									?>
										<div class="note_col" id="note_id_{{$list->id}}">
											<div class="note_content">
												<h4><a class="viewnote" data-id="{{$list->id}}" href="javascript:;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...') }}</a></h4>
												<p><?php //echo @$list->description == "" ? config('constants.empty') : str_limit(@$list->description, '15', '...'); ?></p>
											</div>
											<div class="extra_content">
												<div class="left">
													<div class="author">
														<a href="#">{{substr($admin->first_name, 0, 1)}}</a>
													</div>
													<div class="note_modify">
														<small>Last Modified <span>{{date('Y-m-d', strtotime($list->updated_at))}}</span></small>
													</div>
												</div>
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
															<?php if($list->pin == 1){
									?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
									<?php
								}else{ ?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
								<?php } ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php }
									}else{
										echo '<h4>No Record Found</h4>';
									}
									?>
									</div>
									<div class="clearfix"></div>
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
											<input type="hidden" name="type" value="partner">
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
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('type','partner')->orderby('created_at', 'DESC')->get();
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
								<div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" class="btn btn-primary createaddapointment"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="appointmentlist">
										<div class="row">
											<div class="col-md-5 appointment_grid_list">
												<?php
												$rr=0;
												$appointmentdata = array();
												$appointmentlists = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'partner')->orderby('created_at', 'DESC')->get();
												$appointmentlistslast = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'partner')->orderby('created_at', 'DESC')->first();
												foreach($appointmentlists as $appointmentlist){
													$admin = \App\Models\Admin::where('id', $appointmentlist->user_id)->first();
													$datetime = $appointmentlist->created_at;
													$timeago = Controller::time_elapsed_string($datetime);

													$appointmentdata[$appointmentlist->id] = array(
														'title' => $appointmentlist->title,
														'time' => date('H:i A', strtotime($appointmentlist->time)),
														'date' => date('d D, M Y', strtotime($appointmentlist->date)),
														'description' => $appointmentlist->description,
														'createdby' => substr($admin->first_name, 0, 1),
														'createdname' => $admin->first_name,
														'createdemail' => $admin->email,
													);
												?>
												<div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
													<div class="appointment_col">
														<div class="appointdate">
															<h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
															<p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
															<i><small><?php echo $timeago ?></small></i></p>
														</div>
														<div class="title_desc">
															<h5><?php echo $appointmentlist->title; ?></h5>
															<p><?php echo $appointmentlist->description; ?></p>
														</div>
														<div class="appoint_created">
															<span class="span_label">Created By:
															<span>{{substr($admin->first_name, 0, 1)}}</span></span>
															<div class="dropdown d-inline dropdown_ellipsis_icon">
																<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
																<div class="dropdown-menu">
																	<a class="dropdown-item edit_appointment" data-id="{{$appointmentlist->id}}" href="javascript:;">Edit</a>
																	<a data-id="{{$appointmentlist->id}}" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
																</div>
															</div>
														</div>
													</div>
												</div>
												<?php $rr++; } ?>
											</div>
											<div class="col-md-7">
												<div class="editappointment">
												@if($appointmentlistslast)
													<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php echo @$appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>
													<?php
													$adminfirst = \App\Models\Admin::where('id', @$appointmentlistslast->user_id)->first();
													?>
													<div class="content">
														<h4 class="appointmentname"><?php echo @$appointmentlistslast->title; ?></h4>
														<div class="appitem">
															<i class="fa fa-clock"></i>
															<span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime(@$appointmentlistslast->time)); ?></span>
														</div>
														<div class="appitem">
															<i class="fa fa-calendar"></i>
															<span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime(@$appointmentlistslast->date)); ?></span>
														</div>
														<div class="description appointmentdescription">
															<p><?php echo @$appointmentlistslast->description; ?></p>
														</div>
														<div class="created_by">
															<span class="label">Created By:</span>
															<div class="createdby">
																<span class="appointmentcreatedby"><?php echo substr(@$adminfirst->first_name, 0, 1); ?></span>
															</div>
															<div class="createdinfo">
																<a href="" class="appointmentcreatedname"><?php echo @$adminfirst->first_name ?></a>
																<p class="appointmentcreatedemail"><?php echo @$adminfirst->primary_email; ?></p>
															</div>
														</div>
													</div>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">

									<div class="table-responsive">
										<table class="table invoicetable text_wrap">
											<thead>
												<tr>
													<th>Invoice No.</th>
													<th>Issue Date</th>
													<th>Service</th>
													<th>Invoice Amount</th>
													<th>Paid Amount</th>
													<th>Status</th>
													<th>Actions</th>
												</tr>
											</thead>
											<tbody class="tdata invoicedatalist">
												<?php
												$applications = \App\Models\Application::where('partner_id',$fetchedData->id)->get();

												foreach($applications as $application){
													$invoicelists = \App\Models\Invoice::where('application_id',$application->id)->orderby('created_at','DESC')->get();

														foreach($invoicelists as $invoicelist){
															if($invoicelist->type == 3){
																$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
															}else{
																$applicationdata = \App\Models\Application::where('id', $invoicelist->application_id)->first();
																$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
																$partnerdata = \App\Models\Partner::where('id', $applicationdata->partner_id)->first();
															}
															$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicelist->id)->orderby('id','ASC')->get();
															$netamount = 0;
															$coom_amt = 0;
															$total_fee = 0;
															foreach($invoiceitemdetails as $invoiceitemdetail){
																$netamount += $invoiceitemdetail->netamount;
																$coom_amt += $invoiceitemdetail->comm_amt;
																$total_fee += $invoiceitemdetail->total_fee;
															}

													$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicelist->id)->orderby('created_at', 'DESC')->get();
													$amount_rec = 0;
													foreach($paymentdetails as $paymentdetail){
														$amount_rec += $paymentdetail->amount_rec;
													}
													if($invoicelist->type == 1){
														$totaldue = $total_fee - $coom_amt;
													} if($invoicelist->type == 2){
														$totaldue = $netamount - $amount_rec;
													}else{
														$totaldue = $netamount - $amount_rec;
													}


												?>
												<tr id="iid_{{$invoicelist->id}}">
													<td>{{$invoicelist->id}}</td>
													<td>{{$invoicelist->invoice_date}}
													<?php if($invoicelist->type == 1){
														$rtype = 'Net Claim';
													}else if($invoicelist->type == 2){
														$rtype = 'Gross Claim';
													}else{
														$rtype = 'General';
													} ?>
													<span title="{{$rtype}}" class="ui label zippyLabel">{{$rtype}}</span></td>
													<td>{{@$workflowdaa->name}}<br>{{@$partnerdata->partner_name}}</td>
													<td>AUD {{$invoicelist->net_fee_rec}}</td>
													<td>{{$amount_rec}}</td>

													<td>
													@if($invoicelist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Paid</span></td>
													@else
														<span class="ag-label--circular" style="color: #ed5a5a" >UnPaid</span></td>
													@endif
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('admin/invoice/view/')}}/{{$invoicelist->id}}">View</a>
																<?php if($invoicelist->status == 0){ ?>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('admin/invoice/edit/')}}/{{$invoicelist->id}}">Edit</a>
																<a data-netamount="{{$netamount}}" data-dueamount="{{$totaldue}}" data-invoiceid="{{$invoicelist->id}}" class="dropdown-item has-icon addpaymentmodal" href="javascript:;"> Make Payment</a>
																<?php } ?>
															</div>
														</div>
													</td>
												</tr>
												<?php }

												}
												?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="conversations" role="tabpanel" aria-labelledby="conversations-tab">
									<div class="conversation_tabs">
										<ul class="nav nav-pills round_tabs" id="client_tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Email</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" id="sms-tab" href="#sms" role="tab" aria-controls="sms" aria-selected="false">SMS</a>
											</li>
										</ul>
										<div class="tab-content" id="conversationContent">
											<div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
											<?php

											$mailreports = \App\Models\MailReport::whereRaw('FIND_IN_SET("'.$fetchedData->id.'", to_mail)')->where('type','partner')->orderby('created_at', 'DESC')->get();

											foreach($mailreports as $mailreport){
												$admin = \App\Models\Admin::where('id', $mailreport->user_id)->first();

												$client = \App\Models\Partner::Where('id', $fetchedData->id)->first();
			$subject = str_replace('{Client First Name}',$client->partner_name, $mailreport->subject);
			$message = $mailreport->message;
			$message = str_replace('{Client First Name}',$client->partner_name, $message);
			$message = str_replace('{Client Assignee Name}',$client->partner_name, $message);
			$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
											?>
				<div class="conversation_list" >
					<div class="conversa_item">
						<div class="ds_flex">
							<div class="title">
								<span>{{$subject}}</span>
							</div>
							<div class="conver_action">
								<div class="date">
									<span>{{date('h:i A', strtotime($mailreport->created_at))}}</span>
								</div>
								<div class="conver_link">
									<a datamailid="{{$mailreport->id}}" datasubject="{{$subject}}" class="create_note" datatype="mailnote" href="javascript:;" ><i class="fas fa-file-alt"></i></a>
									<a datamailid="{{$mailreport->id}}" datasubject="{{$subject}}" href="javascript:;" class="opentaskmodal"><i class="fas fa-shopping-bag"></i></a>
								</div>
							</div>
						</div>
						<div class="email_info">
							<div class="avatar_img">
								<span>{{substr($admin->first_name, 0, 1)}}</span>
							</div>
							<div class="email_content">
								<span class="email_label">Sent by:</span>
								<span class="email_sentby"><strong>{{@$admin->first_name}}</strong> [{{$mailreport->from_mail}}]</span>
								<span class="label success">Delivered</span>
								<span class="span_desc">
									<span class="email_label">Sent by</span>
									<span class="email_sentby"><i class="fa fa-angle-left"></i>{{$client->email}}<i class="fa fa-angle-right"></i></span>
								</span>
							</div>
						</div>
						<div class="divider"></div>
						<div class="email_desc">
						{!!$message!!}
						</div>
						<!--<div class="divider"></div>
						<div class="email_attachment">
							<span class="attach_label"><i class="fa fa-link"></i> Attachments:</span>
							<div class="attach_file_list">
								<div class="attach_col">
									<a href="#">quotation_6.pdf</a>
								</div>
							</div>
						</div> -->
					</div>
				</div>
											<?php } ?>
											</div>
											<div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
												<span>sms</span>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary opencreate_task"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive">
										<table id="my-datatable" class="table-2 table text_wrap">
											<thead>
												<tr>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
												</tr>
											</thead>
											<tbody class="taskdata ">
											<?php
											foreach(\App\Task::where('client_id', $fetchedData->id)->where('type','partner')->orderby('created_at','Desc')->get() as $alist){
												$admin = \App\Models\Admin::where('id', $alist->user_id)->first();
												?>
												<tr class="opentaskview" style="cursor:pointer;" id="{{$alist->id}}">
													<td></td>
													<td><b>{{$alist->category}}</b>: {{$alist->title}}</td>
													<td><span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($admin->first_name, 0, 1); ?></span></td>
													<td>{{$alist->priority}}</td>
													<td><i class="fa fa-clock"></i> {{$alist->due_date}} {{$alist->due_time}}</td>
													<td><?php
													if($alist->status == 3){
														echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
													}else if($alist->status == 1){
														echo '<span style="color: rgb(3, 169, 244); width: 84px;">In Progress</span>';
													}else if($alist->status == 2){
														echo '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
													}else{
														echo '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
													}
													?></td>

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
								<div class="tab-pane fade" id="other_info" role="tabpanel" aria-labelledby="other_info-tab">
									<span>other_info</span>
								</div>
								<div class="tab-pane fade" id="promotions" role="tabpanel" aria-labelledby="promotions-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary add_promotion"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="promotionlists">
									<?php
									$promotionslist = \App\Models\Promotion::where('partner_id',$fetchedData->id)->orderby('created_at','DESC')->get();
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
												<div class="left">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item openpromotonform" data-id="{{$promotion->id}}" href="javascript:;">Edit</a>
														</div>
													</div>
												</div>
												<div class="right">
													<div class="custom-switches">
														<label class="custom-switch">
															<input type="checkbox" data-status="<?php echo $promotion->status; ?>" data-id="{{$promotion->id}}" name="custom-switch-checkbox" class="custom-switch-input changepromotonstatus" @if($promotion->status == 1) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
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

@include('Admin/partners/addpartnermodal')
@include('Admin/partners/editpartnermodal')

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
				<input type ="hidden" value="partner" name="type">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}

									?>
								</select>
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

<div class="modal fade addbranch custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Add New Branch</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="branchform" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_name">Name <span class="span_req">*</span></label>
								{{ Form::text('branch_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
								<span class="custom-error branch_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_email">Email <span class="span_req">*</span></label>
								{{ Form::text('branch_email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' )) }}
									<span class="custom-error branch_email_error" role="alert">
										<strong></strong>
									</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_country">Country</label>
							<select class="form-control branch_country select2" name="branch_country" >
								<option value="">Select</option>
								<?php
								foreach(\App\Models\Country::all() as $list){
									?>
									<option value="{{@$list->name}}">{{@$list->name}}</option>
									<?php
								}
								?>
							</select>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_city">City</label>
								{{ Form::text('branch_city', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter City' )) }}
								@if ($errors->has('branch_city'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_city') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_state">State</label>
								{{ Form::text('branch_state', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' )) }}
								@if ($errors->has('branch_state'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_state') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_address">Street</label>
								{{ Form::text('branch_address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Street' )) }}
								@if ($errors->has('branch_address'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_address') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_zip">Zip Code</label>
								{{ Form::text('branch_zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' )) }}
								@if ($errors->has('branch_zip'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_zip') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_phone">Phone</label>
								<div class="cus_field_input">
									<div class="country_code">
										<input class="telephone" id="telephone" type="tel" value="+61" name="brnch_country_code" readonly >
									</div>
									{{ Form::text('branch_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )) }}
									@if ($errors->has('branch_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('branch_phone') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary savebranch">Save</button>
							<button type="button" id="update_branch" style="display:none" class="btn btn-primary">Update</button>
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
$(document).delegate('input[name="apply_to"]', 'change', function () {
	var v = $('input[name="apply_to"]:checked').val();
	if(v == 'All Products'){
		$('.ifselectproducts').hide();
		$('.productselect2').attr('data-valid', '');
	}else{
		$('.ifselectproducts').show();
		$('.productselect2').attr('data-valid', 'required');
	}
});
	$('.productselect2').select2({
  placeholder: "Select Product",
  multiple: true,
      width: "100%"
});

	$(document).delegate('.opencreate_task', 'click', function () {
	$('#tasktermclientform')[0].reset();
	$('#tasktermclientform select').val('').trigger('change');
	$('.create_task').modal('show');
	$('.ifselecttask').hide();
	$('.ifselecttask select').attr('data-valid', '');

});
	$(document).delegate('.edit_appointment', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#edit_appointment').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/partner/getAppointmentdetail')}}',
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
	<?php
       $json = json_encode ( $appointmentdata, JSON_FORCE_OBJECT );
   ?>
$(document).delegate('.appointmentdata', 'click', function () {
	var v = $(this).attr('data-id');
$('.appointmentdata').removeClass('active');
$(this).addClass('active');
	var res = $.parseJSON('<?php echo $json; ?>');

	$('.appointmentname').html(res[v].title);
	 $('.appointmenttime').html(res[v].time);
	$('.appointmentdate').html(res[v].date);
	$('.appointmentdescription').html(res[v].description);
	$('.appointmentcreatedby').html(res[v].createdby);
	$('.appointmentcreatedname').html(res[v].createdname);
	$('.appointmentcreatedemail').html(res[v].createdemail);
	$('.editappointment .edit_link').attr('data-id', v);
});
	$(document).delegate('.createaddapointment', 'click', function(){
	$('#create_appoint').modal('show');
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
	$(document).delegate('.viewnote', 'click', function(){
		$('#view_note').modal('show');
		var v = $(this).attr('data-id');
		$('#view_note input[name="noteid"]').val(v);
			$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/viewnotedetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);

				if(res.status){
					$('#view_note .modal-body .note_content h5').html(res.data.title);
					$("#view_note .modal-body .note_content p").html(res.data.description);
					var ad = res.data.admin;
					$("#view_note .modal-body .note_content .author").html('<a href="#">'+ad+'</a>');
					var updated_at = res.data.updated_at;
					$("#view_note .modal-body .note_content .lastdate").html('<a href="#">'+updated_at+'</a>');

				}
			}
		});
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
	$(document).delegate('.add_clientcontact','click', function(){
		$('#add_clientcontact #appliationModalLabel').html('Add Contact');

		$('#add_clientcontact input[name="contact_id"]').val('');
		$('#add_clientcontact #telephone').val('+61');
		$('#add_clientcontact #primary_contact').prop('checked', false);
		$('#add_clientcontact .allinputfields input').val('');
		$('#add_clientcontact .allinputfields select').val('');
		$('#add_clientcontact').modal('show');
	});

	$(document).delegate('.openbranchnew','click', function(){
		$('#add_clientbranch #appliationModalLabel').html('Add new branch');

		$('#add_clientbranch input[name="branch_id"]').val('');
		$('#add_clientbranch #telephone').val('+61');
		$('#add_clientbranch #head_office').prop('checked', false);
		$('#add_clientbranch .allinputfields input').val('');
		$('#add_clientbranch .allinputfields select').val('');
		$('#add_clientbranch').modal('show');

	});
	<?php
	if(@$fetchedData->contract_expiry != ''){
		?>
		 $('.contract_expiry').val('{{@$fetchedData->contract_expiry}}');
		<?php
	}else{
		?>
		 $('.contract_expiry').val('');
		<?php
	}

	?>
	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/partner/get-task-detail',
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
		data:{clientid:'{{$fetchedData->id}}',type:'partner'},
		success: function(responses){
			$('.popuploader').hide();
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
			data:{note_id:notid,type:'partner'},
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirmModal').modal('hide');
				if(res.status){
					$('#note_id_'+notid).remove();
					if(delhref == 'deletedocs'){
						$('.documnetlist #id_'+notid).remove();
					}
					else if(delhref == 'deleteservices'){
						$.ajax({
						url: site_url+'/admin/get-services',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){

							$('.interest_serv_list').html(responses);
						}
					});
					}else if(delhref == 'deleteappointment'){
						$.ajax({
						url: site_url+'/admin/partner/get-appointments',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){

							$('.appointmentlist').html(responses);
						}
					});
					}else if(delhref == 'deletecontact'){
						$.ajax({
						url: site_url+'/admin/get-contacts',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){

							$('.contact_term_list').html(responses);
						}
					});
					}else if(delhref == 'deletebranch'){
						$.ajax({
						url: site_url+'/admin/get-branches',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){

							$('.branch_term_list').html(responses);
						}
					});
					}else{
						getallnotes();
					}

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

	$(document).delegate('.opencontactform', 'click', function(){
		$('#add_clientcontact').modal('show');
		$('#add_clientcontact #appliationModalLabel').html('Edit Contact');
		var v = $(this).attr('data-id');
		$('#add_clientcontact input[name="contact_id"]').val(v);
			$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getcontactdetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);

				if(res.status){
					$('#add_clientcontact input[name="name"]').val(res.data.name);
					$('#add_clientcontact input[name="email"]').val(res.data.contact_email);
					$('#add_clientcontact input[name="phone"]').val(res.data.contact_phone);
					$('#add_clientcontact input[name="fax"]').val(res.data.fax);
					$('#add_clientcontact input[name="telephone"]').val(res.data.countrycode);
					$('#add_clientcontact input[name="department"]').val(res.data.department);
					$('#add_clientcontact input[name="position"]').val(res.data.position);
					$('#add_clientcontact #branch').val(res.data.branch);
					if(res.data.primary_contact == 1){
						$('#add_clientcontact #primary_contact').prop('checked', true);
					}else{
						$('#add_clientcontact #primary_contact').prop('checked', false);
					}


				}
			}
		});
	});
	$(document).delegate('.openbranchform', 'click', function(){
		$('#add_clientbranch').modal('show');
		$('#add_clientbranch #appliationModalLabel').html('Edit Contact');
		var v = $(this).attr('data-id');
		$('#add_clientbranch input[name="branch_id"]').val(v);
			$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getbranchdetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);

				if(res.status){
					$('#add_clientbranch input[name="name"]').val(res.data.name);
					$('#add_clientbranch input[name="email"]').val(res.data.email);
					$('#add_clientbranch input[name="phone"]').val(res.data.phone);
					$('#add_clientbranch #country').val(res.data.country);
					$('#add_clientbranch #telephone').val(res.data.country_code);
					$('#add_clientbranch input[name="city"]').val(res.data.city);
					$('#add_clientbranch input[name="state"]').val(res.data.state);
					$('#add_clientbranch input[name="zip_code"]').val(res.data.zip);
					$('#add_clientbranch #branch').val(res.data.branch);
					if(res.data.primary_contact == 1){
						$('#add_clientbranch #head_office').prop('checked', true);
					}else{
						$('#add_clientbranch #head_office').prop('checked', false);
					}


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

	$(document).delegate('.pinnote', 'click', function(){
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/pinnote')}}/',
			type:'GET',
			datatype:'json',
			data:{note_id:$(this).attr('data-id')},
			success:function(response){
				getallnotes();
			}
		});
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
    { "sortable": false }
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

$(document).delegate('input[name=document_upload]', 'click', function() {
	$(this).attr("value", "");
});

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
            } else {
                $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
            }
			getallactivities();
		}
	});
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
    $(document).delegate('#educationform #subjectlist', 'change', function(){

				var v = $('#educationform #subjectlist option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getsubjects')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#educationform #subject').html(response);

				$(".add_appliation #subject").val('').trigger('change');

			}
		});
				}
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

	$(document).delegate('.add_promotion', 'click', function(){
		$('#create_promotion').modal('show');

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

  $(document).delegate('.changepromotonstatus', 'change', function(){
	  $('.popuploader').show();
	  var appliid = $(this).attr('data-id');
	  var dstatus = $(this).attr('data-status');
	  $.ajax({
			url: '{{URL::to('/admin/change-promotion-status')}}',
			type:'GET',
			data:{id:appliid, status:dstatus},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					if(dstatus == 1){
							var updated_status = 0;
						} else {
							var updated_status = 1;
						}
					$(".changepromotonstatus[data-id="+appliid+"]").attr('data-status', updated_status);
					$.ajax({
						url: site_url+'/admin/get-promotions',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){

							$('.promotionlists').html(responses);
						}
					});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
					if(current_status == 1){
							$(".changepromotonstatus[data-id="+appliid+"]").prop('checked', true);
						} else {
							$(".changepromotonstatus[data-id="+appliid+"]").prop('checked', false);
						}
				}
			}
		});
  });
  $(document).delegate('.changetaskstatus', 'click', function(){
	  var statusname = $(this).attr('data-statusname');
	  var did = $(this).attr('data-id');
	  var dstatus = $(this).attr('data-status');
	  $('.taskstatus').html(statusname+' <i class="fa fa-angle-down"></i>');
	  $('.popuploader').show();
	  $.ajax({
			url: '{{URL::to('/admin/change-task-status')}}',
			type:'GET',
			data:{id:did, status:dstatus},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					$('.tasklogs').html(obj.data);
					$.ajax({
							url: site_url+'/admin/partner/get-tasks',
							type:'GET',
							data:{clientid:'{{$fetchedData->id}}'},
							success: function(responses){
								 $('#my-datatable').DataTable().destroy();
								$('.taskdata').html(responses);
								$('#my-datatable').DataTable({
									"searching": false,
									"lengthChange": false,
								  "columnDefs": [
									{ "sortable": false, "targets": [0, 2, 3] }
								  ],
								  order: [[1, "desc"]] //column indexes is zero based


								}).draw();

							}
						});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

				}
			}
		});
  });

  $(document).delegate('.changeprioritystatus', 'click', function(){
	  var statusname = $(this).attr('data-statusname');
	  var did = $(this).attr('data-id');
	  var dstatus = $(this).attr('data-status');
	  $('.prioritystatus').html(statusname+' <i class="fa fa-angle-down"></i>');
	  $('.popuploader').show();
	  $.ajax({
			url: '{{URL::to('/admin/change-task-priority')}}',
			type:'GET',
			data:{id:did, status:statusname},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					$('.tasklogs').html(obj.data);
					$.ajax({
							url: site_url+'/admin/partner/get-tasks',
							type:'GET',
							data:{clientid:'{{$fetchedData->id}}'},
							success: function(responses){
								 $('#my-datatable').DataTable().destroy();
								$('.taskdata').html(responses);
								$('#my-datatable').DataTable({
									"searching": false,
									"lengthChange": false,
								  "columnDefs": [
									{ "sortable": false, "targets": [0, 2, 3] }
								  ],
								  order: [[1, "desc"]] //column indexes is zero based


								}).draw();

							}
						});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

				}
			}
		});
  });
  $(document).delegate('.savecomment', 'click', function(){
	  var flag = false;
	  if($('#comment').val() == ''){
		  $('.comment-error').html('The Comment field is required.');
		  flag = true;
	  }

	  if(!flag){
		  $('.popuploader').show();
		  $.ajax({
			url: '{{URL::to('/admin/partner/savecomment')}}',
			type:'POST',
			data:{comment:$('#comment').val(), taskid:$('#taskid').val()},
			 headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.tasklogs').html(obj.data);
				}
			}
		});
	  }
  });
  $(document).delegate('.openpromotonform', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('#edit_promotion').modal('show');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpromotioneditform')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('.showpromotionedit').html(response);
					$('.productselect2').select2({
				  placeholder: "Select Product",
				  multiple: true,
					  width: "100%"
				});
			}
		});
	});


  $('#attachments').on('change',function(){
       // output raw value of file input
      $('.showattachment').html('');

        // or, manipulate it further with regex etc.
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        // .. do your magic

       $('.showattachment').html(filename);
    });


});
function arcivedAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;
			} else {
				$('#popuploader').show();
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:'{{URL::to('/')}}/admin/delete_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						$('#popuploader').hide();
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							window.location.href= '{{URL::to('admin/partners')}}';

						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#popuploader").hide();
					},
					beforeSend: function() {
						$("#popuploader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
</script>
@endsection
