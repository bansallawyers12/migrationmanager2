@extends('layouts.admin_client_detail')
@section('title', 'Assigned by Me')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles for assign_by_me page */
    .listing-container .client-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
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
    }
    
    .listing-container .nav-pills .nav-item .nav-link {
        margin-left: 10px;
    }
    
    .listing-container .sort_col a {
        color: #0d6efd !important;
        text-decoration: none;
    }
    
    .listing-container .sort_col a:hover {
        text-decoration: underline;
    }
    
    .listing-container .countAction {
        background: #1f1655;
        padding: 2px 8px;
        border-radius: 50%;
        color: #fff;
        font-size: 0.8em;
        margin-left: 5px;
    }
    
    .listing-container .complete_task {
        cursor: pointer;
    }
    
    .listing-container .btn-sm {
        padding: 5px 10px;
        font-size: 0.85em;
    }
    
    .listing-container .modal-content {
        border-radius: 8px;
    }
    
    .listing-container .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .listing-container .modal-body {
        padding: 20px;
    }
    
    .listing-container .select2-container {
        z-index: 100000;
        width: 100% !important;
    }

    /* Page-specific margin fix for activities page */
    .listing-container {
        margin-left: 80px !important; /* Add margin to prevent overlap with left sidebar */
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .listing-container .table th,
        .listing-container .table td {
            font-size: 0.85em;
            padding: 8px;
        }
        
        .listing-container .btn-sm {
            padding: 4px 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="listing-container">
    <section class="listing-section" style="padding-top: 80px;">
        <div class="listing-section-body">
            @include('../Elements/flash-message')
            
            <div class="client-header">
                <h4>Assigned by Me</h4>
                <div class="client-status">
                    <ul class="nav nav-pills" id="client_tabs" role="tablist">
                        <li class="nav-item">
                            <a class="status-badge nav-link active" href="{{ URL::to('/admin/activities') }}">Incomplete</a>
                        </li>
                        <li class="nav-item">
                            <a class="status-badge nav-link" href="{{ URL::to('/admin/activities_completed') }}">Completed</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('assignee.assigned_by_me') }}" method="get" class="mb-4">
                        <div class="row">
                            <div class="col-md-12 group_type_section">
                                <!-- Add filters if needed -->
                            </div>
                        </div>
                    </form>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="active_quotation" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%" style="text-align: center;">Sno</th>
                                            <th width="5%" style="text-align: center;">Done</th>
                                            <th width="15%">Assignee Name</th>
                                            <th width="15%">Client Reference</th>
                                            <th width="15%" class="sort_col">@sortablelink('followup_date', 'Assign Date')</th>
                                            <th width="10%" class="sort_col">@sortablelink('task_group', 'Type')</th>
                                            <th>Note</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($assignees_notCompleted) > 0)
                                            @foreach ($assignees_notCompleted as $list)
                                                @php
                                                    $admin = \App\Models\Admin::where('id', $list->assigned_to)->first();
                                                    $full_name = $admin ? ($admin->first_name ?? 'N/A') . ' ' . ($admin->last_name ?? 'N/A') : 'N/P';
                                                    $user_name = $list->noteClient ? $list->noteClient->first_name . ' ' . $list->noteClient->last_name : 'N/P';
                                                @endphp
                                                <tr>
                                                    <td style="text-align: center;">{{ ++$i }}</td>
                                                    <td style="text-align: center;">
                                                        <input type="radio" class="complete_task" data-toggle="tooltip" title="Mark Complete!" data-id="{{ $list->id }}" data-unique_group_id="{{ $list->unique_group_id }}">
                                                    </td>
                                                    <td>{{ $full_name }}</td>
                                                    <td>
                                                        {{ $user_name }}
                                                        <br>
                                                        @if ($list->noteClient)
                                                            <a href="{{ URL::to('/admin/clients/detail/' . base64_encode(convert_uuencode($list->client_id))) }}" target="_blank">{{ $list->noteClient->client_id }}</a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $list->followup_date ? date('d/m/Y', strtotime($list->followup_date)) : 'N/P' }}</td>
                                                    <td>{{ $list->task_group ?? 'N/P' }}</td>
                                                    <td>
                                                        @if (isset($list->description) && $list->description != "")
                                                            @if (strlen($list->description) > 190)
                                                                {!! substr($list->description, 0, 190) !!}
                                                                <button type="button" class="btn btn-link" data-toggle="popover" title="" data-content="{{ $list->description }}">Read more</button>
                                                            @else
                                                                {!! $list->description !!}
                                                            @endif
                                                        @else
                                                            N/P
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($list->task_group != 'Personal Task')
                                                            <button type="button" data-noteid="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->followup_date }}" class="btn btn-primary btn-sm update_task" data-toggle="tooltip" title="Update Task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content='
                                                                <div id="popover-content">
                                                                    <h4 class="text-center">Update Task</h4>
                                                                    <div class="form-group row" style="margin-bottom:12px">
                                                                        <label for="rem_cat" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Select Assignee</label>
                                                                        <div class="col-sm-9">
                                                                            <select class="assigneeselect2 form-control selec_reg" id="rem_cat" name="rem_cat">
                                                                                <option value="">Select</option>
                                                                                @foreach (\App\Models\Admin::where('role', '!=', 7)->where('status', 1)->orderBy('first_name', 'ASC')->get() as $admin)
                                                                                    @php
                                                                                        $branchname = \App\Models\Branch::where('id', $admin->office_id)->first();
                                                                                    @endphp
                                                                                    <option value="{{ $admin->id }}" {{ $admin->id == $list->assigned_to ? 'selected' : '' }}>{{ $admin->first_name . ' ' . $admin->last_name . ' (' . ($branchname->office_name ?? 'N/A') . ')' }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row" style="margin-bottom:12px">
                                                                        <label for="assignnote" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Note</label>
                                                                        <div class="col-sm-9">
                                                                            <textarea id="assignnote" class="form-control summernote-simple f13" placeholder="Enter a note..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row" style="margin-bottom:12px">
                                                                        <label for="popoverdatetime" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Date</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="date" class="form-control f13" placeholder="yyyy-mm-dd" id="popoverdatetime" value="{{ date('Y-m-d') }}" name="popoverdate">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row" style="margin-bottom:12px">
                                                                        <label for="task_group" class="col-sm-3 control-label c6 f13" style="margin-top:8px">Group</label>
                                                                        <div class="col-sm-9">
                                                                            <select class="assigneeselect2 form-control selec_reg" id="task_group" name="task_group">
                                                                                <option value="">Select</option>
                                                                                <option value="Call">Call</option>
                                                                                <option value="Checklist">Checklist</option>
                                                                                <option value="Review">Review</option>
                                                                                <option value="Query">Query</option>
                                                                                <option value="Urgent">Urgent</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <input id="assign_note_id" type="hidden" value="">
                                                                    <input id="assign_client_id" type="hidden" value="{{ base64_encode(convert_uuencode($list->client_id)) }}">
                                                                    <div class="text-center">
                                                                        <button class="btn btn-info" id="updateTask">Update Task</button>
                                                                    </div>
                                                                </div>'>
                                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" style="text-align: center; padding: 20px;">
                                                    No activities assigned by me.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                
                                <!-- Pagination -->
                                <div class="card-footer">
                                    {!! $assignees_notCompleted->appends($_GET)->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Assign Modal -->
<div class="modal fade custom_modal" id="openassigneview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content taskview">
            <!-- Modal content will be loaded dynamically -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ URL::to('/') }}/js/popover.js"></script>
<script>
    jQuery(document).ready(function($) {
        // Initialize Select2 for assignee dropdowns
        $('.listing-container .assigneeselect2').select2({
            dropdownParent: $('#openassigneview'),
        });

        // Open assignee modal
        $(document).on('click', '.listing-container .openassignee', function() {
            $('.assignee').show();
        });

        $(document).on('click', '.listing-container .closeassignee', function() {
            $('.assignee').hide();
        });

        // Reassign task
        $(document).on('click', '.listing-container .reassign_task', function() {
            var note_id = $(this).attr('data-noteid');
            $('#assignnote').val(note_id);
            var task_id = $(this).attr('data-taskid');
            $('#assign_note_id').val(task_id);
        });

        // Update task
        $(document).on('click', '.listing-container .update_task', function() {
            var note_id = $(this).attr('data-noteid');
            $('#assignnote').val(note_id);
            var task_id = $(this).attr('data-taskid');
            $('#assign_note_id').val(task_id);
            var taskgroup_id = $(this).attr('data-taskgroupid');
            $('#task_group').val(taskgroup_id);
            var followupdate_id = $(this).attr('data-followupdate');
            $('#popoverdatetime').val(followupdate_id);
        });

        // Mark task as not complete
        $(document).on('click', '.listing-container .not_complete_task', function() {
            var row_id = $(this).attr('data-id');
            var row_unique_group_id = $(this).attr('data-unique_group_id');
            if (row_id != "") {
                $.ajax({
                    type: 'post',
                    url: "{{ URL::to('/') }}/admin/update-task-not-completed",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { id: row_id, unique_group_id: row_unique_group_id },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        });

        // Mark task as complete
        $(document).on('click', '.listing-container .complete_task', function() {
            var row_id = $(this).attr('data-id');
            var row_unique_group_id = $(this).attr('data-unique_group_id');
            if (row_id != "") {
                $.ajax({
                    type: 'post',
                    url: "{{ URL::to('/') }}/admin/update-task-completed",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { id: row_id, unique_group_id: row_unique_group_id },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        });

        // Update task
        $(document).on('click', '#updateTask', function() {
            $(".popuploader").show();
            var flag = true;
            var error = "";
            $(".custom-error").remove();

            if ($('#rem_cat').val() == '') {
                $('.popuploader').hide();
                error = "Assignee field is required.";
                $('#rem_cat').after("<span class='custom-error' role='alert'>" + error + "</span>");
                flag = false;
            }
            if ($('#assignnote').val() == '') {
                $('.popuploader').hide();
                error = "Note field is required.";
                $('#assignnote').after("<span class='custom-error' role='alert'>" + error + "</span>");
                flag = false;
            }
            if ($('#task_group').val() == '') {
                $('.popuploader').hide();
                error = "Group field is required.";
                $('#task_group').after("<span class='custom-error' role='alert'>" + error + "</span>");
                flag = false;
            }
            if (flag) {
                $.ajax({
                    type: 'post',
                    url: "{{ URL::to('/') }}/admin/clients/updatefollowup/store",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        note_id: $('#assign_note_id').val(),
                        note_type: 'follow_up',
                        description: $('#assignnote').val(),
                        client_id: $('#assign_client_id').val(),
                        followup_datetime: $('#popoverdatetime').val(),
                        assignee_name: $('#rem_cat :selected').text(),
                        rem_cat: $('#rem_cat option:selected').val(),
                        task_group: $('#task_group option:selected').val()
                    },
                    success: function(response) {
                        $('.popuploader').hide();
                        var obj = $.parseJSON(response);
                        if (obj.success) {
                            $("[data-role=popover]").each(function() {
                                (($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false;
                            });
                            location.reload();
                        } else {
                            alert(obj.message);
                            location.reload();
                        }
                    }
                });
            } else {
                $("#loader").hide();
            }
        });

        // Open assignee view modal
        $(document).on('click', '.listing-container .openassigneview', function() {
            $('#openassigneview').modal('show');
            var v = $(this).attr('id');
            $.ajax({
                url: site_url + '/admin/get-assigne-detail',
                type: 'GET',
                data: { id: v },
                success: function(responses) {
                    $('.popuploader').hide();
                    $('.taskview').html(responses);
                }
            });
        });
    });
</script>
@endpush
