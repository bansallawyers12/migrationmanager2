@extends('layouts.admin')
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
			<form action="{{ url('admin/users/edit') }}" name="edit-user" autocomplete="off" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ @$fetchedData->id }}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Users</h4>
								<div class="card-header-action">
									<a href="{{route('admin.users.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
                                            $permission_arr = array($fetchedData->permission);
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

								<div class="form-group float-right">
									<input type="submit" value="Update User" class="btn btn-primary">
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>
@endsection
