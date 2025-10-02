@extends('layouts.dashboard_frontend')
@section('content')
<div class="enquiry_sec">
	<div class="container">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="enquiry_form">
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<h3>Enquiry Form</h3>
					<form action="{{URL::to('/enquiry/store')}}" method="post">
						@csrf
						<div class="form-group">
							<label for="first_name">First Name <span style="color:#ff0000;">*</span></label>
							<input required type="text" class="form-control" name="first_name"/>
							@if ($errors->has('first_name'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('first_name') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="last_name">Last Name <span style="color:#ff0000;">*</span></label>
							<input required type="text" class="form-control" name="last_name"/>
							@if ($errors->has('last_name'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('last_name') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="email">Email <span style="color:#ff0000;">*</span></label>
							<input required type="email" class="form-control" name="email"/>
							@if ($errors->has('email'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('email') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="phone">Phone <span style="color:#ff0000;">*</span></label>
							<input required type="text" class="form-control" name="phone"/>
							@if ($errors->has('phone'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('phone') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="country">Country</label>
							<select required class="form-control  select2" name="country" >
							<?php
								foreach(\App\Models\Country::all() as $list){
								?>
								<option @if($list->sortname == 'AU') selected @endif value="{{@$list->sortname}}" >{{@$list->name}}</option>
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
						<div class="form-group">
							<label for="city">City</label>
							<input type="text" class="form-control" name="city"/>
							@if ($errors->has('city'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('city') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="address">Address</label>
							<input required type="text" class="form-control" name="address"/>
							@if ($errors->has('address'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('address') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="city">Message <span style="color:#ff0000;">*</span></label>
							<textarea required class="form-control" name="message"></textarea>
							@if ($errors->has('message'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('message') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="address">Source</label>
							<select required class="form-control" name="source">
								<option value="">Select Source</option>
								@foreach(\App\Models\EnquirySource::all() as $sources)
								  <option value="{{$sources->name}}">{{$sources->name}}</option>
								  @endforeach
							</select>
						</div>
						<div class="form-group">
							<label for="cur_visa">Current Visa</label>
							<input type="text" class="form-control" name="cur_visa"/>
							@if ($errors->has('cur_visa'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('cur_visa') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-group">
							<label for="visa_expiry">Visa Expiry</label>
							<input type="text" class="form-control dobdatepicker" name="visa_expiry"/>
							@if ($errors->has('visa_expiry'))
								<span class="custom-error" role="alert">
									<strong>{{ @$errors->first('visa_expiry') }}</strong>
								</span> 
							@endif
						</div>
						<div class="form-btn text-center">
							<input type="submit" class="btn btn-primary" value="Submit"/>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
