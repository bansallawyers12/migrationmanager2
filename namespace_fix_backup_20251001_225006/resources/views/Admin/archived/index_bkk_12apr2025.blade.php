@extends('layouts.admin')
@section('title', 'Clients')

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
							<h4>All Clients</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;">
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="3"checked /> Agent</label>
										<label class="dropdown-option"><input type="checkbox" value="4"checked /> Tag(s)</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Current City</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Archived By</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Archived On</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Added On</label>
									</div>
								</div>
								<a href="{{route('admin.clients.create')}}" class="btn btn-primary">Create Client</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">

								<li class="nav-item">
									<a class="nav-link " id="clients-tab"  href="{{URL::to('/admin/clients')}}" >Clients</a>
								</li>
								<li class="nav-item ">
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/admin/archived')}}" >Archived</a>
								</li>

                                <li class="nav-item is_checked_clientn">
									<a class="nav-link" id="lead-tab"  href="{{URL::to('/admin/leads')}}" >Leads</a>
								</li>
							</ul>
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="archived" role="tabpanel" aria-labelledby="archived-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>
													<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
													</th>
													<th>Name</th>
													<th>Agent</th>
													<th>Tag(s)</th>
													<th>Current City</th>

													<th>Assignee</th>
													<th>Archived By</th>
													<th>Archived On</th>
													<th>Added On</th>
													<th></th>
												</tr>
											</thead>

											<tbody class="tdata">
												@if(@$totalData !== 0)
												@foreach (@$lists as $list)
												<tr id="id_{{$list->id}}">
													<td style="white-space: initial;" class="text-center">
														<div class="custom-checkbox custom-control">
															<input type="checkbox" data-checkboxes="mygroup" class="custom-control-input" id="checkbox-1">
															<label for="checkbox-1" class="custom-control-label">&nbsp;</label>
														</div>
													</td>
													<td style="white-space: initial;"> {{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }} {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }}</td>
													<?php
													$agent = \App\Agent::where('id', $list->agent_id)->first();
													?>
													<td style="white-space: initial;">@if($agent) <a target="_blank" href="{{URL::to('/admin/agent/detail/'.base64_encode(convert_uuencode(@$agent->id)))}}">{{@$agent->full_name}}<a/>@else - @endif</td>
													<td style="white-space: initial;">-</td>
													<td style="white-space: initial;">{{@$list->city}}</td>
													<?php
													$assignee = \App\Models\Admin::where('id',@$list->assignee)->first();
													?>
													<td style="white-space: initial;">{{ @$assignee->first_name == "" ? config('constants.empty') : str_limit(@$assignee->first_name, '50', '...') }}</td>
													<td style="white-space: initial;">{{date('d/m/Y', strtotime($list->archived_on))}}</td>
													<td style="white-space: initial;">-</td>
													<td style="white-space: initial;">{{date('d/m/Y', strtotime($list->created_at))}}</td>
													<td style="white-space: initial;">
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">

																<a class="dropdown-item has-icon" href="javascript:;" onclick="movetoclientAction({{$list->id}}, 'admins','is_archived')">Move to clients</a>
															</div>
														</div>
													</td>
												</tr>
												@endforeach
											</tbody>
											@else
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
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
