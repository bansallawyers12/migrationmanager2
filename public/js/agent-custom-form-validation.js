var requiredError = 'This field is required.';
var emailError = "Please enter the valid email address.";
var captcha = "Captcha invalid.";
var maxError = "Number should be less than or equal to ";
var min = "This field should be greater than or equal to ";
var max = "This field should be less than or equal to ";
var equal = "This field should be equal to ";

function customValidate(formName, savetype = '')
	{
		$(".popuploader").show(); //all form submit
		
		var i = 0;	
		$(".custom-error").remove(); //remove all errors when submit the button
		
		$("form[name="+formName+"] :input[data-valid]").each(function(){
			var dataValidation = $(this).attr('data-valid');
			var splitDataValidation = dataValidation.split(' ');
			
			var j = 0; //for serial wise errors shown	
			if($.inArray("required", splitDataValidation) !== -1) //for required
				{
					var for_class = $(this).attr('class');	
					if(for_class.indexOf('multiselect_subject') != -1)
						{
							var value = $.trim($(this).val());	
							if (value.length === 0) 
								{
									i++;
									j++;
									$(this).parent().after(errorDisplay(requiredError)); 
								}	
						} 
					else 
						{
							if( !$.trim($(this).val()) ) 
								{
									i++;
									j++;
									$(this).after(errorDisplay(requiredError));  
								}
						}
				}
			if(j <= 0)
				{
					if($.inArray("email", splitDataValidation) !== -1) //for email
						{
							if(!validateEmail($.trim($(this).val()))) 
								{
									i++;
									$(this).after(errorDisplay(emailError));  
								}
						}
						
							
					var forMin = splitDataValidation.find(a =>a.includes("min"));
					if(typeof forMin != 'undefined')
						{
							var breakMin = forMin.split('-');
							var digit = breakMin[1];

							var value = $.trim($(this).val()).length;
							if(value < digit) 
								{
									i++;
									$(this).after(errorDisplay(min+' '+digit+' character.'));  
								}	
						}
						
					var forMax = splitDataValidation.find(a =>a.includes("max"));
					if(typeof forMax != 'undefined')
						{
							var breakMax = forMax.split('-');
							var digit = breakMax[1];

							var value = $.trim($(this).val()).length;
							if(value > digit) 
								{
									i++;
									$(this).after(errorDisplay(max+' '+digit+' character.'));  
								}	
						}
						
					var forEqual = splitDataValidation.find(a =>a.includes("equal"));
					if(typeof forEqual != 'undefined')
						{
							var breakEqual = forEqual.split('-');
							var digit = breakEqual[1];

							var value = ($.trim($(this).val()).replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-')).length;
							if(value != digit) 
								{
									i++;
									$(this).after(errorDisplay(equal+' '+digit+' character.'));  
								}	
						}
				}			
		});
		
		if(i > 0)
			{
				
				if(formName == 'add-query'){
					$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
				}else if(formName != 'upload-answer')	{
					$('html, body').animate({scrollTop:0}, 'slow');
				}
				$(".popuploader").hide();
				return false;
			}	
		else
			{
				if(formName == 'add-query')
					{
						$('#preloader').show();
						$('#preloader div').show();
						var myform = document.getElementById('enquiryco');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#preloader').hide();
								$('#preloader div').hide();
								var obj = $.parseJSON(response);
								if(obj.success){
									window.location = redirecturl;
								}else{
									$('.customerror').html(obj.message);
									$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
								}
							}
						});
					}else if(formName == 'queryform')
					{
						$('#preloader').show();
						$('#preloader div').show();
						var myform = document.getElementById('popenquiryco');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#preloader').hide();
								$('#preloader div').hide();
								var obj = $.parseJSON(response);
								if(obj.success){
									window.location = redirecturl;
								}else{
									$('.customerror').html(obj.message);
									
								}
							}
						});
					}else if(formName == 'add-note')
					{   
						var myform = document.getElementById('addnoteform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.success){
									$('#myAddnotes .modal-title').html('');
									$('#myAddnotes #note_type').html('');
									$('#myAddnotes').modal('hide');
									myfollowuplist(obj.leadid);
								}else{
									$('#myAddnotes .customerror').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'appnotetermform')
					{   
				var noteid = $('#appnotetermform input[name="noteid"]').val();
						var myform = document.getElementById('appnotetermform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
									$('.popuploader').hide();
								var obj = $.parseJSON(response);
								if(obj.status){
									$('#create_applicationnote').modal('hide');
									$.ajax({
										url: site_url+'/agent/get-applications-logs',
										type:'GET',
										data:{id: noteid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
								}else{
									$('#create_applicationnote .customerror').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'clientnotetermform')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('clientnotetermform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
							if(obj.status){
								$('#create_note').modal('hide');
							$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
							$.ajax({
								url: site_url+'/agent/get-notes',
								type:'GET',
								data:{clientid:client_id,type:'partner'},
								success: function(responses){
									
									$('.note_term_list').html(responses);
								}
							});
								// Refresh activity feed
								if (typeof getallactivities === 'function') {
									getallactivities(client_id);
								} else if (typeof loadActivities === 'function') {
									loadActivities();
								}
							}else{
								$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								
							}
							}
						});
					}else if(formName == 'clientcontact')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('clientcontact');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#add_clientcontact').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
									url: site_url+'/agent/get-contacts',
									type:'GET',
									data:{clientid:client_id,type:'partner'},
									success: function(responses){
										
										$('.contact_term_list').html(responses);
									}
								});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'clientbranch')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('clientbranch');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#add_clientbranch').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
									url: site_url+'/agent/get-branches',
									type:'GET',
									data:{clientid:client_id,type:'partner'},
									success: function(responses){
										
										$('.branch_term_list').html(responses);
									}
								});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}
					// Old taskform removed - Task table/model no longer existselse if(formName == 'tasktermclientform'){
						var client_id = $('#tasktermclientform input[name="partnerid"]').val();
						var myform = document.getElementById('tasktermclientform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#opentaskmodal').modal('hide');
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/partner/get-tasks',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 $('#my-datatable').DataTable().destroy();
											$('.taskdata').html(responses);
											$('#my-datatable').DataTable({
												"searching": false,
												"lengthChange": false,
											  "columnDefs": [
												{ "sortable": false, "targets": [0, 2, 3] }
											  ],
											  order: [[1, "desc"]] //column indexes is zero based

												
											}).draw();
											
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					// educationform validation REMOVED - form deleted in Phase 1 (education system deprecated)
					}else if(formName == 'feeform'){
						var product_id = $('#feeform input[name="product_id"]').val();
						var myform = document.getElementById('feeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#new_fee_option').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-all-fees',
										type:'GET',
										data:{clientid:product_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.feeslist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					// Application fee form has been removed
					}else if(formName == 'applicationfeeform'){
						$('.custom-error-msg').html('<span class="alert alert-danger">Application fee options feature has been removed.</span>');
									
								}
							}
						});		
					}else if(formName == 'servicefeeform'){
						
						var myform = document.getElementById('servicefeeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
									$('#new_fee_option_serv').modal('hide');
									
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#interest_service_view').modal('show');
									$(document).on("hidden.bs.modal", "#new_fee_option_serv", function (e) {
										$('body').addClass('modal-open');
									});
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.productfeedata .installtype').html(obj.installment_type);
									$('.productfeedata .feedata').html(obj.feedata);
								
									$('.productfeedata .client_dicounts').html(obj.discount);
									var t = parseFloat(obj.totalfee) - parseFloat(obj.discount);
									$('.productfeedata .client_totl').html(t);
									$.ajax({
										url: site_url+'/agent/get-services',
										type:'GET',
										data:{clientid:obj.client_id},
										success: function(responses){
											$('.popuploader').hide(); 
											$('.interest_serv_list').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'setuppaymentschedule'){
						
						var myform = document.getElementById('setuppaymentschedule');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
									$('#create_apppaymentschedule').modal('hide');
								var obj = $.parseJSON(response);
							
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.openpaymentschedule').hide();
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editinvpaymentschedule'){
						if($('#editinvpaymentschedule input[name="is_ajax"]').val() == 'true'){
						var myform = document.getElementById('editinvpaymentschedule');
						var appliid = $('#editinvpaymentschedule input[name="id"]').val();
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#editpaymentschedule').modal('hide');
								var obj = $.parseJSON(response);
								
									if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-all-paymentschedules',
										type:'GET',
										data:{appid:obj.application_id, client_id:obj.client_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.showpaymentscheduledata').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});	
						}						
					}else if(formName == 'checklistform'){
						
						var myform = document.getElementById('checklistform');
						var checklist_type = $('#checklist_type').val();
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#create_checklist').modal('hide');
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
									if(obj.status){
									$('#document_type').val();
									$('#checklistdesc').val();
									$('.due_date_col').hide();
									$('.checklistdue_date').val(0);
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.'+checklist_type+'_checklists').html(obj.data);
									$('.checklistcount').html(obj.countchecklist);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});	
											
					}else if(formName == 'addinvpaymentschedule'){
						
						var myform = document.getElementById('addinvpaymentschedule');
			
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#addpaymentschedule').modal('hide');
								var obj = $.parseJSON(response);
								
									if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-all-paymentschedules',
										type:'GET',
										data:{appid:obj.application_id, client_id:obj.client_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.showpaymentscheduledata').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});	
										
					}else if(formName == 'editfeeform'){
						var product_id = $('#editfeeform input[name="product_id"]').val();
						var myform = document.getElementById('editfeeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#editfeeoption').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-all-fees',
										type:'GET',
										data:{clientid:product_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.feeslist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'promotionform'){
						var client_id = $('#promotionform input[name="client_id"]').val();
						var myform = document.getElementById('promotionform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#create_promotion').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-promotions',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 
											$('.promotionlists').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editpromotionform'){
						var client_id = $('#editpromotionform input[name="client_id"]').val();
						var myform = document.getElementById('editpromotionform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#edit_promotion').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-promotions',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 
											$('.promotionlists').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'saveacademic'){
						var client_id = $('#saveacademic input[name="client_id"]').val();
						var myform = document.getElementById('saveacademic');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#add_academic_requirement').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.education_list').html(obj.data);
									$('.editacademic').attr('data-academic_score_per', obj.requirment.academic_score_per);
									$('.editacademic').attr('data-academic_score_type', obj.requirment.academic_score_type);
									$('.editacademic').attr('data-degree', obj.requirment.degree);
									$('.editacademic').show();
									$('.add_academic_requirement').hide();
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					// editeducationform validation REMOVED - form deleted in Phase 1 (education system deprecated)
					}else if(formName == 'testscoreform'){
						var client_id = $('#testscoreform input[name="client_id"]').val();
						var myform = document.getElementById('testscoreform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('.edit_english_test').modal('hide');
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$('.tofl_lis').html(obj.toefl_Listening);	
								$('.tofl_reading').html(obj.toefl_Reading);	
								$('.tofl_writing').html(obj.toefl_Writing);	
								$('.tofl_speaking').html(obj.toefl_Speaking);
								$('.tofl_score').html(obj.score_1);	
								$('.ilets_Listening').html(obj.ilets_Listening);
								$('.ilets_Reading').html(obj.ilets_Reading);
								$('.ilets_Writing').html(obj.ilets_Writing);
								$('.ilets_speaking').html(obj.ilets_Speaking);	
								$('.ilets_score').html(obj.score_2);	
								$('.pte_Listening').html(obj.pte_Listening);
								$('.pte_Reading').html(obj.pte_Reading);	
								$('.pte_Writing').html(obj.pte_Writing);
								$('.pte_Speaking').html(obj.pte_Speaking);	
								$('.pte_score').html(obj.score_3);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'saveagreement'){
						
						var myform = document.getElementById('saveagreement');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.status){
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'savesubjectarea'){
						
						var myform = document.getElementById('savesubjectarea');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.status){
									$('#other_info_add').modal('hide');
									$('.otherinfolist').html(obj.data);
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editsubjectarea'){
						
						var myform = document.getElementById('editsubjectarea');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.status){
									$('#other_info_edit').modal('hide');
									$('.otherinfolist').html(obj.data);
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'othertestform'){
						var client_id = $('#othertestform input[name="client_id"]').val();
						var myform = document.getElementById('othertestform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('.edit_other_test').modal('hide');
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$('.gmat').html(obj.gmat);	
								$('.gre').html(obj.gre);	
								$('.sat_ii').html(obj.sat_ii);	
								$('.sat_i').html(obj.sat_i);
								
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'ajaxinvoicepaymentform'){
							var client_id = $('#ajaxinvoicepaymentform input[name="client_id"]').val();
						var myform = document.getElementById('ajaxinvoicepaymentform');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#addpaymentmodal').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $.ajax({
										url: site_url+'/agent/get-invoices',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
												 $('.invoicetable').DataTable().destroy();
													$('.invoicedatalist').html(responses);
												$('.invoicetable').DataTable({
													"searching": false,
													"lengthChange": false,
												  "columnDefs": [
													{ "sortable": false, "targets": [0, 2, 3] }
												  ],
												  order: [[1, "desc"]] //column indexes is zero based

													
												}).draw();
											
										}
									}); 
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					
					}else if(formName == 'discontinue_application'){
							var client_id = $('#discontinue_application input[name="client_id"]').val();
						var myform = document.getElementById('discontinue_application');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#discon_application').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $('.ifdiscont').hide();
									 $('.revertapp').show();
									 $('.applicationstatus').html('Discontinued');
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					
					}else if(formName == 'revertapplication'){
						var appliid = $('#revertapplication input[name="revapp_id"]').val();	
						var myform = document.getElementById('revertapplication');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#revert_application').modal('hide');
								if(obj.status){
									
									$.ajax({
										url: site_url+'/agent/get-applications-logs',
										type:'GET',
										data:{id: appliid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								
								$('.progress-circle span').html(obj.width+' %');
				var over = '';
				if(obj.width > 50){
					over = '50';
				}
				$("#progresscir").removeClass();
				$("#progresscir").addClass('progress-circle');
				$("#progresscir").addClass('prgs_'+obj.width);
				$("#progresscir").addClass('over_'+over); 
									 $('.ifdiscont').show();
									$('.completestage').show();
									 $('.nextstage').hide();
									 $('.revertapp').hide();
									 $('.applicationstatus').html('In Progress');
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					
					}else if(formName == 'spagent_application'){
							
						var myform = document.getElementById('spagent_application');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#superagent_application').modal('hide');
								if(obj.status){
									$('#super_agent').val('');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 
								$('.supagent_data').html(obj.data);	
								}else{
									alert(obj.message);
								}
							}
						});
					
					}else if(formName == 'sbagent_application'){
							
						var myform = document.getElementById('sbagent_application');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#subagent_application').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 
									$('.subagent_data').html(obj.data);	
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'xapplication_ownership'){
							
						var myform = document.getElementById('xapplication_ownership');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#application_ownership').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 
									$('.application_ownership').attr('data-ration',obj.ratio);	
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'saleforcast'){
							
						var myform = document.getElementById('saleforcast');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#application_opensaleforcast').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $('.opensaleforcast').attr('data-client_revenue',obj.client_revenue);	
									 $('.opensaleforcast').attr('data-partner_revenue',obj.partner_revenue);	
									 $('.opensaleforcast').attr('data-discounts',obj.discounts);	
									 
									$('.appsaleforcast .client_revenue').html(obj.client_revenue);	
									$('.appsaleforcast .partner_revenue').html(obj.partner_revenue);	
									$('.appsaleforcast .discounts').html(obj.discounts);	
									var t = parseFloat(obj.client_revenue) + parseFloat(obj.partner_revenue) - parseFloat(obj.discounts);
									$('.appsaleforcast .netrevenue').html(t);	
									$('.app_sale_forcast').html(t+ 'AUD');	
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'saleforcastservice'){
						var myform = document.getElementById('saleforcastservice');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								
								var obj = $.parseJSON(response);
								$('#application_opensaleforcastservice').modal('hide');
								if(obj.status){
									$('#interest_service_view').modal('show');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $('.opensaleforcastservice').attr('data-client_revenue',obj.client_revenue);	
									 $('.opensaleforcastservice').attr('data-partner_revenue',obj.partner_revenue);	
									 $('.opensaleforcastservice').attr('data-discounts',obj.discounts);	
									 
									$('.appsaleforcastserv .client_revenue').html(obj.client_revenue);	
									$('.appsaleforcastserv .partner_revenue').html(obj.partner_revenue);	
									$('.appsaleforcastserv .discounts').html(obj.discounts);	
									var t = parseFloat(obj.client_revenue) + parseFloat(obj.partner_revenue) - parseFloat(obj.discounts);
									$('.appsaleforcastserv .netrevenue').html(t);	
									$.ajax({
										url: site_url+'/agent/get-services',
										type:'GET',
										data:{clientid:obj.client_id},
										success: function(responses){
											$('.popuploader').hide(); 
											$('.interest_serv_list').html(responses);
										}
									});
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'inter_servform'){
						var client_id = $('#inter_servform input[name="client_id"]').val();
						var myform = document.getElementById('inter_servform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('.add_interested_service').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-services',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.interest_serv_list').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/agent/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
										success: function(responses){
											var ress = JSON.parse(responses);
											var html = '';
											$.each(ress.data, function(k, v) {
												html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
												if(v.message != null){
													html += '<p>'+v.message+'</p>';
												}
												html += '</div></div>';
											});
											$('.activities').html(html);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'inter_servform_serv'){
						var myform = document.getElementById('inter_servform_serv');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#add_interested_service').modal('hide');
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
								$('html, body').animate({scrollTop:0}, 'slow');
							}
						});		
					}else if(formName == 'editinter_servform'){
						var client_id = $('#editinter_servform input[name="client_id"]').val();
						var myform = document.getElementById('editinter_servform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#eidt_interested_service').modal('hide');
								if(obj.status){ 
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-services',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.interest_serv_list').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/agent/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
										success: function(responses){
											var ress = JSON.parse(responses);
											var html = '';
											$.each(ress.data, function(k, v) {
												html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
												if(v.message != null){
													html += '<p>'+v.message+'</p>';
												}
												html += '</div></div>';
											});
											$('.activities').html(html);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'appointform'){
						var client_id = $('#appointform input[name="client_id"]').val();
						var myform = document.getElementById('appointform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('.add_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/agent/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
										success: function(responses){
											var ress = JSON.parse(responses);
											var html = '';
											$.each(ress.data, function(k, v) {
												html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
												if(v.message != null){
													html += '<p>'+v.message+'</p>';
												}
												html += '</div></div>';
											});
											$('.activities').html(html);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'partnerappointform'){
						var client_id = $('#partnerappointform input[name="client_id"]').val();
						var myform = document.getElementById('partnerappointform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#create_appoint').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/partner/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'appliappointform'){
						var client_id = $('#appliappointform input[name="client_id"]').val();
						var noteid = $('#appliappointform input[name="noteid"]').val();
						var myform = document.getElementById('appliappointform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('.add_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
									$.ajax({
										url: site_url+'/agent/get-applications-logs',
										type:'GET',
										data:{id: noteid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'appkicationsendmail'){
						var client_id = $('#appkicationsendmail input[name="client_id"]').val();
						var noteid = $('#appkicationsendmail input[name="noteid"]').val();
						var myform = document.getElementById('appkicationsendmail');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#applicationemailmodal').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
									$.ajax({
										url: site_url+'/agent/get-applications-logs',
										type:'GET',
										data:{id: noteid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					// DEPRECATED: Appointment system removed - editappointment form no longer exists
					}else if(formName == 'editappointment'){
						console.warn('editappointment form validation has been removed - appointment system deprecated');
						return false;
						/*
						var client_id = $('#editappointment input[name="client_id"]').val();
						var myform = document.getElementById('editappointment');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#edit_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/agent/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
										success: function(responses){
											var ress = JSON.parse(responses);
											var html = '';
											$.each(ress.data, function(k, v) {
												html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
												if(v.message != null){
													html += '<p>'+v.message+'</p>';
												}
												html += '</div></div>';
											});
											$('.activities').html(html);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
						*/
						// End of deprecated editappointment validation
					}else if(formName == 'editpartnerappointment'){
						var client_id = $('#editpartnerappointment input[name="client_id"]').val();
						var myform = document.getElementById('editpartnerappointment');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#edit_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/agent/partner/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'notetermform')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('notetermform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
	url: site_url+'/agent/get-notes',
	type:'GET',
	data:{clientid:client_id,type:'client'},
	success: function(responses){
		
		$('.note_term_list').html(responses);
	}
});
								// Refresh activity feed using the standardized function
								if (typeof getallactivities === 'function') {
									getallactivities(client_id);
								} else if (typeof loadActivities === 'function') {
									loadActivities();
								} else {
									console.warn('Activity refresh functions not available');
								}
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}
					else if(formName == 'addtoapplicationform'){
						var myform = document.getElementById('addtoapplicationform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
$('#add_application').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-popupmsg').html('<span  class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}
					else if(formName == 'applicationform')
					{   
						var client_id = $('input[name="client_id"]').val(); 
						var myform = document.getElementById('addapplicationformform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$(".add_appliation #workflow").val('').trigger('change');
			$(".add_appliation #partner").val('').trigger('change');
			$(".add_appliation #product").val('').trigger('change');
								if(obj.status){
									$('.add_appliation').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
					url: site_url+'/agent/get-application-lists',
					type:'GET',
					datatype:'json',
					data:{id:client_id},
					success: function(responses){
						$('.applicationtdata').html(responses);
					}
				});
									$.ajax({
					url: site_url+'/agent/get-activities',
					type:'GET',
					datatype:'json',
					data:{id:client_id},
					success: function(responses){
						var ress = JSON.parse(responses);
						var html = '';
						$.each(ress.data, function(k, v) {
							html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
							if(v.message != null){
								html += '<p>'+v.message+'</p>';
							}
							html += '</div></div>';
						});
						$('.activities').html(html);
					}
				});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
						
						
					}
				else if(formName == 'submit-review')
					{
						$("form[name=submit-review] :input[data-max]").each(function(){
							var data_max  = $(this).attr('data-max');
							var value = $.trim($(this).val());	
							if(parseInt(value) > parseInt(data_max))	
								{
									$(this).after(errorDisplay(maxError + data_max)); 
									$("#loader").hide();
									return false;	
								}
							else
								{
									$("form[name="+formName+"]").submit();
									return true;
								}	
						});	
					}
				else
					{	
						if(formName == 'invoiceform')
						{
							$('input[name="btn"]').val(savetype);
						}
						$("form[name="+formName+"]").submit();
						return true;	
					} 
			}	
		
	}	
	

function customInvoiceValidate(formName, savetype)
	{
		$("#loader").show(); //all form submit
		
		var i = 0;	
		$(".custom-error").remove(); //remove all errors when submit the button
		$("#save_type").val(savetype);
		$("form[name="+formName+"] :input[data-valid]").each(function(){
			var dataValidation = $(this).attr('data-valid');
			var splitDataValidation = dataValidation.split(' ');
			
			var j = 0; //for serial wise errors shown	
			if($.inArray("required", splitDataValidation) !== -1) //for required
				{
					var for_class = $(this).attr('class');	
					if(for_class.indexOf('multiselect_subject') != -1)
						{
							var value = $.trim($(this).val());	
							if (value.length === 0) 
								{
									i++;
									j++;
									$(this).parent().after(errorDisplay(requiredError)); 
								}	
						} 
					else 
						{
							if( !$.trim($(this).val()) ) 
								{
									i++;
									j++;
									$(this).after(errorDisplay(requiredError));  
								}
						}
				}
			if(j <= 0)
				{
					if($.inArray("email", splitDataValidation) !== -1) //for email
						{
							if(!validateEmail($.trim($(this).val()))) 
								{
									i++;
									$(this).after(errorDisplay(emailError));  
								}
						}
						
							
					var forMin = splitDataValidation.find(a =>a.includes("min"));
					if(typeof forMin != 'undefined')
						{
							var breakMin = forMin.split('-');
							var digit = breakMin[1];

							var value = $.trim($(this).val()).length;
							if(value < digit) 
								{
									i++;
									$(this).after(errorDisplay(min+' '+digit+' character.'));  
								}	
						}
						
					var forMax = splitDataValidation.find(a =>a.includes("max"));
					if(typeof forMax != 'undefined')
						{
							var breakMax = forMax.split('-');
							var digit = breakMax[1];

							var value = $.trim($(this).val()).length;
							if(value > digit) 
								{
									i++;
									$(this).after(errorDisplay(max+' '+digit+' character.'));  
								}	
						}
						
					var forEqual = splitDataValidation.find(a =>a.includes("equal"));
					if(typeof forEqual != 'undefined')
						{
							var breakEqual = forEqual.split('-');
							var digit = breakEqual[1];

							var value = ($.trim($(this).val()).replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-')).length;
							if(value != digit) 
								{
									i++;
									$(this).after(errorDisplay(equal+' '+digit+' character.'));  
								}	
						}
				}			
		});
		
		if(i > 0)
			{
				if(formName == 'add-query'){
					$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
				}else if(formName != 'upload-answer')	{
					$('html, body').animate({scrollTop:0}, 'slow');
				}
				$("#loader").hide();
				return false;
			}	
		else
			{
				if(formName == 'add-query')
					{
						$('#preloader').show();
						$('#preloader div').show();
						var myform = document.getElementById('enquiryco');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#preloader').hide();
								$('#preloader div').hide();
								var obj = $.parseJSON(response);
								if(obj.success){
									window.location = redirecturl;
								}else{
									$('.customerror').html(obj.message);
									$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
								}
							}
						});
					}
				else
					{	
				
						$("form[name="+formName+"]").submit();
						return true;	
					} 
			}	
		
	}
	
function errorDisplay(error) {
	return "<span class='custom-error' role='alert'>"+error+"</span>";
}

function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
		return true;
	}
    else {
		return false;
    }
}