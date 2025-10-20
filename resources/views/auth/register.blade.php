@extends('layouts.admin-login')
@section('title', 'Register')
@section('content')
<style>
#login_bg{min-height: 140vh!important;background-size: 60%!important;background-position: 115% 10%!important;}
</style>
<div id="login" class="myregister">
	<aside class="tside">
		<figure>
			<a href="#"><img src="{!! asset('public/img/logo.png') !!}" data-retina="true" alt="" width="150"></a>
		</figure>
			  <form method="POST" action="{{ route('register') }}">
                @csrf
					<div class="form-group row">
						<label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

						<div class="col-md-6">
							<input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" required autofocus>

							@if ($errors->has('first_name'))
								<span class="invalid-feedback" role="alert">
									<strong>{{ $errors->first('first_name') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group row">
						<label for="last_name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>
						<div class="col-md-6">
							<input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" required autofocus>
							@if ($errors->has('last_name'))
								<span class="invalid-feedback" role="alert">
									<strong>{{ $errors->first('last_name') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group row">
						<label for="company_name" class="col-md-4 col-form-label text-md-right">{{ __('Company Name') }}</label>
						<div class="col-md-6">
							<input id="company_name" type="text" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" name="company_name" value="{{ old('company_name') }}" required autofocus>
							@if ($errors->has('company_name'))
								<span class="invalid-feedback" role="alert">
									<strong>{{ $errors->first('company_name') }}</strong>
								</span>
							@endif
						</div>
					</div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" required>

                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                                <a class="btn btn-warning" href="{{URL::to('/admin')}}"><b>back to Login</b></a>
                            </div>
                        </div>
		</form>
	</aside>
	</div>
@endsection