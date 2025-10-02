@extends('layouts.admin')
@section('title', 'Add Product')

@section('content')
<style>
	textarea.form-control{height: 100px !important;}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section"> 
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/products/store', 'name'=>"add-products", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Products</h4>
								<div class="card-header-action">
									<a href="{{route('admin.products.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="accordion-header" >
											<h4>Product Details</h4>
										</div>
<div class="accordion-body collapse show" id="product_details">
	<div class="row">
		<div class="col-12 col-md-4 col-lg-4">
			<div class="form-group"> 
				<label for="name">Name <span class="span_req">*</span></label>
				{{ Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
				@if ($errors->has('name'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('name') }}</strong>
					</span> 
				@endif
			</div>
		</div>
		<div class="col-12 col-md-4 col-lg-4">
			<div class="form-group"> 
				<label for="partner">Partner</label>
				<select class="form-control select2" data-valid="required" id="intrested_product" name="partner">
					<option value="">Select Partner</option>
					@foreach(\App\Models\Partner::all() as $plist)
						<option value="{{$plist->id}}">{{$plist->partner_name}}</option>
					@endforeach
				</select>
				@if ($errors->has('partner'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('partner') }}</strong>
					</span> 
				@endif
			</div>
		</div>
		<div class="col-12 col-md-4 col-lg-4">
			<div class="form-group">  
				<label for="branches">Branches</label>
				<select class="form-control select2" data-valid="required" id="intrested_branch" name="branches">
					<option></option>
				</select>
				@if ($errors->has('branches'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('branches') }}</strong>
					</span> 
				@endif
			</div>
		</div>
		<div class="col-12 col-md-4 col-lg-4">
			<div class="form-group"> 
				<label for="product_type">Product Type</label>
				<select class="form-control select2" data-valid="required" name="product_type">
				<option value=""></option>
				@foreach(\App\Models\ProductType::all() as $plist)
					<option value="{{$plist->name}}">{{$plist->name}}</option>
				@endforeach
				</select>
				@if ($errors->has('product_type'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('product_type') }}</strong>
					</span> 
				@endif
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-6">
			<div class="form-group"> 
				<label for="revenue_type" class="d-block">Revenue Type</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" id="revenue_client" value="Revenue From Client" name="revenue_type">
					<label class="form-check-label" for="revenue_client">Revenue From Client</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" id="commision_partner" value="Commission From Partner" name="revenue_type">
					<label class="form-check-label" for="commision_partner">Commission From Partner</label>
				</div>
				@if ($errors->has('revenue_type'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('revenue_type') }}</strong>
					</span> 
				@endif
			</div>
		</div>
	</div>
	</div>
	</div>
<div class="accordion">
	<div class="accordion-header" role="button">
		<h4>Product Information</h4>
	</div>
	<div class="accordion-body collapse show" id="product_info" >
		<div class="row">
			<div class="col-12 col-md-6 col-lg-6">
				<div class="form-group"> 
					<label for="duration">Duration</label>
					{{ Form::text('duration', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Duration' )) }}
					<span class="span_note">e.g: 1 year 2 months 6 weeks</span> 
					@if ($errors->has('duration'))
						<span class="custom-error" role="alert">
							<strong>{{ @$errors->first('duration') }}</strong>
						</span> 
					@endif
				</div>
			</div> 
			<div class="col-12 col-md-6 col-lg-6">
				<div class="form-group"> 
					<label for="intake_month">Intake Month</label>
					<select class="form-control select2" name="intake_month">
						<option>-- Select Intake Month --</option>
						<option value="January">January</option>
						<option value="February">February</option>
						<option value="March">March</option>
						<option value="April">April</option>
						<option value="May">May</option>
						<option value="June">June</option>
						<option value="July">July</option>
						<option value="August">August</option>
						<option value="September">September</option>
						<option value="October">October</option>
						<option value="November">November</option>
						<option value="December">December</option>
					</select>
					@if ($errors->has('revenue_type'))
						<span class="custom-error" role="alert">
							<strong>{{ @$errors->first('revenue_type') }}</strong>
						</span> 
					@endif
				</div>
			</div>
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group">
														<label for="description">Description</label>
														<textarea class="summernote-simple" name="description"></textarea>
														@if ($errors->has('description'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('description') }}</strong>
															</span>  
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group">
														<label for="note">Note</label>
														<textarea class="form-control" name="note"></textarea>
														@if ($errors->has('note'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('note') }}</strong>
															</span>  
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</div> 
								  
								<div class="form-group float-right">
									{{ Form::button('Save Product', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-products")']) }}
								</div>
							</div>
						</div>
					</div>
				</div>
			 {{ Form::close() }}	
		</div>
	</section>
</div>

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('#intrested_product','change', function(){
		var v = $('#intrested_product option:selected').val();
		if(v != ''){
			$('.popuploader').show();
			$.ajax({
				url: '{{URL::to('/admin/getnewPartnerbranch')}}',
				type:'GET',
				data:{cat_id:v},
				success:function(response){
					$('.popuploader').hide();
					$('#intrested_branch').html(response);
				
				}
			});
		}
	});
});
</script>
@endsection