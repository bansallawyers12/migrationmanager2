@extends('layouts.admin')
@section('title', 'Paid Group Invoice')

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
							<h4>Paid Group Invoice</h4>
							<div class="card-header-action">
								<a href="javascript:;" class="btn btn-primary creategroupinvoice">Create Group Invoice</a>
							</div> 
						</div> 
						<div class="card-body">							
							<ul class="nav nav-pills" id="invoice_tabs" role="tablist">
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="unpaid-tab"  href="{{URL::to('/admin/group-invoice/unpaid')}}" >Unpaid</a>
								</li> 
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="paid-tab"  href="{{URL::to('/admin/group-invoice/paid')}}" >Paid</a>
								</li>							
							</ul> 
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="paid" role="tabpanel" aria-labelledby="paid-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap">
											<thead>
												<tr> 
													
													<th>GID</th>
													<th>Invoice To</th>
													<th>Status</th>
													<th>Due Date</th>
													<th>Created On</th> 
													<th>Created By</th>
													<th>Office</th>
													<th>Product</th>
													<th>Invoice Amount</th>			
													<th>Amount Due</th>
													<th>Action</th>
												</tr> 
											</thead> 
											
											<tbody class="tdata">	
												<tr>
													<td>1</td>
													<td>Akelsh</td>
													<td>Pedning</td>
													<td>19/07/2022</td>
													<td>19/07/2022</td>
													<td>Arun</td>
													<td>Head Office</td>
													<td>Light Motor Vehicle</td>
													<td>30000</td>
													<td>19/07/2022</td>
												</tr>
											</tbody>											
										</table>
									</div>
								</div>	
							</div> 
						</div>
						<div class="card-footer">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade custom_modal" id="creategroupinvoice" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Create Group Invoice</h5>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
	
				<p>Invoices will be created for Gross Unpaid Invoices Only.</p>
				<form method="post" action="{{URL::to('/admin/addgroupinvoice')}}" name="addgroupinvoice" id="addgroupinvoice" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
						
							<div class="form-group">
								<label style="display:block;" for="partner_type">Select Partner Type:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner_inv" value="Partner" name="partner_type" checked>
									<label class="form-check-label" for="partner_inv">Partner</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="superagent_inv" value="superagent" name="partner_type">
									<label class="form-check-label" for="superagent_inv">Super Agent</label>
								</div>
								<span class="custom-error partner_type_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
					
						<div class="col-12 col-md-12 col-lg-12 is_partnerinv">
							<div class="form-group">
								<label for="timezone">Select Partner for which you want to create invoice <span class="span_req">*</span></label>
								<select class="form-control timezoneselect2" name="partner" data-valid="required">
									<option value="">Select</option>
									<?php
									foreach(\App\Models\Partner::all() as $tlist){
										?>
										<option value="<?php echo $tlist->id; ?>" ><?php echo $tlist->partner_name; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_superagentinv">
							<div class="form-group">
								<label for="timezone">Select Super Agent for which you want to create invoice <span class="span_req">*</span></label>
								<select class="form-control" name="super_agent" data-valid="required">
									<option value="">Select Super Agent</option>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_superagentinv">
							<div class="form-group">
								<label for="timezone">Select Currency: <span class="span_req">*</span></label>
								<select class="form-control" name="currency" data-valid="required">
									<option value="">Select Here</option>
								</select>
							</div>
						</div>						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('addgroupinvoice')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>  
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){ 
	$(document).delegate('.creategroupinvoice','click', function(){
		$('#creategroupinvoice').modal('show');
	});	
});	
</script>
@endsection