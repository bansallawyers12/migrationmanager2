@extends('layouts.admin')
@section('title', 'Edit Leads')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		    <div class="server-error">
				@include('../Elements/flash-message')
			</div>
			{{ Form::open(array('url' => 'admin/leads/edit', 'name'=>"edit-leads", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }} 
			 {{ Form::hidden('id', @$fetchedData->id) }}
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Leads</h4>
								<div class="card-header-action">
									<a href="{{route('admin.leads.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-3 col-md-3 col-lg-3">
										<div class="form-group">
											<input type="hidden" id="old_profile_img" name="old_profile_img" value="{{@$fetchedData->profile_img}}" />
											<div class="profile_upload">
												<div class="upload_content">
												@if(@$fetchedData->profile_img != '')
													<img src="{{URL::to('/public/img/profile_imgs')}}/{{@$fetchedData->profile_img}}" style="width:100px;height:100px;" id="output"/> 
												@else
													<img id="output"/> 
												@endif
													<i <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="fa fa-camera if_image"></i>
													<span <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="if_image">Upload Profile Image</span>
												</div>
												<input onchange="loadFile(event)" type="file" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
											</div>	
											
											@if ($errors->has('profile_img'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('profile_img') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-9 col-md-9 col-lg-9">
										<div class="row">
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="first_name">First Name <span class="span_req">*</span></label>
													{{ Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
													@if ($errors->has('first_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('first_name') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="last_name">Last Name <span class="span_req">*</span></label>
													{{ Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
													@if ($errors->has('last_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('last_name') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label style="display:block;" for="gender">Gender <span class="span_req">*</span></label>
													<div class="form-check form-check-inline">
														<input class="form-check-input" type="radio" id="male" value="Male" name="gender" @if(@$fetchedData->gender == "Male") checked @endif>
														<label class="form-check-label" for="male">Male</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" type="radio" id="female" value="Female" name="gender" @if(@$fetchedData->gender == "Female") checked @endif>
														<label class="form-check-label" for="female">Female</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" type="radio" id="other" value="Other" name="gender" @if(@$fetchedData->gender == "Other") checked @endif>
														<label class="form-check-label" for="other">Other</label>
													</div>
													@if ($errors->has('gender'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('gender') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<?php
											if($fetchedData->dob != ''){
												$dob = date('d/m/Y', strtotime($fetchedData->dob));
											}
											?>	
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group">
													<label for="dob">
													Date of Birth</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{{ Form::text('dob', @$dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }} 
														@if ($errors->has('dob'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('dob') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="age">Age</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{{ Form::text('age', $fetchedData->age, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
														@if ($errors->has('age'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('age') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group">
													<label for="martial_status">
													Marital Status</label>
													<select style="padding: 0px 5px;" name="martial_status" id="martial_status" class="form-control">
														<option value="">Select Marital Status</option>
														<option value="Married" @if(@$fetchedData->martial_status == "Married") selected @endif>Married</option>
														<option value="Never Married" @if(@$fetchedData->martial_status == "Never Married") selected @endif>Never Married</option>
														<option value="Engaged" @if(@$fetchedData->martial_status == "Engaged") selected @endif>Engaged</option>
														<option value="Divorced" @if(@$fetchedData->martial_status == "Divorced") selected @endif>Divorced</option>
														<option value="Separated" @if(@$fetchedData->martial_status == "Separated") selected @endif>Separated</option>
														<option value="De facto" @if(@$fetchedData->martial_status == "De facto") selected @endif>De facto</option>
														<option value="Widowed" @if(@$fetchedData->martial_status == "Widowed") selected @endif>Widowed</option>
														<option value="Others" @if(@$fetchedData->martial_status == "Others") selected @endif>Others</option>
													</select>
													@if ($errors->has('martial_status'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('martial_status') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="contact_type">
											Contact Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="contact_type" id="contact_type" class="form-control" data-valid="required">
												<option value="Personal" @if(@$fetchedData->contact_type == "Personal") selected @endif> Personal</option>
												<option value="Office" @if(@$fetchedData->contact_type == "Office") selected @endif>Office</option>
											</select>
											@if ($errors->has('contact_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('contact_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="phone">Contact No.<span style="color:#ff0000;">*</span></label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="country_code" readonly >
												</div>	
												{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control tel_input', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
												@if ($errors->has('phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="email_type">
											Email Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="email_type" id="email_type" class="form-control" data-valid="required">
												<option value="Personal" @if(@$fetchedData->email_type == "Personal") selected @endif> Personal</option>
												<option value="Business" @if(@$fetchedData->email_type == "Business") selected @endif>Business</option>
											</select>
											@if ($errors->has('email_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="email">Email <span style="color:#ff0000;">*</span></label>
											{{ Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="att_email">Email </label>
											{{ Form::text('att_email', @$fetchedData->att_email, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('att_email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('att_email') }}</strong>
												</span> 
											@endif
										</div>
									</div> 
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="att_phone">Phone</label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="att_country_code"  readonly >
												</div>	
												{{ Form::text('att_phone', @$fetchedData->att_phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												@if ($errors->has('att_phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('att_phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>	
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visa_type">Visa Type</label>
										<select class="form-control select2" name="visa_type">
										<option value="">- Select Visa Type -</option>
										@foreach(\App\Models\Matter::select('id', 'title', 'nick_name')->where('status', 1)->orderBy('title', 'ASC')->get() as $visalist)
											<option @if($fetchedData->visa_type == $visalist->id) selected @endif value="{{$visalist->id}}">{{$visalist->title}} ({{@$visalist->nick_name}})</option>
										@endforeach
											</select>
											@if ($errors->has('visa_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('visa_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<?php
										$visa_expiry_date = '';
										if($fetchedData->visa_expiry_date != '' && $fetchedData->visa_expiry_date != '0000-00-00'){
											$visa_expiry_date = date('d/m/Y', strtotime($fetchedData->visa_expiry_date));
										}

										?>	
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visa_expiry_date">Visa Expiry Date</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												{{ Form::text('visa_expiry_date', $visa_expiry_date, array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
												@if ($errors->has('visa_expiry_date'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('visa_expiry_date') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="preferredIntake">Preferred Intake</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												{{ Form::text('preferredIntake', @$fetchedData->preferredIntake, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												@if ($errors->has('preferredIntake'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('preferredIntake') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="country_passport">Country of Passport</label>
											<select class="form-control  select2" name="country_passport" >
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(@$fetchedData->country_passport == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}" >{{@$list->name}}</option>
													<?php
												}
												?>
											</select>
											@if ($errors->has('country_passport'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('country_passport') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="passport_no">Passport Number</label>
											{{ Form::text('passport_no', @$fetchedData->passport_no, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('passport_no'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('passport_no') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="address">Address</label>
											{{ Form::text('address', $fetchedData->address, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('address'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('address') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="city">City</label>
											{{ Form::text('city', $fetchedData->city, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('city'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('city') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="state">State</label>
											<select class="form-control" name="state">
												<option value="">- Select State -</option>
												<option value="Australian Capital Territory" @if(@$fetchedData->state == "Australian Capital Territory") selected @endif>Australian Capital Territory</option>
												<option value="New South Wales" @if(@$fetchedData->state == "New South Wales") selected @endif>New South Wales</option>
												<option value="Northern Territory" @if(@$fetchedData->state == "Northern Territory") selected @endif>Northern Territory</option>
												<option value="Queensland" @if(@$fetchedData->state == "Queensland") selected @endif>Queensland</option>
												<option value="South Australia" @if(@$fetchedData->state == "South Australia") selected @endif>South Australia</option>
												<option value="Tasmania" @if(@$fetchedData->state == "Tasmania") selected @endif>Tasmania</option>
												<option value="Victoria" @if(@$fetchedData->state == "Victoria") selected @endif>Victoria</option>
												<option value="Western Australia" @if(@$fetchedData->state == "Western Australia") selected @endif>Western Australia</option>
											</select>
											@if ($errors->has('state'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('state') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="zip">Post Code</label>
											{{ Form::text('zip', $fetchedData->zip, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('zip'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('zip') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								<hr style="border-color: #000;"/>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="country">Country</label>
											<select class="form-control select2" name="country" >
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(@$fetchedData->country == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}" >{{@$list->name}}</option>
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
									<div class="col-sm-8">
										<div class="form-group"> 
											<label for="related_files">Similar related files</label>
												<select multiple class="form-control js-data-example-ajaxcc" name="related_files[]">
											
												
											</select>
											@if ($errors->has('related_files'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('related_files') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="nomi_occupation">Nominated Occupation</label>
											{{ Form::text('nomi_occupation', $fetchedData->nomi_occupation, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
											@if ($errors->has('nomi_occupation'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('nomi_occupation') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="skill_assessment">Skill Assessment</label>
											<select class="form-control" name="skill_assessment">
												<option  value="">Select</option>
												<option @if($fetchedData->skill_assessment == 'Yes') selected @endif value="Yes">Yes</option>
												<option @if($fetchedData->skill_assessment == 'No') selected @endif value="No">No</option>
											</select>
											@if ($errors->has('skill_assessment'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('skill_assessment') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_aus">Highest Qualification in Australia</label>
											{{ Form::text('high_quali_aus', $fetchedData->high_quali_aus, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
											@if ($errors->has('high_quali_aus'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('high_quali_aus') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_overseas">Highest Qualification Overseas</label>
											{{ Form::text('high_quali_overseas', $fetchedData->high_quali_overseas, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
											@if ($errors->has('high_quali_overseas'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('high_quali_overseas') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
											{{ Form::text('relevant_work_exp_aus', $fetchedData->relevant_work_exp_aus, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
											@if ($errors->has('relevant_work_exp_aus'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_aus') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
											{{ Form::text('relevant_work_exp_over', $fetchedData->relevant_work_exp_over, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												
											@if ($errors->has('relevant_work_exp_over'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_over') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="married_partner">If married, English score of partner</label>
											{{ Form::text('married_partner', $fetchedData->married_partner, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												
											@if ($errors->has('married_partner'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('married_partner') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="naati_py">Naati/PY</label>
												<select class="form-control" name="naati_py">
													<option value="">Select</option>
													<option @if($fetchedData->naati_py == 'Naati') selected @endif value="Naati">Naati</option>
													<option @if($fetchedData->naati_py == 'PY') selected @endif value="PY">PY</option>
											</select>
											@if ($errors->has('naati_py'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('naati_py') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="total_points">Total Points</label>
											{{ Form::text('total_points', $fetchedData->total_points, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												
											@if ($errors->has('total_points'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('total_points') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="start_process">When You want to start Process</label>
												<select class="form-control" name="start_process">
													<option value="">Select</option>
													<option @if($fetchedData->start_process == 'As soon As Possible') selected @endif value="As soon As Possible">As soon As Possible</option>
													<option @if($fetchedData->start_process == 'In Next 3 Months') selected @endif value="In Next 3 Months">In Next 3 Months</option>
													<option @if($fetchedData->start_process == 'In Next 6 Months') selected @endif value="In Next 6 Months">In Next 6 Months</option>
													<option @if($fetchedData->start_process == 'Advise Only') selected @endif value="Advise Only">Advise Only</option>
											</select>
											@if ($errors->has('start_process'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('start_process') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								<hr style="border-color: #000;"/>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<label for="service">Service <span style="color:#ff0000;">*</span></label>
												<select class="form-control select2" name="service" data-valid="required">
											<option value="">- Select Lead Service -</option>
													@foreach(\App\Models\LeadService::orderby('name', 'ASC')->get() as $leadservlist)
												<option @if($fetchedData->service == $leadservlist->name) selected @endif value="{{$leadservlist->name}}">{{$leadservlist->name}}</option>
											@endforeach
											</select>
											@if ($errors->has('service'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('service') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="assign_to">Assign To <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="assign_to" id="assign_to" class="form-control select2" data-valid="required">
											<option value="">Select User</option>
												<?php
												$admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
												foreach($admins as $admin){
													 $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
												?>
												<option @if(@$fetchedData->assign_to == $admin->id) selected @endif value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
												<?php } ?>
											</select>
											@if ($errors->has('assign_to'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('assign_to') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="status">Status</label>
											<select style="padding: 0px 5px;" name="status" id="status" class="form-control" data-valid="">
												<option value="">Select Status</option>
												<option value="Unassigned" @if(@$fetchedData->status == "Unassigned") selected @endif>Unassigned</option>
												<option value="Assigned" @if(@$fetchedData->status == "Assigned") selected @endif>Assigned</option>
												<option value="In-Progress" @if(@$fetchedData->status == "In-Progress") selected @endif>In-Progress</option>
												<option value="Closed" @if(@$fetchedData->status == "Closed") selected @endif>Closed</option>
											</select>
											@if ($errors->has('status'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('status') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="lead_quality">Lead Quality <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="lead_quality" id="lead_quality" class="form-control" data-valid="required">
												<option value="1" @if(@$fetchedData->lead_quality == "1") selected @endif>1</option>
												<option value="2" @if(@$fetchedData->lead_quality == "2") selected @endif>2</option>
												<option value="3" @if(@$fetchedData->lead_quality == "3") selected @endif>3</option>
												<option value="4" @if(@$fetchedData->lead_quality == "4") selected @endif>4</option>
												<option value="5" @if(@$fetchedData->lead_quality == "5") selected @endif>5</option>
											</select>
											@if ($errors->has('lead_quality'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('lead_quality') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="lead_source">Lead Source <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="lead_source" id="lead_source" class="form-control" data-valid="required">
												<option value="">Lead Source</option>
													@foreach(\App\Models\Source::all() as $sources)
											<option value="{{$sources->name}}" @if(@$fetchedData->lead_source == $sources->name) selected @endif>{{$sources->name}}</option>
							@endforeach
											
											</select>
											@if ($errors->has('lead_source'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('lead_source') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="tags_label">Tags/Label </label>
											{{ Form::text('tags_label', @$fetchedData->tags_label, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('tags_label'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('tags_label') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label for="comments_note">Comments / Note</label>
											<textarea class="form-control" name="comments_note" placeholder="" data-valid="">{{@$fetchedData->comments_note}}</textarea>
											@if ($errors->has('comments_note')) 
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('comments_note') }}</strong>
												</span> 
											@endif
										</div>
									</div> 
									<div class="col-sm-12">
										<div class="form-group float-right">
											{{ Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-leads")' ]) }}
										</div>
									</div>
								</div> 
							</div>
						</div>	
					</div>
				</div>  
			 {{ Form::close() }}	
		</div>
	</section>
</div>

@endsection
@section('scripts')
<?php
if($fetchedData->related_files != ''){
    $exploder = explode(',', $fetchedData->related_files);
       foreach($exploder AS $EXP){ 
			$relatedclients = \App\Models\Admin::where('id', $EXP)->first();	
			?>
			<input type="hidden" class="relatedfile" data-id="{{@$relatedclients->id}}" data-email="{{@$relatedclients->email}}" data-name="{{@$relatedclients->first_name}} {{@$relatedclients->last_name}}">
			<?php
								
}
}
?>
<script>
jQuery(document).ready(function($){
    <?php if($fetchedData->related_files != ''){ ?>
    	var array = [];
	var data = [];
    $('.relatedfile').each(function(){
		
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
	});
	$(".js-data-example-ajaxcc").select2({
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
});
	$('.js-data-example-ajaxcc').val(array);
		$('.js-data-example-ajaxcc').trigger('change');
	
	
	<?php } ?>
	
$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
	
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
});

  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src); // free memory
	  $('.if_image').hide();
	  $('#output').css({'width':"100px",'height':"100px"});
    }
  };
</script>
@endsection