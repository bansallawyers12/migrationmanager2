@extends('layouts.admin_client_detail_dashboard')

@push('styles')
<link rel="stylesheet" href="{{asset('css/client-forms.css')}}">
@endpush

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
            
            <!-- Configuration for external JavaScript -->
            <script>
                // Configuration object for edit-client.js
                window.editClientConfig = {
                    visaTypesRoute: '{{ route("admin.getVisaTypes") }}',
                    countriesRoute: '{{ route("admin.getCountries") }}',
                    searchPartnerRoute: '{{ route("admin.clients.searchPartner") }}',
                    csrfToken: '{{ csrf_token() }}'
                };
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
                                <input type="text" id="dob" name="dob" value="{{ $fetchedData->dob ? date('d/m/Y', strtotime($fetchedData->dob)) : '' }}" placeholder="dd/mm/yyyy" autocomplete="off">
                                @error('dob')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="text" id="age" name="age" value="{{ $fetchedData->age }}" readonly>
                                @error('age')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender <span class="text-danger">*</span></label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ $fetchedData->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $fetchedData->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ $fetchedData->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
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
                                    <option value="Defacto" {{ $fetchedData->martial_status == 'Defacto' ? 'selected' : '' }}>Defacto</option>
                                    <option value="Divorced" {{ $fetchedData->martial_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Widowed" {{ $fetchedData->martial_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                                @error('martial_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Contact Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-phone-alt"></i> Phone Numbers</h3>
                        
                        <div id="phoneNumbersContainer">
                            @foreach($clientContacts as $index => $contact)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Phone" onclick="this.parentElement.remove(); validatePersonalPhoneNumbers();"><i class="fas fa-times-circle"></i></button>
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
                                                    <input class="telephone country-code-input" type="tel" name="country_code[{{ $index }}]" value="{{ $contact->country_code }}" style="width: 55px;height: 42px;" readonly>
                                                </div>
                                                <input type="tel" name="phone[{{ $index }}]" value="{{ $contact->phone }}" placeholder="Enter Phone Number" class="phone-number-input" style="width: 230px;" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addPhoneNumber()"><i class="fas fa-plus-circle"></i> Add Phone Number</button>
                    </section>

                    <!-- Email Addresses -->
                    <section class="form-section">
                        <h3><i class="fas fa-envelope"></i> Email Addresses</h3>
                        
                        <div id="emailAddressesContainer">
                            @foreach($emails as $index => $email)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Email" onclick="this.parentElement.remove(); validatePersonalEmailTypes();"><i class="fas fa-times-circle"></i></button>
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
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="add-item-btn" onclick="addEmailAddress()"><i class="fas fa-plus-circle"></i> Add Email Address</button>
    </section>
                </div>

                <!-- Visa, Passport & Citizenship Tab -->
                <div id="visaPassportCitizenshipTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-passport"></i> Passport Information</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label for="passportCountry">Country of Passport</label>
                                <select id="passportCountry" name="country_passport">
                                    <option value="">Select Country</option>
                                    <option value="Australia" {{ $fetchedData->country_passport == 'Australia' ? 'selected' : '' }}>Australia</option>
                                    <option value="India" {{ $fetchedData->country_passport == 'India' ? 'selected' : '' }}>India</option>
                                    <!-- Add more countries as needed -->
                                </select>
                                @error('country_passport')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Passport Details -->
                        <div id="passportDetailsContainer">
                            @foreach($clientPassports as $index => $passport)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Passport" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="passport_id[{{ $index }}]" value="{{ $passport->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Passport #</label>
                                            <input type="text" name="passports[{{ $index }}][passport_number]" value="{{ $passport->passport }}" placeholder="Enter Passport Number">
                                        </div>
                                        <div class="form-group">
                                            <label>Issue Date</label>
                                            <input type="text" name="passports[{{ $index }}][issue_date]" value="{{ $passport->passport_issue_date ? date('d/m/Y', strtotime($passport->passport_issue_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>Expiry Date</label>
                                            <input type="text" name="passports[{{ $index }}][expiry_date]" value="{{ $passport->passport_expiry_date ? date('d/m/Y', strtotime($passport->passport_expiry_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addPassportDetail()"><i class="fas fa-plus-circle"></i> Add Passport</button>
                    </section>

                    <!-- Visa Information -->
                    <section class="form-section">
                        <h3><i class="fas fa-plane"></i> Visa Information</h3>

                        <!-- Visa Details -->
                        <div id="visaDetailsSection">
                            <div id="visaDetailsContainer">
                                @foreach($visaCountries as $index => $visa)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Visa"><i class="fas fa-times-circle"></i></button>
                                        <input type="hidden" name="visa_id[{{ $index }}]" value="{{ $visa->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Visa Type / Subclass</label>
                                                <input type="text" name="visas[{{ $index }}][visa_type]" value="{{ $visa->visa_type }}" class="visa-type-field">
                                            </div>
                                            <div class="form-group">
                                                <label>Visa Expiry Date</label>
                                                <input type="text" name="visas[{{ $index }}][expiry_date]" value="{{ $visa->visa_expiry_date ? date('d/m/Y', strtotime($visa->visa_expiry_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                                            </div>
                                            <div class="form-group">
                                                <label>Visa Grant Date</label>
                                                <input type="text" name="visas[{{ $index }}][grant_date]" value="{{ $visa->visa_grant_date ? date('d/m/Y', strtotime($visa->visa_grant_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-grant-field date-picker">
                                            </div>
                                            <div class="form-group">
                                                <label>Visa Description</label>
                                                <input type="text" name="visas[{{ $index }}][description]" value="{{ $visa->visa_description }}" class="visa-description-field">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addVisaDetail()"><i class="fas fa-plus-circle"></i> Add Visa Detail</button>
                        </div>

                        <!-- Visa Expiry Verified -->
                        <div id="visaExpiryVerifiedContainer" class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <label>Visa Expiry Verified?</label>
                            <label class="switch" style="margin: 0;">
                                <input type="checkbox" name="visa_expiry_verified" value="1" {{ $fetchedData->visa_expiry_verified ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </section>
                </div>

                <!-- Address & Travel Tab -->
                <div id="addressTravelTab" class="tab-content">
                    <!-- Address Information Section -->
                    <section class="form-section">
                        <h3><i class="fas fa-home"></i> Address Information</h3>
                        
                        <div id="addressContainer">
                            @foreach($clientAddresses as $index => $address)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Address" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="address_id[{{ $index }}]" value="{{ $address->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address[{{ $index }}]" rows="2" placeholder="Enter Address">{{ $address->address }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Postal Code</label>
                                            <input type="text" name="zip[{{ $index }}]" value="{{ $address->zip }}" placeholder="Enter Postal Code">
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="address_start_date[{{ $index }}]" value="{{ $address->start_date ? date('d/m/Y', strtotime($address->start_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="text" name="address_end_date[{{ $index }}]" value="{{ $address->end_date ? date('d/m/Y', strtotime($address->end_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addAddress()"><i class="fas fa-plus-circle"></i> Add Address</button>
                    </section>

                    <!-- Travel Information Section -->
                    <section class="form-section">
                        <h3><i class="fas fa-plane-departure"></i> Travel Information</h3>
                        
                        <div id="travelDetailsContainer">
                            @foreach($clientTravels as $index => $travel)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Travel" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="travel_id[{{ $index }}]" value="{{ $travel->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Country Visited</label>
                                            <input type="text" name="travel_country_visited[{{ $index }}]" value="{{ $travel->country_visited }}" placeholder="Country Visited">
                                        </div>
                                        <div class="form-group">
                                            <label>Arrival Date</label>
                                            <input type="text" name="travel_arrival_date[{{ $index }}]" value="{{ $travel->arrival_date ? date('d/m/Y', strtotime($travel->arrival_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>Departure Date</label>
                                            <input type="text" name="travel_departure_date[{{ $index }}]" value="{{ $travel->departure_date ? date('d/m/Y', strtotime($travel->departure_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>Travel Purpose</label>
                                            <input type="text" name="travel_purpose[{{ $index }}]" value="{{ $travel->travel_purpose }}" placeholder="Enter Travel Purpose">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addTravelDetail()"><i class="fas fa-plus-circle"></i> Add Travel Detail</button>
                    </section>
                </div>

                <!-- Skills & Education Tab -->
                <div id="skillsEducationTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-graduation-cap"></i> Educational Qualifications</h3>
                        
                        <div id="qualificationsContainer">
                            @foreach($qualifications as $index => $qualification)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Qualification" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="qualification_id[{{ $index }}]" value="{{ $qualification->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Qualification</label>
                                            <input type="text" name="qualification[{{ $index }}]" value="{{ $qualification->qualification }}" placeholder="Enter Qualification">
                                        </div>
                                        <div class="form-group">
                                            <label>Institution</label>
                                            <input type="text" name="institution[{{ $index }}]" value="{{ $qualification->institution }}" placeholder="Enter Institution">
                                        </div>
                                        <div class="form-group">
                                            <label>Country</label>
                                            <input type="text" name="qual_country[{{ $index }}]" value="{{ $qualification->country }}" placeholder="Enter Country">
                                        </div>
                                        <div class="form-group">
                                            <label>Year</label>
                                            <input type="text" name="year[{{ $index }}]" value="{{ $qualification->year }}" placeholder="Enter Year">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addQualification()"><i class="fas fa-plus-circle"></i> Add Qualification</button>
                    </section>

                    <!-- Work Experience Section -->
                    <section class="form-section">
                        <h3><i class="fas fa-briefcase"></i> Work Experience</h3>
                        
                        <div id="experienceContainer">
                            @foreach($experiences as $index => $experience)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Experience" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="experience_id[{{ $index }}]" value="{{ $experience->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Company</label>
                                            <input type="text" name="company[{{ $index }}]" value="{{ $experience->company }}" placeholder="Enter Company">
                                        </div>
                                        <div class="form-group">
                                            <label>Position</label>
                                            <input type="text" name="position[{{ $index }}]" value="{{ $experience->position }}" placeholder="Enter Position">
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="exp_start_date[{{ $index }}]" value="{{ $experience->start_date ? date('d/m/Y', strtotime($experience->start_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="text" name="exp_end_date[{{ $index }}]" value="{{ $experience->end_date ? date('d/m/Y', strtotime($experience->end_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addExperience()"><i class="fas fa-plus-circle"></i> Add Experience</button>
                    </section>
                </div>

                <!-- Other Information Tab -->
                <div id="otherInformationTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
                        
                        <div class="content-grid">
                            <div class="form-group">
                                <label for="naatiTest">NAATI Test</label>
                                <select id="naatiTest" name="naati_test">
                                    <option value="0" {{ !$fetchedData->naati_test ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $fetchedData->naati_test ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="naatiDate">NAATI Date</label>
                                <input type="text" id="naatiDate" name="naati_date" value="{{ $fetchedData->naati_date ? date('d/m/Y', strtotime($fetchedData->naati_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                            <div class="form-group">
                                <label for="pyTest">PY Test</label>
                                <select id="pyTest" name="py_test">
                                    <option value="0" {{ !$fetchedData->py_test ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $fetchedData->py_test ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pyDate">PY Date</label>
                                <input type="text" id="pyDate" name="py_date" value="{{ $fetchedData->py_date ? date('d/m/Y', strtotime($fetchedData->py_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                            </div>
                        </div>

                        <!-- Character Section -->
                        <div class="form-section">
                            <h3><i class="fas fa-shield-alt"></i> Character Information</h3>
                            
                            <div id="characterContainer">
                                @foreach($clientCharacters as $index => $character)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Character" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                        <input type="hidden" name="character_id[{{ $index }}]" value="{{ $character->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Detail</label>
                                                <textarea name="character_detail[{{ $index }}]" rows="2" placeholder="Enter Detail">{{ $character->detail }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addCharacterRow('characterContainer', 'character_detail')"><i class="fas fa-plus-circle"></i> Add Character</button>
                        </div>
                    </section>
                </div>

                <!-- Family Information Tab -->
                <div id="familyTab" class="tab-content">
                    <!-- Partner Section -->
                    <section class="form-section">
                        <h3><i class="fas fa-heart"></i> Partner</h3>
                        
                        <div id="partnerContainer">
                            @foreach($clientPartners->where('relationship_type', 'Husband')->merge($clientPartners->where('relationship_type', 'Wife'))->merge($clientPartners->where('relationship_type', 'Ex-Wife'))->merge($clientPartners->where('relationship_type', 'Defacto')) as $index => $partner)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Partner" onclick="removePartnerRow(this, 'partner', {{ $partner->id }})"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="partner_id[{{ $index }}]" class="partner-id" value="{{ $partner->related_client_id }}">
                                    <input type="hidden" name="relationship_id[{{ $index }}]" value="{{ $partner->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="partner_details[{{ $index }}]" class="partner-details" value="{{ $partner->relatedClient ? $partner->relatedClient->first_name . ' ' . $partner->relatedClient->last_name . ' (' . $partner->relatedClient->email . ', ' . $partner->relatedClient->phone . ', ' . $partner->relatedClient->client_id . ')' : $partner->details }}" placeholder="Search by Name, Email, Client ID, or Phone" readonly>
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
                                    <div class="partner-extra-fields" style="display: none;">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="partner_email[{{ $index }}]" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="partner_first_name[{{ $index }}]" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="partner_last_name[{{ $index }}]" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="partner_phone[{{ $index }}]" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="partner_dob[{{ $index }}]" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addPartnerRow('partner')"><i class="fas fa-plus-circle"></i> Add Partner</button>
                    </section>

                    <!-- Children Section -->
                    <section class="form-section">
                        <h3><i class="fas fa-child"></i> Children</h3>
                        
                        <div id="childrenContainer">
                            @foreach($clientPartners->whereIn('relationship_type', ['Son', 'Daughter', 'Step Son', 'Step Daughter']) as $index => $child)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Child" onclick="removePartnerRow(this, 'children', {{ $child->id }})"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="children_id[{{ $index }}]" class="partner-id" value="{{ $child->related_client_id }}">
                                    <input type="hidden" name="children_relationship_id[{{ $index }}]" value="{{ $child->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Details</label>
                                            <input type="text" name="children_details[{{ $index }}]" class="partner-details" value="{{ $child->relatedClient ? $child->relatedClient->first_name . ' ' . $child->relatedClient->last_name . ' (' . $child->relatedClient->email . ', ' . $child->relatedClient->phone . ', ' . $child->relatedClient->client_id . ')' : $child->details }}" placeholder="Search by Name, Email, Client ID, or Phone" readonly>
                                            <div class="autocomplete-items"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship Type</label>
                                            <select name="children_relationship_type[{{ $index }}]" required>
                                                <option value="">Select Relationship</option>
                                                <option value="Son" {{ $child->relationship_type == 'Son' ? 'selected' : '' }}>Son</option>
                                                <option value="Daughter" {{ $child->relationship_type == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                                <option value="Step Son" {{ $child->relationship_type == 'Step Son' ? 'selected' : '' }}>Step Son</option>
                                                <option value="Step Daughter" {{ $child->relationship_type == 'Step Daughter' ? 'selected' : '' }}>Step Daughter</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="children_gender[{{ $index }}]" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $child->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $child->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ $child->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Company Type</label>
                                            <select name="children_company_type[{{ $index }}]">
                                                <option value="">Select Company Type</option>
                                                <option value="Accompany Member" {{ $child->company_type == 'Accompany Member' ? 'selected' : '' }}>Accompany Member</option>
                                                <option value="Non-Accompany Member" {{ $child->company_type == 'Non-Accompany Member' ? 'selected' : '' }}>Non-Accompany Member</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partner-extra-fields" style="display: none;">
                                        <div class="content-grid single-row">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="children_email[{{ $index }}]" placeholder="Enter Email">
                                            </div>
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="children_first_name[{{ $index }}]" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="children_last_name[{{ $index }}]" placeholder="Enter Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="children_phone[{{ $index }}]" placeholder="Enter Phone">
                                            </div>
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="text" name="children_dob[{{ $index }}]" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addPartnerRow('children')"><i class="fas fa-plus-circle"></i> Add Child</button>
                    </section>
                </div>

                <!-- EOI Reference Tab -->
                <div id="eoiReferenceTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-file-alt"></i> EOI References</h3>
                        
                        <div id="eoiReferencesContainer">
                            @foreach($clientEoiReferences as $index => $eoi)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove EOI Reference" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="eoi_id[{{ $index }}]" value="{{ $eoi->id }}">
                                    <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                        <div class="form-group">
                                            <label>EOI Number</label>
                                            <input type="text" name="EOI_number[{{ $index }}]" value="{{ $eoi->EOI_number }}" placeholder="Enter EOI Number">
                                        </div>
                                        <div class="form-group">
                                            <label>Subclass</label>
                                            <input type="text" name="EOI_subclass[{{ $index }}]" value="{{ $eoi->EOI_subclass }}" placeholder="Enter Subclass">
                                        </div>
                                        <div class="form-group">
                                            <label>Occupation</label>
                                            <input type="text" name="EOI_occupation[{{ $index }}]" value="{{ $eoi->EOI_occupation }}" placeholder="Enter Occupation">
                                        </div>
                                        <div class="form-group">
                                            <label>Point</label>
                                            <input type="text" name="EOI_point[{{ $index }}]" value="{{ $eoi->EOI_point }}" placeholder="Enter Point">
                                        </div>
                                        <div class="form-group">
                                            <label>State</label>
                                            <input type="text" name="EOI_state[{{ $index }}]" value="{{ $eoi->EOI_state }}" placeholder="Enter State">
                                        </div>
                                        <div class="form-group">
                                            <label>Submission Date</label>
                                            <input type="text" name="EOI_submission_date[{{ $index }}]" value="{{ $eoi->EOI_submission_date ? date('d/m/Y', strtotime($eoi->EOI_submission_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>ROI</label>
                                            <input type="text" name="EOI_ROI[{{ $index }}]" value="{{ $eoi->EOI_ROI }}" placeholder="Enter ROI">
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <input type="password" name="EOI_password[{{ $index }}]" value="{{ $eoi->EOI_password }}" placeholder="Enter Password" class="eoi-password-input" data-index="{{ $index }}">
                                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" data-index="{{ $index }}" title="Show/Hide Password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="add-item-btn" onclick="addEoiReference()"><i class="fas fa-plus-circle"></i> Add EOI Reference</button>
                    </section>
                </div>

                <!-- Summary Tab -->
                <div id="summaryTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-list"></i> Summary</h3>
                        <p>Review all entered information before submitting.</p>
                        <!-- Summary content will be populated here -->
                    </section>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="{{asset('js/clients/edit-client.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATpl3gyx8FSoykbCx3otznCIWP_-8hk7c&libraries=places&callback=initGoogleMaps" async defer></script>
    @endpush
@endsection
