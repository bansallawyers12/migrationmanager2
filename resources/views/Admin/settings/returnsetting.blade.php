@extends('layouts.admin')
@section('title', 'Tax Setting')

@section('content')
 <style>
 
 <?php if(@Auth::user()->is_business_gst != ''){ if(@Auth::user()->is_business_gst == 'yes'){ ?>.is_gst_yes{display:block;} <?php }else{ ?>.is_gst_yes{display:none;} <?php }}else{ ?>.is_gst_yes{display:none;}<?php } ?>
 </style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Tax Setting</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Tax Setting</li>
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
				<div class="col-md-4">	
					<div class="card">
						<div class="card-body p-0" style="display: block;">
							<ul class="nav nav-pills flex-column"> <!---->
								<li class="nav-item"> <a href="{{route('admin.returnsetting')}}" id="ember5168" class="nav-link active ember-view"> GST Settings </a> </li> <!----><!----> </ul>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">GST Settings</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/settings/taxes/savereturnsetting', 'name'=>"add-city", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group" style="text-align:right;">
							
									</div>
								</div>
							
								<div class="col-sm-12">
									<div class="form-group"> 
										<label for="" class="col-form-label">Is your business registered for GST?</label>
										<label for="" class=""><input type="radio" value="yes" <?php if(@Auth::user()->is_business_gst == 'yes'){ echo 'checked'; } ?> name="is_business_gst"> Yes</label>
										<label for="" class=""><input type="radio" <?php if(@Auth::user()->is_business_gst != ''){ if(Auth::user()->is_business_gst == 'no'){ echo 'checked'; } }else{ echo 'checked'; } ?> value="no" name="is_business_gst"> No</label>
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12 is_gst_yes">
									<div class="form-group"> 
										<label for="gstin" class="col-form-label">GSTIN <span style="color:#ff0000;">*</span></label>
										{{ Form::text('gstin', @Auth::user()->gstin, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
										<p>(Maximum 15 digits)</p>
										@if ($errors->has('gstin'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('gstin') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12 is_gst_yes ">
									<div class="form-group"> 
										<label for="gst_date" class="col-form-label">GST Registered On</label>
										{{ Form::text('gst_date', @Auth::user()->gst_date, array('class' => 'form-control commodategst', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
										
										@if ($errors->has('gst_date'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('gst_date') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12 <?php if(@Auth::user()->is_business_gst != ''){ if(@Auth::user()->is_business_gst == 'yes'){ ?><?php }else{ ?><?php }}else{ ?>is_gst_yes<?php } ?>" >
									<div class="form-group float-right">
										{{ Form::button('<i class="fa fa-save"></i> Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-city")' ]) }}
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
<script>
jQuery(document).ready(function($){
	$('input[name="is_business_gst"]').on('change', function(){
		var val = $('input[name="is_business_gst"]:checked').val();
		if(val == 'yes'){
			$('.is_gst_yes').show();
			$('input[name="gstin"]').attr('data-valid','required min-15 max-15');
		}else{
			$('.is_gst_yes').hide();
			$('input[name="gstin"]').attr('data-valid','');
		}
	});
});
</script>
@endsection