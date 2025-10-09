@extends('layouts.admin_client_detail')
@section('title', 'Promo Code')

@section('content')

<div class="crm-container">
	<div class="main-content">
		<div class="server-error">
			@include('../Elements/flash-message')
		</div>
		<div class="custom-error-msg">
		</div>
		<div class="row">
			 <div class="col-3 col-md-3 col-lg-3">
		        	@include('../Elements/Admin/setting')
	        </div>
			<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>Promo Code</h4>
							<div class="card-header-action">
								<a href="{{route('admin.feature.promocode.create')}}" class="btn btn-primary">Create Promo Code</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table">
								<table class="table text_wrap">
								<thead>
									<tr>
                                        <th>Title</th>
                                        <th>Code</th>
										<!--<th>Discount</th>-->
										<th></th>
									</tr>
								</thead>
								@if(@$totalData !== 0)
								<?php $i=0; ?>
								<tbody class="tdata">
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->id}}">
                                        <td><?php if(isset($list->title)){ echo $list->title; }?></td>
										<td><?php if(isset($list->code)){ echo $list->code; }?></td>
                                        <!--<td><?php //if(isset($list->discount_percentage)){ echo $list->discount_percentage; }?></td>-->
                                        <td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{URL::to('/admin/promo-code/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
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

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$('.cb-element').change(function () {
	if ($('.cb-element:checked').length == $('.cb-element').length){
	  $('#checkbox-all').prop('checked',true);
	}
	else {
	  $('#checkbox-all').prop('checked',false);
	}

	});
});
</script>
@endsection
