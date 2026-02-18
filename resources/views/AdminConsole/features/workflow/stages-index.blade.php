@extends('layouts.crm_client_detail')
@section('title', 'Workflow Stages: ' . ($workflow->name ?? ''))

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
							<div class="table-responsive common_table">
								<table class="table text_wrap">
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
									<?php $countmatters = \App\Models\ClientMatter::where('workflow_stage_id', $list->id)->where('workflow_id', $workflow->id)->count(); ?>
									<tr>
										<td>{{ $list->name ?: config('constants.empty', '—') }}</td>
										<td>{{ $countmatters }}</td>
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item" href="{{ route('adminconsole.features.workflow.edit', base64_encode(convert_uuencode($list->id))) }}"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item" href="javascript:;" onclick="deleteAction({{ $list->id }}, 'workflow_stages')"><i class="fas fa-trash"></i> Delete</a>
												</div>
											</div>
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
