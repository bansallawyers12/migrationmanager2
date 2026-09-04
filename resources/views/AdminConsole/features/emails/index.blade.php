@extends('layouts.crm_client_detail')
@section('title', 'Emails')

@section('styles')
<style>
    /* Scoped to this list so other Admin Console tables are unchanged */
    .emails-list-page .card-body {
        overflow: visible !important;
    }
    .emails-list-table.table-responsive,
    .emails-list-table {
        overflow: visible !important;
    }
    .emails-list-table table,
    .emails-list-table td {
        overflow: visible !important;
    }
    .emails-list-table tbody tr {
        position: relative;
        z-index: 1;
    }
    .emails-list-table tbody tr.dropdown-open,
    .emails-list-table tbody tr:has(.dropdown.show) {
        z-index: 20;
    }
    .emails-list-table td .dropdown {
        position: relative;
        display: inline-block;
        overflow: visible !important;
    }
    .emails-list-table .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        float: none !important;
        min-width: 180px;
        padding: 8px 0;
        margin: 4px 0 0;
        font-size: 14px;
        text-align: left;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 21 !important;
        transform: none !important;
        max-height: none !important;
        overflow: visible !important;
    }
    .emails-list-table .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    .emails-list-table .dropdown-item {
        display: block;
        padding: 10px 20px;
        clear: both;
        color: #495057;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        text-decoration: none;
        border-radius: 4px;
        margin: 2px 8px;
        width: calc(100% - 16px);
    }
    .emails-list-table .dropdown-item:hover {
        color: #667eea;
        text-decoration: none;
        background: #f8f9fa;
    }
    .emails-list-table .dropdown-item:active {
        background: #e9ecef;
    }
    .emails-list-table .dropdown-item.has-icon {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .emails-list-table .dropdown-menu .dropdown-item {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        min-height: 32px !important;
        line-height: 1.5 !important;
    }
</style>
@endsection
 
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
				<div class="col-9 col-md-9 col-lg-9 emails-list-page">
					<div class="card">
						<div class="card-header">
							<h4>All Emails</h4>
							<div class="card-header-action">
								<a href="{{ route('adminconsole.features.emails.create') }}" class="btn btn-primary">@icon('fa-plus') Add New</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table emails-list-table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										<th>Name</th>
										<th>Display Name</th>
										<th>Email Signature</th>
										<th>User Sharing</th>
										<th>Status</th>
										<th></th>
									</tr> 
								</thead>
								@if(@$totalData !== 0)
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<tr id="id_{{ md5(@$list->email) }}">
										<td>{{ @$list->email == "" ? config('constants.empty') : Str::limit(@$list->email, '50', '...') }}</td> 	
										<td>{{ @$list->display_name == "" ? config('constants.empty') : Str::limit(@$list->display_name, '50', '...') }}</td>
										<td>{!! @$list->email_signature == "" ? config('constants.empty') : Str::limit(strip_tags(@$list->email_signature), '80', '...') !!}</td>
										<td>{{ @$list->user_sharing == "" ? config('constants.empty') : Str::limit(@$list->user_sharing, '50', '...') }}</td> 	
										<td>
										<?php
										if($list->status == 1){ echo '<span class=" text-success">Active</span>'; }else{
											echo '<span class=" text-danger">Inactive</span>';
										}
										?>
										</td>
										<td>
											@if(!empty($list->id))
											<div class="dropdown d-inline" data-bs-display="static">
												<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{ route('adminconsole.features.emails.edit', base64_encode(convert_uuencode(@$list->id))) }}">@icon('fa-edit') Edit</a>
												</div>
											</div>
											@endif
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
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
 
@endsection

@section('scripts')
<script>
(function ($) {
	var $table = $('.emails-list-table');
	if (!$table.length) {
		return;
	}
	$table.on('show.bs.dropdown', '.dropdown', function () {
		$(this).closest('tr').addClass('dropdown-open');
	}).on('hidden.bs.dropdown', '.dropdown', function () {
		$(this).closest('tr').removeClass('dropdown-open');
	});
})(jQuery);
</script>
@endsection
