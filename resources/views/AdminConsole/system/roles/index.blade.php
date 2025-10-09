@extends('layouts.admin_client_detail')
@section('title', 'Roles and Permissions')

@section('content')

<div class="crm-container">
	<div class="main-content">
		<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Roles and Permissions</h4>
							<div class="card-header-action">
								<a href="{{route('admin.userrole.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
							</div>
						</div>
						<div class="card-body">
							<table class="table">
								<thead>
									<tr>
										<th>Name</th>	
										<th>Description</th>
										<th>No. of Permission</th>
										<th></th>
									</tr> 
								</thead>
								<tbody class="tdata">	
								@if(@$totalData !== 0)
								@foreach (@$lists as $list)
								<?php
								/* echo '<pre>';
								print_r(json_decode($list->module_access));
								echo '<pre>'; */
								?>
								<?php $newarray = json_decode($list->module_access);
									$module_access = (array) $newarray;								
								?>
									<tr id="id_{{@$list->id}}"> 
										<td>{{ @$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...') }}</td> 
										<td>{{ @$list->description == "" ? config('constants.empty') : str_limit(@$list->description, '50', '...') }}</td> 
										<td><?php echo count($module_access); ?></td> 
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{URL::to('/admin/userrole/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
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
</div>

@endsection