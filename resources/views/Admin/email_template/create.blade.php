@extends('layouts.admin_client_detail')
@section('title', 'Create Email Template')

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
			<form action="{{ url('admin/email_templates/store') }}" method="POST" name="add-template" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create Email Template</h4>
								<div class="card-header-action">
									<a href="{{route('admin.email.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
											<h4>Template Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="title">Name <span class="span_req">*</span></label>
														<input type="text" name="title" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Template Name">
														@if ($errors->has('title'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('title') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="subject">Subject <span class="span_req">*</span></label>
														<input type="text" name="subject" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Subject">
														@if ($errors->has('subject'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('subject') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="description">Description <span class="span_req">*</span></label>
														<textarea name="description" data-valid="required" class="form-control summernote-simple" placeholder="Enter template description" style="height: 200px;"></textarea>
														@if ($errors->has('description'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('description') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary" onClick="customValidate('add-template')"><i class="fa fa-save"></i> Save Template</button>
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