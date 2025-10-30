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
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>View User</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.system.users.edit', $fetchedData->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
								<a href="{{route('adminconsole.system.users.active')}}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-6">
					<div class="card">
						<div class="card-body">
							<h4>PERSONAL DETAILS</h4>
							<div class="form-group">
								<label for="first_name"><strong>First Name</strong></label>
								<p class="form-control-plaintext">{{ $fetchedData->first_name ?? 'N/A' }}</p>
							</div>
							<div class="form-group">
								<label for="last_name"><strong>Last Name</strong></label>
								<p class="form-control-plaintext">{{ $fetchedData->last_name ?? 'N/A' }}</p>
							</div>
							<div class="form-group">
								<label for="email"><strong>Email</strong></label>
								<p class="form-control-plaintext">{{ $fetchedData->email ?? 'N/A' }}</p>
							</div>
							<div class="form-group">
								<label for="phone"><strong>Phone Number</strong></label>
								<p class="form-control-plaintext">
									@if($fetchedData->telephone)
										{{ $fetchedData->telephone }} 
									@endif
									{{ $fetchedData->phone ?? 'N/A' }}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-6">
					<div class="card">
						<div class="card-body">
							<h4>OFFICE DETAILS</h4>
							<div class="form-group">
								<label for="position"><strong>Position Title</strong></label>
								<p class="form-control-plaintext">{{ $fetchedData->position ?? 'N/A' }}</p>
							</div>
							<div class="form-group">
								<label for="role"><strong>User Role (Type)</strong></label>
								<p class="form-control-plaintext">{{ optional($fetchedData->usertype)->name ?? 'N/A' }}</p>
							</div>
							<div class="form-group">
								<label for="office"><strong>Office</strong></label>
								<p class="form-control-plaintext">
									<?php
									$branchx = \App\Models\Branch::where('id', $fetchedData->office_id)->first();
									?>
									{{ $branchx->office_name ?? 'N/A' }}
								</p>
							</div>
							<div class="form-group">
								<label for="team"><strong>Department (Team)</strong></label>
								<p class="form-control-plaintext">
									<?php
									if($fetchedData->team != ""){
										$teamData = \App\Models\Team::select('name')->where('id', '=', $fetchedData->team)->first();
										$teamname = $teamData ? $teamData->name : "N/A";
									} else {
										$teamname = "N/A";
									}
									?>
									{{ $teamname }}
								</p>
							</div>
							<div class="form-group">
								<label for="permission"><strong>Permission</strong></label>
								<div class="form-control-plaintext">
									<?php
									if( isset($fetchedData->permission) && $fetchedData->permission !="")
									{
										if( strpos($fetchedData->permission,",") ){
											$permission_arr =  explode(",",$fetchedData->permission);
										} else {
											$permission_arr = array($fetchedData->permission);
										}
										?>
										<div><strong>Notes:</strong> 
											@if(in_array(1, $permission_arr)) <span class="badge badge-success">View</span> @endif
											@if(in_array(2, $permission_arr)) <span class="badge badge-success">Add/Edit</span> @endif
											@if(in_array(3, $permission_arr)) <span class="badge badge-success">Delete</span> @endif
										</div>
										<div class="mt-2"><strong>Documents:</strong> 
											@if(in_array(4, $permission_arr)) <span class="badge badge-info">View</span> @endif
											@if(in_array(5, $permission_arr)) <span class="badge badge-info">Add/Edit</span> @endif
											@if(in_array(6, $permission_arr)) <span class="badge badge-info">Delete</span> @endif
										</div>
									<?php
									}
									else
									{
									?>
										<span class="text-muted">No permissions assigned</span>
									<?php
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label for="show_dashboard_per"><strong>Dashboard View Permission</strong></label>
								<p class="form-control-plaintext">
									@if($fetchedData->show_dashboard_per == 1)
										<span class="badge badge-success">Can view dashboard</span>
									@else
										<span class="badge badge-secondary">Cannot view dashboard</span>
									@endif
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
