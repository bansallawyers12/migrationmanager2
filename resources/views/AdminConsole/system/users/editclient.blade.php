@extends('layouts.admin')
@section('title', 'Edit Client')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body"> 
			{{ Form::open(array('url' => 'admin/users/editclient', 'name'=>"edit-client", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			{{ Form::hidden('id', @$fetchedData->id) }}
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Client</h4>
								<div class="card-header-action">
									<a href="{{route('admin.users.clientlist')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group">
									<label for="first_name">First Name</label>
									{{ Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'First Name' )) }}
									@if ($errors->has('first_name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('first_name') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="last_name">Last Name</label>
									{{ Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Last Name' )) }}
									@if ($errors->has('last_name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('last_name') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group"> 
									<label for="company_name">Company Name</label>
									{{ Form::text('company_name', @$fetchedData->company_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Company Name' )) }}
									@if ($errors->has('company_name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('company_name') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="company_website">Company Website</label>
									{{ Form::text('company_website', @$fetchedData->company_website, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Company Website' )) }}
									@if ($errors->has('company_website'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('company_website') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="email">Email</label>
									{{ Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' )) }}
									@if ($errors->has('email'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('email') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="password">Password</label>
									<input type="password" name="password" class="form-control" autocomplete="off" value="" placeholder="Enter Password" data-valid="required" />							
									@if ($errors->has('password'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('password') }}</strong>
										</span> 
									@endif
								</div>  
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group">
									<label for="phone">Phone No.</label>
									{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Phone Number' )) }}
									@if ($errors->has('phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('phone') }}</strong>
										</span> 
									@endif
								</div>	
								<div class="form-group">
									<label for="profile_img">Company Logo</label>
									<div class="custom-file">	
										<input type="hidden" id="old_profile_img" name="old_profile_img" value="{{@$fetchedData->profile_img}}" />
										<input type="file" name="profile_img" class="form-control custom-file-input" id="customFile" autocomplete="off" data-valid="required" />		
										<label class="custom-file-label" for="customFile">Choose file</label>
										@if ($errors->has('profile_img'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('profile_img') }}</strong>
											</span> 
										@endif 	
									</div>	
									<div class="show-uploded-img" style="width:140px;margin-top:10px;">	
										@if(@$fetchedData->profile_img != '')
											<img style="width:100%;" src="{{URL::to('/public/img/profile_imgs')}}/{{@$fetchedData->profile_img}}" class="img-avatar"/>
										@endif
									</div>
								</div>
								<div class="form-group country_field"> 
									<label for="country" class="">Country <span style="color:#ff0000;">*</span></label>
									<div name="country" class="country_input niceCountryInputSelector" id="basic" data-selectedcountry="IN" data-showspecial="false" data-showflags="true" data-i18nall="All selected" data-i18nnofilter="No selection" data-i18nfilter="Filter" data-onchangecallback="onChangeCallback"></div>
									@if ($errors->has('country'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('country') }}</strong>
										</span> 
									@endif 
								</div>
								<div class="form-group">
									<label for="city">City</label>
									{{ Form::text('city', @$fetchedData->city, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'City' )) }}
									@if ($errors->has('city'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('city') }}</strong>
										</span> 
									@endif 
								</div> 
								<div class="form-group">
									<label for="gst_no">GST No.</label>
									{{ Form::text('gst_no', @$fetchedData->gst_no, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'e.g. 22AAAAA00000AZ5' )) }}
									@if ($errors->has('gst_no'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('gst_no') }}</strong>
										</span> 
									@endif
								</div> 
								<div class="form-group float-right">
									{{ Form::submit('Update', ['class'=>'btn btn-primary' ]) }}
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