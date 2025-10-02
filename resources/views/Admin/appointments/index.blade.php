@extends('layouts.admin_client_detail')
@section('title', 'Appointments')

@section('content')

<link rel="stylesheet" href="{{URL::asset('public/css/bootstrap-datepicker.min.css')}}">

<style>
/* Force prevent horizontal scroll on this page */
html, body {
    overflow-x: hidden !important;
    overflow-y: auto !important;
    max-width: 100% !important;
}

.fc-event-container .fc-h-event{cursor:pointer;}
#openassigneview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}

/* Fix for white text color in appointments table */
.card .card-body table.table {
    --bs-table-color: #000 !important;
    --bs-table-striped-color: #000 !important;
    --bs-table-active-color: #000 !important;
    --bs-table-hover-color: #000 !important;
}

.card .card-body table.table th,
.card .card-body table.table td {
    color: #000 !important;
}

.card .card-body table.table thead th {
    color: #000 !important;
    font-weight: bold;
}

.card .card-body table.table tbody td {
    color: #000 !important;
}

/* Ensure status badges are visible */
.card .card-body table.table tbody td .badge {
    color: #fff !important;
}

/* Pagination styles */
.pagination {
    justify-content: center;
    margin-top: 20px;
}

.pagination .page-link {
    color: #007bff;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

/* Fix horizontal scrollbar issue - Force override */
.card-body {
    overflow-x: hidden !important;
    overflow-y: auto !important;
    max-width: 100% !important;
    max-height: none !important;
}

.table-responsive {
    overflow-x: auto !important;
    overflow-y: visible !important;
    max-width: 100% !important;
    width: 100% !important;
    max-height: none !important;
}

.table-responsive table {
    min-width: 800px !important;
    width: 100% !important;
    table-layout: fixed !important;
}

.table-responsive table th,
.table-responsive table td {
    white-space: nowrap !important;
    vertical-align: middle !important;
    padding: 8px 12px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Optimize column widths with !important */
.table-responsive table th:nth-child(1), /* # */
.table-responsive table td:nth-child(1) {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
}

.table-responsive table th:nth-child(2), /* Client */
.table-responsive table td:nth-child(2) {
    width: 150px !important;
    min-width: 150px !important;
    max-width: 150px !important;
    white-space: normal !important;
}

.table-responsive table th:nth-child(3), /* DateTime */
.table-responsive table td:nth-child(3) {
    width: 120px !important;
    min-width: 120px !important;
    max-width: 120px !important;
}

.table-responsive table th:nth-child(4), /* Nature of enquiry */
.table-responsive table td:nth-child(4) {
    width: 140px !important;
    min-width: 140px !important;
    max-width: 140px !important;
    white-space: normal !important;
}

.table-responsive table th:nth-child(5), /* Description */
.table-responsive table td:nth-child(5) {
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
    white-space: normal !important;
    word-wrap: break-word !important;
}

.table-responsive table th:nth-child(6), /* Added By */
.table-responsive table td:nth-child(6) {
    width: 120px !important;
    min-width: 120px !important;
    max-width: 120px !important;
    white-space: normal !important;
}

.table-responsive table th:nth-child(7), /* Status */
.table-responsive table td:nth-child(7) {
    width: 140px !important;
    min-width: 140px !important;
    max-width: 140px !important;
}

.table-responsive table th:nth-child(8), /* Action */
.table-responsive table td:nth-child(8) {
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
}

/* Make action buttons more compact */
.table-responsive table .btn {
    padding: 4px 8px !important;
    font-size: 12px !important;
    margin: 2px !important;
    white-space: nowrap !important;
}

/* Force container to prevent horizontal scroll */
.main-content {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

.section-body {
    overflow-x: hidden !important;
    max-width: 100% !important;
}

/* Responsive adjustments for smaller screens */
@media (max-width: 768px) {
    .table-responsive table {
        min-width: 600px !important;
    }
    
    .table-responsive table th:nth-child(5), /* Description */
    .table-responsive table td:nth-child(5) {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
    }
    
    .table-responsive table th:nth-child(8), /* Action */
    .table-responsive table td:nth-child(8) {
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
    }
}
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
					<div class="card" style="margin-top: 70px;">
						<div class="card-header">
							<h4 style="color: #212529;">Appointments</h4>
							<div class="card-header-action">
								<!-- <a href="{{--URL::to('admin/quotations/template/create')--}}"  class="btn btn-primary is_checked_clientn">Create Template</a> -->
							</div>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
                                <form action="{{ route('appointments.index') }}" method="get">
                                    <div class="row">
                                        
                                       <div class="col-md-3">
                                            <input type="text" class="form-control mb-3 appointment_date_fields" placeholder="Search with appointment date" name="r" value="<?php if( isset($_REQUEST['r']) && $_REQUEST['r'] != ""){ echo $_REQUEST['r'];}?>" autocomplete="off">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control mb-3" placeholder="Search with Client refernce,description" name="q" value="<?php if( isset($_REQUEST['q']) && $_REQUEST['q'] != ""){ echo $_REQUEST['q'];}?>" autocomplete="off">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" class="form-control mb-3 btn btn-primary" value="Search">
                                        </div>
                                    </div>
                                </form>
								<div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table">
									<!-- @if ($message = Session::get('success'))
										<div class="alert alert-success">
											<p>{{ $message }}</p>
										</div>
									@endif   -->
									<table class="table table-bordered">
										<tr>
											<th>#</th>
                                            <th>Client</th>
											<th>DateTime</th>
                                            <th>Nature of enquiry</th>
											<th>Desciption</th>
                                            <th>Added By</th>
											<!-- <th>Invites</th> -->
											<th>status</th>
											<th>Action</th>
										</tr>
										@foreach ($appointments as $appointment)
                                            <?php //dd($appointment);?>
										<tr>
											<td>{{ ++$i }}</td>
                                            <td>
                                                {{ $appointment->clients->first_name }}  {{$appointment->clients->last_name}} <br>
                                                <a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$appointment->client_id)))}}" target="_blank" >{{ $appointment->clients->client_id }}</a>
                                            </td>
											<td>{{date('d/m/Y', strtotime($appointment->date ))}} {{ $appointment->time }}</td>
                                            @if($appointment->natureOfEnquiry)
											<td>{{$appointment->natureOfEnquiry->title}}</td>
											@else
											<td>N/A</td>
											@endif
                                            <td>{{ $appointment->description }}</td>
                                            @if($appointment->user)
											   <td>{{ $appointment->user->first_name }}  {{$appointment->user->last_name}}</td>
											@else
											   <td>{{ 'N/A' }}</td>
											@endif
											<!-- <td>{{--$appointment->invites ?? 0--}}</td> -->

											@if($appointment->status == 0)
											<td><span title="draft" class="ui label uppercase badge bg-warning">Pending/Not confirmed</span></td>

											@elseif($appointment->status == 2)
											<td><span title="draft" class="ui label uppercase badge bg-success">Completed</span></td>

											@elseif($appointment->status == 4)
											<td><span title="draft" class="ui label uppercase badge bg-primary">N/P</span></td>

											@elseif($appointment->status == 6)
											<td><span title="draft" class="ui label uppercase badge bg-primary">Did Not Come</span></td>
											@elseif($appointment->status == 7)
											<td><span title="draft" class="ui label uppercase badge bg-primary">Cancelled</span></td>
											@elseif($appointment->status == 8)
											<td><span title="draft" class="ui label uppercase badge bg-primary">Missed</span></td>
											@elseif($appointment->status == 9)
											<td><span title="draft" class="ui label uppercase badge bg-warning">Payment Pending</span></td>
											@elseif($appointment->status == 10)
											<td><span title="draft" class="ui label uppercase badge bg-success">Payment Success</span></td>
											@elseif($appointment->status == 11)
											<td><span title="draft" class="ui label uppercase badge bg-warning">Payment Failed</span></td>
											@endif

                                            <td>
												<form action="{{ route('appointments.destroy',$appointment->id) }}" method="POST">
                                                    <a class="btn btn-info" href="{{ route('appointments.show',$appointment->id) }}">Show</a>
                                                    <a class="btn btn-primary" href="{{route('appointments.edit',$appointment->id)}}">Edit</a>

                                                    @csrf
													@method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
													{{--<a class="btn btn-primary openassigneview" id="{{$appointment->id}}" href="#">Assign</a> --}}
												</form>
											</td>
										</tr>
										@endforeach
									</table>
   										 {!! $appointments->appends($_GET)->links() !!}
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
  <script src="{{URL::asset('public/js/bootstrap-datepicker.js')}}"></script>
<script>
	jQuery(document).ready(function($){

     $('.appointment_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true });

     $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });
	$(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
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
//////appointment pending todo
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
			url: site_url+'/admin/update_appointment_status',
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
			url: site_url+'/admin/update_appointment_priority',
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
			data:{id: appliid,visit_purpose:visitpurpose},
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
