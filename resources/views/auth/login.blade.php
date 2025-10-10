@extends('layouts.dashboard_frontend')
@section('title', 'Login')
@section('meta_title', 'Login - Migration Manager')
@section('meta_keyword', 'login, migration manager, sign in')
@section('meta_description', 'Login to Migration Manager')
@section('content')
<div class="row">
	<!-- {{dd('jijiijij')}} -->
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<!-- Flash Message End -->
	
		<!-- Login Start -->
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box login-form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<div class="form-top-left ">
							<h3>Sign-in</h3>
							<p>Enter Mobile/Email and password to log on</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-lock"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{!! html()->form('POST', url('/login'))->attribute('name', 'login')->attribute('autocomplete', 'off')->class('login-form')->open() !!}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="text" placeholder="Email / Mobile*" class="form-mobile form-control" name="email" value="{{ (Cookie::get('email') !='' && !old('email')) ? Cookie::get('email') : old('email')  }}" autocomplete="new-password" data-valid="required" />

								@if ($errors->has('email'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="password" placeholder="Password" class="form-control" name="password" value="{{ (Cookie::get('password') !='' && !old('password')) ? Cookie::get('password') : old('password')  }}" autocomplete="new-password" data-valid="required" />
								
								@if ($errors->has('password'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('password') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">	
								<input class="ml-1" type="checkbox" name="remember" id="remember" @if(Cookie::get('email') != '' && Cookie::get('password') != '') checked  @endif>
								<span class="text-muted ml-1 remember-me-text">{{ __('Remember Me') }}</span>
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">	
								<a href="{{URL::to('/forgot_password')}}" class="text-muted ml-1 remember-me-text"><strong>Forgot Password ?</strong></a>
							</div>
							
							
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->button('Sign in!')->class('btn btn-primary')->attribute('onClick', 'customValidate("login")') !!}
							</div>
							
							<!--<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<a class="common-btn btn-facebook" href="{{url('/auth/facebook')}}">
									<i class="fa fa-facebook"></i> Sign Up with Facebook
								</a>
								<a class="common-btn btn-google" href="{{url('/auth/google')}}">
									<i class="fa fa-google-plus"></i> Sign Up with Google Plus
								</a>
							</div>-->
						{!! html()->close() !!}	
					</div>
				</div>
			</div>
		<!-- Login End -->
		
		<div class="col-sm-1 middle-border"></div>
		<div class="col-sm-1"></div>
		
		<!-- Signup Start -->
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12 ">
						<div class="form-top-left">
							<h3>Sign up now</h3>
							<p>Fill in the form below to get instant access.</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-pencil"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{!! html()->form('POST', url('/register'))->attribute('name', 'register')->attribute('autocomplete', 'off')->class('registration-form')->open() !!}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->text('first_name')->class('form-name form-control')->attribute('data-valid', 'required')->attribute('placeholder', 'First Name*')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('first_name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('first_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->text('last_name')->class('form-name form-control')->attribute('data-valid', 'required')->attribute('placeholder', 'Last Name*')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('last_name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('last_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->text('email_register')->class('form-name form-control')->attribute('data-valid', 'required email')->attribute('placeholder', 'Email*')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('email_register'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_register') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->password('password_register')->class('form-name form-control')->attribute('data-valid', 'required min-6 max-12')->attribute('placeholder', 'Password*')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('password_register'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('password_register') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-6 col-sm-6 col-md-6 col-xs-12">
								{!! html()->text('phone_register')->class('form-name form-control mask')->attribute('data-valid', 'required equal-10')->attribute('placeholder', 'Mobile*')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('phone_register'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('phone_register') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-6 col-sm-6 col-md-6 col-xs-12">
								{!! html()->text('course_level')->class('form-name form-control')->attribute('placeholder', 'Course Level')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('course_level'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('course_level') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								<select name="country" class="form-name form-control" data-valid="required">
									<option value="{{ @$country->id }}">{{ @$country->name }}</option>		
								</select>
								
								@if ($errors->has('country'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('country') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">	
								<select name="state" class="form-control" data-valid="required">
									<option value="">Choose State</option>
									@if(count(@$states) !== 0)
										@foreach (@$states as $state)
											<option value="{{ $state->id }}">{{ $state->name }}</option>
										@endforeach
									@endif
								</select>
							
								@if ($errors->has('state'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('state') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->text('city')->class('form-name form-control')->attribute('placeholder', 'City')->attribute('data-valid', 'required')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('city'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('city') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								{!! html()->textarea('address')->class('form-control textarea')->attribute('placeholder', 'Please write Your Address...')->attribute('data-valid', 'required')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('address'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('address') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->text('zip')->class('form-name form-control')->attribute('placeholder', 'Zip Code')->attribute('data-valid', 'required')->attribute('autocomplete', 'new-password') !!}
							
								@if ($errors->has('zip'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('zip') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{!! html()->button('Sign me up!')->class('btn btn-primary')->attribute('onClick', 'customValidate("register")') !!}
							</div>
							<!--<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<a class="common-btn btn-facebook" href="{{url('/auth/facebook')}}">
									<i class="fa fa-facebook"></i> Sign Up with Facebook
								</a>
								<a class="common-btn btn-google" href="{{url('/auth/google')}}">
									<i class="fa fa-google-plus"></i> Sign Up with Google Plus
								</a>
							</div>-->
						{!! html()->close() !!}	
					</div>
				</div>
			</div>
		<!-- Signup End -->	
	</div>
</div>
@endsection