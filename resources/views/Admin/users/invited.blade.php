@extends('layouts.admin')
@section('title', 'Users')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
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
									<a class="nav-link" id="inactive-tab"  href="{{URL::to('/admin/users/inactive')}}" >Inactive</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="invited-tab"  href="{{URL::to('/admin/users/invited')}}" >Invited</a>
								</li>								
							</ul>
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="invited" role="tabpanel" aria-labelledby="invited-tab">
									<div class="table-responsive common_table"> 
										<table class="table"> 
											<thead>
												<tr>
												  <th>Name</th>
												  <th>Position</th>
												  <th>Office</th> 
												  <th>Role</th>
												</tr> 
											</thead>
											@if(@$totalData !== 0)
											@foreach (@$lists as $list)
										<?php
										$b = \App\Models\Branch::where('id', $list->office_id)->first();
										?>
											<tbody class="tdata">	
												<tr id="id_{{@$list->id}}"> 
													<td><a href="{{URL::to('/admin/users/view')}}/{{$b->id ?? '#'}}">{{@$list->first_name}}</a><br>{{@$list->email}}</td> 
													<td></td> 
													<td><a href="{{URL::to('/admin/branch/view/')}}/{{$b->id ?? '#'}}">{{$b->office_name ?? ''}}</a></td> 
													
													
													<td>{{ @$list->usertype->name == "" ? config('constants.empty') : str_limit(@$list->usertype->name, '50', '...') }}</td>  
													
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