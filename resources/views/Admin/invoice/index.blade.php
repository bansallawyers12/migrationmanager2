@extends('layouts.admin_client_detail')
@section('title', 'Invoices')

@section('content')

<div class="crm-container">
	<div class="main-content">
		<h1 class="m-0 text-dark mb-4">Invoices</h1>
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error"> 
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-12">
					<div class="card"> 
						<div class="card-header">   
							<div class="card-title">
								<a href="{{route('admin.invoice.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Invoices</a> 
							</div> 
							<div class="card-tools card_tools">
								<div class="input-group input-group-sm" style="width: 150px;">
									<input type="text" name="table_search" class="form-control float-right" placeholder="Search">
									<div class="input-group-append">
										<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
									</div>
								</div>
							</div>
						</div>    
						<div class="card-body table-responsive">
							<table id="invoicetable" class="table table-bordered table-hover text-nowrap">
							  <thead>
								<tr> 
								  <th class="no-sort"><input type="checkbox" id="checkedAll"></th>
								  <th>Date</th>
								  <th>Invoice</th>  
								  <th>Order Number</th>
								  <th>Customer Name</th>
								  <th>Status</th>
								  <th>Due Date</th>
								  <th class="no-sort">Amount</th>
								  <th class="no-sort">Balance Due</th>
								  <th class="no-sort">Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
							   @if(@$totalData !== 0)
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}"> 
								  <td><input class="checkSingle" type="checkbox" name="allcheckbox" value="{{@$list->id}}"></td>	
								  <td>{{date('d/m/Y',strtotime($list->invoice_date))}}</td> 
								  <td><a href="{{URL::to('/admin/invoice/lists/'.base64_encode(convert_uuencode(@$list->id)))}}">{{$list->invoice}}</a></td> 
								  <td>{{$list->order_id}}</td> 
								  <td>{{$list->customer->first_name}} {{$list->customer->last_name}}</td> 
								  <td>
									<?php
									$today = date('Y-m-d');
										$count = 2;
										$datetime1 = new DateTime($today);
										$datetime2 = new DateTime($list->due_date);
										$interval = $datetime1->diff($datetime2);
										$diff = $interval->format('%a');
										if((strtotime($today) > strtotime($list->due_date)) && $list->status != 1){
											echo '<span class="float-right text-overdue text-uppercase">OVERDUE BY '.$diff.' DAYS</span>';
										}else{	
											if($list->status == 1){
												echo '<span class="float-right text-accepted text-uppercase">Paid</span>';
											}else if($list->status == 2){
												echo '<span class="float-right text-sent text-uppercase">Sent</span>';
											}else if($list->status == 3){
												echo '<span class="float-right text-accepted text-uppercase">Partially Paid</span>';
											}else{
												echo '<span class="float-right text-sent text-uppercase">Draft</span>';
											}
										}
									?>
								  </td>
								  <?php $currencydata = \App\Models\Currency::where('id',$list->currency_id)->first(); 
								  
								  ?>
								  <td>{{date('d/m/Y',strtotime($list->due_date))}}</td> 
								  <td>{{number_format($list->amount, $currencydata->decimal)}}</td> 
								  <td>{{number_format($list->amount, $currencydata->decimal)}}</td> 
								  <td>
									<div class="nav-item dropdown action_dropdown">
										<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
										<div class="dropdown-menu">
											<a href="{{URL::to('/admin/invoice/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a>
											<a href="javascript:;" onClick="deleteAction({{@$list->id}}, 'invoice')"><i class="fa fa-trash"></i> Delete</a>
										</div>
									</div>  
								  </td> 
								</tr>	 
							  @endforeach	
								@else
								<tr>
									<td style="text-align:center;" colspan="9">
										No Record found
									</td>
								</tr>										
							  @endif 							  
							  </tbody> 
							 
							</table>
							<div class="card-footer hide">
							{{-- {!! $lists->appends(\Request::except('page'))->render() !!} --}}
							 </div>
						  </div>
					</div>	
				</div>	
			</div>
	</div>
</div>
@endsection