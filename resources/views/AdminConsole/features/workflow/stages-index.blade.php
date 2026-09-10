@extends('layouts.crm_client_detail')
@section('title', 'Workflow Stages: ' . ($workflow->name ?? ''))

@section('styles')
<style>
	/* Stacked compact actions — avoids dropdown clipping / side-by-side overflow in narrow cells */
	.workflow-stages-table td.workflow-stage-actions-col {
		white-space: nowrap !important;
		vertical-align: middle;
		width: 1%;
	}
	.workflow-stage-cell-actions {
		display: inline-flex;
		flex-wrap: nowrap;
		align-items: center;
		gap: 0.25rem;
	}
	.workflow-stage-cell-actions .btn {
		width: 2rem;
		height: 2rem;
		padding: 0;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		line-height: 1;
	}
	.workflow-stage-protected-lock {
		display: inline-flex;
		align-items: center;
		margin-left: 0.35rem;
		vertical-align: middle;
		color: #d97706;
		line-height: 1;
	}
	.workflow-stage-protected-lock__icon {
		width: 0.95rem;
		height: 0.95rem;
		stroke-width: 2.25;
	}
	.workflow-stage-portal-name {
		color: #6c757d;
	}
</style>
@endsection

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
							<h4>Workflow Stages: {{ $workflow->name }}</h4>
							<div class="card-header-action">
								<a href="{{ route('adminconsole.features.workflow.index') }}" class="btn btn-secondary">@icon('fa-arrow-left') Back to Workflows</a>
								<a href="{{ route('adminconsole.features.workflow.createStage', base64_encode(convert_uuencode($workflow->id))) }}" class="btn btn-primary">@icon('fa-plus') Add Stage</a>
							</div>
						</div>
						<div class="card-body">
							<p class="small mb-3 text-muted"><strong>Manage stages</strong> for this workflow below. Use <strong>Add Stage</strong> (top right) to add rows. Locked stages cannot be renamed or removed. You can mark a stage Protected when adding or editing it.</p>
							<div class="table-responsive common_table">
								<table class="table text_wrap workflow-stages-table">
									<thead>
										<tr>
											<th>Stage</th>
											<th>Portal Stage</th>
											<th>Total Matters</th>
											<th>Workflow Checklists</th>
											<th>Portal Tasklists</th>
											<th class="text-nowrap">Actions</th>
										</tr>
									</thead>
									@if($lists->count() > 0)
									<tbody>
									@foreach ($lists as $list)
									<?php $countmatters = $matterCounts[$list->id] ?? 0; ?>
									<?php $checklistCount = $checklistCounts[$list->id] ?? 0; ?>
									<?php $portalTasklistCount = $portalTasklistCounts[$list->id] ?? 0; ?>
									<?php $stageFrozen = $list->isFrozen(); ?>
									<?php $portalName = \App\Support\WorkflowV2Display::distinctClientLabelForStage($list->name); ?>
									<tr>
										<td>
											{{ $list->name ?: config('constants.empty', '—') }}
											@if($stageFrozen)
												@include('AdminConsole.features.workflow.partials.protected-lock')
											@endif
										</td>
										<td class="workflow-stage-portal-name">{{ $portalName ?: config('constants.empty', '—') }}</td>
										<td>{{ $countmatters }}</td>
										<td>{{ $checklistCount }}</td>
										<td>{{ $portalTasklistCount }}</td>
										<td class="workflow-stage-actions-col">
											<div class="workflow-stage-cell-actions">
												<a class="btn btn-sm btn-success" href="{{ route('adminconsole.features.workflow.stageChecklists', [base64_encode(convert_uuencode($workflow->id)), base64_encode(convert_uuencode($list->id))]) }}" data-bs-toggle="tooltip" title="Workflow Checklists" aria-label="Workflow Checklists">@icon('fa-tasks')</a>
												<a class="btn btn-sm btn-warning" href="{{ route('adminconsole.features.workflow.stagePortalTasklists', [base64_encode(convert_uuencode($workflow->id)), base64_encode(convert_uuencode($list->id))]) }}" data-bs-toggle="tooltip" title="Portal Tasklists" aria-label="Portal Tasklists">@icon('fa-mobile-alt')</a>
												<a class="btn btn-sm btn-primary" href="{{ route('adminconsole.features.workflow.edit', base64_encode(convert_uuencode($list->id))) }}" data-bs-toggle="tooltip" title="{{ $stageFrozen ? 'View (protected — name cannot be changed)' : 'Edit' }}" aria-label="{{ $stageFrozen ? 'View stage' : 'Edit' }}">@icon('fa-edit')</a>
												<a class="btn btn-sm btn-info" href="{{ route('adminconsole.features.workflow.createStage', base64_encode(convert_uuencode($workflow->id))) }}?after={{ rawurlencode(base64_encode(convert_uuencode($list->id))) }}" data-bs-toggle="tooltip" title="Add After" aria-label="Add After">@icon('fa-plus')</a>
												@if($stageFrozen)
												<span data-bs-toggle="tooltip" title="Protected stages cannot be deleted">
													<button type="button" class="btn btn-sm btn-outline-secondary" disabled aria-label="Delete">@icon('fa-trash')</button>
												</span>
												@else
												<a class="btn btn-sm btn-outline-danger" href="javascript:;" onclick="deleteAction({{ $list->id }}, 'workflow_stages')" data-bs-toggle="tooltip" title="Delete" aria-label="Delete">@icon('fa-trash')</a>
												@endif
											</div>
										</td>
									</tr>
									@endforeach
									</tbody>
									@else
									<tbody>
										<tr><td colspan="6" class="text-center">No stages. <a href="{{ route('adminconsole.features.workflow.createStage', base64_encode(convert_uuencode($workflow->id))) }}">Add stage</a>.</td></tr>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
		document.querySelectorAll('.workflow-stage-cell-actions [data-bs-toggle="tooltip"]').forEach(function (el) {
			new bootstrap.Tooltip(el);
		});
	}
});
</script>
@endsection
