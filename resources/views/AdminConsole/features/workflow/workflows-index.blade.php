@extends('layouts.crm_client_detail')
@section('title', 'Workflows')

@section('styles')
<style>
    /* Fix dropdown menu positioning for action buttons */
    .table-responsive {
        overflow: visible !important;
    }
    
    .common_table .table td {
        overflow: visible !important;
    }
    
    .common_table .table td .dropdown {
        position: relative;
        display: inline-block;
        overflow: visible !important;
    }
    
    .common_table .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        float: none !important;
        min-width: 180px;
        padding: 8px 0;
        margin: 4px 0 0;
        font-size: 14px;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 9999 !important;
        overflow: visible !important;
    }
    
    .common_table .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Always-visible workflow row actions — icons only, label on hover */
    .workflows-index-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.25rem;
    }
    .workflows-index-actions .btn {
        width: 2rem;
        height: 2rem;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
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
							<h4>Workflows</h4>
							<div class="card-header-action">
								<a href="{{ route('adminconsole.features.workflow.create') }}" class="btn btn-primary">Add Workflow</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table">
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Workflow Name</th>
											<th>Linked Matter</th>
											<th>Stages</th>
											<th class="text-nowrap">Actions</th>
										</tr>
									</thead>
									@if($lists->count() > 0)
									<tbody>
									@foreach ($lists as $wf)
									<tr>
										<td>{{ $wf->name }}</td>
										<td>{{ $wf->matter ? $wf->matter->title : '—' }}</td>
										<td>{{ $wf->stages->count() }}</td>
										<td>
											<div class="workflows-index-actions">
												<a class="btn btn-sm btn-primary" href="{{ route('adminconsole.features.workflow.stages', base64_encode(convert_uuencode($wf->id))) }}" data-bs-toggle="tooltip" title="Manage Stages" aria-label="Manage Stages">@icon('fa-list')</a>
												<a class="btn btn-sm btn-secondary" href="{{ route('adminconsole.features.workflow.editWorkflow', base64_encode(convert_uuencode($wf->id))) }}" data-bs-toggle="tooltip" title="Edit Workflow" aria-label="Edit Workflow">@icon('fa-edit')</a>
											</div>
										</td>
									</tr>
									@endforeach
									</tbody>
									@else
									<tbody>
										<tr><td colspan="4" class="text-center">No workflows found. <a href="{{ route('adminconsole.features.workflow.create') }}">Create one</a>.</td></tr>
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
		document.querySelectorAll('.workflows-index-actions [data-bs-toggle="tooltip"]').forEach(function (el) {
			new bootstrap.Tooltip(el);
		});
	}
});
</script>
@endsection
