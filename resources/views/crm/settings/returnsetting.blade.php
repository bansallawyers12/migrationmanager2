@extends('layouts.crm_client_detail')
@section('title', 'Tax Setting')

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
							<h4>GST Settings</h4>
						</div>
						<form action="{{ url('settings/taxes/savereturnsetting') }}" method="POST" name="add-city" autocomplete="off" enctype="multipart/form-data">
							@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-12 col-lg-12">
									<div class="form-group"> 
										<label class="col-form-label">Is your business registered for GST?</label><br>
										<label class="mr-3"><input type="radio" value="yes" <?php if(@Auth::user()->is_business_gst == 'yes'){ echo 'checked'; } ?> name="is_business_gst"> Yes</label>
										<label><input type="radio" <?php if(@Auth::user()->is_business_gst != ''){ if(Auth::user()->is_business_gst == 'no'){ echo 'checked'; } }else{ echo 'checked'; } ?> value="no" name="is_business_gst"> No</label>
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-12 col-md-6 col-lg-6 is_gst_yes" style="<?php if(@Auth::user()->is_business_gst != ''){ if(@Auth::user()->is_business_gst == 'yes'){ ?>display:block;<?php }else{ ?>display:none;<?php }}else{ ?>display:none;<?php } ?>">
									<div class="form-group"> 
										<label for="gstin">GSTIN <span class="span_req">*</span></label>
										<input type="text" name="gstin" value="{{ @Auth::user()->gstin }}" class="form-control" data-valid="" autocomplete="off" placeholder="">
										<small class="form-text text-muted">Maximum 15 digits</small>
										@if ($errors->has('gstin'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('gstin') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-12 col-md-6 col-lg-6 is_gst_yes" style="<?php if(@Auth::user()->is_business_gst != ''){ if(@Auth::user()->is_business_gst == 'yes'){ ?>display:block;<?php }else{ ?>display:none;<?php }}else{ ?>display:none;<?php } ?>">
									<div class="form-group"> 
										<label for="gst_date">GST Registered On</label>
										<input type="text" name="gst_date" value="{{ @Auth::user()->gst_date }}" class="form-control commodategst" data-valid="" autocomplete="off" placeholder="">
										
										@if ($errors->has('gst_date'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('gst_date') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-12">
									<div class="form-group float-right">
										<button type="submit" class="btn btn-primary" onClick="customValidate('add-city')"><i class="fa fa-save"></i> Save</button>
									</div> 
								</div> 
							</div> 
						</div> 
						</form>
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