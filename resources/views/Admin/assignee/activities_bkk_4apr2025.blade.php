@extends('layouts.admin')
@section('title', 'Action')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
#openassigneview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}
.sort_col a { color: #212529 !important; font-weight: 700 !important;}
.group_type_section a.active {color:black;}
.select2-container{z-index:100000;width:315px !important;}
.countAction {background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;}
.table:not(.table-sm) thead th {background-color:#fff !important;height: 60px;vertical-align: middle;padding: 0 10px !important;color: #212529;font-size: 15px;}
.card .card-body table.table thead tr th {padding: 0px 10px!important;}
.uniqueClassName {text-align: center;}
.filter-checkbox{/*margin-left: 30px;*/}
.filter-checkbox:first-child{margin-left:0}
/*.table-responsive {width:98% !important; overflow-x: hidden !important;}*/
.card .card-body table.table tbody tr td {padding: 8px 5px!important;}
.table-responsive { overflow: hidden;}
.dataTables_wrapper .dataTables_filter{float: left !important;margin-left: 310px !important;}
.popover .popover-body {width: 500px !important;}
.filter-wrapper div.active {color:blue !important;}


/************ Action PopUp Start**********************/
.dropdown-multi-select {
    position: relative;
    display: inline-block;
    border: 1px solid #ccc; /* Border around the dropdown */
    border-radius: 4px; /* Rounded corners */
    padding: 8px;
	width: 336px;
}
.dropdown-toggle::after {
    display: inline-block;
    margin-left: 17.255em !important;
    vertical-align: .255em;
    content: "";
    border-top: .3em solid;
    border-right: .3em solid transparent;
    border-bottom: 0;
    border-left: .3em solid transparent;
}

.dropdown-multi-select .dropdown-toggle {
    background-color: #f8f9fa;
    border: none;
    width: 100%;
    text-align: left;
}

.dropdown-multi-select .dropdown-menu {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 100%; /* Ensure the dropdown menu is the same width as the dropdown toggle */
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
    padding: 8px;
    border-radius: 4px; /* Same border-radius as the dropdown */
    border: 1px solid #ccc; /* Same border color as the dropdown */
    font-size: 14px;
}

.dropdown-multi-select .dropdown-menu label {
    display: flex;
    align-items: center;
    margin: 5px 0;
    font-weight: normal;
    cursor: pointer;
}

.dropdown-multi-select .dropdown-menu input[type="checkbox"] {
    margin-right: 8px;
}

.dropdown-multi-select.show .dropdown-menu {
    display: block;
}
/************ Action PopUp End**********************/
</style>
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
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
                            <h4>Action</h4>
                            <div class="card-header-action">
                            </div>
                            <ul class="nav nav-pills" id="client_tabs" role="tablist">
                                <li class="nav-item is_checked_clientn12">
									<a class="nav-link" id="assigned_by_me"  href="{{URL::to('/admin/assigned_by_me')}}">Assigned by me</a>
								</li>

                                <li class="nav-item is_checked_clientn11">
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/admin/activities_completed')}}">Completed</a>
								</li>
                            </ul>
                        </div>

						<div class="card-body">
							<div class="tab-content" id="quotationContent">

                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if(\Auth::user()->role == 1){
                                            $assigneesCount_All_type = \App\Models\Note::where('type','client')
                                            ->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_call_type = \App\Models\Note::where('task_group','like','Call')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Checklist_type = \App\Models\Note::where('task_group','like','Checklist')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Review_type = \App\Models\Note::where('task_group','like','Review')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Query_type = \App\Models\Note::where('task_group','like','Query')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Urgent_type = \App\Models\Note::where('task_group','like','Urgent')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Personal_Task_type = \App\Models\Note::where('task_group','like','Personal Task')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            //$assigneesCount_deadline_type = \App\Models\Note::where('type','client')->whereNotNull('note_deadline')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();
                                        } else {
                                            $assigneesCount_All_type = \App\Models\Note::where('assigned_to',Auth::user()->id)
                                            ->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_call_type = \App\Models\Note::where('task_group','like','Call')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Checklist_type = \App\Models\Note::where('task_group','like','Checklist')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Review_type = \App\Models\Note::where('task_group','like','Review')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Query_type = \App\Models\Note::where('task_group','like','Query')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Urgent_type = \App\Models\Note::where('task_group','like','Urgent')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Personal_Task_type = \App\Models\Note::where('task_group','like','Personal Task')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            //$assigneesCount_deadline_type = \App\Models\Note::where('assigned_to',Auth::user()->id)->whereNotNull('note_deadline')->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                        } ?>
                                        <div class="filter-wrapper">
                                            <div class="btn btn-light filter-checkbox active" data-val="All"> All <span class="countAction">{{ $assigneesCount_All_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Call"><i class="fa fa-phone" aria-hidden="true"></i> Call <span class="countAction">{{ $assigneesCount_call_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Checklist"> <i class="fa fa-bars" aria-hidden="true"></i> Checklist <span class="countAction">{{ $assigneesCount_Checklist_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Review"> <i class="fa fa-check" aria-hidden="true"></i> Review <span class="countAction">{{ $assigneesCount_Review_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Query"><i class="fa fa-question" aria-hidden="true"></i> Query <span class="countAction">{{ $assigneesCount_Query_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Urgent"> <i class="fa fa-flag" aria-hidden="true"></i> Urgent <span class="countAction">{{ $assigneesCount_Urgent_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Personal Task"> <i class="fa fa-tasks" aria-hidden="true"></i> Personal Task <span class="countAction">{{ $assigneesCount_Personal_Task_type }}</span></div>
                                            <!--<div class="btn btn-light filter-checkbox" data-val="Deadline"> <i class="fa fa-flag" aria-hidden="true"></i> Deadline <span class="countAction">{{--$assigneesCount_deadline_type--}}</span></div>-->

                                            <button type="button" class="btn btn-primary btn-block add_my_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content11&quot;>
                                                <h4 class=&quot;text-center&quot;>Add My Task</h4>
                                                <div class=&quot;clearfix&quot;></div>
                                                <div class=&quot;box-header with-border&quot;>
                                                    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                        <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                                        <div class=&quot;col-sm-9&quot;>
                                                        <div class=&quot;form-group&quot;>
                                                            <div class=&quot;dropdown-multi-select&quot;>
                                                            <button type=&quot;button&quot; class=&quot;btn btn-default dropdown-toggle&quot; id=&quot;dropdownMenuButton&quot; data-toggle=&quot;dropdown&quot; aria-haspopup=&quot;true&quot; aria-expanded=&quot;false&quot;&gt;
                                                                Assign User
                                                            </button>
                                                            <div class=&quot;dropdown-menu&quot; aria-labelledby=&quot;dropdownMenuButton&quot;&gt;
                                                                @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                    <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                                                    <label>
                                                                        <input type=&quot;checkbox&quot; class=&quot;checkbox-item&quot; value=&quot;{{ $admin->id }}&quot;&gt;
                                                                        {{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})
                                                                    </label><br&gt;
                                                                @endforeach
                                                            </div>
											            </div>
											        </div>

                                                    <!-- Hidden select input to store selected values -->
                                                    <select class=&quot;d-none&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat[]&quot; multiple=&quot;multiple&quot;&gt;
                                                        @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                        <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                                        <option value=&quot;{{ $admin->id }}&quot;&gt;{{ $admin->first_name }} {{ $admin->last_name }} ({{ @$branchname->office_name }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class=&quot;clearfix&quot;></div>
                                            </div>
                                        </div><div id=&quot;popover-content&quot;>
                                            <div class=&quot;box-header with-border&quot;>
                                                <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                    <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Note</label>
                                                    <div class=&quot;col-sm-9&quot;>
                                                        <textarea id=&quot;assignnote&quot; class=&quot;form-control summernote-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
                                                    </div>
                                                    <div class=&quot;clearfix&quot;></div>
                                                </div>
                                            </div>
                                            <div class=&quot;box-header with-border&quot;>
                                                <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                    <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>DateTime</label>
                                                    <div class=&quot;col-sm-9&quot;>
                                                        <input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot;name=&quot;popoverdate&quot;>
                                                    </div>
                                                    <div class=&quot;clearfix&quot;></div>
                                                </div>
                                            </div>

                                            <input id=&quot;task_group&quot;  name=&quot;task_group&quot;  type=&quot;hidden&quot; value=&quot;Personal Task&quot;>
                                            <form class=&quot;form-inline mr-auto&quot;>
                                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Client</label>
                                                <div class=&quot;search-element&quot; style=&quot;margin-left: 5px;width:70%;&quot;>
                                                    <select id=&quot;assign_client_id&quot;  class=&quot;form-control js-data-example-ajaxccsearch__addmytask&quot; type=&quot;search&quot; placeholder=&quot;Search&quot; aria-label=&quot;Search&quot; data-width=&quot;200&quot; style=&quot;width:200px&quot;></select>
                                                    <button class=&quot;btn&quot; type=&quot;submit&quot;><i class=&quot;fas fa-search&quot;></i></button>
                                                </div>
                                            </form>

                                            <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
                                                <div class=&quot;row&quot;>
                                                    <input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
                                                </div>
                                                <div class=&quot;row text-center&quot;>
                                                    <div class=&quot;col-md-12 text-center&quot;>
                                                    <button  class=&quot;btn btn-info&quot; id=&quot;add_my_task&quot;>Add My Task</button>
                                                    </div>
                                                </div>
                                            </div>" data-original-title="" title="" style="width: 105px;display: inline;margin-left: 10px">Add My Task</button>

                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table">
									    <!-- @if ($message = Session::get('success'))
										<div class="alert alert-success">
											<p>{{ $message }}</p>
										</div>
									    @endif   -->

                                        <table class="table table-bordered yajra-datatable">
                                            <thead>
                                                <tr>
                                                    <th>Sno</th>
                                                    <th>Done</th>
                                                    <th>Assigner Name</th>
                                                    <th>Client Reference</th>
                                                    <th>Assign Date</th>
                                                    <th>Type</th>
                                                    <!--<th>Deadline</th>-->
                                                    <th>Note</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>


								    </div>
								    <div class="card-footer">

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

<div class="modal fade custom_modal" id="openassigneview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content taskview">

		</div>
	</div>
</div>
@endsection
@section('scripts')


<script src="{{URL::to('/')}}/js/popover.js"></script>

<script type="text/javascript">
$(function () {

    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('activities.list') }}",
        columns: [
            {sWidth: '40px',className: "uniqueClassName", data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {sWidth: '50px',className: "uniqueClassName", data: 'done_task', name: 'done_task',orderable: false,searchable: false},
            {sWidth: '120px',data: 'assigner_name', name: 'assigner_name'},
            {sWidth: '130px',data: 'client_reference', name: 'client_reference'},
            {sWidth: '100px',data: 'assign_date', name: 'assign_date'},
            {sWidth: '80px',data: 'task_group', name: 'task_group'},
            //{sWidth: '80px',data: 'note_deadline', name: 'note_deadline'},
            {data: 'note_description', name: 'note_description'},
            {sWidth: '120px',data: 'action',name: 'action',orderable: false,searchable: false},
        ],
        "fnDrawCallback": function() {
            $('[data-toggle="popover"]').popover({
                html: true,
                sanitize: false,
                outsideClick:true
            });
        },
        "bAutoWidth": false
    });


    // Clear any existing custom filters
    $.fn.dataTable.ext.search.pop();

    // Add a custom filter to DataTables
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            console.log('Row Data:', data);
            var filterValue = $('.filter-checkbox.active').attr('data-val');
            var date = data[6]; // Get the date from the 6th column

            console.log('Row ' + dataIndex + ' - Column 6 Value: ' + date);

            var validDate = moment(date, 'YYYY-MM-DD', true).isValid();

            if (filterValue === 'Deadline') {
                console.log('Filter Value:', filterValue);
                console.log('Date:', date);
                return validDate; // Only include rows with valid dates in column 6
            } else {
                return true; // Allow all rows for other filters
            }
        }
    );

    // Apply the custom filter and redraw the table
    $('.filter-checkbox').on('click', function(e) {
        var filterValue = $(this).attr('data-val');

        // Remove active class from all checkboxes and add to the clicked one
        $(".filter-checkbox").removeClass("active");
        $(this).addClass("active");

        console.log('Selected Filter:', filterValue);

        if (filterValue === 'Deadline') {
            // Redraw the table, which applies the custom filter
            table.draw();
        } else {
            var searchTerms = [];
            if (filterValue === 'All') {
                searchTerms = ["Call", "Checklist", "Review", "Query", "Urgent", "Personal Task"];
            } else {
                searchTerms.push("^" + filterValue + "$");
            }

            // Apply search on column 5
            table.column(5).search(searchTerms.join('|'), true, false, true).draw();
        }
    });

    // Delete record
    $('.yajra-datatable').on('click','.deleteNote',function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = $(this).data('remote');

        var deleteConfirm = confirm("Are you sure?");
        if (deleteConfirm == true) {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {method: '_DELETE', submit: true}
            }).always(function (data) {
                $('.yajra-datatable').DataTable().draw(false);
            });
        }
    });
});

</script>

<script>
jQuery(document).ready(function($){

    //$('[data-toggle="tooltip"]').tooltip();

    $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });

	$(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });

    //reassign task
    $(document).delegate('.reassign_task', 'click', function(){
        var assignedto = $(this).attr('data-assignedto'); //alert(assignedto);
        $.ajax({
			type:'post',
            url:"{{URL::to('/')}}/admin/get_assignee_list",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {assignedto:assignedto},
            success: function(response){
                var obj = $.parseJSON(response);
                //console.log(obj.message);
                $('#rem_cat').html(obj.message);
            }
		});

        var note_id = $(this).attr('data-noteid');
        $('#assignnote').val(note_id);

        var task_id = $(this).attr('data-taskid');
        $('#assign_note_id').val(task_id);

        //get current date
        /*var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        today =  yyyy + '-' + mm + '-' + dd;
        $('#popoverdatetime').attr('value', today);*/


        var taskgroup_id = $(this).attr('data-taskgroupid');
        $('#task_group').val(taskgroup_id);

        //set current date
        var followupdate_id = $(this).attr('data-followupdate');
        var folowDateArr = followupdate_id.split(" ");
        var finalDate = folowDateArr[0];
        $('#popoverdatetime').val(finalDate);
    });


    //update task
    $(document).delegate('.update_task', 'click', function(){
        var assignedto = $(this).attr('data-assignedto'); //alert(assignedto);
        $.ajax({
			type:'post',
            url:"{{URL::to('/')}}/admin/get_assignee_list",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {assignedto:assignedto},
            success: function(response){
                var obj = $.parseJSON(response);
                //console.log(obj.message);
                $('#rem_cat').html(obj.message);
            }
		});

        var note_id = $(this).attr('data-noteid');
        $('#assignnote').val(note_id);

        var task_id = $(this).attr('data-taskid');
        $('#assign_note_id').val(task_id);

        var taskgroup_id = $(this).attr('data-taskgroupid');
        $('#task_group').val(taskgroup_id);

        //set current date
        var followupdate_id = $(this).attr('data-followupdate');
        var folowDateArr = followupdate_id.split(" ");
        var finalDate = folowDateArr[0];
        $('#popoverdatetime').val(finalDate);
    });


    //Function is used for not complete the task
	$(document).delegate('.not_complete_task', 'click', function(){
		var row_id = $(this).attr('data-id');
        var row_unique_group_id = $(this).attr('data-unique_group_id');
        if(row_id !=""){
            $.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/update-task-not-completed",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id:row_id,unique_group_id:row_unique_group_id},
                success: function(response){
                    //console.log(response);
                    //var obj = $.parseJSON(response);
                    //location.reload();
                    $('.yajra-datatable').DataTable().draw(false);
                }
			});
        }
	});

    //Function is used for complete the task
	$(document).delegate('.complete_task', 'click', function(){
		var row_id = $(this).attr('data-id');
        var row_unique_group_id = $(this).attr('data-unique_group_id');
        if(row_id !=""){ //&& confirm('Are you sure want to complete the task?')
            $.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/update-task-completed",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id:row_id,unique_group_id:row_unique_group_id },
                success: function(response){
                    //console.log(response);
                    //var obj = $.parseJSON(response);
                    //location.reload();
                    $('.yajra-datatable').DataTable().draw(false);
                }
			});
        }
	});

    //re-assign task or update task
    $(document).delegate('#assignUser','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error = "";
		$(".custom-error").remove();
		if($('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if($('#task_group').val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/clients/reassignfollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {note_id:$('#assign_note_id').val(),note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group option:selected').val()},
                success: function(response){
                    console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                        getallactivities();
                        getallnotes();
                    } else{
                        alert(obj.message);
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                    }
                }
			});
		}else{
			$("#loader").hide();
		}
	});

    //update task
    $(document).delegate('#updateTask','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();

		if($('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if($('#task_group').val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/clients/updatefollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {note_id:$('#assign_note_id').val(),note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group option:selected').val()},
                success: function(response){
                    console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                        getallactivities();
                        getallnotes();
                    } else{
                        alert(obj.message);
                        $('.yajra-datatable').DataTable().draw(false);
                        //location.reload();
                    }
                }
			});
		}else{
			$("#loader").hide();
		}
	});

    //Add Personal Task
    /*$(document).delegate('#add_my_task','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();
        if($('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if(flag){
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/clients/personalfollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group').val()},
                success: function(response){
                    //console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                        getallactivities();
                        getallnotes();
                    } else{
                        alert(obj.message);
                        $('.yajra-datatable').DataTable().draw(false);
                        //location.reload();
                    }
                }
			});
		}else{
			$("#loader").hide();
		}
	});*/


    $(document).delegate('#add_my_task', 'click', function(){
        $(".popuploader").show();
        var flag = true;
        var error = "";
        $(".custom-error").remove();

        // Check if any checkbox is selected in the multi-select dropdown
        var selectedRemCat = [];
        $(".checkbox-item:checked").each(function() {
            selectedRemCat.push($(this).val());
        });

        if(selectedRemCat.length === 0) {
            $('.popuploader').hide();
            error = "Assignee field is required.";
            $('#dropdownMenuButton').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

        if($('#assignnote').val() == ''){
            $('.popuploader').hide();
            error = "Note field is required.";
            $('#assignnote').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

    if(flag){
        $.ajax({
            type: 'post',
            url: "{{URL::to('/')}}/admin/clients/personalfollowup/store",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                note_type: 'follow_up',
                description: $('#assignnote').val(),
                client_id: $('#assign_client_id').val(),
                followup_datetime: $('#popoverdatetime').val(),
                rem_cat: selectedRemCat, // Send array of selected checkbox values
                task_group: $('#task_group').val()
            },
            success: function(response){
                $('.popuploader').hide();
                var obj = $.parseJSON(response);
                if(obj.success){
                    $("[data-role=popover]").each(function(){
                        (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                    });
                    $('.yajra-datatable').DataTable().draw(false);
                    getallactivities();
                    getallnotes();
                } else{
                    alert(obj.message);
                    $('.yajra-datatable').DataTable().draw(false);
                }
            }
        });
    } else {
        $("#loader").hide();
    }
});


	$(document).delegate('.saveassignee', 'click', function(){
        var appliid = $(this).attr('data-id');

		var assinee= $('#changeassignee').val();
		$('.popuploader').show();
		// console.log($('#changeassignee').val());
		$.ajax({
			url: site_url+'/admin/change_assignee',
			type:'GET',
			data:{id: appliid,assinee: assinee},
			success: function(response){
				// console.log(response);
				 var obj = $.parseJSON(response);
				if(obj.status){
				    alert(obj.message);
				location.reload();

				}else{
					alert(obj.message);
				}
			}
		});
    });

	$(document).delegate('.savecomment', 'click', function(){
		var visitcomment = $('.taskcomment').val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_apppointment_comment',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_comment:visitcomment},
			success: function(responses){
				// $('.popuploader').hide();
				$('.taskcomment').val('');
				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});
			}
		});
	});

	$(document).delegate('.openassigneview', 'click', function(){
	    // $('.popuploader').hide();
	    $('#openassigneview').modal('show');
	    var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-assigne-detail',
			type:'GET',
			data:{id:v},
			success: function(responses){
				$('.popuploader').hide();
				$('.taskview').html(responses);
			}
		});
	});

	$(document).delegate('.changestatus', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		var statusame = $(this).attr('data-status-name');
		$('.popuploader').show();

		$.ajax({
			url: site_url+'/admin/update_list_status',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,statusname:statusame,status:status},
			success: function(responses){
				$('.popuploader').hide();
				var obj = JSON.parse(responses);
				if(obj.status){
				    console.log(obj.status);
				    $('.updatestatusview'+appliid).html(obj.viewstatus);
				}
				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});
			}
		});
	});


	$(document).delegate('.changepriority', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		$('.popuploader').show();

		$.ajax({
			url: site_url+'/admin/update_list_priority',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,status:status},
			success: function(responses){
				$('.popuploader').hide();

				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						console.log(responses);
						$('.taskview').html(responses);

					}
				});
			}
		});
	});

	$(document).delegate('.desc_click', 'click', function(){
		$(this).hide();
		$('.taskdesc').show();
		$('.taskdesc').focus();
	});
	$(document).delegate('.taskdesc', 'blur', function(){
		$(this).hide();
		$('.desc_click').show();
	});

	$(document).delegate('.tasknewdesc', 'blur', function(){
		var visitpurpose = $(this).val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_apppointment_description',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});

			}
		});
	});

	$(document).delegate('.taskdesc', 'blur', function(){
		var visitpurpose = $(this).val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_apppointment_description',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				 $.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});
            }
		});
	});
});
</script>
@endsection
