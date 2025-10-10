@extends('layouts.admin_client_detail')
@section('title', 'Block Slot')

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
			        	@include('../Elements/Admin/setting')
		        </div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>Block Slot</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.features.appointmentdisabledate.create')}}" class="btn btn-primary">Add Block Slot</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table">
								<table class="table text_wrap">
								<thead>
									<tr>
                                        <th>Person</th>
										<th>Block Slot</th>
										<th>Action</th>
									</tr>
								</thead>
								@if(@$totalData !== 0)
								<tbody class="tdata">
								@foreach (@$groupedSlots as $personName => $personSlots)
									@foreach ($personSlots as $index => $slot)
									<tr id="id_{{@$slot['id']}}">
                                        <td>
											@if($index === 0)
												<strong>{{ $personName }}</strong>
											@endif
										</td>
										<td>
                                            {{ $slot['date'] }} - {{ $slot['slots'] }}
                                        </td>

                                        <td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{route('adminconsole.features.appointmentdisabledate.edit', base64_encode(convert_uuencode($slot['id'])))}}"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon text-danger" href="#" onclick="deleteSlot({{ $slot['id'] }})"><i class="far fa-trash-alt"></i> Delete</a>
												</div>
											</div>
										</td>
									</tr>
									@endforeach
								@endforeach
								</tbody>
								@else
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="3">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this block slot? This action cannot be undone.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script>
let slotToDelete = null;

function deleteSlot(slotId) {
	slotToDelete = slotId;
	$('#deleteModal').modal('show');
}

$('#confirmDelete').click(function() {
	if (slotToDelete) {
		// Create a form to submit DELETE request
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = '{{ route("adminconsole.features.appointmentdisabledate.destroy", ":id") }}'.replace(':id', btoa(encodeURIComponent(slotToDelete)));
		
		// Add CSRF token
		const csrfToken = document.createElement('input');
		csrfToken.type = 'hidden';
		csrfToken.name = '_token';
		csrfToken.value = '{{ csrf_token() }}';
		form.appendChild(csrfToken);
		
		// Add method override for DELETE
		const methodField = document.createElement('input');
		methodField.type = 'hidden';
		methodField.name = '_method';
		methodField.value = 'DELETE';
		form.appendChild(methodField);
		
		document.body.appendChild(form);
		form.submit();
	}
});

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
