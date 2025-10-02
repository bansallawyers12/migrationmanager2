@extends('layouts.admin')
@section('title', 'Subject Area')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="{{ url('admin/subject/store') }}" name="add-visatype" autocomplete="off" enctype="multipart/form-data" method="POST">
				@csrf
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Subject Area</h4>
								<div class="card-header-action">
									<a href="{{route('admin.feature.subject.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
														<input type="text" name="name" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Name" value="{{ old('name') }}">
														@if ($errors->has('name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="name">Subject Area <span class="span_req">*</span></label>
														<select class="form-control select2" name="subjectarea">
															<option value=""></option>
															@foreach(\App\SubjectArea::all() as $alist)
																<option value="{{$alist->id}}">{{$alist->name}}</option>
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
									<button type="submit" class="btn btn-primary">Save</button>
								</div> 
							</div>
						</div>	
					</div>
				</div>
			</form>	
		</div>
	</section>
</div>

@endsection