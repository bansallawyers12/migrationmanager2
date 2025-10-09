@extends('layouts.admin')
@section('title', 'Users')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
			     <div class="col-12 col-md-12 col-lg-12"><div class="custom-error-msg"></div></div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Users</h4>
							<div class="card-header-action">
								<a href="javascript:;" class="btn btn-primary">Invite User</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="user_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="active-tab"  href="{{URL::to('/admin/users/active')}}" >Active</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="inactive-tab"  href="{{URL::to('/admin/users/inactive')}}" >Inactive</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="invited-tab"  href="{{URL::to('/admin/users/invited')}}" >Invited</a>
								</li>
							</ul>
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
									<div class="table-responsive common_table">
										<table class="table">
											<thead>
												<tr>
												  <th>Name</th>
												  <th>Position</th>
												  <th>Office</th>
												  <th>Role</th>
												  <th>Status</th>
												</tr>
											</thead>
											@if(@$totalData !== 0)
											@foreach (@$lists as $list)
										<?php
										$b = \App\Models\Branch::where('id', $list->office_id)->first();
										?>
											<tbody class="tdata">
												<tr id="id_{{@$list->id}}">
													<td><a href="{{URL::to('/admin/users/view')}}/{{@$list->id}}">{{@$list->first_name.' '.@$list->last_name}}</a><br>{{@$list->email}}</td>
													<td>{{@$list->position}}</td>
													<td><a href="{{URL::to('/admin/branch/view/')}}/{{@$b->id}}">{{@$b->office_name}}</a></td>


													<td>{{ @$list->usertype->name == "" ? config('constants.empty') : str_limit(@$list->usertype->name, '50', '...') }}</td>
													<td>
													    <div class="custom-switches">
									<label class="">
										<input value="1" data-id="{{@$list->id}}"  data-status="{{@$list->status}}" data-col="status" data-table="admins" type="checkbox" name="custom-switch-checkbox" class="change-status custom-switch-input" {{ (@$list->status == 1 ? 'checked' : '')}}>
										<span class="custom-switch-indicator"></span>
									</label>
								</div>
													</td>
												</tr>
											@endforeach
											</tbody>
											@else
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="6">
														No Record found
													</td>
												</tr>
											</tbody>
											@endif
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection
