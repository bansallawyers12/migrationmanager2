@extends('layouts.admin_client_detail_dashboard')

@section('content')
    <style>
    /* Ensure extra fields are in a single row */
    .partner-extra-fields .content-grid.single-row {
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    @media (max-width: 768px) {
        .partner-extra-fields .content-grid.single-row {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="crm-container">
        <div class="main-content">
            <div class="client-header" style="padding-top: 35px;">
                <div>
                    <h1>Create New Lead</h1>
                </div>
                <div class="client-status">
                    <button class="btn btn-secondary" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Back</button>
                    <button class="btn btn-primary" type="submit" form="createLeadForm"><i class="fas fa-save"></i> Save Lead</button>
                </div>
            </div>

            <!-- Display General Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="content-tabs">
                <button class="tab-button active" onclick="openTab(event, 'personalTab')"><i class="fas fa-user"></i> Personal</button>
                <button class="tab-button" onclick="openTab(event, 'visaPassportCitizenshipTab')"><i class="fas fa-passport"></i> Visa, Passport & Citizenship</button>
                <button class="tab-button" onclick="openTab(event, 'addressTravelTab')"><i class="fas fa-map-marker-alt"></i> Address & Travel</button>
                <button class="tab-button" onclick="openTab(event, 'skillsEducationTab')"><i class="fas fa-briefcase"></i> Skills & Education</button>
                <button class="tab-button" onclick="openTab(event, 'otherInformationTab')"><i class="fas fa-info-circle"></i> Other Information</button>
                <button class="tab-button" onclick="openTab(event, 'familyTab')"><i class="fas fa-info-circle"></i> Family Information</button>
            </div>

            <form id="createLeadForm" action="{{ route('admin.leads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Personal Tab -->
                <div id="personalTab" class="tab-content active">
                    <section class="form-section">
                        <h3><i class="fas fa-id-card"></i> Basic Information</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="last_name" value="{{ old('last_name') }}">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="text" id="dob" name="dob" value="{{ old('dob') }}" class="date-picker" placeholder="dd/mm/yyyy">
                                @error('dob')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- DOB Verified Slider -->
                            <div class="form-group" style="align-items: center;">
                                <label>DOB Verified?</label>
                                <label class="switch">
                                    <input type="checkbox" name="dob_verified" value="1" {{ old('dob_verified') ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="text" id="age" name="age" value="{{ old('age') }}" readonly>
                            </div>

                            <div class="form-group" id="dobVerifyDocumentGroup" style="display: none;">
                                <label for="dob_verify_document">DOB Verify Document</label>
                                <input type="text" id="dob_verify_document" name="dob_verify_document" value="{{ old('dob_verify_document') }}">
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <div class="radio-group">
                                    <label><input type="radio" name="gender" value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }}> Male</label>
                                    <label><input type="radio" name="gender" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}> Female</label>
                                    <label><input type="radio" name="gender" value="Other" {{ old('gender') == 'Other' ? 'checked' : '' }}> Other</label>
                                </div>
                                @error('gender')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="maritalStatus">Marital Status</label>
                                <select id="maritalStatus" name="marital_status">
                                    <option value="">Select Marital Status</option>
                                    <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="De Facto" {{ old('marital_status') == 'De Facto' ? 'selected' : '' }}>De Facto</option>
                                    <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                </select>
                                @error('marital_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Contact Information Section -->
                    <section class="form-section">
                        <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Emergency Contact Type</label>
                                <select name="emergency_contact_type" class="contact-type-selector">
                                    <option value="Personal" {{ old('emergency_contact_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Work" {{ old('emergency_contact_type') == 'Work' ? 'selected' : '' }}>Work</option>
                                    <option value="Mobile" {{ old('emergency_contact_type') == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                    <option value="Business" {{ old('emergency_contact_type') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Secondary" {{ old('emergency_contact_type') == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                                    <option value="Father" {{ old('emergency_contact_type') == 'Father' ? 'selected' : '' }}>Father</option>
                                    <option value="Mother" {{ old('emergency_contact_type') == 'Mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="Brother" {{ old('emergency_contact_type') == 'Brother' ? 'selected' : '' }}>Brother</option>
                                    <option value="Sister" {{ old('emergency_contact_type') == 'Sister' ? 'selected' : '' }}>Sister</option>
                                    <option value="Uncle" {{ old('emergency_contact_type') == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                    <option value="Aunt" {{ old('emergency_contact_type') == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                    <option value="Cousin" {{ old('emergency_contact_type') == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                                    <option value="Others" {{ old('emergency_contact_type') == 'Others' ? 'selected' : '' }}>Others</option>
                                    <option value="Partner" {{ old('emergency_contact_type') == 'Partner' ? 'selected' : '' }}>Partner</option>
                                    <option value="Not In Use" {{ old('emergency_contact_type') == 'Not In Use' ? 'selected' : '' }}>Not In Use</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Number</label>
                                <div class="cus_field_input" style="display:flex;">
                                    <div class="country_code">
                                        <input class="telephone" id="telephone" type="tel" name="emergency_country_code" value="{{ old('emergency_country_code') }}" style="width: 55px;height: 42px;" readonly >
                                    </div>
                                    <input type="text" name="emergency_contact_no" value="{{ old('emergency_contact_no') }}" class="form-control tel_input" autocomplete="off" placeholder="Emergency Contact Number" style="width: 230px;">
                                </div>
                            </div>
                        </div>
                        <!-- Phone Numbers -->
                        <div class="form-section">
                            <h4><i class="fas fa-phone"></i> Phone Numbers</h4>
                            <div id="phoneNumbersContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                                <button type="button" class="add-item-btn" onclick="addPhoneNumber()"><i class="fas fa-plus"></i> Add Phone Number</button>
                                <div class="form-group" style="align-items: center;">
                                    <label>Recent Phone Verified?</label>
                                    <label class="switch">
                                        <input type="checkbox" name="phone_verified" value="1" {{ old('phone_verified') ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Email Addresses -->
                        <div class="form-section">
                            <h4><i class="fas fa-envelope"></i> Email Addresses</h4>
                            <div id="emailAddressesContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                                <button type="button" class="add-item-btn" onclick="addEmailAddress()"><i class="fas fa-plus"></i> Add Email Address</button>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Visa, Passport & Citizenship Tab -->
                <div id="visaPassportCitizenshipTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-globe"></i> Country of Passport</h3>
                        <div class="form-group">
                            <label for="passportCountry">Country of Passport</label>
                            <select name="visa_country[]" id="passportCountry" class="passport-country-selector">
                                <option value="">Select Country</option>
                                <option value="India" {{ old('visa_country.0') == 'India' ? 'selected' : '' }}>India</option>
                                <option value="Australia" {{ old('visa_country.0') == 'Australia' ? 'selected' : '' }}>Australia</option>
                                @foreach(\App\Models\Country::all() as $list)
                                    @if($list->name != 'India' && $list->name != 'Australia')
                                        <option value="{{ $list->name }}" {{ old('visa_country.0') == $list->name ? 'selected' : '' }}>{{ $list->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('visa_country.0')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-passport"></i> Passport Information</h3>
                        <div id="passportDetailsContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPassportDetail()"><i class="fas fa-plus"></i> Add Passport</button>
                    </section>

                    <section class="form-section" id="visaDetailsSection">
                        <h3 id="visaDetailsContainerHeading"><i class="fas fa-file-contract"></i> Visa Details</h3>
                        <div id="visaDetailsContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                            <button type="button" class="add-item-btn" onclick="addVisaDetail()"><i class="fas fa-plus"></i> Add Visa Detail</button>
                            <div class="form-group" style="align-items: center;" id="visaExpiryVerifiedContainer">
                                <label>Visa Expiry Verified?</label>
                                <label class="switch">
                                    <input type="checkbox" name="visa_expiry_verified" value="1" {{ old('visa_expiry_verified') ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Address & Travel Tab -->
                <div id="addressTravelTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-map-marker-alt"></i> Addresses</h3>
                        <div class="form-group" style="align-items: center;">
                            <label>Is this your current address?</label>
                            <label class="switch">
                                <input type="checkbox" id="isCurrentAddress" name="is_current_address" value="1" {{ old('is_current_address') ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div id="addressesContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addAddress()"><i class="fas fa-plus"></i> Add Address</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-plane"></i> Travel History</h3>
                        <div id="travelDetailsContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addTravelDetail()"><i class="fas fa-plus"></i> Add Travel Detail</button>
                    </section>
                </div>

                <!-- Skills & Education Tab -->
                <div id="skillsEducationTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-graduation-cap"></i> Qualifications</h3>
                        <div id="qualificationsContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addQualification()"><i class="fas fa-plus"></i> Add Qualification</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-briefcase"></i> Work Experience</h3>
                        <div id="experienceContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addExperience()"><i class="fas fa-plus"></i> Add Experience</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-cogs"></i> Occupation & Skills</h3>
                        <div id="occupationContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addOccupation()"><i class="fas fa-plus"></i> Add Occupation</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-language"></i> English Test Scores</h3>
                        <div id="testScoresContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addTestScore()"><i class="fas fa-plus"></i> Add Test Score</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-certificate"></i> Other Tests / Credentials</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>NAATI CCL Test?</label>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <label class="switch">
                                        <input type="checkbox" id="naatiGiven" name="naati_test" value="1" {{ old('naati_test') ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <input type="text" id="naatiDate" name="naati_date" value="{{ old('naati_date') }}" style="max-width: 150px;" placeholder="dd/mm/yyyy" class="date-picker naati-date">
                                    @error('naati_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <input type="text" id="nati_language" name="nati_language" value="" style="max-width: 150px;" placeholder="Nati Language">

                                </div>
                            </div>
                            <div class="form-group">
                                <label>Professional Year (PY)?</label>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <label class="switch">
                                        <input type="checkbox" id="pyGiven" name="py_test" value="1" {{ old('py_test') ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <input type="text" id="pyDate" name="py_date" value="{{ old('py_date') }}" style="max-width: 150px;" placeholder="dd/mm/yyyy" class="date-picker py-date">
                                    @error('py_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <select name="py_field" id="py_field">
                                        <option value="">Select Type</option>
                                        <option value="Accounting">Accounting</option>
                                        <option value="IT">IT</option>
                                        <option value="Engineering">Engineering</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Regional Points</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <input type="text" id="regional_points" name="regional_points" value="" style="max-width: 150px;">
                            </div>
                        </div>
                    </section>

                    <section class="form-section" id="spouseDetailsSection" style="display: none;">
                        <h3><i class="fas fa-user"></i> Spouse Details</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Does your spouse have an English score? *</label>
                                <select name="spouse_has_english_score" id="spouseHasEnglishScore" onchange="toggleSpouseEnglishFields()">
                                    <option value="No" {{ old('spouse_has_english_score') == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Yes" {{ old('spouse_has_english_score') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Does your spouse have a skill assessment? *</label>
                                <select name="spouse_has_skill_assessment" id="spouseHasSkillAssessment" onchange="toggleSpouseSkillFields()">
                                    <option value="No" {{ old('spouse_has_skill_assessment') == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Yes" {{ old('spouse_has_skill_assessment') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>

                        <!-- English Score Fields (Hidden by Default) -->
                        <div id="spouseEnglishFields" style="display: none;">
                            <div class="content-grid">
                                <div class="form-group">
                                    <label>Spouse Test Type</label>
                                    <select name="spouse_test_type">
                                        <option value="">Select Test</option>
                                        <option value="IELTS" {{ old('spouse_test_type') == 'IELTS' ? 'selected' : '' }}>IELTS General</option>
                                        <option value="IELTS_A" {{ old('spouse_test_type') == 'IELTS_A' ? 'selected' : '' }}>IELTS Academic</option>
                                        <option value="PTE" {{ old('spouse_test_type') == 'PTE' ? 'selected' : '' }}>PTE Academic</option>
                                        <option value="TOEFL" {{ old('spouse_test_type') == 'TOEFL' ? 'selected' : '' }}>TOEFL iBT</option>
                                        <option value="CAE" {{ old('spouse_test_type') == 'CAE' ? 'selected' : '' }}>CAE</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Spouse Listening Score</label>
                                    <input type="text" name="spouse_listening_score" value="{{ old('spouse_listening_score') }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Reading Score</label>
                                    <input type="text" name="spouse_reading_score" value="{{ old('spouse_reading_score') }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Writing Score</label>
                                    <input type="text" name="spouse_writing_score" value="{{ old('spouse_writing_score') }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Speaking Score</label>
                                    <input type="text" name="spouse_speaking_score" value="{{ old('spouse_speaking_score') }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Overall Score</label>
                                    <input type="text" name="spouse_overall_score" value="{{ old('spouse_overall_score') }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Test Date</label>
                                    <input type="text" name="spouse_test_date" value="{{ old('spouse_test_date') }}" placeholder="dd/mm/yyyy" class="date-picker">
                                    @error('spouse_test_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Skill Assessment Fields (Hidden by Default) -->
                        <div id="spouseSkillFields" style="display: none;">
                            <div class="content-grid">
                                <div class="form-group">
                                    <label>Spouse Skill Assessment Status</label>
                                    <input type="text" name="spouse_skill_assessment_status" value="{{ old('spouse_skill_assessment_status') }}" placeholder="Enter Status">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Nominated Occupation</label>
                                    <input type="text" name="spouse_nomi_occupation" value="{{ old('spouse_nomi_occupation') }}" placeholder="Enter Occupation">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Assessment Date</label>
                                    <input type="text" name="spouse_assessment_date" value="{{ old('spouse_assessment_date') }}" placeholder="dd/mm/yyyy" class="date-picker">
                                    @error('spouse_assessment_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Other Information Tab -->
                <div id="otherInformationTab" class="tab-content">
                    <!-- Lead Management Fields -->
                    <section class="form-section">
                        <h3><i class="fas fa-cog"></i> Lead Management</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label for="contact_type">Contact Type <span class="text-danger">*</span></label>
                                <select id="contact_type" name="contact_type" required>
                                    <option value="">Select Contact Type</option>
                                    <option value="Personal" {{ old('contact_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Office" {{ old('contact_type') == 'Office' ? 'selected' : '' }}>Office</option>
                                </select>
                                @error('contact_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email_type">Email Type <span class="text-danger">*</span></label>
                                <select id="email_type" name="email_type" required>
                                    <option value="">Select Email Type</option>
                                    <option value="Personal" {{ old('email_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Business" {{ old('email_type') == 'Business' ? 'selected' : '' }}>Business</option>
                                </select>
                                @error('email_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="service">Service <span class="text-danger">*</span></label>
                                <select id="service" name="service" required>
                                    <option value="">Select Service</option>
                                    @foreach(\App\Models\Matter::select('id', 'title', 'nick_name')->where('status', 1)->orderBy('title', 'ASC')->get() as $service)
                                        <option value="{{$service->id}}" {{ old('service') == $service->id ? 'selected' : '' }}>{{$service->title}} ({{@$service->nick_name}})</option>
                                    @endforeach
                                </select>
                                @error('service')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="assign_to">Assign To <span class="text-danger">*</span></label>
                                <select id="assign_to" name="assign_to" required>
                                    <option value="">Select User</option>
                                    @php
                                        $admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                    @endphp
                                    @foreach($admins as $admin)
                                        @php
                                            $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
                                        @endphp
                                        <option value="{{$admin->id}}" {{ old('assign_to') == $admin->id ? 'selected' : '' }}>
                                            {{$admin->first_name}} {{$admin->last_name}} ({{@$branchname->office_name}})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assign_to')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="lead_quality">Lead Quality <span class="text-danger">*</span></label>
                                <select id="lead_quality" name="lead_quality" required>
                                    <option value="">Select Quality</option>
                                    <option value="1" {{ old('lead_quality') == '1' ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ old('lead_quality') == '2' ? 'selected' : '' }}>2</option>
                                    <option value="3" {{ old('lead_quality') == '3' ? 'selected' : '' }}>3</option>
                                    <option value="4" {{ old('lead_quality') == '4' ? 'selected' : '' }}>4</option>
                                    <option value="5" {{ old('lead_quality') == '5' ? 'selected' : '' }}>5</option>
                                </select>
                                @error('lead_quality')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="lead_source">Lead Source <span class="text-danger">*</span></label>
                                <select id="lead_source" name="lead_source" required>
                                    <option value="">Select Lead Source</option>
                                    <option value="Website" {{ old('lead_source') == 'Website' ? 'selected' : '' }}>Website</option>
                                    <option value="Google Ads" {{ old('lead_source') == 'Google Ads' ? 'selected' : '' }}>Google Ads</option>
                                    <option value="Facebook" {{ old('lead_source') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="Referral" {{ old('lead_source') == 'Referral' ? 'selected' : '' }}>Referral</option>
                                    <option value="Walk-in" {{ old('lead_source') == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                                    <option value="Phone Call" {{ old('lead_source') == 'Phone Call' ? 'selected' : '' }}>Phone Call</option>
                                    <option value="Email" {{ old('lead_source') == 'Email' ? 'selected' : '' }}>Email</option>
                                    <option value="LinkedIn" {{ old('lead_source') == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                                    <option value="Instagram" {{ old('lead_source') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="YouTube" {{ old('lead_source') == 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="Partner Agency" {{ old('lead_source') == 'Partner Agency' ? 'selected' : '' }}>Partner Agency</option>
                                    <option value="Trade Show" {{ old('lead_source') == 'Trade Show' ? 'selected' : '' }}>Trade Show</option>
                                    <option value="Other" {{ old('lead_source') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('lead_source')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>
                    
                    <section class="form-section">
                        <h3><i class="fas fa-exclamation-circle"></i> Character & History</h3>

                        <!-- Criminal Charges -->
                        <div class="form-section">
                            <h4>Criminal Charges</h4>
                            <div id="criminalChargesContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('criminalChargesContainer', 'criminal_charges')"><i class="fas fa-plus"></i> Add Criminal Charge</button>
                        </div>

                        <!-- Military Service -->
                        <div class="form-section">
                            <h4>Military Service</h4>
                            <div id="militaryServiceContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('militaryServiceContainer', 'military_service')"><i class="fas fa-plus"></i> Add Military Service</button>
                        </div>

                        <!-- Intelligence Work -->
                        <div class="form-section">
                            <h4>Intelligence Work</h4>
                            <div id="intelligenceWorkContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('intelligenceWorkContainer', 'intelligence_work')"><i class="fas fa-plus"></i> Add Intelligence Work</button>
                        </div>

                        <!-- Visa Refusals -->
                        <div class="form-section">
                            <h4>Visa Refusals</h4>
                            <div id="visaRefusalsContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('visaRefusalsContainer', 'visa_refusals')"><i class="fas fa-plus"></i> Add Visa Refusal</button>
                        </div>

                        <!-- Deportations -->
                        <div class="form-section">
                            <h4>Deportations</h4>
                            <div id="deportationsContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('deportationsContainer', 'deportations')"><i class="fas fa-plus"></i> Add Deportation</button>
                        </div>

                        <!-- Citizenship Refusals -->
                        <div class="form-section">
                            <h4>Citizenship Refusals</h4>
                            <div id="citizenshipRefusalsContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('citizenshipRefusalsContainer', 'citizenship_refusals')"><i class="fas fa-plus"></i> Add Citizenship Refusal</button>
                        </div>

                        <!-- Health Declaration -->
                        <div class="form-section">
                            <h4>Health Declaration</h4>
                            <div id="healthDeclarationsContainer">
                                <!-- Initially empty; will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-item-btn" onclick="addCharacterRow('healthDeclarationsContainer', 'health_declarations')"><i class="fas fa-plus"></i> Add Health Declaration</button>
                        </div>
                    </section>

                </div>

                <!-- Family Information Tab -->
                <div id="familyTab" class="tab-content">
                    <h3><i class="fas fa-users"></i> Family Members</h3>
                    <!-- Partner Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-users"></i> Partner</h3>
                        <div id="partnerContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('partner')"><i class="fas fa-plus"></i> Add Partner</button>
                    </section>

                    <!-- Children Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-child"></i> Children</h3>
                        <div id="childrenContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('children')"><i class="fas fa-plus"></i> Add Child</button>
                    </section>

                    <!-- Parent Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-user-friends"></i> Parents</h3>
                        <div id="parentContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('parent')"><i class="fas fa-plus"></i> Add Parent</button>
                    </section>

                    <!-- Siblings Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-users"></i> Siblings</h3>
                        <div id="siblingsContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('siblings')"><i class="fas fa-plus"></i> Add Sibling</button>
                    </section>

                    <!-- Others Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-users"></i> Others</h3>
                        <div id="othersContainer">
                            <!-- Initially empty; will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('others')"><i class="fas fa-plus"></i> Add Other</button>
                    </section>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            :root {
                --primary-color: #005792;
                --secondary-color: #00BBF0;
                --background-color: #f4f7fc;
                --card-bg-color: #ffffff;
                --text-color: #333333;
                --text-muted-color: #666666;
                --border-color: #dcdcdc;
                --input-border-color: #ced4da;
                --input-focus-border-color: var(--secondary-color);
                --danger-color: #dc3545;
                --success-color: #28a745;
            }

            .crm-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
                background: var(--background-color);
            }

            .main-content {
                background: var(--card-bg-color);
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                padding: 20px;
            }

            .client-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-bottom: 15px;
                border-bottom: 1px solid var(--border-color);
                margin-bottom: 20px;
            }

            .client-header h1 {
                font-size: 1.8em;
                color: var(--primary-color);
                margin: 0;
            }

            .client-id {
                font-size: 0.9em;
                color: var(--text-muted-color);
            }

            .client-status .btn {
                padding: 8px 15px;
                font-size: 0.9em;
                border-radius: 5px;
                cursor: pointer;
            }

            .btn-secondary {
                background: #6c757d;
                color: white;
                border: none;
            }

            .btn-primary {
                background: var(--primary-color);
                color: white;
                border: none;
            }

            .content-tabs {
                display: flex;
                border-bottom: 1px solid var(--border-color);
                margin-bottom: 20px;
            }

            .tab-button {
                padding: 12px 20px;
                background: #f1f3f5;
                border: 1px solid var(--border-color);
                border-bottom: none;
                border-radius: 5px 5px 0 0;
                margin-right: 5px;
                cursor: pointer;
                color: var(--text-muted-color);
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .tab-button i {
                margin-right: 8px;
            }

            .tab-button.active {
                background: var(--card-bg-color);
                color: var(--primary-color);
                border-bottom: 2px solid var(--primary-color);
            }

            .tab-button:hover:not(.active) {
                background: #e9ecef;
            }

            .tab-content {
                display: none;
                padding: 20px;
            }

            .tab-content.active {
                display: block;
            }

            .form-section {
                margin-bottom: 30px;
            }

            .form-section h3 {
                font-size: 1.3em;
                color: var(--primary-color);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
            }

            .form-section h3 i {
                margin-right: 10px;
                color: var(--secondary-color);
            }

            .content-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }

            /* Family members specific grid - 4 columns in one row */
            #familyTab .content-grid {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 15px !important;
            }

            /* Responsive design for family members */
            @media (max-width: 1200px) {
                #familyTab .content-grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }

            @media (max-width: 768px) {
                #familyTab .content-grid {
                    grid-template-columns: 1fr !important;
                }
            }

            .form-group {
                display: flex;
                flex-direction: column;
            }

            .form-group label {
                font-size: 0.95em;
                font-weight: 500;
                color: var(--text-muted-color);
                margin-bottom: 5px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 10px;
                border: 1px solid var(--input-border-color);
                border-radius: 5px;
                font-size: 1em;
                color: var(--text-color);
                background: #fff;
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
            }

            .form-group input:focus,
            .form-group select:focus,
            .form-group textarea:focus {
                border-color: var(--input-focus-border-color);
                box-shadow: 0 0 5px rgba(0, 187, 240, 0.3);
                outline: none;
            }

            .form-group input[readonly] {
                background: #f8f9fa;
                cursor: not-allowed;
            }

            .input-group {
                display: flex;
                align-items: center;
            }

            .input-group input:first-child {
                border-radius: 5px 0 0 5px;
                border-right: none;
            }

            .input-group input:last-child {
                border-radius: 0 5px 5px 0;
            }

            .radio-group {
                display: flex;
                gap: 15px;
            }

            .radio-group label {
                font-weight: normal;
                color: var(--text-color);
                display: flex;
                align-items: center;
            }

            .radio-group input[type="radio"] {
                margin-right: 5px;
            }

            .repeatable-section {
                border: 1px dashed var(--border-color);
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 5px;
                position: relative;
                background: #fafafa;
            }

            .remove-item-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                background: none;
                border: none;
                color: var(--danger-color);
                font-size: 1.2em;
                cursor: pointer;
                transition: color 0.3s ease;
            }

            .remove-item-btn:hover {
                color: #a71d2a;
            }

            .add-item-btn {
                padding: 8px 15px;
                background: var(--primary-color);
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 0.9em;
                transition: background 0.3s ease;
            }

            .add-item-btn i {
                margin-right: 5px;
            }

            .add-item-btn:hover {
                background: #004670;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
            }

            .switch input {
                display: none;
            }

            .slider {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: #ccc;
                border-radius: 24px;
                transition: 0.4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background: white;
                border-radius: 50%;
                transition: 0.4s;
            }

            input:checked + .slider {
                background: var(--success-color);
            }

            input:checked + .slider:before {
                transform: translateX(26px);
            }

            .points-display {
                font-size: 1.5em;
                font-weight: 600;
                color: var(--primary-color);
            }

            /* Error Message Styling */
            .text-danger {
                color: #dc3545;
                font-size: 0.9em;
                margin-top: 5px;
            }

            .alert-danger {
                background-color: #f8d7da;
                color: #721c24;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }

            .alert-danger ul {
                margin: 0;
                padding-left: 20px;
            }


            /* Autocomplete container */
            .autocomplete-items {
                position: absolute;
                z-index: 1000;
                background-color: #fff;
                /*border: 1px solid var(--border-color);*/
                border-radius: 5px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                max-height: 200px;
                overflow-y: auto;
                width: 100%;
                margin-top: 5px;
            }

            /* Autocomplete item */
            .autocomplete-item {
                padding: 10px 15px;
                font-size: 0.95em;
                color: var(--text-color);
                cursor: pointer;
                transition: background-color 0.2s ease;
            }

            /* Hover effect for autocomplete items */
            .autocomplete-item:hover {
                background-color: #f1f3f5;
            }

            /* No results message */
            .autocomplete-item.autocomplete-no-results {
                color: var(--text-muted-color);
                font-style: italic;
                cursor: default;
            }

            .autocomplete-item.autocomplete-no-results:hover {
                background-color: transparent;
            }

            /* Ensure the form-group is positioned relatively for absolute positioning of the autocomplete */
            .form-group {
                position: relative;
            }

            @media (max-width: 768px) {
                .client-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .client-status {
                    margin-top: 10px;
                }
                .content-tabs {
                    flex-wrap: wrap;
                }
                .tab-button {
                    flex: 1 0 100%;
                    margin-bottom: 5px;
                    border-radius: 5px;
                }
                .content-grid {
                    grid-template-columns: 1fr;
                }
            }
            /* Added styling for the verification sliders next to buttons */
            .form-section .add-item-btn + .form-group {
                margin-left: 20px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Toggle Visa Details Section based on Country of Passport
            document.getElementById('passportCountry').addEventListener('change', function() {
                const visaDetailsSection = document.getElementById('visaDetailsSection');
                const selectedCountry = this.value;

                // Hide Visa Details if the country is Australia, show otherwise
                if (selectedCountry === 'Australia') {
                    visaDetailsSection.style.display = 'none';
                } else {
                    visaDetailsSection.style.display = 'block';
                }
            });

            // Tab functionality
            function openTab(evt, tabName) {
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tab-content");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tab-button");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.className += " active";
            }

            // Initialize datepickers
            function initializeDatepickers() {
                $('.date-picker').each(function() {
                    if (!$(this).data('daterangepicker')) {
                        $(this).attr('autocomplete', 'off'); // Disable browser autocomplete
                        $(this).daterangepicker({
                            singleDatePicker: true,
                            showDropdowns: true,
                            autoUpdateInput: false,
                            locale: {
                                format: 'DD/MM/YYYY',
                                applyLabel: 'Apply',
                                cancelLabel: 'Cancel',
                                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                                monthNames: [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                ],
                                firstDay: 1
                            },
                            autoApply: true,
                            minDate: '01/01/1900',
                            minYear: 1900,
                            maxYear: parseInt(moment().format('YYYY')) + 50,

                        }).on('apply.daterangepicker', function(ev, picker) {
                            // Get the selected year directly from the datepicker's year dropdown
                            var selectedYear = parseInt(picker.container.find('.calendar-table .yearselect').val());
                            var selectedMonth = picker.startDate.month(); // 0-based (0 = January)
                            var selectedDay = picker.startDate.date();
                            // Construct the correct date using the selected year
                            var correctDate = moment([selectedYear, selectedMonth, selectedDay]);
                            console.log(correctDate.format('DD/MM/YYYY')); // Debug the correct date
                            $(this).val(correctDate.format('DD/MM/YYYY')); // Set the correct date
                        }).on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                        }).on('show.daterangepicker', function(ev, picker) {
                            // Update the datepicker's view to reflect the selected date in the input field
                            var inputDate = $(this).val();
                            if (inputDate) {
                                var parsedDate = moment(inputDate, 'DD/MM/YYYY');
                                if (parsedDate.isValid()) {
                                    picker.setStartDate(parsedDate);
                                    picker.setEndDate(parsedDate);
                                    // Update the dropdown to reflect the selected year
                                    picker.container.find('.calendar-table .yearselect').val(parsedDate.year());
                                    // Update the month dropdown as well
                                    picker.container.find('.calendar-table .monthselect').val(parsedDate.month());
                                }
                            }
                            picker.container.css('z-index', '1050');
                            picker.container.css({
                                'top': $(this).offset().top + $(this).outerHeight(),
                                'left': $(this).offset().left
                            });
                        });
                    }
                });
            }

            // Calculate age based on DOB in years and months
            $('#dob').on('apply.daterangepicker', function(ev, picker) {
                var dob = picker.startDate;
                var today = moment('2025-05-30', 'YYYY-MM-DD');
                if (dob.isAfter(today)) {
                    $('#age').val('');
                    return;
                }

                var years = today.diff(dob, 'years');
                dob.add(years, 'years');
                var months = today.diff(dob, 'months');

                var ageString = '';
                if (years > 0) {
                    ageString += years + (years === 1 ? ' year' : ' years');
                }
                if (months > 0) {
                    ageString += (years > 0 ? ' and ' : '') + months + (months === 1 ? ' month' : ' months');
                }
                if (years === 0 && months === 0) {
                    ageString = '0 months';
                }

                $('#age').val(ageString);
            });

            // Ensure DOB datepicker is initialized on page load
            document.addEventListener('DOMContentLoaded', function() {
                initializeDatepickers();
                
                // Initialize DOB Verify Document field visibility and validation
                toggleDobVerifyDocument();
            });

            // Toggle DOB Verify Document field visibility and validation
            function toggleDobVerifyDocument() {
                const dobVerifiedCheckbox = document.querySelector('input[name="dob_verified"]');
                const dobVerifyDocumentGroup = document.getElementById('dobVerifyDocumentGroup');
                const dobVerifyDocumentInput = document.getElementById('dob_verify_document');
                
                if (dobVerifiedCheckbox.checked) {
                    dobVerifyDocumentGroup.style.display = 'block';
                    dobVerifyDocumentInput.setAttribute('required', 'required');
                } else {
                    dobVerifyDocumentGroup.style.display = 'none';
                    dobVerifyDocumentInput.removeAttribute('required');
                    dobVerifyDocumentInput.value = ''; // Clear the value when hidden
                }
            }

            // Add event listener for DOB Verified checkbox
            document.addEventListener('DOMContentLoaded', function() {
                const dobVerifiedCheckbox = document.querySelector('input[name="dob_verified"]');
                if (dobVerifiedCheckbox) {
                    dobVerifiedCheckbox.addEventListener('change', toggleDobVerifyDocument);
                }
            });






            // Add Phone Number
            function addPhoneNumber() {
                const container = document.getElementById('phoneNumbersContainer');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                                                        <button type="button" class="remove-item-btn" title="Remove Phone"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="contact_type_hidden[${index}]" class="contact-type-selector">
                                    <option value="Personal">Personal</option>
                                    <option value="Work">Work</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="Business">Business</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Cousin">Cousin</option>
                                    <option value="Others">Others</option>
                                    <option value="Partner">Partner</option>
                                    <option value="Not In Use">Not In Use</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Number</label>
                                <div class="cus_field_input" style="display:flex;">
                                    <div class="country_code">
                                        <input class="telephone country-code-input" id="telephone" type="tel" name="country_code[${index}]" style="width: 55px;height: 42px;" readonly >
                                    </div>
                                    <input type="tel" name="phone[${index}]" placeholder="Enter Phone Number" class="phone-number-input" style="width: 230px;" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $(".telephone").intlTelInput();
                validatePersonalPhoneNumbers();
            }

            // Add Email Address
            function addEmailAddress() {
                const container = document.getElementById('emailAddressesContainer');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                                                        <button type="button" class="remove-item-btn" title="Remove Email"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="email_type_hidden[${index}]" class="email-type-selector">
                                    <option value="Personal">Personal</option>
                                    <option value="Work">Work</option>
                                    <option value="Business">Business</option>

                                    <option value="Mobile">Mobile</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Cousin">Cousin</option>
                                    <option value="Others">Others</option>
                                    <option value="Partner">Partner</option>
                                    <option value="Not In Use">Not In Use</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email[${index}]" placeholder="Enter Email Address">
                            </div>
                        </div>
                    </div>
                `);
                validatePersonalEmailTypes();
            }

            // Validate only one Personal phone number
            function validatePersonalPhoneNumbers() {
                const phoneSections = document.querySelectorAll('#phoneNumbersContainer .repeatable-section');
                let personalCount = 0;
                phoneSections.forEach(section => {
                    const type = section.querySelector('.contact-type-selector').value;
                    if (type === 'Personal') {
                        personalCount++;
                    }
                });

                const errorMessage = personalCount > 1 ? 'Only one phone number can be marked as Personal.' : '';
                const existingError = document.querySelector('#phoneNumbersContainer ~ .text-danger');
                if (errorMessage) {
                    if (!existingError) {
                        const errorElement = document.createElement('span');
                        errorElement.className = 'text-danger';
                        errorElement.textContent = errorMessage;
                        document.getElementById('phoneNumbersContainer').insertAdjacentElement('afterend', errorElement);
                    } else {
                        existingError.textContent = errorMessage;
                    }
                } else if (existingError) {
                    existingError.remove();
                }

                return personalCount <= 1;
            }

            // Validate only one Personal email address
            function validatePersonalEmailTypes() {
                const emailSections = document.querySelectorAll('#emailAddressesContainer .repeatable-section');
                let personalCount = 0;
                emailSections.forEach(section => {
                    const type = section.querySelector('.email-type-selector').value;
                    if (type === 'Personal') {
                        personalCount++;
                    }
                });

                const errorMessage = personalCount > 1 ? 'Only one email address can be marked as Personal.' : '';
                const existingError = document.querySelector('#emailAddressesContainer ~ .text-danger');
                if (errorMessage) {
                    if (!existingError) {
                        const errorElement = document.createElement('span');
                        errorElement.className = 'text-danger';
                        errorElement.textContent = errorMessage;
                        document.getElementById('emailAddressesContainer').insertAdjacentElement('afterend', errorElement);
                    } else {
                        existingError.textContent = errorMessage;
                    }
                } else if (existingError) {
                    existingError.remove();
                }

                return personalCount <= 1;
            }

            // Add Passport Detail
            function addPassportDetail() {
                const container = document.getElementById('passportDetailsContainer');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                                                        <button type="button" class="remove-item-btn" title="Remove Passport"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Passport #</label>
                                <input type="text" name="passports[${index}][passport_number]" placeholder="Enter Passport Number">
                            </div>
                            <div class="form-group">
                                <label>Issue Date</label>
                                <input type="text" name="passports[${index}][issue_date]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" name="passports[${index}][expiry_date]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            // Add Visa Detail
            function addVisaDetail() {
                const container = document.getElementById('visaDetailsContainer');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                                                        <button type="button" class="remove-item-btn" title="Remove Visa"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Visa Type / Subclass</label>
                                <select name="visas[${index}][visa_type]" class="visa-type-field">
                                    <option value="">Select Visa Type</option>
                                    @foreach(\App\Models\Matter::select('id','title','nick_name')->where('status',1)->where('title', 'not like', '%skill assessment%')->orderby('title','ASC')->get() as $matterlist)
                                        <option value="{{ $matterlist->id }}">{{ $matterlist->title }} ({{ @$matterlist->nick_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Visa Expiry Date</label>
                                <input type="text" name="visas[${index}][expiry_date]" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                            </div>
                            <div class="form-group">
                                <label>Visa Grant Date</label>
                                <input type="text" name="visas[${index}][grant_date]" placeholder="dd/mm/yyyy" class="visa-grant-field date-picker">
                            </div>
                            <div class="form-group">
                                <label>Visa Description</label>
                                <input type="text" name="visas[${index}][description]" class="visa-description-field">
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            // Add Travel Detail
            async function addTravelDetail() {
                const container = document.getElementById('travelDetailsContainer');
                const index = container.children.length;

                // Fetch countries
                const countries = await fetchCountries();

                // Build the options for the country dropdown
                let countryOptionsHtml = '';
                countries.forEach(country => {
                    countryOptionsHtml += `<option value="${country}">${country}</option>`;
                });

                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                                                        <button type="button" class="remove-item-btn" title="Remove Travel"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Country Visited</label>
                                <select name="travel_country_visited[${index}]">
                                    ${countryOptionsHtml}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Arrival Date</label>
                                <input type="text" name="travel_arrival_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group">
                                <label>Departure Date</label>
                                <input type="text" name="travel_departure_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group">
                                <label>Travel Purpose</label>
                                <input type="text" name="travel_purpose[${index}]" placeholder="Enter Travel Purpose">
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            // Autocomplete for Nominated Occupation
            $(document).on('input', '.nomi_occupation', function() {
                const $input = $(this);
                const query = $input.val();
                const $row = $input.closest('.repeatable-section');
                const $autocomplete = $row.find('.autocomplete-items');
                $autocomplete.empty();

                if (query.length < 2) return;

                $.ajax({
                    url: '{{ route("admin.clients.updateOccupation") }}',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    method: 'POST',
                    data: { occupation: query },
                    success: function(response) {
                        const occupations = response.occupations || []; // Access the 'occupations' array from response
                        if (occupations.length > 0) {
                            occupations.forEach(function(occupation) {
                                var $item = $('<div>').addClass('autocomplete-item').text(occupation.occupation);
                                $item.data('occupation', occupation);
                                $item.appendTo($autocomplete);

                                $item.on('click', function(e) {
                                    e.stopPropagation();
                                    var occupationData = $(this).data('occupation');
                                    $row.find('.nomi_occupation').val(occupationData.occupation); // Map 'occupation' to 'nomi_occupation'
                                    $row.find('.occupation_code').val(occupationData.occupation_code);
                                    $row.find('.list').val(occupationData.list);
                                    $row.find('.visa_subclass').val(occupationData.visa_subclass);
                                    $row.find('.dates').val(''); // Clear dates since not provided by controller
                                    $row.find('.expiry_dates').val(''); // Clear expiry_dates since not provided by controller
                                    $autocomplete.empty();

                                    // Initialize datepicker for Assessment Date without defaulting to current date
                                    $row.find('.dates').daterangepicker({
                                        singleDatePicker: true,
                                        showDropdowns: true,
                                        autoUpdateInput: false, // Prevent auto-filling with current date
                                        locale: {
                                            format: 'DD/MM/YYYY',
                                            applyLabel: 'Apply',
                                            cancelLabel: 'Cancel',
                                            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                                            monthNames: [
                                                'January', 'February', 'March', 'April', 'May', 'June',
                                                'July', 'August', 'September', 'October', 'November', 'December'
                                            ],
                                            firstDay: 1
                                        },
                                        autoApply: true,
                                        minDate: '01/01/1000',
                                        minYear: 1000,
                                        maxYear: parseInt(moment().format('YYYY')) + 50
                                    }).on('apply.daterangepicker', function(ev, picker) {
                                        $(this).val(picker.startDate.format('DD/MM/YYYY'));
                                    }).on('cancel.daterangepicker', function(ev, picker) {
                                        $(this).val(''); // Clear the input when cancel is clicked
                                    });

                                    // Initialize datepicker for Expiry Date without defaulting to current date
                                    $row.find('.expiry_dates').daterangepicker({
                                        singleDatePicker: true,
                                        showDropdowns: true,
                                        autoUpdateInput: false, // Prevent auto-filling with current date
                                        locale: {
                                            format: 'DD/MM/YYYY',
                                            applyLabel: 'Apply',
                                            cancelLabel: 'Cancel',
                                            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                                            monthNames: [
                                                'January', 'February', 'March', 'April', 'May', 'June',
                                                'July', 'August', 'September', 'October', 'November', 'December'
                                            ],
                                            firstDay: 1
                                        },
                                        autoApply: true,
                                        minDate: '01/01/1000',
                                        minYear: 1000,
                                        maxYear: parseInt(moment().format('YYYY')) + 50
                                    }).on('apply.daterangepicker', function(ev, picker) {
                                        $(this).val(picker.startDate.format('DD/MM/YYYY'));
                                    }).on('cancel.daterangepicker', function(ev, picker) {
                                        $(this).val(''); // Clear the input when cancel is clicked
                                    });
                                });
                            });
                        } else {
                            $autocomplete.html('<div class="autocomplete-item autocomplete-no-results">No results found</div>');
                        }
                    },
                    error: function() {
                        alert('Error fetching occupation details.');
                    }
                });
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nomi_occupation').length && !$(e.target).closest('.autocomplete-items').length) {
                    $('.autocomplete-items').empty();
                }
            });

            // Toggle Spouse Details Section
            function toggleSpouseDetailsSection() {
                const maritalStatus = document.getElementById('maritalStatus').value;
                const spouseDetailsSection = document.getElementById('spouseDetailsSection');

                if (maritalStatus === 'Married') {
                    spouseDetailsSection.style.display = 'block';
                    toggleSpouseEnglishFields();
                    toggleSpouseSkillFields();
                } else {
                    spouseDetailsSection.style.display = 'none';
                    document.getElementById('spouseEnglishFields').style.display = 'none';
                    document.getElementById('spouseSkillFields').style.display = 'none';
                }
            }

            function toggleSpouseEnglishFields() {
                const spouseHasEnglishScore = document.getElementById('spouseHasEnglishScore').value;
                const spouseEnglishFields = document.getElementById('spouseEnglishFields');

                if (spouseHasEnglishScore === 'Yes') {
                    spouseEnglishFields.style.display = 'block';
                } else {
                    spouseEnglishFields.style.display = 'none';
                }
                initializeDatepickers();
            }

            function toggleSpouseSkillFields() {
                const spouseHasSkillAssessment = document.getElementById('spouseHasSkillAssessment').value;
                const spouseSkillFields = document.getElementById('spouseSkillFields');

                if (spouseHasSkillAssessment === 'Yes') {
                    spouseSkillFields.style.display = 'block';
                } else {
                    spouseSkillFields.style.display = 'none';
                }
                initializeDatepickers();
            }



            document.addEventListener('DOMContentLoaded', function() {
                toggleSpouseDetailsSection();
            });

            document.getElementById('maritalStatus').addEventListener('change', function() {
                toggleSpouseDetailsSection();
            });

            // Validate Passport Dates on Form Submission
            document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                let hasError = false;

                // Validate DOB Verify Document when DOB Verified is checked
                const dobVerifiedCheckbox = document.querySelector('input[name="dob_verified"]');
                const dobVerifyDocumentInput = document.getElementById('dob_verify_document');
                
                if (dobVerifiedCheckbox.checked && !dobVerifyDocumentInput.value.trim()) {
                    hasError = true;
                    dobVerifyDocumentInput.setCustomValidity('DOB Verify Document is required when DOB is verified');
                } else {
                    dobVerifyDocumentInput.setCustomValidity('');
                }

                const passportSections = document.querySelectorAll('#passportDetailsContainer .repeatable-section');
                passportSections.forEach((section, index) => {
                    const issueDateInput = section.querySelector(`input[name="passports[${index}][issue_date]"]`);
                    const expiryDateInput = section.querySelector(`input[name="passports[${index}][expiry_date]"]`);

                    if (issueDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(issueDateInput.value)) {
                        hasError = true;
                        issueDateInput.setCustomValidity('Passport Issue Date must be in dd/mm/yyyy format');
                    } else {
                        issueDateInput.setCustomValidity('');
                    }

                    if (expiryDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(expiryDateInput.value)) {
                        hasError = true;
                        expiryDateInput.setCustomValidity('Passport Expiry Date must be in dd/mm/yyyy format');
                    } else {
                        expiryDateInput.setCustomValidity('');
                    }
                });

                if (hasError) {
                    e.preventDefault();
                }
            });

            // Validate Travel Dates on Form Submission
            document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                let hasError = false;

                const travelSections = document.querySelectorAll('#travelDetailsContainer .repeatable-section');
                travelSections.forEach((section, index) => {
                    const arrivalDateInput = section.querySelector(`input[name="travel_arrival_date[${index}]"]`);
                    const departureDateInput = section.querySelector(`input[name="travel_departure_date[${index}]"]`);

                    if (arrivalDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(arrivalDateInput.value)) {
                        hasError = true;
                        arrivalDateInput.setCustomValidity('Travel Arrival Date must be in dd/mm/yyyy format');
                    } else {
                        arrivalDateInput.setCustomValidity('');
                    }

                    if (departureDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(departureDateInput.value)) {
                        hasError = true;
                        departureDateInput.setCustomValidity('Travel Departure Date must be in dd/mm/yyyy format');
                    } else {
                        departureDateInput.setCustomValidity('');
                    }
                });

                if (hasError) {
                    e.preventDefault();
                }
            });

            // Load Google Maps Places API
            function initGoogleMaps() {
                const inputs = document.querySelectorAll('.address-input');
                inputs.forEach(input => {
                    const autocomplete = new google.maps.places.Autocomplete(input, {
                        types: ['address'],
                        fields: ['formatted_address', 'address_components']
                    });

                    autocomplete.addListener('place_changed', () => {
                        const place = autocomplete.getPlace();
                        if (!place.formatted_address) {
                            return;
                        }

                        input.value = place.formatted_address;

                        const row = input.closest('.repeatable-section');
                        const postcodeInput = row.querySelector('.postcode-input');
                        const regionalCodeInput = row.querySelector('.regional-code-input');

                        let postcode = '';
                        let region = '';

                        place.address_components.forEach(component => {
                            if (component.types.includes('postal_code')) {
                                postcode = component.long_name;
                            }
                            if (component.types.includes('administrative_area_level_1')) {
                                region = component.short_name;
                            }
                        });

                        if (postcode) {
                            postcodeInput.value = postcode;
                        }
                        if (region) {
                            regionalCodeInput.value = region;
                        }

                        const autocompleteItems = input.nextElementSibling;
                        if (autocompleteItems) {
                            autocompleteItems.innerHTML = '';
                        }
                    });
                });
            }

            // Add Address
            function addAddress() {
                const isCurrentAddress = document.getElementById('isCurrentAddress');
                if (!isCurrentAddress) {
                    console.error('Checkbox with ID "isCurrentAddress" not found.');
                    return;
                }

                if (isCurrentAddress.checked) {
                    alert('Please uncheck "Is this your current address?" to add a new address.');
                    return;
                }

                const container = document.getElementById('addressesContainer');
                const index = container.children.length;

                container.insertAdjacentHTML('afterbegin', `
                    <div class="repeatable-section">
                                                        <button type="button" class="remove-item-btn" title="Remove Address"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Full Address</label>
                                <textarea name="address[${index}]" rows="1" class="address-input"></textarea>
                                <div class="autocomplete-items"></div>
                            </div>
                            <div class="form-group">
                                <label>Post Code</label>
                                <input type="text" name="zip[${index}]" class="postcode-input">
                            </div>
                        </div>
                        <div class="content-grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Regional Code Info</label>
                                <input type="text" name="regional_code[${index}]" class="regional-code-input" readonly>
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" name="address_start_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker address-start-date">
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" name="address_end_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker address-end-date">
                            </div>
                        </div>
                    </div>
                `);

                initializeDatepickers();
                initGoogleMaps();
                updateCurrentAddressCheckbox();
            }

            function updateCurrentAddressCheckbox() {
                const container = document.getElementById('addressesContainer');
                const checkbox = document.getElementById('isCurrentAddress');
                checkbox.disabled = container.children.length === 0;
                if (container.children.length === 0) {
                    checkbox.checked = false;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateCurrentAddressCheckbox();
                initGoogleMaps();

                // Handle removal of addresses
                document.getElementById('addressesContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this address?');
                        if (confirmDelete) {
                            section.remove();
                            updateCurrentAddressCheckbox();
                        }
                    }
                });

                // Handle removal of phone numbers
                document.getElementById('phoneNumbersContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this phone number?');
                        if (confirmDelete) {
                            section.remove();
                            validatePersonalPhoneNumbers();
                        }
                    }
                });

                // Handle removal of email addresses
                document.getElementById('emailAddressesContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this email address?');
                        if (confirmDelete) {
                            section.remove();
                            validatePersonalEmailTypes();
                        }
                    }
                });

                // Handle removal of passport details
                document.getElementById('passportDetailsContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this passport detail?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                // Handle removal of visa details
                document.getElementById('visaDetailsContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this visa detail?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                // Handle removal of travel details
                document.getElementById('travelDetailsContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this travel detail?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                // Handle removal of qualifications
                document.getElementById('qualificationsContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this qualification?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                // Handle removal of work experience
                document.getElementById('experienceContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this work experience?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                // Handle removal of occupations
                document.getElementById('occupationContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this occupation?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                // Handle removal of test scores
                document.getElementById('testScoresContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-item-btn')) {
                        const section = e.target.closest('.repeatable-section');
                        const confirmDelete = confirm('Are you sure you want to delete this test score?');
                        if (confirmDelete) {
                            section.remove();
                        }
                    }
                });

                document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                    const addressSections = document.querySelectorAll('#addressesContainer .repeatable-section');
                    let hasError = false;

                    addressSections.forEach((section, index) => {
                        const startDateInput = section.querySelector('.address-start-date');
                        const endDateInput = section.querySelector('.address-end-date');

                        if (startDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(startDateInput.value)) {
                            hasError = true;
                            startDateInput.setCustomValidity('Start Date must be in dd/mm/yyyy format');
                        } else {
                            startDateInput.setCustomValidity('');
                        }

                        if (endDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(endDateInput.value)) {
                            hasError = true;
                            endDateInput.setCustomValidity('End Date must be in dd/mm/yyyy format');
                        } else {
                            endDateInput.setCustomValidity('');
                        }
                    });

                    if (hasError) {
                        e.preventDefault();
                    }
                });
            });

            // Cache countries
            let countriesCache = null;

            async function fetchCountries() {
                if (countriesCache) {
                    return countriesCache;
                }

                try {
                    const response = await $.ajax({
                        url: '{{ route("admin.getCountries") }}',
                        method: 'GET',
                        dataType: 'json',
                    });
                    countriesCache = response;
                    return response;
                } catch (error) {
                    console.error('Error fetching countries:', error);
                    return ['India', 'Australia'];
                }
            }

            // Add Qualification
            async function addQualification() {
                const container = document.getElementById('qualificationsContainer');
                const index = container.children.length;

                const countries = await fetchCountries();
                let countryOptionsHtml = '';
                countries.forEach(country => {
                    countryOptionsHtml += `<option value="${country}">${country}</option>`;
                });

                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                        <button type="button" class="remove-item-btn" title="Remove Qualification"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                            <div class="form-group">
                                <label>Level</label>
                                <select name="level_hidden[${index}]">
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
                                    <option value="12">12</option>
                                    <option value="10">10</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name[${index}]" placeholder="e.g., B. Engineering">
                            </div>
                            <div class="form-group">
                                <label>College Name</label>
                                <input type="text" name="qual_college_name[${index}]" placeholder="Enter college name">
                            </div>
                            <div class="form-group">
                                <label>Campus</label>
                                <input type="text" name="qual_campus[${index}]" placeholder="Enter campus">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <select name="country_hidden[${index}]">
                                    ${countryOptionsHtml}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="qual_state[${index}]" rows="2" placeholder="Enter address"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" name="start_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group">
                                <label>Finish Date</label>
                                <input type="text" name="finish_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group" style="align-items: center;">
                                <label>Relevant?</label>
                                <label class="switch">
                                    <input type="checkbox" name="relevant_qualification_hidden[${index}]" value="1">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            // Add Experience
            async function addExperience() {
                const container = document.getElementById('experienceContainer');
                const index = container.children.length;

                const countries = await fetchCountries();
                let countryOptionsHtml = '';
                countries.forEach(country => {
                    countryOptionsHtml += `<option value="${country}">${country}</option>`;
                });

                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                        <button type="button" class="remove-item-btn" title="Remove Experience"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                            <div class="form-group">
                                <label>Job Title</label>
                                <input type="text" name="job_title[${index}]" placeholder="e.g., Software Engineer">
                            </div>
                            <div class="form-group">
                                <label>ANZSCO Code</label>
                                <input type="text" name="job_code[${index}]" placeholder="e.g., 261313">
                            </div>
                             <div class="form-group">
                                <label>Employer Name</label>
                                <input type="text" name="job_emp_name[${index}]" placeholder="Enter employer name">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <select name="job_country_hidden[${index}]">
                                    ${countryOptionsHtml}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="job_state[${index}]" rows="2" placeholder="Enter address"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Job Type</label>
                                <select name="job_type[${index}]">
                                    <option value="">Select job type</option>
                                    <option value="Part Time">Part Time</option>
                                    <option value="Full Time">Full Time</option>
                                    <option value="Casual">Casual</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" name="job_start_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group">
                                <label>Finish Date</label>
                                <input type="text" name="job_finish_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group" style="align-items: center;">
                                <label>Relevant?</label>
                                <label class="switch">
                                    <input type="checkbox" name="relevant_experience_hidden[${index}]" value="1">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            // Add Occupation
            function addOccupation() {
                const container = document.getElementById('occupationContainer');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                        <button type="button" class="remove-item-btn" title="Remove Occupation"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Nominated Occupation</label>
                                <input type="text" name="nomi_occupation[${index}]" class="nomi_occupation" placeholder="Enter Occupation">
                                <div class="autocomplete-items"></div>
                            </div>
                            <div class="form-group">
                                <label>Assessing Authority</label>
                                <input type="text" name="list[${index}]" class="list" placeholder="e.g., ACS, VETASSESS">
                            </div>
                            <div class="form-group">
                                <label>Occupation Code (ANZSCO)</label>
                                <input type="text" name="occupation_code[${index}]" class="occupation_code" placeholder="Enter Code">
                            </div>
                            <div class="form-group">
                                <label for="occ_reference_no">Reference No</label>
                                <input type="text" name="occ_reference_no[${index}]" placeholder="Enter Reference No.">
                            </div>
                            <div class="form-group">
                                <label for="dates">Assessment Date</label>
                                <input type="text" name="dates[${index}]" class="dates date-picker" placeholder="dd/mm/yyyy">
                            </div>

                            <div class="form-group">
                                <label for="expiry_dates">Expiry Date</label>
                                <input type="text" name="expiry_dates[${index}]" class="expiry_dates date-picker" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            // Add Test Score
            function addTestScore() {
                const container = document.getElementById('testScoresContainer');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                        <button type="button" class="remove-item-btn" title="Remove Test"><i class="fas fa-times-circle"></i></button>
                        <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px;">
                            <div class="form-group">
                                <label>Test Type</label>
                                <select name="test_type_hidden[${index}]" class="test-type-selector" onchange="updateTestScoreValidation(this, ${index})">
                                    <option value="">Select Test</option>
                                    <option value="IELTS">IELTS General</option>
                                    <option value="IELTS_A">IELTS Academic</option>
                                    <option value="PTE">PTE Academic</option>
                                    <option value="TOEFL">TOEFL iBT</option>
                                    <option value="CAE">CAE</option>
                                    <option value="OET">OET</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Listening</label>
                                <input type="text" name="listening[${index}]" placeholder="Score" class="test-score-input" data-index="${index}">
                            </div>
                            <div class="form-group">
                                <label>Reading</label>
                                <input type="text" name="reading[${index}]" placeholder="Score" class="test-score-input" data-index="${index}">
                            </div>
                            <div class="form-group">
                                <label>Writing</label>
                                <input type="text" name="writing[${index}]" placeholder="Score" class="test-score-input" data-index="${index}">
                            </div>
                            <div class="form-group">
                                <label>Speaking</label>
                                <input type="text" name="speaking[${index}]" placeholder="Score" class="test-score-input" data-index="${index}">
                            </div>
                            <div class="form-group">
                                <label>Overall</label>
                                <input type="text" name="overall_score[${index}]" placeholder="Score" class="test-score-input" data-index="${index}">
                            </div>
                            <div class="form-group">
                                <label>Date of Test</label>
                                <input type="text" name="test_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker test-date">
                            </div>

                            <div class="form-group">
                                <label>Reference No</label>
                                <input type="text" name="test_reference_no[${index}]" placeholder="Reference no">
                            </div>
                            <div class="form-group" style="align-items: center;">
                                <label>Relevant?</label>
                                <label class="switch">
                                    <input type="checkbox" name="relevant_test_hidden[${index}]" value="1">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            function updateTestScoreValidation(selectElement, index) {
                const testType = selectElement.value;
                const row = selectElement.closest('.repeatable-section');
                const inputs = row.querySelectorAll('.test-score-input[data-index="' + index + '"]');

                inputs.forEach(input => {
                    input.removeAttribute('pattern');
                    input.removeAttribute('step');
                    input.removeAttribute('min');
                    input.removeAttribute('max');

                    if (testType === 'IELTS' || testType === 'IELTS_A') {
                        input.setAttribute('type', 'number');
                        input.setAttribute('step', '0.5');
                        input.setAttribute('min', '1');
                        input.setAttribute('max', '9');
                    } else if (testType === 'TOEFL') {
                        input.setAttribute('type', 'number');
                        input.setAttribute('min', '0');
                        input.setAttribute('max', '30');
                    } else if (testType === 'PTE') {
                        input.setAttribute('type', 'number');
                        input.setAttribute('min', '0');
                        input.setAttribute('max', '90');
                    } else if (testType === 'OET') {
                        input.setAttribute('type', 'text');
                        input.setAttribute('pattern', '^(A|B|C|C\\+\\+|D)$');
                        input.setAttribute('title', 'Only A, B, C, C++, or D are allowed');
                    } else {
                        input.setAttribute('type', 'text');
                    }
                });
            }

            document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                const testSections = document.querySelectorAll('#testScoresContainer .repeatable-section');
                let hasError = false;

                testSections.forEach((section, index) => {
                    const testType = section.querySelector('.test-type-selector').value;
                    const inputs = section.querySelectorAll('.test-score-input');
                    const dateInput = section.querySelector('.test-date');

                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }

                    inputs.forEach(input => {
                        const value = input.value;
                        if (value) {
                            if (testType === 'IELTS' || testType === 'IELTS_A') {
                                const num = parseFloat(value);
                                if (isNaN(num) || num < 1 || num > 9 || (num * 2) % 1 !== 0) {
                                    hasError = true;
                                    input.setCustomValidity('IELTS scores must be between 1 and 9 in steps of 0.5');
                                } else {
                                    input.setCustomValidity('');
                                }
                            } else if (testType === 'TOEFL') {
                                const num = parseInt(value);
                                if (isNaN(num) || num < 0 || num > 30) {
                                    hasError = true;
                                    input.setCustomValidity('TOEFL scores must be between 0 and 30');
                                } else {
                                    input.setCustomValidity('');
                                }
                            } else if (testType === 'PTE') {
                                const num = parseInt(value);
                                if (isNaN(num) || num < 0 || num > 90) {
                                    hasError = true;
                                    input.setCustomValidity('PTE scores must be between 0 and 90');
                                } else {
                                    input.setCustomValidity('');
                                }
                            } else if (testType === 'OET') {
                                if (!/^(A|B|C|C\+\+|D)$/.test(value)) {
                                    hasError = true;
                                    input.setCustomValidity('OET scores must be A, B, C, C++, or D');
                                } else {
                                    input.setCustomValidity('');
                                }
                            } else {
                                input.setCustomValidity('');
                            }
                        } else {
                            input.setCustomValidity('');
                        }
                    });
                });

                if (hasError) {
                    e.preventDefault();
                }
            });

            document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                const naatiDateInput = document.getElementById('naatiDate');
                const pyDateInput = document.getElementById('pyDate');
                let hasError = false;

                if (naatiDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(naatiDateInput.value)) {
                    hasError = true;
                    naatiDateInput.setCustomValidity('NAATI CCL Test date must be in dd/mm/yyyy format');
                } else {
                    naatiDateInput.setCustomValidity('');
                }

                if (pyDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(pyDateInput.value)) {
                    hasError = true;
                    pyDateInput.setCustomValidity('Professional Year (PY) date must be in dd/mm/yyyy format');
                } else {
                    pyDateInput.setCustomValidity('');
                }

                if (hasError) {
                    e.preventDefault();
                }
            });

            document.getElementById('naatiGiven').addEventListener('change', function() {
                document.getElementById('naatiDate').style.display = this.checked ? 'block' : 'none';
                document.getElementById('nati_language').style.display = this.checked ? 'block' : 'none';
            });
            document.getElementById('pyGiven').addEventListener('change', function() {
                document.getElementById('pyDate').style.display = this.checked ? 'block' : 'none';
                 document.getElementById('py_field').style.display = this.checked ? 'block' : 'none';
            });

            document.getElementById('naatiDate').style.display = document.getElementById('naatiGiven').checked ? 'block' : 'none';
            document.getElementById('pyDate').style.display = document.getElementById('pyGiven').checked ? 'block' : 'none';

            document.addEventListener('DOMContentLoaded', function() {
                const phoneNumbersContainer = document.getElementById('phoneNumbersContainer');
                phoneNumbersContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('contact-type-selector') || e.target.classList.contains('phone-number-input') || e.target.classList.contains('country-code-input')) {
                        validatePersonalPhoneNumbers();
                    }
                });

                phoneNumbersContainer.addEventListener('input', function(e) {
                    if (e.target.classList.contains('phone-number-input') || e.target.classList.contains('country-code-input')) {
                        validatePersonalPhoneNumbers();
                    }
                });

                validatePersonalPhoneNumbers();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const emailAddressesContainer = document.getElementById('emailAddressesContainer');
                emailAddressesContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('email-type-selector')) {
                        validatePersonalEmailTypes();
                    }
                });

                document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                    if (!validatePersonalEmailTypes()) {
                        e.preventDefault();
                        alert('Only one email address can be of type Personal. Please correct the entries.');
                    }
                });

                validatePersonalEmailTypes();
            });

            // Add Character Row
            function addCharacterRow(containerId, fieldName) {
                const container = document.getElementById(containerId);
                const index = container.children.length;

                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                        <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Details</label>
                                <textarea name="${fieldName}[${index}][details]" rows="1"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="text" name="${fieldName}[${index}][date]" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, '${fieldName}')" title="Remove ${fieldName.replace('_', ' ').replace(/(^\w|\s\w)/g, m => m.toUpperCase())}"><i class="fas fa-times-circle"></i></button>
                        </div>
                    </div>
                `);
                initializeDatepickers();
            }

            function removeCharacterRow(button, fieldName) {
                const section = button.closest('.repeatable-section');
                const confirmDelete = confirm('Are you sure you want to delete this record?');
                if (confirmDelete) {
                    section.remove();
                }
            }

            document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                let hasError = false;

                const criminalSections = document.querySelectorAll('#criminalChargesContainer .repeatable-section');
                criminalSections.forEach((section, index) => {
                    const dateInput = section.querySelector(`input[name="criminal_charges[${index}][date]"]`);
                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Criminal Charges Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });

                const militarySections = document.querySelectorAll('#militaryServiceContainer .repeatable-section');
                militarySections.forEach((section, index) => {
                    const dateInput = section.querySelector(`input[name="military_service[${index}][date]"]`);
                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Military Service Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });

                const intelligenceSections = document.querySelectorAll('#intelligenceWorkContainer .repeatable-section');
                intelligenceSections.forEach((section, index) => {
                    const dateInput = section.querySelector(`input[name="intelligence_work[${index}][date]"]`);
                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Intelligence Work Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });

                const visaRefusalSections = document.querySelectorAll('#visaRefusalsContainer .repeatable-section');
                visaRefusalSections.forEach((section, index) => {
                    const dateInput = section.querySelector(`input[name="visa_refusals[${index}][date]"]`);
                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Visa Refusals Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });

                const deportationSections = document.querySelectorAll('#deportationsContainer .repeatable-section');
                deportationSections.forEach((section, index) => {
                    const dateInput = section.querySelector(`input[name="deportations[${index}][date]"]`);
                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Deportations Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });

                const citizenshipRefusalSections = document.querySelectorAll('#citizenshipRefusalsContainer .repeatable-section');
                citizenshipRefusalSections.forEach((section, index) => {
                    const dateInput = section.querySelector(`input[name="citizenship_refusals[${index}][date]"]`);
                    if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                        hasError = true;
                        dateInput.setCustomValidity('Citizenship Refusals Date must be in dd/mm/yyyy format');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });

                if (hasError) {
                    e.preventDefault();
                }
            });


            // Modified addPartnerRow to handle different types with hidden extra fields by default
            function addPartnerRow(type) {
                const containerId = type + 'Container';
                const container = document.getElementById(containerId);
                const index = container.children.length;

                let relationshipOptions = '';
                switch (type) {
                    case 'children':
                        relationshipOptions = `
                            <option value="">Select Relationship</option>
                            <option value="Son">Son</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Step Son">Step Son</option>
                            <option value="Step Daughter">Step Daughter</option>
                        `;
                        break;
                    case 'parent':
                        relationshipOptions = `
                            <option value="">Select Relationship</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Step Father">Step Father</option>
                            <option value="Step Mother">Step Mother</option>
                        `;
                        break;
                    case 'siblings':
                        relationshipOptions = `
                            <option value="">Select Relationship</option>
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Step Brother">Step Brother</option>
                            <option value="Step Sister">Step Sister</option>
                        `;
                        break;
                    case 'others':
                        relationshipOptions = `
                            <option value="">Select Relationship</option>
                            <option value="Cousin">Cousin</option>
                            <option value="Friend">Friend</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Aunt">Aunt</option>
                            <option value="Grandchild">Grandchild</option>
                            <option value="Granddaughter">Granddaughter</option>
                            <option value="Grandparent">Grandparent</option>
                            <option value="Niece">Niece</option>
                            <option value="Nephew">Nephew</option>
                            <option value="Grandfather">Grandfather</option>
                        `;
                        break;
                    default: // Partner
                        relationshipOptions = `
                            <option value="">Select Relationship</option>
                            <option value="Husband">Husband</option>
                            <option value="Wife">Wife</option>
                            <option value="Ex-Wife">Ex-Wife</option>
                            <option value="Defacto">Defacto</option>
                        `;
                }

                container.insertAdjacentHTML('beforeend', `
                    <div class="repeatable-section">
                        <button type="button" class="remove-item-btn" title="Remove ${type.charAt(0).toUpperCase() + type.slice(1)}" onclick="removePartnerRow(this, '${type}')"><i class="fas fa-times-circle"></i></button>
                        <input type="hidden" name="${type}_id[${index}]" class="partner-id">
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Details</label>
                                <input type="text" name="${type}_details[${index}]" class="partner-details" placeholder="Search by Name, Email, Client ID, or Phone">
                                <div class="autocomplete-items"></div>
                            </div>
                            <div class="form-group">
                                <label>Relationship Type</label>
                                <select name="${type}_relationship_type[${index}]" required>
                                    ${relationshipOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select name="${type}_gender[${index}]" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Company Type</label>
                                <select name="${type}_company_type[${index}]">
                                    <option value="">Select Company Type</option>
                                    <option value="Accompany Member">Accompany Member</option>
                                    <option value="Non-Accompany Member">Non-Accompany Member</option>
                                </select>
                            </div>
                        </div>
                        <div class="partner-extra-fields" style="display: none;">
                            <div class="content-grid single-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="${type}_email[${index}]" placeholder="Enter Email">
                                </div>
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="${type}_first_name[${index}]" placeholder="Enter First Name">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="${type}_last_name[${index}]" placeholder="Enter Last Name">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="${type}_phone[${index}]" placeholder="Enter Phone">
                                </div>
                                <div class="form-group">
                                    <label>DOB</label>
                                    <input type="text" name="${type}_dob[${index}]"  placeholder="dd/mm/yyyy" class="date-picker">
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                // Reinitialize datepickers for the newly added fields
                initializeDatepickers();
            }

            function removePartnerRow(button, type, relationshipId = null) {
                const section = button.closest('.repeatable-section');
                const confirmDelete = confirm(`Are you sure you want to delete this ${type} record?`);

                if (confirmDelete) {
                    if (relationshipId) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `delete_${type}_ids[]`;
                        hiddenInput.value = relationshipId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }

            $(document).on('input', '.partner-details', function() {
                const $input = $(this);
                const query = $input.val().trim();
                const $row = $input.closest('.repeatable-section');
                const $autocomplete = $row.find('.autocomplete-items');
                const $extraFields = $row.find('.partner-extra-fields');

                const type = $row.closest('[id$="Container"]').attr('id').replace('Container', '');
                const $partnerIdInput = $row.find('.partner-id');

                // Clear autocomplete if query is too short
                if (query.length < 3) {
                    $autocomplete.empty();
                    // Do not show extra fields until a search is performed and no results are found
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.clients.searchPartner") }}',
                    method: 'POST',
                    data: {
                        query: query,
                        _token: '{{ csrf_token() }}' // Include CSRF token for POST request
                    },
                    success: function(response) {
                        $autocomplete.empty();
                        // Check if response contains partners array
                        if (response.partners && Array.isArray(response.partners) && response.partners.length > 0) {
                            response.partners.forEach(function(client) {
                                const displayText = `${client.first_name} ${client.last_name || ''} (${client.email}, ${client.phone}, ${client.client_id})`;
                                const $item = $('<div class="autocomplete-item"></div>')
                                    .text(displayText)
                                    .data('client', client)
                                    .appendTo($autocomplete);

                                $item.on('click', function() {
                                    const clientData = $(this).data('client');
                                    $input.val(displayText);
                                    $input.attr('readonly', true);
                                    //$row.find('input[name*="_id"]').val(clientData.id);
                                    $partnerIdInput.val(clientData.id); // Set the partner ID
                                    $extraFields.hide();
                                    $autocomplete.empty();
                                });
                            });
                        } else {
                            $autocomplete.html('<div class="autocomplete-item autocomplete-no-results">No results found</div>');
                            // Show confirmation prompt before displaying extra fields
                            setTimeout(() => {
                                const addNewUser = confirm('No matching record found. Do you want to save details of your client then Proceed?');
                                if (addNewUser) {
                                    $input.attr('readonly', true);
                                    $partnerIdInput.val(''); // Clear the partner ID
                                    $extraFields.show();
                                } else {
                                    $extraFields.hide();
                                }
                                $autocomplete.empty();
                            }, 300); // Small delay to ensure the "No results found" message is visible briefly
                        }
                    },
                    error: function(xhr) {
                        $autocomplete.empty();
                        let errorMsg = 'Error fetching client details.';
                        if (xhr.status === 422) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            if (errors && errors.query) {
                                errorMsg = errors.query[0];
                            }
                        }
                        $autocomplete.html(`<div class="autocomplete-item autocomplete-error">${errorMsg}</div>`);
                    }
                });
            });


            document.getElementById('createLeadForm').addEventListener('submit', function(e) {
                let hasError = false;
                const types = ['partner', 'children', 'parent', 'siblings', 'others'];

                types.forEach(type => {
                    const sections = document.querySelectorAll(`#${type}Container .repeatable-section`);
                    sections.forEach((section, index) => {
                        const details = section.querySelector('.partner-details').value.trim();
                        const relationshipType = section.querySelector(`select[name^="${type}_relationship_type"]`).value;
                        const extraFieldsVisible = section.querySelector('.partner-extra-fields').style.display !== 'none';

                        if (extraFieldsVisible) {
                            //const email = section.querySelector(`input[name^="${type}_email"]`).value.trim();
                            const firstName = section.querySelector(`input[name^="${type}_first_name"]`).value.trim();
                            const lastName = section.querySelector(`input[name^="${type}_last_name"]`).value.trim();
                            //const phone = section.querySelector(`input[name^="${type}_phone"]`).value.trim();

                            if (!firstName || !lastName) {
                                hasError = true;
                                alert(`Please fill in all ${type} details (First Name, Last Name) when no match is found.`);
                            }
                        } else if (!details && !relationshipType) {
                            return;
                        } else if (!relationshipType) {
                            hasError = true;
                            alert(`Please select a Relationship Type for all ${type}s.`);
                        }
                    });
                });

                if (hasError) {
                    e.preventDefault();
                }
            });

            // Ensure the correct tab is active after a validation error
            @if ($errors->has('email') || $errors->has('email.*') || $errors->has('phone') || $errors->has('phone.*') || $errors->has('town_city') || $errors->has('state_region') || $errors->has('country') || $errors->has('dob_verified') || $errors->has('email_verified') || $errors->has('phone_verified'))
                document.getElementById('personalTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'personalTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'personalTab')") {
                        btn.classList.remove('active');
                    }
                });
            @endif

            @if ($errors->has('visa_country') || $errors->has('passports') || $errors->has('passports.*') || $errors->has('visas') || $errors->has('visas.*') || $errors->has('visa_expiry_verified'))
                document.getElementById('visaPassportCitizenshipTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'visaPassportCitizenshipTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'visaPassportCitizenshipTab')") {
                        btn.classList.remove('active');
                    }
                });
            @endif

            @if ($errors->has('address') || $errors->has('address.*') || $errors->has('zip') || $errors->has('zip.*') || $errors->has('regional_code') || $errors->has('regional_code.*') || $errors->has('address_start_date') || $errors->has('address_start_date.*') || $errors->has('address_end_date') || $errors->has('address_end_date.*') || $errors->has('travel_country_visited') || $errors->has('travel_country_visited.*') || $errors->has('travel_arrival_date') || $errors->has('travel_arrival_date.*') || $errors->has('travel_departure_date') || $errors->has('travel_departure_date.*') || $errors->has('travel_purpose') || $errors->has('travel_purpose.*'))
                document.getElementById('addressTravelTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'addressTravelTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'addressTravelTab')") {
                        btn.classList.remove('active');
                    }
                });
            @endif

            @if ($errors->has('test_type_hidden') || $errors->has('test_type_hidden.*') || $errors->has('listening') || $errors->has('listening.*') || $errors->has('reading') || $errors->has('reading.*') || $errors->has('writing') || $errors->has('writing.*') || $errors->has('speaking') || $errors->has('speaking.*') || $errors->has('overall_score') || $errors->has('overall_score.*') || $errors->has('test_date') || $errors->has('test_date.*') || $errors->has('naati_test') || $errors->has('naati_date') || $errors->has('py_test') || $errors->has('py_date') || $errors->has('spouse_has_english_score') || $errors->has('spouse_has_skill_assessment') || $errors->has('spouse_test_type') || $errors->has('spouse_listening_score') || $errors->has('spouse_reading_score') || $errors->has('spouse_writing_score') || $errors->has('spouse_speaking_score') || $errors->has('spouse_overall_score') || $errors->has('spouse_test_date') || $errors->has('spouse_skill_assessment_status') || $errors->has('spouse_nomi_occupation') || $errors->has('spouse_assessment_date') || $errors->has('level_hidden') || $errors->has('level_hidden.*') || $errors->has('name') || $errors->has('name.*') || $errors->has('country_hidden') || $errors->has('country_hidden.*') || $errors->has('start_date') || $errors->has('start_date.*') || $errors->has('finish_date') || $errors->has('finish_date.*') || $errors->has('relevant_qualification_hidden') || $errors->has('relevant_qualification_hidden.*') || $errors->has('job_title') || $errors->has('job_title.*') || $errors->has('job_code') || $errors->has('job_code.*') || $errors->has('job_country_hidden') || $errors->has('job_country_hidden.*') || $errors->has('job_start_date') || $errors->has('job_start_date.*') || $errors->has('job_finish_date') || $errors->has('job_finish_date.*') || $errors->has('relevant_experience_hidden') || $errors->has('relevant_experience_hidden.*') || $errors->has('nomi_occupation') || $errors->has('nomi_occupation.*') || $errors->has('occupation_code') || $errors->has('occupation_code.*') || $errors->has('list') || $errors->has('list.*') || $errors->has('visa_subclass') || $errors->has('visa_subclass.*') || $errors->has('dates') || $errors->has('dates.*') || $errors->has('expiry_dates') || $errors->has('expiry_dates.*') || $errors->has('relevant_occupation_hidden') || $errors->has('relevant_occupation_hidden.*'))
                document.getElementById('skillsEducationTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'skillsEducationTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'skillsEducationTab')") {
                        btn.classList.remove('active');
                    }
                });
            @endif

            @if ($errors->has('criminal_charges') || $errors->has('criminal_charges.*') ||
                $errors->has('military_service') || $errors->has('military_service.*') ||
                $errors->has('intelligence_work') || $errors->has('intelligence_work.*') ||
                $errors->has('visa_refusals') || $errors->has('visa_refusals.*') ||
                $errors->has('deportations') || $errors->has('deportations.*') ||
                $errors->has('citizenship_refusals') || $errors->has('citizenship_refusals.*'))
                document.getElementById('otherInformationTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'otherInformationTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'otherInformationTab')") {
                        btn.classList.remove('active');
                    }
                });
            @endif

            @if ($errors->has('children_details') || $errors->has('children_details.*') || $errors->has('children_relationship_type') || $errors->has('children_relationship_type.*') || $errors->has('children_email') || $errors->has('children_email.*') || $errors->has('children_first_name') || $errors->has('children_first_name.*') || $errors->has('children_last_name') || $errors->has('children_last_name.*') || $errors->has('children_phone') || $errors->has('children_phone.*') ||
                $errors->has('parent_details') || $errors->has('parent_details.*') || $errors->has('parent_relationship_type') || $errors->has('parent_relationship_type.*') || $errors->has('parent_email') || $errors->has('parent_email.*') || $errors->has('parent_first_name') || $errors->has('parent_first_name.*') || $errors->has('parent_last_name') || $errors->has('parent_last_name.*') || $errors->has('parent_phone') || $errors->has('parent_phone.*') ||
                $errors->has('siblings_details') || $errors->has('siblings_details.*') || $errors->has('siblings_relationship_type') || $errors->has('siblings_relationship_type.*') || $errors->has('siblings_email') || $errors->has('siblings_email.*') || $errors->has('siblings_first_name') || $errors->has('siblings_first_name.*') || $errors->has('siblings_last_name') || $errors->has('siblings_last_name.*') || $errors->has('siblings_phone') || $errors->has('siblings_phone.*') ||
                $errors->has('others_details') || $errors->has('others_details.*') || $errors->has('others_relationship_type') || $errors->has('others_relationship_type.*') || $errors->has('others_email') || $errors->has('others_email.*') || $errors->has('others_first_name') || $errors->has('others_first_name.*') || $errors->has('others_last_name') || $errors->has('others_last_name.*') || $errors->has('others_phone') || $errors->has('others_phone.*'))
                document.getElementById('familyTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'familyTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'familyTab')") {
                        btn.classList.remove('active');
                    }
                });
            @endif

            // Ensure the correct tab is active after a validation error
            @if ($errors->has('visa_country') || $errors->has('passports') || $errors->has('passports.*') || $errors->has('visas') || $errors->has('visas.*') || $errors->has('visa_expiry_verified'))
                document.getElementById('visaPassportCitizenshipTab').style.display = 'block';
                document.querySelector('button[onclick="openTab(event, \'visaPassportCitizenshipTab\')]').classList.add('active');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    if (btn.getAttribute('onclick') !== "openTab(event, 'visaPassportCitizenshipTab')") {
                        btn.classList.remove('active');
                    }
                });

                // Check initial state of Country of Passport on page load after validation error
                document.addEventListener('DOMContentLoaded', function() {
                    const passportCountry = document.getElementById('passportCountry');
                    const visaDetailsSection = document.getElementById('visaDetailsSection');
                    if (passportCountry.value === 'Australia') {
                        visaDetailsSection.style.display = 'none';
                    } else {
                        visaDetailsSection.style.display = 'block';
                    }
                });
            @else
                // Set initial state on page load if no validation errors
                document.addEventListener('DOMContentLoaded', function() {
                    const passportCountry = document.getElementById('passportCountry');
                    const visaDetailsSection = document.getElementById('visaDetailsSection');
                    if (passportCountry.value === 'Australia') {
                        visaDetailsSection.style.display = 'none';
                    } else {
                        visaDetailsSection.style.display = 'block';
                    }
                });
            @endif
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATpl3gyx8FSoykbCx3otznCIWP_-8hk7c&libraries=places&callback=initGoogleMaps" async defer></script>
    @endpush
@endsection
