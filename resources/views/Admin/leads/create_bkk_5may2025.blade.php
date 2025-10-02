@extends('layouts.admin')
@section('title', 'Create Leads')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
	    <div class="server-error">
				@include('../Elements/flash-message')
        </div>
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/clients/store', 'method'=>'post', 'name'=>"add-leads", 'id'=>'add-leads','autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			    <input type="hidden" name="type" value="lead">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create Leads</h4>
								<div class="card-header-action">
									<a href="{{route('admin.leads.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
                                <div class="row">
									<div class="col-12 col-md-12 col-lg-12">
										<div class="row">
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group">
													<label for="first_name">First Name <span class="span_req">*</span></label>
													{{ Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
													@if ($errors->has('first_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('first_name') }}</strong>
														</span>
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group">
													<label for="last_name">Last Name</label>
													{{ Form::text('last_name', '', array('class' => 'form-control','autocomplete'=>'off','placeholder'=>'' )) }}
													@if ($errors->has('last_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('last_name') }}</strong>
														</span>
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<?php $oldgender = old('gender');?>
												<div class="form-group">
													<label style="display:block;" for="gender">Gender <span class="span_req">*</span></label>
													<div class="form-check form-check-inline">
														<input <?php if($oldgender == 'Male'){ echo 'checked'; } ?> class="form-check-input" type="radio" id="male" value="Male" name="gender" checked>
														<label class="form-check-label" for="male">Male</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" <?php if($oldgender == 'Female'){ echo 'checked'; } ?> type="radio" id="female" value="Female" name="gender">
														<label class="form-check-label" for="female">Female</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" <?php if($oldgender == 'Other'){ echo 'checked'; } ?> type="radio" id="other" value="Other" name="gender">
														<label class="form-check-label" for="other">Other</label>
													</div>
													@if ($errors->has('gender'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('gender') }}</strong>
														</span>
													@endif
												</div>
											</div>

											<div class="col-3 col-md-3 col-lg-3">
												<div class="form-group">
													<label for="dob">Date of Birth</label>
													<div class="input-group" style="width: 90%;">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{{ Form::text('dob', '', array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
														@if ($errors->has('dob'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('dob') }}</strong>
															</span>
														@endif
													</div>
												</div>
											</div>

											<div class="col-3 col-md-3 col-lg-3">
												<div class="form-group">
													<label for="age">Age</label>
													<div class="input-group" style="width: 90%;">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{{ Form::text('age', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
														@if ($errors->has('age'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('age') }}</strong>
															</span>
														@endif
													</div>
												</div>
											</div>

                                            <div class="col-3 col-md-3 col-lg-3">
												<div class="form-group">
													<label for="martial_status">Marital Status</label>
													<select style="padding: 0px 5px;width: 165px;" name="martial_status" id="marital-status-select" class="form-control">
														<option value="">Select Marital Status</option>
														<option value="Married" <?php if(old('martial_status') == 'Married'){ echo 'selected'; } ?>>Married</option>
														<option <?php if(old('martial_status') == 'Never Married'){ echo 'selected'; } ?> value="Never Married">Never Married</option>
														<option <?php if(old('martial_status') == 'Engaged'){ echo 'selected'; } ?> value="Engaged">Engaged</option>
														<option <?php if(old('martial_status') == 'Divorced'){ echo 'selected'; } ?> value="Divorced">Divorced</option>
														<option <?php if(old('martial_status') == 'Separated'){ echo 'selected'; } ?> value="Separated">Separated</option>
														<option <?php if(old('martial_status') == 'De facto'){ echo 'selected'; } ?> value="De facto">De facto</option>
														<option <?php if(old('martial_status') == 'Widowed'){ echo 'selected'; } ?> value="Widowed">Widowed</option>
														<option <?php if(old('martial_status') == 'Others'){ echo 'selected'; } ?> value="Others">Others</option>
													</select>
													@if ($errors->has('martial_status'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('martial_status') }}</strong>
														</span>
													@endif
												</div>
											</div>
                                        </div>


                                        <!--- ----------------------------  Start Contact Fields------------------------ --->
                                        <div id="contact-wrapper">
                                            <div class="contact-group row mb-3" id="row-new">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="contact_type_new">Contact Type <span style="color:#ff0000;">*</span></label>
                                                        <select style="padding: 0px 5px;" name="contact_type[]" id="contact_type_new" class="form-control contactCls" data-valid="required">
                                                            <option value="Personal">Personal</option>
                                                            <option value="Office">Office</option>
                                                        </select>
                                                        <input type="hidden" name="contact_type_hidden[]" id="contact_type_new_hidden" value="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="phone_new">Contact No.<span style="color:#ff0000;">*</span></label>
                                                        <div class="cus_field_input">
                                                            <div class="country_code">
                                                                <input class="telephone" id="telephone_new" type="tel" name="country_code[]">
                                                            </div>
                                                            <input type="text" name="phone[]" class="form-control tel_input" id="phone_new" placeholder="Enter Phone Number" autocomplete="off">
                                                            <div class="error-message" id="error-phone_new">Phone number must be min 10 digits.</div>
                                                            <div class="duplicate-error" id="duplicate-phone_${contactIndex}"  style="display:none;color:red;">This phone number is already in use.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-primary add-button" data-index="new">+</button>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                        $(document).ready(function() {
                                            var contactIndex = 1; // Start index for new rows based on existing contacts

                                            var contact_type_new_val = $('#contact_type_new').val();
                                            $('#contact_type_new_hidden').val(contact_type_new_val);
                                            $(document).on('change', '#contact_type_new', function() {
                                                var contact_type_new_val = $('#contact_type_new').val();
                                                $('#contact_type_new_hidden').val(contact_type_new_val);
                                            });

                                            function addNewContactRow(afterIndex) {
                                                var newRow = `
                                                <div class="contact-group row mb-3" id="row-${contactIndex}">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="contact_type_${contactIndex}">Contact Type <span style="color:#ff0000;">*</span></label>
                                                            <select style="padding: 0px 5px;" name="contact_type[]" id="contact_type_${contactIndex}" class="form-control contactCls" data-valid="required">
                                                                <option value="Personal">Personal</option>
                                                                <option value="Office">Office</option>
                                                            </select>
                                                            <input type="hidden" name="contact_type_hidden[]" id="contact_type_hidden_${contactIndex}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="phone_${contactIndex}">Contact No.<span style="color:#ff0000;">*</span></label>
                                                            <div class="cus_field_input">
                                                                <div class="country_code">
                                                                    <input class="telephone" id="telephone_new_${contactIndex}" type="tel" name="country_code[]">
                                                                </div>
                                                                <input type="text" name="phone[]" class="form-control tel_input" id="phone_${contactIndex}" placeholder="Enter Phone Number" autocomplete="off">
                                                                <div class="error-message" id="error-phone_${contactIndex}">Phone number must be min 10 digits.</div>
                                                                <div class="duplicate-error" id="duplicate-phone_${contactIndex}" style="display:none;color:red;">This phone number is already in use.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-primary add-button" data-index="${contactIndex}">+</button>
                                                    </div>
                                                </div>`;

                                                if (afterIndex === 'new') {
                                                    $('#contact-wrapper').append(newRow);
                                                } else {
                                                    $(`#row-${afterIndex}`).after(newRow);
                                                }
                                                $(".telephone").intlTelInput();

                                                //console.log('contactIndex=='+contactIndex);
                                                var contact_type_upd_val_next = $('#contact_type_'+contactIndex).val();
                                                $('#contact_type_hidden_'+contactIndex).val(contact_type_upd_val_next);

                                                $(document).on('change', '.contactCls', function() {
                                                    var contact_type_id = $(this).attr('id');
                                                    var contact_type_arr = contact_type_id.split('_');
                                                    if( contact_type_arr.length >0 ){
                                                        var contactVal = $('#contact_type_'+contact_type_arr[2]).val();
                                                        //console.log('contactVal==='+contactVal);
                                                        $('#contact_type_hidden_'+contact_type_arr[2]).val(contactVal);
                                                    }
                                                });
                                                contactIndex++; // Increment index for next row
                                            }

                                            function validatePhoneNumber($input) {
                                                var phone = $input.val().trim();
                                                var $row = $input.closest('.contact-group');
                                                var $error = $row.find('.error-message');

                                                //if (/^\d{10}$/.test(phone)) {
                                                if (phone.length >= 10) {
                                                    $input.removeClass('error-border');
                                                    $error.hide();
                                                    return true;
                                                } else {
                                                    $input.addClass('error-border');
                                                    $error.show();
                                                    return false;
                                                }
                                            }

                                            function checkPhoneNumberUniqueness(phone, $input) {
                                                var $row = $input.closest('.contact-group');
                                                var $duplicateError = $row.find('.duplicate-error');

                                                $.ajax({
                                                    url: '{{ route("check.phone") }}', // Ensure route is correct
                                                    method: 'POST',
                                                    data: {
                                                        phone: phone,
                                                        _token: '{{ csrf_token() }}'
                                                    },
                                                    success: function(response) {
                                                        if (response.status === 'exists') {
                                                            $duplicateError.show();
                                                            $input.addClass('error-border');
                                                            $('.btn_submit').prop('disabled', true); // Disable submit if duplicate found
                                                        } else {
                                                            $duplicateError.hide();
                                                            $input.removeClass('error-border');
                                                            $('.btn_submit').prop('disabled', false); // Enable submit if valid
                                                        }
                                                    },
                                                    error: function() {
                                                        alert('An error occurred while checking the phone number.');
                                                    }
                                                });
                                            }

                                            function makePreviousRowsReadonlyContact() {
                                                $('.contact-group').not(`#row-${contactIndex - 1}`).each(function() {
                                                    //$(this).find('input, select').addClass('readonly').prop('readonly', true).attr('disabled', true);

                                                    $(this).find('input').attr('readonly', true);
                                                    $(this).find('select').attr('disabled', true);
                                                });
                                            }

                                            $(document).on('blur input', 'input.tel_input', function() {
                                                var $this = $(this);
                                                var phone = $this.val().trim();

                                                var $duplicateError = $this.closest('.contact-group').find('.duplicate-error');
                                                $duplicateError.hide(); // Hide the duplicate message when the user starts editing

                                                if (validatePhoneNumber($this)) {
                                                    checkPhoneNumberUniqueness(phone, $this);
                                                }
                                            });

                                            $(document).on('click', '.add-button', function() {
                                                var index = $(this).data('index');
                                                var $currentRow = index === 'new' ? $('#row-new') : $(`#row-${index}`);

                                                if (validatePhoneNumber($currentRow.find('input.tel_input'))) {
                                                    if (index === 'new') {
                                                        addNewContactRow('new'); // Add a new row at the end
                                                        makePreviousRowsReadonlyContact(); // Make previous rows readonly
                                                        $currentRow.find('button').prop('disabled', true); // Disable current row's button
                                                    } else {
                                                        var isConfirmed = confirm("Are you sure you want to freeze the current row?");
                                                        if (isConfirmed) {
                                                            addNewContactRow(index); // Add a new row after the current row
                                                            makePreviousRowsReadonlyContact(); // Make previous rows readonly
                                                            $currentRow.find('button').prop('disabled', true); // Disable current row's button
                                                        }
                                                    }
                                                } else {
                                                    alert("Please correct the errors in the current row before adding a new one.");
                                                }
                                            });
                                        });
                                        </script>
                                        <!--- ----------------------------  End Contact Fields ---------------------------------------->

                                        <!--- --------------------------------  Start Email Fields----------------------------------- --->


                                        <div id="email-fields-wrapper">
                                            <div class="email-fields row mb-3" id="row-new">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="email_type_new">Email Type <span style="color:#ff0000;">*</span></label>
                                                        <select style="padding: 0px 5px;" name="email_type[]" id="email_type_new" class="form-control emailCls" data-valid="required">
                                                            <option value="Personal">Personal</option>
                                                            <option value="Business">Business</option>
                                                            <option value="Brother">Brother</option>
                                                            <option value="Father">Father</option>
                                                            <option value="Mother">Mother</option>
                                                            <option value="Uncle">Uncle</option>
                                                            <option value="Auntie">Auntie</option>
                                                        </select>
                                                        <input type="hidden" name="email_type_hidden[]" id="email_type_new_hidden" value="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="email_new">Email <span style="color:#ff0000;">*</span></label>
                                                        <input type="email" data-valid="required" name="email[]" class="form-control email-input" id="email_new" placeholder="Enter Email" autocomplete="off">
                                                        <div class="error-message" id="error-email_new">Please enter a valid email address.</div>

                                                        <div class="duplicate-error" id="duplicate-email_new" style="display:none;color:red;">This email is already taken.</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-primary new-email-row-btn" id="new-email-row">+</button>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                        $(document).ready(function() {
                                            var emailIndex = 1; // Start index for new rows
                                            var email_type_new_val = $('#email_type_new').val();
                                            $('#email_type_new_hidden').val(email_type_new_val);
                                            $(document).on('change', '#email_type_new', function() {
                                                var email_type_new_val = $('#email_type_new').val();
                                                $('#email_type_new_hidden').val(email_type_new_val);
                                            });
                                            function addNewRow() {
                                                var newRow = `<div class="email-fields row mb-3" id="row-${emailIndex}">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="email_type_${emailIndex}">Email Type <span style="color:#ff0000;">*</span></label>
                                                            <select style="padding: 0px 5px;" name="email_type[]" id="email_type_${emailIndex}" class="form-control emailCls" data-valid="required">
                                                                <option value="Personal">Personal</option>
                                                                <option value="Business">Business</option>
                                                                <option value="Brother">Brother</option>
                                                                <option value="Father">Father</option>
                                                                <option value="Mother">Mother</option>
                                                                <option value="Uncle">Uncle</option>
                                                                <option value="Auntie">Auntie</option>
                                                            </select>
                                                            <input type="hidden" name="email_type_hidden[]" id="email_type_hidden_${emailIndex}">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="email_${emailIndex}">Email <span style="color:#ff0000;">*</span></label>
                                                            <input type="email" name="email[]" class="form-control email-input" id="email_${emailIndex}" placeholder="Enter Email" autocomplete="off">
                                                            <div class="error-message" id="error-email_${emailIndex}">Please enter a valid email address.</div>

                                                        <div class="duplicate-error" id="duplicate-email_${emailIndex}" style="display:none;color:red;">This email is already taken.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 d-flex align-items-center">
                                                        <button type="button" class="btn btn-primary new-email-row-btn" id="new-email-row-${emailIndex}" data-row-id="${emailIndex}">+</button>
                                                    </div>
                                                </div>`;

                                                $('#email-fields-wrapper').append(newRow);
                                                //console.log('emailIndex=='+emailIndex);
                                                var email_type_upd_val_next = $('#email_type_'+emailIndex).val();
                                                $('#email_type_hidden_'+emailIndex).val(email_type_upd_val_next);

                                                $(document).on('change', '.emailCls', function() {
                                                    var email_type_id = $(this).attr('id');
                                                    var email_type_arr = email_type_id.split('_');
                                                    if( email_type_arr.length >0 ){
                                                        var emailVal = $('#email_type_'+email_type_arr[2]).val();
                                                        //console.log('emailVal==='+emailVal);
                                                        $('#email_type_hidden_'+email_type_arr[2]).val(emailVal);
                                                    }
                                                });

                                                emailIndex++; // Increment index for next row
                                            }

                                            function validateCurrentRow() {
                                                var $currentRow = $('.email-fields:last');
                                                var email = $currentRow.find('input[name="email[]"]').val().trim();
                                                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email validation regex
                                                var $error = $currentRow.find('.error-message');

                                                if (!email || !emailRegex.test(email)) {
                                                    $error.show();
                                                    return false;
                                                }

                                                // Check for duplicate email in existing rows
                                                var isDuplicate = false;
                                                $('input[name="email[]"]').each(function() {
                                                    if ($(this).val().trim() === email && $(this).attr('id') !== $currentRow.find('input[name="email[]"]').attr('id')) {
                                                        isDuplicate = true;
                                                        return false; // Exit loop
                                                    }
                                                });

                                                if (isDuplicate) {
                                                    alert('This email is already entered.');
                                                    $error.show();
                                                    return false;
                                                }

                                                $error.hide();
                                                return true;
                                            }

                                            function makePreviousRowsReadonly() {
                                                $('.email-fields').each(function() {
                                                    if (!$(this).is(':last')) {
                                                        //$(this).find('input, select').attr('readonly', true).attr('disabled', true); // Make readonly and disabled
                                                        $(this).find('input').attr('readonly', true);
		                                                $(this).find('select').attr('disabled', true);
                                                    }
                                                });
                                            }

                                            $(document).on('click', '.new-email-row-btn', function() {
                                                if (validateCurrentRow()) {
                                                    var isConfirmed = confirm("Are you sure you want to freeze the current row?");
                                                    if (isConfirmed) {
                                                        makePreviousRowsReadonly(); // Make previous rows readonly
                                                        addNewRow();
                                                    }
                                                } else {
                                                    alert("Please correct the errors in the current row before adding a new one.");
                                                }
                                            });

                                            $(document).on('input', 'input.email-input', function() {
                                                var $currentRow = $(this).closest('.email-fields');
                                                var email = $(this).val().trim();
                                                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email validation regex
                                                var $error = $currentRow.find('.error-message');

                                                if (email && emailRegex.test(email)) {
                                                    $error.hide();
                                                } else {
                                                    $error.show();
                                                }
                                            });

                                            // Event on blur for email input
                                            $(document).on('blur input', 'input.email-input', function() {
                                                var $this = $(this);
                                                var email = $this.val().trim();
                                                var email_id_attr = $(this).attr('id');
                                                //console.log('email_id_attr='+email_id_attr);

                                                var $duplicateError = $this.siblings('.duplicate-error');
                                                var duplicateErrorId = $this.siblings('.duplicate-error').attr('id');
                                                //console.log('duplicateErrorId='+duplicateErrorId);


                                                var email_id_attr_arr = email_id_attr.split('_');
                                                console.log('email_id_attr_arr='+email_id_attr_arr[1]);

                                                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                                                // Validate email format
                                                if (email && emailRegex.test(email)) {
                                                    $this.siblings('.error-message').hide();

                                                    // Perform AJAX check for email uniqueness
                                                    $.ajax({
                                                        url: '{{ route("check.email") }}',
                                                        method: 'POST',
                                                        data: {
                                                            email: email,
                                                            _token: '{{ csrf_token() }}'
                                                        },
                                                        success: function(response) {
                                                            if (response.status === 'exists') {
                                                                //$('#duplicate-email_new').show();  duplicate-email_1
                                                                $('#'+duplicateErrorId).show();
                                                                $('#'+email_id_attr).attr('data-valid', 'required');
                                                                if( email_id_attr_arr[1] == 'new'){
                                                                    $('#new-email-row').prop('disabled', true);
                                                                } else {
                                                                    $('#new-email-row-'+email_id_attr_arr[1]).prop('disabled', true);
                                                                }

                                                                $('.btn_submit').prop('disabled', true);
                                                            } else {
                                                                //$duplicateError.hide(); // Email is available
                                                                $('#'+duplicateErrorId).hide();
                                                                $('.btn_submit').prop('disabled', false);

                                                                if( email_id_attr_arr[1] == 'new'){
                                                                    $('#new-email-row').prop('disabled', false);
                                                                } else {
                                                                    $('#new-email-row-'+email_id_attr_arr[1]).prop('disabled', false);
                                                                }
                                                            }
                                                        },
                                                        error: function() {
                                                            alert('An error occurred while checking the email.');
                                                        }
                                                    });
                                                } else {
                                                    $this.siblings('.error-message').show(); // Invalid email format
                                                    $duplicateError.hide(); // Hide duplicate error if any
                                                }
                                            });
                                        });
                                        </script>
                                        <!--- --------------------------------  End Email Fields----------------------------------- --->



                                        <!------------------------------------  Start Country and Visa Fields --------------------------->
                                        <div class="fields row mb-3" id="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="visa_country">Country of Passport <span style="color:#ff0000;">*</span></label>
                                                    <select name="visa_country[]" id="visa_country" class="form-control field-input" data-valid="required">
                                                        <option value="India" {{ @$fetchedData->country_passport == 'India' ? 'selected' : '' }}>India</option>
                                                        <option value="Australia" {{ @$fetchedData->country_passport == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                        <?php
                                                        foreach (\App\Models\Country::all() as $list) {
                                                            // Skip India and Australia since they've already been added manually
                                                            if ($list->name == 'India' || $list->name == 'Australia') {
                                                                continue;
                                                            }
                                                            $selected = (@$fetchedData->country_passport == $list->name) ? 'selected' : '';
                                                        ?>
                                                            <option value="{{ $list->name }}" {{ $selected }}>{{ $list->name }}</option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <meta name="csrf-token" content="{{ csrf_token() }}">
                                        <title>Country and Visa Fields</title>
                                        <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">-->
                                        <style>
                                            .error-border {
                                                border: 2px solid red; /* Red border for incomplete rows */
                                            }
                                            .readonly {
                                                background-color: #f5f5f5; /* Light grey background for readonly fields */
                                                cursor: not-allowed;
                                            }
                                            .disabled-button {
                                                cursor: not-allowed;
                                                opacity: 0.5;
                                            }
                                            .description-container {
                                                display: none; /* Initially hide the description field */
                                            }
                                        </style>


                                        <div id="fields-wrapper">
                                            <div class="fields row mb-3 visa-full-cu" id="row-0">
                                                <div class="col-sm-12">
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="visa_type_0">Visa Type <span style="color:#ff0000;">*</span></label>
                                                        <select name="visa_type[]" id="visa_type_0" class="form-control field-input visaTypeCls" data-valid="required">
                                                            <option value="">Select Visa Type</option>
                                                            <!--<option value="Student Visa">Student Visa</option>
                                                            <option value="Work Visa">Work Visa</option>
                                                            <option value="Tourist Visa">Tourist Visa</option>-->
                                                            @foreach(\App\Models\Matter::select('id','title','nick_name')->where('status',1)->orderby('id','ASC')->get() as $matterlist)
                                                            <option value="{{ $matterlist->id }}">{{ $matterlist->title }} ({{ @$matterlist->nick_name }})</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="visa_type_hidden[]" id="visa_type_hidden_0" value="">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="visa_expiry_date_0">Visa Expiry Date</label>
                                                        <input type="date" name="visa_expiry_date[]" id="visa_expiry_date_0" class="form-control field-input">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 description-container" id="description-container-0">
                                                    <div class="form-group">
                                                        <label for="visa_description_0">Visa Description</label>
                                                        <input type="text" name="visa_description[]" id="visa_description_0" class="form-control field-input" rows="3">
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-primary add-row-visacountry disabled-button" id="add-row-visacountry-0" data-row-id="0" disabled>+</button>
                                                </div>
                                            </div>
                                        </div>

                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                                        <script>
                                        $(document).ready(function() {

                                            var visa_type_new_val = $('#visa_type_0').val();
                                            $('#visa_type_hidden_0').val(visa_type_new_val);

                                            $(document).on('change', '.visaTypeCls', function() {
                                                var visa_type_id = $(this).attr('id');
                                                var visa_type_arr = visa_type_id.split('_');
                                                if( visa_type_arr.length >0 ){
                                                    var visatypeVal = $('#visa_type_'+visa_type_arr[2]).val();
                                                    //console.log('visatypeVal==='+visatypeVal);
                                                    $('#visa_type_hidden_'+visa_type_arr[2]).val(visatypeVal);
                                                }
                                            });

                                            // Function to show or hide fields based on the selected country
                                            function toggleFieldsBasedOnCountry() {
                                                var selectedCountry = $('#visa_country').val(); // Get the selected country

                                                if (selectedCountry === 'Australia') {
                                                    // Hide the div with the class 'visa-full-cu'
                                                    $('.visa-full-cu').hide();
                                                    if( $('.visa-full-cu').length >0 ) {
                                                        $('.visa-full-cu').each(function(index) {
                                                            $('#visa_type_'+index).removeAttr('data-valid');
                                                        });
                                                    }
                                                } else {
                                                    // Show the div with the class 'visa-full-cu'
                                                    $('.visa-full-cu').show();
                                                    if( $('.visa-full-cu').length >0 ) {
                                                        $('.visa-full-cu').each(function(index) {
                                                            $('#visa_type_'+index).attr('data-valid', 'required');
                                                        });
                                                    }
                                                }
                                            }

                                            // Trigger when the country dropdown changes
                                            $('#visa_country').on('change', function() {
                                                toggleFieldsBasedOnCountry(); // Check the selected country and toggle visibility accordingly
                                            });

                                            // Initial check on page load
                                            toggleFieldsBasedOnCountry(); // Check on page load
                                        });


                                        $(document).ready(function() {
                                            var rowIndex = 1; // Start index for new rows

                                            // Function to check if all fields in a row are filled
                                            function validateRow(rowId) {
                                                var isValid = true;
                                                $('#row-' + rowId + ' .field-input').each(function() {
                                                    if ($(this).val() === "" && $(this).attr('data-valid') === "required") {
                                                        isValid = false;
                                                        return false; // Exit each loop if any field is empty and required
                                                    }
                                                });

                                                return isValid;
                                            }

                                            // Enable or disable the + button based on row validation
                                            function toggleAddButton(rowId) {
                                                var selectedCountry = $('#visa_country').val();
                                                if (validateRow(rowId) && selectedCountry !== 'Australia') {
                                                    $('#add-row-visacountry-' + rowId).removeClass('disabled-button').prop('disabled', false);
                                                } else {
                                                    $('#add-row-visacountry-' + rowId).addClass('disabled-button').prop('disabled', true);
                                                }
                                            }

                                            // Show the description field based on Visa Type selection
                                            function toggleDescriptionField(rowId) {
                                                var visaType = $('#visa_type_' + rowId).val();
                                                if (visaType) { // Show the textarea if any visa type is selected
                                                    $('#description-container-' + rowId).show();
                                                } else {
                                                    $('#description-container-' + rowId).hide();
                                                }
                                            }

                                            // Function to disable/enable fields based on country selection
                                            function toggleFields(rowId) {
                                                var selectedCountry = $('#visa_country').val();
                                                if (selectedCountry === 'Australia') {
                                                    $('#row-' + rowId + ' .field-input').attr('disabled', true).addClass('readonly');
                                                    $('#add-row-visacountry-' + rowId).addClass('disabled-button').prop('disabled', true);
                                                } else {
                                                    $('#row-' + rowId + ' .field-input').attr('disabled', false).removeClass('readonly');
                                                    toggleAddButton(rowId); // Re-evaluate the add button
                                                }
                                            }


                                            // Validate and enable the + button when fields change
                                            $(document).on('change', '.field-input, #visa_country', function() {
                                                var rowId = $(this).closest('.fields').attr('id').split('-')[1];
                                                toggleFields(rowId); // Check if fields need to be disabled
                                                toggleAddButton(rowId);
                                                toggleDescriptionField(rowId);
                                            });

                                            // Handle click event for adding a new row with confirmation
                                            $(document).on('click', '.add-row-visacountry', function() {
                                                var currentRowId = $(this).data('row-id');
                                                var selectedCountry = $('#visa_country').val();

                                                if (validateRow(currentRowId) && selectedCountry !== 'Australia') {
                                                    // Confirm freezing the current row
                                                    var isConfirmed = confirm("Are you sure you want to freeze the current row?");

                                                    if (isConfirmed) {
                                                        // Freeze the current row by making fields readonly
                                                        //$('#row-' + currentRowId + ' input.field-input').addClass('readonly').attr('disabled', true);

                                                        $('#row-' + currentRowId + ' input.field-input').attr('readonly', 'readonly');
                                                        $('#row-' + currentRowId + ' select.field-input').attr('disabled', true);

                                                        //$('#row-' + currentRowId + ' .field-input').addClass('readonly').attr('disabled', true);
                                                        $('#add-row-visacountry-' + currentRowId).addClass('disabled-button').prop('disabled', true);

                                                        // Add a new row
                                                        var newRow = `<div class="fields row mb-3 visa-full-cu" id="row-${rowIndex}">
                                                            <div class="col-sm-3">
                                                                <div class="form-group">
                                                                    <label for="visa_type_${rowIndex}">Visa Type <span style="color:#ff0000;">*</span></label>
                                                                    <select name="visa_type[]" id="visa_type_${rowIndex}" class="form-control field-input visaTypeCls" data-valid="required">
                                                                        <option value="">Select Visa Type</option>
                                                                        <!--<option value="Student Visa">Student Visa</option>
                                                                        <option value="Work Visa">Work Visa</option>
                                                                        <option value="Tourist Visa">Tourist Visa</option>-->
                                                                        @foreach(\App\Models\Matter::select('id','title','nick_name')->where('status',1)->orderby('id','ASC')->get() as $matterlist)
                                                                        <option value="{{ $matterlist->id }}">{{ $matterlist->title }} ({{ @$matterlist->nick_name }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <input type="hidden" name="visa_type_hidden[]" id="visa_type_hidden_${rowIndex}">
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div class="form-group">
                                                                    <label for="visa_expiry_date_${rowIndex}">Visa Expiry Date</label>
                                                                    <input type="date" name="visa_expiry_date[]" id="visa_expiry_date_${rowIndex}" class="form-control field-input">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 description-container" id="description-container-${rowIndex}">
                                                                <div class="form-group">
                                                                    <label for="visa_description_${rowIndex}">Visa Description</label>
                                                                    <input type="text" name="visa_description[]" id="visa_description_${rowIndex}" class="form-control field-input" rows="3">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1 d-flex align-items-center">
                                                                <button type="button" class="btn btn-primary add-row-visacountry disabled-button" id="add-row-visacountry-${rowIndex}" data-row-id="${rowIndex}" disabled>+</button>
                                                            </div>
                                                        </div>`;

                                                        $('#fields-wrapper').append(newRow);

                                                        //console.log('rowIndex=='+rowIndex);
                                                        var visa_type_upd_val_next = $('#visa_type_'+rowIndex).val();
                                                        $('#visa_type_hidden_'+rowIndex).val(visa_type_upd_val_next);

                                                        $(document).on('change', '.visaTypeCls', function() {
                                                            var visa_type_id = $(this).attr('id');
                                                            var visa_type_arr = visa_type_id.split('_');
                                                            if( visa_type_arr.length >0 ){
                                                                var visaVal = $('#visa_type_'+visa_type_arr[2]).val();
                                                                //console.log('visaVal==='+visaVal);
                                                                $('#visa_type_hidden_'+visa_type_arr[2]).val(visaVal);
                                                            }
                                                        });

                                                        rowIndex++; // Increment index for new rows
                                                    }
                                                }
                                            });

                                            // Initial state setup for existing rows
                                            $('.fields').each(function() {
                                                var rowId = $(this).attr('id').split('-')[1];
                                                toggleFields(rowId);
                                                toggleAddButton(rowId);
                                                toggleDescriptionField(rowId);
                                            });
                                        });
                                        </script>

                                        <!------------------------------------  End Country and Visa Fields --------------------------->

                                        <!------------------------------------  Start Address Auto-Fill --------------------------->
                                        <style>
                                            .autocomplete-items {
                                                border: 1px solid #d4d4d4;
                                                border-bottom: none;
                                                border-top: none;
                                                z-index: 99;
                                                top: 100%;
                                                left: 0;
                                                right: 0;
                                                background-color: white;
                                                max-height: 100px;
                                                overflow-y: auto;
                                                position: absolute;
                                                margin: -16px 12px;
                                            }
                                            .autocomplete-item {
                                                padding: 10px;
                                                cursor: pointer;
                                            }
                                            .autocomplete-item:hover {
                                                background-color: #d3d3d3;
                                            }
                                            .error-message {
                                                color: red;
                                                margin-top: 10px;
                                            }
                                            .error-row {
                                                border: 2px solid red;
                                            }
                                            .error-border {
                                                border: 1px solid red;
                                            }
                                            .form-control {
                                                border-radius: 0.25rem;
                                            }
                                        </style>

                                        <div id="address-fields-wrapper">
                                            <!-- Blank row for adding new addresses -->
                                            <div class="address-fields row mb-3">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="zip">Post Code</label>
                                                        <input type="text" name="zip[]" class="form-control postal_code" autocomplete="off" placeholder="Enter Post Code">
                                                        <div class="autocomplete-items"></div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="address">Address</label>
                                                        <input type="text" name="address[]" class="form-control address-input" autocomplete="off" placeholder="Search Box">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="regional_code">Regional Code Info</label>
                                                        <input type="text" name="regional_code[]" class="form-control regional_code_info" placeholder="Regional Code info" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-primary add-row-btn">+</button>
                                                </div>
                                            </div>

                                            <div id="error-message" class="error-message" style="display: none;"></div>
                                        </div>

                                        <script>
                                        $(document).ready(function() {
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                            });

                                            function addNewRow() {
                                                // Remove error classes from the last row
                                                $('.address-fields:last').find('input').removeClass('error-border');

                                                var newRow = `<div class="address-fields row mb-3">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="zip">Post Code</label>
                                                            <input type="text" name="zip[]" class="form-control postal_code" autocomplete="off" placeholder="Enter Post Code">
                                                            <div class="autocomplete-items"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="address">Address</label>
                                                            <input type="text" name="address[]" class="form-control address-input" autocomplete="off" placeholder="Search Box">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="regional_code">Regional Code Info</label>
                                                            <input type="text" name="regional_code[]" class="form-control regional_code_info" placeholder="Regional Code info" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 d-flex align-items-center">
                                                        <button type="button" class="btn btn-primary add-row-btn">+</button>
                                                    </div>
                                                </div>`;
                                                $('#address-fields-wrapper').append(newRow);
                                                $('#error-message').hide();
                                            }

                                            function validateCurrentRow() {
                                                var $currentRow = $('.address-fields:last');
                                                var zip = $currentRow.find('input[name="zip[]"]').val().trim();
                                                var address = $currentRow.find('input[name="address[]"]').val().trim();

                                                // Remove error class initially
                                                $currentRow.find('input').removeClass('error-border');

                                                if (zip === '' || address === '') {
                                                    if (zip === '') {
                                                        $currentRow.find('input[name="zip[]"]').addClass('error-border');
                                                    }
                                                    if (address === '') {
                                                        $currentRow.find('input[name="address[]"]').addClass('error-border');
                                                    }
                                                    return false;
                                                }

                                                return true;
                                            }

                                            function freezeCurrentRow() {
                                                var $currentRow = $('.address-fields:last');
                                                // Set the current row's fields to readonly
                                                $currentRow.find('input').attr('readonly', true);
                                                $currentRow.find('button').prop('disabled', true);
                                                $currentRow.addClass('frozen'); // Optionally, add a class to indicate the row is frozen
                                            }

                                            $(document).on('click', '.add-row-btn', function() {
                                                if (validateCurrentRow()) {
                                                    var isConfirmed = confirm("Are you sure want to freeze the current row?");
                                                    if (isConfirmed) {
                                                        freezeCurrentRow(); // Freeze the current row
                                                        addNewRow(); // Add a new row
                                                    }
                                                }
                                            });

                                            function getRegionalCodeInfo(postCode){
                                                //2259, 2264 to 2308, 2500 to 2526, 2528 to 2535 and 2574
                                                if(
                                                    ( postCode ==2259)
                                                    ||
                                                    ( postCode >=2264 && postCode <= 2308 )
                                                    ||
                                                    ( postCode >=2500 && postCode <= 2526 )
                                                    ||
                                                    ( postCode >=2528 && postCode <= 2535 )
                                                    ||
                                                    ( postCode == 2574)
                                                ){
                                                    var postCodeInfo = "Cities and major regional centres of New South Wales";
                                                }

                                                //2250 to 2258, 2260 to 2263, 2311 to 2490, 2527, 2536 to 2551, 2575 to 2739, 2753 to 2754, 2756 to 2758 and 2773 to 2898
                                                else if(
                                                    ( postCode >=2250 && postCode <= 2258 )
                                                    ||
                                                    ( postCode >=2260 && postCode <= 2263 )
                                                    ||
                                                    ( postCode >=2311 && postCode <= 2490 )
                                                    ||
                                                    ( postCode == 2527)
                                                    ||
                                                    ( postCode >=2536 && postCode <= 2551 )
                                                    ||
                                                    ( postCode >=2575 && postCode <= 2739 )
                                                    ||
                                                    ( postCode >=2753 && postCode <= 2754 )
                                                    ||
                                                    ( postCode >=2756 && postCode <= 2758 )
                                                    ||
                                                    ( postCode >=2773 && postCode <= 2898 )
                                                ){
                                                    var postCodeInfo = "Regional centres and other regional areas of New South Wales";
                                                }

                                                //3211 to 3232, 3235, 3240, 3328, 3330 to 3333, 3340 and 3342
                                                else if(
                                                    ( postCode >=3211 && postCode <= 3232 )
                                                    ||
                                                    ( postCode == 3235)
                                                    ||
                                                    ( postCode == 3240 )
                                                    ||
                                                    ( postCode == 3328)
                                                    ||
                                                    ( postCode >=3330 && postCode <= 3333 )
                                                    ||
                                                    ( postCode == 3340)
                                                    ||
                                                    ( postCode == 3342)
                                                ){
                                                    var postCodeInfo = "Cities and major regional centres of Victoria";
                                                }

                                                //3097 to 3099, 3139, 3233 to 3234, 3236 to 3239, 3241 to 3325, 3329, 3334, 3341,
                                                //3345 to 3424, 3430 to 3799, 3809 to 3909, 3912 to 3971 and 3978 to 3996
                                                else if(
                                                    ( postCode >=3097 && postCode <= 3099 )
                                                    ||
                                                    ( postCode == 3139)
                                                    ||
                                                    ( postCode >= 3233 && postCode <= 3234 )
                                                    ||
                                                    ( postCode >= 3236 && postCode <= 3239 )
                                                    ||
                                                    ( postCode >= 3241 && postCode <= 3325 )
                                                    ||
                                                    ( postCode == 3329 )
                                                    ||
                                                    ( postCode == 3334 )
                                                    ||
                                                    ( postCode == 3341 )
                                                    ||
                                                    ( postCode >= 3345 && postCode <= 3424 )
                                                    ||
                                                    ( postCode >= 3430 && postCode <= 3799 )
                                                    ||
                                                    ( postCode >= 3809 && postCode <= 3909 )
                                                    ||
                                                    ( postCode >= 3912 && postCode <= 3971 )
                                                    ||
                                                    ( postCode >= 3978 && postCode <= 3996 )
                                                ){
                                                    var postCodeInfo = "Regional centres and other regional areas of Victoria";
                                                }

                                                //4019 to 4022*, 4025*, 4037*, 4074*, 4076 to 4078*, 4207 to 4275, 4300 to 4301*,
                                                //4303 to 4305*, 4500 to 4506*, 4508 to 4512*, 4514 to 4516*, 4517 to 4519, 4521*,
                                                //4550 to 4551, 4553 to 4562, 4564 to 4569 and 4571 to 4575
                                                else if(
                                                    ( postCode >=4019 && postCode <= 4022 )
                                                    ||
                                                    ( postCode == 4025)
                                                    ||
                                                    ( postCode == 4037)
                                                    ||
                                                    ( postCode == 4074 )
                                                    ||
                                                    ( postCode >= 4076 && postCode <= 4078 )
                                                    ||
                                                    ( postCode >= 4207 && postCode <= 4275 )
                                                    ||
                                                    ( postCode >= 4300 && postCode <= 4301 )
                                                    ||
                                                    ( postCode >= 4303 && postCode <= 4305 )
                                                    ||
                                                    ( postCode >= 4500 && postCode <= 4506 )
                                                    ||
                                                    ( postCode >= 4508 && postCode <= 4512 )
                                                    ||
                                                    ( postCode >= 4514 && postCode <= 4516 )
                                                    ||
                                                    ( postCode >= 4517 && postCode <= 4519 )
                                                    ||
                                                    ( postCode == 4521 )
                                                    ||
                                                    ( postCode >= 4550 && postCode <= 4551 )
                                                    ||
                                                    ( postCode >= 4553 && postCode <= 4562 )
                                                    ||
                                                    ( postCode >= 4564 && postCode <= 4569 )
                                                    ||
                                                    ( postCode >= 4571 && postCode <= 4575 )
                                                ){
                                                    var postCodeInfo = "Cities and major regional centres of Queensland";
                                                }

                                                //4124, 4125, 4133, 4183 to 4184, 4280 to 4287, 4306 to 4498, 4507, 4552, 4563,
                                                //4570 and 4580 to 4895
                                                else if(
                                                    ( postCode == 4124 )
                                                    ||
                                                    ( postCode == 4125)
                                                    ||
                                                    ( postCode == 4133)
                                                    ||
                                                    ( postCode >= 4183 && postCode <= 4184 )
                                                    ||
                                                    ( postCode >= 4280 && postCode <= 4287 )
                                                    ||
                                                    ( postCode >= 4306 && postCode <= 4498 )
                                                    ||
                                                    ( postCode == 4507)
                                                    ||
                                                    ( postCode == 4552 )
                                                    ||
                                                    ( postCode == 4563 )
                                                    ||
                                                    ( postCode == 4570 )
                                                    ||
                                                    ( postCode >= 4580 && postCode <= 4895 )
                                                ){
                                                    var postCodeInfo = "Regional centres and other regional areas of Queensland";
                                                }

                                                //6000 to 6038, 6050 to 6083, 6090 to 6182, 6208 to 6211, 6214 and 6556 to 6558
                                                else if(
                                                    ( postCode >= 6000 && postCode <= 6038 )
                                                    ||
                                                    ( postCode >= 6050 && postCode <= 6083 )
                                                    ||
                                                    ( postCode >= 6090 && postCode <= 6182 )
                                                    ||
                                                    ( postCode >= 6208 && postCode <= 6211 )
                                                    ||
                                                    ( postCode == 6214 )
                                                    ||
                                                    ( postCode >= 6556 && postCode <= 6558 )
                                                ){
                                                    var postCodeInfo = "Cities and major regional centres of Western Australia";
                                                }

                                                //5000 to 5171, 5173 to 5174, 5231 to 5235, 5240 to 5252, 5351 and 5950 to 5960
                                                else if(
                                                    ( postCode >= 5000 && postCode <= 5171 )
                                                    ||
                                                    ( postCode >= 5173 && postCode <= 5174 )
                                                    ||
                                                    ( postCode >= 5231 && postCode <= 5235 )
                                                    ||
                                                    ( postCode >= 5240 && postCode <= 5252 )
                                                    ||
                                                    ( postCode == 5351 )
                                                    ||
                                                    ( postCode >= 5950 && postCode <= 5960 )
                                                ){
                                                    var postCodeInfo = "Cities and major regional centres of South Australia";
                                                }
                                                //7000, 7004 to 7026, 7030 to 7109, 7140 to 7151 and 7170 to 7177
                                                else if(
                                                    ( postCode == 7000 )
                                                    ||
                                                    ( postCode >= 7004 && postCode <= 7026 )
                                                    ||
                                                    ( postCode >= 7030 && postCode <= 7109 )
                                                    ||
                                                    ( postCode >= 7140 && postCode <= 7151 )
                                                    ||
                                                    ( postCode >= 7170 && postCode <= 7177 )
                                                ){
                                                    var postCodeInfo = "Cities and major regional centres of Tasmania";
                                                }
                                                else {
                                                    var postCodeInfo = '';
                                                }
                                                return postCodeInfo;
                                            }

                                            $(document).on('input', '.postal_code', function() {
                                                var postcode = $(this).val(); //console.log('postcode ='+postcode);
                                                var $row = $(this).closest('.address-fields');
                                                //console.log('postcode_length ='+postcode.length);
                                                if (postcode.length > 3) {
                                                    $.ajax({
                                                        url: '{{ route("admin.clients.updateAddress") }}',
                                                        method: 'POST',
                                                        data: { postcode: postcode },
                                                        success: function(response) {
                                                            var val_type = Array.isArray(response.localities.locality);
                                                            //console.log('val_type==='+val_type );
                                                            if(val_type){ //if type is array (means records>1)
                                                                var suggestions_cnt = response.localities.locality;
                                                                var suggestions_cnt_length = Object.keys(suggestions_cnt).length;
                                                            } else { //if type is not array (means records <=1 )
                                                                var suggestions_cnt = response.localities;
                                                                var suggestions_cnt_length = Object.keys(suggestions_cnt).length;
                                                            }
                                                            //console.log('suggestions_cnt==='+suggestions_cnt_length );

                                                            var suggestions = response.localities.locality || [];
                                                            var $autocomplete = $row.find('.autocomplete-items').empty();
                                                            //console.log('suggestion_length ='+suggestions.length);
                                                            if ( suggestions_cnt_length > 1) {
                                                                suggestions.forEach(function(suggestion) {
                                                                    var fullAddress = (suggestion.location || '') + ', ' + (suggestion.state || '');
                                                                    var $item = $('<div class="autocomplete-item"></div>')
                                                                        .text(fullAddress)
                                                                        .data('address', {
                                                                            fullAddress: fullAddress,
                                                                            postcode: suggestion.postcode || ''
                                                                        })
                                                                        .appendTo($autocomplete);

                                                                    $item.on('click', function() {
                                                                        var addressData = $(this).data('address');
                                                                        console.log('postcodeM='+addressData.postcode);
                                                                        $row.find('.address-input').val(addressData.fullAddress);
                                                                        $row.find('.postal_code').val(addressData.postcode);

                                                                        var regional_code_info = getRegionalCodeInfo(addressData.postcode);
                                                                        $row.find('.regional_code_info').val(regional_code_info);

                                                                        $autocomplete.empty();
                                                                    });
                                                                });
                                                            }
                                                            else if ( suggestions_cnt_length == 1) {
                                                                var fullAddress = (suggestions.location || '') + ', ' + (suggestions.state || '');
                                                                var $item = $('<div class="autocomplete-item"></div>')
                                                                        .text(fullAddress)
                                                                        .data('address', {
                                                                            fullAddress: fullAddress,
                                                                            postcode: suggestions.postcode || ''
                                                                        })
                                                                        .appendTo($autocomplete);

                                                                $item.on('click', function() {
                                                                    var addressData = $(this).data('address');
                                                                    console.log('postcodeS='+addressData.postcode);
                                                                    $row.find('.address-input').val(addressData.fullAddress);
                                                                    $row.find('.postal_code').val(addressData.postcode);

                                                                    var regional_code_info = getRegionalCodeInfo(addressData.postcode);
                                                                    $row.find('.regional_code_info').val(regional_code_info);

                                                                    $autocomplete.empty();
                                                                });
                                                            } else {
                                                                $autocomplete.html('<div class="autocomplete-item">No results found</div>');
                                                            }
                                                        },
                                                        error: function() {
                                                            alert('Error fetching address details.');
                                                        }
                                                    });
                                                } else {
                                                    $row.find('.autocomplete-items').empty();
                                                }
                                            });
                                        });
                                        </script>
                                        <!------------------------------------  End Address Auto-Fill --------------------------->


                                        <meta name="csrf-token" content="{{ csrf_token() }}">
                                        <div class="col-sm-12">
                                            <h5 style="color: #FFFFFF;">Client Qualification</h5>
                                            <div id="qualification-fields-wrapper">
                                                <!-- Existing Qualification Fields -->

                                                <!-- Always include a blank row for adding new qualifications -->
                                                <div class="qualification-fields row mb-6" id="row_new">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="level_new">Level </label>
                                                            <select name="level[]" id="level_new" class="form-control levelCls">
                                                                <option value="">Select Level</option>
                                                                <option value="Certificate I">Certificate I</option>
                                                                <option value="Certificate II">Certificate II</option>
                                                                <option value="Certificate III">Certificate III</option>
                                                                <option value="Certificate IV">Certificate IV</option>
                                                                <option value="Diploma">Diploma</option>
                                                                <option value="Advanced Diploma">Advanced Diploma</option>
                                                                <option value="Associate Degree">Associate Degree</option>
                                                                <option value="Bachelor Degree">Bachelor Degree</option>
                                                                <option value="Bachelor Honours Degree">Bachelor Honours Degree</option>
                                                                <option value="Graduate Certificate">Graduate Certificate</option>
                                                                <option value="Graduate Diploma">Graduate Diploma</option>
                                                                <option value="Masters Degree">Masters Degree</option>
                                                                <option value="Doctoral Degree">Doctoral Degree</option>
                                                                <option value="11">+2</option>
                                                            </select>
                                                            <input type="hidden" name="level_hidden[]" id="level_new_hidden" value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="name_new">Name</label>
                                                            <input type="text" name="name[]" class="form-control name-input" id="name_new" placeholder="Enter Name">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="country_new">Country </label>
                                                            <select name="country[]" id="country_new" class="form-control countryCls">
                                                                <option value="Australia">Australia</option>
                                                                <option value="India">India</option>
                                                                <!-- Add other countries dynamically -->
                                                                <?php foreach (\App\Models\Country::all() as $list) {
                                                                    if ($list->name != 'Australia' && $list->name != 'India') {
                                                                        echo '<option value="' . $list->name . '">' . $list->name . '</option>';
                                                                    }
                                                                } ?>
                                                            </select>
                                                            <input type="hidden" name="country_hidden[]" id="country_new_hidden" value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="start_date_new">Start Date</label>
                                                            <input type="date" name="start_date[]" class="form-control start-date-input" id="start_date_new" placeholder="Enter Start Date">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="finish_date_new">Finish Date</label>
                                                            <input type="date" name="finish_date[]" class="form-control finish-date-input" id="finish_date_new" placeholder="Enter Finish Date">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="relevant_qualification_new">Relevant</label>
                                                            <select name="relevant_qualification[]" class="form-control relevantQualificationCls" id="relevant_qualification_new">
                                                                <option value="">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="relevant_qualification_hidden[]" id="relevant_qualification_new_hidden" value="">
                                                    </div>

                                                    <!-- Plus Button for Adding New Rows -->
                                                    <div class="col-sm-1">
                                                        <button type="button" class="btn btn-primary add-qualification-btn" data-index="new">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <style>
                                        .invalid {
                                            border: 1px solid red;
                                        }
                                        </style>
                                        <script>
                                        $(document).ready(function() {

                                            var level_new_val = $('#level_new').val();
                                            $('#level_new_hidden').val(level_new_val);
                                            $(document).on('change', '#level_new', function() {
                                                var level_new_val = $('#level_new').val();
                                                $('#level_new_hidden').val(level_new_val);
                                            });

                                            var country_new_val = $('#country_new').val();
                                            $('#country_new_hidden').val(country_new_val);
                                            $(document).on('change', '#country_new', function() {
                                                var country_new_val = $('#country_new').val();
                                                $('#country_new_hidden').val(country_new_val);
                                            });

                                            var relqual_new_val = $('#relevant_qualification_new').val();
                                            $('#relevant_qualification_new_hidden').val(relqual_new_val);
                                            $(document).on('change', '#relevant_qualification_new', function() {
                                                var relqual_new_val = $('#relevant_qualification_new').val();
                                                $('#relevant_qualification_new_hidden').val(relqual_new_val);
                                            });



                                            let qualificationIndex = 1;

                                            function validateFields(row) {
                                                let isValid = true;
                                                $(row).find('select, input').each(function() {
                                                    if ($(this).attr('type') !== 'date') {  // Exclude date inputs
                                                        if ($(this).val() === '') {
                                                            $(this).addClass('invalid');
                                                            isValid = false;
                                                        } else {
                                                            $(this).removeClass('invalid');
                                                        }
                                                    }
                                                });
                                                return isValid;
                                            }

                                            function addNewQualificationRow() {
                                                // Validate the current row before adding a new one
                                                const currentRow = $('.qualification-fields:last');
                                                if (!validateFields(currentRow)) {
                                                    alert('Please fill in all required fields before adding a new row.');
                                                    return;
                                                }

                                                // Display a confirmation dialog before freezing the current row
                                                if (confirm("Are you sure you want to freeze the current row?")) {
                                                    // Set all input fields in the previous row to readonly if the user confirms
                                                    currentRow.find('input').attr('readonly', true);
                                                    currentRow.find('select').prop('disabled', true);
                                                    currentRow.find('.add-qualification-btn').prop('disabled', true);
                                                }

                                                const newRow = `
                                                    <div class="qualification-fields row mb-6" id="row-${qualificationIndex}">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="level_${qualificationIndex}">Level </label>
                                                                <select name="level[]" id="level_${qualificationIndex}" class="form-control levelCls">
                                                                    <option value="">Select Level</option>
                                                                    <option value="Certificate I">Certificate I</option>
                                                                    <option value="Certificate II">Certificate II</option>
                                                                    <option value="Certificate III">Certificate III</option>
                                                                    <option value="Certificate IV">Certificate IV</option>
                                                                    <option value="Diploma">Diploma</option>
                                                                    <option value="Advanced Diploma">Advanced Diploma</option>
                                                                    <option value="Associate Degree">Associate Degree</option>
                                                                    <option value="Bachelor Degree">Bachelor Degree</option>
                                                                    <option value="Bachelor Honours Degree">Bachelor Honours Degree</option>
                                                                    <option value="Graduate Certificate">Graduate Certificate</option>
                                                                    <option value="Graduate Diploma">Graduate Diploma</option>
                                                                    <option value="Masters Degree">Masters Degree</option>
                                                                    <option value="Doctoral Degree">Doctoral Degree</option>
                                                                    <option value="11">+2</option>
                                                                </select>
                                                                <input type="hidden" name="level_hidden[]" id="level_hidden_${qualificationIndex}">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="name_${qualificationIndex}">Name </label>
                                                                <input type="text" name="name[]" class="form-control name-input" id="name_${qualificationIndex}" placeholder="Enter Name">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="country_${qualificationIndex}">Country </label>
                                                                <select name="country[]" id="country_${qualificationIndex}" class="form-control countryCls">
                                                                    <option value="Australia">Australia</option>
                                                                    <option value="India">India</option>
                                                                    <!-- Add other countries dynamically -->
                                                                    <?php foreach (\App\Models\Country::all() as $list) {
                                                                        if ($list->name != 'Australia' && $list->name != 'India') {
                                                                            echo '<option value="' . $list->name . '">' . $list->name . '</option>';
                                                                        }
                                                                    } ?>
                                                                </select>
                                                                <input type="hidden" name="country_hidden[]" id="country_hidden_${qualificationIndex}">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="start_date_${qualificationIndex}">Start Date</label>
                                                                <input type="date" name="start_date[]" class="form-control start-date-input" id="start_date_${qualificationIndex}" placeholder="Enter Start Date">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="finish_date_${qualificationIndex}">Finish Date</label>
                                                                <input type="date" name="finish_date[]" class="form-control finish-date-input" id="finish_date_${qualificationIndex}" placeholder="Enter Finish Date">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-1">
                                                            <div class="form-group">
                                                                <label for="relevant_qualification_${qualificationIndex}">Relevant</label>
                                                                <select name="relevant_qualification[]" class="form-control relevantQualificationCls" id="relevant_qualification_${qualificationIndex}">
                                                                    <option value="">Select</option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                            </div>
                                                            <input type="hidden" name="relevant_qualification_hidden[]" id="relevant_qualification_hidden_${qualificationIndex}" value="">
                                                        </div>

                                                        <div class="col-sm-1">
                                                            <button type="button" class="btn btn-primary add-qualification-btn" data-index="${qualificationIndex}">+</button>
                                                        </div>
                                                    </div>`;

                                                qualificationIndex++;
                                                $('#qualification-fields-wrapper').append(newRow);

                                                //console.log('qualificationIndex=='+qualificationIndex);
                                                var updqualificationIndex = qualificationIndex - 1;
                                                var level_upd_val_next = $('#level_'+updqualificationIndex).val();
                                                $('#level_hidden_'+updqualificationIndex).val(level_upd_val_next);

                                                $(document).on('change', '.levelCls', function() {
                                                    var level_type_id = $(this).attr('id');
                                                    var level_type_arr = level_type_id.split('_');
                                                    if( level_type_arr.length >0 ){
                                                        var levelVal = $('#level_'+level_type_arr[1]).val();
                                                        //console.log('levelVal==='+levelVal);
                                                        $('#level_hidden_'+level_type_arr[1]).val(levelVal);
                                                    }
                                                });

                                                //console.log('qualificationIndex==='+qualificationIndex);
                                                var country_upd_val_next = $('#country_'+updqualificationIndex).val();
                                                $('#country_hidden_'+updqualificationIndex).val(country_upd_val_next);

                                                $(document).on('change', '.countryCls', function() {
                                                    var country_type_id = $(this).attr('id');
                                                    var country_type_arr = country_type_id.split('_');
                                                    if( country_type_arr.length >0 ){
                                                        var countryVal = $('#country_'+country_type_arr[1]).val();
                                                        //console.log('countryVal==='+countryVal);
                                                        $('#country_hidden_'+country_type_arr[1]).val(countryVal);
                                                    }
                                                });

                                                var relevant_qualification_upd_val_next = $('#relevant_qualification_'+updqualificationIndex).val();
                                                $('#relevant_qualification_hidden__'+updqualificationIndex).val(relevant_qualification_upd_val_next);

                                                $(document).on('change', '.relevantQualificationCls', function() {
                                                    var relevant_Qualification_id = $(this).attr('id');
                                                    var relevant_Qualification_arr = relevant_Qualification_id.split('_');
                                                    if( relevant_Qualification_arr.length >0 ){
                                                        var relQualVal = $('#relevant_qualification_'+relevant_Qualification_arr[2]).val();
                                                        //console.log('relQualVal==='+relQualVal);
                                                        $('#relevant_qualification_hidden_'+relevant_Qualification_arr[2]).val(relQualVal);
                                                    }
                                                });
                                            }


                                            $(document).on('click', '.add-qualification-btn', function() {
                                                addNewQualificationRow();
                                            });

                                            // Event listener to remove the red border on input or select change
                                            $(document).on('input change', 'select, input', function() {
                                                $(this).removeClass('invalid');
                                            });

                                            // Initial setup: make the first row editable if no qualifications exist
                                            if (qualificationIndex === 0) {
                                                $('.qualification-fields:first').find('input, select').attr('readonly', false);
                                            }
                                        });
                                        </script>


                                        <meta name="csrf-token" content="{{ csrf_token() }}">
                                        <div class="col-sm-12">
                                            <h5 style="color: #FFFFFF;">Client Experience</h5>
                                            <div id="job-fields-wrapper">
                                                <!-- Existing Job Fields -->

                                                <!-- Always include a blank row for adding new job details -->
                                                <div class="job-fields row mb-6" id="row_new">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="job_title_new">Job Title</label>
                                                            <input type="text" name="job_title[]" id="job_title_new" class="form-control" placeholder="Enter Job Title">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="job_code_new">Code</label>
                                                            <input type="text" name="job_code[]" class="form-control job-code-input" id="job_code_new" placeholder="Enter Code">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="job_country_new">Country</label>
                                                            <select name="job_country[]" id="job_country_new" class="form-control jobcountryCls">
                                                                <option value="Australia">Australia</option>
                                                                <option value="India">India</option>
                                                                <!-- Add other countries dynamically -->
                                                                <?php foreach (\App\Models\Country::all() as $list) {
                                                                    if ($list->name != 'Australia' && $list->name != 'India') {
                                                                        echo '<option value="' . $list->name . '">' . $list->name . '</option>';
                                                                    }
                                                                } ?>
                                                            </select>
                                                            <input type="hidden" name="job_country_hidden[]" id="job_country_new_hidden" value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="job_start_date_new">Start Date</label>
                                                            <input type="date" name="job_start_date[]" class="form-control job-start-date-input" id="job_start_date_new" placeholder="Enter Start Date">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="job_finish_date_new">Finish Date</label>
                                                            <input type="date" name="job_finish_date[]" class="form-control job-finish-date-input" id="job_finish_date_new" placeholder="Enter Finish Date">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="relevant_experience_new">Relevant</label>
                                                            <select name="relevant_experience[]" class="form-control relevantExperienceCls" id="relevant_experience_new">
                                                                <option value="">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="relevant_experience_hidden[]" id="relevant_experience_new_hidden" value="">
                                                    </div>

                                                    <!-- Plus Button for Adding New Rows -->
                                                    <div class="col-sm-1">
                                                        <button type="button" class="btn btn-primary add-job-btn" data-index="new">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <style>
                                        .invalid {
                                            border: 1px solid red;
                                        }
                                        </style>
                                        <script>
                                        $(document).ready(function() {
                                            let jobIndex = 1;

                                            var job_country_new_val = $('#job_country_new').val();
                                            $('#job_country_new_hidden').val(job_country_new_val);
                                            $(document).on('change', '#job_country_new', function() {
                                                var job_country_new_val = $('#job_country_new').val();
                                                $('#job_country_new_hidden').val(job_country_new_val);
                                            });

                                            var relevant_experience_new_val = $('#relevant_experience_new').val();
                                            $('#relevant_experience_new_hidden').val(relevant_experience_new_val);
                                            $(document).on('change', '#relevant_experience_new', function() {
                                                var relevant_experience_new_val = $('#relevant_experience_new').val();
                                                $('#relevant_experience_new_hidden').val(relevant_experience_new_val);
                                            });

                                            function validateFields(row) {
                                                let isValid = true;

                                                $(row).find('select, input').each(function() {
                                                    if ($(this).attr('type') !== 'date') {  // Exclude date inputs
                                                        if ($(this).val() === '') {
                                                            $(this).addClass('invalid');
                                                            isValid = false;
                                                        } else {
                                                            $(this).removeClass('invalid');
                                                        }
                                                    }
                                                });

                                                return isValid;
                                            }

                                            function addNewJobRow() {
                                                // Validate the current row before adding a new one
                                                const currentRow = $('.job-fields:last');
                                                if (!validateFields(currentRow)) {
                                                    alert('Please fill in all required fields before adding a new row.');
                                                    return;
                                                }

                                                // Display a confirmation dialog before freezing the current row
                                                if (confirm("Are you sure you want to freeze the current row?")) {
                                                    // Set all input fields in the previous row to readonly if the user confirms
                                                    currentRow.find('input').attr('readonly', true);
                                                    currentRow.find('select').prop('disabled', true);
                                                    currentRow.find('.add-job-btn').prop('disabled', true);
                                                }

                                                const newRow = `
                                                    <div class="job-fields row mb-6" id="row-${jobIndex}">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="job_title_${jobIndex}">Job Title</label>
                                                                <input type="text" name="job_title[]" id="job_title_${jobIndex}" class="form-control" placeholder="Enter Job Title">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="job_code_${jobIndex}">Code</label>
                                                                <input type="text" name="job_code[]" class="form-control job-code-input" id="job_code_${jobIndex}" placeholder="Enter Code">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="job_country_${jobIndex}">Country</label>
                                                                <select name="job_country[]" id="job_country_${jobIndex}" class="form-control jobcountryCls">
                                                                    <option value="Australia">Australia</option>
                                                                    <option value="India">India</option>
                                                                    <!-- Add other countries dynamically -->
                                                                    <?php foreach (\App\Models\Country::all() as $list) {
                                                                        if ($list->name != 'Australia' && $list->name != 'India') {
                                                                            echo '<option value="' . $list->name . '">' . $list->name . '</option>';
                                                                        }
                                                                    } ?>
                                                                </select>
                                                                <input type="hidden" name="job_country_hidden[]" id="job_country_hidden_${jobIndex}">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="job_start_date_${jobIndex}">Start Date</label>
                                                                <input type="date" name="job_start_date[]" class="form-control job-start-date-input" id="job_start_date_${jobIndex}" placeholder="Enter Start Date">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="job_finish_date_${jobIndex}">Finish Date</label>
                                                                <input type="date" name="job_finish_date[]" class="form-control job-finish-date-input" id="job_finish_date_${jobIndex}" placeholder="Enter Finish Date">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-1">
                                                            <div class="form-group">
                                                                <label for="relevant_experience_${jobIndex}">Relevant</label>
                                                                <select name="relevant_experience[]" class="form-control relevantExperienceCls" id="relevant_experience_${jobIndex}">
                                                                    <option value="">Select</option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                            </div>
                                                            <input type="hidden" name="relevant_experience_hidden[]" id="relevant_experience_hidden_${jobIndex}" value="">
                                                        </div>

                                                        <div class="col-sm-1">
                                                            <button type="button" class="btn btn-primary add-job-btn" data-index="${jobIndex}">+</button>
                                                        </div>
                                                    </div>`;

                                                jobIndex++;
                                                $('#job-fields-wrapper').append(newRow);

                                                var updjobIndex = jobIndex-1;
                                                var job_country_upd_val_next = $('#job_country_'+updjobIndex).val();
                                                $('#job_country_hidden_'+updjobIndex).val(job_country_upd_val_next);

                                                $(document).on('change', '.jobcountryCls', function() {
                                                    var job_country_id = $(this).attr('id');
                                                    var job_country_arr = job_country_id.split('_');
                                                    if( job_country_arr.length >0 ){
                                                        var jobCountryVal = $('#job_country_'+job_country_arr[2]).val();
                                                        //console.log('jobCountryVal==='+jobCountryVal);
                                                        $('#job_country_hidden_'+job_country_arr[2]).val(jobCountryVal);
                                                    }
                                                });

                                                var relevant_experience_upd_val_next = $('#relevant_experience_'+updjobIndex).val();
                                                $('#relevant_experience_hidden_'+updjobIndex).val(relevant_experience_upd_val_next);

                                                $(document).on('change', '.relevantExperienceCls', function() {
                                                    var relevant_experience_id = $(this).attr('id');
                                                    var relevant_experience_id_arr = relevant_experience_id.split('_');
                                                    if( relevant_experience_id_arr.length >0 ){
                                                        var relevantExperienceVal = $('#relevant_experience_'+relevant_experience_id_arr[2]).val();
                                                        //console.log('relevantExperienceVal==='+relevantExperienceVal);
                                                        $('#relevant_experience_hidden_'+relevant_experience_id_arr[2]).val(relevantExperienceVal);
                                                    }
                                                });
                                            }

                                            $(document).on('click', '.add-job-btn', function() {
                                                addNewJobRow();
                                            });

                                            // Event listener to remove the red border on input or select change
                                            $(document).on('input change', 'select, input', function() {
                                                $(this).removeClass('invalid');
                                            });

                                            // Initial setup: make the first row editable if no jobs exist
                                            if (jobIndex === 0) {
                                                $('.job-fields:first').find('input, select').attr('readonly', false);
                                            }
                                        });
                                        </script>
                                        <!------------------------------------  Start Occupation  --------------------------->

                                        <div class="col-sm-12">
                                            <div id="occupation-fields-wrapper">
                                                <h5 style="color: #FFFFFF;">Occupation</h5>

                                                <!-- Blank row for adding new occupations -->
                                                <div class="occupation-fields row mb-3">
                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="skill_assessment_new">Skill</label>
                                                            <select name="skill_assessment[]" class="form-control skill-assessment-input skillCls" id="skill_assessment_new" required>
                                                                <option value="">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="skill_assessment_hidden[]" class="skillCls_hidden" id="skill_assessment_new_hidden" value="">
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="nomi_occupation_new">Occupation</label>
                                                            <input type="text" name="nomi_occupation[]" class="form-control nomi_occupation" id="nomi_occupation_new" autocomplete="off" placeholder="Enter Occupation">
                                                            <div class="autocomplete-items"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="occupation_code_new">Occupation Code</label>
                                                            <input type="text" name="occupation_code[]" class="form-control occupation_code" id="occupation_code_new" autocomplete="off" placeholder="Occupation Code">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="list_new">List</label>
                                                            <input type="text" name="list[]" class="form-control list" id="list_new" autocomplete="off" placeholder="List">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="visa_subclass_new">Visa Subclass</label>
                                                            <input type="text" name="visa_subclass[]" class="form-control visa_subclass" id="visa_subclass_new" autocomplete="off" placeholder="Visa Subclass">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 date-wrapper">
                                                        <div class="form-group">
                                                            <label for="date_new">Date</label>
                                                            <input type="date" name="dates[]" class="form-control date-input" id="date_new">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="relevant_occupation_new">Relevant</label>
                                                            <select name="relevant_occupation[]" class="form-control relevantOccupationCls" id="relevant_occupation_new">
                                                                <option value="">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="relevant_occupation_hidden[]" class="relevantOccupationCls_hidden" id="relevant_occupation_new_hidden" value="">
                                                    </div>

                                                    <div class="col-sm-1 d-flex align-items-center">
                                                        <button type="button" class="btn btn-primary add-row-btn-skill">+</button>
                                                    </div>
                                                </div>

                                                <div id="error-message" class="error-message" style="display: none;"></div>
                                            </div>

                                            <style>
                                                /* Custom invalid field border */
                                                .custom-invalid {
                                                    border: 2px solid red;
                                                }

                                                /* Reset default Bootstrap validation styles */
                                                .is-invalid {
                                                    box-shadow: none;
                                                }
                                            </style>

                                            <script>
                                            $(document).ready(function() {

                                                var skill_assessment_new_val = $('#skill_assessment_new').val();
                                                $('#skill_assessment_new_hidden').val(skill_assessment_new_val);
                                                $(document).on('change', '#skill_assessment_new', function() {
                                                    var skill_assessment_new_val = $('#skill_assessment_new').val();
                                                    $('#skill_assessment_new_hidden').val(skill_assessment_new_val);
                                                });

                                                var relevant_occupation_new_val = $('#relevant_occupation_new').val();
                                                $('#relevant_occupation_new_hidden').val(relevant_occupation_new_val);
                                                $(document).on('change', '#relevant_occupation_new', function() {
                                                    var relevant_occupation_new_val = $('#relevant_occupation_new').val();
                                                    $('#relevant_occupation_new_hidden').val(relevant_occupation_new_val);
                                                });

                                                // Function to toggle the date field based on skill assessment value
                                                function toggleDateField($row) {
                                                    var skillAssessment = $row.find('.skill-assessment-input').val();
                                                    var $dateInput = $row.find('.date-wrapper');
                                                    if (skillAssessment === 'Yes') {
                                                        $dateInput.show();
                                                        //$row.find('.date-wrapper input').addClass('custom-invalid');
                                                    } else {
                                                        $dateInput.hide();
                                                        $row.find('.date-wrapper input').removeClass('custom-invalid');
                                                    }
                                                }

                                                // Initialize date field visibility for existing rows
                                                $('.occupation-fields').each(function() {
                                                    toggleDateField($(this));
                                                });

                                                // Event listener for skill assessment dropdown change
                                                $(document).on('change', '.skill-assessment-input', function() {
                                                    var $row = $(this).closest('.occupation-fields');
                                                    toggleDateField($row);
                                                });

                                                // Function to validate and apply red border to blank fields
                                                function validateCurrentRowSkill() {
                                                    var $currentRow = $('.occupation-fields:last');
                                                    var isValid = true;

                                                    // Validate if fields are not empty, add red border if empty
                                                    $currentRow.find('input, select').each(function() {
                                                        if ($(this).val() === '') {
                                                            $(this).addClass('custom-invalid');
                                                            isValid = false;
                                                        } else {
                                                            $(this).removeClass('custom-invalid');
                                                        }
                                                    });
                                                    return isValid;
                                                }

                                                // Make fields read-only
                                                function makeRowReadOnlySkill($row) {
                                                    $row.find('input, select').prop('readonly', true);
                                                    $row.find('select').prop('disabled', true);
                                                }

                                                // Function to add a new row
                                                function addNewRowSkill() {
                                                    $('.occupation-fields:last').removeClass('error-row');

                                                    var newRow = `<div class="occupation-fields row mb-3">
                                                        <div class="col-sm-1">
                                                            <div class="form-group">
                                                                <label for="skill_assessment_new">Skill <span style="color:#ff0000;">*</span></label>
                                                                <select name="skill_assessment[]" class="form-control skill-assessment-input skillCls" id="skill_assessment_new" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                                <input type="hidden" name="skill_assessment_hidden[]" class="skillCls_hidden" id="skill_assessment_new_hidden" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="nomi_occupation_new">Occupation</label>
                                                                <input type="text" name="nomi_occupation[]" class="form-control nomi_occupation" id="nomi_occupation_new" autocomplete="off" placeholder="Enter Occupation">
                                                                <div class="autocomplete-items"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="occupation_code_new">Occupation Code</label>
                                                                <input type="text" name="occupation_code[]" class="form-control occupation_code" id="occupation_code_new" autocomplete="off" placeholder="Occupation Code">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-1">
                                                            <div class="form-group">
                                                                <label for="list_new">List</label>
                                                                <input type="text" name="list[]" class="form-control list" id="list_new" autocomplete="off" placeholder="List">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="visa_subclass_new">Visa Subclass</label>
                                                                <input type="text" name="visa_subclass[]" class="form-control visa_subclass" id="visa_subclass_new" autocomplete="off" placeholder="Visa Subclass">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2 date-wrapper">
                                                            <div class="form-group">
                                                                <label for="date_new">Date</label>
                                                                <input type="date" name="dates[]" class="form-control date-input" id="date_new">
                                                            </div>
                                                        </div>


                                                        <div class="col-sm-1">
                                                            <div class="form-group">
                                                                <label for="relevant_occupation_new">Relevant</label>
                                                                <select name="relevant_occupation[]" class="form-control relevantOccupationCls" id="relevant_occupation_new" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                                <input type="hidden" name="relevant_occupation_hidden[]" class="relevantOccupationCls_hidden" id="relevant_occupation_new_hidden" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-1 d-flex align-items-center">
                                                            <button type="button" class="btn btn-primary add-row-btn-skill">+</button>
                                                        </div>
                                                    </div>`;
                                                    $('#occupation-fields-wrapper').append(newRow);

                                                    /*$(document).on('change', '.skillCls', function() {
                                                        var skill_type_id = $(this).val();
                                                        var contact_type_arr = skill_type_id.split('_');
                                                        if( contact_type_arr.length >0 ){
                                                            var contactVal = $('#contact_type_'+contact_type_arr[2]).val();
                                                            //console.log('contactVal==='+contactVal);
                                                            $('#contact_type_hidden_'+contact_type_arr[2]).val(contactVal);
                                                        }
                                                    });*/

                                                    if( $('.occupation-fields').length >0 ) {
                                                        //console.log('length='+$('.occupation-fields').length );
                                                        $('.occupation-fields').each(function(index) {
                                                            $(document).on('change', '.skillCls', function() {
                                                                var skillCls_val = $(this).val();
                                                                $('.occupation-fields').find('.skillCls_hidden').val(skillCls_val);
                                                            });

                                                            $(document).on('change', '.relevantOccupationCls', function() {
                                                                var relevantOccupationCls_val = $(this).val();
                                                                $('.occupation-fields').find('.relevantOccupationCls_hidden').val(relevantOccupationCls_val);
                                                            });
                                                        });
                                                    }
                                                    $('#error-message').hide();
                                                }

                                                // Add row on button click
                                                $(document).on('click', '.add-row-btn-skill', function() {
                                                    if (validateCurrentRowSkill()) {
                                                        var isConfirmed = confirm("Are you sure you want to freeze the current row?");
                                                        if (isConfirmed) {
                                                            var $currentRow = $(this).closest('.occupation-fields');
                                                            makeRowReadOnlySkill($currentRow); // Make current row read-only
                                                            addNewRowSkill(); // Add new row
                                                        }
                                                    }
                                                });

                                                // Occupation autocomplete logic here...
                                                $(document).on('input', '.nomi_occupation', function() {
                                                    var occupation = $(this).val();
                                                    var $row = $(this).closest('.occupation-fields');

                                                    if (occupation.length > 2) {
                                                        $.ajax({
                                                            url: '{{ route("admin.leads.updateOccupation") }}',
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            },
                                                            data: { occupation: occupation },
                                                            success: function(response) {
                                                                var suggestions = response.occupations || [];
                                                                var $autocomplete = $row.find('.autocomplete-items').empty();

                                                                if (suggestions.length > 0) {
                                                                    suggestions.forEach(function(suggestion) {
                                                                        var $item = $('<div class="autocomplete-item"></div>')
                                                                            .text(suggestion.occupation)
                                                                            .data('occupation', {
                                                                                occupation_code: suggestion.occupation_code || '',
                                                                                list: suggestion.list || '',
                                                                                visa_subclass: suggestion.visa_subclass || ''
                                                                            })
                                                                            .appendTo($autocomplete);

                                                                        $item.on('click', function() {
                                                                            var occupationData = $(this).data('occupation');
                                                                            $row.find('.nomi_occupation').val(suggestion.occupation);
                                                                            $row.find('.occupation_code').val(occupationData.occupation_code);
                                                                            $row.find('.list').val(occupationData.list);
                                                                            $row.find('.visa_subclass').val(occupationData.visa_subclass);
                                                                            $autocomplete.empty(); // Clear suggestions
                                                                        });
                                                                    });
                                                                } else {
                                                                    $autocomplete.append('<div>No suggestions found</div>');
                                                                }
                                                            },
                                                            error: function() {
                                                                console.log('Error fetching occupation data');
                                                            }
                                                        });
                                                    }
                                                });
                                            });
                                            </script>
                                        </div>
                                        <!------------------------------------  End Occupation  --------------------------->


                                        <!------------------------------------  Start Test Scores  --------------------------->
                                        <div class="col-sm-12">
                                            <h5 style="color: #FFFFFF;">Test Scores</h5>
                                            <div id="test-score-fields-wrapper">

                                                <!-- New Test Score Fields -->
                                                <div class="test-score-fields form-row mb-3 test_score_row_0" id="row-new-test">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="test_type_new">Test Type <span style="color:#ff0000;">*</span></label>
                                                            <select name="test_type[]" class="form-control test-type-input testTypeCls" id="test_type_new" required>
                                                                <option value="">Select Test Type</option>
                                                                <option value="IELTS Academic">IELTS Academic</option>
                                                                <option value="IELTS General Training">IELTS General Training</option>
                                                                <option value="TOEFL iBT">TOEFL iBT</option>
                                                                <option value="PTE Academic">PTE Academic</option>
                                                                <option value="OET">OET</option>
                                                            </select>
                                                            <div class="error-msg" id="test_type_error_new">Please select a test type</div>
                                                        </div>
                                                        <input type="hidden" name="test_type_hidden[]" id="test_type_new_hidden" value="">
                                                    </div>

                                                    <!-- Listening Input -->
                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="listening_new">Listening</label>
                                                            <input type="text" name="listening[]" class="form-control listening-input" id="listening_new">
                                                            <div class="error-msg" id="listening_error_new"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Reading Input -->
                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="reading_new">Reading</label>
                                                            <input type="text" name="reading[]" class="form-control reading-input" id="reading_new">
                                                            <div class="error-msg" id="reading_error_new"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Writing Input -->
                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="writing_new">Writing</label>
                                                            <input type="text" name="writing[]" class="form-control writing-input" id="writing_new">
                                                            <div class="error-msg" id="writing_error_new"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Speaking Input -->
                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="speaking_new">Speaking</label>
                                                            <input type="text" name="speaking[]" class="form-control speaking-input" id="speaking_new">
                                                            <div class="error-msg" id="speaking_error_new"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Overall Score Input -->
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="overall_score_new">Overall Score</label>
                                                            <input type="text" name="overall_score[]" class="form-control overall-score-input" id="overall_score_new">
                                                            <div class="error-msg" id="overall_error_new"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Date Input -->
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="test_date_new">Date of Test</label>
                                                            <input type="date" name="test_date[]" class="form-control test-date-input" id="test_date_new">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <div class="form-group">
                                                            <label for="relevant_test_new">Relevant</label>
                                                            <select name="relevant_test[]" class="form-control relevantTestCls" id="relevant_test_new" required>
                                                                <option value="">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                            <div class="error-msg" id="relevant_test_error_new">Please select</div>
                                                        </div>
                                                        <input type="hidden" name="relevant_test_hidden[]" id="relevant_test_new_hidden" value="">
                                                    </div>

                                                    <div class="col-sm-1">
                                                        <button type="button" class="btn btn-primary add-test-score" data-index="new">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                        $(document).ready(function()
                                        {
                                            var index = 1;

                                            var test_type_new_val = $('#test_type_new').val();
                                            $('#test_type_new_hidden').val(test_type_new_val);
                                            $(document).on('change', '#test_type_new', function() {
                                                var test_type_new_val = $('#test_type_new').val();
                                                $('#test_type_new_hidden').val(test_type_new_val);
                                            });

                                            var relevant_test_new_val = $('#relevant_test_new').val();
                                            $('#relevant_test_new_hidden').val(relevant_test_new_val);
                                            $(document).on('change', '#relevant_test_new', function() {
                                                var relevant_test_new_val = $('#relevant_test_new').val();
                                                $('#relevant_test_new_hidden').val(relevant_test_new_val);
                                            });

                                            // Function to validate a single row
                                            function validateRowTestScore(row) {
                                                let isValid = true;
                                                $(row).find('select[required], input[required]').each(function() {
                                                    const value = $(this).val();
                                                    if (!value) {
                                                        isValid = false;
                                                        $(this).next('.error-msg').show();
                                                    } else {
                                                        $(this).next('.error-msg').hide();
                                                    }
                                                });
                                                return isValid;
                                            }

                                            // Function to add a new test score row
                                            function addNewTestScoreRow()
                                            {

                                                // Validate existing rows before adding a new row
                                                let allRowsValid = true;
                                                $('#test-score-fields-wrapper .test-score-fields').each(function() {
                                                    if (!validateRowTestScore(this)) {
                                                        allRowsValid = false;
                                                    }
                                                });

                                                if (!allRowsValid) {
                                                    alert('Please fill out all required fields in the existing rows before adding a new row.');
                                                    return;
                                                }

                                                // Ask for confirmation before freezing the current row
                                                var isConfirmed = confirm("Are you sure you want to freeze the current row?");
                                                if (!isConfirmed) {
                                                    return; // If the user cancels, don't proceed with freezing the row or adding a new one
                                                }

                                                // Clone the new row template
                                                var newRow = $('#row-new-test').clone(); // Clone the new row template
                                                newRow.removeAttr('id'); // Remove ID from cloned row to avoid duplication
                                                newRow.addClass('test_score_row_'+index);
                                                newRow.removeClass('test_score_row_0');



                                                // Update IDs and names of inputs and selects in the new row
                                                newRow.find('input, select').each(function() {
                                                    var name = $(this).attr('name');
                                                    var id = $(this).attr('id');
                                                    if (id) {
                                                        $(this).attr('id', id.replace('_new', '_' + index));
                                                    }
                                                    if (name) {
                                                        $(this).attr('name', name.replace('[]', '[' + index + ']'));
                                                    }
                                                });

                                                // Update error message IDs
                                                newRow.find('.error-msg').each(function() {
                                                    var id = $(this).attr('id');
                                                    if (id) {
                                                        $(this).attr('id', id.replace('_new', '_' + index));
                                                    }
                                                });

                                                // Reset input values
                                                newRow.find('input').val('');
                                                newRow.find('select').val('');

                                                // Append the new row to the container
                                                newRow.appendTo('#test-score-fields-wrapper');

                                                $(document).on('change', '.testTypeCls', function() {
                                                    var test_type_id = $(this).attr('id');
                                                    var test_type_arr = test_type_id.split('_');
                                                    if( test_type_arr.length >0 ){
                                                        var testTypeVal = $('#test_type_'+test_type_arr[2]).val();
                                                        //console.log('testTypeVal==='+testTypeVal);
                                                        $('#test_type_'+test_type_arr[2]+'_hidden').val(testTypeVal);
                                                    }
                                                });

                                                $(document).on('change', '.relevantTestCls', function() {
                                                    var relevant_test_id = $(this).attr('id');
                                                    var relevant_test_arr = relevant_test_id.split('_');
                                                    if( relevant_test_arr.length >0 ){
                                                        var relevantTestVal = $('#relevant_test_'+relevant_test_arr[2]).val();
                                                        //console.log('relevantTestVal==='+relevantTestVal);
                                                        $('#relevant_test_'+relevant_test_arr[2]+'_hidden').val(relevantTestVal);
                                                    }
                                                });

                                                if( $('.test-score-fields').length >0 ) {
                                                    //console.log('length='+$('.test-score-fields').length );
                                                    var testScoreLength = $('.test-score-fields').length;
                                                    //console.log(typeof testScoreLength);
                                                    var updtestScoreLength = testScoreLength-1;
                                                    $('.test-score-fields').each(function(index) {
                                                        if( index < updtestScoreLength ){
                                                            $('.test_score_row_'+index).find('input').attr('readonly', true);  // Make input fields read-only
                                                            $('.test_score_row_'+index).find('select').attr('disabled', true);  // Disable select fields
                                                        } else {
                                                            $('.test_score_row_'+index).find('input').removeAttr('readonly');  // Make input fields read-only
                                                            $('.test_score_row_'+index).find('select').removeAttr('disabled');  // Disable select fields
                                                        }
                                                    });
                                                }

                                                // Increment index
                                                index++;

                                            }

                                            // Event handler for adding new rows
                                            $('#test-score-fields-wrapper').on('click', '.add-test-score', function() {
                                                addNewTestScoreRow();
                                            });

                                            // Initial validation for the new row
                                            $('#test_type_new').on('change', function() {
                                                // Add validation logic for new rows if necessary
                                            });

                                            // Function to validate input fields based on test type (existing logic)
                                            function validateDecimal(input, min, max, step, errorId) {
                                                const value = parseFloat($(input).val());
                                                if (isNaN(value) || value < min || value > max || (value * 10) % (step * 10) !== 0) {
                                                    showError(errorId, `Value must be between ${min} and ${max} with steps of ${step}.`);
                                                } else {
                                                    hideError(errorId);
                                                }
                                            }

                                            function validateWholeNumber(input, min, max, errorId) {
                                                const value = parseInt($(input).val(), 10);
                                                if (isNaN(value) || value < min || value > max) {
                                                    showError(errorId, `Value must be between ${min} and ${max}.`);
                                                } else {
                                                    hideError(errorId);
                                                }
                                            }

                                            function validateIncrement(input, min, max, increment, errorId) {
                                                const value = parseFloat($(input).val());
                                                if (isNaN(value) || value < min || value > max || (value % increment !== 0)) {
                                                    showError(errorId, `Value must be between ${min} and ${max} with increments of ${increment}.`);
                                                } else {
                                                    hideError(errorId);
                                                }
                                            }

                                            function showError(errorId, message) {
                                                $('#' + errorId).text(message).show();
                                            }

                                            function hideError(errorId) {
                                                $('#' + errorId).hide();
                                            }
                                        });
                                        </script>


                                        <script>
                                        $(document).ready(function() {
                                            /*$(document).delegate(".test_type_new", "change", function(e) {
                                                const selectedTest = $(this).val();
                                                const listeningField = $('#listening_new');
                                                const readingField = $('#reading_new');
                                                const writingField = $('#writing_new');
                                                const speakingField = $('#speaking_new');
                                                const overallField = $('#overall_score_new');

                                                function resetValidation() {
                                                    $('.error-msg').hide();
                                                    listeningField.val('');
                                                    readingField.val('');
                                                    writingField.val('');
                                                    speakingField.val('');
                                                    overallField.val('');
                                                    listeningField.prop('readonly', false);
                                                    readingField.prop('readonly', false);
                                                    writingField.prop('readonly', false);
                                                    speakingField.prop('readonly', false);
                                                    overallField.prop('readonly', false);
                                                }

                                                resetValidation(); // Reset all fields when a new test type is selected

                                                // Clear previous event handlers
                                                listeningField.off('input');
                                                readingField.off('input');
                                                writingField.off('input');
                                                speakingField.off('input');
                                                overallField.off('input');

                                                // Validate based on selected test type
                                                if (selectedTest === 'IELTS Academic' || selectedTest === 'IELTS General Training') {
                                                    listeningField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, 'listening_error_new');
                                                    });
                                                    readingField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, 'reading_error_new');
                                                    });
                                                    writingField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, 'writing_error_new');
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, 'speaking_error_new');
                                                    });
                                                    overallField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, 'overall_error_new');
                                                    });
                                                } else if (selectedTest === 'PTE Academic') {
                                                    listeningField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, 'listening_error_new');
                                                    });
                                                    readingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, 'reading_error_new');
                                                    });
                                                    writingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, 'writing_error_new');
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, 'speaking_error_new');
                                                    });
                                                    overallField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, 'overall_error_new');
                                                    });
                                                } else if (selectedTest === 'TOEFL iBT') {
                                                    listeningField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, 'listening_error_new');
                                                    });
                                                    readingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, 'reading_error_new');
                                                    });
                                                    writingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, 'writing_error_new');
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, 'speaking_error_new');
                                                    });
                                                    overallField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, 'overall_error_new');
                                                    });
                                                } else if (selectedTest === 'OET') {
                                                    // Allow only A, B, C, C++, D for OET fields
                                                    const validGrades = ['A', 'B', 'C', 'C++', 'D'];

                                                    listeningField.on('input', function() {
                                                        validateGrade(this, validGrades, 'listening_error_new');
                                                    });
                                                    readingField.on('input', function() {
                                                        validateGrade(this, validGrades, 'reading_error_new');
                                                    });
                                                    writingField.on('input', function() {
                                                        validateGrade(this, validGrades, 'writing_error_new');
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateGrade(this, validGrades, 'speaking_error_new');
                                                    });
                                                    overallField.on('input', function() {
                                                        validateGrade(this, validGrades, 'overall_error_new');
                                                    });
                                                }
                                            });

                                            function validateDecimal(input, min, max, step, errorId) {
                                                const value = parseFloat($(input).val());
                                                if (isNaN(value) || value < min || value > max || (value * 10) % (step * 10) !== 0) {
                                                    showError(errorId, `Value must be between ${min} and ${max} with steps of ${step}.`);
                                                } else {
                                                    hideError(errorId);
                                                }
                                            }

                                            function validateWholeNumber(input, min, max, errorId) {
                                                const value = parseInt($(input).val(), 10);
                                                if (isNaN(value) || value < min || value > max) {
                                                    showError(errorId, `Value must be between ${min} and ${max}.`);
                                                } else {
                                                    hideError(errorId);
                                                }
                                            }

                                            // Validation function for grades A, B, C, C++, D
                                            function validateGrade(input, validGrades, errorId) {
                                                const value = $(input).val(); // Keep it case-sensitive
                                                if (validGrades.includes(value)) {
                                                    hideError(errorId);
                                                } else {
                                                    showError(errorId, `Invalid grade. Only uppercase A, B, C, C++, D are allowed.`);
                                                }
                                            }*/

                                            $(document).on('change', '.test-type-input', function() {
                                                const selectedTest = $(this).val();

                                                // Find the closest parent row to get the relevant inputs
                                                const parentRow = $(this).closest('.test-score-fields');

                                                const listeningField = parentRow.find('.listening-input');
                                                const readingField = parentRow.find('.reading-input');
                                                const writingField = parentRow.find('.writing-input');
                                                const speakingField = parentRow.find('.speaking-input');
                                                const overallField = parentRow.find('.overall-input');

                                                function resetValidation() {
                                                    parentRow.find('.error-msg').hide();
                                                    listeningField.val('').prop('readonly', false);
                                                    readingField.val('').prop('readonly', false);
                                                    writingField.val('').prop('readonly', false);
                                                    speakingField.val('').prop('readonly', false);
                                                    overallField.val('').prop('readonly', false);
                                                }

                                                resetValidation(); // Reset all fields when a new test type is selected

                                                // Clear previous event handlers
                                                listeningField.off('input');
                                                readingField.off('input');
                                                writingField.off('input');
                                                speakingField.off('input');
                                                overallField.off('input');

                                                // Validate based on selected test type
                                                if (selectedTest === 'IELTS Academic' || selectedTest === 'IELTS General Training') {
                                                    listeningField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, parentRow.find('#listening_error_new'));
                                                    });
                                                    readingField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, parentRow.find('#reading_error_new'));
                                                    });
                                                    writingField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, parentRow.find('#writing_error_new'));
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, parentRow.find('#speaking_error_new'));
                                                    });
                                                    overallField.on('input', function() {
                                                        validateDecimal(this, 1, 9, 0.5, parentRow.find('#overall_error_new'));
                                                    });
                                                } else if (selectedTest === 'PTE Academic') {
                                                    listeningField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, parentRow.find('#listening_error_new'));
                                                    });
                                                    readingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, parentRow.find('#reading_error_new'));
                                                    });
                                                    writingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, parentRow.find('#writing_error_new'));
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, parentRow.find('#speaking_error_new'));
                                                    });
                                                    overallField.on('input', function() {
                                                        validateWholeNumber(this, 0, 90, parentRow.find('#overall_error_new'));
                                                    });
                                                } else if (selectedTest === 'TOEFL iBT') {
                                                    listeningField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, parentRow.find('#listening_error_new'));
                                                    });
                                                    readingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, parentRow.find('#reading_error_new'));
                                                    });
                                                    writingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, parentRow.find('#writing_error_new'));
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, parentRow.find('#speaking_error_new'));
                                                    });
                                                    overallField.on('input', function() {
                                                        validateWholeNumber(this, 0, 30, parentRow.find('#overall_error_new'));
                                                    });
                                                } else if (selectedTest === 'OET') {
                                                    // Allow only A, B, C, C++, D for OET fields
                                                    const validGrades = ['A', 'B', 'C', 'C++', 'D'];

                                                    listeningField.on('input', function() {
                                                        validateGrade(this, validGrades, parentRow.find('#listening_error_new'));
                                                    });
                                                    readingField.on('input', function() {
                                                        validateGrade(this, validGrades, parentRow.find('#reading_error_new'));
                                                    });
                                                    writingField.on('input', function() {
                                                        validateGrade(this, validGrades, parentRow.find('#writing_error_new'));
                                                    });
                                                    speakingField.on('input', function() {
                                                        validateGrade(this, validGrades, parentRow.find('#speaking_error_new'));
                                                    });
                                                    overallField.on('input', function() {
                                                        validateGrade(this, validGrades, parentRow.find('#overall_error_new'));
                                                    });
                                                }
                                            });

                                            function validateDecimal(input, min, max, step) {
                                                const value = parseFloat($(input).val());
                                                const errorElement = $(input).closest('.form-group').find('.error-msg');

                                                if (isNaN(value) || value < min || value > max || (value * 10) % (step * 10) !== 0) {
                                                    errorElement.show().text(`Value must be between ${min} and ${max} with steps of ${step}.`);
                                                } else {
                                                    errorElement.hide();
                                                }
                                            }

                                            function validateWholeNumber(input, min, max) {
                                                const value = parseInt($(input).val(), 10);
                                                const errorElement = $(input).closest('.form-group').find('.error-msg');

                                                if (isNaN(value) || value < min || value > max) {
                                                    errorElement.show().text(`Value must be between ${min} and ${max}.`);
                                                } else {
                                                    errorElement.hide();
                                                }
                                            }

                                            function validateGrade(input, validGrades) {
                                                const value = $(input).val();
                                                const errorElement = $(input).closest('.form-group').find('.error-msg');

                                                if (validGrades.includes(value)) {
                                                    errorElement.hide();
                                                } else {
                                                    errorElement.show().text(`Invalid grade. Only uppercase A, B, C, C++, D are allowed.`);
                                                }
                                            }

                                            function showError(errorId, message) {
                                                $('#' + errorId).text(message).show();
                                            }

                                            function hideError(errorId) {
                                                $('#' + errorId).hide();
                                            }

                                            // Add more validation logic for test_date if needed
                                        });
                                        </script>
                                        <!------------------------------------  end Test Scores  --------------------------->


                                        <!------------------------------------  start NAATI  --------------------------->
                                        <div class="col-sm-12">
                                            <!-- NAATI Test Section -->
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Have you given NAATI test?</label>
                                                <div class="col-sm-3">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <select class="form-control" name="naati_test" id="naati-test-select">
                                                                <option value="0" >No</option>
                                                                <option value="1" >Yes</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-12  date-naati-cu">
                                                            <div class="date-field" id="naati-date">
                                                                <label for="naati-date-input" class="mt-3">Test Date:</label>
                                                                <input type="date" class="form-control" id="naati-date-input" name="naati_date" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PY Test Section -->
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Have you given PY test?</label>
                                                <div class="col-sm-3">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <select class="form-control" name="py_test" id="py-test-select">
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-12 date-ny-cu ">
                                                            <div class="date-field " id="py-date">
                                                                <label for="py-date-input" class="mt-3">Test Date:</label>
                                                                <input type="date" class="form-control" id="py-date-input" name="py_date" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!------------------------------------  end NAATI  --------------------------->
                                            <!----------------------------- Married Status ----------------------------------->
                                            <div class="row" id="spouse-english-question" style="display:none;">
                                                <label class="col-sm-3 col-form-label" style="color: #FFFFFF;font-size: 15px;font-weight: bold;"><b>Does your spouse have an English score?</b></label>
                                                <div class="col-sm-3">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <select class="form-control" name="spouse_english_score" id="english-score-select">
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Conditional Fields for English Score -->
                                            <div id="english-score-fields" class="row col-sm-12" style="display: none;">
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="spouse_test_type">Test Type <span style="color:#ff0000;">*</span></label>
                                                        <select name="spouse_test_type" class="form-control" id="spouse_test_type" required>
                                                            <option value="">Select Test Type</option>
                                                            <option value="IELTS Academic">IELTS Academic</option>
                                                            <option value="IELTS General Training">IELTS General Training</option>
                                                            <option value="TOEFL iBT">TOEFL iBT</option>
                                                            <option value="PTE Academic">PTE Academic</option>
                                                            <option value="OET">OET</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="spouse_listening_score">Listening</label>
                                                        <input type="text" name="spouse_listening_score" class="form-control" id="spouse_listening_score">
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="spouse_reading_score">Reading</label>
                                                        <input type="text" name="spouse_reading_score" class="form-control" id="spouse_reading_score">
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="spouse_writing_score">Writing</label>
                                                        <input type="text" name="spouse_writing_score" class="form-control" id="spouse_writing_score">
                                                    </div>
                                                </div>

                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <label for="spouse_speaking_score">Speaking</label>
                                                        <input type="text" name="spouse_speaking_score" class="form-control" id="spouse_speaking_score">
                                                    </div>
                                                </div>

                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="spouse_overall_score">Overall Score</label>
                                                        <input type="text" name="spouse_overall_score" class="form-control" id="spouse_overall_score">
                                                    </div>
                                                </div>

                                                <!-- Additional Field: English Test Date -->
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="spouse_assessment_date">Test Date</label>
                                                        <input type="date" name="spouse_test_date" class="form-control" id="spouse_test_date">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Second Conditional Question -->
                                            <div class="row" id="spouse-skill-assessment-question" style="display:none;margin-top: 5px;">
                                                <label class="col-sm-3 col-form-label" style="color: #FFFFFF;font-size: 15px;font-weight: bold;"><b>Does your spouse have a skill assessment?</b></label>
                                                <div class="col-sm-3">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <select class="form-control" name="spouse_skill_assessment" id="skill-assessment-select">
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Conditional Fields for Skill Assessment -->
                                            <div id="skill-assessment-fields" class="row col-sm-12" style="display: none;">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="spouse_skill_assessment_status">Skill Assessment Status </label>
                                                        <select name="spouse_skill_assessment_status" class="form-control" id="spouse_skill_assessment_status" required>
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="spouse_nomi_occupation">Nominated Occupation</label>
                                                        <input type="text" name="spouse_nomi_occupation" class="form-control" id="spouse_nomi_occupation">
                                                    </div>
                                                </div>

                                                <!-- Additional Field: Assessment Authority -->
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="spouse_assessment_date">Assessment Date</label>
                                                        <input type="date" name="spouse_assessment_date" class="form-control" id="spouse_assessment_date">
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                $(document).ready(function() {
                                                    // Show or hide spouse questions based on Marital Status
                                                    $('#marital-status-select').change(function() {

                                                        var maritalStatus = $(this).val();
                                                        if ( maritalStatus === 'Married' || maritalStatus === 'De facto' ) {
                                                            $('#spouse-english-question').show();
                                                            $('#spouse-skill-assessment-question').show();
                                                        } else {
                                                            $('#spouse-english-question').hide();
                                                            $('#english-score-fields').hide();
                                                            $('#spouse-skill-assessment-question').hide();
                                                            $('#skill-assessment-fields').hide();
                                                        }
                                                    });

                                                    // Show or hide English score fields based on selection
                                                    $('#english-score-select').change(function() {
                                                        var englishScore = $(this).val();
                                                        if (englishScore === '1') { // Yes selected
                                                            $('#english-score-fields').show();
                                                        } else {
                                                            $('#english-score-fields').hide();
                                                        }
                                                    });

                                                    // Show or hide Skill assessment fields based on selection
                                                    $('#skill-assessment-select').change(function() {
                                                        var skillAssessment = $(this).val();
                                                        if (skillAssessment === '1') { // Yes selected
                                                            $('#skill-assessment-fields').show();
                                                        } else {
                                                            $('#skill-assessment-fields').hide();
                                                        }
                                                    });
                                                });
                                            </script>

                                            <script>
                                            $(document).ready(function() {
                                                function toggleDateField(selectId, dateFieldId) {
                                                    var selectedValue = $(selectId).val();
                                                    if (selectedValue === '1') {
                                                        $(dateFieldId).slideDown(); // Show the date field
                                                    } else {
                                                        $(dateFieldId).slideUp(); // Hide the date field
                                                    }
                                                }

                                                // Initialize the form fields based on existing values
                                                function initializeForm() {
                                                    toggleDateField('#naati-test-select', '#naati-date');
                                                    toggleDateField('#py-test-select', '#py-date');
                                                }

                                                // Event listener for NAATI test select field
                                                $('#naati-test-select').change(function() {
                                                    toggleDateField('#naati-test-select', '#naati-date');
                                                });

                                                // Event listener for PY test select field
                                                $('#py-test-select').change(function() {
                                                    toggleDateField('#py-test-select', '#py-date');
                                                });

                                                // Initialize form on page load
                                                initializeForm();
                                            });
                                            </script>
                                            <!----------------------------- end Married Status ----------------------------------->

                                        <div class="col-sm-12">

                                        <div class="form-group float-right">
                                            {{ Form::button('Save', ['class'=>'btn btn-primary btn_submit', 'onClick'=>'customValidate("add-leads")' ]) }}
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </section>
                    </div>

@endsection

@section('scripts')



<script>
jQuery(document).ready(function($){

	$("#country_select").select2({ width: '200px' });


    /*$(document).delegate('#pac-input', 'blur', function(){
		var address = $.trim($(this).val());
        $.ajax({
			type:'post',
            url:"{{URL::to('/')}}/admin/address_auto_populate",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {address:address},
            success: function(response){

            }
		});
	});*/

    $(document).delegate('.add_other_email_phone', 'click', function(){
		if ($('.other_email_div').css('display') == 'none') {
			$('.other_email_div').css('display','inline-block');
			$('.other_phone_div').css('display','inline-block');
			$('.add_other_email_phone').html('<i class="fa fa-minus" aria-hidden="true"></i>');
		} else {
			$('.other_email_div').css('display','none');
			$('.other_phone_div').css('display','none');
			$('.add_other_email_phone').html('<i class="fa fa-plus" aria-hidden="true"></i>');
		}
	});

    //Check email uniqueness
    $(document).delegate('.email_unique', 'keyup blur', function(){
        var email = $.trim($(this).val());
        $.ajax({
			type:'post',
            url:"{{URL::to('/')}}/admin/is_email_unique",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {email:email},
            success: function(response){
                var obj = $.parseJSON(response);
                if(obj.status == 1){
                    var message = '<strong>'+obj.message+'</strong>';
                    //console.log('==='+$( ".email_unique").parent().find('span.custom-error').length)
                    if( $( ".email_unique").parent().find('span.custom-error').length <1){
                        $('.email_unique').after(errorDisplay(message));
                    }
                    $('.email_unique').attr('data-valid','required');
                } else {
                    $(".email_unique").parent().find('span.custom-error').remove();
                    $('.email_unique').attr('data-valid','');
                }
            }
		});
    });

    //Check contact number uniqueness
    $(document).delegate('.contactno_unique', 'keyup blur', function(){
        var contact = $.trim($(this).val());
        $.ajax({
			type:'post',
            url:"{{URL::to('/')}}/admin/is_contactno_unique",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {contact:contact},
            success: function(response){
                var obj = $.parseJSON(response);
                if(obj.status == 1){
                    var message = '<strong>'+obj.message+'</strong>';
                    //console.log('==='+$( ".contactno_unique").parent().find('span.custom-error').length)
                    if( $( ".contactno_unique").parent().find('span.custom-error').length <1){
                        $('.contactno_unique').after(errorDisplay(message));
                    }
                    $('.contactno_unique').attr('data-valid','required');
                } else {
                    $(".contactno_unique").parent().find('span.custom-error').remove();
                    $('.contactno_unique').attr('data-valid','');
                }
            }
		});
    });

    $('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,

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
	templateResult: formatRepo,
	templateSelection: formatRepoSelection
});
function formatRepo (repo) {
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

function formatRepoSelection (repo) {
  return repo.name || repo.text;
}

    //Datepicker show on whole date field not only on icon
    $(document).on('focus click', '.field-input, .start-date-input, .finish-date-input, .job-start-date-input, .job-finish-date-input, .date-input, .test-date-input, #naati-date-input, #py-date-input, #spouse_test_date, #spouse_assessment_date', function() {
    this.showPicker();
    });
});
</script>


<style>
/* ----------------------- */
/* start Email specific styles */
/* ----------------------- */
    .col-sm-12 {
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 100%;
        margin: 0px -15px;
    }
	.theme-white .btn-primary {
    background-color: #FCCD02 !important;
    border-color: transparent !important;
    color: #fff !important;
    margin: 31px 8px 9px 46px !important;
}
.error-border {
border: 2px solid red; /* Red border for invalid fields */
}
.error-message {
color: red;
font-size: 0.875em;
display: none; /* Hide error message by default */
}
.readonly {
background-color: #f5f5f5; /* Light grey background for readonly fields */
cursor: not-allowed;
}
button#add-email-0 {
margin: 1px 0px -11px;
}

/* ----------------------- */
/* Country and Visa Fields  */
/* ----------------------- */

.error-border {
border: 2px solid red; /* Red border for incomplete rows */
}
.readonly {
background-color: #f5f5f5; /* Light grey background for readonly fields */
cursor: not-allowed;
}
.disabled-button {
cursor: not-allowed;
opacity: 0.5;
}
.description-container {
display: none; /* Initially hide the description field */
}

/* ----------------------- */
/* Address Auto-Fill  */
/* ----------------------- */


.autocomplete-items
{
border: 1px solid #d4d4d4;
border-bottom: none;
border-top: none;
z-index: 99;
top: 100%;
left: 0;
right: 0;
background-color: white;
max-height: 100px;
overflow-y: auto;
position: absolute;
}
.autocomplete-item {
padding: 10px;
cursor: pointer;
}
.autocomplete-item:hover {
background-color: #d3d3d3;
}
.error-message {
color: red;
margin-top: 10px;
}
.error-row {
border: 2px solid red;
}


/* ----------------------- */
/* start  Qualification Management  */
/* ----------------------- */
.error-message {
color: red;
margin-top: 10px;
}
.error-row {
border: 2px solid red; /* Red border for incomplete rows */
}
button.btn.btn-primary.add-client-experience {
margin: 24px 0 -6px 31px;
}
button.btn.btn-primary.add-qualification-btn {
margin: 31px 4px 19px 4px;
}



/* ----------------------- */
/* start  occupation   */
/* ----------------------- */



.autocomplete-items {
border: 1px solid #d4d4d4;
border-bottom: none;
border-top: none;
z-index: 99;
top: 100%;
left: 0;
right: 0;
background-color: white;
max-height: 100px;
overflow-y: auto;
position: absolute;
}
.autocomplete-item {
padding: 10px;
cursor: pointer;
}
.autocomplete-item:hover {
background-color: #d3d3d3;
}
.error-message {
color: red;
margin-top: 10px;
}
.error-row {
border: 2px solid red;
}
.form-control {
border-radius: 0.25rem;
}

/* ----------------------- */
/* Start Test Scores  occupation  */
/* ----------------------- */

button.btn.btn-primary.add-test-score {
margin: 31px 6px 14px 14px;
}
.form-group.occupation-date-cu {
margin: -10px 4px 28px 4px;
}
.error-msg {
color: red;
font-size: 12px;
display: none;
}

/* ----------------------- */
/* start NAATI test */
/* ----------------------- */


.hidden-date {
display: none;
}
.date-field {
margin-top: 10px;
}
input#naati-date-input {
margin: -41px 1px 13px 81px;
}
div#naati-date {
margin: -8px 16px 27px -18px;
}
div#py-date {
margin: -8px 16px 27px -18px;
}
input#py-date-input {
margin: -41px 1px 13px 81px;
}
.col-sm-12.date-naati-cu {
margin: -37px 4px 1px 153px;
}
.col-sm-12.date-ny-cu {
margin: -37px 4px 1px 153px;
}
select#naati-test-select {
margin: 1px -63px;
}
select#py-test-select{
margin: 1px -63px;
}

button.btn.btn-primary.add-skill-assessment {
margin: 32px 11px 7px;
}
</style>

@endsection
