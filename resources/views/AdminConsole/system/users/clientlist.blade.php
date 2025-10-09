@extends('layouts.admin')
@section('title', 'Client List')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Client List</h4>
							<div class="card-header-action">
								<a href="{{route('admin.users.createclient')}}" class="btn btn-primary">Create Client</a>
							</div>
						</div>
						<div class="card-body">
							<table class="table">
								<thead>
									<tr>
										<th>Company Name</th>
										<th>Owner Name</th>
										<th>Email</th>
										<th>Phone</th>
										<th>Is Active</th>
										<th></th>
									</tr> 
								</thead>
								<tbody class="tdata">	
									@if(@$totalData !== 0)
									@foreach (@$lists as $list)	
									<tr id="id_{{@$list->id}}"> 
										<td>{{ @$list->company_name == "" ? config('constants.empty') : str_limit(@$list->company_name, '50', '...') }}</td> 
										<td>{{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }} {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }}</td> 
										<td>{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...') }}</td> 
										<td>{{ @$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...') }}</td>
										<td>
											<label class="custom-switch">
												<input type="checkbox" name="is_active" class="custom-switch-input" data-id="{{@$list->id}}"  data-status="{{@$list->status}}" data-col="status" data-table="admins" name="option" {{ (@$list->status == 1 ? 'checked' : '')}}>
												<span class="custom-switch-indicator"></span>
											</label>
										</td>
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{URL::to('/admin/users/editclient/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'admins')"><i class="fas fa-trash"></i> Delete</a>
												</div>
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