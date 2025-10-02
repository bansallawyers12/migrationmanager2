@extends('layouts.admin')
@section('title', 'Email Invoice')

@section('content')
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Email Invoice</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Email Invoice</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Email To {{ @$invoice->customer->company_name}}</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/invoice/reminder', 'name'=>"email-invoice", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					  {{ Form::hidden('id', @$invoice->id) }}
					  {{ Form::hidden('type', 'reminder') }}
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group" style="text-align:right;">
										<a style="margin-right:5px;" href="{{route('admin.invoice.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
										{{ Form::button('<i class="fa fa-mail"></i> Send', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("email-invoice")' ]) }}
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group"> 
										<label for="from" class="col-form-label">From <span style="color:#ff0000;">*</span></label>
										{{ Form::text('from', Auth::user()->email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
										@if ($errors->has('from'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('from') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group"> 
										<label for="send_to" class="col-form-label">Send To <span style="color:#ff0000;">*</span></label>
										{{ Form::text('send_to', @$invoice->customer->contact_email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
										@if ($errors->has('send_to'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('send_to') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group"> 
									<?php
									$amount_rec = \App\Models\InvoicePayment::where('invoice_id',$invoice->id)->get()->sum("amount_rec");
									$baldue = $invoice->amount - $amount_rec;
									$currency_sign = '₹';
										 $replacesub = array('{due_amount}', '{invoice_no}');					
									$replace_with_sub = array($currency_sign.$baldue, @$invoice->invoice);
									
									$subContent 	= 	$emailtemplate->subject;
									$subContent	=	str_replace($replacesub,$replace_with_sub,$subContent);
									?>
										<label for="subject" class="col-form-label">Subject <span style="color:#ff0000;">*</span></label>
										{{ Form::text('subject', @$subContent, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
										@if ($errors->has('subject'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('subject') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group"> 
										<div class="email_template" style="height: 500px;overflow-x: auto;">
											<?php
												 $replace = array('{customer_name}', '{invoice_no}', '{invoice_date}', '{due_date}','{amount}','{company_name}');					
					$replace_with = array(@$invoice->customer->first_name.' '.@$invoice->customer->last_name, @$invoice->invoice,@$invoice->invoice_date, @$invoice->due_date, $currency_sign.$baldue, @Auth::user()->company_name);
					
					$emailContent 	= 	$emailtemplate->description;
					$emailContent	=	str_replace($replace,$replace_with,$emailContent);
								echo htmlspecialchars_decode(stripslashes(@$emailContent)); ?>
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="col-sm-6">
										<div class="form-group"> 
											<label for="attach_invoice" class="col-form-label">Attach Invoice PDF </label>
											<input type="checkbox" name="attach_invoice" value="1" checked>
											@if ($errors->has('attach_invoice'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('attach_invoice') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
								</div>
								<div class="col-sm-12">
									<div class="form-group float-right">
										{{ Form::button('<i class="fa fa-mail"></i> Send', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("email-invoice")' ]) }}
									</div> 
								</div> 
							</div> 
						</div> 
					  {{ Form::close() }}
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>
@endsection