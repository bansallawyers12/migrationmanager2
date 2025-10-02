<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="keyword" content="E-Weblink CRM">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>CRM Digitrex | @yield('title')</title>
		<link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">
		<!-- Font Awesome -->
	  <link rel="stylesheet" href="{{URL::asset('public/icons/font-awesome/css/all.min.css')}}">
	  <!-- Ionicons -->
	  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	  <!-- Datatable -->
	  
	  <!-- Select2 -->
	  <link rel="stylesheet" href="{{URL::asset('public/css/select2.min.css')}}">
	  <link rel="stylesheet" href="{{URL::asset('public/css/select2-bootstrap4.min.css')}}">
	  <!-- Theme style -->
	  <link rel="stylesheet" href="{{URL::asset('public/css/admintheme.min.css')}}">
	  <!-- overlayScrollbars -->
	 
	  <link rel="stylesheet" type="text/css" href="{{URL::asset('public/css/bootstrap-select.min.css')}}" >
	  <!-- summernote -->
	  <link rel="stylesheet" href="{{URL::asset('public/css/summernote-bs4.css')}}"> 
	  <!-- style --> 
	  <link rel="stylesheet" href="{{URL::asset('public/css/style.css')}}">
	  <link rel="stylesheet" href="{{URL::asset('public/css/font-awesome.min.css')}}">
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
	 
	  <!--<link rel="stylesheet" href="{{URL::asset('public/css/niceCountryInput.css')}}">-->
	  <!-- Google Font: Source Sans Pro -->
	  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
	  
		<!-- jQuery -->
		<script src="{{URL::asset('public/js/jquery.min.js')}}"></script>	 
		<script>var billingdata = new Array();</script>	 
		
	<style>
        .upic > img {
            width: 32px;
            height: auto;
            float: left;
        }
        .margin-r-10{
            margin-right:10px
            }
        .margin-r-20{
            margin-right:20px
        }

        .ps_btn {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            color: #666666;
            padding: 5px 8px;
        }

	.dnone{display:none}
	.f18{font-size:18px;}
	.mt{margin-top:5px;}
	.btn-arrow-right {
	position: relative;
	padding-left: 18px;
	padding-right: 18px;}
	.btn-arrow-right {padding-left: 36px;}
	.btn-arrow-right:before,
	.btn-arrow-right:after{
		content:"";
		position: absolute;
		top: 5px;
		width: 22px;
		height: 22px;
		background: inherit;
		border: inherit;
		border-left-color: transparent;
		border-bottom-color: transparent;
		border-radius: 0px 4px 0px 0px;
		-webkit-border-radius: 0px 4px 0px 0px;
		-moz-border-radius: 0px 4px 0px 0px;}
	.btn-arrow-right:before,
	.btn-arrow-right:after {
		transform: rotate(45deg);
		-webkit-transform: rotate(45deg);
		-moz-transform: rotate(45deg);
		-o-transform: rotate(45deg);
		-ms-transform: rotate(45deg);}
	.btn-arrow-right:before {left: -11px;}
	.btn-arrow-right:after {right: -11px;}
	.btn-arrow-right:after { z-index: 1;}
	.btn-arrow-right:before{ background-color: white;}
	.text-ellipsis{white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}
	

	.dispM{display:block}
	.ww40{width:40%}
	.logo{display:block}
	.sear01{width:60%}
	.lh24{line-height:24px}.lh28{line-height:28px}.lin_drp a{color:#333}
	
	@media only screen and (max-width:479px)
        {
            .dispM{display:none}
            .ww40{width:100%}
			.logo{display:none!important}
			.sear01{width:100%; border:none;-webkit-box-shadow:none}
		}
		
        blockquote {
            font-size : 14px; 
        }
        .popover {max-width:700px;}
        .selec_reg{background-color:#f4f4f4; border:1px solid #ddd; color:#444; border-radius: 3px; font-size:12px}
        .selec_reg option{background-color:#fff; color:#444; padding:5px; cursor:pointer;}
        .f13{font-size:13px}
        .attch_downl a{width:270px; display:block; float:left; margin-bottom:8px; margin-right:20px}
        @font-face {
            font-family: 'Material Icons';
            font-style: normal;
            font-weight: 400;
            src: local('Material Icons'), local('MaterialIcons-Regular'), url(https://fonts.gstatic.com/s/materialicons/v21/2fcrYFNaTjcS6g4U3t-Y5ZjZjT5FdEJ140U2DJYC3mY.woff2) format('woff2');
        }

        .material-icons {
            font-family: 'Material Icons';
            -moz-font-feature-settings: 'liga';
            -moz-osx-font-smoothing: grayscale;
        }
        .qr_btn{padding:2px 10px 3px; border-radius:15px; cursor:pointer}
    
    </style>	
		   
	</head>
	<body class="hold-transition sidebar-mini layout-fixed loderover">
	
		<div class="wrapper">
		<div id="loader">
			<div class="overlay">
              <i class="fa fa-refresh fa-spin"></i>
            </div> 
        </div>
			<!--Header-->
			@include('../Elements/Admin/header')
		
			<!--Content-->
			<div class="app-body">
				<!--Left Side Bar-->
				@include('../Elements/Admin/left-side-bar')
				
				@yield('content')
				
			</div>
			<!--Footer-->
			@include('../Elements/Admin/footer')
		</div>	
		<div class="modal fade" id="leadsearch_modal">
			<div class="modal-dialog modal-lg">
			  <div class="modal-content">
				<div class="modal-header">
				  <h4 class="modal-title">Lead Search</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<form action="{{route('admin.leads.index')}}" method="get">
				<div class="modal-body"> 
					
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row">
									<label for="lead_id" class="col-sm-2 col-form-label">Lead ID</label>
									<div class="col-sm-10">
										{!! html()->text('lead_id', Request::get('lead_id'))->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Lead ID')->attribute('id', 'lead_id') !!}	 						
										@if ($errors->has('lead_id'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('lead_id') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="name" class="col-sm-2 col-form-label">Name</label>
									<div class="col-sm-10">
										{!! html()->text('name', Request::get('name'))->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Name')->attribute('id', 'name') !!}	 						
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="email" class="col-sm-2 col-form-label">Email</label>
									<div class="col-sm-10">
										{!! html()->text('email', Request::get('email'))->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Email')->attribute('id', 'email') !!}	 						
										@if ($errors->has('email'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('email') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="phone" class="col-sm-2 col-form-label">Phone</label>
									<div class="col-sm-10">
										{!! html()->text('phone', Request::get('phone'))->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Phone')->attribute('id', 'phone') !!}	 						
										@if ($errors->has('phone'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('phone') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="followupdate" class="col-sm-2 col-form-label">Followup Date</label>
									<div class="col-sm-10">
										{!! html()->text('followupdate', Request::get('followupdate'))->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Followup Date')->attribute('id', 'followupdate') !!}	 						
										@if ($errors->has('followupdate'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('followupdate') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							
							
						</div>
					
				</div>
				<div class="modal-footer justify-content-between">
				  <a href="{{route('admin.leads.index')}}" class="btn btn-default" >Reset</a>
				  <button type="submit" id="" class="btn btn-primary">Search</button>
				</div>
				</form>	
			  </div>
			  <!-- /.modal-content -->
			</div>
		<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->	
		<div class="customer_support">
			<a href="javascript:;" data-toggle="modal" data-target="#contactsupport_modal" class="btn btn-primary"><i class="fa fa-envelope"></i> Contact Support</a>
		</div>
		<div class="modal fade" id="contactsupport_modal">
			<div class="modal-dialog modal-lg">
			  <div class="modal-content">
				<div class="modal-header">
				  <h4 class="modal-title">At Your Service</h4>
				  <p>Responses to this email will be sent to info@eweblink.net</p>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<div class="modal-body">
					<form action="" method="post">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="subject" class="col-form-label">Subject</label>
									{!! html()->text('subject', '')->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Subject')->attribute('id', 'subject') !!}	 						
									@if ($errors->has('subject'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('subject') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="how_help_you" class="col-form-label">How can we help you today?</label>
									{!! html()->text('how_help_you', '')->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'How can we help you today?')->attribute('id', 'how_help_you') !!}	 						
									@if ($errors->has('how_help_you'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('how_help_you') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="attach_file" class="col-form-label">Attachments <i class="fa fa-explanation"></i></label>
									<input type="file" name="attach_file" class="" autocomplete="off" data-valid="" style="display:block;" />
									@if ($errors->has('attach_file'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('attach_file') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="contact_no" class="col-form-label">Contact Number</label>
									{!! html()->text('contact_no', '')->class('form-control')->attribute('data-valid', '')->attribute('autocomplete', 'off')->attribute('placeholder', 'Contact Number')->attribute('id', 'contact_no') !!}	 						
									@if ($errors->has('contact_no'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('contact_no') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="critical_request" class="col-form-label">How critical is your request?</label>
									<select name="critical_request" data-valid="required" id="critical_request" class="form-control" autocomplete="new-password">
										<option value="None">None</option>
										<option value="Just FYI">Just FYI</option>
										<option value="Nothing urgent, can wait">Nothing urgent, can wait</option>
										<option value="I'm stuck, need assistance">I'm stuck, need assistance</option>
									</select>	 						
									@if ($errors->has('critical_request'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('critical_request') }}</strong>
										</span> 
									@endif
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer justify-content-between">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="button" id="support_save" class="btn btn-primary">Save</button>
				</div>
			  </div>
			  <!-- /.modal-content -->
			</div>
		<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- jQuery UI 1.11.4 -->
		<script src="{{URL::asset('public/js/moment.min.js')}}"></script>
		   
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script> 
		<script src="{{URL::asset('public/js/bootstrap.bundle.min.js')}}"></script>	
		<!-- Datatable  -->
		
		<!-- Select2 -->		
		<!--<script src="{{URL::asset('public/js/select2.full.min.js')}}"></script>	-->
		<!-- Select2 -->		
		<script src="{{URL::asset('public/js/select2.min.js')}}"></script>			
		<!-- daterangepicker -->
		
		<!-- Summernote -->
		<script src="{{URL::asset('public/js/summernote-bs4.min.js')}}"></script>
		
		
		<!-- Admin Theme App -->
		<script src="{{URL::asset('public/js/admintheme.min.js')}}"></script>
		
		<!-- Admin Theme dashboard demo (This is only for demo purposes) -->
	
		
		<script src="{{URL::asset('public/js/custom-form-validation.js')}}"></script>
		  
		
		<script type="text/javascript">
			var site_url = "<?php echo URL::to('/'); ?>"; 
			// Followup functionality removed
		</script>
		<!--<script async src="https://app.appzi.io/bootstrap/bundle.js?token=unZ6A"></script><div id="zbwid-3c79022e"></div>-->
		
		

		<script>
	
        
    $(function () {


        var date = new Date();
        date.setDate(date.getDate() - 0);
	$('#deptime').datetimepicker({autoclose: true,
            startDate: date, minDate:0,step: 15,
			onSelectDate:function(dateText, inst) {
				
				var date1 = new Date($('#deptime').val());
				var date2 = new Date($('#artime').val());
				
				calduration(date1,date2);
			},onSelectTime:function(dateText, inst){
				var date1 = new Date($('#deptime').val());
				var date2 = new Date($('#artime').val());
				
				calduration(date1,date2);
			}
			});
			$('#retartime').datetimepicker({autoclose: true,
            startDate: date, minDate:0,step: 15,
			onSelectDate:function(dateText, inst) {
				
				var date1 = new Date($('#deptime').val());
				var date2 = new Date($('#retartime').val());
				
				retcalduration(date1,date2);
			},onSelectTime:function(dateText, inst){
				var date1 = new Date($('#retdeptime').val());
				var date2 = new Date($('#retartime').val());
				
				retcalduration(date1,date2);
			}
			});
			$('#retdeptime').datetimepicker({autoclose: true,
            startDate: date, minDate:0,step: 15,
			onSelectDate:function(dateText, inst) {
				
				var date1 = new Date($('#retdeptime').val());
				var date2 = new Date($('#retartime').val());
				
				retcalduration(date1,date2);
			},onSelectTime:function(dateText, inst){
				var date1 = new Date($('#retdeptime').val());
				var date2 = new Date($('#retartime').val());
				
				retcalduration(date1,date2);
			}
			});
       
			$('#artime').datetimepicker({autoclose: true,
            startDate: date, minDate:0,step: 15,
			onSelectDate:function(dateText, inst) {
				
				var date1 = new Date($('#deptime').val());
				var date2 = new Date($('#artime').val());
				calduration(date1,date2);
			},onSelectTime:function(dateText, inst){
				var date1 = new Date($('#deptime').val());
				var date2 = new Date($('#artime').val());
				calduration(date1,date2);
			}
			
			}); 
			function append(dtTxt, ddTxt) {
				$('input[name="duration"]').val(dtTxt);
			}
			function retappend(dtTxt, ddTxt) {
				$('input[name="ret_duration"]').val(dtTxt);
			}
        function retcalduration(d1,d2){
			if(d2 != ''){
			 var msec = d2 - d1;
			var mins = Math.floor(msec / 60000);
			var hrs = Math.floor(mins / 60);
			var days = Math.floor(hrs / 24);
			var yrs = Math.floor(days / 365);
			mins = mins % 60;
			retappend(hrs + "h " + mins + "m");
			}
		}function calduration(d1,d2){
			if(d2 != ''){
			 var msec = d2 - d1;
			var mins = Math.floor(msec / 60000);
			var hrs = Math.floor(mins / 60);
			var days = Math.floor(hrs / 24);
			var yrs = Math.floor(days / 365);
			mins = mins % 60;
			append(hrs + "h " + mins + "m");
			}
		}
		 $('#ardate').datetimepicker({autoclose: true,
            startDate: date, minDate:0,timepicker: false,format:'Y-m-d'});
	 //Initialize Select2 Elements
		$('.select2_name, .select2_source, .select2_destination').select2({
		  theme: 'bootstrap4'
		}); 
		// Summernote
		$('.textarea').summernote();
			
    }); 

		</script>   
									
	</body>
</html> 