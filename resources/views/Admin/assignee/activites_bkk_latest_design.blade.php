@extends('layouts.admin_client_detail')
@section('title', 'Activities')

@section('styles')

<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles for activities page */
    .listing-container .client-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px; 
        padding-bottom: 20px; 
        flex-wrap: wrap;
        gap: 15px;
        max-width: 100%;
    }
    
    .listing-container .client-header h1 { 
        font-size: 1.8em; 
        font-weight: 600; 
        color: #212529; 
        margin: 0; 
        word-wrap: break-word;
    }
    
    .listing-container .client-status {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        min-width: 0;
    }
    
    /* Add New Task button specific styling */
    .listing-container .add_my_task {
        margin-right: 15px !important;
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: unset !important;
        min-width: auto !important;
        max-width: none !important;
        flex-shrink: 0 !important;
        width: auto !important;
    }
    
    /* Tab styles */
    .listing-container .tabs { 
        display: flex; 
        gap: 10px; 
        margin-bottom: 20px; 
        flex-wrap: wrap;
        max-width: 100%;
    }
    
    .listing-container .tab-button { 
        padding: 8px 15px; 
        border: none; 
        border-radius: 0px; 
        background-color: #0d6efd; 
        color: white; 
        font-size: 0.9em; 
        font-weight: 500; 
        cursor: pointer; 
        transition: background-color 0.2s ease;
        white-space: nowrap;
    }
    
    .listing-container .tab-button.active, .listing-container .tab-button:hover { 
        background-color: #0d6efd; 
        color: #FFF !important;
    }
    
    .listing-container .tab-button .badge { 
        background-color: #ffffff; 
        color: #0d6efd; 
        border-radius: 10px; 
        padding: 2px 6px; 
        margin-left: 5px; 
        font-size: 0.8em; 
    }
    
    /* Search controls */
    .listing-container .header-controls {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
        max-width: 100%;
    }
    
    .listing-container .search-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        min-width: 0;
    }
    
    .listing-container .search-bar label { 
        font-size: 0.9em; 
        color: #343a40; 
        white-space: nowrap;
    }
    
    .listing-container .search-bar input {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.9em;
        width: 200px;
        max-width: 100%;
        flex-shrink: 0;
    }
    
    /* DataTables customization */
    .listing-container .dataTables_wrapper {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .listing-container .dataTables_wrapper .dataTables_length { 
        margin-bottom: 0; 
    }
    
    .listing-container .dataTables_wrapper .dataTables_filter { 
        display: none; 
    }
    
    .listing-container #DataTables_Table_0_info { 
        margin-top: 20px; 
    }
    
    .listing-container .bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        flex-wrap: wrap;
        gap: 10px;
        max-width: 100%;
    }
    
    .listing-container .dataTables_length {
        flex-shrink: 0;
    }
    
    .listing-container .dataTables_length select {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.9em;
    }
    
    .listing-container .dataTables_length label {
        font-size: 0.9em;
        color: #343a40;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    
    .listing-container .dataTables_info {
        flex-grow: 1;
        text-align: center;
        font-size: 0.9em;
        color: #343a40;
        word-wrap: break-word;
    }
    
    /* Page-specific margin fix for activities page */
    .listing-container {
        margin-left: 80px !important; /* Add margin to prevent overlap with left sidebar */
    }
    

    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .listing-container {
            margin-left: 0 !important; /* Remove sidebar margin on mobile */
        }
        
        .listing-container .client-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .listing-container .client-status {
            width: 100%;
            justify-content: flex-start;
        }
        
        .listing-container .tabs {
            gap: 5px;
        }
        
        .listing-container .tab-button {
            padding: 6px 10px;
            font-size: 0.8em;
        }
        
        .listing-container .search-bar input {
            width: 150px;
        }
    }
</style>
@endsection

@section('content')

<!-- Main Content -->
<div class="listing-container">
    <section class="listing-section" style="padding-top: 80px;">
        <div class="listing-section-body">
            <div class="server-error">
                @include('../Elements/flash-message')
            </div>
            <div class="custom-error-msg"></div>

            <div class="client-header">
                <h4>Action</h4>
                <div class="client-status" style="margin-right: 50px;">
                    <a class="btn btn-primary" style="border-radius: 0px;" id="assigned_by_me"  href="{{URL::to('/admin/assigned_by_me')}}">Assigned by me</a>
                    <a class="btn btn-primary" style="border-radius: 0px;" id="archived-tab"  href="{{URL::to('/admin/activities_completed')}}">Completed</a>
                    <button class="btn btn-primary tab-button add_my_task" data-container="body" data-role="popover" data-placement="bottom-start" data-html="true" data-content="
                        <div id='popover-content11'>
                            <div class='clearfix'></div>
                            <div class='box-header with-border'>
                                <div class='form-group'>
                                    <label for='inputSub3' class='form-label'>Select Assignee</label>
                                    <div class='dropdown-multi-select'>
                                        <button type='button' class='btn btn-default dropdown-toggle' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                            Assign User
                                        </button>
                                        <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                            @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                                <label class='dropdown-item'>
                                                    <input type='checkbox' class='checkbox-item' value='{{ $admin->id }}'>
                                                    {{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <select class='d-none' id='rem_cat' name='rem_cat[]' multiple='multiple'>
                                        @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                            <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                            <option value='{{ $admin->id }}'>{{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id='popover-content'>
                                <div class='box-header with-border'>
                                    <div class='form-group'>
                                        <label for='assignnote' class='form-label'>Note</label>
                                        <textarea id='assignnote' class='form-control' placeholder='Enter a note....'></textarea>
                                    </div>
                                </div>
                                <div class='box-header with-border'>
                                    <div class='form-group'>
                                        <label for='popoverdatetime' class='form-label'>DateTime</label>
                                        <input type='text' class='form-control datepicker' placeholder='dd/mm/yyyy' id='popoverdatetime' value='<?php echo date('d/m/Y');?>' name='popoverdate'>
                                    </div>
                                </div>
                                <div class='box-header with-border'>
                                    <div class='form-group'>
                                        <label for='assign_client_id' class='form-label'>Select Client</label>
                                        <select id='assign_client_id' class='form-control js-data-example-ajaxccsearch__addmytask' placeholder='Search client...'></select>
                                    </div>
                                </div>
                                <input id='task_group' name='task_group' type='hidden' value='Personal Task'>
                                <div class='box-footer'>
                                    <input type='hidden' value='' id='popoverrealdate' name='popoverrealdate' />
                                    <button class='btn btn-info' id='add_my_task'>Add My Task</button>
                                </div>
                            </div>
                        </div>">
                        <i class="fas fa-plus"></i> Add New Task
                    </button>
                </div>
            </div>

            <!-- Tabs (Filters) -->
            <div class="tabs">
                <button class="tab-button active" data-filter="all">ALL <span class="badge" id="all-count">0</span></button>
                <button class="tab-button" data-filter="call">Call <span class="badge" id="call-count">0</span></button>
                <button class="tab-button" data-filter="checklist">Checklist <span class="badge" id="checklist-count">0</span></button>
                <button class="tab-button" data-filter="review">Review <span class="badge" id="review-count">0</span></button>
                <button class="tab-button" data-filter="query">Query <span class="badge" id="query-count">0</span></button>
                <button class="tab-button" data-filter="urgent">Urgent <span class="badge" id="urgent-count">0</span></button>
                <button class="tab-button" data-filter="personal_task">Personal Task <span class="badge" id="personal-task-count">0</span></button>
            </div>

            <!-- Header Controls (Only Search Bar) -->
            <div class="header-controls">
                <div class="search-bar">
                    <label for="searchInput">Search:</label>
                    <input type="text" id="searchInput" placeholder="Search tasks...">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table yajra-datatable">
                    <thead>
                        <tr>
                            <th data-column="DT_RowIndex">Sno</th>
                            <th data-column="done">Done</th>
                            <th data-column="assigner_name">Assigner Name</th>
                            <th data-column="client_reference">Client Reference</th>
                            <th data-column="assign_date">Assign Date</th>
                            <th data-column="task_group">Type</th>
                            <th data-column="note_description">Note</th>
                            <th data-column="action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Assign Modal -->
<div class="modal fade custom_modal" id="openassigneview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content taskview"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{URL::to('/')}}/js/popover.js"></script>
<style>
/* Ensure popovers display correctly */
.popover {
    z-index: 9999 !important;
    max-width: 500px !important;
}

.btn_readmore {
    color: #007bff !important;
    text-decoration: none !important;
    background: none !important;
    border: none !important;
    padding: 0 !important;
    font-size: inherit !important;
    cursor: pointer !important;
}

.btn_readmore:hover {
    color: #0056b3 !important;
    text-decoration: underline !important;
}

    /* Popover styling for better design */
    .popover {
        max-width: 500px !important;
        width: 500px !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e9ecef !important;
        z-index: 9999 !important;
    }
    
    /* Adjust popover position for Add New Task button */
    .add_my_task + .popover {
        left: 0 !important;
        top: 5px !important;
    }
    
    /* Ensure proper popover positioning for Add New Task */
    .add_my_task[data-toggle="popover"] + .popover {
        left: 0 !important;
        top: 5px !important;
        transform: none !important;
    }
    
    /* Additional positioning for Add New Task popover */
    body > .popover[data-popper-placement*="bottom"] {
        left: 0 !important;
        top: 5px !important;
    }
    
    /* Target the specific popover content for Add New Task */
    .popover[data-content*="Select Assignee"] {
        left: 0 !important;
        top: 5px !important;
    }
    
    /* More specific targeting for Add New Task popover */
    .popover[data-content*="popover-content11"] {
        left: 0 !important;
        top: 5px !important;
        margin-top: 0 !important;
    }
    
    /* Additional positioning to bring popup closer to button */
    .add_my_task + .popover,
    .add_my_task ~ .popover {
        left: 0 !important;
        top: 5px !important;
        margin-top: 0 !important;
        position: absolute !important;
    }
    
    /* Ensure popup appears right below the button */
    body > .popover {
        margin-top: 5px !important;
    }
    
    /* Force popup to appear near the button */
    .popover {
        position: fixed !important;
        left: auto !important;
        right: auto !important;
    }
    
    /* Fix popover display issues */
    .popover {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Ensure popover shows on click */
    .popover.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

.popover .popover-header {
    background-color: #0d6efd !important;
    color: white !important;
    border-bottom: 1px solid #0d6efd !important;
    border-radius: 8px 8px 0 0 !important;
    padding: 15px 20px !important;
    font-weight: 600 !important;
    font-size: 16px !important;
}

.popover .popover-body {
    padding: 20px !important;
    max-height: 400px !important;
    overflow-y: auto !important;
    word-wrap: break-word !important;
    white-space: normal !important;
}

/* Form styling within popover */
.popover .form-group {
    margin-bottom: 15px !important;
}

.popover .form-group label {
    font-weight: 500 !important;
    color: #495057 !important;
    margin-bottom: 5px !important;
    display: block !important;
}

.popover .form-control {
    border: 1px solid #ced4da !important;
    border-radius: 6px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
}

.popover .form-control:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    outline: 0 !important;
}

.popover textarea.form-control {
    min-height: 80px !important;
    resize: vertical !important;
}

.popover select.form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 0.75rem center !important;
    background-size: 16px 12px !important;
    padding-right: 2.5rem !important;
}

/* Button styling */
.popover .btn {
    padding: 10px 20px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
}

.popover .btn-info {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
}

.popover .btn-info:hover {
    background-color: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3) !important;
}

/* Error message styling */
.popover .error-message {
    color: #dc3545 !important;
    font-size: 12px !important;
    margin-top: 5px !important;
    font-weight: 500 !important;
}

/* Box header styling */
.popover .box-header {
    border-bottom: 1px solid #e9ecef !important;
    padding-bottom: 15px !important;
    margin-bottom: 15px !important;
}

.popover .box-header:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}

/* Box footer styling */
.popover .box-footer {
    border-top: 1px solid #e9ecef !important;
    padding-top: 15px !important;
    margin-top: 15px !important;
    text-align: center !important;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .popover {
        max-width: 90vw !important;
        width: 90vw !important;
        left: 5vw !important;
    }
    
    .popover .popover-body {
        padding: 15px !important;
    }
    
    .popover .form-group {
        margin-bottom: 12px !important;
    }
}

/* Add My Task specific styling */
.popover .dropdown-multi-select {
    position: relative;
    display: block;
    width: 100%;
}

.popover .dropdown-multi-select .btn {
    width: 100%;
    text-align: left;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    color: #495057;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.popover .dropdown-multi-select .btn:hover,
.popover .dropdown-multi-select .btn:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    outline: 0;
}

.popover .dropdown-multi-select .dropdown-menu {
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ced4da;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 8px;
    margin-top: 2px;
}

.popover .dropdown-multi-select .dropdown-item {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.popover .dropdown-multi-select .dropdown-item:hover {
    background-color: #f8f9fa;
}

.popover .dropdown-multi-select .dropdown-item input[type="checkbox"] {
    margin-right: 8px;
    margin-bottom: 0;
}

.popover .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
}

.popover .form-group {
    margin-bottom: 20px;
}

.popover .form-group:last-child {
    margin-bottom: 0;
}

/* Client search select styling */
.popover .js-data-example-ajaxccsearch__addmytask {
    width: 100%;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    background-color: #fff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

    .popover .js-data-example-ajaxccsearch__addmytask:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: 0;
    }
    
    /* Final overflow prevention */
    /*body > * {
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    /* Ensure no horizontal scroll on the page */
    /*.main-wrapper {
        overflow-x: hidden;
        max-width: 100vw;
    } */
    
    /* Datepicker z-index fix to appear above popovers */
    .daterangepicker {
        z-index: 99999 !important;
        position: fixed !important;
    }
    
    /* Ensure datepicker dropdown appears above all other elements */
    .daterangepicker .drp-buttons {
        z-index: 99999 !important;
    }
    
    /* Additional z-index for datepicker elements */
    .daterangepicker .calendar-table {
        z-index: 99999 !important;
    }
    
    .daterangepicker .ranges {
        z-index: 99999 !important;
    }
</style>
<script type="text/javascript">
$(function () {
    // Initialize Add New Task popover immediately
    $('.add_my_task').popover({
        html: true,
        sanitize: false,
        trigger: 'click',
        placement: 'bottom-start',
        container: 'body',
        template: '<div class="popover" role="tooltip"><div class="popover-header"></div><div class="popover-body"></div></div>'
    });
    
    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('activities.list') }}",
            data: function(d) {
                d.filter = $('.tab-button.active').data('filter');
                d.search = $('#searchInput').val(); // Pass the search term to the server
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'done_task', name: 'done', orderable: false, searchable: false},
            {data: 'assigner_name', name: 'assigner_name', orderable: true, searchable: true},
            {data: 'client_reference', name: 'client_reference', orderable: true, searchable: true},
            {data: 'assign_date', name: 'assign_date', orderable: true, searchable: true},
            {data: 'task_group', name: 'task_group', orderable: true, searchable: true},
            {data: 'note_description', name: 'note_description', orderable: true, searchable: true},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        "fnDrawCallback": function() {
            // Initialize popovers for dynamically added elements
            $('[data-toggle="popover"]').popover({
                html: true,
                sanitize: false,
                trigger: 'click',
                placement: 'bottom',
                container: 'body'
            });

            // Update badge counts
            updateBadgeCounts();
        },
        "bAutoWidth": false,
        "scrollX": true,
        "scrollCollapse": true,
        "dom": 'rt<"bottom"lip><"clear">', // Move length menu (l) to bottom with info (i) and pagination (p)
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100], // Options for entries dropdown
        "order": [[4, 'desc']], // Default sorting by assign_date descending
        "responsive": false,
        "autoWidth": false
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        table.ajax.reload(); // Trigger DataTables reload with the new search term
    });

    // Function to generate Update Task popover content
    function getUpdateTaskContent(assignedTo, noteId, taskId, taskGroup, followupDate, clientId) {
        // Convert followupDate to dd/mm/YYYY if it exists
        var formattedDate = followupDate ? moment(followupDate, 'YYYY-MM-DD').format('DD/MM/YYYY') : '<?php echo date('d/m/Y'); ?>';
        return `
            <div id="popover-content">
                <div class="clearfix"></div>
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px">
                        <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Select Assignee</label>
                        <div class="col-sm-9">
                            <select class="assigneeselect2 form-control selec_reg" id="rem_cat" name="rem_cat">
                                <option value="">Select</option>
                                @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                    <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                    <option value="{{ $admin->id }}" ${assignedTo == {{ $admin->id }} ? 'selected' : ''}>
                                        {{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="assignee-error" class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Note</label>
                        <div class="col-sm-9">
                            <textarea id="assignnote" class="form-control summernote-simple f13" placeholder="Enter a note....">${noteId || ''}</textarea>
                            <div id="note-error" class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px">DateTime</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control f13 datepicker" placeholder="dd/mm/yyyy" id="popoverdatetime" value="${formattedDate}" name="popoverdate">
                        </div>
                    </div>
                </div>
                <div class="form-group row" style="margin-bottom:12px">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Group</label>
                    <div class="col-sm-9">
                        <select class="assigneeselect2 form-control selec_reg" id="task_group" name="task_group">
                            <option value="">Select</option>
                            <option value="Call" ${taskGroup == 'Call' ? 'selected' : ''}>Call</option>
                            <option value="Checklist" ${taskGroup == 'Checklist' ? 'selected' : ''}>Checklist</option>
                            <option value="Review" ${taskGroup == 'Review' ? 'selected' : ''}>Review</option>
                            <option value="Query" ${taskGroup == 'Query' ? 'selected' : ''}>Query</option>
                            <option value="Urgent" ${taskGroup == 'Urgent' ? 'selected' : ''}>Urgent</option>
                        </select>
                        <div id="task-group-error" class="error-message"></div>
                    </div>
                </div>
                <input id="assign_note_id" type="hidden" value="${taskId}">
                <input id="assign_client_id" type="hidden" value="${clientId}">
                <div class="box-footer" style="padding:10px 0">
                    <div class="row">
                        <input type="hidden" value="" id="popoverrealdate" name="popoverrealdate" />
                    </div>
                    <div class="row text-center">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-info" id="updateTask">Update Task</button>
                        </div>
                    </div>
                </div>
            </div>`;
    }

    // Initialize datepicker for Add My Task popover
    $(document).on('shown.bs.popover', '.add_my_task', function() {
        $('#popoverdatetime').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
            parentEl: 'body' // Force the datepicker to be appended to body
        });
        
        // Ensure datepicker appears above popover
        $('.daterangepicker').css({
            'z-index': '99999',
            'position': 'fixed'
        });
        
        // Initialize client search select
        /* ($('#assign_client_id').length) {
            $('#assign_client_id').select2({
                placeholder: 'Search client...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('.popover')
            });
        }*/
    });
    
    // Ensure Add My Task popover works correctly
    $(document).on('click', '.add_my_task', function(e) {
        // Let the popover handle the click naturally
        // Only prevent default if needed
    });
    
    // Position popup correctly after it's shown
    $(document).on('shown.bs.popover', '.add_my_task', function() {
        var $popover = $('.popover').last();
        var $button = $(this);
        var buttonOffset = $button.offset();
        
        // Position the popup right below the button
        $popover.css({
            'position': 'absolute',
            'left': buttonOffset.left + 'px',
            'top': (buttonOffset.top + $button.outerHeight() + 5) + 'px',
            'z-index': 9999
        });
    });
    
    // Re-initialize Add New Task popover if needed
    $(document).ready(function() {
        if (!$('.add_my_task').data('bs.popover')) {
            $('.add_my_task').popover({
                html: true,
                sanitize: false,
                trigger: 'click',
                placement: 'bottom-start',
                container: 'body',
                template: '<div class="popover" role="tooltip"><div class="popover-header"></div><div class="popover-body"></div></div>'
            });
        }
    });

    // Initialize datepicker for Update Task popover
    $(document).on('shown.bs.popover', '.update_task', function() {
        $('#popoverdatetime').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
            parentEl: 'body' // Force the datepicker to be appended to body
        });
        
        // Ensure datepicker appears above popover
        $('.daterangepicker').css({
            'z-index': '99999',
            'position': 'fixed'
        });
        
        //$('.assigneeselect2').select2();
        //$('.summernote-simple').summernote();
    });

    // Update badge counts
    function updateBadgeCounts() {
        $.ajax({
            url: "{{ route('activities.counts') }}",
            method: "GET",
            success: function(data) {
                $('#all-count').text(data.all || 0);
                $('#call-count').text(data.call || 0);
                $('#checklist-count').text(data.checklist || 0);
                $('#review-count').text(data.review || 0);
                $('#query-count').text(data.query || 0);
                $('#urgent-count').text(data.urgent || 0);
                $('#personal-task-count').text(data.personal_task || 0);
            }
        });
    }

    // Filter by tabs
    $('.tab-button').on('click', function() {
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        table.ajax.reload();
    });

    // Handle Update Task button click
    $('.yajra-datatable').on('click', '.update_task', function() {
        var $button = $(this);
        var assignedTo = $button.data('assignedto');
        var noteId = $button.data('noteid');
        var taskId = $button.data('taskid');
        var taskGroup = $button.data('taskgroupid');
        var followupDate = $button.data('followupdate');
        var clientId = $button.data('clientid');

        // Set popover content
        $button.popover('dispose'); // Dispose of any existing popover
        $button.popover({
            html: true,
            sanitize: false,
            title: 'Update Task',
            content: getUpdateTaskContent(assignedTo, noteId, taskId, taskGroup, followupDate, clientId),
            trigger: 'manual',
            placement: 'auto',
            template: '<div class="popover" role="tooltip"><div class="popover-header"></div><div class="popover-body"></div></div>',
            container: 'body'
        }).popover('show');
    });

    // Close popover when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.popover').length && !$(e.target).closest('.update_task').length && !$(e.target).closest('.btn_readmore').length) {
            $('.update_task').popover('hide');
            $('.btn_readmore').popover('hide');
        }
    });

    // Handle Read More button clicks specifically
    $(document).on('click', '.btn_readmore', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $button = $(this);
        var fullContent = $button.data('full-content');
        
        // Hide any other open popovers
        $('.update_task').popover('hide');
        $('.btn_readmore').popover('hide');
        
        // Set popover content and show
        $button.popover('dispose');
        $button.popover({
            html: true,
            sanitize: false,
            content: fullContent,
            trigger: 'manual',
            placement: 'top'
        }).popover('show');
    });

    // Re-initialize popovers after DataTable redraw
    $(document).on('draw.dt', '.yajra-datatable', function() {
        // Destroy existing popovers
        $('.btn_readmore').popover('dispose');
    });

    // Handle Update Task submission
    $(document).on('click', '#updateTask', function() {
        var $popover = $(this).closest('.popover');
        var taskId = $popover.find('#assign_note_id').val();
        var clientId = $popover.find('#assign_client_id').val();
        var assignee = $popover.find('#rem_cat').val();
        var note = $popover.find('#assignnote').val();
        var followupDate = $popover.find('#popoverdatetime').val();
        var taskGroup = $popover.find('#task_group').val();

        // Clear previous error messages
        $popover.find('.error-message').text('');

        // Client-side validation
        var isValid = true;
        if (!assignee) {
            $popover.find('#assignee-error').text('Please select an assignee.');
            isValid = false;
        }
        if (!note) {
            $popover.find('#note-error').text('Please enter a note.');
            isValid = false;
        }
        if (!taskGroup) {
            $popover.find('#task-group-error').text('Please select a task group.');
            isValid = false;
        }

        if (!isValid) {
            return; // Stop submission if validation fails
        }

        // Convert dd/mm/YYYY to YYYY-MM-DD for server compatibility
        var formattedDate = moment(followupDate, 'DD/MM/YYYY').format('YYYY-MM-DD');

        $.ajax({
            type: 'post',
            url: "{{URL::to('/')}}/admin/update-task",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                id: taskId,
                client_id: clientId,
                assigned_to: assignee,
                description: note,
                followup_date: formattedDate,
                task_group: taskGroup
            },
            success: function(response) {
                $('.update_task').popover('hide');
                table.draw(false);
            },
            error: function(xhr) {
                console.error('Error updating task:', xhr.responseText);
                alert('An error occurred while updating the task. Please check the console for details.');
            }
        });
    });

    // Delete record
    $('.yajra-datatable').on('click', '.deleteNote', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = $(this).data('remote');
        var deleteConfirm = confirm("Are you sure?");
        if (deleteConfirm) {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {method: '_DELETE', submit: true}
            }).always(function(data) {
                table.draw(false);
            });
        }
    });

    // Complete task
    $('.yajra-datatable').on('click', '.complete_task', function() {
        var row_id = $(this).attr('data-id');
        var row_unique_group_id = $(this).attr('data-unique_group_id');
        if (row_id) {
            $.ajax({
                type: 'post',
                url: "{{URL::to('/')}}/admin/update-task-completed",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id: row_id, unique_group_id: row_unique_group_id},
                success: function(response) {
                    table.draw(false);
                }
            });
        }
    });

    // Add My Task
    $(document).delegate('#add_my_task', 'click', function() {
        $(".popuploader").show();
        var flag = true;
        var error = "";
        $(".custom-error").remove();

        var selectedRemCat = [];
        $(".checkbox-item:checked").each(function() {
            selectedRemCat.push($(this).val());
        });

        if (selectedRemCat.length === 0) {
            $('.popuploader').hide();
            error = "Assignee field is required.";
            $('#dropdownMenuButton').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

        if ($('#assignnote').val() == '') {
            $('.popuploader').hide();
            error = "Note field is required.";
            $('#assignnote').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

        if (flag) {
            // Convert dd/mm/YYYY to YYYY-MM-DD for server compatibility
            var followupDate = $('#popoverdatetime').val();
            var formattedDate = moment(followupDate, 'DD/MM/YYYY').format('YYYY-MM-DD');

            $.ajax({
                type: 'post',
                url: "{{URL::to('/')}}/admin/clients/personalfollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    note_type: 'follow_up',
                    description: $('#assignnote').val(),
                    client_id: $('#assign_client_id').val(),
                    followup_datetime: formattedDate,
                    rem_cat: selectedRemCat,
                    task_group: $('#task_group').val()
                },
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if (obj.success) {
                        $("[data-role=popover]").each(function() {
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false
                        });
                        table.draw(false);
                        getallactivities();
                        getallnotes();
                    } else {
                        alert(obj.message);
                        table.draw(false);
                    }
                }
            });
        } else {
            $("#loader").hide();
        }
    });
});
</script>
@endsection
