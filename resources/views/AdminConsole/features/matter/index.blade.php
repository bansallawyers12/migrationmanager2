@extends('layouts.crm_client_detail')
@section('title', 'Matter')

@section('content')
<style>
    /* Filter Panel Styling */
    .filter_panel {
        margin-bottom: 30px;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: none;
    }

    .filter_panel h4 {
        color: #4a5568 !important;
        font-size: 1.1rem;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    /* Fix table header and text visibility */
    .table thead th {
        background-color: #f8f9fa !important;
        color: #343a40 !important;
        font-weight: 600 !important;
        border-bottom: 2px solid #dee2e6 !important;
        padding: 12px 15px !important;
    }
    
    .table tbody td {
        color: #495057 !important;
        padding: 12px 15px !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    /* Fix form label visibility */
    .form-group label {
        color: #495057 !important;
        font-weight: 500 !important;
        margin-bottom: 8px !important;
    }
    
    /* Card header styling */
    .card-header h4 {
        color: #343a40 !important;
        font-weight: 600 !important;
        margin: 0 !important;
    }
    
    /* Fix dropdown menu width and item sizing */
    .dropdown-menu {
        min-width: 200px !important;
        max-width: 220px !important;
        width: auto !important;
    }
    
    .dropdown-item {
        padding: 8px 12px !important;
        font-size: 0.9rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }
    
    .dropdown-item i {
        margin-right: 6px !important;
        width: 14px !important;
        text-align: center !important;
    }
    
    /* Ensure dropdown fits within viewport */
    .dropdown-menu {
        position: absolute !important;
        top: auto !important;
        bottom: 100% !important;
        z-index: 9999 !important;
        background-color: #fff !important;
        border: 1px solid #e9ecef !important;
        border-radius: 6px !important;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15) !important;
        margin-bottom: 2px !important;
        max-height: 400px !important;
        overflow-y: auto !important;
        display: none !important;
    }
    
    .dropdown-menu.show {
        display: block !important;
    }
    
    
    /* Ensure all icons and text are aligned consistently */
    .dropdown-item.has-icon {
        display: flex !important;
        align-items: center !important;
        padding: 8px 12px !important;
        font-size: 0.8rem !important;
        line-height: 1.2 !important;
        position: relative !important;
        min-height: 32px !important;
    }
    
    .dropdown-item.has-icon i {
        width: 14px !important;
        height: 14px !important;
        flex-shrink: 0 !important;
        text-align: center !important;
        display: inline-block !important;
        margin-right: 8px !important;
        position: static !important;
    }
    
    /* Ensure text starts at consistent position */
    .dropdown-item.has-icon {
        padding-left: 12px !important;
    }
    
    /* Handle long text with ellipsis */
    .dropdown-item.has-icon {
        max-width: 180px !important;
        overflow: visible !important;
        text-overflow: clip !important;
        white-space: nowrap !important;
    }
    
    /* Ensure all dropdown items are visible */
    .dropdown-menu .dropdown-item {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    /* Fix table overflow issues */
    .table-responsive {
        overflow: visible !important;
    }
    
    .table tbody tr {
        position: relative !important;
    }
    
    .table tbody tr td:last-child {
        overflow: visible !important;
        position: relative !important;
    }
    
    /* Ensure text doesn't wrap and fits properly */
    .dropdown-item span,
    .dropdown-item {
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }
    
    /* Ensure only one dropdown is visible at a time */
    .dropdown-menu:not(.show) {
        display: none !important;
    }
    
    .dropdown-menu.show {
        display: block !important;
    }
</style>
<div class="crm-container">
	<div class="main-content">
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
							<h4>All Matters</h4>
                            <div class="card-header-action">
                                <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn mr-2"><i class="fas fa-filter"></i> Filter</a>
                                <a href="{{route('adminconsole.features.matter.create')}}" class="btn btn-primary">Create Matter</a>
							</div>
						</div>
						<div class="card-body">
                            <div class="filter_panel"><h4>Search</h4>
                                <form action="{{route('adminconsole.features.matter.index')}}" method="get">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title" class="col-form-label" style="color:#495057 !important; font-weight: 500 !important;">Matter Name</label>
                                                <input type="text" name="title" value="{{ old('title', Request::get('title')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Select Matter" id="title">
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top:35px;">
                                            <button type="submit" class="btn btn-primary btn-theme-lg">Search</button>
                                            <a class="btn btn-info" href="{{route('adminconsole.features.matter.index')}}">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>

							<div class="table-responsive common_table">
								<table class="table text_wrap">
								<thead>
									<tr>
										<th>Matter Name</th>
										<th></th>
									</tr>
								</thead>
								@if(@$totalData !== 0)
								<?php $i=0; ?>
								<tbody class="tdata">
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->id}}">
										<td>{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '50', '...') }}</td>
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{route('adminconsole.features.matter.edit', base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'matters')"><i class="fas fa-trash"></i> Delete</a>
													<?php
													$hasTemplate = \App\Models\MatterEmailTemplate::where('matter_id', $list->id)->exists();
													?>
													@if($hasTemplate)
													<?php
													$Template_info = \App\Models\MatterEmailTemplate::where('matter_id', $list->id)->first();
													?>
													<a class="dropdown-item has-icon" href="{{route('adminconsole.features.matteremailtemplate.edit', [$Template_info->id, $list->id])}}"><i class="far fa-edit"></i> Edit First Email</a>
													@else
													<a class="dropdown-item has-icon" href="{{route('adminconsole.features.matteremailtemplate.create', @$list->id)}}"><i class="far fa-edit"></i> Create First Email</a>
													@endif

													<a class="dropdown-item has-icon" href="{{route('upload_checklists.matter', @$list->id)}}"><i class="fas fa-list"></i> Matter Checklist</a>
													<a class="dropdown-item has-icon" href="{{route('adminconsole.features.matterotheremailtemplate.index', @$list->id)}}"><i class="fas fa-envelope"></i> Email Templates</a>
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

                        <div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('scripts')
<script>
jQuery(document).ready(function($){
    $('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});

	$('.cb-element').change(function () {
        if ($('.cb-element:checked').length == $('.cb-element').length){
            $('#checkbox-all').prop('checked',true);
        } else {
            $('#checkbox-all').prop('checked',false);
        }
    });
    
    // Fix dropdown to open upward - Enhanced version
    $('.dropdown-toggle').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $this = $(this);
        var $dropdown = $this.next('.dropdown-menu');
        var isOpen = $dropdown.hasClass('show');
        
        // Force close ALL dropdowns first (more aggressive)
        $('.dropdown-menu').removeClass('show').css('display', 'none');
        $('.dropdown-toggle').attr('aria-expanded', 'false');
        
        // Small delay to ensure other dropdowns are closed
        setTimeout(function() {
            if (!isOpen) {
                // Open current dropdown
                $dropdown.addClass('show');
                $this.attr('aria-expanded', 'true');
                
                // Force upward positioning
                $dropdown.css({
                    'position': 'absolute',
                    'top': 'auto',
                    'bottom': '100%',
                    'left': '0',
                    'z-index': '9999',
                    'display': 'block',
                    'margin-bottom': '2px'
                });
                
                // Ensure all dropdown items are visible
                $dropdown.find('.dropdown-item').each(function() {
                    $(this).css({
                        'display': 'block',
                        'visibility': 'visible',
                        'opacity': '1',
                        'position': 'relative',
                        'z-index': '1'
                    });
                });
                
                // Debug: Log the number of dropdown items found
                console.log('Dropdown items found:', $dropdown.find('.dropdown-item').length);
            }
        }, 10);
    });
    
    // Close dropdowns when clicking outside - Enhanced version
    $(document).off('click.dropdown').on('click.dropdown', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show').css('display', 'none');
            $('.dropdown-toggle').attr('aria-expanded', 'false');
        }
    });
    
    // Additional safety - close dropdowns on window resize
    $(window).on('resize', function() {
        $('.dropdown-menu').removeClass('show').css('display', 'none');
        $('.dropdown-toggle').attr('aria-expanded', 'false');
    });
});
</script>
@endpush
