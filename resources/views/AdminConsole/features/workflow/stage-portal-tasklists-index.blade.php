@extends('layouts.crm_client_detail')
@section('title', 'Portal Tasklists: ' . ($stage->name ?? ''))

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="row">
				<div class="col-3 col-md-3 col-lg-3">
					@include('../Elements/CRM/setting')
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>Portal Tasklists: {{ $stage->name }}</h4>
							<div class="card-header-action">
								<a href="{{ route('adminconsole.features.workflow.stages', base64_encode(convert_uuencode($workflow->id))) }}" class="btn btn-secondary">@icon('fa-arrow-left') Back to Stages</a>
							</div>
						</div>
						<div class="card-body">
							<p class="small text-muted mb-4">
								<strong>Workflow:</strong> {{ $workflow->name }} —
								These tasks are copied onto <strong>every client matter</strong> using this workflow and appear on Client Portal → Documents.
								Staff checklists stay on the Workflow Checklists button. Items added from Client Portal → Add Portal Checklist stay on that matter only.
							</p>

							<form method="post" action="{{ route('adminconsole.features.workflow.syncPortalTasklists', base64_encode(convert_uuencode($workflow->id))) }}" class="mb-3">
								@csrf
								<button type="submit" class="btn btn-outline-primary btn-sm">@icon('fa-sync') Apply all client portal tasks to existing matters</button>
							</form>

							<div class="card border mb-4">
								<div class="card-header">
									<h5 class="mb-0">@icon('fa-plus') Add Portal Task</h5>
								</div>
								<div class="card-body">
									<form method="post" action="{{ route('adminconsole.features.workflow.storeStagePortalTasklist') }}">
										@csrf
										<input type="hidden" name="workflow_id" value="{{ $workflow->id }}">
										<input type="hidden" name="workflow_stage_id" value="{{ $stage->id }}">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="name">Task Name <span class="span_req">*</span></label>
													<input type="text" name="name" id="name" class="form-control" maxlength="255" value="{{ old('name') }}" required placeholder="e.g. Service agreement">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="task_type">Task Type <span class="span_req">*</span></label>
													<select name="task_type" id="task_type" class="form-control" required>
														@foreach($taskTypes as $type)
															<option value="{{ $type->value }}" @selected(old('task_type', 'upload') === $type->value)>{{ $type->label() }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="description">Description <small class="text-muted">(optional)</small></label>
													<input type="text" name="description" id="description" class="form-control" maxlength="1000" value="{{ old('description') }}" placeholder="Optional notes shown to the client">
												</div>
											</div>
											<div class="col-md-6">
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input" id="allow_client" name="allow_client" value="1" {{ old('allow_client', '1') ? 'checked' : '' }}>
													<label class="custom-control-label" for="allow_client">Allow For Client</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input" id="is_required" name="is_required" value="1" {{ old('is_required', '1') ? 'checked' : '' }}>
													<label class="custom-control-label" for="is_required">Required</label>
												</div>
											</div>
											<div class="col-12">
												<button type="submit" class="btn btn-primary">@icon('fa-save') Save Task</button>
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="table-responsive common_table">
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>#</th>
											<th>Task Name</th>
											<th>Type</th>
											<th>Description</th>
											<th>Allow Client</th>
											<th>Required</th>
											<th>Actions</th>
										</tr>
									</thead>
									@if($tasklists->count() > 0)
									<tbody>
										@foreach($tasklists as $index => $item)
										@php
											$itemType = $item->task_type instanceof \App\Enums\PortalTaskType
												? $item->task_type
												: (\App\Enums\PortalTaskType::tryFrom((string) $item->task_type) ?? \App\Enums\PortalTaskType::Upload);
										@endphp
										<tr>
											<td>{{ $index + 1 }}</td>
											<td>{{ $item->name }}</td>
											<td>{{ $itemType->label() }}</td>
											<td>{{ $item->description ?: '—' }}</td>
											<td>{{ $item->allow_client ? 'Yes' : 'No' }}</td>
											<td>{{ $item->is_required ? 'Yes' : 'No' }}</td>
											<td>
												<button type="button"
													class="btn btn-sm btn-primary btn-edit-stage-portal-tasklist"
													data-name="{{ e($item->name) }}"
													data-description="{{ e($item->description ?? '') }}"
													data-task-type="{{ $itemType->value }}"
													data-allow-client="{{ $item->allow_client ? '1' : '0' }}"
													data-is-required="{{ $item->is_required ? '1' : '0' }}"
													data-update-url="{{ route('adminconsole.features.workflow.updateStagePortalTasklist', base64_encode(convert_uuencode($item->id))) }}">
													@icon('fa-edit') Edit
												</button>
												<form method="post" action="{{ route('adminconsole.features.workflow.destroyStagePortalTasklist', base64_encode(convert_uuencode($item->id))) }}" class="d-inline" onsubmit="return confirm('Remove this client portal task template? Existing client tasks will not be deleted.');">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-outline-danger">@icon('fa-trash') Delete</button>
												</form>
											</td>
										</tr>
										@endforeach
									</tbody>
									@else
									<tbody>
										<tr>
											<td colspan="7" class="text-center text-muted">No portal tasks for this stage yet.</td>
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

<div class="modal fade" id="editStagePortalTasklistModal" tabindex="-1" role="dialog" aria-labelledby="editStagePortalTasklistModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editStagePortalTasklistModalLabel">@icon('fa-edit') Edit Portal Task</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="edit_stage_portal_tasklist_form" action="">
				@csrf
				@method('PUT')
				<div class="modal-body">
					<div class="form-group">
						<label for="edit_portal_task_name">Task Name <span class="span_req">*</span></label>
						<input type="text" name="name" id="edit_portal_task_name" class="form-control" maxlength="255" required>
					</div>
					<div class="form-group">
						<label for="edit_portal_task_type">Task Type <span class="span_req">*</span></label>
						<select name="task_type" id="edit_portal_task_type" class="form-control" required>
							@foreach($taskTypes as $type)
								<option value="{{ $type->value }}">{{ $type->label() }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="edit_portal_task_description">Description <small class="text-muted">(optional)</small></label>
						<input type="text" name="description" id="edit_portal_task_description" class="form-control" maxlength="1000">
					</div>
					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="edit_portal_allow_client" name="allow_client" value="1">
							<label class="custom-control-label" for="edit_portal_allow_client">Allow For Client</label>
						</div>
					</div>
					<div class="form-group mb-0">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="edit_portal_is_required" name="is_required" value="1">
							<label class="custom-control-label" for="edit_portal_is_required">Required</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">@icon('fa-save') Save Task</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.btn-edit-stage-portal-tasklist').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var form = document.getElementById('edit_stage_portal_tasklist_form');
			form.action = btn.getAttribute('data-update-url') || '';
			document.getElementById('edit_portal_task_name').value = btn.getAttribute('data-name') || '';
			document.getElementById('edit_portal_task_description').value = btn.getAttribute('data-description') || '';
			document.getElementById('edit_portal_task_type').value = btn.getAttribute('data-task-type') || 'upload';
			document.getElementById('edit_portal_allow_client').checked = btn.getAttribute('data-allow-client') === '1';
			document.getElementById('edit_portal_is_required').checked = btn.getAttribute('data-is-required') === '1';
			if (typeof $ !== 'undefined' && $('#editStagePortalTasklistModal').modal) {
				$('#editStagePortalTasklistModal').modal('show');
			}
		});
	});
});
</script>
@endsection
