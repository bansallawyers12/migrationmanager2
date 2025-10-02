@extends('layouts.admin')
@section('title', 'Edit Partner Type')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/partner-type/edit', 'name'=>"edit-partnertype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			{{ Form::hidden('id', @$fetchedData->id) }}
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Partner Type</h4>
								<div class="card-header-action">
									<a href="{{route('admin.feature.partnertype.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="name">Name <span class="span_req">*</span></label>
														{{ Form::text('name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
														@if ($errors->has('name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
													<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="name">Master Category <span class="span_req">*</span></label>
													<select class="form-control" data-valid="required" name="category">
													    
													    <option value="">Select</option>
										@foreach(\App\Category::all() as $clists)
										<option @if(@$fetchedData->category_id == $clists->id) selected @endif value="{{$clists->id}}">{{$clists->category_name}}</option>
										@endforeach
													</select>
														@if ($errors->has('name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
										</div> 
									</div>
								</div>
								<div class="form-group float-right">
									{{ Form::submit('Update Partner Type', ['class'=>'btn btn-primary' ]) }}
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