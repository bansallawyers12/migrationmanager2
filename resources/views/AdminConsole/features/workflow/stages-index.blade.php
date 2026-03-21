@extends('layouts.crm_client_detail')
@section('title', 'Workflow Stages: ' . ($workflow->name ?? ''))

@section('styles')
<style>
	/* Stacked compact actions — avoids dropdown clipping / side-by-side overflow in narrow cells */
	.workflow-stages-table td.workflow-stage-actions-col {
		white-space: normal !important;
		vertical-align: middle;
		width: 1%;
		min-width: 5.5rem;
	}
	.workflow-stage-cell-actions {
		display: flex;
		flex-direction: column;
		align-items: stretch;
		gap: 0.25rem;
	}
	.workflow-stage-cell-actions .btn {
		font-size: 0.75rem;
		padding: 0.2rem 0.45rem;
		line-height: 1.25;
		white-space: nowrap;
		text-align: center;
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
								<a href="{{ route('adminconsole.features.workflow.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to Workflows</a>
								<a href="{{ route('adminconsole.features.workflow.createStage', base64_encode(convert_uuencode($workflow->id))) }}" class="btn btn-primary">Add Stage</a>
							</div>
						</div>
						<div class="card-body">
							<p class="text-muted small mb-3">Stages labelled <span class="badge badge-secondary">Frozen</span> are required for the system (e.g. Checklist, Verification, Decision Received, Ready to Close, File Closed) and cannot be edited or deleted.</p>
							<div class="table-responsive common_table">
								<table class="table text_wrap workflow-stages-table">
									<thead>
										<tr>
											<th>Stage</th>
											<th>Total Matters</th>
											<th></th>
										</tr>
									</thead>
									@if($lists->count() > 0)
									<tbody>
									@foreach ($lists as $list)
									<?php $countmatters = $matterCounts[$list->id] ?? 0; ?>
									<?php $stageFrozen = $list->isFrozen(); ?>
									<tr>
										<td>
											{{ $list->name ?: config('constants.empty', '—') }}
											@if($stageFrozen)
											<span class="badge badge-secondary ml-1 align-middle" title="This stage cannot be renamed or deleted">Frozen</span>
											@endif
										</td>
										<td>{{ $countmatters }}</td>
										<td class="workflow-stage-actions-col">
											@if($stageFrozen)
											<div class="workflow-stage-cell-actions">
												<span class="text-muted small text-center py-1">Locked</span>
											</div>
											@else
											<div class="workflow-stage-cell-actions">
												<a class="btn btn-sm btn-primary" href="{{ route('adminconsole.features.workflow.edit', base64_encode(convert_uuencode($list->id))) }}"><i class="far fa-edit"></i> Edit</a>
												<a class="btn btn-sm btn-outline-danger" href="javascript:;" onclick="deleteAction({{ $list->id }}, 'workflow_stages')"><i class="fas fa-trash"></i> Delete</a>
											</div>
											@endif
										</td>
									</tr>
									@endforeach
									</tbody>
									@else
									<tbody>
										<tr><td colspan="3" class="text-center">No stages. <a href="{{ route('adminconsole.features.workflow.createStage', base64_encode(convert_uuencode($workflow->id))) }}">Add stage</a>.</td></tr>
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
