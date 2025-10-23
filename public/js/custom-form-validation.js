var requiredError = 'This field is required.';
var emailError = "Please enter the valid email address.";
var captcha = "Captcha invalid.";
var maxError = "Number should be less than or equal to ";
var min = "This field should be greater than or equal to ";
var max = "This field should be less than or equal to ";
var equal = "This field should be equal to ";

function customValidate(formName, savetype = '')
	{ //alert(formName);
		$(".popuploader").show(); //all form submit

		// Legacy sales forecast flows are disabled
		if (formName === 'saleforcast' || formName === 'saleforcastservice') {
			$(".popuploader").hide();
			return false;
		}

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
					}else if(formName == 'edit-note')
					{
						var myform = document.getElementById('editnoteform');
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
									$('#myeditnotes .modal-title').html('');
									$('#myeditnotes #note_type').html('');
									$('#myeditnotes').modal('hide');
									myfollowuplist(obj.leadid);
								}else{
									$('#myeditnotes .customerror').html('<span class="alert alert-danger">'+obj.message+'</span>');

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
										url: site_url+'/get-applications-logs',
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
									url: site_url+'/get-notes',
									type:'GET',
									data:{clientid:client_id,type:'partner'},
									success: function(responses){

										$('.note_term_list').html(responses);
									}
								});

								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

								}
							}
						});
					}else if(formName == 'clientcontact')
					{
                        //nt.getElementById('clientcontact');
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
									url: site_url+'/get-contacts',
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
									url: site_url+'/get-branches',
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
					else if(formName == 'taskform'){
						var client_id = $('#tasktermform input[name="client_id"]').val();
						var myform = document.getElementById('tasktermform');
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
										url: site_url+'/get-tasks',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											// $('#my-datatable').DataTable().destroy();
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
					}
                    else if(formName == 'mig_upload_form'){
						var client_id = $('#mig_upload_form input[name="clientid"]').val();
                        var folder_name = $('#mig_upload_form input[name="folder_name"]').val();
						var myform = document.getElementById('mig_upload_form');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							//datatype:'json',
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#openmigrationdocsmodal').modal('hide');
								if(obj.status){ //alert('folder_name=='+folder_name);
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.migdocumnetlist_'+folder_name).html(obj.data);
									//$('.miggriddata').show();
									$('.miggriddata').html(obj.griddata);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
                                //Fetch All Activities
								getallactivities(client_id);
							}
						});
					}
					else if(formName == 'edu_upload_form'){
						var client_id = $('#edu_upload_form input[name="clientid"]').val();
						var myform = document.getElementById('edu_upload_form');
                        var doccategory = $('#edu_upload_form input[name="doccategory"]').val();
                        //console.log(doccategory);
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							//datatype:'json',
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#openeducationdocsmodal').modal('hide');
								if(obj.status){
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.documnetlist_'+doccategory).html(obj.data);
									$('.griddata_'+doccategory).html(obj.griddata);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
                                //Fetch All Activities
								getallactivities(client_id);
							}
						});
					}

					else if(formName == 'uploadAndFetchMail'){
						var client_id = $('#uploadAndFetchMail input[name="client_id"]').val();
						var myform = document.getElementById('uploadAndFetchMail');
                        var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							//datatype:'json',

                            success: function(response) {
                                $('.popuploader').hide();
                                $('.custom-error-msg').html('');
                                if (response.status) {
                                    $('#uploadAndFetchMailModel').modal('hide');
									localStorage.setItem('activeTab', 'conversations');
                                    location.reload();
                                    $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');
                                } else {
                                    $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    displayValidationErrors(errors);
                                } else {
                                    $('.custom-error-msg').html('<span class="alert alert-danger">An unexpected error occurred. Please try again.</span>');
                                }
                            }
						});
					}


                    else if(formName == 'uploadSentAndFetchMail'){
						var client_id = $('#uploadSentAndFetchMail input[name="client_id"]').val();
						var myform = document.getElementById('uploadSentAndFetchMail');
                        var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							//datatype:'json',
                            success: function(response) {
                                $('.popuploader').hide();
                                $('.custom-error-msg').html('');
                                if (response.status) {
                                    $('#uploadSentAndFetchMailModel').modal('hide');
									// 1. Remove 'active' from all subtabs
									$('.subtab-button[data-subtab="inbox"]').removeClass('active');

									// 2. Add 'active' to the 'Sent' subtab only
									$('.subtab-button[data-subtab="sent"]').addClass('active');

									// 3. Set localStorage values
									localStorage.setItem('activeTab', 'conversations');
									//localStorage.setItem('subactiveTab', 'sent');
                                    location.reload();

                                    $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');
                                } else {
                                    $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    displayValidationErrors2(errors);
                                } else {
                                    $('.custom-error-msg').html('<span class="alert alert-danger">An unexpected error occurred. Please try again.</span>');
                                }
                            }
						});
					}

                    else if(formName == 'change_matter_assignee'){
                        var client_id = $('#change_matter_assignee input[name="client_id"]').val();
                        var myform = document.getElementById('change_matter_assignee');
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                var obj = response; // Remove $.parseJSON(response)
                                $('#changeMatterAssigneeModal').modal('hide');
                                location.reload();
                                $('.popuploader').hide();
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                }else{
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

					else if(formName == 'costAssignmentformlead'){
						var client_id = $('#costAssignmentformlead input[name="client_id"]').val();
						var myform = document.getElementById('costAssignmentformlead');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								var obj = response; // Remove $.parseJSON(response)
								$('#costAssignmentCreateFormModelLead').modal('hide');
								location.reload();
								$('.popuploader').hide();
								if(obj.status){
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}

                    else if(formName == 'add_pers_doc_cat_form'){
                        var client_id = $('#add_pers_doc_cat_form input[name="client_id"]').val();
                        var myform = document.getElementById('add_pers_doc_cat_form');
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                var obj = response; // Remove $.parseJSON(response)
                                $('#addpersonaldoccatmodel').modal('hide');
                                $('.popuploader').hide();
                                if(obj.status){
									localStorage.setItem('activeTab', 'documentalls');
                                    location.reload();
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                }else{
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

                    else if(formName == 'add_visa_doc_cat_form'){
                        var client_id = $('#add_visa_doc_cat_form input[name="client_id"]').val();
                        var myform = document.getElementById('add_visa_doc_cat_form');
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                var obj = response; // Remove $.parseJSON(response)
                                $('#addvisadoccatmodel').modal('hide');
                                $('.popuploader').hide();
                                if(obj.status){
									localStorage.setItem('activeTab', 'documentalls');
                                    location.reload();
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                }else{
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

					else if(formName == 'client_receipt_form'){
						var client_id = $('#client_receipt_form input[name="client_id"]').val();
						var myform = document.getElementById('client_receipt_form');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
                            success: function(response){
                                var obj = response; // Remove $.parseJSON(response)
                                $('.popuploader').hide();
                                if(obj.status){
									$('#createreceiptmodal').modal('hide');
                                    localStorage.setItem('activeTab', 'accounts');
                                    location.reload();
									if(obj.requestData){
										var reqData = obj.requestData;
										var awsUrl = obj.awsUrl; //console.log('awsUrl='+awsUrl);
										var trRows = "";
										$.each(reqData, function(index, subArray) {
											// Determine icon based on type
											let typeIcon = '';
											if(subArray.client_fund_ledger_type == 'Deposit'){
												typeIcon = 'fa-arrow-down';
											} else if(subArray.client_fund_ledger_type == 'Fee Transfer'){
												typeIcon = 'fa-arrow-right-from-bracket';
											} else if(subArray.client_fund_ledger_type == 'Disbursement'){
												typeIcon = 'fa-arrow-up';
											}

											// Create AWS link if available
											let awsLink = (awsUrl != "") ? '<a target="_blank" class="link-primary" href="'+awsUrl+'" title="View Receipt '+subArray.trans_no+'"><i class="fas fa-file-pdf"></i></a>' : '';

											// Format currency
											let depositAmount = subArray.deposit_amount ? "$" + parseFloat(subArray.deposit_amount).toFixed(2) : '';
											let withdrawAmount = subArray.withdraw_amount ? "$" + parseFloat(subArray.withdraw_amount).toFixed(2) : '';
											let balanceAmount = subArray.balance_amount ? "$" + parseFloat(subArray.balance_amount).toFixed(2) : '';

											trRows += `<tr>
												<td>${subArray.trans_date} ${awsLink}</td>
												<td class="type-cell">
													<i class="fas ${typeIcon} type-icon" title="${subArray.client_fund_ledger_type}"></i>
													<span>${subArray.client_fund_ledger_type}  ${subArray.invoice_no ? `(${subArray.invoice_no})` : ''}</span>
												</td>
												<td></td>
												<td class="description">${subArray.description}</td>
												<td><a href="#" title="View Receipt ${subArray.trans_no}">${subArray.trans_no}</a></td>
												<td class="currency text-success">${depositAmount}</td>
												<td class="currency">${withdrawAmount}</td>
												<td class="currency balance">${balanceAmount}</td>
											</tr>`;
										});
									}
				                    //console.log('trRows='+trRows);
									$('.productitemList').append(trRows);
									if(obj.db_total_balance_amount){
										let db_total_balance_amount = obj.db_total_balance_amount ? "$" + parseFloat(obj.db_total_balance_amount).toFixed(2) : '';
										$('.funds-held').html(db_total_balance_amount);
									}

                                    // Now find and update the row
                                    if( obj.invoice_no != "" ) {
                                        let invoiceNo = obj.invoice_no;
                                        let invoiceBalance = obj.invoice_balance;
                                        let invoiceStatus = obj.invoice_status;

                                        // Define the status class and description
                                        let statusClassMap = {
                                            '0': 'status-unpaid',
                                            '1': 'status-paid',
                                            '2': 'status-partial',
                                            '3': 'status-void'
                                        };

                                        let statusVal = {
                                            '0': 'Unpaid',
                                            '1': 'Paid',
                                            '2': 'Partial',
                                            '3': 'Void'
                                        };

                                        let statusClass = statusClassMap[invoiceStatus];
                                        let statusText = statusVal[invoiceStatus];

                                        $(".productitemList_invoice tr").each(function () {
                                            let $row = $(this);
                                            let rowInvoiceNo = $.trim($row.find("td:first").clone().children().remove().end().text());
                                            console.log('rowInvoiceNo='+rowInvoiceNo);
                                            console.log('invoiceNo='+invoiceNo);
                                            if (rowInvoiceNo === invoiceNo) {
                                                // Update Amount
                                                $row.find("td.currency").html(`$ ${invoiceBalance}`);

                                                // Update Status
                                                $row.find("td:last").html(
                                                    `<span class="status-badge ${statusClass}">${statusText}</span>`
                                                );
                                            }
                                        });
                                        $('.outstanding-balance').text('$ ' + obj.outstanding_balance);
                                    }
                                    //Fetch All Activities
                                    getallactivities(client_id);
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									alert(obj.message);
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}

					else if(formName == 'invoice_receipt_form'){
						var client_id = $('#invoice_receipt_form input[name="client_id"]').val();
						var myform = document.getElementById('invoice_receipt_form');
						var fd = new FormData(myform);
						fd.append('save_type', savetype);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
                                alert('Invoice No - '+ obj.invoice_no + ' is generated');
								$('#createreceiptmodal').modal('hide');
                                localStorage.setItem('activeTab', 'accounts');
                                location.reload();
								if(obj.status)
								{
									if(obj.function_type == 'add')
									{
										/*
                                        if(obj.requestData)
										{
											var reqData = obj.requestData;
											var trRows_invoice = "";
											$.each(reqData, function(index, subArray) {
												var unique_invoice_id = "invoiceTrRow_" + subArray.id;
												var trcls = (subArray.void_invoice == 1) ? "strike-through" : "";

												// Generate action link
												var actionLink = '';
												if(subArray.save_type === 'draft'){
													actionLink = '<a class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="'+subArray.receipt_id+'"><i class="fas fa-pencil-alt"></i></a>';
												} else if(subArray.save_type === 'final') {
													var invoiceUrl = "/clients/genInvoice/" + subArray.receipt_id;
													actionLink = '<a target="_blank" class="link-primary" href="'+invoiceUrl+'"><i class="fas fa-file-pdf"></i></a>';
												}

												// Status mapping
												var statusMap = {
													'0': {class: 'status-unpaid', text: 'Unpaid'},
													'1': {class: 'status-paid', text: 'Paid'},
													'2': {class: 'status-partial', text: 'Partial'},
													'3': {class: 'status-void', text: 'Void'}
												};
												var statusInfo = statusMap[subArray.invoice_status] || {class: '', text: ''};

												trRows_invoice += "<tr class='invoiceTrRow "+trcls+"' id='"+unique_invoice_id+"'>" +
													"<td>"+subArray.trans_no+" "+actionLink+"</td>" +
													"<td>"+subArray.trans_date+"</td>" +
													"<td>"+subArray.description+"</td>" +
													"<td class='currency'>$ "+parseFloat(subArray.withdraw_amount).toFixed(2)+"</td>" +
													"<td><span class='status-badge "+statusInfo.class+"'>"+statusInfo.text+"</span></td>" +
													"</tr>";
											});
											$('.productitemList_invoice').append(trRows_invoice);
										}*/

                                        if (obj.requestData && obj.requestData.length > 0) {
                                            var subArray = obj.requestData[obj.requestData.length - 1]; // last item
                                            //var allAmounts = obj.requestData.map(item => parseFloat(item.balance_amount));
                                            //var totalAmount = allAmounts.reduce((acc, val) => acc + val, 0); // sum all amounts

                                            var totalAmount = obj.total_balance_amount; //alert(totalAmount);
                                            var unique_invoice_id = "invoiceTrRow_" + subArray.id;
                                            var trcls = (subArray.void_invoice == 1) ? "strike-through" : "";

                                            // Generate action link
                                            var actionLink = '';
                                            if (subArray.save_type === 'draft') {
                                                actionLink = '<a class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="'+subArray.receipt_id+'"><i class="fas fa-pencil-alt"></i></a>';
                                            } else if (subArray.save_type === 'final') {
                                                var invoiceUrl = "/clients/genInvoice/" + subArray.receipt_id;
                                                actionLink = '<a target="_blank" class="link-primary" href="'+invoiceUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                            }

                                            // Status mapping
                                            var statusMap = {
                                                '0': {class: 'status-unpaid', text: 'Unpaid'},
                                                '1': {class: 'status-paid', text: 'Paid'},
                                                '2': {class: 'status-partial', text: 'Partial'},
                                                '3': {class: 'status-void', text: 'Void'}
                                            };
                                            var statusInfo = statusMap[subArray.invoice_status] || {class: '', text: ''};

                                            var trRow = "<tr class='invoiceTrRow "+trcls+"' id='"+unique_invoice_id+"'>" +
                                                "<td>"+subArray.trans_no+" "+actionLink+"</td>" +
                                                "<td>"+subArray.trans_date+"</td>" +
                                                "<td>"+subArray.description+"</td>" +
                                                "<td class='currency'>$ "+totalAmount.toFixed(2)+"</td>" + // show sum here
                                                "<td><span class='status-badge "+statusInfo.class+"'>"+statusInfo.text+"</span></td>" +
                                                "</tr>";

                                            $('.productitemList_invoice').append(trRow);
                                        }

                                        //Update Outstanding Balance by adding new unpaid/partial to current balance
                                        var currentBalanceText = $('.outstanding-balance').text().replace(/[$,]/g, '').trim();
                                        var currentBalance = parseFloat(currentBalanceText) || 0;

                                        var newOutstanding = 0;
                                        $.each(obj.requestData, function(index, item) {
                                            if (item.invoice_status == '0' || item.invoice_status == '2') {
                                                newOutstanding += parseFloat(item.withdraw_amount);
                                            }
                                        });
                                        var updatedOutstanding = currentBalance + newOutstanding;
                                        $('.outstanding-balance').text('$ ' + updatedOutstanding.toFixed(2));
                                        //Fetch All Activities
                                        getallactivities(client_id);
                                    }

									if(obj.function_type == 'edit')
									{
										//for delete
										if(obj.requestDeleteDataType == 'delete'){
											if(obj.requestDeleteData){
												var requestDeleteData = obj.requestDeleteData;
												$.each(requestDeleteData, function(index1, subArray1) {
													$('#invoiceTrRow_'+subArray1).remove();
												});
											}
										}

										//for add new entry
										if(obj.requestAddDataType == 'add'){
											if(obj.requestAddData){
												var trRows_invoice2 = "";
												var requestAddData = obj.requestAddData;
												$.each(requestAddData, function(index2, subArray2) {
													if(subArray2.save_type == 'draft'){
														var unique_invoice_id2 = "invoiceTrRow_"+subArray2.id;
														var draftlink2 = '<a class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="'+subArray2.receipt_id+'"><i class="fas fa-pencil-alt"></i></a>';
														trRows_invoice2 += "<tr class='invoiceTrRow' id='"+unique_invoice_id2+"'><td>"+subArray2.invoice_no+" "+draftlink2+"</td><td>"+subArray2.trans_date+"</td><td>"+subArray2.entry_date+"</td><td>"+subArray2.trans_no+"</td><td>"+subArray2.gst_included+"</td><td>"+subArray2.payment_type+"</td><td>"+subArray2.description+"</td><td>$"+subArray2.deposit_amount+"</td></tr>";
													}
													else if(subArray2.save_type == 'final') {
														var unique_invoice_id2 = "invoiceTrRow_"+subArray2.id;
														var invoiceUrl2 = "/clients/genInvoice/"+subArray2.receipt_id;
														var finallink2 = '<a target="_blank" class="link-primary" href="'+invoiceUrl2+'"><i class="fas fa-file-pdf"></i></a>';
														trRows_invoice2 += "<tr class='invoiceTrRow' id='"+unique_invoice_id2+"'><td>"+subArray2.invoice_no+" "+finallink2+"</td><td>"+subArray2.trans_date+"</td><td>"+subArray2.entry_date+"</td><td>"+subArray2.trans_no+"</td><td>"+subArray2.gst_included+"</td><td>"+subArray2.payment_type+"</td><td>"+subArray2.description+"</td><td>$"+subArray2.deposit_amount+"</td></tr>";
													}
												});
												$('.productitemList_invoice tr:last').before(trRows_invoice2);
											}
										}


										if(obj.requestData){
											var reqData = obj.requestData;
											//var trRows_invoice = "";
											$.each(reqData, function(index, subArray) {
												$('#invoiceTrRow_'+subArray.id).empty();
												//console.log('save_type='+subArray.save_type);
												if(subArray.save_type == 'draft'){
													var draftlink = '<a class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="'+subArray.receipt_id+'"><i class="fas fa-pencil-alt"></i></a>';
													var trRows_invoice = "<td>"+subArray.invoice_no+" "+draftlink+"</td><td>"+subArray.trans_date+"</td><td>"+subArray.entry_date+"</td><td>"+subArray.trans_no+"</td><td>"+subArray.gst_included+"</td><td>"+subArray.payment_type+"</td><td>"+subArray.description+"</td><td>$"+subArray.deposit_amount+"</td>";
												}
												else if(subArray.save_type == 'final') {
													var invoiceUrl = "/clients/genInvoice/"+subArray.receipt_id;
													var finallink = '<a target="_blank" class="link-primary" href="'+invoiceUrl+'"><i class="fas fa-file-pdf"></i></a>';
													var trRows_invoice = "<td>"+subArray.invoice_no+" "+finallink+"</td><td>"+subArray.trans_date+"</td><td>"+subArray.entry_date+"</td><td>"+subArray.trans_no+"</td><td>"+subArray.gst_included+"</td><td>"+subArray.payment_type+"</td><td>"+subArray.description+"</td><td>$"+subArray.deposit_amount+"</td>";
												}
												$('#invoiceTrRow_'+subArray.id).append(trRows_invoice);
											});
										}
										//console.log('trRows_invoice='+trRows_invoice);
										//$('.lastRow_invoice').before(trRows_invoice);
										if(obj.db_total_deposit_amount){
											$('.totDepoAmTillNow_invoice').html("$"+obj.db_total_deposit_amount);
											$('#sum_of_invoice').val("$"+obj.db_total_deposit_amount);


											//Calculation Of Total balance
											var sum_of_invoice = $('#sum_of_invoice').val();
											sum_of_invoice = sum_of_invoice.replace('$', '');

											var sum_of_client_receipts = $('#sum_of_client_receipts').val();
											sum_of_client_receipts = sum_of_client_receipts.replace('$', '');

											var sum_of_office_receipts = $('#sum_of_office_receipts').val();
											sum_of_office_receipts = sum_of_office_receipts.replace('$', '');

											var Total_balance = sum_of_invoice - sum_of_client_receipts - sum_of_office_receipts;
											if(Total_balance<0){
												Total_balance = 0;
											}
											$('#total_balance').val("$"+Total_balance);
										}
									}
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}

					// Office Receipt Form Validation
                    else if (formName == 'office_receipt_form') {
                        // Step 1: Perform the invoice amount validation before submitting the form
                        let invoiceNos = document.getElementsByName('invoice_no[]');
                        let receivedAmounts = document.getElementsByName('deposit_amount[]');
                        let shouldProceed = true;

                        // Group received amounts by invoice number
                        let invoiceTotals = {};

                        for (let i = 0; i < invoiceNos.length; i++) {
                            let invoiceNo = invoiceNos[i].value; // e.g., INV-005
                            let receivedAmount = parseFloat(receivedAmounts[i].value) || 0;

                            if (invoiceNo) { // Only process if invoiceNo is not empty
                                if (!invoiceTotals[invoiceNo]) {
                                    invoiceTotals[invoiceNo] = 0;
                                }
                                invoiceTotals[invoiceNo] += receivedAmount;
                            }
                        }

                        // Step 2: Compare the summed received amount for each invoice with its invoice amount
                        // Since getInvoiceAmount is async, we need to handle this asynchronously
                        (async function() {
                            for (let invoiceNo in invoiceTotals) {
                                let totalReceivedAmount = invoiceTotals[invoiceNo];
                                let invoiceAmount = await getInvoiceAmount(invoiceNo); // Fetch invoice amount via AJAX

                                // Case 1: Total Received Amount > Invoice Amount
                                if (totalReceivedAmount > invoiceAmount) {
                                    let confirmMessage = `The total received amount ($${totalReceivedAmount.toFixed(2)}) for invoice ${invoiceNo} exceeds the invoice amount ($${invoiceAmount.toFixed(2)}). Do you want to continue with this amount?`;
                                    if (!confirm(confirmMessage)) { // Show warning popup
                                        shouldProceed = false; // If user clicks "No", stop submission
                                        $('.popuploader').hide();
                                        break;
                                    }
                                }
                                // Case 2: Total Received Amount <= Invoice Amount (no popup, proceed directly)
                            }

                            // Step 3: If validation passes, proceed with the form submission
                            if (shouldProceed) {
                                var client_id = $('#office_receipt_form input[name="client_id"]').val();
                                var myform = document.getElementById('office_receipt_form');
                                var fd = new FormData(myform);

                                $('.popuploader').show(); // Show loader if part of your UI

                                $.ajax({
                                    type: 'post',
                                    url: $("form[name=" + formName + "]").attr('action'),
                                    processData: false,
                                    contentType: false,
                                    data: fd,
                                    success: function(response) {
                                        $('.popuploader').hide();
                                        var obj = response; // Remove $.parseJSON(response)
                                        $('#createreceiptmodal').modal('hide');
                                        localStorage.setItem('activeTab', 'accounts');
                                        location.reload();

                                        if (obj.status) {
                                            if (obj.requestData) {
                                                var reqData = obj.requestData;
                                                var awsUrl = obj.awsUrl;
                                                var trRows_office = "";
                                                $.each(reqData, function(index, subArray) {
                                                    let awsLink = awsUrl !== "" ? '<a target="_blank" class="link-primary" href="' + awsUrl + '"><i class="fas fa-file-pdf"></i></a>' : '';

                                                    let payIconMap = {
                                                        'Cash': 'fa-arrow-down',
                                                        'Bank transfer': 'fa-arrow-right-from-bracket',
                                                        'EFTPOS': 'fa-arrow-right-from-bracket',
                                                        'Refund': 'fa-arrow-right-from-bracket'
                                                    };
                                                    let paymentIcon = payIconMap[subArray.payment_method] || 'fa-money-bill';

                                                    let depositAmount = subArray.deposit_amount ? '$ ' + parseFloat(subArray.deposit_amount).toFixed(2) : '';

                                                    trRows_office += `
                                                        <tr>
                                                            <td>${subArray.trans_date} ${awsLink}</td>
                                                            <td class="type-cell">
                                                                <i class="fas ${paymentIcon} type-icon"></i>
                                                                <span>
                                                                ${subArray.payment_method}<br/>
                                                                (${subArray.invoice_no})
                                                                </span>
                                                            </td>
                                                            <td></td>
                                                            <td class="description">${subArray.description}</td>
                                                            <td><a href="#" title="View Receipt ${subArray.trans_no}">${subArray.trans_no}</a></td>
                                                            <td class="currency text-success">${depositAmount}</td>
                                                        </tr>
                                                    `;
                                                });
                                                $('.productitemList_office').append(trRows_office);
                                            }

                                            // Update invoice row if applicable
                                            if (obj.invoice_no != "") {
                                                let invoiceNo = obj.invoice_no;
                                                let invoiceBalance = obj.invoice_balance;
                                                let invoiceStatus = obj.invoice_status;

                                                let statusClassMap = {
                                                    '0': 'status-unpaid',
                                                    '1': 'status-paid',
                                                    '2': 'status-partial',
                                                    '3': 'status-void'
                                                };

                                                let statusVal = {
                                                    '0': 'Unpaid',
                                                    '1': 'Paid',
                                                    '2': 'Partial',
                                                    '3': 'Void'
                                                };

                                                let statusClass = statusClassMap[invoiceStatus];
                                                let statusText = statusVal[invoiceStatus];

                                                $(".productitemList_invoice tr").each(function() {
                                                    let $row = $(this);
                                                    let rowInvoiceNo = $.trim($row.find("td:first").clone().children().remove().end().text());
                                                    if (rowInvoiceNo === invoiceNo) {
                                                        $row.find("td.currency").html(`$ ${invoiceBalance}`);
                                                        $row.find("td:last").html(
                                                            `<span class="status-badge ${statusClass}">${statusText}</span>`
                                                        );
                                                    }
                                                });
                                                $('.outstanding-balance').text('$ ' + obj.outstanding_balance);
                                            }

                                            //Fetch All Activities
                                            getallactivities(client_id);
                                            $('.custom-error-msg').html('<span class="alert alert-success">' + obj.message + '</span>');
                                        } else {
                                            $('.custom-error-msg').html('<span class="alert alert-danger">' + obj.message + '</span>');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        $('.popuploader').hide();
                                        $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred while submitting the form.</span>');
                                    }
                                });
                            }
                        })();
                    }


                    else if(formName == 'adjust_invoice_receipt_form'){
						var client_id = $('#adjust_invoice_receipt_form input[name="client_id"]').val();
						var myform = document.getElementById('adjust_invoice_receipt_form');
						var fd = new FormData(myform);
						fd.append('save_type', savetype);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#createadjustinvoicereceiptmodal').modal('hide');
                                location.reload();
								if(obj.status)
								{
									if(obj.function_type == 'add')
									{

                                        if (obj.requestData && obj.requestData.length > 0) {
                                            var subArray = obj.requestData[obj.requestData.length - 1]; // last item
                                            var totalAmount = obj.total_balance_amount; //alert(totalAmount);
                                            var unique_invoice_id = "invoiceTrRow_" + subArray.id;
                                            var trcls = (subArray.void_invoice == 1) ? "strike-through" : "";

                                            // Generate action link
                                            var actionLink = '';
                                            if (subArray.save_type === 'draft') {
                                                actionLink = '<a class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="'+subArray.receipt_id+'"><i class="fas fa-pencil-alt"></i></a>';
                                            } else if (subArray.save_type === 'final') {
                                                var invoiceUrl = "/clients/genInvoice/" + subArray.receipt_id;
                                                actionLink = '<a target="_blank" class="link-primary" href="'+invoiceUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                            }

                                            // Status mapping
                                            var statusMap = {
                                                '0': {class: 'status-unpaid', text: 'Unpaid'},
                                                '1': {class: 'status-paid', text: 'Paid'},
                                                '2': {class: 'status-partial', text: 'Partial'},
                                                '3': {class: 'status-void', text: 'Void'}
                                            };
                                            var statusInfo = statusMap[subArray.invoice_status] || {class: '', text: ''};

                                            var trRow = "<tr class='invoiceTrRow "+trcls+"' id='"+unique_invoice_id+"'>" +
                                                "<td>"+subArray.trans_no+" "+actionLink+"</td>" +
                                                "<td>"+subArray.trans_date+"</td>" +
                                                "<td>"+subArray.description+"</td>" +
                                                "<td class='currency'>$ "+totalAmount.toFixed(2)+"</td>" + // show sum here
                                                "<td><span class='status-badge "+statusInfo.class+"'>"+statusInfo.text+"</span></td>" +
                                                "</tr>";

                                            $('.productitemList_invoice').append(trRow);
                                        }

                                        //Update Outstanding Balance by adding new unpaid/partial to current balance
                                        var currentBalanceText = $('.outstanding-balance').text().replace(/[$,]/g, '').trim();
                                        var currentBalance = parseFloat(currentBalanceText) || 0;

                                        var newOutstanding = 0;
                                        $.each(obj.requestData, function(index, item) {
                                            if (item.invoice_status == '0' || item.invoice_status == '2') {
                                                newOutstanding += parseFloat(item.withdraw_amount);
                                            }
                                        });
                                        var updatedOutstanding = currentBalance + newOutstanding;
                                        $('.outstanding-balance').text('$ ' + updatedOutstanding.toFixed(2));

                                        //Fetch All Activities
                                        getallactivities(client_id);
                                    }
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}

					else if(formName == 'create_journal_receipt'){
						var client_id = $('#create_journal_receipt input[name="client_id"]').val();
						var myform = document.getElementById('create_journal_receipt');
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
								$('#createjournalreceiptmodal').modal('hide');
								if(obj.status){
									if(obj.requestData){
										var reqData = obj.requestData;
										var awsUrl = obj.awsUrl; //console.log('awsUrl='+awsUrl);
										var trRows_journal = "";
										$.each(reqData, function(index, subArray) {
											if(awsUrl != ""){
												var awsLink = '<a target="_blank" class="link-primary" href="'+awsUrl+'"><i class="fas fa-file-pdf"></i></a>';
											} else {
												var awsLink = '';
											}
											trRows_journal += "<tr><td>"+subArray.trans_date+" "+awsLink+"</td><td>"+subArray.entry_date+"</td><td>"+subArray.trans_no+"</td><td>"+subArray.invoice_no+"</td><td>"+subArray.description+"</td><td>"+subArray.withdrawal_amount+"</td></tr>";
										});
									}
									//console.log('trRows_journal='+trRows_journal);
									$('.lastRow_journal').before(trRows_journal);

									if(obj.db_total_withdrawal_amount){
										$('.totWithdrwalAmTillNow_journal').html("$"+obj.db_total_withdrawal_amount);
									}

									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}

					else if(formName == 'tasktermclientform'){
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
										url: site_url+'/partner/get-tasks',
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
					}else if(formName == 'educationform'){
						var client_id = $('#educationform input[name="client_id"]').val();
						var myform = document.getElementById('educationform');
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
								$('.create_education').modal('hide');
								if(obj.status){
									$('#create_note').modal('hide');
								    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/get-educations',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
                                            $('.education_list').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
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
										url: site_url+'/get-all-fees',
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
					}else if(formName == 'applicationfeeform'){

						var myform = document.getElementById('applicationfeeform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
									$('#new_fee_option').modal('hide');
								var obj = $.parseJSON(response);

								if(obj.status){

								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.product_totalfee').html(obj.totalfee);
									$('.product_discount').html(obj.discount);
									var t = parseFloat(obj.totalfee) - parseFloat(obj.discount);
									$('.product_net_fee').html(t);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

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
										url: site_url+'/get-services',
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
										url: site_url+'/get-all-paymentschedules',
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
								var obj = $.parseJSON(response)
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
										url: site_url+'/get-all-paymentschedules',
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
										url: site_url+'/get-all-fees',
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
										url: site_url+'/get-promotions',
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
										url: site_url+'/get-promotions',
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
					}else if(formName == 'editeducationform'){
						var client_id = $('#editeducationform input[name="client_id"]').val();
						var myform = document.getElementById('editeducationform');
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
									$('#edit_education').modal('hide');
								    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/get-educations',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
                                            $('.education_list').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
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
								$('.toefl_date').html(obj.toefl_Date);
								$('.ilets_Listening').html(obj.ilets_Listening);
								$('.ilets_Reading').html(obj.ilets_Reading);
								$('.ilets_Writing').html(obj.ilets_Writing);
								$('.ilets_speaking').html(obj.ilets_Speaking);
								$('.ilets_score').html(obj.score_2);
								$('.ilets_date').html(obj.ilets_date);
								$('.pte_Listening').html(obj.pte_Listening);
								$('.pte_Reading').html(obj.pte_Reading);
								$('.pte_Writing').html(obj.pte_Writing);
								$('.pte_Speaking').html(obj.pte_Speaking);
								$('.pte_score').html(obj.score_3);
								$('.pte_date').html(obj.pte_Date);
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
										url: site_url+'/get-invoices',
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
										url: site_url+'/get-applications-logs',
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
										url: site_url+'/get-services',
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
										url: site_url+'/get-services',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
                                            $('.interest_serv_list').html(responses);
										}
									});
									//Fetch All Activities
                                    getallactivities(client_id);
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
										url: site_url+'/get-services',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
                                            $('.interest_serv_list').html(responses);
										}
									});
									//Fetch All Activities
                                    getallactivities(client_id);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
					}

                    else if(formName == 'appointform'){
						var client_id = $('#appointform input[name="client_id"]').val();
						 var appoint_date = $('#timeslot_col_date').val(); //alert(appoint_date);
                        var appoint_time = $('#timeslot_col_time').val(); //alert(appoint_time);

						if( appoint_date == "" || appoint_time == ""){
                            $('.popuploader').hide();
                            $('.timeslot_col_date_time').show();
                            return false;
                        } else {
							$('.timeslot_col_date_time').hide();
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
                                        /*if(obj.reloadpage){
                                            location.reload();
                                        }*/
                                        $('.add_appointment').modal('hide');
                                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                        $.ajax({
                                            url: site_url+'/get-appointments',
                                            type:'GET',
                                            data:{clientid:client_id},
                                            success: function(responses){
                                                $('.appointmentlist').html(responses);
                                            }
                                        });
                                        //Fetch All Activities
                                        getallactivities(client_id);
									}else{
										$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                    }
								}
							});
					  	}
					}

                    else if(formName == 'partnerappointform'){
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
										url: site_url+'/partner/get-appointments',
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
										url: site_url+'/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){

											$('.appointmentlist').html(responses);
										}
									});

									$.ajax({
										url: site_url+'/get-applications-logs',
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
										url: site_url+'/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){

											$('.appointmentlist').html(responses);
										}
									});

									$.ajax({
										url: site_url+'/get-applications-logs',
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
					}else if(formName == 'editappointment'){
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
										url: site_url+'/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
                                            $('.appointmentlist').html(responses);
										}
									});
									//Fetch All Activities
                                    getallactivities(client_id);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
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
										url: site_url+'/partner/get-appointments',
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
					}
					else if(formName == 'notetermform')
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
										url: site_url+'/get-notes',
										type:'GET',
										data:{clientid:client_id,type:'client'},
										success: function(responses){
											$('.note_term_list').html(responses);
                                            // Get the task group that was selected when creating the note
                                            const createdTaskGroup = $('#noteType').val();
                                            
                                            // Auto-switch to the correct task group tab
                                            if (createdTaskGroup && createdTaskGroup !== '') {
                                                $('.subtab8-button').removeClass('active');
                                                $('.subtab8-pane').removeClass('active');
                                                $(`[data-subtab8="${createdTaskGroup}"]`).addClass('active');
                                                $(`#${createdTaskGroup}-subtab8`).addClass('active');
                                            }
                                            
                                            // Apply combined matter and task group filtering
                                            const selectedMatter = $('.general_matter_checkbox_client_detail').is(':checked') 
                                                ? $('.general_matter_checkbox_client_detail').val() 
                                                : $('#sel_matter_id_client_detail').val();
                                            const activeTaskGroup = $('.subtab8-button.active').data('subtab8') || 'All';
                                            
                                            $('#noteterm-tab').find('.note-card-redesign').each(function() {
                                                const noteMatterId = $(this).data('matterid');
                                                const noteType = $(this).data('type');
                                                
                                                let showNote = false;
                                                
                                                // Matter filtering logic
                                                if (selectedMatter !== "") {
                                                    // Show notes that match the selected matter OR notes with no matter_id
                                                    showNote = (noteMatterId == selectedMatter || noteMatterId == '' || noteMatterId == null);
                                                } else {
                                                    // Show all notes when no matter is selected
                                                    showNote = true;
                                                }
                                                
                                                // Task group filtering logic
                                                if (showNote && activeTaskGroup !== 'All') {
                                                    showNote = (noteType === activeTaskGroup);
                                                }
                                                
                                                if (showNote) {
                                                    $(this).show();
                                                } else {
                                                    $(this).hide();
                                                }
                                            });
										}
									});
									//Fetch All Activities
                                    getallactivities(client_id);
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}
					else if(formName == 'notetermform_n')
					{
                        var client_id = $('input[name="client_id"]').val();
						var myform = document.getElementById('notetermform_n');
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
								    $('#create_note_d input[name="title"]').val('');
								    $('#create_note_d input[name="title"]').val('');
									$("#create_note_d .summernote-simple").val('');
									$('#create_note_d input[name="noteid"]').val('');
									$("#create_note_d .summernote-simple").summernote('code','');
									$('#create_note_d').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');

									$.ajax({
										url: site_url+'/get-notes',
										type:'GET',
										data:{clientid:client_id,type:'client'},
										success: function(responses){
											$('.note_term_list').html(responses);
                                            // Get the task group that was selected when creating the note
                                            const createdTaskGroup = $('#noteType').val();
                                            
                                            // Auto-switch to the correct task group tab
                                            if (createdTaskGroup && createdTaskGroup !== '') {
                                                $('.subtab8-button').removeClass('active');
                                                $('.subtab8-pane').removeClass('active');
                                                $(`[data-subtab8="${createdTaskGroup}"]`).addClass('active');
                                                $(`#${createdTaskGroup}-subtab8`).addClass('active');
                                            }
                                            
                                            // Apply combined matter and task group filtering
                                            const selectedMatter = $('.general_matter_checkbox_client_detail').is(':checked') 
                                                ? $('.general_matter_checkbox_client_detail').val() 
                                                : $('#sel_matter_id_client_detail').val();
                                            const activeTaskGroup = $('.subtab8-button.active').data('subtab8') || 'All';
                                            
                                            $('#noteterm-tab').find('.note-card-redesign').each(function() {
                                                const noteMatterId = $(this).data('matterid');
                                                const noteType = $(this).data('type');
                                                
                                                let showNote = false;
                                                
                                                // Matter filtering logic
                                                if (selectedMatter !== "") {
                                                    // Show notes that match the selected matter OR notes with no matter_id
                                                    showNote = (noteMatterId == selectedMatter || noteMatterId == '' || noteMatterId == null);
                                                } else {
                                                    // Show all notes when no matter is selected
                                                    showNote = true;
                                                }
                                                
                                                // Task group filtering logic
                                                if (showNote && activeTaskGroup !== 'All') {
                                                    showNote = (noteType === activeTaskGroup);
                                                }
                                                
                                                if (showNote) {
                                                    $(this).show();
                                                } else {
                                                    $(this).hide();
                                                }
                                            });
										}
									});
                                    //Fetch All activities
                                    getallactivities(client_id);
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
                                        url: site_url+'/get-application-lists',
                                        type:'GET',
                                        datatype:'json',
                                        data:{id:client_id},
                                        success: function(responses){
                                            $('.applicationtdata').html(responses);
                                        }
                                    });
                                    //Fetch All Activities
                                    getallactivities(client_id);
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

                    else if(formName == 'inbox-email-assign-to-client-matter'){
                        var myform = document.getElementById('inbox-email-assign-to-client-matter');
                        var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#inbox_assignemail_modal').modal('hide');
								var obj = $.parseJSON(response);
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
                    }

                    else if(formName == 'sent-email-assign-to-client-matter'){
                        var myform = document.getElementById('sent-email-assign-to-client-matter');
                        var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#sent_assignemail_modal').modal('hide');
								var obj = $.parseJSON(response);
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
                    }


                    else if(formName == 'inbox-email-reassign-to-client-matter'){
                        var myform = document.getElementById('inbox-email-reassign-to-client-matter');
                        var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
                                $('.popuploader').hide();
								$('#inbox_reassignemail_modal').modal('hide');
                                location.reload();
								var obj = $.parseJSON(response);
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
                    }

					else if(formName == 'sent-email-reassign-to-client-matter') {
						var myform = document.getElementById('sent-email-reassign-to-client-matter');
                        var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
                                $('.popuploader').hide();
								$('#sent_reassignemail_modal').modal('hide');
                                location.reload();
								var obj = $.parseJSON(response);
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
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


// Function to show validation errors dynamically
function displayValidationErrors(errors) {
    $('.error-message').remove(); // Remove existing messages
    $('.popuploader').hide();
    $.each(errors, function(field, messages) { //alert(field+'---'+messages);
        //let inputField = $('#'+field);
        let errorMessage = '<small style="display: inline-block;" class="text-danger error-message">' + messages[0] + '</small>';
        //inputField.after(errorMessage);
        $('#email_files').after(errorMessage);
    });
}

// Function to show validation errors dynamically
function displayValidationErrors2(errors) {
    $('.error-message').remove(); // Remove existing messages
    $('.popuploader').hide();
    $.each(errors, function(field, messages) { //alert(field+'---'+messages);
        //let inputField = $('#'+field);
        let errorMessage = '<small style="display: inline-block;" class="text-danger error-message">' + messages[0] + '</small>';
        $('#email_files1').after(errorMessage);
    });
}

// Function to fetch invoice amount via AJAX
async function getInvoiceAmount(invoiceNo) {
    try {
        const response = await fetch('/clients/invoiceamount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Include CSRF token
            },
            body: JSON.stringify({ invoice_no: invoiceNo }),
        });

        const data = await response.json();

        if (data.success) {
            return parseFloat(data.balance_amount) || 0;
        } else {
            console.error('Invoice not found:', data.message);
            return 0;
        }
    } catch (error) {
        console.error('Error fetching invoice amount:', error);
        return 0; // Return 0 in case of error
    }
}

//Fetch All Activities
function getallactivities(client_id){
	$.ajax({
		url: site_url+'/get-activities',
		type:'GET',
		datatype:'json',
		data:{id:client_id},
		success: function(responses){
			var ress = JSON.parse(responses);
			var html = '';
		$.each(ress.data, function (k, v) {
			// Determine icon based on activity type
			var activityType = v.activity_type ?? 'note';
			var subjectIcon;
			var iconClass = '';
			
			if (activityType === 'sms') {
				subjectIcon = '<i class="fas fa-sms"></i>';
				iconClass = 'feed-icon-sms';
			} else if (v.subject && v.subject.toLowerCase().includes("document")) {
				subjectIcon = '<i class="fas fa-file-alt"></i>';
			} else {
				subjectIcon = '<i class="fas fa-sticky-note"></i>';
			}

			var subject = v.subject ?? '';
			var description = v.message ?? '';
			var taskGroup = v.task_group ?? '';
			var followupDate = v.followup_date ?? '';
			var date = v.date ?? '';
			var createdBy = v.createdname ?? 'Unknown';
			var fullName = v.name ?? '';
			var activityTypeClass = activityType ? 'activity-type-' + activityType : '';

			html += `
				<li class="feed-item feed-item--email activity ${activityTypeClass}" id="activity_${v.activity_id}">
					<span class="feed-icon ${iconClass}">
						${subjectIcon}
					</span>
					<div class="feed-content">
						<p><strong>${fullName} ${subject}</strong></p>
						${description !== '' ? `<p>${description}</p>` : ''}
						${taskGroup !== '' ? `<p>${taskGroup}</p>` : ''}
						${followupDate !== '' ? `<p>${followupDate}</p>` : ''}
						<span class="feed-timestamp">${date}</span>
					</div>
				</li>
			`;
		});
			$('.feed-list').html(html);
			$('.popuploader').hide();
		}
	});
}
