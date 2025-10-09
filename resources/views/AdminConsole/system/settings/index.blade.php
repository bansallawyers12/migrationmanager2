@extends('layouts.admin_client_detail')
@section('title', 'Gen Settings')

@section('content')

<div class="crm-container">
	<div class="main-content">
		<form method="POST" action="{{ url('admin/gen-settings/update') }}" name="add-visatype" autocomplete="off" enctype="multipart/form-data">
			@csrf
			<input type="hidden" name="id" value="{{ @$fetchedData->id }}">
			<div class="row">   
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Enquiry source</h4>
							<div class="card-header-action">
								<a href="{{route('admin.gensettings.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-3 col-md-3 col-lg-3">
		        	@include('../Elements/Admin/setting')
		        </div>       
				<div class="col-9 col-md-9 col-lg-9">
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Settings</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-12 col-lg-12">
												   
											<div class="form-group">
											    <label>Date Format</label>
											    <ul style="list-style:none;">
											        <li><label><input type="radio" <?php if( $setting && $setting->date_format == 'F j, Y'){ echo 'checked'; } ?> name="date_format" value="F j, Y"> {{date('F j, Y')}} <span>(F j, Y)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->date_format == 'Y-m-d'){ echo 'checked'; } ?> name="date_format" value="Y-m-d"> {{date('Y-m-d')}} <span>(Y-m-d)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->date_format == 'm/d/Y'){ echo 'checked'; } ?> name="date_format" value="m/d/Y"> {{date('m/d/Y')}} <span>(m/d/Y)</span></label></li>
											                <li><label><input type="radio" <?php if($setting && $setting->date_format == 'd/m/Y'){ echo 'checked'; } ?> name="date_format" value="d/m/Y"> {{date('d/m/Y')}} <span>(d/m/Y)</span></label></li>
											    </ul>
											    
											</div>
												</div>
											<div class="col-12 col-md-12 col-lg-12">
												   
											<div class="form-group">
											    <label>Time Format</label>
											    <ul style="list-style:none;">
											        <li><label><input type="radio" <?php if($setting && $setting->time_format == 'g:i a'){ echo 'checked'; } ?> name="time_format" value="g:i a"> {{date('g:i a')}} <span>(g:i a)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->time_format == 'g:i A'){ echo 'checked'; } ?> name="time_format" value="g:i A"> {{date('g:i A')}} <span>(g:i A)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->time_format == 'H:i'){ echo 'checked'; } ?> name="time_format" value="H:i"> {{date('H:i')}} <span>(H:i)</span></label></li>
											              
											    </ul>
											    
											</div>
												</div>		
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary">Update</button>
								</div> 
							</div>
						</div>	
				</div>
			</div>
		</form>	
	</div>
</div>

@endsection