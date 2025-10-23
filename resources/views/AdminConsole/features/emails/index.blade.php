@extends('layouts.crm_client_detail')
@section('title', 'Emails')
 
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
				 <div class="col-3 col-md-3 col-lg-3">
			        	@include('../Elements/CRM/setting')
		        </div>       
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>All Emails</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.features.emails.create')}}" class="btn btn-primary">Create Emails</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										
										<th>Name</th>
										<th>User Sharing</th>
										<th>Status</th>
										<th></th>
									</tr> 
								</thead>
								@if(@$totalData !== 0)
								<?php $i=0; ?>
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<?php
										$userids = json_decode($list->user_id);
										$username = '';
										foreach($userids as $userid){
											$users = \App\Models\Admin::where('id', $userid)->first();
											$username .= $users->first_name.', ';
										}
									?>
									<tr id="id_{{@$list->id}}">
										
										<td>{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...') }}</td> 	
										<td>{{ @$username == "" ? config('constants.empty') : str_limit(rtrim(@$username,', '), '50', '...') }}</td> 	
										<td>
										<?php
										if($list->status == 1){ echo '<span class=" text-success">Active</span>'; }else{
											echo '<span class=" text-danger">Inactive</span>';
										}
										?>
										</td> 	
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{route('adminconsole.features.emails.edit', base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'emails')"><i class="fas fa-trash"></i> Delete</a>
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
	</section>
</div>
 
@endsection