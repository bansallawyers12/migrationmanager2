@extends('layouts.admin_client_detail')
@section('title', 'Users')

@section('content')

<div class="crm-container">
	<div class="main-content">
		<div class="row">
			    <div class="col-12 col-md-12 col-lg-12"><div class="custom-error-msg"></div></div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Users</h4>
							<div class="card-header-action">
								<a href="{{URL::to('admin/users/create')}}" class="btn btn-primary">Add User</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="user_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="active-tab"  href="{{URL::to('/admin/users/active')}}" >Active</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="inactive-tab"  href="{{URL::to('/admin/users/inactive')}}" >Inactive</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="invited-tab"  href="{{URL::to('/admin/users/invited')}}" >Invited</a>
								</li>

                                <form action="{{ route('admin.users.active') }}" method="get">
                                    <div class="" style="display: inline-flex;float: right;margin-left:680px;">
                                        <input id="search-input" type="search" name="search_by"  class="form-control" value="<?php  if(isset($_GET['search_by']) &&  $_GET['search_by'] != "") { echo $_GET['search_by'];} else { echo "";}?>" />
                                        <button id="search-button" type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
							</ul>
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
									<div class="table-responsive common_table">
										<table class="table">
											<thead>
												<tr>
												  <th>Name</th>
												  <th>Position</th>
												  <th>Office</th>
												  <th>Role</th>
												  <th>Status</th>
                                                  <th>Action</th>
												</tr>
											</thead>
											@if(@$totalData !== 0)
											@foreach (@$lists as $list)
										<?php
										$b = \App\Models\Branch::where('id', $list->office_id)->first();
										?>
											<tbody class="tdata">
												<tr id="id_{{@$list->id}}">
													<td><a href="{{URL::to('/admin/users/view')}}/{{$list->id}}">{{@$list->first_name.' '.@$list->last_name}}</a><br>{{@$list->email}}</td>
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
                                                    <td>
                                                        <?php
                                                        if(\Auth::user()->id != $list->id) //if loggedin user is not same
                                                        {?>
                                                            <div class="card-header-action">
                                                                <a href="{{URL::to('admin/users/edit/'.$list->id)}}" class="btn btn-primary">Edit User</a>
                                                            </div>
                                                        <?php
                                                        } ?>

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
</div>

@endsection
