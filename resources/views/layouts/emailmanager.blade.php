<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="keyword" content="E-Weblink CRM">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" type="image/png" href="{{ asset('img/favicon.png') }}"/>
	<title>Immigration | @yield('title')</title>

	<link rel="stylesheet" href="{{asset('css/app.min.css')}}">
	 <link rel="stylesheet" href="{{asset('css/fullcalendar.min.css')}}">
	<link rel="stylesheet" href="{{asset('css/summernote-bs4.css')}}">
	<link rel="stylesheet" href="{{asset('css/daterangepicker.css')}}">
	<link rel="stylesheet" href="{{asset('css/bootstrap-timepicker.min.css')}}">
	<link rel="stylesheet" href="{{asset('css/select2.min.css')}}">
	<!-- Template CSS -->
	<link rel="stylesheet" href="{{asset('css/niceCountryInput.css')}}">
	<!--<link rel="stylesheet" href="{{asset('css/flagstrap.css')}}">-->
	<link rel="stylesheet" href="{{asset('css/bootstrap-formhelpers.min.css')}}">
	<link rel="stylesheet" href="{{asset('css/intlTelInput.css')}}">
	<link rel="stylesheet" href="{{asset('css/style.css')}}">
	<link rel="stylesheet" href="{{asset('css/components.css')}}">
	<!-- Custom style CSS -->
	<link rel="stylesheet" href="{{asset('css/custom.css')}}">
	   <style>
.dropbtn {
  background-color: transparent;
 border:0;
}

.dropbtn:hover, .dropbtn:focus {
  background-color: transparent;
   border:0;
}

.mydropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 160px;
  overflow: auto;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.mydropdown a:hover {background-color: #ddd;}

.show {display: block;}
</style>
</head>
<body>
	<div class="loader"></div>
		<div class="popuploader" style="display: none;"></div>
	<div id="app">
		<div class="main-wrapper main-wrapper-1">
			<div class="navbar-bg"></div>

			<!--Header-->
			@include('../Elements/Emailuser/header')

            <?php
            use Illuminate\Support\Facades\DB;
            $email_users_info = DB::connection('second_db')->table('email_users')->where('user_type', 2)->get();
            //dd($email_users_info);
            ?>

            <!--Left Side Bar-->
			@include('../Elements/Emailuser/left-side-bar',['email_users_info' => $email_users_info] )

			@yield('content')

			@include('../Elements/Emailuser/footer')
		</div>
	</div>
	<script>var site_url = '{{URL::to('/')}}';</script>
	<!--<script src="{{asset('js/niceCountryInput.js')}}"></script> -->
	<script src="{{asset('js/app.min.js')}}"></script>
	<script src="{{asset('js/fullcalendar.min.js')}}"></script>
	<script src="{{asset('js/chart.min.js')}}"></script>
	<script src="{{asset('js/datatables.min.js')}}"></script>
	<script src="{{asset('js/dataTables.bootstrap4.js')}}"></script>
	 <!-- JS Libraies -->
	<!--<script src="{{asset('js/apexcharts.min.js')}}"></script>-->
	<!-- Page Specific JS File -->
	<!--<script src="{{asset('js/index.js')}}"></script> -->
	<script src="{{asset('js/summernote-bs4.js')}}"></script>
	<script src="{{asset('js/daterangepicker.js')}}"></script>
	<script src="{{asset('js/bootstrap-timepicker.min.js')}}"></script>

	<script src="{{asset('js/select2.full.min.js')}}"></script>
	<!--<script src="{{asset('js/jquery.flagstrap.js')}}"></script>-->
	<script src="{{asset('js/bootstrap-formhelpers.min.js')}}"></script>
	<script src="{{asset('js/intlTelInput.js')}}"></script>
	<script src="{{asset('js/agent-custom-form-validation.js')}}"></script>
	<!-- Template JS File -->
	<script src="{{asset('js/scripts.js')}}"></script>

	<!-- Custom JS File -->
	<script src="{{asset('js/custom.js')}}"></script>
	<script>
		$(document).ready(function () {
			$('.js-data-example-ajaxccsearch').select2({

		 closeOnSelect: true,

		  ajax: {
			url: '{{URL::to('/admin/clients/get-allclients')}}',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };

			},
			 cache: true

		  },
	templateResult: formatRepomain,
	templateSelection: formatRepoSelectionmain
});
function formatRepomain (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div dataid="+repo.cid+" class='selectclient select2-result-repository ag-flex ag-space-between ag-align-center')'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +

        "<span class='ui label yellow select2-result-repository__statistics'>" +

        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);

  return $container;
}

function formatRepoSelectionmain (repo) {
  return repo.name || repo.text;
}

 $('.js-data-example-ajaxccsearch').on('change', function () {
         var v = $(this).val();
         var s = v.split('/');
         if(s[1] == 'Client'){
              window.location = '{{URL::to('/admin/clients/detail/')}}/'+s[0]; // redirect
         }else{
              window.location = '{{URL::to('/admin/leads/history/')}}/'+s[0]; // redirect
         }

          return false;
      });


$(document).delegate('.opencheckin', 'click', function(){

	$('#checkinmodal').modal('show');
});
	$(document).delegate('.visitpurpose', 'blur', function(){
		var visitpurpose = $(this).val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_visit_purpose',
			type:'POST',
			data:{id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				 $.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});

			}
		});
	});

	$(document).delegate('.savevisitcomment', 'click', function(){
		var visitcomment = $('.visit_comment').val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_visit_comment',
			type:'POST',
			data:{id: appliid,visit_comment:visitcomment},
			success: function(responses){
				// $('.popuploader').hide();
				$('.visit_comment').val('');
				$.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});
			}
		});
	});

	$(document).delegate('.attendsession', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/attend_session',
			type:'POST',
			data:{id: appliid,waitcountdata: $('#waitcountdata').val()},
			success: function(response){

				 var obj = $.parseJSON(response);
				if(obj.status){
					$.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});
					$('.checindata #id_'+appliid).remove();
				}else{
					alert(obj.message);
				}
			}
		});
	});

	$(document).delegate('.completesession', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/complete_session',
			type:'POST',
			data:{id: appliid,attendcountdata: $('#attendcountdata').val()},
			success: function(response){

				 var obj = $.parseJSON(response);
				if(obj.status){
					$.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});
					$('.checindata #id_'+appliid).remove();
				}else{
					alert(obj.message);
				}
			}
		});
	});
	$(document).delegate('.opencheckindetail', 'click', function(){
		$('#checkindetailmodal').modal('show');
		$('.popuploader').show();
		var appliid = $(this).attr('id');
		$.ajax({
				url: site_url+'/admin/get-checkin-detail',
				type:'GET',
				data:{id: appliid},
				success: function(responses){
					 $('.popuploader').hide();
					$('.showchecindetail').html(responses);
				}
			});
	});
			/* $(".niceCountryInputSelector").each(function(i,e){
				new NiceCountryInput(e).init();
			}); */
			//$('.country_input').flagStrap();
			$(".telephone").intlTelInput();
			$('.drop_table_data button').on('click', function(){
				$('.client_dropdown_list').toggleClass('active');
			});
			$('.client_dropdown_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.client_table_data table tr td').show();
				$('.client_table_data table tr th').show();
				$('.client_dropdown_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.client_dropdown_list label.dropdown-option input').prop('checked', false);
					$('.client_table_data table tr td').hide();
					$('.client_table_data table tr th').hide();
					$('.client_table_data table tr td:nth-child(1)').show();
					$('.client_table_data table tr th:nth-child(1)').show();
					$('.client_table_data table tr td:nth-child(2)').show();
					$('.client_table_data table tr th:nth-child(2)').show();
					$('.client_table_data table tr td:nth-child(17)').show();
					$('.client_table_data table tr th:nth-child(17)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.client_table_data table tr td:nth-child('+val+')').show();
					$('.client_table_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.client_dropdown_list label.dropdown-option.all input').prop('checked', false);
						$('.client_table_data table tr td:nth-child('+val+')').hide();
						$('.client_table_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('.drop_table_data button').on('click', function(){
				$('.client_report_list').toggleClass('active');
			});
			$('.client_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.client_report_data table tr td').show();
				$('.client_report_data table tr th').show();
				$('.client_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.client_report_list label.dropdown-option input').prop('checked', false);
					$('.client_report_data table tr td').hide();
					$('.client_report_data table tr th').hide();
					$('.client_report_data table tr td:nth-child(1)').show();
					$('.client_report_data table tr th:nth-child(1)').show();
					$('.client_report_data table tr td:nth-child(2)').show();
					$('.client_report_data table tr th:nth-child(2)').show();
					$('.client_report_data table tr td:nth-child(11)').show();
					$('.client_report_data table tr th:nth-child(11)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.client_report_data table tr td:nth-child('+val+')').show();
					$('.client_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.client_report_list label.dropdown-option.all input').prop('checked', false);
						$('.client_report_data table tr td:nth-child('+val+')').hide();
						$('.client_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('.drop_table_data button').on('click', function(){
				$('.application_report_list').toggleClass('active');
			});
			$('.application_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.application_report_data table tr td').show();
				$('.application_report_data table tr th').show();
				$('.application_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.application_report_list label.dropdown-option input').prop('checked', false);
					$('.application_report_data table tr td').hide();
					$('.application_report_data table tr th').hide();
					$('.application_report_data table tr td:nth-child(1)').show();
					$('.application_report_data table tr th:nth-child(1)').show();
					$('.application_report_data table tr td:nth-child(2)').show();
					$('.application_report_data table tr th:nth-child(2)').show();
					$('.application_report_data table tr td:nth-child(3)').show();
					$('.application_report_data table tr th:nth-child(3)').show();
					$('.application_report_data table tr td:nth-child(5)').show();
					$('.application_report_data table tr th:nth-child(5)').show();
					$('.application_report_data table tr td:nth-child(7)').show();
					$('.application_report_data table tr th:nth-child(7)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.application_report_data table tr td:nth-child('+val+')').show();
					$('.application_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.application_report_list label.dropdown-option.all input').prop('checked', false);
						$('.application_report_data table tr td:nth-child('+val+')').hide();
						$('.application_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('.drop_table_data button').on('click', function(){
				$('.officevisit_report_list').toggleClass('active');
			});
			$('.officevisit_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.officevisit_report_data table tr td').show();
				$('.officevisit_report_data table tr th').show();
				$('.officevisit_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.officevisit_report_list label.dropdown-option input').prop('checked', false);
					$('.officevisit_report_data table tr td').hide();
					$('.officevisit_report_data table tr th').hide();
					$('.officevisit_report_data table tr td:nth-child(1)').show();
					$('.officevisit_report_data table tr th:nth-child(1)').show();
					$('.officevisit_report_data table tr td:nth-child(2)').show();
					$('.officevisit_report_data table tr th:nth-child(2)').show();
					$('.officevisit_report_data table tr td:nth-child(4)').show();
					$('.officevisit_report_data table tr th:nth-child(4)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.officevisit_report_data table tr td:nth-child('+val+')').show();
					$('.officevisit_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.officevisit_report_list label.dropdown-option.all input').prop('checked', false);
						$('.officevisit_report_data table tr td:nth-child('+val+')').hide();
						$('.officevisit_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('.drop_table_data button').on('click', function(){
				$('.invoice_report_list').toggleClass('active');
			});
			$('.invoice_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.invoice_report_data table tr td').show();
				$('.invoice_report_data table tr th').show();
				$('.invoice_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.invoice_report_list label.dropdown-option input').prop('checked', false);
					$('.invoice_report_data table tr td').hide();
					$('.invoice_report_data table tr th').hide();
					$('.invoice_report_data table tr td:nth-child(1)').show();
					$('.invoice_report_data table tr th:nth-child(1)').show();
					$('.invoice_report_data table tr td:nth-child(2)').show();
					$('.invoice_report_data table tr th:nth-child(2)').show();
					$('.invoice_report_data table tr td:nth-child(4)').show();
					$('.invoice_report_data table tr th:nth-child(4)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.invoice_report_data table tr td:nth-child('+val+')').show();
					$('.invoice_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.invoice_report_list label.dropdown-option.all input').prop('checked', false);
						$('.invoice_report_data table tr td:nth-child('+val+')').hide();
						$('.invoice_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('.drop_table_data button').on('click', function(){
				$('.saleforecast_applic_report_list').toggleClass('active');
			});
			$('.saleforecast_applic_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.saleforecast_application_report_data table tr td').show();
				$('.saleforecast_application_report_data table tr th').show();
				$('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', false);
					$('.saleforecast_application_report_data table tr td').hide();
					$('.saleforecast_application_report_data table tr th').hide();
					$('.saleforecast_application_report_data table tr td:nth-child(1)').show();
					$('.saleforecast_application_report_data table tr th:nth-child(1)').show();
					$('.saleforecast_application_report_data table tr td:nth-child(2)').show();
					$('.saleforecast_application_report_data table tr th:nth-child(2)').show();
					$('.saleforecast_application_report_data table tr td:nth-child(4)').show();
					$('.saleforecast_application_report_data table tr th:nth-child(4)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.saleforecast_application_report_data table tr td:nth-child('+val+')').show();
					$('.saleforecast_application_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.saleforecast_applic_report_list label.dropdown-option.all input').prop('checked', false);
						$('.saleforecast_application_report_data table tr td:nth-child('+val+')').hide();
						$('.saleforecast_application_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('.drop_table_data button').on('click', function(){
				$('.interest_service_report_list').toggleClass('active');
			});
			$('.interest_service_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.interest_service_report_data table tr td').show();
				$('.interest_service_report_data table tr th').show();
				$('.interest_service_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.interest_service_report_list label.dropdown-option input').prop('checked', false);
					$('.interest_service_report_data table tr td').hide();
					$('.interest_service_report_data table tr th').hide();
					$('.interest_service_report_data table tr td:nth-child(1)').show();
					$('.interest_service_report_data table tr th:nth-child(1)').show();
					$('.interest_service_report_data table tr td:nth-child(2)').show();
					$('.interest_service_report_data table tr th:nth-child(2)').show();
					$('.interest_service_report_data table tr td:nth-child(10)').show();
					$('.interest_service_report_data table tr th:nth-child(10)').show();
					$('.interest_service_report_data table tr td:nth-child(14)').show();
					$('.interest_service_report_data table tr th:nth-child(14)').show();
				}

			}else{

			 if ($(this).is(":checked")) {
					$('.interest_service_report_data table tr td:nth-child('+val+')').show();
					$('.interest_service_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.interest_service_report_list label.dropdown-option.all input').prop('checked', false);
						$('.interest_service_report_data table tr td:nth-child('+val+')').hide();
						$('.interest_service_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});

			$('#personal_details .is_business').hide();
			$('#office_income_share .is_super_agent').hide();
			$('#office_income_share .is_sub_agent').hide();

			$('.modal-body form#addgroupinvoice .is_superagentinv').hide();

			$('#agentstructure input[name="struture"]').on('change', function(){
				var id = $(this).attr('id');
				if(id == 'individual'){
					$('#personal_details .is_business').hide();
					$('#personal_details .is_individual').show();
					$('#personal_details .is_business input').attr('data-valid', '');
					$('#personal_details .is_individual input').attr('data-valid', 'required');
				}
				else{
					$('#personal_details .is_individual').hide();
					$('#personal_details .is_business').show();
					$('#personal_details .is_business input').attr('data-valid', 'required');
					$('#personal_details .is_individual input').attr('data-valid', '');
				}
			});
			$('.modal-body form#addgroupinvoice input[name="partner_type"]').on('change', function(){
				var invid = $(this).attr('id');
				if(invid == 'superagent_inv'){
					$('.modal-body form#addgroupinvoice .is_partnerinv').hide();
					$('.modal-body form#addgroupinvoice .is_superagentinv').show();
					$('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', '');
					$('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', 'required');
				}
				else{
					$('.modal-body form#addgroupinvoice .is_superagentinv').hide();
					$('.modal-body form#addgroupinvoice .is_partnerinv').show();
					$('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', 'required');
					$('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', '');
				}
			});
			$('.modal .modal-body .is_partner').hide();
			$('.modal .modal-body .is_application').hide();
			$('.modal .modal-body input[name="related_to"]').on('change', function(){
				var relid = $(this).attr('id');
				if(relid == 'contact'){
					$('.modal .modal-body .is_partner').hide();
					$('.modal .modal-body .is_application').hide();
					$('.modal .modal-body .is_contact').show();
					$('.modal .modal-body .is_partner select').attr('data-valid', '');
					$('.modal .modal-body .is_application select').attr('data-valid', '');
					$('.modal .modal-body .is_contact select').attr('data-valid', 'required');
				}
				else if(relid == 'partner'){
					$('.modal .modal-body .is_contact').hide();
					$('.modal .modal-body .is_application').hide();
					$('.modal .modal-body .is_partner').show();
					$('.modal .modal-body .is_contact select').attr('data-valid', '');
					$('.modal .modal-body .is_application select').attr('data-valid', '');
					$('.modal .modal-body .is_partner select').attr('data-valid', 'required');
				}
				else if(relid == 'application'){
					$('.modal .modal-body .is_contact').hide();
					$('.modal .modal-body .is_partner').hide();
					$('.modal .modal-body .is_application').show();
					$('.modal .modal-body .is_contact select').attr('data-valid', '');
					$('.modal .modal-body .is_partner select').attr('data-valid', '');
					$('.modal .modal-body .is_application select').attr('data-valid', 'required');
				}
				else{
					$('.modal .modal-body .is_contact').hide();
					$('.modal .modal-body .is_partner').hide();
					$('.modal .modal-body .is_application').hide();
					$('.modal .modal-body .is_contact input').attr('data-valid', '');
					$('.modal .modal-body .is_partner input').attr('data-valid', '');
					$('.modal .modal-body .is_application input').attr('data-valid', '');
				}
			});
			$('#agenttype input#super_agent').on('click', function(){
				if ($(this).is(":checked")) {
					$('#office_income_share .is_super_agent').show();
				}
				else{
					$('#office_income_share .is_super_agent').hide();
				}
			});
			$('#agenttype input#sub_agent').on('click', function(){
				if ($(this).is(":checked")) {
					$('#office_income_share .is_sub_agent').show();
				}
				else{
					$('#office_income_share .is_sub_agent').hide();
				}
			});

			$('#internal select[name="source"]').on('change', function(){
				var sourceval = $(this).val();
				if(sourceval == 'Sub Agent'){
					$('#internal .is_subagent').show();
					$('#internal .is_subagent select').attr('data-valid', 'required');
				}
				else{
					$('#internal .is_subagent').hide();
					$('#internal .is_subagent select').attr('data-valid', '');
				}
			});

			$('.card .card-body .grid_data').hide();
			$('.card .card-body .document_layout_type a.list').on('click', function(){
				$('.card .card-body .document_layout_type a').removeClass('active');
				$(this).addClass('active');
				$('.card .card-body .grid_data').hide();
				$('.card .card-body .list_data').show();
			});
			$('.card .card-body .document_layout_type a.grid').on('click', function(){
				$('.card .card-body .document_layout_type a').removeClass('active');
				$(this).addClass('active');
				$('.card .card-body .list_data').hide();
				$('.card .card-body .grid_data').show();
			});



			$('.js-data-example-ajax-check').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#checkinmodal'),
		  ajax: {
			url: '{{URL::to('/admin/clients/get-recipients')}}',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };

			},
			 cache: true

		  },
	templateResult: formatRepocheck,
	templateSelection: formatRepoSelectioncheck
});
function formatRepocheck (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +

        "<span class='ui label yellow select2-result-repository__statistics'>" +

        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);

  return $container;
}

function formatRepoSelectioncheck (repo) {
  return repo.name || repo.text;
}

		/* $('.timepicker').timepicker({
			minuteStep: 1,
			showSeconds: true,
		}); */
	});



	</script>

	<div id="checkinmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Create In Person Client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="checkinmodalsave" id="checkinmodalsave" action="{{URL::to('/admin/checkin')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Search Contact <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax-check" name="contact"></select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Office <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control" name="office">
									<option value="">Select</option>
									@foreach(\App\Models\Branch::all() as $of)
										<option value="{{$of->id}}">{{$of->office_name}}</option>
									@endforeach
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Visit Purpose <span class="span_req">*</span></label>
								<textarea class="form-control" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Select In Person Assignee <span class="span_req">*</span></label>
								<?php
								$assignee = \App\Models\Admin::where('role',1)->get();
								?>
								<select class="form-control" name="assignee">
								@foreach($assignee as $assigne)
									<option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
								@endforeach
								</select>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('checkinmodalsave')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="checkindetailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">In Person Details</h5>
				<a style="margin-left:10px;" href="javascript:;"><i class="fa fa-trash"></i> Archive</a>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showchecindetail">

			</div>
		</div>
	</div>
</div>
@stack('scripts')

</body>
</html>
