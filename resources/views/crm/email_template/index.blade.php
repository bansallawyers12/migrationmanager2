@extends('layouts.crm_client_detail')
@section('title', 'Email Templates')

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
							<h4>Email Templates</h4>
							<div class="card-header-action">
								<a href="{{route('email.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Create Email Template</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table">
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>ID</th>
											<th>Name</th>
											<th>Subject</th>
											<th></th>
										</tr> 
									</thead>
									<tbody class="tdata">	
										@if(@$totalData !== 0)
										@foreach (@$lists as $list)	
										<tr id="id_{{@$list->id}}"> 
											<td>{{ @$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...') }}</td> 
											<td>{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '50', '...') }}</td>
											<td>{{ @$list->subject == "" ? config('constants.empty') : str_limit(@$list->subject, '50', '...') }}</td>
											<td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu">
														<a class="dropdown-item has-icon" href="{{URL::to('/edit_email_template/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
														<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'email_templates')"><i class="fas fa-trash"></i> Delete</a>
													</div>
												</div>
											</td>
										</tr>	
										@endforeach						
									</tbody>
									@else
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="4">
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
	</section>
</div>
@endsection