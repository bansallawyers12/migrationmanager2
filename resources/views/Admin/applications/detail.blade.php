@extends('layouts.admin')
@section('title', 'Application Detail')

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
							<h4>Application Detail</h4>
							<div class="card-header-action">
								<a href="#" class="btn btn-primary">Application List</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-3 col-md-3 col-lg-3">
					<div class="card author-box">
						<div class="card-body">
							<div class="author-box-center">
							<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>SK</b></span>
								<div class="clearfix"></div>
								<div class="author-box-name">
									<a href="#">Sunil Kumar</a>
								</div>
								<div class="author-rating">
									<a href="javascript:;" rating="Lost" class="change_client_status lost"><i class="fas fa-exclamation-triangle"></i> Lost</a>
									<a href="javascript:;" rating="Cold" class="change_client_status cold" style=""><i class="fas fa-snowflake"></i> Cold</a>
									<a href="javascript:;" rating="Warm" class="change_client_status warm" style=""><i class="fas fa-mug-hot" ></i> Warm</a>
									<a href="javascript:;" rating="Hot" class="change_client_status hot" style=""><i class="fas fa-fire"></i> Hot</a>
								</div>
								<div class="author-mail_sms">
									<a href="#" title="Compose SMS"><i class="fas fa-comment-alt"></i></a>
									<a href="javascript:;" data-id="" data-email="" data-name="" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a> 
									<a href="#" title="Edit"><i class="fa fa-edit"></i></a>
									<a href="javascript:;" onclick="" title="Archive"><i class="fas fa-archive"></i></a>
								</div>
								<p class="clearfix" style="text-align:left;">
									<span class="float-left">Application Sales Forecast</span>
									<span class="float-right text-muted"> 0.00  USD</span>
								</p>
								<p class="clearfix" style="text-align:left;">
									<span class="float-left">Interested Services Sales Forecast</span>
									<span class="float-right text-muted"> 0.00  USD</span>
								</p>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<h4>Personal Details</h4>
						</div>
						<div class="card-body">
							<p class="clearfix"> 
								<span class="float-left">Tag(s):</span>
								<span class="float-right text-muted">
									<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
								</span>
							</p>
							<div class="clearfix">
								<span class="float-left">Client Portal:</span>
								<div class="custom-switches float-right">
									<label class="custom-switch">
										<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked>
										<span class="custom-switch-indicator"></span>
									</label>
								</div>
							</div>
							<p class="clearfix"> 
								<span class="float-left">Added From:</span>
								<span class="float-right text-muted">system</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Client Id:</span>
								<span class="float-right text-muted">-</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Internal Id:</span>
								<span class="float-right text-muted">13</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Date Of Birth:</span>
								<span class="float-right text-muted">-</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Phone No:</span>
								<span class="float-right text-muted">+61455455455</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Email:</span>
								<span class="float-right text-muted">santu537@gmail.com</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Secondary Email:</span>
								<span class="float-right text-muted">-</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Address:</span>
								<span class="float-right text-muted">37 Mossigle, Pakenham, Victoria, 3810, Australia</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Country of Passport:</span>
								<span class="float-right text-muted">India</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Passport Number:</span>
								<span class="float-right text-muted">E3434345</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Preferred Intake:</span>
								<span class="float-right text-muted">May 2022</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Visa Expiry Date:</span>
								<span class="float-right text-muted">2022-05-30</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Visa type:</span>
								<span class="float-right text-muted">Student</span>
							</p> 
							<div class="client_added client_info_tags"> 
								<span class="">Added By:</span>
								<div class="client_info">
									<div class="cl_logo">A</div>
									<div class="cl_name">
										<span class="name">Arun</span>
										<span class="email">arun@gmail.com</span>
									</div>
								</div> 
							</div>
							<div class="client_assign client_info_tags"> 
								<span class="">Assignee:</span>
								<div class="client_info">
									<div class="cl_logo">A</div>
									<div class="cl_name">
										<span class="name">Arun</span>
										<span class="email">arun@gmail.com</span>
									</div>
								</div>
							</div>
							<p class="clearfix"> 
								<span class="float-left">Followers</span>
								<span class="float-right text-muted">
									<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
								</span>
							</p>	
						</div>
					</div>
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="activities-tab" href="#activities" role="tab" aria-controls="activities" aria-selected="true">Activities</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a> 
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="interested_service-tab" href="#interested_service" role="tab" aria-controls="interested_service" aria-selected="false">Interested Services</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="quotations-tab" href="#quotations" role="tab" aria-controls="quotations" aria-selected="false">Quotations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="accounts-tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="false">Accounts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="conversations-tab" href="#conversations" role="tab" aria-controls="conversations" aria-selected="false">Conversations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="tasks-tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">Tasks</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="education-tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Education</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="other_info-tab" href="#other_info" role="tab" aria-controls="other_info" aria-selected="false">Other Information</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="checkinlogs-tab" href="#checkinlogs" role="tab" aria-controls="checkinlogs" aria-selected="false">Check-In Logs</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								<div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
									<div class="activities">
										
									</div>	
								</div>	
								<div class="tab-pane fade show active" id="application" role="tabpanel" aria-labelledby="application-tab">
									<div class="card-header-action" style="padding-bottom:15px;">
										<div class="float-left">
											<h5>In Progress</h5> 
										</div>
										<div class="float-right">
											<div class="application_btns">
												<a href="javascript:;" class="btn btn-primary"><i class="fa fa-print"></i></a>
												<a href="javascript:;" class="btn btn-outline-danger"><i class="fa fa-times"></i> Discontinue</a>
												<a href="javascript:;" class="btn btn-outline-primary"><i class="fa fa-angle-left"></i> Back to Previous Stage</a>
												<a href="javascript:;" class="btn btn-success">Proceed to Next Stage <i class="fa fa-angle-right"></i></a>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="application_grid_col">
										<div class="grid_column">
											<span>Course:</span>
											<p>10 Weeks Beginners</p>
										</div>
										<div class="grid_column">
											<span>School:</span>
											<p>Virginia School of English</p>
										</div>
										<div class="grid_column">
											<span>Branch:</span>
											<p>Head Office</p>
										</div>
										<div class="grid_column">
											<span>Workflow:</span>
											<p>Us Education F1</p>
										</div>
										<div class="grid_column">
											<span>Current Stage:</span>
											<p>Visa Application</p>
										</div>
										<div class="grid_column">
											<span>Application Id:</span>
											<p>13</p>
										</div>
										<div class="grid_column">
											<span>Partner's Client Id:</span>
											<p>22</p>
										</div>
										<div class="grid_column">
											<span>Started at:</span>
											<p>2022-05-29</p>
										</div>
										<div class="grid_column">
											<span>Last Updated:</span>
											<p>2022-06-06</p>
										</div>
										<div class="grid_column">
											<div class="overall_progress">
												<span>Overall Progress:</span>
											</div>
										</div>
										<div class="grid_column last_grid_column">
											<div class="view_other_detail">
												<a href="#" class="btn btn-outline-primary">View Other Details</a>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="divider"></div>
									<div class="row">
										<div class="col-md-9">
											<div class="application_other_info">
												<ul class="nav nav-pills" id="applicat_detail_tabs" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" id="applicate_activities-tab" data-toggle="tab" href="#applicate_activities">Activities</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents">Documents</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" id="notes-tab" data-toggle="tab"  href="#notes">Notes</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks">Tasks</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" id="paymentschedule-tab" data-toggle="tab" href="#paymentschedule">Payment Schedule</a>
													</li>
												</ul> 
												<div class="tab-content" id="applicationContent">
													<div class="tab-pane fade show active" id="applicate_activities" role="tabpanel" aria-labelledby="applicate_activities-tab">
														<div id="accordion">
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed active" role="button" data-toggle="collapse" data-target="#application_accor" aria-expanded="false">
																	<h4>Application</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="application_accor" data-parent="#accordion" style="">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Acceptance</b> to <b>Application</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Acceptance</b> to <b>Application</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Acceptance</b> to <b>Application</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#acceptance">
																	<h4>Acceptance</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="acceptance" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Application</b> to <b>Acceptance</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Application</b> to <b>Acceptance</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div> 
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Payment</b> to <b>Acceptance</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#payment">
																	<h4>Payment</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="payment" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Application</b> to <b>Acceptance</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Application</b> to <b>Acceptance</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div> 
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Payment</b> to <b>Acceptance</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#formi20">
																	<h4>Form I 20</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="formi20" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Application</b> to <b>Acceptance</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Application</b> to <b>Acceptance</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div> 
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Payment</b> to <b>Acceptance</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																	</div>
																</div>
															</div> 
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#visa_application">
																	<h4>Visa Application</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="visa_application" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Form I 20</b> to <b>Visa Application</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Form I 20</b> to <b>Visa Application</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div> 
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Form I 20</b> to <b>Visa Application</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#interview">
																	<h4>Interview</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="interview" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Visa Application</b> to <b>Interview</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Enrolment</b> to <b>Interview</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div> 
																	</div>
																</div>
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#enrolment">
																	<h4>Enrolment</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="enrolment" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Interview</b> to <b>Enrolment</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Course Ongoing</b> to <b>Enrolment</b></span>
																			<span class="span_time">07 Mon, Jun 2022 09:13 PM</span>
																		</div> 
																	</div>
																</div>
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#course_ongoing">
																	<h4>Course Ongoing</h4>
																	<div class="accord_hover">
																		<a title="Add Note" href="#"><i class="fa fa-file-alt"></i></a>
																		<a title="Add Documents	" href="#"><i class="fa fa-file"></i></a>
																		<a title="Add Appointments" href="#"><i class="fa fa-calendar"></i></a>
																		<a title="Email" href="#"><i class="fa fa-envelope"></i></a>
																	</div>
																</div>
																<div class="accordion-body collapse" id="course_ongoing" data-parent="#accordion">
																	<div class="activity_list">
																		<div class="activity_col">
																			<span class="span_txt"><b>Arun</b> moved the stage from <b>Enrolment</b> to <b>Course Ongoing</b></span>
																			<span class="span_time">06 Mon, Jun 2022 09:13 PM</span>
																		</div>
																	</div>
																</div>
															</div>
														</div> 
													</div>
													<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
														<div class="document_checklist">
															<h4>Document Checklist (0/0)</h4>
															<p>The changes & addition of the checklist will only be affected to current application only.</p>
															<div class="checklist">
																<ul>
																	<li><span>Application</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Acceptance</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Payment</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Form I 20</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Visa Application</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Interview</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Enrolment</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																	<li><span>Course Ongoing</span><a href="#"><i class="fa fa-plus"></i> Add New Checklist</a></li>
																</ul>
															</div>
														</div>
														<div class="table-responsive"> 
															<table class="table text_wrap">
																<thead>
																	<tr>
																		<th>Filename / Checklist</th>
																		<th>Related Stage</th>
																		<th>Added By</th>
																		<th>Added On</th>
																	</tr> 
																</thead>
																<tbody class="tdata">	
																	<tr id="">
																		<td></td> 
																		<td></td>
																		<td></td>
																		<td></td>
																	</tr>
																</tbody>
																<!--<tbody>
																	<tr>
																		<td style="text-align:center;" colspan="10">
																			No Record found
																		</td>
																	</tr>
																</tbody>-->
															</table> 
														</div>
													</div>
													<div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
													</div>
													<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
														<div id="taskaccordion">
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed active" role="button" data-toggle="collapse" data-target="#application_accor" aria-expanded="false">
																	<h4>Application</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="application_accor" data-parent="#taskaccordion" style="">
																</div>-->
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#acceptance">
																	<h4>Acceptance</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="acceptance" data-parent="#taskaccordion">
																</div>-->
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#payment">
																	<h4>Payment</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="payment" data-parent="#accordion">
																</div>-->
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#formi20">
																	<h4>Form I 20</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="formi20" data-parent="#accordion">
																</div>-->
															</div> 
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#visa_application">
																	<h4>Visa Application</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="visa_application" data-parent="#accordion">
																</div>-->
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#interview">
																	<h4>Interview</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="interview" data-parent="#accordion">
																</div>-->
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#enrolment">
																	<h4>Enrolment</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="enrolment" data-parent="#accordion">
																</div>-->
															</div>
															<div class="accordion cus_accrodian">
																<div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#course_ongoing">
																	<h4>Course Ongoing</h4>
																	<div class="accord_hover">
																		<a title="Add Task" href="#"><i class="fa fa-suitcase"></i></a>
																	</div>
																</div>
																<!--<div class="accordion-body collapse" id="course_ongoing" data-parent="#accordion">
																</div>-->
															</div>
														</div>
													</div>
													<div class="tab-pane fade" id="paymentschedule" role="tabpanel" aria-labelledby="paymentschedule-tab">
														<div class="row">
															<div class="col-md-6">
																<div class="schedule_box">
																	<div class="schedule_col">
																		<span>Scheduled</span>
																		<h4>0.00</h4>
																	</div>
																	<div class="schedule_col">
																		<span>Invoiced</span>
																		<h4>0.00</h4>
																	</div>
																	<div class="schedule_col">
																		<span>Pending</span>
																		<h4>0.00</h4>
																	</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="schedule_btns">
																	<a href="#" class="btn btn-primary"><i class="fa fa-plus"></i> Add Schedule</a>
																	<div class="dropdown d-inline">
																		<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Schedule</button>
																		<div class="dropdown-menu">
																			<a class="dropdown-item" href="javascript:;">Email Schedule</a>
																			<a class="dropdown-item" href="javascript:;">Preview Schedule</a>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="table-responsive"> 
															<table class="table text_wrap">
																<thead>
																	<tr>
																		<th>ID</th>
																		<th>Installment</th>
																		<th>Fee Type</th>
																		<th>Fee</th>
																		<th>Total Fees</th>
																		<th>Discounts</th>
																		<th>Invoicing</th>
																		<th>Status</th>
																		<th></th>
																	</tr> 
																</thead>
																<tbody class="tdata">	
																	<tr id="">
																		<td>12</td> 
																		<td>43556</td>
																		<td>Bank</td>
																		<td>45453</td>
																		<td>45453</td>
																		<td>1300</td>
																		<td>45453</td>
																		<td>Pending</td>
																		<td>
																			<div class="dropdown d-inline">
																				<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
																				<div class="dropdown-menu">
																					<a class="dropdown-item" href="javascript:;">Edit</a>
																					<a class="dropdown-item" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')">Delete</a>
																				</div>
																			</div>
																		</td>
																	</tr>
																</tbody>
																<!--<tbody>
																	<tr>
																		<td style="text-align:center;" colspan="10">
																			No Record found
																		</td>
																	</tr>
																</tbody>-->
															</table> 
														</div>
													</div> 
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="cus_sidebar">
												<div class="form-group">
													<label for="applied_intake">Applied Intake:</label>
													{{ Form::text('applied_intake', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
													<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
													@if ($errors->has('applied_intake'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('applied_intake') }}</strong>
														</span> 
													@endif 
												</div>
												<div class="app_date_sec">
													<div class="app_start_date common_apply_date">
														<span>Start</span>
														<div class="date_col">
															<div class="add_date">
																<span><i class="fa fa-plus"></i> Add</span>
															</div>  
															<input type="text" class="datepicker" />
															<div class="apply_val">
																<span class="month">Mar</span>
																<span class="day">31</span>
																<span class="year">2022</span>
															</div>
														</div>
													</div>
													<div class="app_end_date common_apply_date">
														<span>End</span>
														<div class="date_col">
															<div class="add_date">
																<span><i class="fa fa-plus"></i> Add</span> 
															</div>
															<input type="text" class="datepicker" />
															<div class="apply_val">
																<span class="month">Jun</span>
																<span class="day">24</span>
																<span class="year">2022</span>
															</div>
														</div>
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="divider"></div>
												<div class="setup_payment_sche">
													<a href="#" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Setup Payment Schedule</a>
												</div>
												<div class="divider"></div>
												<div class="cus_prod_fees">
													<h5>Product Fees <span>USD</span></h5>
													<a href="#"><i class="fa fa-edit"></i></a>
													<div class="clearfix"></div>
												</div>
												<p class="clearfix"> 
													<span class="float-left">Total Fee</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<p class="clearfix" style="color:#ff0000;"> 
													<span class="float-left">Discount</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<p class="clearfix" style="color:#6777ef;"> 
													<span class="float-left">Net Fee</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<div class="divider"></div>
												<div class="cus_prod_fees">
													<h5>Sales Forecast <span>USD</span></h5>
													<a href="#"><i class="fa fa-edit"></i></a>
													<div class="clearfix"></div>
												</div>
												<p class="clearfix"> 
													<span class="float-left">Partner Revenue</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<p class="clearfix"> 
													<span class="float-left">Client Revenue</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<p class="clearfix" style="color:#ff0000;"> 
													<span class="float-left">Discount</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<p class="clearfix" style="color:#6777ef;"> 
													<span class="float-left">Net Revenue</span>
													<span class="float-right text-muted">0.00</span>
												</p>
												<div class="form-group">
													<label for="expect_win_date">Expected Win Date:</label>
													{{ Form::text('expect_win_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
													<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
													@if ($errors->has('expect_win_date'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('expect_win_date') }}</strong>
														</span> 
													@endif 
												</div>
												<div class="divider"></div>
												<div class="setup_payment_sche">
													<a href="#" class="btn btn-primary">View Appliation Ownership Ratio</a>
												</div>
												<div class="divider"></div> 
												<div class="client_added client_info_tags"> 
													<span class="">Started By:</span>
													<div class="client_info">
														<div class="cl_logo">A</div>
														<div class="cl_name">
															<span class="name">Arun</span>
															<span class="email">arun@gmail.com</span>
														</div>
													</div> 
												</div>
												<div class="client_assign client_info_tags"> 
													<span class="">Assignee:</span>
													<div class="client_info">
														<div class="cl_logo">A</div>
														<div class="cl_name">
															<span class="name">Arun</span>
															<span class="email">arun@gmail.com</span>
														</div>
													</div>
												</div>
												<div class="divider"></div> 
												<p class="clearfix"> 
													<span class="float-left">Super Agent:</span>
													<span class="float-right text-muted">
														<a href="javascript:;" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add</a>
													</span>
												</p>
												<p class="clearfix"> 
													<span class="float-left">Sub Agent:</span>
													<span class="float-right text-muted">
														<a href="javascript:;" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add</a>
													</span>
												</p>
											</div> 
										</div>
									</div>
								</div>	
								<div class="tab-pane fade" id="interested_service" role="tabpanel" aria-labelledby="interested_service-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" data-target=".add_interested_service" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="interest_serv_list">
										<div class="interest_column">
											<div class="interest_serv_status status_default">
												<span>Draft</span>
											</div>
											<div class="interest_serv_info">
												<h4>Us Education F1</h4>
												<h6>Bachelors in Information Technology</h6>
												<p>California State University</p>
												<p>Head Office</p>
											</div>
											<div class="interest_serv_fees">
												<div class="fees_col cus_col">
													<span class="cus_label">Product Fees</span>
													<span class="cus_value">AUD: 0.00</span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Sales Forecast</span>
													<span class="cus_value">AUD: 0.00</span>
												</div>
											</div>
											<div class="interest_serv_date">
												<div class="date_col cus_col">
													<span class="cus_label">Expected Start Date</span>
													<span class="cus_value">2022-05-25</span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Expected Win Date</span>
													<span class="cus_value">2022-05-31</span>
												</div>
											</div>
											<div class="interest_serv_row">
												<div class="serv_user_data">
													<div class="serv_user_img">A</div>
													<div class="serv_user_info">
														<span class="serv_name">Arun</span>
														<span class="serv_create">2022-05-31</span>
													</div> 
												</div>
												<div class="serv_user_action">
													<a href="javascript:;" data-toggle="modal" data-target=".interest_service_view" class="btn btn-primary">View</a>
													<div class="dropdown d-inline dropdown_ellipsis_icon" style="margin-left:10px;">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item" href="javascript:;">Create Appliation</a>
															<a class="dropdown-item" href="javascript:;">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="interest_column">
											<div class="interest_serv_status status_active">
												<span>Converted</span>
											</div>
											<div class="interest_serv_info">
												<h4>Us Education F1</h4>
												<h6>Bachelors in Information Technology</h6>
												<p>California State University</p>
												<p>Head Office</p>
											</div>
											<div class="interest_serv_fees">
												<div class="fees_col cus_col">
													<span class="cus_label">Product Fees</span>
													<span class="cus_value">AUD: 0.00</span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Sales Forecast</span>
													<span class="cus_value">AUD: 0.00</span>
												</div>
											</div>
											<div class="interest_serv_date">
												<div class="date_col cus_col">
													<span class="cus_label">Expected Start Date</span>
													<span class="cus_value">2022-05-25</span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Expected Win Date</span>
													<span class="cus_value">2022-05-31</span>
												</div>
											</div>
											<div class="interest_serv_row">
												<div class="serv_user_data">
													<div class="serv_user_img">A</div>
													<div class="serv_user_info">
														<span class="serv_name">Arun</span>
														<span class="serv_create">2022-05-31</span>
													</div> 
												</div>
												<div class="serv_user_action">
													<a href="javascript:;" data-toggle="modal" data-target=".interest_service_view" class="btn btn-primary">View</a>
													<div class="dropdown d-inline dropdown_ellipsis_icon" style="margin-left:10px;">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item" href="javascript:;">Delete</a>
														</div>
													</div>
												</div> 
											</div> 
										</div>
									</div>
									<div class="clearfix"></div>
								</div>	
								<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<div class="upload_document" style="display:inline-block;">
											<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
											<input type="file" name="document_upload"/>
										</div>
									</div>
									<div class="list_data"> 
										<div class="table-responsive"> 
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>File Name</th>
														<th>Added By</th>
														<th>File Size</th>
														<th>Added Date</th>
														<th></th>
													</tr> 
												</thead>
												<tbody class="tdata">	
													<tr id="">
														<td><i class="fas fa-file-image"></i> 1643474600_MYF_33 .jpg</td> 
														<td>Arun</td>
														<td>18.34 KB</td>
														<td>2022-05-29</td> 
														<td>
															<div class="dropdown d-inline">
																<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
																<div class="dropdown-menu">
																	<a class="dropdown-item" href="javascript:;">Rename</a>
																	<a class="dropdown-item" href="javascript:;">Preview</a>
																	<a class="dropdown-item" href="javascript:;">Download</a>
																	<a class="dropdown-item" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')">Delete</a>
																</div>
															</div>								  
														</td>
													</tr>
												</tbody>
												<!--<tbody>
													<tr>
														<td style="text-align:center;" colspan="10">
															No Record found
														</td>
													</tr>
												</tbody>-->
											</table> 
										</div>
									</div>
									<div class="grid_data">
										<div class="grid_list">
											<div class="grid_col"> 
												<div class="grid_icon">
													<i class="fas fa-file-image"></i>
												</div>
												<div class="grid_content">
													<span>1643474600_MYF_33 .jpg</span>
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item" href="javascript:;">Preview</a>
															<a class="dropdown-item" href="javascript:;">Download</a>
															<a class="dropdown-item" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" data-target=".add_appointment" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="appointment_info row"> 
										<div class="appointment_list col-md-5">
											<div class="appoint_date">
												<span>Jun 2022</span>
											</div> 
											<div class="appointment_grid_list">
												<div class="appointment_col active">
													<div class="appointdate">
														<h5>10 Fri</h5>
														<p>11:00 AM <span>1 day Ago</span></p>
													</div>
													<div class="title_desc">
														<h5>Lorum Ipsum</h5>
														<p>dfsgg</p>
													</div>
													<div class="appoint_created">
														<span class="span_label">Created By: <span>A</span></span>
														<div class="dropdown d-inline dropdown_ellipsis_icon">
															<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
															<div class="dropdown-menu">
																<a class="dropdown-item" href="javascript:;">Edit</a>
																<a class="dropdown-item" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')">Delete</a>
															</div>
														</div>  
													</div>
												</div>
												<div class="appointment_col">
													<div class="appointdate">
														<h5>12 Sun</h5>
														<p>09:00 AM <span>3 day Ago</span></p>
													</div>
													<div class="title_desc">
														<h5>Lorum Ipsum</h5>
														<p>fgdfg</p>
													</div>
													<div class="appoint_created">
														<span class="span_label">Created By: <span>A</span></span>
														<div class="dropdown d-inline dropdown_ellipsis_icon">
															<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
															<div class="dropdown-menu">
																<a class="dropdown-item" href="javascript:;">Edit</a>
																<a class="dropdown-item" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')">Delete</a>
															</div>
														</div>  
													</div>
												</div>
											</div> 
										</div>
										<div class="appointment_detail col-md-7">
											<div class="appointment_edit">
												<a href="#"><i class="fa fa-edit"></i></a>
											</div>
											<div class="content">
												<h4>SALES FORECAST</h4>
												<div class="appoint_item">
													<i class="fa fa-clock"></i> <span>11:00 AM</span>
												</div>
												<div class="appoint_item">
													<i class="fa fa-calendar"></i> <span>10 Fri, Jun 2022</span>
												</div>
												<div class="description">
													<p>saf sfsf dfgdfgdg dfgdfgsfgf gfsgfdg</p>
												</div>
												<div class="created_by">
													<span class="label">Created By:</span>
													<div class="appoint_avatar">
														<span class="avatar_icon">A</span>
														<span class="name">Test<br/><small>test@gmail.com</small></span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="quotations-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
									</div>
									<div class="table-responsive"> 
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>No</th>
													<th>Status</th>
													<th>Products</th>
													<th>Total Fee</th>
													<th>Due Date</th>
													<th>Created On</th>
													<th>Created By</th>
													<th></th>
												</tr> 
											</thead>
											<tbody class="tdata">	
												<tr id="id_1">
													<td>1</td>
													<td><span title="draft" class="ui label uppercase">draft</span></td>
													<td>1</td>
													<td>99.00 AUD</td>	
													<td>2022-05-31</td>
													<td>2022-05-31</td>
													<td>Arun</td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a class="dropdown-item has-icon" href="#"><i class="far fa-mail"></i> Decline</a>
																<a class="dropdown-item has-icon" href="javascript:;" onclick="deleteAction(1, 'quotations')">Archive</a>
															</div>
														</div>								  
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
									<div class="table-responsive"> 
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>Invoice No.</th>
													<th>Issue Date</th>
													<th>Service</th>
													<th>Invoice Amount</th>
													<th>Discount Given</th>
													<th>Income Shared</th>
													<th>Status</th>
													<th></th>
												</tr> 
											</thead>
											<tbody class="tdata">	
												<tr id="id_1">
													<td>3</td>
													<td>2022-06-03<span title="General" class="ui label uppercase">General</span></td>
													<td>Us Education F1</td>
													<td>AUD 1000.00</td>	
													<td>-</td>
													<td>-</td>
													<td><span class="ag-label--circular" style="color: #6777ef" >Paid</span></td> 
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a class="dropdown-item has-icon" href="#"><i class="far fa-mail"></i> View</a>
															</div>
														</div>								  
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="conversations" role="tabpanel" aria-labelledby="conversations-tab">
									<div class="conversation_tabs"> 
										<ul class="nav nav-pills round_tabs" id="client_tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Email</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" id="sms-tab" href="#sms" role="tab" aria-controls="sms" aria-selected="false">SMS</a>
											</li>
										</ul>
										<div class="tab-content" id="conversationContent">
											<div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
												<div class="conversation_list">
													<div class="conversa_item">
														<div class="ds_flex">
															<div class="title">
																<span>hiii</span>
															</div>
															<div class="conver_action">
																<div class="date">
																	<span>03:54 PM, 29 May, 2022</span>
																</div>
																<div class="conver_link">
																	<a href="javascript:;" ><i class="fas fa-file-alt"></i></a>
																	<a href="javascript:;" data-toggle="modal" data-target=".create_task"><i class="fas fa-shopping-bag"></i></a>
																</div> 
															</div>
														</div>
														<div class="email_info">
															<div class="avatar_img">
																<span>A</span>
															</div>
															<div class="email_content">
																<span class="email_label">Sent by:</span>
																<span class="email_sentby"><strong>Test</strong> [test@gmail.com]</span>
																<span class="label success">Delivered</span>
																<span class="span_desc">
																	<span class="email_label">Sent by</span>
																	<span class="email_sentby"><i class="fa fa-angle-left"></i>santosh@gmail.com<i class="fa fa-angle-right"></i></span>
																</span>
															</div>
														</div>
														<div class="divider"></div>
														<div class="email_desc">
															<p>Hi <b>sds</b>,</p>
															<p>Congratulation !! We are so glad to inform you that your visa has been granted. Please contact your counselor - <b>Arun</b> for further details.</p>
															<p>In the meantime, please feel free to contact us, if you need any further help.</p>
															<p>Best Regards,</p>
															<p><b>Bansal Education</b> Team</p>
														</div>
														<div class="divider"></div>
														<div class="email_attachment">
															<span class="attach_label"><i class="fa fa-link"></i> Attachments:</span>
															<div class="attach_file_list">
																<div class="attach_col">
																	<a href="#">quotation_6.pdf</a>
																</div>
															</div> 
														</div>
													</div>
												</div>
											</div>
											<div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
												<span>sms</span>
											</div>
										</div>
									</div>
								</div> 
								<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" data-target=".create_task" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
								</div>
								<div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
									<div class="card-header-action" style="padding-bottom:15px;">
										<div class="float-left">
											<h5>Education Background</h5> 
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".create_education" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_note">
										<span>* Click add button to fill education background </span>
									</div>
									<div class="education_list">
										<div class="education_item">
											<div class="row">
												<div class="col-md-5">
													<div class="title_desc">
														<h6>dfadsfds</h6>
														<p>ffsdf</p>
													</div>
												</div>
												<div class="col-md-7">
													<div class="education_info">
														<div class="edu_date">June 2019<span>-</span>June 2022</div>
														<div class="edu_score"><span>Score: 45.88 % </span></div>
														<div class="edu_study_area">
															<span>Bachelor</span>
															<span>Applied and Pure Science</span>
															<span>Biology</span>
														</div>
													</div>
													<div class="education_action">
														<a href="javascript:;"><i class="fa fa-edit"></i></a>
														<a href="javascript:;"><i class="fa fa-trash"></i></a>
													</div>
												</div>
											</div>
										</div> 
									</div>
									<div class="divider"></div>
									<div class="card-header-action" style="padding-top:15px;padding-bottom:10px;">
										<div class="float-left">
											<h5>English Test Scores</h5> 
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".edit_english_test" class="btn btn-primary"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_test_score edu_english_score">
										<div class="edu_test_row" style="text-align:center;">
											<div class="edu_test_col">&nbsp;</div>
											<div class="edu_test_col"><span>Listening</span></div>
											<div class="edu_test_col"><span>Reading</span></div>
											<div class="edu_test_col"><span>Writing</span></div>
											<div class="edu_test_col"><span>Speaking</span></div>
											<div class="edu_test_col"><span>Overall Scores</span></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>TOEFL</span></div>
											<div class="edu_test_col"><strong>-</strong></div>
											<div class="edu_test_col"><strong>6.99</strong></div>
											<div class="edu_test_col"><strong>4.99</strong></div>
											<div class="edu_test_col"><strong>5.99</strong></div>
											<div class="edu_test_col overal_block"><strong>7</strong></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>IELTS</span></div>
											<div class="edu_test_col"><strong>-</strong></div>
											<div class="edu_test_col"><strong>6.99</strong></div>
											<div class="edu_test_col"><strong>4.99</strong></div>
											<div class="edu_test_col"><strong>5.99</strong></div>
											<div class="edu_test_col overal_block"><strong>7</strong></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>PTE</span></div>
											<div class="edu_test_col"><strong>-</strong></div>
											<div class="edu_test_col"><strong>6.99</strong></div>
											<div class="edu_test_col"><strong>4.99</strong></div>
											<div class="edu_test_col"><strong>7.99</strong></div>
											<div class="edu_test_col overal_block"><strong>7</strong></div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="card-header-action" style="padding-top:15px;padding-bottom:10px;">
										<div class="float-left">
											<h5>Other Test Scores</h5> 
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".edit_other_test" class="btn btn-primary"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_test_score edu_othertest_score">
										<div class="edu_test_row" style="text-align:center;">
											<div class="edu_test_col"></div>
											<div class="edu_test_col"><span>SAT I</span></div>
											<div class="edu_test_col"><span>SAT II</span></div>
											<div class="edu_test_col"><span>GRE</span></div>
											<div class="edu_test_col"><span>GMAT</span></div>
										</div> 
										<div class="edu_test_row flex_row">
											<div class="edu_test_col">Overall Scores</div>
											<div class="edu_test_col overal_block"><strong>-</strong></div>
											<div class="edu_test_col overal_block"><strong>6.99</strong></div>
											<div class="edu_test_col overal_block"><strong>4.99</strong></div>
											<div class="edu_test_col overal_block"><strong>5.99</strong></div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="other_info" role="tabpanel" aria-labelledby="other_info-tab">
									<span>other_info</span>
								</div>
								<div class="tab-pane fade" id="checkinlogs" role="tabpanel" aria-labelledby="checkinlogs-tab">
									<div class="table-responsive"> 
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>ID</th>
													<th>Date</th>
													<th>Start</th>
													<th>End</th>
													<th>Session Time</th>
													<th>Visit Purpose</th>
													<th>Assignee</th>
													<th>Status</th>
													<th></th>
												</tr> 
											</thead>
											<tbody class="tdata">	
												<tr id="id_1">
													<td></td>
													<td></td>
													<td></td>
													<td></td>	
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a class="dropdown-item has-icon" href="#"><i class="far fa-mail"></i> Decline</a>
																<a class="dropdown-item has-icon" href="javascript:;" onclick="deleteAction(1, 'quotations')">Archive</a>
															</div>
														</div>								  
													</td>
												</tr>
											</tbody>
											<!--<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
														No Record found
													</td>
												</tr>
											</tbody>-->
										</table>
									</div>
								</div>	
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div> 

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	
});
</script>
@endsection