@extends('layouts.admin')
@section('title', 'Tax Setting')

@section('content')
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">New Tax</h1>
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
								<li class="nav-item"> <a href="{{route('admin.returnsetting')}}" id="ember5168" class="nav-link ember-view"> GST Settings </a> </li> <!----><!----> </ul>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">New Tax</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/settings/taxes/savereturnsetting', 'name'=>"add-city", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12 is_gst_yes">
									<div class="form-group"> 
										<label for="name" class="col-form-label">Tax Name <span style="color:#ff0000;">*</span></label>
										{{ Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
						
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12 is_gst_yes ">
									<div class="form-group"> 
										<label for="rate" class="col-form-label">Rate % <span style="color:#ff0000;">*</span></label>
									
										<input type="text" name="rate" onkeyup="this.value=this.value.replace(/[^0-9\.]/g,'')" autocomplete="off" class="form-control" data-valid="required">
										@if ($errors->has('rate'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('rate') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-12" >
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
@endsection