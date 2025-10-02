@extends('layouts.admin_client_detail')
@section('title', 'Appointments')

@section('content')

<link rel="stylesheet" href="{{URL::asset('public/css/bootstrap-datepicker.min.css')}}">

<style>
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
							<h4>Appointments</h4>
							<div class="card-header-action">
								<!-- <a href="{{--URL::to('admin/quotations/template/create')--}}"  class="btn btn-primary is_checked_clientn">Create Template</a> -->
							</div>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
                                <form action="{{ route('appointments.index') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control mb-3 appointment_date_fields" placeholder="Search with appointment date" name="appointment_date_field" value="{{ $currentParams['appointment_date_field'] ?? '' }}" autocomplete="off">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control mb-3" placeholder="Search with Client refernce,description" name="other_field" value="{{ $currentParams['other_field'] ?? '' }}" autocomplete="off">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" class="form-control mb-3 btn btn-primary" value="Search">
                                        </div>
                                        <div class="col-md-2">
                                            <a href="{{ route('appointments.index') }}" class="form-control mb-3 btn btn-secondary">Clear</a>
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
											<th>status</th>
											<th width="280px">Action</th>
										</tr>
                                        @php
                                            $pagination = $appointments['data']['pagination'] ?? [];
                                            $currentPage = $pagination['current_page'] ?? 1;
                                            $perPage = $pagination['per_page'] ?? 20;
                                            $startNumber = (($currentPage - 1) * $perPage) + 1;
                                            $i = $startNumber;
                                        @endphp
										@foreach ($appointments['data']['data'] ?? [] as $appointment)
										<tr>
											<td>{{ $i++ }}</td>
                                            <td>
                                                {{ $appointment['client']['name'] ?? 'NA' }}  <br>

                                                <a href="{{ $appointment['client_unique_id'] ? URL::to('/admin/clients/detail/' . base64_encode(convert_uuencode($appointment['client_id']))) : '#' }}" target="_blank">
                                                    {{ $appointment['client_unique_id'] ?? 'NA' }}
                                                </a>
                                            </td>
											<td>{{date('d/m/Y', strtotime($appointment['date'] ))}} {{ $appointment['time'] }}</td>
                                            @if(isset($appointment['nature_of_enquiry']))
                                                <td>{{ $appointment['nature_of_enquiry']['title'] ?? 'N/A' }}</td>
                                            @else
                                                <td>N/A</td>
                                            @endif

                                            <td>{{ $appointment['description'] }}</td>

                                            @if(isset($appointment['user']))
                                                <td>{{ $appointment['user']['name'] ?? 'N/A' }}</td>
                                            @else
                                                <td>N/A</td>
                                            @endif

											@if($appointment['status'] == 0)
											<td><span title="draft" class="ui label uppercase badge bg-warning">Pending/Not confirmed</span></td>

											@elseif($appointment['status'] == 2)
											<td><span title="draft" class="ui label uppercase badge bg-success">Completed</span></td>

											@elseif($appointment['status']== 4)
											<td><span title="draft" class="ui label uppercase badge bg-primary">N/P</span></td>

											@elseif($appointment['status'] == 6)
											<td><span title="draft" class="ui label uppercase badge bg-primary">Did Not Come</span></td>

                                            @elseif($appointment['status'] == 7)
											<td><span title="draft" class="ui label uppercase badge bg-primary">Cancelled</span></td>

                                            @elseif($appointment['status'] == 8)
											<td><span title="draft" class="ui label uppercase badge bg-primary">Missed</span></td>

                                            @elseif($appointment['status'] == 9)
											<td><span title="draft" class="ui label uppercase badge bg-warning">Payment Pending</span></td>

                                            @elseif($appointment['status'] == 10)
											<td><span title="draft" class="ui label uppercase badge bg-success">Payment Success</span></td>

                                            @elseif($appointment['status'] == 11)
											<td><span title="draft" class="ui label uppercase badge bg-warning">Payment Failed</span></td>
											@endif

                                            <td>
												<form action="{{ route('appointments.destroy',$appointment['id']) }}" method="POST">
                                                    <a class="btn btn-info" href="{{ route('appointments.show',$appointment['id']) }}">Show</a>
                                                    <a class="btn btn-primary" href="{{route('appointments.edit',$appointment['id'])}}">Edit</a>

                                                    @csrf
													@method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
												</form>
											</td>
										</tr>
										@endforeach
									</table>

                                    {{-- Pagination --}}
                                    @if(isset($appointments['data']['pagination']))
                                        @php
                                            $pagination = $appointments['data']['pagination'];
                                            $currentPage = $pagination['current_page'] ?? 1;
                                            $lastPage = $pagination['last_page'] ?? 1;
                                            $perPage = $pagination['per_page'] ?? 20;
                                            $total = $pagination['total'] ?? 0;
                                        @endphp

                                        @if($lastPage > 1)
                                            <nav aria-label="Appointments pagination">
                                                <ul class="pagination">
                                                    {{-- Previous Page --}}
                                                    @if($currentPage > 1)
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ route('appointments.index', array_merge($currentParams, ['page' => $currentPage - 1])) }}">Previous</a>
                                                        </li>
                                                    @endif

                                                    {{-- Page Numbers --}}
                                                    @for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                                                        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                                            <a class="page-link" href="{{ route('appointments.index', array_merge($currentParams, ['page' => $i])) }}">{{ $i }}</a>
                                                        </li>
                                                    @endfor

                                                    {{-- Next Page --}}
                                                    @if($currentPage < $lastPage)
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ route('appointments.index', array_merge($currentParams, ['page' => $currentPage + 1])) }}">Next</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </nav>

                                            {{-- Pagination Info --}}
                                            <div class="text-center mt-2">
                                                <small class="text-muted">
                                                    Showing {{ (($currentPage - 1) * $perPage) + 1 }} to {{ min($currentPage * $perPage, $total) }} of {{ $total }} appointments
                                                </small>
                                            </div>
                                        @endif
                                    @endif
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

