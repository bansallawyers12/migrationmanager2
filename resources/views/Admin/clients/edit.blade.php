@extends('layouts.admin_client_detail_dashboard')

@section('content')
    <div class="crm-container">
        <div class="main-content">
            <div class="client-header" style="padding-top: 35px;">
                <div>
                    <h1>{{ $fetchedData->type == 'lead' ? 'Edit Lead' : ($fetchedData->type == 'client' ? 'Edit Client' : '') }}
                        : {{ $fetchedData->first_name }} {{ $fetchedData->last_name }}</h1>
                    <div class="client-id">
                        {{ $fetchedData->type == 'lead' ? 'Lead ID' : ($fetchedData->type == 'client' ? 'Client ID' : '') }}
                        : {{ $fetchedData->client_id }}</div>
                </div>
                <div class="client-status">
                    <button class="btn btn-secondary" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Back</button>
                    <button class="btn btn-primary" type="submit" form="editClientForm" onclick="return validateForm()"><i class="fas fa-save"></i> Save Changes</button>
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
                <button class="tab-button" onclick="openTab(event, 'eoiReferenceTab')"><i class="fas fa-file-alt"></i> EOI Reference</button>
                <button class="tab-button" onclick="openTab(event, 'summaryTab')"><i class="fas fa-list"></i> Summary</button>
            </div>
            
            <!-- IMMEDIATE SCRIPT TO ENSURE openTab FUNCTION IS AVAILABLE -->
            <script>
                // Define validateForm function IMMEDIATELY to prevent "not defined" errors
                window.validateForm = function() {
                    const form = document.getElementById('editClientForm');
                    const errors = [];
                    
                    // Check all required fields
                    const requiredFields = form.querySelectorAll('[required]');
                    requiredFields.forEach(function(field) {
                        const fieldName = field.name;
                        let isValid = false;
                        
                        // Debug log for gender fields
                        if (fieldName && (fieldName.includes('gender') || fieldName.includes('siblings_gender'))) {
                            const tabContent = field.closest('.tab-content');
                            const tabId = tabContent ? tabContent.id : 'unknown';
                            console.log('Checking gender field:', fieldName, 'Value:', field.value, 'SelectedIndex:', field.selectedIndex, 'Tab:', tabId);
                        }
                        
                        // Handle different field types
                        if (field.tagName === 'SELECT') {
                            // For select fields, check if a valid option is selected (not the first empty option)
                            isValid = field.selectedIndex > 0 && field.value !== '';
                        } else {
                            // For other fields (input, textarea), check if they have a value
                            isValid = field.value && field.value.trim() !== '';
                        }
                        
                        // If field is valid, skip to next field
                        if (isValid) {
                            return;
                        }
                        
                        // Field is invalid, add error with more context
                        const label = field.closest('.form-group')?.querySelector('label')?.textContent?.trim() || field.name || 'Unknown field';
                        
                        // Add context about which tab/section the field is in
                        let context = '';
                        const tabContent = field.closest('.tab-content');
                        if (tabContent) {
                            const tabId = tabContent.id;
                            if (tabId === 'personalTab') {
                                context = ' (Personal tab)';
                            } else if (tabId === 'familyTab') {
                                context = ' (Family Information tab)';
                            } else if (tabId === 'visaPassportCitizenshipTab') {
                                context = ' (Visa, Passport & Citizenship tab)';
                            } else if (tabId === 'addressTravelTab') {
                                context = ' (Address & Travel tab)';
                            } else if (tabId === 'skillsEducationTab') {
                                context = ' (Skills & Education tab)';
                            } else if (tabId === 'otherInformationTab') {
                                context = ' (Other Information tab)';
                            } else if (tabId === 'eoiReferenceTab') {
                                context = ' (EOI Reference tab)';
                            }
                        }
                        
                        errors.push(`"${label}" is required${context}`);
                    });
                    
                    // If there are errors, show them in alert and prevent submission
                    if (errors.length > 0) {
                        const errorMessage = 'Please fix the following errors:\n\n' + errors.join('\n');
                        alert(errorMessage);
                        return false;
                    }
                    
                    return true;
                };

                // Define openTab function IMMEDIATELY to prevent "not defined" errors
                if (typeof window.openTab === 'undefined') {
                    window.openTab = function(evt, tabName) {
                        try {
                            // Prevent default behavior
                            if (evt && evt.preventDefault) {
                                evt.preventDefault();
                            }
                            
                            // Hide all tab content
                            var tabcontent = document.getElementsByClassName("tab-content");
                            for (var i = 0; i < tabcontent.length; i++) {
                                tabcontent[i].style.display = "none";
                            }
                            
                            // Remove active class from all tab buttons
                            var tablinks = document.getElementsByClassName("tab-button");
                            for (var i = 0; i < tablinks.length; i++) {
                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                            }
                            
                            // Show the selected tab content
                            var targetTab = document.getElementById(tabName);
                            if (targetTab) {
                                targetTab.style.display = "block";
                            } else {
                                console.error('Tab content not found:', tabName);
                            }
                            
                            // Add active class to the clicked button
                            if (evt && evt.currentTarget) {
                                evt.currentTarget.className += " active";
                            }
                            
                            console.log('Tab switched to:', tabName);
                            
                        } catch (error) {
                            console.error('Error in openTab function:', error);
                            // Fallback: try to show the tab content directly
                            try {
                                var fallbackTab = document.getElementById(tabName);
                                if (fallbackTab) {
                                    // Hide all tabs first
                                    var allTabs = document.getElementsByClassName("tab-content");
                                    for (var i = 0; i < allTabs.length; i++) {
                                        allTabs[i].style.display = "none";
                                    }
                                    // Show target tab
                                    fallbackTab.style.display = "block";
                                }
                            } catch (fallbackError) {
                                console.error('Fallback also failed:', fallbackError);
                            }
                        }
                    };
                    console.log('openTab function defined immediately');
                }
                
                // ADDITIONAL FALLBACK: Set up event listeners as backup
                document.addEventListener('DOMContentLoaded', function() {
                    var tabButtons = document.querySelectorAll('.tab-button');
                    tabButtons.forEach(function(button) {
                        var onclickAttr = button.getAttribute('onclick');
                        if (onclickAttr && onclickAttr.includes('openTab')) {
                            var match = onclickAttr.match(/openTab\(event,\s*['"]([^'"]+)['"]\)/);
                            if (match) {
                                var tabName = match[1];
                                // Add event listener as backup
                                button.addEventListener('click', function(e) {
                                    if (typeof window.openTab === 'function') {
                                        window.openTab(e, tabName);
                                    } else {
                                        // Direct fallback if function still not available
                                        try {
                                            var tabcontent = document.getElementsByClassName("tab-content");
                                            for (var i = 0; i < tabcontent.length; i++) {
                                                tabcontent[i].style.display = "none";
                                            }
                                            var tablinks = document.getElementsByClassName("tab-button");
                                            for (var i = 0; i < tablinks.length; i++) {
                                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                                            }
                                            var targetTab = document.getElementById(tabName);
                                            if (targetTab) {
                                                targetTab.style.display = "block";
                                            }
                                            e.currentTarget.className += " active";
                                        } catch (directError) {
                                            console.error('Direct fallback failed:', directError);
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
            </script>

            <form id="editClientForm" action="{{ route('admin.clients.edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $fetchedData->id }}">
                <input type="hidden" name="type" value="{{ $fetchedData->type }}">

                <!-- Personal Tab -->
                <div id="personalTab" class="tab-content active">
                    <section class="form-section">
                        <h3><i class="fas fa-id-card"></i> Basic Information</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="first_name" value="{{ $fetchedData->first_name }}" required>
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="last_name" value="{{ $fetchedData->last_name }}">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="clientId">{{ $fetchedData->type == 'lead' ? 'Lead ID' : ($fetchedData->type == 'client' ? 'Client ID' : '') }}</label>
                                <input type="text" id="clientId" name="client_id" value="{{ $fetchedData->client_id }}" readonly>
                                @error('client_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="text" id="dob" name="dob" value="{{ $fetchedData->dob ? date('d/m/Y', strtotime($fetchedData->dob)) : '' }}" class="date-picker" placeholder="dd/mm/yyyy">
                                @error('dob')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- Added DOB Verified Slider -->
                            <div class="form-group" style="align-items: center;">
                                <label>DOB Verified?</label>
                                <label class="switch">
                                    <input type="checkbox" name="dob_verified" value="1" {{ $fetchedData->dob_verified_date ? 'checked' : '' }} 
                                    @if(Auth::user()->role != 1)
                                        disabled
                                    @endif
                                    >
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="text" id="age" name="age" value="{{ $fetchedData->age }}" readonly>
                            </div>

                             <div class="form-group" id="dob_verify_document_group" style="display: {{ (Auth::user()->role == 1 || $fetchedData->dob_verified_date) ? 'block' : 'none' }};">
                                <label for="dob_verify_document">DOB Verify Document</label>
                                <input type="text" id="dob_verify_document" name="dob_verify_document" value="{{ $fetchedData->dob_verify_document ?? '' }}" 
                                @if(Auth::user()->role == 1)
                                    {{-- Superadmin can always edit --}}
                                @else
                                    {{-- Non-superadmin can only edit if DOB is not verified or no document exists --}}
                                    @if($fetchedData->dob_verified_date && !empty($fetchedData->dob_verify_document))
                                        readonly
                                    @endif
                                @endif
                                >
                                @error('dob_verify_document')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <?php
                            $dob_verify_by_name = '';
                            if( isset($fetchedData->dob_verified_by) && $fetchedData->dob_verified_by != "")
                            {
                                $dob_verify_by_arr = \App\Models\Admin::select('first_name', 'last_name')->where('id', $fetchedData->dob_verified_by)->first();
                                if($dob_verify_by_arr){
                                    $dob_verify_by_name = $dob_verify_by_arr->first_name.' '.$dob_verify_by_arr->last_name;
                                }
                            }
                            ?>
                            <div class="form-group" id="dob_verify_by_group" style="display: {{ $fetchedData->dob_verified_date ? 'block' : 'none' }};">
                                <label for="dob_verify_by">DOB Verify By</label>
                                <input type="text" id="dob_verify_by" name="dob_verify_by" value="{{ $dob_verify_by_name }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <div class="radio-group">
                                    <label><input type="radio" name="gender" value="Male" {{ $fetchedData->gender == 'Male' ? 'checked' : '' }}> Male</label>
                                    <label><input type="radio" name="gender" value="Female" {{ $fetchedData->gender == 'Female' ? 'checked' : '' }}> Female</label>
                                    <label><input type="radio" name="gender" value="Other" {{ $fetchedData->gender == 'Other' ? 'checked' : '' }}> Other</label>
                                </div>
                                @error('gender')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="martialStatus">Marital Status</label>
                                <select id="martialStatus" name="martial_status">
                                    <option value="">Select Marital Status</option>
                                    <option value="Single" {{ $fetchedData->martial_status == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ $fetchedData->martial_status == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="De Facto" {{ $fetchedData->martial_status == 'De Facto' ? 'selected' : '' }}>De Facto</option>
                                    <option value="Divorced" {{ $fetchedData->martial_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Widowed" {{ $fetchedData->martial_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ $fetchedData->martial_status == 'Separated' ? 'selected' : '' }}>Separated</option>
                                </select>
                                @error('martial_status')
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
                                    <option value="Personal" {{ $fetchedData->emergency_contact_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Work" {{ $fetchedData->emergency_contact_type == 'Work' ? 'selected' : '' }}>Work</option>
                                    <option value="Mobile" {{ $fetchedData->emergency_contact_type == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                    <option value="Business" {{ $fetchedData->emergency_contact_type == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Secondary" {{ $fetchedData->emergency_contact_type == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                                    <option value="Father" {{ $fetchedData->emergency_contact_type == 'Father' ? 'selected' : '' }}>Father</option>
                                    <option value="Mother" {{ $fetchedData->emergency_contact_type == 'Mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="Brother" {{ $fetchedData->emergency_contact_type == 'Brother' ? 'selected' : '' }}>Brother</option>
                                    <option value="Sister" {{ $fetchedData->emergency_contact_type == 'Sister' ? 'selected' : '' }}>Sister</option>
                                    <option value="Uncle" {{ $fetchedData->emergency_contact_type == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                    <option value="Aunt" {{ $fetchedData->emergency_contact_type == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                    <option value="Cousin" {{ $fetchedData->emergency_contact_type == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                                    <option value="Others" {{ $fetchedData->emergency_contact_type == 'Others' ? 'selected' : '' }}>Others</option>
                                    <option value="Partner" {{ $fetchedData->emergency_contact_type == 'Partner' ? 'selected' : '' }}>Partner</option>
                                    <option value="Not In Use" {{ $fetchedData->emergency_contact_type == 'Not In Use' ? 'selected' : '' }}>Not In Use</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Number</label>
                                <div class="cus_field_input" style="display:flex;">
                                    <div class="country_code">
                                        <input class="telephone" id="telephone" type="tel" name="emergency_country_code" value="{{ $fetchedData->emergency_country_code }}" style="width: 55px;height: 42px;" readonly >
                                    </div>
                                    <input type="text" name="emergency_contact_no" value="{{ isset($fetchedData->emergency_contact_no) ? $fetchedData->emergency_contact_no : '' }}" class="form-control tel_input" autocomplete="off" placeholder="Emergency Contact Number" style="width: 230px;">
                                </div>
                            </div>
                        </div>

                        <!-- Phone Numbers -->
                        <div class="form-section">
                            <h4><i class="fas fa-phone"></i> Phone Numbers</h4>
                            <div id="phoneNumbersContainer">
                                @foreach($clientContacts as $index => $contact)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Phone"><i class="fas fa-times-circle"></i></button>
                                        <input type="hidden" name="contact_id[{{ $index }}]" value="{{ $contact->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select name="contact_type_hidden[{{ $index }}]" class="contact-type-selector">
                                                    <option value="Personal" {{ $contact->contact_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                                    <option value="Work" {{ $contact->contact_type == 'Work' ? 'selected' : '' }}>Work</option>
                                                    <option value="Mobile" {{ $contact->contact_type == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                                    <option value="Business" {{ $contact->contact_type == 'Business' ? 'selected' : '' }}>Business</option>
                                                    <option value="Secondary" {{ $contact->contact_type == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                                                    <option value="Father" {{ $contact->contact_type == 'Father' ? 'selected' : '' }}>Father</option>
                                                    <option value="Mother" {{ $contact->contact_type == 'Mother' ? 'selected' : '' }}>Mother</option>
                                                    <option value="Brother" {{ $contact->contact_type == 'Brother' ? 'selected' : '' }}>Brother</option>
                                                    <option value="Sister" {{ $contact->contact_type == 'Sister' ? 'selected' : '' }}>Sister</option>
                                                    <option value="Uncle" {{ $contact->contact_type == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                                    <option value="Aunt" {{ $contact->contact_type == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                                    <option value="Cousin" {{ $contact->contact_type == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                                                    <option value="Others" {{ $contact->contact_type == 'Others' ? 'selected' : '' }}>Others</option>
                                                    <option value="Partner" {{ $contact->contact_type == 'Partner' ? 'selected' : '' }}>Partner</option>
                                                    <option value="Not In Use" {{ $contact->contact_type == 'Not In Use' ? 'selected' : '' }}>Not In Use</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Number</label>
                                                <div class="cus_field_input" style="display:flex;">
                                                    <div class="country_code">
                                                        <input class="telephone country-code-input" id="telephone" type="tel" name="country_code[{{ $index }}]" value="{{ $contact->country_code }}" style="width: 55px;height: 42px;" readonly >
                                                    </div>
                                                    <input type="tel" name="phone[{{ $index }}]" value="{{ $contact->phone }}" placeholder="Enter Phone Number" class="phone-number-input" style="width: 230px;" autocomplete="off">
                                                </div>
                                                @error('phone.' . $index)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                                <button type="button" class="add-item-btn" onclick="addPhoneNumber()"><i class="fas fa-plus"></i> Add Phone Number</button>
                                <!-- Moved Phone Verified Slider Here -->
                                <div class="form-group" style="align-items: center;">
                                    <label>Recent Phone Verified?</label>
                                    <label class="switch">
                                        <input type="checkbox" name="phone_verified" value="1" {{ $fetchedData->phone_verified_date ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Email Addresses -->
                        <div class="form-section">
                            <h4><i class="fas fa-envelope"></i> Email Addresses</h4>
                            <div id="emailAddressesContainer">
                                @foreach($emails as $index => $email)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Email"><i class="fas fa-times-circle"></i></button>
                                        <input type="hidden" name="email_id[{{ $index }}]" value="{{ $email->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select name="email_type_hidden[{{ $index }}]" class="email-type-selector">
                                                    <option value="Personal" {{ $email->email_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                                    <option value="Work" {{ $email->email_type == 'Work' ? 'selected' : '' }}>Work</option>
                                                    <option value="Business" {{ $email->email_type == 'Business' ? 'selected' : '' }}>Business</option>

                                                    <option value="Mobile" {{ $email->email_type == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                                    <option value="Secondary" {{ $email->email_type == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                                                    <option value="Father" {{ $email->email_type == 'Father' ? 'selected' : '' }}>Father</option>
                                                    <option value="Mother" {{ $email->email_type == 'Mother' ? 'selected' : '' }}>Mother</option>
                                                    <option value="Brother" {{ $email->email_type == 'Brother' ? 'selected' : '' }}>Brother</option>
                                                    <option value="Sister" {{ $email->email_type == 'Sister' ? 'selected' : '' }}>Sister</option>
                                                    <option value="Uncle" {{ $email->email_type == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                                    <option value="Aunt" {{ $email->email_type == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                                    <option value="Cousin" {{ $email->email_type == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                                                    <option value="Others" {{ $email->email_type == 'Others' ? 'selected' : '' }}>Others</option>
                                                    <option value="Partner" {{ $email->email_type == 'Partner' ? 'selected' : '' }}>Partner</option>
                                                    <option value="Not In Use" {{ $email->email_type == 'Not In Use' ? 'selected' : '' }}>Not In Use</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Email Address</label>
                                                <input type="email" name="email[{{ $index }}]" value="{{ $email->email }}" placeholder="Enter Email Address">
                                                @error('email.' . $index)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                                <button type="button" class="add-item-btn" onclick="addEmailAddress()"><i class="fas fa-plus"></i> Add Email Address</button>
                                <!-- Moved Email Verified Slider Here -->
                                <div class="form-group" style="align-items: center;">
                                    <label>Recent Email Verified?</label>
                                    <label class="switch">
                                        <input type="checkbox" name="email_verified" value="1" {{ $fetchedData->email_verified_date ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
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
                            <select name="visa_country" id="passportCountry" class="passport-country-selector">
                                <option value="">Select Country</option>
                                <option value="India" {{ $fetchedData->country_passport == 'India' ? 'selected' : '' }}>India</option>
                                <option value="Australia" {{ $fetchedData->country_passport == 'Australia' ? 'selected' : '' }}>Australia</option>
                                @foreach(\App\Models\Country::all() as $list)
                                    @if($list->name != 'India' && $list->name != 'Australia')
                                        <option value="{{ $list->name }}" {{ $fetchedData->country_passport == $list->name ? 'selected' : '' }}>{{ $list->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('visa_country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-passport"></i> Passport Information</h3>
                        <div id="passportDetailsContainer">
                            @foreach($clientPassports as $index => $passport)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Passport"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="passport_id[{{ $index }}]" value="{{ $passport->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Passport #</label>
                                            <input type="text" name="passports[{{ $index }}][passport_number]" value="{{ $passport->passport }}" placeholder="Enter Passport Number">
                                            @error('passports.' . $index . '.passport_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Issue Date</label>
                                            <input type="text" name="passports[{{ $index }}][issue_date]" value="{{ $passport->passport_issue_date ? date('d/m/Y', strtotime($passport->passport_issue_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error('passports.' . $index . '.issue_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Expiry Date</label>
                                            <input type="text" name="passports[{{ $index }}][expiry_date]" value="{{ $passport->passport_expiry_date ? date('d/m/Y', strtotime($passport->passport_expiry_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error('passports.' . $index . '.expiry_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPassportDetail()"><i class="fas fa-plus"></i> Add Passport</button>
                    </section>

                    <section class="form-section" id="visaDetailsSection">
                        <h3 id="visaDetailsContainerHeading"><i class="fas fa-file-contract"></i> Visa Details</h3>
                        <div id="visaDetailsContainer">
                            @foreach($visaCountries as $index => $visa)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Visa"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="visas[{{ $index }}][id]" value="{{ $visa->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Visa Type / Subclass</label>
                                            <select name="visas[{{ $index }}][visa_type]" class="visa-type-field">
                                                <option value="">Select Visa Type</option>
                                                @foreach(\App\Models\Matter::select('id', 'title', 'nick_name')->where('status', 1)->where('title', 'not like', '%skill assessment%')->orderBy('title', 'ASC')->get() as $matterlist)
                                                    <option value="{{ $matterlist->id }}" {{ $visa->visa_type == $matterlist->id ? 'selected' : '' }}>{{ $matterlist->title }} ({{ @$matterlist->nick_name }})</option>
                                                @endforeach
                                            </select>
                                            @error('visas.' . $index . '.visa_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Visa Expiry Date</label>
                                            <input type="text" name="visas[{{ $index }}][expiry_date]" value="{{ $visa->visa_expiry_date ? date('d/m/Y', strtotime($visa->visa_expiry_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                                            @error('visas.' . $index . '.expiry_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                         <div class="form-group">
                                            <label>Visa Grant Date</label>
                                            <input type="text" name="visas[{{ $index }}][grant_date]" value="{{ $visa->visa_grant_date ? date('d/m/Y', strtotime($visa->visa_grant_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-grant-field date-picker">
                                            @error('visas.' . $index . '.grant_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Visa Description</label>
                                            <input type="text" name="visas[{{ $index }}][description]" value="{{ $visa->visa_description }}" class="visa-description-field">
                                            @error('visas.' . $index . '.description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
                            <button type="button" class="add-item-btn" onclick="addVisaDetail()"><i class="fas fa-plus"></i> Add Visa Detail</button>
                            <div class="form-group" style="align-items: center;" id="visaExpiryVerifiedContainer">
                                <label>Visa Expiry Verified?</label>
                                <label class="switch">
                                    <input type="checkbox" name="visa_expiry_verified" value="1" {{ $fetchedData->visa_expiry_verified_at ? 'checked' : '' }}>
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
                                <input type="checkbox" id="isCurrentAddress" name="is_current_address" value="1" {{ $clientAddresses->isNotEmpty() && $clientAddresses->sortByDesc('id')->first()?->is_current ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div id="addressesContainer">
                            @foreach($clientAddresses->sortByDesc('id') as $index => $address)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Address" onclick="removeAddress(this, '{{ $address->id }}')"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="address_id[{{ $index }}]" value="{{ $address->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Full Address</label>
                                            <textarea name="address[{{ $index }}]" rows="1" class="address-input">{{ $address->address }}</textarea>
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Post Code</label>
                                            <input type="text" name="zip[{{ $index }}]" value="{{ $address->zip }}" class="postcode-input">
                                        </div>
                                    </div>
                                    <div class="content-grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Regional Code Info</label>
                                            <input type="text" name="regional_code[{{ $index }}]" value="{{ $address->regional_code }}" class="regional-code-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="address_start_date[{{ $index }}]" value="{{ $address->start_date ? date('d/m/Y', strtotime($address->start_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker address-start-date">
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="text" name="address_end_date[{{ $index }}]" value="{{ $address->end_date ? date('d/m/Y', strtotime($address->end_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker address-end-date">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addAddress()"><i class="fas fa-plus"></i> Add Address</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-plane"></i> Travel History</h3>
                        <div id="travelDetailsContainer">
                            @foreach($clientTravels as $index => $travel)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Travel"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="travel_id[{{ $index }}]" value="{{ $travel->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Country Visited</label>
                                            <select name="travel_country_visited[{{ $index }}]">
                                                <option value="India" {{ $travel->travel_country_visited == 'India' ? 'selected' : '' }}>India</option>
                                                <option value="Australia" {{ $travel->travel_country_visited == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                @foreach(\App\Models\Country::all() as $list)
                                                    @if($list->name != 'India' && $list->name != 'Australia')
                                                        <option value="{{ $list->name }}" {{ $travel->travel_country_visited == $list->name ? 'selected' : '' }}>{{ $list->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('travel_country_visited.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Arrival Date</label>
                                            <input type="text" name="travel_arrival_date[{{ $index }}]" value="{{ $travel->travel_arrival_date ? date('d/m/Y', strtotime($travel->travel_arrival_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error('travel_arrival_date.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Departure Date</label>
                                            <input type="text" name="travel_departure_date[{{ $index }}]" value="{{ $travel->travel_departure_date ? date('d/m/Y', strtotime($travel->travel_departure_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error('travel_departure_date.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Travel Purpose</label>
                                            <input type="text" name="travel_purpose[{{ $index }}]" value="{{ $travel->travel_purpose }}" placeholder="Enter Travel Purpose">
                                            @error('travel_purpose.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addTravelDetail()"><i class="fas fa-plus"></i> Add Travel Detail</button>
                    </section>
                </div>

                 <!-- Skills & Education Tab (Renamed from Skills & History) -->
                <div id="skillsEducationTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-graduation-cap"></i> Qualifications</h3>
                        <div id="qualificationsContainer">
                            @foreach($qualifications as $index => $qualification)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Qualification"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="qualification_id[{{ $index }}]" value="{{ $qualification->id }}">
                                    <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                        <div class="form-group">
                                            <label>Level</label>
                                            <select name="level_hidden[{{ $index }}]">
                                                <option value="">Select Level</option>
                                                <option value="Certificate I" {{ $qualification->level == 'Certificate I' ? 'selected' : '' }}>Certificate I</option>
                                                <option value="Certificate II" {{ $qualification->level == 'Certificate II' ? 'selected' : '' }}>Certificate II</option>
                                                <option value="Certificate III" {{ $qualification->level == 'Certificate III' ? 'selected' : '' }}>Certificate III</option>
                                                <option value="Certificate IV" {{ $qualification->level == 'Certificate IV' ? 'selected' : '' }}>Certificate IV</option>
                                                <option value="Diploma" {{ $qualification->level == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                                <option value="Advanced Diploma" {{ $qualification->level == 'Advanced Diploma' ? 'selected' : '' }}>Advanced Diploma</option>
                                                <option value="Associate Degree" {{ $qualification->level == 'Associate Degree' ? 'selected' : '' }}>Associate Degree</option>
                                                <option value="Bachelor Degree" {{ $qualification->level == 'Bachelor Degree' ? 'selected' : '' }}>Bachelor Degree</option>
                                                <option value="Bachelor Honours Degree" {{ $qualification->level == 'Bachelor Honours Degree' ? 'selected' : '' }}>Bachelor Honours Degree</option>
                                                <option value="Graduate Certificate" {{ $qualification->level == 'Graduate Certificate' ? 'selected' : '' }}>Graduate Certificate</option>
                                                <option value="Graduate Diploma" {{ $qualification->level == 'Graduate Diploma' ? 'selected' : '' }}>Graduate Diploma</option>
                                                <option value="Masters Degree" {{ $qualification->level == 'Masters Degree' ? 'selected' : '' }}>Masters Degree</option>
                                                <option value="Doctoral Degree" {{ $qualification->level == 'Doctoral Degree' ? 'selected' : '' }}>Doctoral Degree</option>
                                                <option value="12" {{ $qualification->level == '12' ? 'selected' : '' }}>12</option>
                                                <option value="10" {{ $qualification->level == '10' ? 'selected' : '' }}>10</option>
                                                <option value="Others" {{ $qualification->level == 'Others' ? 'selected' : '' }}>Others</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name[{{ $index }}]" value="{{ $qualification->name }}" placeholder="e.g., B. Engineering">
                                        </div>
                                        <div class="form-group">
                                            <label>College Name</label>
                                            <input type="text" name="qual_college_name[{{ $index }}]" value="{{ $qualification->qual_college_name }}" placeholder="Enter college name">
                                        </div>
                                        <div class="form-group">
                                            <label>Campus</label>
                                            <input type="text" name="qual_campus[{{ $index }}]" value="{{ $qualification->qual_campus }}" placeholder="Enter campus">
                                        </div>
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country_hidden[{{ $index }}]">
                                                <option value="India" {{ @$qualification->country == 'India' ? 'selected' : '' }}>India</option>
                                                <option value="Australia" {{ @$qualification->country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                @foreach(\App\Models\Country::all() as $list)
                                                    @if($list->name != 'India' && $list->name != 'Australia')
                                                        <option value="{{ $list->name }}" {{ $qualification->country == $list->name ? 'selected' : '' }}>{{ $list->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="qual_state[{{ $index }}]" rows="2" placeholder="Enter address">{{ $qualification->qual_state }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="start_date[{{ $index }}]" value="{{ $qualification->start_date ? date('d/m/Y', strtotime($qualification->start_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>Finish Date</label>
                                            <input type="text" name="finish_date[{{ $index }}]" value="{{ $qualification->finish_date ? date('d/m/Y', strtotime($qualification->finish_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group" style="align-items: center;">
                                            <label>Relevant?</label>
                                            <label class="switch">
                                                <input type="checkbox" name="relevant_qualification_hidden[{{ $index }}]" value="1" {{ $qualification->relevant_qualification ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addQualification()"><i class="fas fa-plus"></i> Add Qualification</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-briefcase"></i> Work Experience</h3>
                        <div id="experienceContainer">
                            @foreach($experiences as $index => $experience)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Experience"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="job_id[{{ $index }}]" value="{{ $experience->id }}">
                                    <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                        <div class="form-group">
                                            <label>Job Title</label>
                                            <input type="text" name="job_title[{{ $index }}]" value="{{ $experience->job_title }}" placeholder="e.g., Software Engineer">
                                        </div>
                                        <div class="form-group">
                                            <label>ANZSCO Code</label>
                                            <input type="text" name="job_code[{{ $index }}]" value="{{ $experience->job_code }}" placeholder="e.g., 261313">
                                        </div>
                                        <div class="form-group">
                                            <label>Employer Name</label>
                                            <input type="text" name="job_emp_name[{{ $index }}]" value="{{ $experience->job_emp_name }}" placeholder="Enter employer name">
                                        </div>
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="job_country_hidden[{{ $index }}]">
                                                <option value="India" {{ @$experience->job_country == 'India' ? 'selected' : '' }}>India</option>
                                                <option value="Australia" {{ @$experience->job_country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                @foreach(\App\Models\Country::all() as $list)
                                                    @if($list->name != 'India' && $list->name != 'Australia')
                                                        <option value="{{ $list->name }}" {{ $experience->job_country == $list->name ? 'selected' : '' }}>{{ $list->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="job_state[{{ $index }}]" rows="2" placeholder="Enter address">{{ $experience->job_state }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Job Type</label>
                                            <select name="job_type[{{ $index }}]">
                                                <option value="">Select job type</option>
                                                <option value="Part Time" {{ @$experience->job_type == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                                <option value="Full Time" {{ @$experience->job_type == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                                <option value="Casual" {{ @$experience->job_type == 'Casual' ? 'selected' : '' }}>Casual</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="job_start_date[{{ $index }}]" value="{{ $experience->job_start_date ? date('d/m/Y', strtotime($experience->job_start_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>Finish Date</label>
                                            <input type="text" name="job_finish_date[{{ $index }}]" value="{{ $experience->job_finish_date ? date('d/m/Y', strtotime($experience->job_finish_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group" style="align-items: center;">
                                            <label>Relevant?</label>
                                            <label class="switch">
                                                <input type="checkbox" name="relevant_experience_hidden[{{ $index }}]" value="1" {{ $experience->relevant_experience ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addExperience()"><i class="fas fa-plus"></i> Add Experience</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-cogs"></i> Occupation & Skills</h3>
                        <div id="occupationContainer">
                            @foreach($clientOccupations as $index => $occupation)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Occupation"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="occupation_id[{{ $index }}]" value="{{ $occupation->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label for="nomiOccupation">Nominated Occupation</label>
                                            <input type="text" name="nomi_occupation[{{ $index }}]" class="nomi_occupation" value="{{ $occupation->nomi_occupation }}" placeholder="Enter Occupation">
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="list">Assessing Authority</label>
                                            <input type="text" name="list[{{ $index }}]" class="list" value="{{ $occupation->list }}" placeholder="e.g., ACS, VETASSESS">
                                        </div>
                                        <div class="form-group">
                                            <label for="occupationCode">Occupation Code (ANZSCO)</label>
                                            <input type="text" name="occupation_code[{{ $index }}]" class="occupation_code" value="{{ $occupation->occupation_code }}" placeholder="Enter Code">
                                        </div>

                                        <div class="form-group">
                                            <label for="occ_reference_no">Reference No</label>
                                            <input type="text" name="occ_reference_no[{{ $index }}]" value="{{ $occupation->occ_reference_no }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="dates">Assessment Date</label>
                                            <input type="text" name="dates[{{ $index }}]" class="dates date-picker" value="{{ $occupation->dates ? date('d/m/Y', strtotime($occupation->dates)) : '' }}" placeholder="dd/mm/yyyy">
                                        </div>
                                        <div class="form-group">
                                            <label for="expiry_dates">Expiry Date</label>
                                            <input type="text" name="expiry_dates[{{ $index }}]" class="expiry_dates date-picker" value="{{ $occupation->expiry_dates ? date('d/m/Y', strtotime($occupation->expiry_dates)) : '' }}" placeholder="dd/mm/yyyy">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addOccupation()"><i class="fas fa-plus"></i> Add Occupation</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-language"></i> English Test Scores</h3>
                        <div id="testScoresContainer">
                            @foreach($testScores as $index => $test)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Test"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="test_score_id[{{ $index }}]" value="{{ $test->id }}">
                                    <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px;">
                                        <div class="form-group">
                                            <label>Test Type</label>
                                            <select name="test_type_hidden[{{ $index }}]" class="test-type-selector" onchange="updateTestScoreValidation(this, {{ $index }})">
                                                <option value="">Select Test</option>
                                                <option value="IELTS" {{ $test->test_type == 'IELTS' ? 'selected' : '' }}>IELTS General</option>
                                                <option value="IELTS_A" {{ $test->test_type == 'IELTS_A' ? 'selected' : '' }}>IELTS Academic</option>
                                                <option value="PTE" {{ $test->test_type == 'PTE' ? 'selected' : '' }}>PTE Academic</option>
                                                <option value="TOEFL" {{ $test->test_type == 'TOEFL' ? 'selected' : '' }}>TOEFL iBT</option>
                                                <option value="CAE" {{ $test->test_type == 'CAE' ? 'selected' : '' }}>CAE</option>
                                                <option value="OET" {{ $test->test_type == 'OET' ? 'selected' : '' }}>OET</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Listening</label>
                                            <input type="text" name="listening[{{ $index }}]" value="{{ $test->listening }}" placeholder="Score" class="test-score-input" data-index="{{ $index }}">
                                            @error('listening.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Reading</label>
                                            <input type="text" name="reading[{{ $index }}]" value="{{ $test->reading }}" placeholder="Score" class="test-score-input" data-index="{{ $index }}">
                                            @error('reading.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Writing</label>
                                            <input type="text" name="writing[{{ $index }}]" value="{{ $test->writing }}" placeholder="Score" class="test-score-input" data-index="{{ $index }}">
                                            @error('writing.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Speaking</label>
                                            <input type="text" name="speaking[{{ $index }}]" value="{{ $test->speaking }}" placeholder="Score" class="test-score-input" data-index="{{ $index }}">
                                            @error('speaking.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Overall</label>
                                            <input type="text" name="overall_score[{{ $index }}]" value="{{ $test->overall_score }}" placeholder="Score" class="test-score-input" data-index="{{ $index }}">
                                            @error('overall_score.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date of Test</label>
                                            <input type="text" name="test_date[{{ $index }}]" value="{{ $test->test_date ? date('d/m/Y', strtotime($test->test_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker test-date">
                                            @error('test_date.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Reference No</label>
                                            <input type="text" name="test_reference_no[{{ $index }}]" value="{{ $test->test_reference_no }}" placeholder="Reference no" data-index="{{ $index }}">
                                            @error('test_reference_no.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group" style="align-items: center;">
                                            <label>Relevant?</label>
                                            <label class="switch">
                                                <input type="checkbox" name="relevant_test_hidden[{{ $index }}]" value="1" {{ $test->relevant_test ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                                        <input type="checkbox" id="naatiGiven" name="naati_test" value="1" {{ $fetchedData->naati_test ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <input type="text" id="naatiDate" name="naati_date" value="{{ $fetchedData->naati_date ? date('d/m/Y', strtotime($fetchedData->naati_date)) : '' }}" style="max-width: 150px;" placeholder="dd/mm/yyyy" class="date-picker naati-date">
                                    @error('naati_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="text" id="nati_language" name="nati_language" value="{{ $fetchedData->nati_language ? $fetchedData->nati_language : '' }}" style="max-width: 150px;" placeholder="Nati Language">

                                </div>
                            </div>
                            <div class="form-group">
                                <label>Professional Year (PY)?</label>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <label class="switch">
                                        <input type="checkbox" id="pyGiven" name="py_test" value="1" {{ $fetchedData->py_test ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <input type="text" id="pyDate" name="py_date" value="{{ $fetchedData->py_date ? date('d/m/Y', strtotime($fetchedData->py_date)) : '' }}" style="max-width: 150px;" placeholder="dd/mm/yyyy" class="date-picker py-date">
                                    @error('py_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <select name="py_field" id="py_field">
                                        <option value="">Select Type</option>
                                        <option value="Accounting" {{ @$fetchedData->py_field == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                        <option value="IT" {{ @$fetchedData->py_field == 'IT' ? 'selected' : '' }}>IT</option>
                                        <option value="Engineering" {{ @$fetchedData->py_field == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Regional Points</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <input type="text" id="regional_points" name="regional_points" value="{{ $fetchedData->regional_points ? $fetchedData->regional_points : '' }}" style="max-width: 150px;">
                            </div>
                        </div>
                    </section>

                    <section class="form-section" id="spouseDetailsSection" style="display: none;">
                        <h3><i class="fas fa-user"></i> Spouse Details</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Does your spouse have an English score? *</label>
                                <select name="spouse_has_english_score" id="spouseHasEnglishScore" onchange="toggleSpouseEnglishFields()">
                                    <option value="No" {{ optional($ClientSpouseDetail)->spouse_has_english_score == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Yes" {{ optional($ClientSpouseDetail)->spouse_has_english_score == 'Yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Does your spouse have a skill assessment? *</label>
                                <select name="spouse_has_skill_assessment" id="spouseHasSkillAssessment" onchange="toggleSpouseSkillFields()">
                                    <option value="No" {{ optional($ClientSpouseDetail)->spouse_has_skill_assessment == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Yes" {{ optional($ClientSpouseDetail)->spouse_has_skill_assessment == 'Yes' ? 'selected' : '' }}>Yes</option>
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
                                        <option value="IELTS" {{ optional($ClientSpouseDetail)->spouse_test_type == 'IELTS' ? 'selected' : '' }}>IELTS General</option>
                                        <option value="IELTS_A" {{ optional($ClientSpouseDetail)->spouse_test_type == 'IELTS_A' ? 'selected' : '' }}>IELTS Academic</option>
                                        <option value="PTE" {{ optional($ClientSpouseDetail)->spouse_test_type == 'PTE' ? 'selected' : '' }}>PTE Academic</option>
                                        <option value="TOEFL" {{ optional($ClientSpouseDetail)->spouse_test_type == 'TOEFL' ? 'selected' : '' }}>TOEFL iBT</option>
                                        <option value="CAE" {{ optional($ClientSpouseDetail)->spouse_test_type == 'CAE' ? 'selected' : '' }}>CAE</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Spouse Listening Score</label>
                                    <input type="text" name="spouse_listening_score" value="{{ optional($ClientSpouseDetail)->spouse_listening_score ?? '' }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Reading Score</label>
                                    <input type="text" name="spouse_reading_score" value="{{ optional($ClientSpouseDetail)->spouse_reading_score ?? '' }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Writing Score</label>
                                    <input type="text" name="spouse_writing_score" value="{{ optional($ClientSpouseDetail)->spouse_writing_score ?? '' }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Speaking Score</label>
                                    <input type="text" name="spouse_speaking_score" value="{{ optional($ClientSpouseDetail)->spouse_speaking_score ?? '' }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Overall Score</label>
                                    <input type="text" name="spouse_overall_score" value="{{ optional($ClientSpouseDetail)->spouse_overall_score ?? '' }}" placeholder="Score">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Test Date</label>
                                    <input type="text" name="spouse_test_date" value="{{ optional($ClientSpouseDetail)->spouse_test_date ? date('d/m/Y', strtotime($ClientSpouseDetail->spouse_test_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
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
                                    <input type="text" name="spouse_skill_assessment_status" value="{{ optional($ClientSpouseDetail)->spouse_skill_assessment_status ?? '' }}" placeholder="e.g., Positive, Negative">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Nominated Occupation</label>
                                    <input type="text" name="spouse_nomi_occupation" value="{{ optional($ClientSpouseDetail)->spouse_nomi_occupation ?? '' }}" placeholder="e.g., Software Engineer">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Assessment Date</label>
                                    <input type="text" name="spouse_assessment_date" value="{{ optional($ClientSpouseDetail)->spouse_assessment_date ? date('d/m/Y', strtotime($ClientSpouseDetail->spouse_assessment_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
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
                    <section class="form-section">
                        <h3><i class="fas fa-gavel"></i> Character & History</h3>

                        <!-- Criminal Charges -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;">Criminal Charges</h4>
                        <div id="criminalChargesContainer">
                            @foreach($clientCharacters->where('type_of_character', 1) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="criminal_charges[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="criminal_charges[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("criminal_charges.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="criminal_charges[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("criminal_charges.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'criminal_charges', '{{ $character->id }}')" title="Remove Criminal Charge"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('criminalChargesContainer', 'criminal_charges')"><i class="fas fa-plus"></i> Add Criminal Charges</button>

                        <!-- Military Service -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;margin-top: 20px;">Military Service</h4>
                        <div id="militaryServiceContainer">
                            @foreach($clientCharacters->where('type_of_character', 2) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="military_service[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="military_service[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("military_service.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="military_service[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("military_service.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'military_service', '{{ $character->id }}')" title="Remove Military Service"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('militaryServiceContainer', 'military_service')"><i class="fas fa-plus"></i> Add Military Service</button>

                        <!-- Intelligence Work -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;margin-top: 20px;">Intelligence Work</h4>
                        <div id="intelligenceWorkContainer">
                            @foreach($clientCharacters->where('type_of_character', 3) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="intelligence_work[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="intelligence_work[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("intelligence_work.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="intelligence_work[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("intelligence_work.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'intelligence_work', '{{ $character->id }}')" title="Remove Intelligence Work"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('intelligenceWorkContainer', 'intelligence_work')"><i class="fas fa-plus"></i> Add Intelligence Work</button>

                        <!-- Visa Refusals -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;margin-top: 20px;">Visa Refusals</h4>
                        <div id="visaRefusalsContainer">
                            @foreach($clientCharacters->where('type_of_character', 4) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="visa_refusals[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="visa_refusals[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("visa_refusals.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="visa_refusals[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("visa_refusals.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'visa_refusals', '{{ $character->id }}')" title="Remove Visa Refusal"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('visaRefusalsContainer', 'visa_refusals')"><i class="fas fa-plus"></i> Add Visa Refusals</button>

                        <!-- Deportations -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;margin-top: 20px;">Deportations</h4>
                        <div id="deportationsContainer">
                            @foreach($clientCharacters->where('type_of_character', 5) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="deportations[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="deportations[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("deportations.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="deportations[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("deportations.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'deportations', '{{ $character->id }}')" title="Remove Deportation"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('deportationsContainer', 'deportations')"><i class="fas fa-plus"></i> Add Deportations</button>

                        <!-- Citizenship Refusals -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;margin-top: 20px;">Citizenship Refusals</h4>
                        <div id="citizenshipRefusalsContainer">
                            @foreach($clientCharacters->where('type_of_character', 6) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="citizenship_refusals[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="citizenship_refusals[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("citizenship_refusals.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="citizenship_refusals[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("citizenship_refusals.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'citizenship_refusals', '{{ $character->id }}')" title="Remove Citizenship Refusal"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('citizenshipRefusalsContainer', 'citizenship_refusals')"><i class="fas fa-plus"></i> Add Citizenship Refusals</button>


                        <!-- Health Declaration -->
                        <h4 style="font-size: 0.9em; margin-bottom: 20px;margin-top: 20px;">Health Declaration</h4>
                        <div id="healthDeclarationsContainer">
                            @foreach($clientCharacters->where('type_of_character', 7) as $index => $character)
                                <div class="repeatable-section">
                                    <input type="hidden" name="health_declarations[{{$index}}][id]" value="{{ $character->id }}">
                                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <textarea name="health_declarations[{{$index}}][details]" rows="1">{{ $character->character_detail ?? '' }}</textarea>
                                            @error("health_declarations.{$index}.details")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="health_declarations[{{$index}}][date]" value="{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error("health_declarations.{$index}.date")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button type="button" class="remove-item-btn" onclick="removeCharacterRow(this, 'health_declarations', '{{ $character->id }}')" title="Remove Health Declaration"><i class="fas fa-times-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addCharacterRow('healthDeclarationsContainer', 'health_declarations')"><i class="fas fa-plus"></i> Add Health Declaration</button>
                    </section>

                    <div class="form-group" style="width: 50%;">
                        <label>Source</label>
                        <select name="source" id="source" style="width: 100%;">
                            <option value="SubAgent" {{ $fetchedData->source == 'SubAgent' ? 'selected' : '' }}>SubAgent</option>
                            <option value="Others" {{ $fetchedData->source == 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>

                    <div class="col-sm-9" style="margin-left: -10px;">
                        <div class="form-group">
                            <label for="related_files">Similar related files</label>
                            <select multiple class="form-control js-data-example-ajaxcc" name="related_files[]">

                            </select>
                            @if ($errors->has('related_files'))
                                <span class="custom-error" role="alert">
                                    <strong>{{ @$errors->first('related_files') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Family Tab -->
                <div id="familyTab" class="tab-content">
                    <h3><i class="fas fa-users"></i> Family Members</h3>
                    <!-- Partner Subsection -->
                    <section class="form-section">
                        <h3><i class="fas fa-user"></i> Partner</h3>
                        <div id="partnerContainer">
                            @foreach($clientPartners->whereIn('relationship_type', ['Husband', 'Wife', 'Ex-Wife', 'Defacto'])->values() as $index => $partner)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Partner" onclick="removePartnerRow(this, 'partner', '{{ $partner->id }}')"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="partner_id[{{ $index }}]" value="{{ $partner->related_client_id ?? '' }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="partner_details[{{ $index }}]" class="partner-details" value="{{ $partner->details }}" placeholder="Search by Name, Email, Client ID, or Phone">

                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship Type</label>
                                            <select name="partner_relationship_type[{{ $index }}]" required>
                                                <option value="">Select Relationship</option>
                                                <option value="Husband" {{ $partner->relationship_type == 'Husband' ? 'selected' : '' }}>Husband</option>
                                                <option value="Wife" {{ $partner->relationship_type == 'Wife' ? 'selected' : '' }}>Wife</option>
                                                <option value="Ex-Wife" {{ $partner->relationship_type == 'Ex-Wife' ? 'selected' : '' }}>Ex-Wife</option>
                                                <option value="Defacto" {{ $partner->relationship_type == 'Defacto' ? 'selected' : '' }}>Defacto</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="partner_gender[{{ $index }}]" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $partner->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $partner->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ $partner->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Type</label>
                                            <select name="partner_company_type[{{ $index }}]">
                                                <option value="">Select Company Type</option>
                                                <option value="Accompany Member" {{ $partner->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                                                <option value="Non-Accompany Member" {{ $partner->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partner-extra-fields" style="display: {{ $partner->related_client_id ? 'none' : 'block' }};">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="partner_email[{{ $index }}]" value="{{ $partner->email }}" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="partner_first_name[{{ $index }}]" value="{{ $partner->first_name }}" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="partner_last_name[{{ $index }}]" value="{{ $partner->last_name }}" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="partner_phone[{{ $index }}]" value="{{ $partner->phone }}" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="partner_dob[{{ $index }}]" value="{{ $partner->dob ? date('d/m/Y', strtotime($partner->dob)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('partner')"><i class="fas fa-plus"></i> Add Partner</button>
                    </section>

                    <!-- Children Subsection -->
                    <section class="form-section">
                        <h3><i class="fas fa-child"></i> Children</h3>
                        <div id="childrenContainer">
                            @foreach($clientPartners->whereIn('relationship_type', ['Son', 'Daughter', 'Step Son', 'Step Daughter'])->values() as $index => $partner)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Child" onclick="removePartnerRow(this, 'children', '{{ $partner->id }}')"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="children_id[{{ $index }}]" value="{{ $partner->related_client_id }}">


                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="children_details[{{ $index }}]" class="partner-details" value="{{ $partner->details }}" placeholder="Search by Name, Email, Client ID, or Phone">
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship Type</label>
                                            <select name="children_relationship_type[{{ $index }}]" required>
                                                <option value="">Select Relationship</option>
                                                <option value="Son" {{ $partner->relationship_type == 'Son' ? 'selected' : '' }}>Son</option>
                                                <option value="Daughter" {{ $partner->relationship_type == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                                <option value="Step Son" {{ $partner->relationship_type == 'Step Son' ? 'selected' : '' }}>Step Son</option>
                                                <option value="Step Daughter" {{ $partner->relationship_type == 'Step Daughter' ? 'selected' : '' }}>Step Daughter</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="children_gender[{{ $index }}]" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $partner->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $partner->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ $partner->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Type</label>
                                            <select name="children_company_type[{{ $index }}]">
                                                <option value="">Select Company Type</option>
                                                <option value="Accompany Member" {{ $partner->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                                                <option value="Non-Accompany Member" {{ $partner->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partner-extra-fields" style="display: {{ $partner->related_client_id ? 'none' : 'block' }};">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="children_email[{{ $index }}]" value="{{ $partner->email }}" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="children_first_name[{{ $index }}]" value="{{ $partner->first_name }}" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="children_last_name[{{ $index }}]" value="{{ $partner->last_name }}" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="children_phone[{{ $index }}]" value="{{ $partner->phone }}" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="children_dob[{{ $index }}]" value="{{ $partner->dob ? date('d/m/Y', strtotime($partner->dob)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('children')"><i class="fas fa-plus"></i> Add Child</button>
                    </section>

                    <!-- Parent Subsection -->
                    <section class="form-section">
                        <h3><i class="fas fa-users"></i> Parents</h3>
                        <div id="parentContainer">
                            @foreach($clientPartners->whereIn('relationship_type', ['Father', 'Mother', 'Step Father', 'Step Mother'])->values() as $index => $partner)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Parent" onclick="removePartnerRow(this, 'parent', '{{ $partner->id }}')"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="parent_id[{{ $index }}]" value="{{ $partner->related_client_id }}">

                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="parent_details[{{ $index }}]" class="partner-details" value="{{ $partner->details }}" placeholder="Search by Name, Email, Client ID, or Phone">
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship Type</label>
                                            <select name="parent_relationship_type[{{ $index }}]" required>
                                                <option value="">Select Relationship</option>
                                                <option value="Father" {{ $partner->relationship_type == 'Father' ? 'selected' : '' }}>Father</option>
                                                <option value="Mother" {{ $partner->relationship_type == 'Mother' ? 'selected' : '' }}>Mother</option>
                                                <option value="Step Father" {{ $partner->relationship_type == 'Step Father' ? 'selected' : '' }}>Step Father</option>
                                                <option value="Step Mother" {{ $partner->relationship_type == 'Step Mother' ? 'selected' : '' }}>Step Mother</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="parent_gender[{{ $index }}]" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $partner->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $partner->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ $partner->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Type</label>
                                            <select name="parent_company_type[{{ $index }}]">
                                                <option value="">Select Company Type</option>
                                                <option value="Accompany Member" {{ $partner->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                                                <option value="Non-Accompany Member" {{ $partner->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partner-extra-fields" style="display: {{ $partner->related_client_id ? 'none' : 'block' }};">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="parent_email[{{ $index }}]" value="{{ $partner->email }}" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="parent_first_name[{{ $index }}]" value="{{ $partner->first_name }}" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="parent_last_name[{{ $index }}]" value="{{ $partner->last_name }}" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="parent_phone[{{ $index }}]" value="{{ $partner->phone }}" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="parent_dob[{{ $index }}]" value="{{ $partner->dob ? date('d/m/Y', strtotime($partner->dob)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('parent')"><i class="fas fa-plus"></i> Add Parent</button>
                    </section>

                    <!-- Siblings Subsection -->
                    <section class="form-section">
                        <h3><i class="fas fa-user-friends"></i> Siblings</h3>
                        <div id="siblingsContainer">
                            @foreach($clientPartners->whereIn('relationship_type', ['Brother', 'Sister', 'Step Brother', 'Step Sister'])->values() as $index => $partner)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Sibling" onclick="removePartnerRow(this, 'siblings', '{{ $partner->id }}')"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="siblings_id[{{ $index }}]" value="{{ $partner->related_client_id }}">

                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="siblings_details[{{ $index }}]" class="partner-details" value="{{ $partner->details }}" placeholder="Search by Name, Email, Client ID, or Phone">
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship Type</label>
                                            <select name="siblings_relationship_type[{{ $index }}]" required>
                                                <option value="">Select Relationship</option>
                                                <option value="Brother" {{ $partner->relationship_type == 'Brother' ? 'selected' : '' }}>Brother</option>
                                                <option value="Sister" {{ $partner->relationship_type == 'Sister' ? 'selected' : '' }}>Sister</option>
                                                <option value="Step Brother" {{ $partner->relationship_type == 'Step Brother' ? 'selected' : '' }}>Step Brother</option>
                                                <option value="Step Sister" {{ $partner->relationship_type == 'Step Sister' ? 'selected' : '' }}>Step Sister</option>
                                            </select>
                                            @error('siblings_relationship_type.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="siblings_gender[{{ $index }}]" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $partner->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $partner->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ $partner->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('siblings_gender.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Company Type</label>
                                            <select name="siblings_company_type[{{ $index }}]">
                                                <option value="">Select Company Type</option>
                                                <option value="Accompany Member" {{ $partner->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                                                <option value="Non-Accompany Member" {{ $partner->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partner-extra-fields" style="display: {{ $partner->related_client_id ? 'none' : 'block' }};">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="siblings_email[{{ $index }}]" value="{{ $partner->email }}" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="siblings_first_name[{{ $index }}]" value="{{ $partner->first_name }}" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="siblings_last_name[{{ $index }}]" value="{{ $partner->last_name }}" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="siblings_phone[{{ $index }}]" value="{{ $partner->phone }}" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="siblings_dob[{{ $index }}]" value="{{ $partner->dob ? date('d/m/Y', strtotime($partner->dob)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('siblings')"><i class="fas fa-plus"></i> Add Sibling</button>
                    </section>

                    <!-- Others Subsection -->
                    <section class="form-section">
                        <h3><i class="fas fa-users-cog"></i> Others</h3>
                        <div id="othersContainer">
                            @foreach($clientPartners->whereIn('relationship_type', ['Cousin', 'Friend', 'Uncle', 'Aunt', 'Grandchild', 'Granddaughter', 'Grandparent', 'Niece', 'Nephew', 'Grandfather'])->values() as $index => $partner)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Other" onclick="removePartnerRow(this, 'others', '{{ $partner->id }}')"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="others_id[{{ $index }}]" value="{{ $partner->related_client_id }}">

                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="others_details[{{ $index }}]" class="partner-details" value="{{ $partner->details }}" placeholder="Search by Name, Email, Client ID, or Phone">
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship Type</label>
                                            <select name="others_relationship_type[{{ $index }}]" required>
                                                <option value="">Select Relationship</option>
                                                <option value="Cousin" {{ $partner->relationship_type == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                                                <option value="Friend" {{ $partner->relationship_type == 'Friend' ? 'selected' : '' }}>Friend</option>
                                                <option value="Uncle" {{ $partner->relationship_type == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                                <option value="Aunt" {{ $partner->relationship_type == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                                <option value="Grandchild" {{ $partner->relationship_type == 'Grandchild' ? 'selected' : '' }}>Grandchild</option>
                                                <option value="Granddaughter" {{ $partner->relationship_type == 'Granddaughter' ? 'selected' : '' }}>Granddaughter</option>
                                                <option value="Grandparent" {{ $partner->relationship_type == 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                                                <option value="Niece" {{ $partner->relationship_type == 'Niece' ? 'selected' : '' }}>Niece</option>
                                                <option value="Nephew" {{ $partner->relationship_type == 'Nephew' ? 'selected' : '' }}>Nephew</option>
                                                <option value="Grandfather" {{ $partner->relationship_type == 'Grandfather' ? 'selected' : '' }}>Grandfather</option>
                                            </select>
                                            @error('others_relationship_type.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="others_gender[{{ $index }}]" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $partner->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $partner->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ $partner->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('others_gender.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Company Type</label>
                                            <select name="others_company_type[{{ $index }}]">
                                                <option value="">Select Company Type</option>
                                                <option value="Accompany Member" {{ $partner->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                                                <option value="Non-Accompany Member" {{ $partner->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partner-extra-fields" style="display: {{ $partner->related_client_id ? 'none' : 'block' }};">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="others_email[{{ $index }}]" value="{{ $partner->email }}" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="others_first_name[{{ $index }}]" value="{{ $partner->first_name }}" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="others_last_name[{{ $index }}]" value="{{ $partner->last_name }}" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="others_phone[{{ $index }}]" value="{{ $partner->phone }}" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="others_dob[{{ $index }}]" value="{{ $partner->dob ? date('d/m/Y', strtotime($partner->dob)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addPartnerRow('others')"><i class="fas fa-plus"></i> Add Other</button>
                    </section>
                </div>

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

                <!-- New EOI Reference Tab -->
                <div id="eoiReferenceTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-file-alt"></i> EOI Reference Details</h3>
                        <div id="eoiReferencesContainer">
                            @foreach($clientEoiReferences ?? [] as $index => $eoi)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove EOI Reference" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="eoi_id[{{ $index }}]" value="{{ $eoi->id ?? '' }}">
                                    <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                        <div class="form-group">
                                            <label>EOI Number</label>
                                            <input type="text" name="EOI_number[{{ $index }}]" value="{{ $eoi->EOI_number ?? '' }}" placeholder="Enter EOI Number">
                                            @error('EOI_number.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Subclass</label>
                                            <input type="text" name="EOI_subclass[{{ $index }}]" value="{{ $eoi->EOI_subclass ?? '' }}" placeholder="Enter Subclass">
                                            @error('EOI_subclass.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Occupation</label>
                                            <input type="text" name="EOI_occupation[{{ $index }}]" value="{{ $eoi->EOI_occupation ?? '' }}" placeholder="Enter Occupation">
                                            @error('EOI_occupation.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Point</label>
                                            <input type="text" name="EOI_point[{{ $index }}]" value="{{ $eoi->EOI_point ?? '' }}" placeholder="Enter Point">
                                            @error('EOI_point.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>State</label>
                                            <input type="text" name="EOI_state[{{ $index }}]" value="{{ $eoi->EOI_state ?? '' }}" placeholder="Enter State">
                                            @error('EOI_state.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Submission Date</label>
                                            <input type="text" name="EOI_submission_date[{{ $index }}]" value="{{ $eoi->EOI_submission_date ? date('d/m/Y', strtotime($eoi->EOI_submission_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            @error('EOI_submission_date.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>ROI</label>
                                            <input type="text" name="EOI_ROI[{{ $index }}]" value="{{ $eoi->EOI_ROI ?? '' }}" placeholder="Enter ROI">
                                            @error('EOI_ROI.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <input type="password" name="EOI_password[{{ $index }}]" value="{{ $eoi->EOI_password ?? '' }}" placeholder="Enter Password" class="eoi-password-input" data-index="{{ $index }}">
                                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" data-index="{{ $index }}" title="Show/Hide Password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('EOI_password.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addEoiReference()"><i class="fas fa-plus"></i> Add EOI Reference</button>
                    </section>
                </div>

                <!-- Add new Summary tab content -->
                <div id="summaryTab" class="tab-content">
                    <div class="summary-container">
                        <!-- Personal Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">Personal</h3>
                            <div class="summary-content">
                                <div class="summary-row">
                                    <span class="summary-label">First Name:</span>
                                    <span class="summary-value">{{ $fetchedData->first_name ?: '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Last Name:</span>
                                    <span class="summary-value">{{ $fetchedData->last_name ?: '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Client ID:</span>
                                    <span class="summary-value">{{ $fetchedData->client_id ?: '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">DOB:</span>
                                    <span class="summary-value">{{ $fetchedData->dob ? date('d.m.Y', strtotime($fetchedData->dob)) : '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Gender:</span>
                                    <span class="summary-value">{{ $fetchedData->gender ?: '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Marital Status:</span>
                                    <span class="summary-value">{{ $fetchedData->martial_status ?: '--' }}</span>
                                </div>
                                @foreach($clientContacts as $index => $contact)
                                <div class="summary-row">
                                    <span class="summary-label">Contact Number ({{ $contact->contact_type }}):</span>
                                    <span class="summary-value">{{ $contact->country_code }} {{ $contact->phone }}</span>
                                </div>
                                @endforeach
                                @foreach($emails as $index => $email)
                                <div class="summary-row">
                                    <span class="summary-label">Email Address ({{ $email->email_type }}):</span>
                                    <span class="summary-value email-link">{{ $email->email }}</span>
                                </div>
                                @endforeach
                                @foreach($clientContacts->where('contact_type', 'Emergency') as $index => $emergency)
                                <div class="summary-row">
                                    <span class="summary-label">Emergency Contact ({{ $emergency->contact_type }}):</span>
                                    <span class="summary-value">{{ $emergency->country_code }} {{ $emergency->phone }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Visa Passport and Citizenship Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">Visa Passport and Citizenship</h3>
                            <div class="summary-content">
                                <div class="summary-row">
                                    <span class="summary-label">Country of Passport:</span>
                                    <span class="summary-value">{{ $fetchedData->country_passport ?: '--' }}</span>
                                </div>
                                @foreach($clientPassports as $index => $passport)
                                <div class="summary-row">
                                    <span class="summary-label">Passport Number:</span>
                                    <span class="summary-value">{{ $passport->passport ?: '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Passport Issue Date:</span>
                                    <span class="summary-value">{{ $passport->passport_issue_date ? date('d.m.Y', strtotime($passport->passport_issue_date)) : '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Passport Expiry Date:</span>
                                    <span class="summary-value">{{ $passport->passport_expiry_date ? date('d.m.Y', strtotime($passport->passport_expiry_date)) : '--' }}</span>
                                </div>
                                @endforeach
                                @foreach($visaCountries as $index => $visa)
                                <div class="summary-row">
                                    <span class="summary-label">Visa Type:</span>
                                    <span class="summary-value">
                                        @php
                                            if($visa->visa_type) {
                                                $matter = \App\Models\Matter::select('title', 'nick_name')->where('id', $visa->visa_type)->first();
                                                if($matter) {
                                                    echo $matter->title . ' (' . $matter->nick_name . ')';
                                                } else {
                                                    echo '--';
                                                }
                                            } else {
                                                echo '--';
                                            }
                                        @endphp
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Visa Grant Date:</span>
                                    <span class="summary-value">{{ $visa->visa_grant_date ? date('d.m.Y', strtotime($visa->visa_grant_date)) : '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Visa Expiry Date:</span>
                                    <span class="summary-value">{{ $visa->visa_expiry_date ? date('d.m.Y', strtotime($visa->visa_expiry_date)) : '--' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Visa Description:</span>
                                    <span class="summary-value">{{ $visa->visa_description ?: '--' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Address and Travel Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">Address and Travel</h3>
                            <div class="summary-content">
                                <div class="summary-subsection">
                                    <h4 class="summary-subsection-header">Current Address</h4>
                                    @foreach($clientAddresses->sortByDesc('id') as $index => $address)
                                    <div class="summary-row">
                                        <span class="summary-label">Address:</span>
                                        <span class="summary-value">{{ $address->address ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Post Code:</span>
                                        <span class="summary-value">{{ $address->zip ?: '--' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="summary-subsection">
                                    <h4 class="summary-subsection-header">Travel (in last 10 years)</h4>
                                    @foreach($clientTravels as $index => $travel)
                                    <div class="summary-row">
                                        <span class="summary-label">Country Visited:</span>
                                        <span class="summary-value">{{ $travel->travel_country_visited ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Travel Period:</span>
                                        <span class="summary-value">
                                            {{ $travel->travel_arrival_date ? date('d.m.Y', strtotime($travel->travel_arrival_date)) : '--' }} - 
                                            {{ $travel->travel_departure_date ? date('d.m.Y', strtotime($travel->travel_departure_date)) : '--' }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Skills and Education Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">Skills and Education</h3>
                            <div class="summary-content">
                                @if($qualifications->count() > 0)
                                <div class="summary-subsection">
                                    <h4 class="summary-subsection-header">Qualifications</h4>
                                    @foreach($qualifications as $index => $qualification)
                                    <div class="summary-row">
                                        <span class="summary-label">Qualification Level:</span>
                                        <span class="summary-value">{{ $qualification->level ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Qualification Name:</span>
                                        <span class="summary-value">{{ $qualification->name ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">College/Institution:</span>
                                        <span class="summary-value">{{ $qualification->qual_college_name ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Campus:</span>
                                        <span class="summary-value">{{ $qualification->qual_campus ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Country:</span>
                                        <span class="summary-value">{{ $qualification->country ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">State:</span>
                                        <span class="summary-value">{{ $qualification->qual_state ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Start Date:</span>
                                        <span class="summary-value">{{ $qualification->start_date ? date('d.m.Y', strtotime($qualification->start_date)) : '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Finish Date:</span>
                                        <span class="summary-value">{{ $qualification->finish_date ? date('d.m.Y', strtotime($qualification->finish_date)) : '--' }}</span>
                                    </div>
                                    @if($qualification->relevant_qualification)
                                    <div class="summary-row">
                                        <span class="summary-label">Relevant Qualification:</span>
                                        <span class="summary-value">{{ $qualification->relevant_qualification ?: '--' }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @endif

                                @if($experiences->count() > 0)
                                <div class="summary-subsection">
                                    <h4 class="summary-subsection-header">Work Experience</h4>
                                    @foreach($experiences as $index => $experience)
                                    <div class="summary-row">
                                        <span class="summary-label">Job Title:</span>
                                        <span class="summary-value">{{ $experience->job_title ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Company:</span>
                                        <span class="summary-value">{{ $experience->job_company ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Country:</span>
                                        <span class="summary-value">{{ $experience->job_country ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Start Date:</span>
                                        <span class="summary-value">{{ $experience->job_start_date ? date('d.m.Y', strtotime($experience->job_start_date)) : '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Finish Date:</span>
                                        <span class="summary-value">{{ $experience->job_finish_date ? date('d.m.Y', strtotime($experience->job_finish_date)) : '--' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($testScores->count() > 0)
                                <div class="summary-subsection">
                                    <h4 class="summary-subsection-header">English Test Scores</h4>
                                    @foreach($testScores as $index => $test)
                                    <div class="summary-row">
                                        <span class="summary-label">Test Type:</span>
                                        <span class="summary-value">{{ $test->test_type ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Listening:</span>
                                        <span class="summary-value">{{ $test->listening ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Reading:</span>
                                        <span class="summary-value">{{ $test->reading ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Writing:</span>
                                        <span class="summary-value">{{ $test->writing ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Speaking:</span>
                                        <span class="summary-value">{{ $test->speaking ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Overall Score:</span>
                                        <span class="summary-value">{{ $test->overall_score ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Test Date:</span>
                                        <span class="summary-value">{{ $test->test_date ? date('d.m.Y', strtotime($test->test_date)) : '--' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($fetchedData->naati_test || $fetchedData->py_test)
                                <div class="summary-subsection">
                                    <h4 class="summary-subsection-header">Additional Skills</h4>
                                    @if($fetchedData->naati_test)
                                    <div class="summary-row">
                                        <span class="summary-label">NAATI CCL Test:</span>
                                        <span class="summary-value">{{ $fetchedData->naati_test ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">NAATI Date:</span>
                                        <span class="summary-value">{{ $fetchedData->naati_date ? date('d.m.Y', strtotime($fetchedData->naati_date)) : '--' }}</span>
                                    </div>
                                    @endif
                                    @if($fetchedData->py_test)
                                    <div class="summary-row">
                                        <span class="summary-label">Professional Year (PY):</span>
                                        <span class="summary-value">{{ $fetchedData->py_test ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">PY Date:</span>
                                        <span class="summary-value">{{ $fetchedData->py_date ? date('d.m.Y', strtotime($fetchedData->py_date)) : '--' }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Other Information Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">Other Information</h3>
                            <div class="summary-content">
                                <div class="summary-row">
                                    <span class="summary-label">Criminal, Refusal Etc. (Which are mentioned):</span>
                                    <span class="summary-value">
                                        @foreach($clientCharacters->where('type_of_character', 1) as $character)
                                            {{ $character->character_detail ?: '--' }}
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Family History Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">Family History</h3>
                            <div class="summary-content">
                                <div class="summary-row">
                                    <span class="summary-label">Family Details:</span>
                                    <span class="summary-value">
                                        @foreach($clientPartners as $partner)
                                            {{ $partner->relationship_type ?: '--' }} - {{ $partner->first_name }} {{ $partner->last_name }}
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- EOI Reference Section -->
                        <div class="summary-section">
                            <h3 class="summary-section-header">EOI Reference</h3>
                            <div class="summary-content">
                                @php
                                    $clientEoiReferences = \App\Models\ClientEoiReference::where('client_id', $fetchedData->id)->orderBy('id', 'desc')->get();
                                @endphp
                                @if($clientEoiReferences->count() > 0)
                                    @foreach($clientEoiReferences as $eoi)
                                    <div class="summary-row">
                                        <span class="summary-label">EOI Number:</span>
                                        <span class="summary-value">{{ $eoi->EOI_number ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">EOI Subclass:</span>
                                        <span class="summary-value">{{ $eoi->EOI_subclass ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">EOI Occupation:</span>
                                        <span class="summary-value">{{ $eoi->EOI_occupation ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">EOI Points:</span>
                                        <span class="summary-value">{{ $eoi->EOI_point ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">EOI State:</span>
                                        <span class="summary-value">{{ $eoi->EOI_state ?: '--' }}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">EOI Submission Date:</span>
                                        <span class="summary-value">{{ $eoi->EOI_submission_date ? date('d.m.Y', strtotime($eoi->EOI_submission_date)) : '--' }}</span>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="summary-row">
                                        <span class="summary-label">EOI Reference:</span>
                                        <span class="summary-value">--</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Notes Field -->
                        <div class="summary-section">
                            <div class="summary-content">
                                <div class="form-group">
                                    <label>Additional Notes:</label>
                                    <textarea name="additional_notes" id="additional_notes" rows="4" class="form-control"></textarea>
                                </div>
                                <!-- Send to n8n Button -->
                                <div class="form-group">
                                    <button id="sendToN8n" style="margin-top: 20px;" class="btn btn-primary">Send to n8n</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <?php
    if($fetchedData->related_files != ''){
        $exploderel = explode(',', $fetchedData->related_files);
        foreach($exploderel AS $EXP){
            $relatedclients = \App\Models\Admin::where('id', $EXP)->first();
        ?>
        <input type="hidden" class="relatedfile" data-email="<?php echo $relatedclients->email; ?>" data-name="<?php echo $relatedclients->first_name.' '.$relatedclients->last_name; ?>" data-id="<?php echo $relatedclients->id; ?>">
        <?php
        }
    }
    ?>

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
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
            }

            /* Responsive design for family members */
            @media (max-width: 1200px) {
                #familyTab .content-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                #familyTab .content-grid {
                    grid-template-columns: 1fr;
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

            /* Password toggle button styling */
            .toggle-password {
                padding: 8px 12px;
                border: 1px solid var(--input-border-color);
                background: #f8f9fa;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .toggle-password:hover {
                background: #e9ecef;
                border-color: var(--input-focus-border-color);
            }

            .toggle-password:focus {
                outline: none;
                box-shadow: 0 0 5px rgba(0, 187, 240, 0.3);
            }

            /* Password input container */
            .form-group .eoi-password-input {
                flex: 1;
            }

            /* Summary Tab Styles */
            .summary-container {
                max-width: 100%;
                margin: 0 auto;
            }

            .summary-section {
                margin-bottom: 30px;
                background: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .summary-section-header {
                background: #005792;
                color: white;
                padding: 15px 20px;
                margin: 0;
                font-size: 1.2em;
                font-weight: 600;
                border-bottom: 1px solid #004670;
            }

            .summary-content {
                padding: 20px;
            }

            .summary-subsection {
                margin-bottom: 20px;
            }

            .summary-subsection:last-child {
                margin-bottom: 0;
            }

            .summary-subsection-header {
                color: #005792;
                font-size: 1.1em;
                font-weight: 600;
                margin: 0 0 15px 0;
                padding-bottom: 5px;
                border-bottom: 2px solid #e0e0e0;
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .summary-row:last-child {
                border-bottom: none;
            }

            .summary-label {
                font-weight: 500;
                color: #333333;
                min-width: 200px;
                flex-shrink: 0;
            }

            .summary-value {
                color: #666666;
                text-align: right;
                flex: 1;
                margin-left: 20px;
            }

            .email-link {
                color: #005792;
                text-decoration: underline;
                cursor: pointer;
            }

            .email-link:hover {
                color: #004670;
            }

            /* Responsive design for summary */
            @media (max-width: 768px) {
                .summary-row {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .summary-label {
                    min-width: auto;
                    margin-bottom: 5px;
                }

                .summary-value {
                    text-align: left;
                    margin-left: 0;
                }
            }
    </style>
    @endpush

    @push('scripts')
    <script>
        // ===== ULTRA-ROBUST TAB FUNCTIONALITY - IMMEDIATE DEFINITION =====
        
        // Define openTab function IMMEDIATELY in global scope
        window.openTab = function(evt, tabName) {
            try {
                // Prevent default behavior
                if (evt && evt.preventDefault) {
                    evt.preventDefault();
                }
                
                // Hide all tab content
                var tabcontent = document.getElementsByClassName("tab-content");
                for (var i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                
                // Remove active class from all tab buttons
                var tablinks = document.getElementsByClassName("tab-button");
                for (var i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                
                // Show the selected tab content
                var targetTab = document.getElementById(tabName);
                if (targetTab) {
                    targetTab.style.display = "block";
                } else {
                    console.error('Tab content not found:', tabName);
                }
                
                // Add active class to the clicked button
                if (evt && evt.currentTarget) {
                    evt.currentTarget.className += " active";
                }
                
                // Fallback: if evt.currentTarget is not available, find the button by onclick attribute
                if (!evt || !evt.currentTarget) {
                    var buttons = document.querySelectorAll('.tab-button');
                    for (var i = 0; i < buttons.length; i++) {
                        if (buttons[i].getAttribute('onclick') && buttons[i].getAttribute('onclick').includes(tabName)) {
                            buttons[i].className += " active";
                            break;
                        }
                    }
                }
                
                console.log('Tab switched to:', tabName);
                
            } catch (error) {
                console.error('Error in openTab function:', error);
                // Fallback: try to show the tab content directly
                try {
                    var fallbackTab = document.getElementById(tabName);
                    if (fallbackTab) {
                        // Hide all tabs first
                        var allTabs = document.getElementsByClassName("tab-content");
                        for (var i = 0; i < allTabs.length; i++) {
                            allTabs[i].style.display = "none";
                        }
                        // Show target tab
                        fallbackTab.style.display = "block";
                    }
                } catch (fallbackError) {
                    console.error('Fallback also failed:', fallbackError);
                }
            }
        }
        
        // Ensure openTab is globally available
        window.openTab = openTab;
        
        // Initialize tab functionality when DOM is ready
        function initializeTabs() {
            try {
                // Set up event listeners for tab buttons as backup
                var tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(function(button) {
                    // Remove existing onclick to prevent conflicts
                    var onclickAttr = button.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes('openTab')) {
                        // Extract tab name from onclick
                        var match = onclickAttr.match(/openTab\(event,\s*['"]([^'"]+)['"]\)/);
                        if (match) {
                            var tabName = match[1];
                            // Add event listener as backup
                            button.addEventListener('click', function(e) {
                                openTab(e, tabName);
                            });
                        }
                    }
                });
                
                // Ensure first tab is active by default
                var firstTab = document.querySelector('.tab-button');
                var firstTabContent = document.getElementById('personalTab');
                if (firstTab && firstTabContent) {
                    firstTab.classList.add('active');
                    firstTabContent.style.display = 'block';
                }
                
                console.log('Tab functionality initialized successfully');
                
            } catch (error) {
                console.error('Error initializing tabs:', error);
            }
        }
        
        // Call initialization when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeTabs);
        } else {
            initializeTabs();
        }
        
        // ===== END TAB FUNCTIONALITY =====
        
        // Initialize arrays to track IDs of records marked for deletion
        let phoneNumbersToDelete = [];
        let emailsToDelete = [];

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

        // Modified removePartnerRow to handle different types
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

        // Update autocomplete to handle all family member types
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

        // Close autocomplete when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.partner-details').length && !$(e.target).closest('.autocomplete-items').length) {
                $('.autocomplete-items').empty();
            }
        });


        // Function to add a new EOI Reference row
        function addEoiReference() {
            const container = document.getElementById('eoiReferencesContainer');
            const index = container.children.length;

            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove EOI Reference" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                    <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                        <div class="form-group">
                            <label>EOI Number</label>
                            <input type="text" name="EOI_number[${index}]" placeholder="Enter EOI Number">
                        </div>
                        <div class="form-group">
                            <label>Subclass</label>
                            <input type="text" name="EOI_subclass[${index}]" placeholder="Enter Subclass">
                        </div>
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" name="EOI_occupation[${index}]" placeholder="Enter Occupation">
                        </div>
                        <div class="form-group">
                            <label>Point</label>
                            <input type="text" name="EOI_point[${index}]" placeholder="Enter Point">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="EOI_state[${index}]" placeholder="Enter State">
                        </div>
                        <div class="form-group">
                            <label>Submission Date</label>
                            <input type="text" name="EOI_submission_date[${index}]" placeholder="dd/mm/yyyy" class="date-picker">
                        </div>
                        <div class="form-group">
                            <label>ROI</label>
                            <input type="text" name="EOI_ROI[${index}]" placeholder="Enter ROI">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="password" name="EOI_password[${index}]" placeholder="Enter Password" class="eoi-password-input" data-index="${index}">
                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" data-index="${index}" title="Show/Hide Password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Reinitialize datepickers for the newly added field
            initializeDatepickers();
        }

        // Handle removal with confirmation
        document.getElementById('eoiReferencesContainer').addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const section = e.target.closest('.repeatable-section');
                const eoiIdInput = section.querySelector('input[name^="eoi_id"]');
                const confirmDelete = confirm('Are you sure you want to delete this EOI Reference?');

                if (confirmDelete) {
                    if (eoiIdInput) {
                        const eoiId = eoiIdInput.value;
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_eoi_ids[]';
                        hiddenInput.value = eoiId;
                        document.getElementById('editClientForm').appendChild(hiddenInput);
                    }
                    section.remove();
                }
            }
        });

        // Toggle Visa Details and Visa Expiry Verified based on passport country
        function toggleVisaDetails() {
            const passportCountrySelector = document.getElementById('passportCountry');
            const visaDetailsSection = document.getElementById('visaDetailsSection');
            const addVisaButton = document.querySelector('button[onclick="addVisaDetail()"]');
            const visaExpiryVerifiedContainer = document.getElementById('visaExpiryVerifiedContainer');

            const isAustralia = passportCountrySelector.value === 'Australia';
            //console.log('isAustralia='+isAustralia);

            if (isAustralia) { //console.log('ifff');
                visaDetailsSection.style.display = 'none';
                addVisaButton.style.display = 'none';
                visaExpiryVerifiedContainer.style.display = 'none';
                // Remove required validation for visa fields
                /*document.querySelectorAll('.visa-type-field, .visa-expiry-field, .visa-description-field').forEach(field => {
                    field.removeAttribute('required');
                });*/
            } else {  //console.log('elseee');
                visaDetailsSection.style.display = 'block';
                addVisaButton.style.display = 'block';
                visaExpiryVerifiedContainer.style.display = 'flex';
                // Add required validation for visa fields
                /*document.querySelectorAll('.visa-type-field').forEach(field => {
                    field.setAttribute('required', 'required');
                });*/
            }
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', function() { //console.log('DOMContentLoaded');
            toggleVisaDetails();
        });

        // Run on passport country change
        document.getElementById('passportCountry').addEventListener('change', function() {
            toggleVisaDetails();
        });


        // Add Passport Detail
        function addPassportDetail() {
            const container = document.getElementById('passportDetailsContainer');
            const index = container.children.length;

            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Passport" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
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

            // Reinitialize datepickers for the newly added fields
            initializeDatepickers();
        }

        // Add Travel Detail
        async function addTravelDetail() {
            const container = document.getElementById('travelDetailsContainer');
            const index = container.children.length;

            // Fetch countries
            const countries = await fetchCountries();

            // Build the options for the country dropdown
            let countryOptionsHtml = '<option value="">Select Country</option>';
            countries.forEach(country => {
                countryOptionsHtml += `<option value="${country}">${country}</option>`;
            });

            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Travel" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
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

            // Reinitialize datepickers for the newly added fields
            initializeDatepickers();
        }

        // Tab functionality - REMOVED DUPLICATE: Using the robust version defined at the top of the script

        // Function to calculate age from date of birth (expects dd/mm/yyyy format)
        function calculateAge(dob) {
            if (!dob || !/^\d{2}\/\d{2}\/\d{4}$/.test(dob)) return '';

            try {
                const [day, month, year] = dob.split('/').map(Number);
                const dobDate = new Date(year, month - 1, day);
                if (isNaN(dobDate.getTime())) return ''; // Invalid date

                const today = new Date();
                let years = today.getFullYear() - dobDate.getFullYear();
                let months = today.getMonth() - dobDate.getMonth();

                if (months < 0) {
                    years--;
                    months += 12;
                }

                if (today.getDate() < dobDate.getDate()) {
                    months--;
                    if (months < 0) {
                        years--;
                        months += 12;
                    }
                }

                return years + ' years ' + months + ' months';
            } catch (e) {
                return '';
            }
        }

        // Initialize age on page load and set up datepicker for DOB
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('dob');
            const ageInput = document.getElementById('age');

            // Initialize age if DOB exists
            if (dobInput.value) {
                ageInput.value = calculateAge(dobInput.value);
            }

            // Function to update age
            const updateAge = function() {
                const dobValue = dobInput.value;
                ageInput.value = calculateAge(dobValue);
            };

            // Handle manual input changes (e.g., typing or pasting)
            dobInput.addEventListener('input', updateAge);

            // Ensure datepicker is initialized and handle datepicker changes
            $(dobInput).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
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
                // Update the input value and calculate age when a date is selected
                dobInput.value = picker.startDate.format('DD/MM/YYYY');
                updateAge();
            }).on('change', updateAge); // Fallback for any direct changes
        });

        // Add Phone Number (Updated to exclude verification slider in repeatable section)
        function addPhoneNumber() {
            const container = document.getElementById('phoneNumbersContainer');
            const index = container.children.length;
            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Phone" onclick="this.parentElement.remove(); validatePersonalPhoneNumbers();"><i class="fas fa-times-circle"></i></button>
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
                                <input type="tel" name="phone[${index}]" placeholder="Enter Phone Number" style="width: 230px;" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $(".telephone").intlTelInput();
            validatePersonalPhoneNumbers();
        }

        // Function to validate that "Personal" phone numbers are unique
        function validatePersonalPhoneNumbers() {
            const container = document.getElementById('phoneNumbersContainer');
            const sections = container.getElementsByClassName('repeatable-section');
            const personalPhones = {};

            // Clear previous error messages
            Array.from(sections).forEach(section => {
                const errorSpan = section.querySelector('.text-danger');
                if (errorSpan) errorSpan.remove();
            });

            // Check for duplicate "Personal" phone numbers
            Array.from(sections).forEach((section, index) => {
                const type = section.querySelector('.contact-type-selector').value;
                const countryCode = section.querySelector('.country-code-input').value;
                const phone = section.querySelector('.phone-number-input').value;
                const fullPhone = countryCode + phone;

                if (type === 'Personal' && phone) {
                    if (personalPhones[fullPhone]) {
                        // Duplicate found
                        const errorMessage = `<span class="text-danger">Personal phone number ${fullPhone} is already used in another entry.</span>`;
                        section.querySelector('.input-group').insertAdjacentHTML('afterend', errorMessage);
                        // Disable the submit button
                        document.querySelector('button[type="submit"]').disabled = true;
                    } else {
                        personalPhones[fullPhone] = true;
                    }
                }
            });

            // Re-enable the submit button if no duplicates are found
            if (!Object.keys(personalPhones).some(phone => personalPhones[phone] === true && Object.keys(personalPhones).filter(p => p === phone).length > 1)) {
                document.querySelector('button[type="submit"]').disabled = false;
            }
        }


        // Add Email Address (Updated to exclude verification slider in repeatable section)
        function addEmailAddress() {
            const container = document.getElementById('emailAddressesContainer');
            const index = container.children.length;
            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Email" onclick="this.parentElement.remove(); validatePersonalEmailTypes();"><i class="fas fa-times-circle"></i></button>
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

        // Function to validate that there is at most one "Personal" type for emails
        function validatePersonalEmailTypes() {
            const container = document.getElementById('emailAddressesContainer');
            const sections = container.getElementsByClassName('repeatable-section');
            let personalCount = 0;

            // Clear previous error messages
            Array.from(sections).forEach(section => {
                const errorSpan = section.querySelector('.text-danger-email-personal');
                if (errorSpan) errorSpan.remove();
            });

            // Count "Personal" types
            Array.from(sections).forEach((section, index) => {
                const type = section.querySelector('.email-type-selector').value;
                if (type === 'Personal') {
                    personalCount++;
                    if (personalCount > 1) {
                        // Display error message
                        const errorMessage = `<span class="text-danger text-danger-email-personal">Only one email address can be of type Personal.</span>`;
                        section.querySelector('.form-group').insertAdjacentHTML('afterend', errorMessage);
                    }
                }
            });

            // Enable or disable the submit button based on validation
            const submitButton = document.querySelector('button[type="submit"]');
            if (personalCount > 1) {
                submitButton.disabled = true;
            } else {
                submitButton.disabled = false;
            }

            return personalCount <= 1;
        }

        // Cache visa types to avoid multiple AJAX calls
        let visaTypesCache = null;

        // Function to fetch visa types via AJAX
        async function fetchVisaTypes() {
            if (visaTypesCache) {
                return visaTypesCache; // Return cached data if available
            }

            try {
                const response = await $.ajax({
                    url: '{{ route("admin.getVisaTypes") }}', // Use Laravel route
                    method: 'GET',
                    dataType: 'json',
                });
                visaTypesCache = response; // Cache the response
                return response;
            } catch (error) {
                console.error('Error fetching visa types:', error);
                return [];
            }
        }

        // Add Visa Detail
        async function addVisaDetail() {
            const container = document.getElementById('visaDetailsContainer');
            const index = container.children.length;

            // Fetch visa types
            const visaTypes = await fetchVisaTypes();

            // Build the options for the dropdown
            let optionsHtml = '<option value="">Select Visa Type</option>';
            visaTypes.forEach(visa => {
                const nickName = visa.nick_name ? ` (${visa.nick_name})` : '';
                optionsHtml += `<option value="${visa.id}">${visa.title}${nickName}</option>`;
            });

            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Visa"><i class="fas fa-times-circle"></i></button>
                    <div class="content-grid">
                        <div class="form-group">
                            <label>Visa Type / Subclass</label>
                            <select name="visas[${index}][visa_type]" class="visa-type-field">
                                ${optionsHtml}
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

            // Reinitialize datepicker for the newly added field
            initializeDatepickers();
            toggleVisaDetails();
        }

        // Initialize Datepickers for both empty and non-empty fields
        function initializeDatepickers() {
            $('.date-picker').each(function() {
                const $this = $(this);
                const currentValue = $this.val(); // Get the current value of the field

                // Initialize the datepicker regardless of whether the field is empty
                $this.daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoUpdateInput: false, // Prevent the datepicker from auto-filling the field
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
                    minDate: '01/01/1000',
                    minYear: 1000,
                    maxYear: parseInt(moment().format('YYYY')) + 50,
                    startDate: currentValue ? moment(currentValue, 'DD/MM/YYYY') : undefined, // Use existing value if present, otherwise no default date
                    endDate: undefined
                }).on('apply.daterangepicker', function(ev, picker) {
                    // On apply, set the selected date
                    $this.val(picker.startDate.format('DD/MM/YYYY'));
                }).on('cancel.daterangepicker', function(ev, picker) {
                    // On cancel, clear the field
                    $this.val('');
                });

                // If the field was empty, ensure it remains empty after initialization
                if (!currentValue) {
                    $this.val('');
                }
            });
        }

        // Initialize datepickers on page load
        $(document).ready(function() {
            // Password toggle functionality
            $(document).on('click', '.toggle-password', function() {
                const index = $(this).data('index');
                const passwordInput = $(`.eoi-password-input[data-index="${index}"]`);
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Password change confirmation
            $(document).on('focus', '.eoi-password-input', function() {
                const currentValue = $(this).val();
                const originalValue = $(this).data('original-value') || '';
                
                // Set original value if not set
                if (!$(this).data('original-value-set')) {
                    $(this).data('original-value', currentValue);
                    $(this).data('original-value-set', true);
                }
                
                // Reset confirmation flag if the current value matches the original
                if (currentValue === originalValue) {
                    $(this).data('confirmation-shown', false);
                }
            });

            // Password change confirmation - only trigger once when user starts typing
            $(document).on('keydown', '.eoi-password-input', function(e) {
                const currentValue = $(this).val();
                const originalValue = $(this).data('original-value') || '';
                const hasShownConfirmation = $(this).data('confirmation-shown') || false;
                
                // Only show confirmation if:
                // 1. There was an original password value (not empty)
                // 2. We haven't shown the confirmation yet for this session
                // 3. User is about to type (not deleting)
                if (originalValue !== '' && !hasShownConfirmation && e.key.length === 1) {
                    const confirmChange = confirm('Do you want to change the password?');
                    $(this).data('confirmation-shown', true);
                    
                    if (!confirmChange) {
                        e.preventDefault();
                        return false;
                    } else {
                        // If user confirms, allow typing to continue
                        $(this).data('original-value', ''); // Clear original value to prevent further confirmations
                    }
                }
            });
            $('#sendToN8n').on('click', function(e) {
                e.preventDefault();

                // Show loading state
                $(this).prop('disabled', true).text('Sending...');

                // Collect all summary data
                let summaryData = {
                    first_name: "{{ $fetchedData->first_name ?? '' }}",
                    last_name: "{{ $fetchedData->last_name ?? '' }}",
                    client_id: "{{ $fetchedData->client_id ?? '' }}",
                    dob: "{{ $fetchedData->dob ? date('d/m/Y', strtotime($fetchedData->dob)) : '' }}",
                    age: "{{ $fetchedData->age ?? '' }}",
                    gender: "{{ $fetchedData->gender ?? '' }}",
                    martial_status: "{{ $fetchedData->martial_status ?? '' }}",
                    country_passport: "{{ $fetchedData->country_passport ?? '' }}",
                    naati_test: "{{ $fetchedData->naati_test ? 'Yes' : 'No' }}",
                    naati_date: "{{ $fetchedData->naati_date ? date('d/m/Y', strtotime($fetchedData->naati_date)) : '' }}",
                    py_test: "{{ $fetchedData->py_test ? 'Yes' : 'No' }}",
                    py_date: "{{ $fetchedData->py_date ? date('d/m/Y', strtotime($fetchedData->py_date)) : '' }}",
                    additional_notes: $('#additional_notes').val() || ''
                };

                // Add contact information
                @foreach($clientContacts as $index => $contact)
                summaryData["contact_{{ $contact->contact_type ?? 'phone' }}_{{ $index }}"] = "{{ $contact->country_code ?? '' }} {{ $contact->phone ?? '' }}";
                @endforeach

                // Add email information
                @foreach($emails as $index => $email)
                summaryData["email_{{ $email->email_type ?? 'email' }}_{{ $index }}"] = "{{ $email->email ?? '' }}";
                @endforeach

                // Add passport information
                @foreach($clientPassports as $index => $passport)
                summaryData["passport_{{ $index }}"] = "{{ $passport->passport ?? '' }}";
                summaryData["passport_issue_date_{{ $index }}"] = "{{ $passport->passport_issue_date ? date('d/m/Y', strtotime($passport->passport_issue_date)) : '' }}";
                summaryData["passport_expiry_date_{{ $index }}"] = "{{ $passport->passport_expiry_date ? date('d/m/Y', strtotime($passport->passport_expiry_date)) : '' }}";
                @endforeach

                // Add visa information
                @foreach($visaCountries as $index => $visa)
                summaryData["visa_type_{{ $index }}"] = "{{ $visa->visa_type ?? '' }}";
                summaryData["visa_expiry_date_{{ $index }}"] = "{{ $visa->visa_expiry_date ? date('d/m/Y', strtotime($visa->visa_expiry_date)) : '' }}";
                summaryData["visa_grant_date_{{ $index }}"] = "{{ $visa->visa_grant_date ? date('d/m/Y', strtotime($visa->visa_grant_date)) : '' }}";
                summaryData["visa_description_{{ $index }}"] = "{{ $visa->visa_description ?? '' }}";
                @endforeach

                // Add address information
                @foreach($clientAddresses as $index => $address)
                summaryData["address_{{ $index }}"] = "{{ $address->address ?? '' }}";
                summaryData["zip_{{ $index }}"] = "{{ $address->zip ?? '' }}";
                summaryData["address_start_date_{{ $index }}"] = "{{ $address->start_date ? date('d/m/Y', strtotime($address->start_date)) : '' }}";
                summaryData["address_end_date_{{ $index }}"] = "{{ $address->end_date ? date('d/m/Y', strtotime($address->end_date)) : '' }}";
                @endforeach

                // Add travel information
                @foreach($clientTravels as $index => $travel)
                summaryData["travel_country_visited_{{ $index }}"] = "{{ $travel->travel_country_visited ?? '' }}";
                summaryData["travel_arrival_date_{{ $index }}"] = "{{ $travel->travel_arrival_date ? date('d/m/Y', strtotime($travel->travel_arrival_date)) : '' }}";
                summaryData["travel_departure_date_{{ $index }}"] = "{{ $travel->travel_departure_date ? date('d/m/Y', strtotime($travel->travel_departure_date)) : '' }}";
                summaryData["travel_purpose_{{ $index }}"] = "{{ $travel->travel_purpose ?? '' }}";
                @endforeach

                // Add qualification information
                @foreach($qualifications as $index => $qualification)
                summaryData["qualification_level_{{ $index }}"] = "{{ $qualification->level ?? '' }}";
                summaryData["qualification_name_{{ $index }}"] = "{{ $qualification->name ?? '' }}";
                summaryData["qualification_start_date_{{ $index }}"] = "{{ $qualification->start_date ? date('d/m/Y', strtotime($qualification->start_date)) : '' }}";
                summaryData["qualification_finish_date_{{ $index }}"] = "{{ $qualification->finish_date ? date('d/m/Y', strtotime($qualification->finish_date)) : '' }}";
                @endforeach

                // Add experience information
                @foreach($experiences as $index => $experience)
                summaryData["job_title_{{ $index }}"] = "{{ $experience->job_title ?? '' }}";
                summaryData["job_start_date_{{ $index }}"] = "{{ $experience->job_start_date ? date('d/m/Y', strtotime($experience->job_start_date)) : '' }}";
                summaryData["job_finish_date_{{ $index }}"] = "{{ $experience->job_finish_date ? date('d/m/Y', strtotime($experience->job_finish_date)) : '' }}";
                @endforeach

                // Add occupation information
                @foreach($clientOccupations as $index => $occupation)
                summaryData["nomi_occupation_{{ $index }}"] = "{{ $occupation->nomi_occupation ?? '' }}";
                summaryData["occupation_code_{{ $index }}"] = "{{ $occupation->occupation_code ?? '' }}";
                summaryData["assessment_date_{{ $index }}"] = "{{ $occupation->dates ? date('d/m/Y', strtotime($occupation->dates)) : '' }}";
                summaryData["expiry_date_{{ $index }}"] = "{{ $occupation->expiry_dates ? date('d/m/Y', strtotime($occupation->expiry_dates)) : '' }}";
                @endforeach

                // Add test scores information
                @foreach($testScores as $index => $test)
                summaryData["test_type_{{ $index }}"] = "{{ $test->test_type ?? '' }}";
                summaryData["listening_{{ $index }}"] = "{{ $test->listening ?? '' }}";
                summaryData["reading_{{ $index }}"] = "{{ $test->reading ?? '' }}";
                summaryData["writing_{{ $index }}"] = "{{ $test->writing ?? '' }}";
                summaryData["speaking_{{ $index }}"] = "{{ $test->speaking ?? '' }}";
                summaryData["overall_score_{{ $index }}"] = "{{ $test->overall_score ?? '' }}";
                summaryData["test_date_{{ $index }}"] = "{{ $test->test_date ? date('d/m/Y', strtotime($test->test_date)) : '' }}";
                @endforeach

                // Add character information
                @foreach($clientCharacters->where('type_of_character', 1) as $index => $character)
                summaryData["criminal_charge_{{ $index }}"] = "{{ $character->character_detail ?? '' }}";
                summaryData["criminal_charge_date_{{ $index }}"] = "{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}";
                @endforeach

                @foreach($clientCharacters->where('type_of_character', 2) as $index => $character)
                summaryData["military_service_{{ $index }}"] = "{{ $character->character_detail ?? '' }}";
                summaryData["military_service_date_{{ $index }}"] = "{{ $character->character_date ? date('d/m/Y', strtotime($character->character_date)) : '' }}";
                @endforeach

                // Add partner information
                @foreach($clientPartners as $index => $partner)
                summaryData["relationship_{{ $index }}"] = "{{ $partner->relationship_type ?? '' }}";
                summaryData["partner_details_{{ $index }}"] = "{{ $partner->details ?? '' }}";
                summaryData["partner_email_{{ $index }}"] = "{{ $partner->email ?? '' }}";
                summaryData["partner_first_name_{{ $index }}"] = "{{ $partner->first_name ?? '' }}";
                summaryData["partner_last_name_{{ $index }}"] = "{{ $partner->last_name ?? '' }}";
                summaryData["partner_phone_{{ $index }}"] = "{{ $partner->phone ?? '' }}";
                @endforeach

                // Get CSRF token
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                if (!csrfToken) {
                    csrfToken = $('input[name="_token"]').val();
                }

                // Send AJAX request
                $.ajax({
                    url: '{{ route('admin.send-webhook') }}',
                    method: 'POST',
                    data: summaryData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    success: function(response) {
                        alert('Data sent to n8n successfully!');
                        console.log('Success:', response);
                        $('#sendToN8n').prop('disabled', false).text('Send to n8n');
                    },
                                         error: function(xhr, status, error) {
                         console.error('Error details:', {
                             status: status,
                             error: error,
                             responseText: xhr.responseText,
                             statusText: xhr.statusText
                         });
                         
                         let errorMessage = 'Error sending data to n8n.';
                         
                         // Check for specific DNS/connection errors
                         if (error === 'Network Error' || status === 'error') {
                             errorMessage = 'Cannot connect to n8n server. The server may be down or the URL is incorrect.';
                         } else if (xhr.responseJSON && xhr.responseJSON.message) {
                             errorMessage += ' ' + xhr.responseJSON.message;
                         }
                         
                         // Add troubleshooting information
                         errorMessage += '\n\nTroubleshooting:\n';
                         errorMessage += '1. Check if n8n server is running\n';
                         errorMessage += '2. Verify the webhook URL in environment settings\n';
                         errorMessage += '3. Check network connectivity\n';
                         errorMessage += '4. Contact system administrator';
                         
                         alert(errorMessage);
                         $('#sendToN8n').prop('disabled', false).text('Send to n8n');
                     }
                });
            });

            // Clean up any existing \n in Target Visa Subclass field on page load
            $('.visa_subclass').each(function() {
                var currentValue = $(this).val();
                if (currentValue) {
                    // Remove newlines and trim whitespace
                    $(this).val(currentValue.replace(/(\r\n|\n|\r)/gm, '').trim());
                }
            });

            initializeDatepickers();

             // Verify the element exists
            if ($('#dob').length === 0) {
                console.error('DOB field with ID #dob not found in the DOM');
                return;
            }

            // Reinitialize the datepicker for #dob specifically to ensure the event fires
            $('#dob').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                autoApply: true, // Matches the existing initializeDatepickers setup
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
                minDate: '01/01/1000',
                minYear: 1000,
                maxYear: parseInt(moment().format('YYYY')) + 50
            }, function(start) {
                // Trigger the apply event manually to ensure consistency
                $('#dob').trigger('apply.daterangepicker', [{ startDate: start }]);
            });

            // Debug to confirm the event listener is attached
            //console.log('apply.daterangepicker event listener attached to #dob');

             // Autocomplete functionality (Updated)
            $(document).on('input', '.nomi_occupation', function() {
                var occupation = $(this).val();
                var $row = $(this).closest('.repeatable-section'); // Target the entire row
                var $input = $(this); // The input field
                var $autocomplete = $row.find('.autocomplete-items'); // The suggestion container

                // Clear previous suggestions
                $autocomplete.empty();

                if (occupation.length > 2) {
                    $.ajax({
                        url: '{{ route("admin.clients.updateOccupation") }}',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        method: 'POST',
                        data: { occupation: occupation },
                        success: function(response) {
                            var suggestions = response.occupations || [];
                            $autocomplete.empty();

                            if (suggestions.length > 0) {
                                suggestions.forEach(function(suggestion) {
                                    // Clean up visa_subclass by removing newlines and trimming whitespace
                                    var cleanedVisaSubclass = (suggestion.visa_subclass || '').replace(/(\r\n|\n|\r)/gm, '').trim();

                                    var $item = $('<div class="autocomplete-item"></div>')
                                        .text(suggestion.occupation)
                                        .data('occupation', {
                                            occupation_code: suggestion.occupation_code || '',
                                            list: suggestion.list || '',
                                            visa_subclass: cleanedVisaSubclass,
                                            //dates: suggestion.dates || ''
                                        })
                                        .appendTo($autocomplete);

                                    $item.on('click', function(e) {
                                        e.stopPropagation(); // Prevent click from bubbling up
                                        var occupationData = $(this).data('occupation');
                                        $row.find('.nomi_occupation').val($(this).text());
                                        $row.find('.occupation_code').val(occupationData.occupation_code);
                                        $row.find('.list').val(occupationData.list);
                                        // Set the cleaned visa_subclass value
                                        $row.find('.visa_subclass').val(occupationData.visa_subclass);
                                        //$row.find('.dates').val(occupationData.dates ? moment(occupationData.dates).format('DD/MM/YYYY') : '');
                                        $autocomplete.empty();

                                        // Reinitialize datepicker for the Assessment Date field in this row
                                        /*$row.find('.dates').daterangepicker({
                                            singleDatePicker: true,
                                            showDropdowns: true,
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
                                        });*/
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
                }
            });

            // Close the autocomplete dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nomi_occupation').length && !$(e.target).closest('.autocomplete-items').length) {
                    $('.autocomplete-items').empty();
                }
            });


            <?php
            if($fetchedData->related_files != '')
            {
            ?>
                var array = [];
                var data = [];
                $('.relatedfile').each(function(){
                    var id = $(this).attr('data-id');
                    array.push(id);
                    var email = $(this).attr('data-email');
                    var name = $(this).attr('data-name');
                    var status = 'Client';

                    data.push({
                        id: id,
                        text: name,
                        html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                            "<div  class='ag-flex ag-align-start'>" +
                                "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
                                "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +

                            "</div>" +
                            "</div>" +
                            "<div class='ag-flex ag-flex-column ag-align-end'>" +

                                "<span class='ui label yellow select2-result-repository__statistics'>"+ status +

                                "</span>" +
                            "</div>" +
                            "</div>",
                        title: name
                    });
                });

                $(".js-data-example-ajaxcc").select2({
                    data: data,
                    escapeMarkup: function(markup) {
                    return markup;
                    },
                    templateResult: function(data) {
                    return data.html;
                    },
                    templateSelection: function(data) {
                    return data.text;
                    }
                });

                $('.js-data-example-ajaxcc').val(array);
                $('.js-data-example-ajaxcc').trigger('change');

            <?php
            } ?>

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
        });

        // Function to toggle visibility of Spouse Details section based on Marital Status
        function toggleSpouseDetailsSection() {
            const martialStatus = document.getElementById('martialStatus').value;
            const spouseDetailsSection = document.getElementById('spouseDetailsSection');

            if (martialStatus === 'Married') {
                spouseDetailsSection.style.display = 'block';
                // Trigger the visibility of dependent fields based on dropdown values
                toggleSpouseEnglishFields();
                toggleSpouseSkillFields();
            } else {
                spouseDetailsSection.style.display = 'none';
                // Hide dependent fields
                document.getElementById('spouseEnglishFields').style.display = 'none';
                document.getElementById('spouseSkillFields').style.display = 'none';
            }
        }

        // Function to toggle visibility of Spouse English Score fields
        function toggleSpouseEnglishFields() {
            const spouseHasEnglishScore = document.getElementById('spouseHasEnglishScore').value;
            const spouseEnglishFields = document.getElementById('spouseEnglishFields');

            if (spouseHasEnglishScore === 'Yes') {
                spouseEnglishFields.style.display = 'block';
            } else {
                spouseEnglishFields.style.display = 'none';
            }
            // Reinitialize datepickers for the newly shown fields
            initializeDatepickers();
        }

        // Function to toggle visibility of Spouse Skill Assessment fields
        function toggleSpouseSkillFields() {
            const spouseHasSkillAssessment = document.getElementById('spouseHasSkillAssessment').value;
            const spouseSkillFields = document.getElementById('spouseSkillFields');

            if (spouseHasSkillAssessment === 'Yes') {
                spouseSkillFields.style.display = 'block';
            } else {
                spouseSkillFields.style.display = 'none';
            }
            // Reinitialize datepickers for the newly shown fields
            initializeDatepickers();
        }

        // Run on page load to set initial state
        document.addEventListener('DOMContentLoaded', function() {
            toggleSpouseDetailsSection();
        });

        // Run on Marital Status change
        document.getElementById('martialStatus').addEventListener('change', function() {
            toggleSpouseDetailsSection();
        });

        // Validate Passport dates on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            let hasError = false;

            // Validate Passport Dates
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

        // Validate Passport and Travel dates on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            let hasError = false;

            // Validate Passport Dates
            const passportSections = document.querySelectorAll('#passportDetailsContainer .repeatable-section');
            passportSections.forEach((section, index) => {
                const issueDateInput = section.querySelector(`input[name="passport_issue_date[${index}]"]`);
                const expiryDateInput = section.querySelector(`input[name="passport_expiry_date[${index}]"]`);

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

            // Validate Travel Dates
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

        // Handle removal of passports and travels with confirmation
        document.addEventListener('DOMContentLoaded', function() {
            // Handle passport removal
            document.getElementById('passportDetailsContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const passportIdInput = section.querySelector('input[name^="passport_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this passport?');

                    if (confirmDelete) {
                        if (passportIdInput) {
                            const passportId = passportIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_passport_ids[]';
                            hiddenInput.value = passportId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });

            // Handle travel removal
            document.getElementById('travelDetailsContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const travelIdInput = section.querySelector('input[name^="travel_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this travel record?');

                    if (confirmDelete) {
                        if (travelIdInput) {
                            const travelId = travelIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_travel_ids[]';
                            hiddenInput.value = travelId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });
        });

        // Validate Spouse Test Date and Assessment Date on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            const spouseTestDateInput = document.querySelector('input[name="spouse_test_date"]');
            const spouseAssessmentDateInput = document.querySelector('input[name="spouse_assessment_date"]');
            let hasError = false;

            // Validate Spouse Test Date
            if (spouseTestDateInput && spouseTestDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(spouseTestDateInput.value)) {
                hasError = true;
                spouseTestDateInput.setCustomValidity('Spouse Test Date must be in dd/mm/yyyy format');
            } else if (spouseTestDateInput) {
                spouseTestDateInput.setCustomValidity('');
            }

            // Validate Spouse Assessment Date
            if (spouseAssessmentDateInput && spouseAssessmentDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(spouseAssessmentDateInput.value)) {
                hasError = true;
                spouseAssessmentDateInput.setCustomValidity('Spouse Assessment Date must be in dd/mm/yyyy format');
            } else if (spouseAssessmentDateInput) {
                spouseAssessmentDateInput.setCustomValidity('');
            }

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

                    // Set the full address
                    input.value = place.formatted_address;

                    // Find the row containing this input
                    const row = input.closest('.repeatable-section');
                    const postcodeInput = row.querySelector('.postcode-input');
                    const regionalCodeInput = row.querySelector('.regional-code-input');

                    // Extract postcode and regional code from address components
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

                    // Update the postcode and regional code fields
                    if (postcode) {
                        postcodeInput.value = postcode;
                    }
                    if (region) {
                        regionalCodeInput.value = region;
                    }

                    // Clear any previous suggestions
                    const autocompleteItems = input.nextElementSibling;
                    if (autocompleteItems) {
                        autocompleteItems.innerHTML = '';
                    }
                });
            });
        }

        // Add Address with the updated layout
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

            // Insert the new address at the top with the updated layout
            container.insertAdjacentHTML('afterbegin', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Address" onclick="removeAddress(this, '')"><i class="fas fa-times-circle"></i></button>
                    <div class="content-grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Full Address</label>
                            <textarea name="address[${index}]" rows="1" class="address-input" ></textarea>
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

            // Reinitialize datepickers for the newly added fields
            initializeDatepickers();
            // Reinitialize Google Maps Autocomplete for the new address input
            initGoogleMaps();
            // Update the state of the checkbox
            updateCurrentAddressCheckbox();
        }

        // Remove Address function
        function removeAddress(button, addressId) {
            const confirmDelete = confirm('Are you sure you want to delete this address?');
            
            if (confirmDelete) {
                const section = button.closest('.repeatable-section');
                
                // If it's an existing address (has an ID), create hidden input for deletion
                if (addressId && addressId !== '') {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'delete_address_ids[]';
                    hiddenInput.value = addressId;
                    document.getElementById('editClientForm').appendChild(hiddenInput);
                }
                
                // Remove the section
                section.remove();
                
                // Update the checkbox state
                updateCurrentAddressCheckbox();
            }
        }

        // Update the state of the "Is this your current address?" checkbox
        function updateCurrentAddressCheckbox() {
            const container = document.getElementById('addressesContainer');
            const checkbox = document.getElementById('isCurrentAddress');
            checkbox.disabled = container.children.length === 0;
            if (container.children.length === 0) {
                checkbox.checked = false;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentAddressCheckbox();
            initGoogleMaps();

            // Address removal is now handled by the removeAddress() function with inline onclick handlers

            // Validate Start Date and End Date on form submission
            document.getElementById('editClientForm').addEventListener('submit', function(e) {
                const addressSections = document.querySelectorAll('#addressesContainer .repeatable-section');
                let hasError = false;

                addressSections.forEach((section, index) => {
                    const startDateInput = section.querySelector('.address-start-date');
                    const endDateInput = section.querySelector('.address-end-date');

                    // Validate Start Date
                    if (startDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(startDateInput.value)) {
                        hasError = true;
                        startDateInput.setCustomValidity('Start Date must be in dd/mm/yyyy format');
                    } else {
                        startDateInput.setCustomValidity('');
                    }

                    // Validate End Date
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

        // Cache countries to avoid multiple AJAX calls
        let countriesCache = null;

        // Function to fetch countries via AJAX
        async function fetchCountries() {
            if (countriesCache) {
                return countriesCache; // Return cached data if available
            }

            try {
                const response = await $.ajax({
                    url: '{{ route("admin.getCountries") }}', // Use Laravel route
                    method: 'GET',
                    dataType: 'json',
                });
                countriesCache = response; // Cache the response
                return response;
            } catch (error) {
                console.error('Error fetching countries:', error);
                return ['India', 'Australia']; // Fallback to at least include India and Australia
            }
        }

        // Add Qualification (updated to include datepickers for Start Date and Finish Date)
        async function addQualification() {
            const container = document.getElementById('qualificationsContainer');
            const index = container.children.length;

            // Fetch countries
            const countries = await fetchCountries();

            // Build the options for the country dropdown
            let countryOptionsHtml = '';
            countries.forEach(country => {
                countryOptionsHtml += `<option value="${country}">${country}</option>`;
            });

            // Insert the new qualification section with datepickers
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

            // Reinitialize datepickers for the newly added fields
            initializeDatepickers();
        }

        // Add Experience (updated to include country dropdown and datepickers for Start Date and Finish Date)
        async function addExperience() {
            const container = document.getElementById('experienceContainer');
            const index = container.children.length;

            // Fetch countries
            const countries = await fetchCountries();

            // Build the options for the country dropdown
            let countryOptionsHtml = '';
            countries.forEach(country => {
                countryOptionsHtml += `<option value="${country}">${country}</option>`;
            });

            // Insert the new experience section with country dropdown and datepickers
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

            // Reinitialize datepickers for the newly added fields
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

            // Reinitialize datepickers for the newly added Assessment Date field
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

        // Function to update validation based on test type
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
                    // IELTS: 1-9 with 0.5 steps
                    input.setAttribute('type', 'number');
                    input.setAttribute('step', '0.5');
                    input.setAttribute('min', '1');
                    input.setAttribute('max', '9');
                } else if (testType === 'TOEFL') {
                    // TOEFL: 0-30
                    input.setAttribute('type', 'number');
                    input.setAttribute('min', '0');
                    input.setAttribute('max', '30');
                } else if (testType === 'PTE') {
                    // PTE: 0-90
                    input.setAttribute('type', 'number');
                    input.setAttribute('min', '0');
                    input.setAttribute('max', '90');
                } else if (testType === 'OET') {
                    // OET: A, B, C, C++, D
                    input.setAttribute('type', 'text');
                    input.setAttribute('pattern', '^(A|B|C|C\\+\\+|D)$');
                    input.setAttribute('title', 'Only A, B, C, C++, or D are allowed');
                } else {
                    // For other test types (CAE), remove restrictions
                    input.setAttribute('type', 'text');
                }
            });
        }

        // Validate test scores on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            const testSections = document.querySelectorAll('#testScoresContainer .repeatable-section');
            let hasError = false;

            testSections.forEach((section, index) => {
                const testType = section.querySelector('.test-type-selector').value;
                const inputs = section.querySelectorAll('.test-score-input');
                const dateInput = section.querySelector('.test-date');

                // Validate date format
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

        // Validate NAATI and PY dates on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            const naatiDateInput = document.getElementById('naatiDate');
            const pyDateInput = document.getElementById('pyDate');
            let hasError = false;

            // Validate NAATI date
            if (naatiDateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(naatiDateInput.value)) {
                hasError = true;
                naatiDateInput.setCustomValidity('NAATI CCL Test date must be in dd/mm/yyyy format');
            } else {
                naatiDateInput.setCustomValidity('');
            }

            // Validate PY date
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

        // Show/hide date inputs based on checkbox state
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

        // Initialize validation for existing test scores
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.test-type-selector').forEach((select, index) => {
                updateTestScoreValidation(select, index);
            });
        });

        // Handle removal of phone numbers and emails with confirmation
        document.addEventListener('DOMContentLoaded', function() {
            // Handle phone number removal
            document.getElementById('phoneNumbersContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const contactIdInput = section.querySelector('input[name^="contact_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this phone number?');

                    if (confirmDelete) {
                        if (contactIdInput) {
                            const contactId = contactIdInput.value;
                            if (!phoneNumbersToDelete.includes(contactId)) {
                                phoneNumbersToDelete.push(contactId);
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'delete_phone_ids[]';
                                hiddenInput.value = contactId;
                                document.getElementById('editClientForm').appendChild(hiddenInput);
                            }
                        }
                        section.remove();
                    }
                }
            });

            // Handle email address removal
            document.getElementById('emailAddressesContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const emailIdInput = section.querySelector('input[name^="email_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this email address?');

                    if (confirmDelete) {
                        if (emailIdInput) {
                            const emailId = emailIdInput.value;
                            if (!emailsToDelete.includes(emailId)) {
                                emailsToDelete.push(emailId);
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'delete_email_ids[]';
                                hiddenInput.value = emailId;
                                document.getElementById('editClientForm').appendChild(hiddenInput);
                            }
                        }
                        section.remove();
                    }
                }
            });
        });

        // Add event listeners for real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            const phoneNumbersContainer = document.getElementById('phoneNumbersContainer');

            // Validate on change of type or phone number
            phoneNumbersContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('contact-type-selector') || e.target.classList.contains('phone-number-input') || e.target.classList.contains('country-code-input')) {
                    validatePersonalPhoneNumbers();
                }
            });

            // Validate on input for real-time feedback
            phoneNumbersContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('phone-number-input') || e.target.classList.contains('country-code-input')) {
                    validatePersonalPhoneNumbers();
                }
            });

            // Initial validation on page load
            validatePersonalPhoneNumbers();
        });

        // Add event listeners for real-time validation and form submission
        document.addEventListener('DOMContentLoaded', function() {
            const emailAddressesContainer = document.getElementById('emailAddressesContainer');

            // Validate on change of type
            emailAddressesContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('email-type-selector')) {
                    validatePersonalEmailTypes();
                }
            });

            // Validate on form submission
            document.getElementById('editClientForm').addEventListener('submit', function(e) {
                if (!validatePersonalEmailTypes()) {
                    e.preventDefault();
                    alert('Only one email address can be of type Personal. Please correct the entries.');
                }
            });

            // Initial validation on page load
            validatePersonalEmailTypes();
        });


        // Function to add a new character row
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

            // Reinitialize datepickers for the newly added field
            initializeDatepickers();
        }

        // Function to remove a character row
        function removeCharacterRow(button, fieldName, characterId = null) {
            const section = button.closest('.repeatable-section');
            const confirmDelete = confirm('Are you sure you want to delete this record?');

            if (confirmDelete) {
                if (characterId) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `delete_${fieldName}_ids[]`;
                    hiddenInput.value = characterId;
                    document.getElementById('editClientForm').appendChild(hiddenInput);
                }
                section.remove();
            }
        }

        // Validate Character & History dates on form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            let hasError = false;

            // Validate Criminal Charges Dates
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

            // Validate Military Service Dates
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

            // Validate Intelligence Work Dates
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

            // Validate Visa Refusals Dates
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

            // Validate Deportations Dates
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

            // Validate Citizenship Refusals Dates
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

            // Validate health Declarations Container Dates
            const healthDeclarationsSections = document.querySelectorAll('#healthDeclarationsContainer .repeatable-section');
            healthDeclarationsSections.forEach((section, index) => {
                const dateInput = section.querySelector(`input[name="health_declarations[${index}][date]"]`);
                if (dateInput.value && !/^\d{2}\/\d{2}\/\d{4}$/.test(dateInput.value)) {
                    hasError = true;
                    dateInput.setCustomValidity('Health Declaration Date must be in dd/mm/yyyy format');
                } else {
                    dateInput.setCustomValidity('');
                }
            });

            if (hasError) {
                e.preventDefault();
            }
        });

        document.getElementById('naatiGiven').addEventListener('change', function() {
            document.getElementById('naatiDate').style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('pyGiven').addEventListener('change', function() {
            document.getElementById('pyDate').style.display = this.checked ? 'block' : 'none';
        });

        // Update form validation for all family member types
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            let hasError = false;
            const types = ['partner', 'children', 'parent', 'siblings', 'others'];

            types.forEach(type => {
                const sections = document.querySelectorAll(`#${type}Container .repeatable-section`);
                sections.forEach((section, index) => {
                    const idInput = section.querySelector(`input[name^="${type}_id"]`);
                    const partnerId = idInput ? idInput.value : null;
                    const details = section.querySelector('.partner-details').value.trim();
                    const relationshipType = section.querySelector(`select[name^="${type}_relationship_type"]`).value;
                    const extraFieldsVisible = section.querySelector('.partner-extra-fields').style.display !== 'none';

                    if (!partnerId && extraFieldsVisible) {
                        //const email = section.querySelector(`input[name^="${type}_email"]`).value.trim();
                        const firstName = section.querySelector(`input[name^="${type}_first_name"]`).value.trim();
                        const lastName = section.querySelector(`input[name^="${type}_last_name"]`).value.trim();
                        //const phone = section.querySelector(`input[name^="${type}_phone"]`).value.trim();

                        if (!firstName || !lastName) {
                            hasError = true;
                            alert(`Please fill in all ${type} details (First Name, Last Name) when no match is found.`);
                        }
                    } else if (!partnerId && !details && !relationshipType) {
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

        document.getElementById('naatiDate').style.display = document.getElementById('naatiGiven').checked ? 'block' : 'none';
        document.getElementById('pyDate').style.display = document.getElementById('pyGiven').checked ? 'block' : 'none';

        // DOB Verified Slider Functionality
        const dobVerifiedCheckbox = document.querySelector('input[name="dob_verified"]');
        const dobVerifyDocumentGroup = document.getElementById('dob_verify_document_group');
        const dobVerifyByGroup = document.getElementById('dob_verify_by_group');
        const dobVerifyDocumentInput = document.getElementById('dob_verify_document');

        function toggleDobVerifiedFields() {
            if (dobVerifiedCheckbox.checked) {
                dobVerifyDocumentGroup.style.display = 'block';
                dobVerifyByGroup.style.display = 'block';
                // Role-based readonly logic
                @if(Auth::user()->role == 1)
                    // Superadmin can always edit - never make readonly
                    dobVerifyDocumentInput.removeAttribute('readonly');
                @else
                    // Non-superadmin: only make readonly if the field already has a value
                    if (dobVerifyDocumentInput.value.trim() !== '') {
                        dobVerifyDocumentInput.setAttribute('readonly', 'readonly');
                    } else {
                        dobVerifyDocumentInput.removeAttribute('readonly');
                    }
                @endif
            } else {
                dobVerifyDocumentGroup.style.display = 'none';
                dobVerifyByGroup.style.display = 'none';
                dobVerifyDocumentInput.removeAttribute('readonly');
            }
        }

        // Add event listener for DOB Verified checkbox
        if (dobVerifiedCheckbox) {
            dobVerifiedCheckbox.addEventListener('change', toggleDobVerifiedFields);
            
            // Initialize the display state on page load
            toggleDobVerifiedFields();
        }

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

        // Ensure the correct tab is active after a validation error
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

        // Ensure the correct tab is active after a validation error for moved sections
        @if ($errors->has('test_type_hidden') || $errors->has('test_type_hidden.*') || $errors->has('listening') || $errors->has('listening.*') || $errors->has('reading') || $errors->has('reading.*') || $errors->has('writing') || $errors->has('writing.*') || $errors->has('speaking') || $errors->has('speaking.*') || $errors->has('overall_score') || $errors->has('overall_score.*') || $errors->has('test_date') || $errors->has('test_date.*') || $errors->has('naati_test') || $errors->has('naati_date') || $errors->has('py_test') || $errors->has('py_date') || $errors->has('spouse_has_english_score') || $errors->has('spouse_has_skill_assessment') || $errors->has('spouse_test_type') || $errors->has('spouse_listening_score') || $errors->has('spouse_reading_score') || $errors->has('spouse_writing_score') || $errors->has('spouse_speaking_score') || $errors->has('spouse_overall_score') || $errors->has('spouse_test_date') || $errors->has('spouse_skill_assessment_status') || $errors->has('spouse_nomi_occupation') || $errors->has('spouse_assessment_date') || $errors->has('level_hidden') || $errors->has('level_hidden.*') || $errors->has('name') || $errors->has('name.*') || $errors->has('country_hidden') || $errors->has('country_hidden.*') || $errors->has('start_date') || $errors->has('start_date.*') || $errors->has('finish_date') || $errors->has('finish_date.*') || $errors->has('relevant_qualification_hidden') || $errors->has('relevant_qualification_hidden.*') || $errors->has('job_title') || $errors->has('job_title.*') || $errors->has('job_code') || $errors->has('job_code.*') || $errors->has('job_country_hidden') || $errors->has('job_country_hidden.*') || $errors->has('job_start_date') || $errors->has('job_start_date.*') || $errors->has('job_finish_date') || $errors->has('job_finish_date.*') || $errors->has('relevant_experience_hidden') || $errors->has('relevant_experience_hidden.*') || $errors->has('nomi_occupation') || $errors->has('nomi_occupation.*') || $errors->has('occupation_code') || $errors->has('occupation_code.*') || $errors->has('list') || $errors->has('list.*')  || $errors->has('dates') || $errors->has('dates.*') || $errors->has('expiry_dates') || $errors->has('expiry_dates.*')  )
            document.getElementById('skillsEducationTab').style.display = 'block';
            document.querySelector('button[onclick="openTab(event, \'skillsEducationTab\')]').classList.add('active');
            document.querySelectorAll('.tab-button').forEach(btn => {
                if (btn.getAttribute('onclick') !== "openTab(event, 'skillsEducationTab')") {
                    btn.classList.remove('active');
                }
            });
        @endif

        // Update error handling to show the correct tab if there are validation errors
        @if ($errors->has('criminal_charges') || $errors->has('criminal_charges.*') ||
             $errors->has('military_service') || $errors->has('military_service.*') ||
             $errors->has('intelligence_work') || $errors->has('intelligence_work.*') ||
             $errors->has('visa_refusals') || $errors->has('visa_refusals.*') ||
             $errors->has('deportations') || $errors->has('deportations.*') ||
             $errors->has('citizenship_refusals') || $errors->has('citizenship_refusals.*')) ||
            $errors->has('health_declarations') || $errors->has('health_declarations.*'))

            document.getElementById('otherInformationTab').style.display = 'block';
            document.querySelector('button[onclick="openTab(event, \'otherInformationTab\')]').classList.add('active');
            document.querySelectorAll('.tab-button').forEach(btn => {
                if (btn.getAttribute('onclick') !== "openTab(event, 'otherInformationTab')") {
                    btn.classList.remove('active');
                }
            });
        @endif

        // Update error handling for new subsections
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

        // Handle removal of Skills & Education items with confirmation
        document.addEventListener('DOMContentLoaded', function() {
            // Handle visa details removal
            document.getElementById('visaDetailsContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const visaIdInput = section.querySelector('input[name^="visas"][name$="[id]"]');
                    const confirmDelete = confirm('Are you sure you want to delete this visa detail?');

                    if (confirmDelete) {
                        if (visaIdInput) {
                            const visaId = visaIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_visa_ids[]';
                            hiddenInput.value = visaId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });

            // Handle qualification removal
            document.getElementById('qualificationsContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const qualificationIdInput = section.querySelector('input[name^="qualification_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this qualification?');

                    if (confirmDelete) {
                        if (qualificationIdInput) {
                            const qualificationId = qualificationIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_qualification_ids[]';
                            hiddenInput.value = qualificationId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });

            // Handle experience removal
            document.getElementById('experienceContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const experienceIdInput = section.querySelector('input[name^="job_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this work experience?');

                    if (confirmDelete) {
                        if (experienceIdInput) {
                            const experienceId = experienceIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_experience_ids[]';
                            hiddenInput.value = experienceId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });

            // Handle occupation removal
            document.getElementById('occupationContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const occupationIdInput = section.querySelector('input[name^="occupation_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this occupation?');

                    if (confirmDelete) {
                        if (occupationIdInput) {
                            const occupationId = occupationIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_occupation_ids[]';
                            hiddenInput.value = occupationId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });

            // Handle test score removal
            document.getElementById('testScoresContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const section = e.target.closest('.repeatable-section');
                    const testScoreIdInput = section.querySelector('input[name^="test_score_id"]');
                    const confirmDelete = confirm('Are you sure you want to delete this test score?');

                    if (confirmDelete) {
                        if (testScoreIdInput) {
                            const testScoreId = testScoreIdInput.value;
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'delete_test_score_ids[]';
                            hiddenInput.value = testScoreId;
                            document.getElementById('editClientForm').appendChild(hiddenInput);
                        }
                        section.remove();
                    }
                }
            });
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATpl3gyx8FSoykbCx3otznCIWP_-8hk7c&libraries=places&callback=initGoogleMaps" async defer></script>
@endpush
@endsection
