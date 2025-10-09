@extends('layouts.admin_client_detail')
@section('title', 'Branches')

@section('content')

<div class="crm-container">
	<div class="main-content">
		<div class="server-error">
			@include('../Elements/flash-message')
		</div>
		<div class="custom-error-msg">
		</div>
		<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>All Branches</h4>
							<div class="card-header-action">
								<a href="{{route('admin.branch.create')}}" class="btn btn-primary">Create Branch</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Name</th>
											<th>City</th>
											<th>Country</th>
											<th>Mobile</th>
											<th>Phone</th>
											<th>Contact Person</th>
											<th></th>
										</tr> 
									</thead>
									@if(@$totalData !== 0)
									@foreach (@$lists as $list)
									<tbody class="tdata">	
										<tr id="id_{{@$list->id}}">
											<td><a href="{{URL::to('/admin/branch/view')}}/{{$list->id}}">{{ @$list->office_name == "" ? config('constants.empty') : str_limit(@$list->office_name, '50', '...') }}</a></td> 
											<td>{{ @$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...') }}</td> 
											<td>{{ @$list->country == "" ? config('constants.empty') : str_limit(@$list->country, '50', '...') }}</td> 
											<td>{{ @$list->mobile == "" ? config('constants.empty') : str_limit(@$list->mobile, '50', '...') }}</td> 
											<td>{{ @$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...') }}</td> 
											<td>{{ @$list->contact_person == "" ? config('constants.empty') : str_limit(@$list->contact_person, '50', '...') }}</td> 	
											<td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu">
														<a class="dropdown-item has-icon" href="{{URL::to('/admin/branch/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
														<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'branches')"><i class="fas fa-trash"></i> Delete</a>
													</div>
												</div>								  
											</td>
										</tr>	
									@endforeach	
									</tbody>
									@else
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="7">
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
			</div>
	</div>
</div>

@endsection