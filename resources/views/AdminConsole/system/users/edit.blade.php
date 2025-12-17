@extends('layouts.crm_client_detail')
@section('title', 'User')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<form action="{{ route('adminconsole.system.users.update', $fetchedData->id) }}" name="edit-user" autocomplete="off" enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Users</h4>
								<div class="card-header-action">
									<a href="{{route('adminconsole.system.users.active')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<h4>PERSONAL DETAILS</h4>
								<div class="form-group">
									<label for="first_name">First Name</label>
									<input type="text" name="first_name" value="{{ old('first_name', @$fetchedData->first_name) }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter User First Name">
                                    @if ($errors->has('first_name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('first_name') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="last_name">Last Name</label>
									<input type="text" name="last_name" value="{{ old('last_name', @$fetchedData->last_name) }}" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter User Last Name">
                                    @if ($errors->has('last_name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('last_name') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="email">Email</label>
									<input type="text" name="email" value="{{ old('email', @$fetchedData->email) }}" class="form-control" data-valid="" autocomplete="off">
                                    @if ($errors->has('email'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('email') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="name">Password</label>
									<input type="password" value="" name="password" class="form-control" autocomplete="off" placeholder="Enter User Password" data-valid="required" />
									@if ($errors->has('password'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('password') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="name">Password Confirmation</label>
									<input type="password" value="" name="password_confirmation" class="form-control" autocomplete="off" placeholder="Enter User Password" data-valid="required" />
									@if ($errors->has('password'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('password') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="name">Phone Number</label>
									<div class="cus_field_input">
									<div class="country_code">
										<input class="telephone" id="telephone" type="tel" name="country_code" readonly value="{{@$fetchedData->telephone}}" >
									</div>
									<input type="text" name="phone" value="{{ old('phone', @$fetchedData->phone) }}" class="form-control tel_input" data-valid="" autocomplete="off" placeholder="Enter Phone">
                                    @if ($errors->has('phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('phone') }}</strong>
										</span>
									@endif
								</div>
									@if ($errors->has('phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('phone') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<h4>Office DETAILS</h4>
								<div class="form-group">
									<label for="name">Position Title</label>
									<input type="text" name="position" value="{{ old('position', @$fetchedData->position) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Enter Position Title">
                                    @if ($errors->has('position'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('position') }}</strong>
										</span>
									@endif
								</div>

                                <div class="form-group">
									<label for="role">User Role (Type)</label>
									<select name="role" id="role" class="form-control" data-valid="required" autocomplete="new-password">
										<option value="">Choose One...</option>
										@if(count(@$usertype) !== 0)
											@foreach (@$usertype as $ut)
												<option value="{{ @$ut->id }}" @if($fetchedData->role == $ut->id) selected @endif>{{ @$ut->name }}</option>
											@endforeach
										@endif
									</select>
									@if ($errors->has('role'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('role') }}</strong>
										</span>
									@endif
								</div>

								<div class="form-group">
                                    <?php
                                    $branchx = \App\Models\Branch::all();
                                    ?>
									<label for="office">Office</label>
									<select class="form-control" data-valid="required" name="office">
										<option value="">Select</option>
										@foreach($branchx as $branch)
											<option @if($fetchedData->office_id == $branch->id) selected @endif value="{{$branch->id}}">{{$branch->office_name}}</option>
										@endforeach
									</select>
									@if ($errors->has('office'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('office') }}</strong>
										</span>
									@endif
								</div>

                                <div class="form-group">
									<label for="role">Department (Team)</label>
									<select name="team" id="team" class="form-control" data-valid="" autocomplete="new-password">
										<option value="">Choose One...</option>

											@foreach (\App\Models\Team::all() as $tm)
												<option @if($fetchedData->team == $tm->id) selected @endif value="{{ @$tm->id }}">{{ @$tm->name }}</option>
											@endforeach

									</select>
                                </div>

                                <div class="form-group">
                                    <label for="role">Permission</label>
							    	<?php
                                    if( isset($fetchedData->permission) && $fetchedData->permission !="")
                                    {
                                        if( strpos($fetchedData->permission,",") ){
                                            $permission_arr =  explode(",",$fetchedData->permission);
                                        } else {
                                            $permission_arr = [$fetchedData->permission];
                                        } ?>

                                            <br><b>Notes</b>  &nbsp;&nbsp;&nbsp;&nbsp;
                                            <input value="1" <?php if ( in_array(1, $permission_arr) ) echo "checked='checked'"; ?> type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; View &nbsp;
                                            <input value="2" <?php if ( in_array(2, $permission_arr) ) echo "checked='checked'"; ?> type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Add/Edit &nbsp;
                                            <input value="3" <?php if ( in_array(3, $permission_arr) ) echo "checked='checked'"; ?> type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Delete &nbsp;

                                            <br><b>Documents</b>
                                            <input value="4" <?php if ( in_array(4, $permission_arr) ) echo "checked='checked'"; ?> type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; View &nbsp;
                                            <input value="5" <?php if ( in_array(5, $permission_arr) ) echo "checked='checked'"; ?> type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Add/Edit &nbsp;
                                            <input value="6" <?php if ( in_array(6, $permission_arr) ) echo "checked='checked'"; ?> type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Delete &nbsp;
                                        <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <br><b>Notes</b>  &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input value="1" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; View &nbsp;
                                        <input value="2" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Add/Edit &nbsp;
                                        <input value="3" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Delete &nbsp;

                                        <br><b>Documents</b>
                                        <input value="4" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; View &nbsp;
                                        <input value="5" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Add/Edit &nbsp;
                                        <input value="6" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Delete &nbsp;
                                    <?php
                                    }?>
                                </div>

							    <div class="form-group">
							    	<label><input @if($fetchedData->show_dashboard_per == 1) checked @endif value="1" type="checkbox" name="show_dashboard_per" class="show_dashboard_per"> Can view on dasboard</label>
								</div>
							</div>
						</div>
					</div>

					<!-- Migration Agent Details Section -->
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="form-group">
									<label class="d-flex align-items-center">
										<input type="checkbox" id="is_migration_agent" name="is_migration_agent" value="1" 
											@if($fetchedData->is_migration_agent == 1) checked @endif class="mr-2">
										<h5 class="mb-0">Is this user a Migration Agent?</h5>
									</label>
								</div>

								<!-- Agent Details Fields -->
								<div id="agent_details_section" style="display: {{ $fetchedData->is_migration_agent == 1 ? 'block' : 'none' }};">
									<hr>
									<h6 class="text-primary mb-3">Migration Agent Registration Details</h6>
									
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="marn_number">MARN Number</label>
												<input type="text" name="marn_number" id="marn_number" value="{{ old('marn_number', @$fetchedData->marn_number) }}" class="form-control" placeholder="Enter MARN Number">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="legal_practitioner_number">Legal Practitioner Number</label>
												<input type="text" name="legal_practitioner_number" value="{{ old('legal_practitioner_number', @$fetchedData->legal_practitioner_number) }}" class="form-control" placeholder="Enter Legal Practitioner Number">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="exempt_person_reason">Exempt Person Reason (if applicable)</label>
												<textarea name="exempt_person_reason" class="form-control" rows="2" placeholder="Enter reason if exempt person">{{ old('exempt_person_reason', @$fetchedData->exempt_person_reason) }}</textarea>
											</div>
										</div>
									</div>

									<h6 class="text-primary mb-3 mt-4">Business Details</h6>
									
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="company_name">Business Name</label>
												<input type="text" name="company_name" value="{{ old('company_name', @$fetchedData->company_name) }}" class="form-control" placeholder="Enter Business Name">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="tax_number">Tax Number (ABN/ACN)</label>
												<input type="text" name="tax_number" value="{{ old('tax_number', @$fetchedData->tax_number) }}" class="form-control" placeholder="Enter Tax Number">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="business_address">Business Address</label>
												<textarea name="business_address" class="form-control" rows="2" placeholder="Enter Business Address">{{ old('business_address', @$fetchedData->business_address) }}</textarea>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="business_phone">Business Phone</label>
												<input type="text" name="business_phone" value="{{ old('business_phone', @$fetchedData->business_phone) }}" class="form-control" placeholder="Enter Business Phone">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="business_mobile">Business Mobile</label>
												<input type="text" name="business_mobile" value="{{ old('business_mobile', @$fetchedData->business_mobile) }}" class="form-control" placeholder="Enter Business Mobile">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="business_email">Business Email</label>
												<input type="email" name="business_email" value="{{ old('business_email', @$fetchedData->business_email) }}" class="form-control" placeholder="Enter Business Email">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="business_fax">Business Fax</label>
												<input type="text" name="business_fax" value="{{ old('business_fax', @$fetchedData->business_fax) }}" class="form-control" placeholder="Enter Business Fax">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12">
						<div class="form-group float-right">
							<input type="submit" value="Update User" class="btn btn-primary">
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
	// Toggle Migration Agent Details Section
	$('#is_migration_agent').on('change', function() {
		if ($(this).is(':checked')) {
			$('#agent_details_section').slideDown();
		} else {
			$('#agent_details_section').slideUp();
		}
	});
});
</script>
@endsection
