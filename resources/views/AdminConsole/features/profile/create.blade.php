@extends('layouts.admin')
@section('title', 'Add Profile')

@section('content')

<!-- Main Content -->
<div class="main-content">
<section class="section">
<div class="section-body">
	{{ Form::open(array('url' => 'admin/profiles/store', 'name'=>"add-profiles", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }} 
		<div class="row">   
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4>Add Profile</h4>
						<div class="card-header-action">
							<a href="{{route('admin.feature.profiles.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
						</div>
					</div>
				</div>
			</div>
			 <div class="col-3 col-md-3 col-lg-3">
				@include('../Elements/Admin/setting')
			</div>       
			<div class="col-9 col-md-9 col-lg-9">
				<div class="card">
					<div class="card-body">
						<div id="accordion"> 
							<div class="accordion">
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
									<h4>Primary Information</h4>
								</div>
								<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
									<div class="row"> 						
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="company_name">Company Name <span class="span_req">*</span></label>
												{{ Form::text('company_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
												@if ($errors->has('company_name'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('company_name') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="abn">ABN NO <span class="span_req">*</span></label>
												{{ Form::text('abn', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
												@if ($errors->has('abn'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('abn') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="address">Address <span class="span_req">*</span></label>
												{{ Form::text('address', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
												@if ($errors->has('address'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('address') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="phone">Phone <span class="span_req">*</span></label>
												{{ Form::text('phone', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
												@if ($errors->has('phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="other_phone">Other Phone <span class="span_req">*</span></label>
												{{ Form::text('other_phone', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
												@if ($errors->has('other_phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('other_phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="email">Email <span class="span_req">*</span></label>
												{{ Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
												@if ($errors->has('email'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('email') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="website">Website <span class="span_req">*</span></label>
												{{ Form::text('website', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
												
											</div>
										</div>
										
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="test_pdf">Company Logo</label>
												<input type="file" id="profile_img" name="profile_img" value="" />
											</div> 
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="note">Note </label>
												<textarea class="form-control" name="note"></textarea>
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group float-right">
							{{ Form::submit('Save', ['class'=>'btn btn-primary' ]) }}
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