@extends('layouts.admin')
@section('title', 'Add Partner')

@section('content')
<style>
.addbranch .error label{
	color: #9f3a38;
}
.addbranch .error input{
	background: #fff6f6;
    border-color: #e0b4b4;
    color: #9f3a38;
    border-radius: "";
    box-shadow: none;
}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/partners/store', 'name'=>"add-partner", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Partners</h4>
								<div class="card-header-action">
									<a href="{{route('admin.partners.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 
												<div class="col-12 col-md-3 col-lg-3">
													<div class="form-group">
														<div class="profile_upload">
															<div class="upload_content">
																<img style="width:100px;height:100px;" id="output"/> 
																<i class="fa fa-camera if_image"></i>
																<span class="if_image">Upload Profile Image</span>
															</div>
															<input type="file" onchange="loadFile(event)" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
														</div>	
														@if ($errors->has('profile_img'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('profile_img') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-9 col-lg-9">
													<div class="row">
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="master_category">Master Category <span class="span_req">*</span></label>
																<select data-valid="required" id="getpartnertype" class="form-control addressselect2" name="master_category">
																	<option value="">Select a Master Category</option>
																	@foreach(\App\Models\Category::all() as $clist)
																	<option value="{{$clist->id}}">{{$clist->category_name}}</option>
																@endforeach
																</select>
																@if ($errors->has('master_category'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('master_category') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="partner_type">Partner Type <span class="span_req">*</span></label>
																<select data-valid="required" id="partner_type" class="form-control addressselect2 " name="partner_type">
																	<option value="">Select a Partner Type</option>
																</select>
																@if ($errors->has('partner_type'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('partner_type') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="partner_name">Partner Name <span class="span_req">*</span></label>
																{{ Form::text('partner_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Partner Name' )) }}
																@if ($errors->has('partner_name'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('partner_name') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="business_reg_no">Business Registration Number</label>
																{{ Form::text('business_reg_no', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Business Registration Number' )) }}
																@if ($errors->has('business_reg_no'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('business_reg_no') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="service_workflow">Service Workflow <span class="span_req">*</span></label>
																<select data-valid="required" class="form-control addressselect2 " name="service_workflow">
																	<option value="">Choose Service workflow</option>
																	@foreach(\App\Models\Workflow::all() as $wlist)
																		<option value="{{$wlist->id}}">{{$wlist->name}}</option>
																	@endforeach
																	
																</select>
																@if ($errors->has('service_workflow'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('service_workflow') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="currency">Currency <span class="span_req">*</span></label>
																<div class="bfh-selectbox bfh-currencies" data-currency="AUD" data-flags="true" data-name="currency"></div>
																@if ($errors->has('currency'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('currency') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#address" aria-expanded="true">
											<h4>Address</h4>
										</div>
										<div class="accordion-body collapse show" id="address" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="address">Address</label>
														{{ Form::text('address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Address' )) }}
														@if ($errors->has('address'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('address') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="city">City</label>
														{{ Form::text('city', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter City' )) }}
														@if ($errors->has('city'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('city') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="state">State</label>
														{{ Form::text('state', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' )) }}
														@if ($errors->has('state'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('state') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="zip">Zip / Post Code</label>
														{{ Form::text('zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' )) }}
														@if ($errors->has('zip'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('zip') }}</strong>
															</span> 
														@endif
													</div>
												</div>  
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="zip" style="visibility:hidden;">Zip / Post Code</label>
														<br>
														<label for=""><input checked  type="radio" value="1" name="is_regional"> Regional</label>
																<label for=""><input type="radio" value="0" name="is_regional"> Non Regional</label>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="country">Country</label>
														<select class="form-control addressselect2" name="country" >
														<?php
															foreach(\App\Models\Country::all() as $list){
																?>
																<option @if(@$list->name == 'Australia') selected @endif value="{{@$list->name}}">{{@$list->name}}</option>
																<?php
															}
															?>
														</select>
														@if ($errors->has('country'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('country') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>  
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#contact_details" aria-expanded="true">
											<h4>Contact Details</h4>
										</div>
										<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="phone">Phone Number</label>
														<div class="cus_field_input">
															<div class="country_code"> 
																<input class="telephone" id="telephone" type="tel" name="country_code" readonly >
															</div>	
															{{ Form::text('phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )) }}
															@if ($errors->has('phone'))
																<span class="custom-error" role="alert">
																	<strong>{{ @$errors->first('phone') }}</strong>
																</span> 
															@endif
														</div>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="email">Email <span class="span_req">*</span></label>
														{{ Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' )) }}
														@if ($errors->has('email'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('email') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="fax">Fax</label>
														{{ Form::text('fax', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Fax' )) }}
														@if ($errors->has('fax'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('fax') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="website">Website</label>
														{{ Form::text('website', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Website' )) }}
														@if ($errors->has('website'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('website') }}</strong>
															</span> 
														@endif
													</div>
												</div>	
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="level">Partner Level</label>
														{{ Form::text('level', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter level' )) }}
														@if ($errors->has('level'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('level') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#branch" aria-expanded="true">
											<h4>Branch</h4>
										</div>
										<div class="accordion-body collapse show" id="branch" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-12 col-lg-4">
													<a href="javascript:;" class="btn btn-outline-primary openbranchnew" ><i class="fa fa-plus"></i> Add Branch</a>
												</div>
											</div>
											
											<div class="branchdata">
													
											</div>
										</div> 
									</div> 
								</div>
								<div class="form-group float-right">
									{{ Form::button('Save Partner', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-partner")' ]) }}
								</div>
							</div>
						</div>	
					</div>
				</div>
			 {{ Form::close() }}	
		</div>
	</section>
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
										<option @if(@$list->name == 'Australia') selected @endif value="{{@$list->name}}">{{@$list->name}}</option>
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
								<div class="form-group"> 
									<label style="visibility:hidden;" for="zip">Zip / Post Code</label>
									<br>
									<label for=""><input class="branchregional" checked type="radio" value="1" name="branch_regional"> Regional</label>
									<label for=""><input class="branchnonregional" type="radio" value="0" name="branch_regional"> Non Regional</label>
								</div>
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

@endsection
@section('scripts')
<script>

jQuery(document).ready(function($){
	function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
		return true;
	}
    else {
		return false;
    }
}
	var branchdata = new Array();
	
	var itag = $('.branchdata .row').length;
	$(document).delegate('.openbranchnew','click', function(){
		$('#clientModalLabel').html('Add New Branch');
		$('.savebranch').show();
		$('#update_branch').hide();
		$('#branchform')[0].reset();
		$(".branch_country").val('Australia').trigger('change') ;
				$('.addbranch').modal('show');
	});
	
	$(document).delegate('#getpartnertype','change', function(){
		$('.popuploader').show();
		var v = $('#getpartnertype option:selected').val();
		$.ajax({
			url: '{{URL::to('/admin/getpaymenttype')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();	
				$('#partner_type').html(response);
			}
		});
	});
	
	$(document).delegate('.savebranch','click', function(){
		var branch_name = $('input[name="branch_name"]').val(); 
		var branch_email = $('input[name="branch_email"]').val(); 
		$('.branch_name_error').html('');
		$('.branch_email_error').html('');
		$('input[name="branch_name"]').parent().removeClass('error');
		$('input[name="branch_email"]').parent().removeClass('error');
		if ($('table#metatag_table').find('#metatag_'+itag).length > 0) {
		}else{ 
			var flag = false;
			if(branch_name == ''){
				$('.branch_name_error').html('The Name field is required.');
				$('input[name="branch_name"]').parent().addClass('error');
				flag = true;
			}
			if(branch_email == ''){
				$('.branch_email_error').html('The Name field is required.');
				$('input[name="branch_email"]').parent().addClass('error');
				flag = true;
			}else if(!validateEmail($.trim(branch_email))) 
			{
				$('.branch_email_error').html('Email is invalid.');
				$('input[name="branch_email"]').parent().addClass('error');
				flag = true;
			}									


			if(!flag){
				var str = $( "#branchform" ).serializeArray();
	console.log(str);
				branchdata[itag] = {
					"name":str[0].value,
					"email":str[1].value,
					"country":str[2].value,
					"city":str[3].value,
					"state":str[4].value,
					"street":str[5].value,
					"zip":str[6].value,
					"rgcode":str[7].value,
					"ccode":str[8].value,
					"phone":str[9].value,
					
				}	
				console.log(branchdata);
				var html = '<div class="row" id="metatag_'+itag+'"><div class="col-12 col-md-3 col-lg-3">';
					 html += '<div class="form-group">';
						 html += '<label for="bname">Name</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchname[]" type="text" value="'+str[0].value+'">';
					 html += '</div>';
				 html += '</div>';
				 html += '<div class="col-12 col-md-3 col-lg-3">';
					 html += '<div class="form-group">';
						 html += '<label for="bemail">Email</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchemail[]" type="text" value="'+str[1].value+'">';
					 html += '</div>';
				 html += '</div>';
				 html += '<div class="col-12 col-md-3 col-lg-3">';
					 html += '<div class="form-group">';
						 html += '<label for="bcountry">Country</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcountry[]" type="text" value="'+str[2].value+'">';
					 html += '</div>';
				 html += '</div>';
				 html += '<div class="col-12 col-md-2 col-lg-2">';
					 html += '<div class="form-group">';
						 html += '<label for="bcity">City</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcity[]" type="text" value="'+str[3].value+'">';
					 html += '</div>';
				 html += '<input autocomplete="off" placeholder="" name="branchstate[]" type="hidden" value="'+str[4].value+'"><input autocomplete="off" placeholder="" name="branchaddress[]" type="hidden" value="'+str[5].value+'"><input autocomplete="off" placeholder="" name="branchzip[]" type="hidden" value="'+str[6].value+'"><input autocomplete="off" placeholder="" name="branchreg[]" type="hidden" value="'+str[7].value+'"><input autocomplete="off" placeholder="" name="branchcountry_code[]" type="hidden" value="'+str[8].value+'"><input autocomplete="off" placeholder="" name="branchphone[]" type="hidden" value="'+str[9].value+'"></div>';
				  html += '<div class="col-12 col-md-1 col-lg-1">';
					 html +=  '<a href="javascript:;" dataid="'+itag+'" class="editbranch"><i class="fa fa-edit"></i></a>';
					 html +=  '<a href="javascript:;" dataid="'+itag+'" class="deletebranch"><i class="fa fa-times"></i></a>';
					 html += '</div>';
				 html += '</div></div>';
				$('.branchdata').append(html);
				$('#branchform')[0].reset();
				$('.addbranch').modal('hide');
				
				$(".branch_country").val('').trigger('change') ;
				itag++;
				}
			}
		
		
	});
	
	$(document).delegate('#update_branch','click', function(){
		var branch_name = $('input[name="branch_name"]').val(); 
		var branch_email = $('input[name="branch_email"]').val(); 
		$('.branch_name_error').html('');
		$('.branch_email_error').html('');
		$('input[name="branch_name"]').parent().removeClass('error');
		$('input[name="branch_email"]').parent().removeClass('error');
		
			var flag = false;
			if(branch_name == ''){
				$('.branch_name_error').html('The Name field is required.');
				$('input[name="branch_name"]').parent().addClass('error');
				flag = true;
			}
			if(branch_email == ''){
				$('.branch_email_error').html('The Name field is required.');
				$('input[name="branch_email"]').parent().addClass('error');
				flag = true;
			}	


			if(!flag){
				var str = $( "#branchform" ).serializeArray();
	
				branchdata[mtval] = {
					"name":str[0].value,
					"email":str[1].value,
					"country":str[2].value,
					"city":str[3].value,
					"state":str[4].value,
					"street":str[5].value,
					"zip":str[6].value,
					"rgcode":str[7].value,
					"ccode":str[8].value,
					"phone":str[9].value,
				
				}	
				console.log(branchdata);
				var html = '<div class="col-12 col-md-3 col-lg-3">';
					 html += '<div class="form-group">';
						 html += '<label for="bname">Name</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchname[]" type="text" value="'+str[0].value+'">';
					 html += '</div>';
				 html += '</div>';
				 html += '<div class="col-12 col-md-3 col-lg-3">';
					 html += '<div class="form-group">';
						 html += '<label for="bemail">Email</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchemail[]" type="text" value="'+str[1].value+'">';
					 html += '</div>';
				 html += '</div>';
				 html += '<div class="col-12 col-md-3 col-lg-3">';
					 html += '<div class="form-group">';
						 html += '<label for="bcountry">Country</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcountry[]" type="text" value="'+str[2].value+'">';
					 html += '</div>';
				 html += '</div>';
				 html += '<div class="col-12 col-md-2 col-lg-2">';
					 html += '<div class="form-group">';
						 html += '<label for="bcity">City</label>';
						 html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcity[]" type="text" value="'+str[3].value+'">';
					 html += '</div>';
				 html += '<input autocomplete="off" placeholder="" name="branchstate[]" type="hidden" value="'+str[4].value+'"><input autocomplete="off" placeholder="" name="branchaddress[]" type="hidden" value="'+str[5].value+'"><input autocomplete="off" placeholder="" name="branchzip[]" type="hidden" value="'+str[6].value+'"><input autocomplete="off" placeholder="" name="branchreg[]" type="hidden" value="'+str[7].value+'"><input autocomplete="off" placeholder="" name="branchcountry_code[]" type="hidden" value="'+str[8].value+'"><input autocomplete="off" placeholder="" name="branchphone[]" type="hidden" value="'+str[9].value+'"></div>';
				  html += '<div class="col-12 col-md-1 col-lg-1">';
					 html +=  '<a href="javascript:;" dataid="'+mtval+'" class="editbranch"><i class="fa fa-edit"></i></a>';
					 html +=  '<a href="javascript:;" dataid="'+mtval+'" class="deletebranch"><i class="fa fa-times"></i></a>';
					 html += '</div>';
				 html += '</div>';
				 $('#metatag_'+mtval).html(html);
			
				$('#branchform')[0].reset();
				$('.addbranch').modal('hide');
				
		}
		
		
		
	});
	var mtval = 0;
	$(document).delegate('.editbranch','click', function(){
		var v = $(this).attr('dataid');
		$('.addbranch').modal('show');
			console.log(branchdata);
		var c = branchdata[v];
		mtval = v;
		$('input[name="branch_name"]').val(c.name);
		$('input[name="branch_email"]').val(c.email);
		$('input[name="branch_city"]').val(c.city);
		$('input[name="branch_state"]').val(c.state);
		$('input[name="branch_address"]').val(c.street);
		$('input[name="branch_zip"]').val(c.zip);
		if(c.rgcode == 1){
			$('.branchregional').prop('checked', true);
		}else{
			$('.branchnonregional').prop('checked', true);
		}
		
		$('input[name="branch_phone"]').val(c.phone);
		$(".branch_country").val(c.country).trigger('change') ;
		//alert(c.ccode);
		$('#telephone').val(c.ccode);
		$('#clientModalLabel').html('Edit Branch');
		$('.savebranch').hide();
		$('#update_branch').show();
		
	});
	
	

	
	
	
	$(document).delegate('.deletebranch','click', function(){
		var v = $(this).attr('dataid');
		 $('#metatag_'+v).remove();
	});
	
	 $(".select2").select2({
    dropdownParent: $(".addbranch .modal-content")
  }); 
  $(".addressselect2").select2();
});
var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
	  $('.if_image').hide();
    }
  };
</script>
@endsection