@extends('layouts.admin')
@section('title', 'Enquiries')

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
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Queries</h4>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="active-tab"  href="{{URL::to('/admin/enquiries')}}" >Queries</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="inactive-tab"  href="{{URL::to('/admin/enquiries/archived-enquiries')}}" >Archived</a>
								</li>
							</ul>
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
									<?php
										$query =\App\Models\Enquiry::where('status',0)->where('is_archived',0)->orderby('created_at','Desc');
										$totalcount = $query->count();
										$enqs = $query->get();
										if($totalcount !== 0){
									?>
									<div class="table-responsive">
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>ID</th>
													<th>Name</th>
													<th>Email</th>
													<th>Phone</th>
													<th>Country</th>
													<th>City</th>
													<th>Message</th>
													<th>Source</th>
													<th>Current Visa</th>
													<th>Visa Expiry</th>
													<th></th>
												</tr>
											</thead>
											<tbody class="referredclienttdata">
												<?php
													foreach($enqs as $enqlist){
												?>
												<tr id="id_{{$enqlist->id}}">
													<td style="white-space: initial;">{{$enqlist->id}}</td>
													<td style="white-space: initial;">{{$enqlist->first_name}} {{$enqlist->last_name}}</td>
													<td style="white-space: initial;">{{$enqlist->email}}</td>
													<td style="white-space: initial;">{{$enqlist->phone}}</td>
													<td style="white-space: initial;">{{$enqlist->country}}</td>
													<td style="white-space: initial;">{{$enqlist->city}}</td>
													<td style="white-space: initial;">{{$enqlist->message}}</td>
													<td style="white-space: initial;">{{$enqlist->source}}</td>
											        <td style="white-space: initial;">{{$enqlist->cur_visa}}</td>
                                                    <td style="white-space: initial;">
                                                    <?php
                                                    if($enqlist->visa_expiry != '' && $enqlist->visa_expiry != '0000-00-00'){
                                                        echo date('d/m/Y', strtotime($enqlist->visa_expiry));
                                                    }?>
                                                    </td>
													<td style="white-space: initial;">
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon converttoclient" data-id="{{$enqlist->id}}" href="javascript:;"><i class="fas fa-edit"></i> Convert to Client</a>
																<a class="dropdown-item has-icon archivedrow" data-id="{{$enqlist->id}}" href="javascript:;"><i class="fas fa-trash"></i> Archived</a>
															</div>
														</div>
													</td>
												</tr>
												<?php
													}
												?>
											</tbody>
										</table>
									</div>
									<?php
									} else{ ?>
									<div style="height: 20rem;position: relative;display: flex;align-items: center;justify-content: center;">
										<div class="ag-empty-state__content">
											<h2  class="text-normal-weight marginNone">Queries</h2>
											<p class="">Queries are the prospective client that fill up your webform.</p>
											<p class="">These leads are interested in your services but are yet to start the application.</p>
											<p  class="">When you assign one of your team member to these leads, the leads get transformed into Prospects.</p>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<div id="confirnconvert" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to convert this Enquiry to client?</h4>
				<button type="submit" data-id="" style="margin-top: 40px;" class="button btn btn-danger accept">Accept</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirnarchived" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Are you sure to archived?</h4>
				<button type="submit" data-id="" style="margin-top: 40px;" class="button btn btn-danger acceptarch">Accept</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$(document).delegate('.archivedrow','click', function(){
			var v = $(this).attr('data-id');
			 $('.acceptarch').attr('data-id', '');
			 $('.acceptarch').attr('data-id', v);
			$('#confirnarchived').modal('show');
	});


	$(document).delegate('#confirnarchived .acceptarch', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/enquiries/archived/'+v,
			type:'GET',
			datatype:'json',
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirnarchived').modal('hide');
				if(res.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
					$('#id_'+v).remove();
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
				}
			}
		});
	});
	$(document).delegate('.converttoclient','click', function(){
			var v = $(this).attr('data-id');
			 $('.accept').attr('data-id', '');
			 $('.accept').attr('data-id', v);
			$('#confirnconvert').modal('show');
	});



	$(document).delegate('#confirnconvert .accept', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/enquiries/covertenquiry/'+v,
			type:'GET',
			datatype:'json',
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirnconvert').modal('hide');
				if(res.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
					$('#id_'+v).remove();
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+res.message+'</span>');
				}
			}
		});
	});
});
</script>
@endsection
