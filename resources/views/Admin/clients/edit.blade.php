@extends('layouts.admin_client_detail_dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address-autocomplete.css') }}">
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
                    <!-- Back button moved to sidebar -->
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

            <!-- Mobile Sidebar Toggle -->
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar Navigation -->
            <div class="sidebar-navigation" id="sidebarNav">
                <div class="nav-header">
                    <h3><i class="fas fa-user-edit"></i> Edit Client</h3>
                </div>
                <nav class="nav-menu">
                    <button class="nav-item active" onclick="scrollToSection('personalSection')">
                        <i class="fas fa-user-circle"></i>
                        <span>Personal</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('visaPassportSection')">
                        <i class="fas fa-id-card"></i>
                        <span>Visa, Passport & Citizenship</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('addressTravelSection')">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Address & Travel</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('skillsEducationSection')">
                        <i class="fas fa-briefcase"></i>
                        <span>Skills & Education</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('otherInformationSection')">
                        <i class="fas fa-info-circle"></i>
                        <span>Other Information</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('familySection')">
                        <i class="fas fa-users"></i>
                        <span>Family Information</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('eoiReferenceSection')">
                        <i class="fas fa-file-alt"></i>
                        <span>EOI Reference</span>
                    </button>
                </nav>
                
                <!-- Back Button in Sidebar -->
                <div class="sidebar-actions">
                    <button class="nav-item summary-nav back-btn" onclick="goBackWithRefresh()">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                </div>
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

            <!-- Main Content Area -->
            <div class="main-content-area">
                <form id="editClientForm" action="{{ route('admin.clients.edit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $fetchedData->id }}">
                    <input type="hidden" name="type" value="{{ $fetchedData->type }}">

                <!-- Personal Section -->
                <section id="personalSection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-user-circle"></i> Basic Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('basicInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="basicInfoSummary" class="summary-view">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">Name:</span>
                                    <span class="summary-value">{{ $fetchedData->first_name }} {{ $fetchedData->last_name }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">{{ $fetchedData->type == 'lead' ? 'Lead ID' : 'Client ID' }}:</span>
                                    <span class="summary-value">{{ $fetchedData->client_id }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Date of Birth:</span>
                                    <span class="summary-value">{{ $fetchedData->dob ? date('d/m/Y', strtotime($fetchedData->dob)) : 'Not set' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Age:</span>
                                    <span class="summary-value">{{ $fetchedData->age ?: 'Not calculated' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Gender:</span>
                                    <span class="summary-value">{{ $fetchedData->gender ?: 'Not set' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Marital Status:</span>
                                    <span class="summary-value">{{ $fetchedData->martial_status ?: 'Not set' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit View -->
                        <div id="basicInfoEdit" class="edit-view" style="display: none;">
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
                                        <option value="Defacto" {{ ($fetchedData->martial_status == 'Defacto' || $fetchedData->martial_status == 'De Facto') ? 'selected' : '' }}>De Facto</option>
                                        <option value="Divorced" {{ $fetchedData->martial_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ $fetchedData->martial_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                    @error('martial_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveBasicInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('basicInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Contact Information -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-mobile-alt"></i> Phone Numbers</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('phoneNumbers')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addPhoneNumber()" title="Add Phone Number">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="phoneNumbersSummary" class="summary-view">
                            @if($clientContacts->count() > 0)
                                <div class="summary-grid">
                                    @foreach($clientContacts as $index => $contact)
                                        <div class="summary-item">
                                            <span class="summary-label">{{ $contact->contact_type }}:</span>
                                            <span class="summary-value">{{ $contact->country_code }}{{ $contact->phone }}</span>
                                            <!-- Verification Button/Badge -->
                                            @if($contact->country_code == '+61')
                                                @if($contact->is_verified)
                                                    <span class="verified-badge" title="Verified on {{ $contact->verified_at ? $contact->verified_at->format('M j, Y g:i A') : 'Unknown' }}">
                                                        <i class="fas fa-check-circle"></i> Verified
                                                    </span>
                                                @else
                                                    <button type="button" class="btn-verify-phone" onclick="sendOTP({{ $contact->id }}, '{{ $contact->phone }}', '{{ $contact->country_code }}')" data-contact-id="{{ $contact->id }}">
                                                        <i class="fas fa-lock"></i> Verify
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No phone numbers added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="phoneNumbersEdit" class="edit-view" style="display: none;">
                            <div id="phoneNumbersContainer">
                                @foreach($clientContacts as $index => $contact)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Phone" onclick="removePhoneField(this)"><i class="fas fa-trash"></i></button>
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
                                                        <select name="country_code[{{ $index }}]" class="country-code-input">
                                                            <option value="+61" {{ $contact->country_code == '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                                                            <option value="+91" {{ $contact->country_code == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                                                            <option value="+1" {{ $contact->country_code == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                                            <option value="+44" {{ $contact->country_code == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                                            <option value="+49" {{ $contact->country_code == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                                            <option value="+33" {{ $contact->country_code == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                                            <option value="+86" {{ $contact->country_code == '+86' ? 'selected' : '' }}>🇨🇳 +86</option>
                                                            <option value="+81" {{ $contact->country_code == '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                                                            <option value="+82" {{ $contact->country_code == '+82' ? 'selected' : '' }}>🇰🇷 +82</option>
                                                            <option value="+65" {{ $contact->country_code == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                                                            <option value="+60" {{ $contact->country_code == '+60' ? 'selected' : '' }}>🇲🇾 +60</option>
                                                            <option value="+66" {{ $contact->country_code == '+66' ? 'selected' : '' }}>🇹🇭 +66</option>
                                                            <option value="+63" {{ $contact->country_code == '+63' ? 'selected' : '' }}>🇵🇭 +63</option>
                                                            <option value="+84" {{ $contact->country_code == '+84' ? 'selected' : '' }}>🇻🇳 +84</option>
                                                            <option value="+62" {{ $contact->country_code == '+62' ? 'selected' : '' }}>🇮🇩 +62</option>
                                                        </select>
                                                    </div>
                                                    <input type="tel" name="phone[{{ $index }}]" value="{{ $contact->phone }}" placeholder="Phone Number" class="phone-number-input" style="width: 140px;" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addPhoneNumber()"><i class="fas fa-plus-circle"></i> Add Phone Number</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="savePhoneNumbers()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('phoneNumbers')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Email Addresses -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-envelope"></i> Email Addresses</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('emailAddresses')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addEmailAddress()" title="Add Email Address">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="emailAddressesSummary" class="summary-view">
                            @if($emails->count() > 0)
                                <div class="summary-grid">
                                    @foreach($emails as $index => $email)
                                        <div class="summary-item">
                                            <span class="summary-label">{{ $email->email_type }}:</span>
                                            <span class="summary-value">{{ $email->email }}</span>
                                            <!-- Verification Button/Badge -->
                                            @if($email->is_verified)
                                                <span class="verified-badge" title="Verified on {{ $email->verified_at ? $email->verified_at->format('M j, Y g:i A') : 'Unknown' }}">
                                                    <i class="fas fa-check-circle"></i> Verified
                                                </span>
                                            @else
                                                <button type="button" class="btn-verify-email" onclick="sendEmailVerification({{ $email->id }}, '{{ $email->email }}')" data-email-id="{{ $email->id }}">
                                                    <i class="fas fa-lock"></i> Verify
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No email addresses added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="emailAddressesEdit" class="edit-view" style="display: none;">
                            <div id="emailAddressesContainer">
                                @foreach($emails as $index => $email)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Email" onclick="removeEmailField(this)"><i class="fas fa-trash"></i></button>
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
                                                <input type="email" name="email[{{ $index }}]" value="{{ $email->email }}" placeholder="Email Address">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addEmailAddress()"><i class="fas fa-plus-circle"></i> Add Email Address</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveEmailAddresses()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('emailAddresses')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Visa, Passport & Citizenship Section -->
                <section id="visaPassportSection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-id-card"></i> Passport Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('passportInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addPassportDetail()" title="Add Passport">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="passportInfoSummary" class="summary-view">
                            @if($clientPassports->count() > 0)
                                <div style="margin-top: 15px;">
                                    @foreach($clientPassports as $index => $passport)
                                        <div class="passport-entry-compact" style="margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #007bff;">
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: center;">
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">COUNTRY:</span>
                                                    <span class="summary-value" style="color: #212529; font-weight: 500;">{{ $passport->passport_country ?: 'Not set' }}</span>
                                                </div>
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">PASSPORT #:</span>
                                                    <span class="summary-value" style="color: #212529; font-weight: 500;">{{ $passport->passport ?: 'Not set' }}</span>
                                                </div>
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">ISSUE DATE:</span>
                                                    <span class="summary-value" style="color: #212529;">{{ $passport->passport_issue_date ? date('d/m/Y', strtotime($passport->passport_issue_date)) : 'Not set' }}</span>
                                                </div>
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">EXPIRY DATE:</span>
                                                    <span class="summary-value" style="color: #212529;">{{ $passport->passport_expiry_date ? date('d/m/Y', strtotime($passport->passport_expiry_date)) : 'Not set' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state" style="margin-top: 15px;">
                                    <p>No passport details added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="passportInfoEdit" class="edit-view" style="display: none;">
                            <!-- Passport Details -->
                            <div id="passportDetailsContainer">
                                @foreach($clientPassports as $index => $passport)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Passport" onclick="removePassportField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="passport_id[{{ $index }}]" value="{{ $passport->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select name="passports[{{ $index }}][passport_country]" class="passport-country-field">
                                                    <option value="">Select Country</option>
                                                    <option value="India" {{ $passport->passport_country == 'India' ? 'selected' : '' }}>India</option>
                                                    <option value="Australia" {{ $passport->passport_country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                    @foreach(\App\Models\Country::all() as $country)
                                                        @if($country->name != 'India' && $country->name != 'Australia')
                                                            <option value="{{ $country->name }}" {{ $passport->passport_country == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Passport #</label>
                                                <input type="text" name="passports[{{ $index }}][passport_number]" value="{{ $passport->passport }}" placeholder="Passport Number">
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
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="savePassportInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('passportInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Visa Information -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-plane-departure"></i> Visa Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('visaInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addVisaDetail()" title="Add Visa Detail">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="visaInfoSummary" class="summary-view">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">Visa Expiry Verified:</span>
                                    <span class="summary-value">{{ $fetchedData->visa_expiry_verified_at ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                            @if($visaCountries->count() > 0)
                                <div style="margin-top: 15px;">
                                    @foreach($visaCountries as $index => $visa)
                                        <div class="visa-entry-compact" style="margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: center;">
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">VISA TYPE:</span>
                                                    <span class="summary-value" style="color: #212529; font-weight: 500;">
                                                        @php
                                                            $Matter_get = App\Models\Matter::select('id','title','nick_name')->where('id',$visa->visa_type)->first();
                                                        @endphp
                                                        {{ $Matter_get ? $Matter_get->title . ' (' . $Matter_get->nick_name . ')' : 'Not set' }}
                                                    </span>
                                                </div>
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">EXPIRY DATE:</span>
                                                    <span class="summary-value" style="color: #212529;">{{ $visa->visa_expiry_date ? date('d/m/Y', strtotime($visa->visa_expiry_date)) : 'Not set' }}</span>
                                                </div>
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">GRANT DATE:</span>
                                                    <span class="summary-value" style="color: #212529;">{{ $visa->visa_grant_date ? date('d/m/Y', strtotime($visa->visa_grant_date)) : 'Not set' }}</span>
                                                </div>
                                                <div class="summary-item-inline">
                                                    <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">DESCRIPTION:</span>
                                                    <span class="summary-value" style="color: #212529;">{{ $visa->visa_description ?: 'Not set' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state" style="margin-top: 15px;">
                                    <p>No visa details added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="visaInfoEdit" class="edit-view" style="display: none;">
                            <!-- Visa Details -->
                            <div id="visaDetailsSection">
                                <div id="visaDetailsContainer">
                                    @foreach($visaCountries as $index => $visa)
                                        <div class="repeatable-section">
                                            <button type="button" class="remove-item-btn" title="Remove Visa" onclick="removeVisaField(this)"><i class="fas fa-trash"></i></button>
                                            <input type="hidden" name="visa_id[{{ $index }}]" value="{{ $visa->id }}">
                                            <div class="content-grid">
                                                <div class="form-group">
                                                    <label>Visa Type / Subclass</label>
                                                    <select name="visa_type_hidden[{{ $index }}]" class="visa-type-field">
                                                        <option value="">Select Visa Type</option>
                                                        @foreach($visaTypes as $visaType)
                                                            <option value="{{ $visaType->id }}" {{ $visa->visa_type == $visaType->id ? 'selected' : '' }}>
                                                                {{ $visaType->title }}{{ $visaType->nick_name ? ' (' . $visaType->nick_name . ')' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Visa Expiry Date</label>
                                                    <input type="text" name="visa_expiry_date[{{ $index }}]" value="{{ $visa->visa_expiry_date ? date('d/m/Y', strtotime($visa->visa_expiry_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                                                </div>
                                                <div class="form-group">
                                                    <label>Visa Grant Date</label>
                                                    <input type="text" name="visa_grant_date[{{ $index }}]" value="{{ $visa->visa_grant_date ? date('d/m/Y', strtotime($visa->visa_grant_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-grant-field date-picker">
                                                </div>
                                                <div class="form-group">
                                                    <label>Visa Description</label>
                                                    <input type="text" name="visa_description[{{ $index }}]" value="{{ $visa->visa_description }}" class="visa-description-field" placeholder="Description">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="add-item-btn" onclick="addVisaDetail()"><i class="fas fa-plus-circle"></i> Add Visa Detail</button>
                            </div>

                            <!-- Visa Expiry Verified -->
                            <div id="visaExpiryVerifiedContainer" class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
                                <label>Visa Expiry Verified?</label>
                                <label class="switch" style="margin: 0;">
                                    <input type="checkbox" name="visa_expiry_verified" value="1" {{ $fetchedData->visa_expiry_verified_at ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveVisaInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('visaInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Address & Travel Section -->
                <section id="addressTravelSection" class="content-section">
                    @include('Admin.clients.partials._address_information')
                    
                    <!-- Travel Information Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-plane-departure"></i> Travel Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('travelInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addTravelDetail()" title="Add Travel Detail">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="travelInfoSummary" class="summary-view">
                            @if($clientTravels->count() > 0)
                                <div class="summary-grid">
                                    @foreach($clientTravels as $index => $travel)
                                        <div class="summary-item">
                                            <span class="summary-label">Country Visited:</span>
                                            <span class="summary-value">{{ $travel->country_visited ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Arrival Date:</span>
                                            <span class="summary-value">{{ $travel->arrival_date ? date('d/m/Y', strtotime($travel->arrival_date)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Departure Date:</span>
                                            <span class="summary-value">{{ $travel->departure_date ? date('d/m/Y', strtotime($travel->departure_date)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Travel Purpose:</span>
                                            <span class="summary-value">{{ $travel->travel_purpose ?: 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No travel details added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="travelInfoEdit" class="edit-view" style="display: none;">
                            <div id="travelDetailsContainer">
                                @foreach($clientTravels as $index => $travel)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Travel" onclick="removeTravelField(this)"><i class="fas fa-trash"></i></button>
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
                                                <input type="text" name="travel_purpose[{{ $index }}]" value="{{ $travel->travel_purpose }}" placeholder="Travel Purpose">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addTravelDetail()"><i class="fas fa-plus-circle"></i> Add Travel Detail</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveTravelInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('travelInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Skills & Education Section -->
                <section id="skillsEducationSection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-graduation-cap"></i> Educational Qualifications</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('qualificationsInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addQualification()" title="Add Qualification">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="qualificationsInfoSummary" class="summary-view">
                            @if($qualifications->count() > 0)
                                <div class="summary-grid">
                                    @foreach($qualifications as $index => $qualification)
                                        <div class="summary-item">
                                            <span class="summary-label">Qualification:</span>
                                            <span class="summary-value">{{ $qualification->qualification ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Institution:</span>
                                            <span class="summary-value">{{ $qualification->institution ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Country:</span>
                                            <span class="summary-value">{{ $qualification->country ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Year:</span>
                                            <span class="summary-value">{{ $qualification->year ?: 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No qualifications added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="qualificationsInfoEdit" class="edit-view" style="display: none;">
                            <div id="qualificationsContainer">
                                @foreach($qualifications as $index => $qualification)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Qualification" onclick="removeQualificationField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="qualification_id[{{ $index }}]" value="{{ $qualification->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Qualification</label>
                                                <input type="text" name="qualification[{{ $index }}]" value="{{ $qualification->qualification }}" placeholder="Qualification">
                                            </div>
                                            <div class="form-group">
                                                <label>Institution</label>
                                                <input type="text" name="institution[{{ $index }}]" value="{{ $qualification->institution }}" placeholder="Institution">
                                            </div>
                                            <div class="form-group">
                                                <label>Country</label>
                                                <input type="text" name="qual_country[{{ $index }}]" value="{{ $qualification->country }}" placeholder="Country">
                                            </div>
                                            <div class="form-group">
                                                <label>Year</label>
                                                <input type="text" name="year[{{ $index }}]" value="{{ $qualification->year }}" placeholder="Year">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addQualification()"><i class="fas fa-plus-circle"></i> Add Qualification</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveQualificationsInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('qualificationsInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Work Experience Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-briefcase"></i> Work Experience</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('experienceInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addExperience()" title="Add Experience">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="experienceInfoSummary" class="summary-view">
                            @if($experiences->count() > 0)
                                <div class="summary-grid">
                                    @foreach($experiences as $index => $experience)
                                        <div class="summary-item">
                                            <span class="summary-label">Company:</span>
                                            <span class="summary-value">{{ $experience->company ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Position:</span>
                                            <span class="summary-value">{{ $experience->position ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Start Date:</span>
                                            <span class="summary-value">{{ $experience->start_date ? date('d/m/Y', strtotime($experience->start_date)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">End Date:</span>
                                            <span class="summary-value">{{ $experience->end_date ? date('d/m/Y', strtotime($experience->end_date)) : 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No work experience added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="experienceInfoEdit" class="edit-view" style="display: none;">
                            <div id="experienceContainer">
                                @foreach($experiences as $index => $experience)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Experience" onclick="removeExperienceField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="experience_id[{{ $index }}]" value="{{ $experience->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Company</label>
                                                <input type="text" name="company[{{ $index }}]" value="{{ $experience->company }}" placeholder="Company">
                                            </div>
                                            <div class="form-group">
                                                <label>Position</label>
                                                <input type="text" name="position[{{ $index }}]" value="{{ $experience->position }}" placeholder="Position">
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
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveExperienceInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('experienceInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Occupation & Skills Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-cogs"></i> Occupation & Skills</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('occupationInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addOccupation()" title="Add Occupation">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="occupationInfoSummary" class="summary-view">
                            @if($clientOccupations->count() > 0)
                                <div class="summary-grid">
                                    @foreach($clientOccupations as $index => $occupation)
                                        <div class="summary-item">
                                            <span class="summary-label">Skill Assessment:</span>
                                            <span class="summary-value">{{ $occupation->skill_assessment ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Nominated Occupation:</span>
                                            <span class="summary-value">{{ $occupation->nomi_occupation ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Occupation Code:</span>
                                            <span class="summary-value">{{ $occupation->occupation_code ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Assessing Authority:</span>
                                            <span class="summary-value">{{ $occupation->list ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Visa Subclass:</span>
                                            <span class="summary-value">{{ $occupation->visa_subclass ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Assessment Date:</span>
                                            <span class="summary-value">{{ $occupation->dates ? date('d/m/Y', strtotime($occupation->dates)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Expiry Date:</span>
                                            <span class="summary-value">{{ $occupation->expiry_dates ? date('d/m/Y', strtotime($occupation->expiry_dates)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Reference No:</span>
                                            <span class="summary-value">{{ $occupation->occ_reference_no ?: 'Not set' }}</span>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="summary-divider"></div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="no-data-message">
                                    <p>No occupation information available.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="occupationInfoEdit" class="edit-view" style="display: none;">
                            <div id="occupationContainer">
                                @foreach($clientOccupations as $index => $occupation)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Occupation" onclick="removeOccupationField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="occupation_id[{{ $index }}]" value="{{ $occupation->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Skill Assessment</label>
                                                <select name="skill_assessment_hidden[{{ $index }}]" class="skill-assessment-select">
                                                    <option value="">Select</option>
                                                    <option value="Yes" {{ $occupation->skill_assessment == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                    <option value="No" {{ $occupation->skill_assessment == 'No' ? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Nominated Occupation</label>
                                                <input type="text" name="nomi_occupation[{{ $index }}]" class="nomi_occupation" value="{{ $occupation->nomi_occupation }}" placeholder="Enter Occupation">
                                                <div class="autocomplete-items"></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Occupation Code (ANZSCO)</label>
                                                <input type="text" name="occupation_code[{{ $index }}]" class="occupation_code" value="{{ $occupation->occupation_code }}" placeholder="Enter Code">
                                            </div>
                                            <div class="form-group">
                                                <label>Assessing Authority</label>
                                                <input type="text" name="list[{{ $index }}]" class="list" value="{{ $occupation->list }}" placeholder="e.g., ACS, VETASSESS">
                                            </div>
                                            <div class="form-group">
                                                <label>Target Visa Subclass</label>
                                                <input type="text" name="visa_subclass[{{ $index }}]" class="visa_subclass" value="{{ $occupation->visa_subclass }}" placeholder="e.g., 189, 190">
                                            </div>
                                            <div class="form-group">
                                                <label>Assessment Date</label>
                                                <input type="text" name="dates[{{ $index }}]" class="dates date-picker" value="{{ $occupation->dates ? date('d/m/Y', strtotime($occupation->dates)) : '' }}" placeholder="dd/mm/yyyy">
                                            </div>
                                            <div class="form-group">
                                                <label>Expiry Date</label>
                                                <input type="text" name="expiry_dates[{{ $index }}]" class="expiry_dates date-picker" value="{{ $occupation->expiry_dates ? date('d/m/Y', strtotime($occupation->expiry_dates)) : '' }}" placeholder="dd/mm/yyyy">
                                            </div>
                                            <div class="form-group">
                                                <label>Reference No</label>
                                                <input type="text" name="occ_reference_no[{{ $index }}]" value="{{ $occupation->occ_reference_no }}" placeholder="Enter Reference No.">
                                            </div>
                                            <div class="form-group" style="align-items: center;">
                                                <label style="margin-bottom: 0;">Relevant Occupation</label>
                                                <input type="checkbox" name="relevant_occupation_hidden[{{ $index }}]" value="1" {{ $occupation->relevant_occupation ? 'checked' : '' }} style="margin-left: 10px;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addOccupation()"><i class="fas fa-plus-circle"></i> Add Occupation</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveOccupationInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('occupationInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- English Test Scores Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-language"></i> English Test Scores</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('testScoreInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addTestScore()" title="Add Test Score">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="testScoreInfoSummary" class="summary-view">
                            @if($testScores->count() > 0)
                                <div class="summary-grid">
                                    @foreach($testScores as $index => $testScore)
                                        <div class="summary-item">
                                            <span class="summary-label">Test Type:</span>
                                            <span class="summary-value">{{ $testScore->test_type ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Listening:</span>
                                            <span class="summary-value">{{ $testScore->listening ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Reading:</span>
                                            <span class="summary-value">{{ $testScore->reading ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Writing:</span>
                                            <span class="summary-value">{{ $testScore->writing ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Speaking:</span>
                                            <span class="summary-value">{{ $testScore->speaking ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Overall:</span>
                                            <span class="summary-value">{{ $testScore->overall_score ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Test Date:</span>
                                            <span class="summary-value">{{ $testScore->test_date ? date('d/m/Y', strtotime($testScore->test_date)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Reference No:</span>
                                            <span class="summary-value">{{ $testScore->test_reference_no ?: 'Not set' }}</span>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="summary-divider"></div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="no-data-message">
                                    <p>No test score information available.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="testScoreInfoEdit" class="edit-view" style="display: none;">
                            <div id="testScoresContainer">
                                @foreach($testScores as $index => $testScore)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Test" onclick="removeTestScoreField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="test_score_id[{{ $index }}]" value="{{ $testScore->id }}">
                                        <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px;">
                                            <div class="form-group">
                                                <label>Test Type</label>
                                                <select name="test_type_hidden[{{ $index }}]" class="test-type-selector" onchange="updateTestScoreValidation(this, {{ $index }})">
                                                    <option value="">Select Test Type</option>
                                                    <option value="IELTS" {{ $testScore->test_type == 'IELTS' ? 'selected' : '' }}>IELTS</option>
                                                    <option value="IELTS_A" {{ $testScore->test_type == 'IELTS_A' ? 'selected' : '' }}>IELTS Academic</option>
                                                    <option value="PTE" {{ $testScore->test_type == 'PTE' ? 'selected' : '' }}>PTE</option>
                                                    <option value="TOEFL" {{ $testScore->test_type == 'TOEFL' ? 'selected' : '' }}>TOEFL</option>
                                                    <option value="CAE" {{ $testScore->test_type == 'CAE' ? 'selected' : '' }}>CAE</option>
                                                    <option value="OET" {{ $testScore->test_type == 'OET' ? 'selected' : '' }}>OET</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Listening</label>
                                                <input type="text" name="listening[{{ $index }}]" class="listening" value="{{ $testScore->listening }}" placeholder="Score" maxlength="5">
                                            </div>
                                            <div class="form-group">
                                                <label>Reading</label>
                                                <input type="text" name="reading[{{ $index }}]" class="reading" value="{{ $testScore->reading }}" placeholder="Score" maxlength="5">
                                            </div>
                                            <div class="form-group">
                                                <label>Writing</label>
                                                <input type="text" name="writing[{{ $index }}]" class="writing" value="{{ $testScore->writing }}" placeholder="Score" maxlength="5">
                                            </div>
                                            <div class="form-group">
                                                <label>Speaking</label>
                                                <input type="text" name="speaking[{{ $index }}]" class="speaking" value="{{ $testScore->speaking }}" placeholder="Score" maxlength="5">
                                            </div>
                                            <div class="form-group">
                                                <label>Overall</label>
                                                <input type="text" name="overall_score[{{ $index }}]" class="overall_score" value="{{ $testScore->overall_score }}" placeholder="Overall" maxlength="5">
                                            </div>
                                            <div class="form-group">
                                                <label>Test Date</label>
                                                <input type="text" name="test_date[{{ $index }}]" class="test_date date-picker" value="{{ $testScore->test_date ? date('d/m/Y', strtotime($testScore->test_date)) : '' }}" placeholder="dd/mm/yyyy">
                                            </div>
                                            <div class="form-group">
                                                <label>Reference No</label>
                                                <input type="text" name="test_reference_no[{{ $index }}]" value="{{ $testScore->test_reference_no }}" placeholder="Reference No.">
                                            </div>
                                            <div class="form-group" style="align-items: center;">
                                                <label style="margin-bottom: 0;">Relevant Test</label>
                                                <input type="checkbox" name="relevant_test_hidden[{{ $index }}]" value="1" {{ $testScore->relevant_test ? 'checked' : '' }} style="margin-left: 10px;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addTestScore()"><i class="fas fa-plus-circle"></i> Add Test Score</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveTestScoreInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('testScoreInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Other Information Section -->
                <section id="otherInformationSection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('additionalInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="additionalInfoSummary" class="summary-view">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">NAATI Test:</span>
                                    <span class="summary-value">{{ $fetchedData->naati_test ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">NAATI Date:</span>
                                    <span class="summary-value">{{ $fetchedData->naati_date ? date('d/m/Y', strtotime($fetchedData->naati_date)) : 'Not set' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">PY Test:</span>
                                    <span class="summary-value">{{ $fetchedData->py_test ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">PY Date:</span>
                                    <span class="summary-value">{{ $fetchedData->py_date ? date('d/m/Y', strtotime($fetchedData->py_date)) : 'Not set' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit View -->
                        <div id="additionalInfoEdit" class="edit-view" style="display: none;">
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
                            
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveAdditionalInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('additionalInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Character Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-shield-alt"></i> Character Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('characterInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addCharacterRow('characterContainer', 'character_detail')" title="Add Character">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="characterInfoSummary" class="summary-view">
                            @if($clientCharacters->count() > 0)
                                <div class="summary-grid">
                                    @foreach($clientCharacters as $index => $character)
                                        <div class="summary-item">
                                            <span class="summary-label">Detail:</span>
                                            <span class="summary-value">{{ $character->detail ?: 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No character information added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="characterInfoEdit" class="edit-view" style="display: none;">
                            <div id="characterContainer">
                                @foreach($clientCharacters as $index => $character)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove Character" onclick="removeCharacterField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="character_id[{{ $index }}]" value="{{ $character->id }}">
                                        <div class="content-grid">
                                            <div class="form-group">
                                                <label>Detail</label>
                                                <textarea name="character_detail[{{ $index }}]" rows="2" placeholder="Detail">{{ $character->detail }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="add-item-btn" onclick="addCharacterRow('characterContainer', 'character_detail')"><i class="fas fa-plus-circle"></i> Add Character</button>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveCharacterInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('characterInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Family Information Section -->
                <section id="familySection" class="content-section">
                    <!-- Partner Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-heart"></i> Partner</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('partnerInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addPartnerRow('partner')" title="Add Partner">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="partnerInfoSummary" class="summary-view">
                            @php
                                $partners = $clientPartners->where('relationship_type', 'Husband')->merge($clientPartners->where('relationship_type', 'Wife'))->merge($clientPartners->where('relationship_type', 'Ex-Wife'))->merge($clientPartners->where('relationship_type', 'Defacto'));
                            @endphp
                            @if($partners->count() > 0)
                                <div class="summary-grid">
                                    @foreach($partners as $index => $partner)
                                        <div class="summary-item">
                                            <span class="summary-label">Details:</span>
                                            <span class="summary-value">{{ $partner->relatedClient ? $partner->relatedClient->first_name . ' ' . $partner->relatedClient->last_name : $partner->details }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Relationship:</span>
                                            <span class="summary-value">{{ $partner->relationship_type ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Gender:</span>
                                            <span class="summary-value">{{ $partner->gender ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Company Type:</span>
                                            <span class="summary-value">{{ $partner->company_type ?: 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No partner information added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="partnerInfoEdit" class="edit-view" style="display: none;">
                            <div id="partnerContainer">
                                @foreach($partners as $index => $partner)
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
                                                    <input type="email" name="partner_email[{{ $index }}]" placeholder="Email">
                                                </div>
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <input type="text" name="partner_first_name[{{ $index }}]" placeholder="First Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <input type="text" name="partner_last_name[{{ $index }}]" placeholder="Last Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="text" name="partner_phone[{{ $index }}]" placeholder="Phone">
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
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="savePartnerInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('partnerInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>

                    <!-- Children Section -->
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-child"></i> Children</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('childrenInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addPartnerRow('children')" title="Add Child">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="childrenInfoSummary" class="summary-view">
                            @php
                                $children = $clientPartners->whereIn('relationship_type', ['Son', 'Daughter', 'Step Son', 'Step Daughter']);
                            @endphp
                            @if($children->count() > 0)
                                <div class="summary-grid">
                                    @foreach($children as $index => $child)
                                        <div class="summary-item">
                                            <span class="summary-label">Details:</span>
                                            <span class="summary-value">{{ $child->relatedClient ? $child->relatedClient->first_name . ' ' . $child->relatedClient->last_name : $child->details }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Relationship:</span>
                                            <span class="summary-value">{{ $child->relationship_type ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Gender:</span>
                                            <span class="summary-value">{{ $child->gender ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Company Type:</span>
                                            <span class="summary-value">{{ $child->company_type ?: 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No children information added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="childrenInfoEdit" class="edit-view" style="display: none;">
                            <div id="childrenContainer">
                                @foreach($children as $index => $child)
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
                                                    <input type="email" name="children_email[{{ $index }}]" placeholder="Email">
                                                </div>
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <input type="text" name="children_first_name[{{ $index }}]" placeholder="First Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <input type="text" name="children_last_name[{{ $index }}]" placeholder="Last Name">
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="text" name="children_phone[{{ $index }}]" placeholder="Phone">
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
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveChildrenInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('childrenInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- EOI Reference Section -->
                <section id="eoiReferenceSection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-file-alt"></i> EOI References</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('eoiInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="add-section-btn" onclick="addEoiReference()" title="Add EOI Reference">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="eoiInfoSummary" class="summary-view">
                            @if($clientEoiReferences->count() > 0)
                                <div class="summary-grid">
                                    @foreach($clientEoiReferences as $index => $eoi)
                                        <div class="summary-item">
                                            <span class="summary-label">EOI Number:</span>
                                            <span class="summary-value">{{ $eoi->EOI_number ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Subclass:</span>
                                            <span class="summary-value">{{ $eoi->EOI_subclass ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Occupation:</span>
                                            <span class="summary-value">{{ $eoi->EOI_occupation ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Point:</span>
                                            <span class="summary-value">{{ $eoi->EOI_point ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">State:</span>
                                            <span class="summary-value">{{ $eoi->EOI_state ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Submission Date:</span>
                                            <span class="summary-value">{{ $eoi->EOI_submission_date ? date('d/m/Y', strtotime($eoi->EOI_submission_date)) : 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">ROI:</span>
                                            <span class="summary-value">{{ $eoi->EOI_ROI ?: 'Not set' }}</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Password:</span>
                                            <span class="summary-value">{{ $eoi->EOI_password ? '••••••••' : 'Not set' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No EOI references added yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="eoiInfoEdit" class="edit-view" style="display: none;">
                            <div id="eoiReferencesContainer">
                                @foreach($clientEoiReferences as $index => $eoi)
                                    <div class="repeatable-section">
                                        <button type="button" class="remove-item-btn" title="Remove EOI Reference" onclick="removeEoiField(this)"><i class="fas fa-trash"></i></button>
                                        <input type="hidden" name="eoi_id[{{ $index }}]" value="{{ $eoi->id }}">
                                        <div class="content-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                            <div class="form-group">
                                                <label>EOI Number</label>
                                                <input type="text" name="EOI_number[{{ $index }}]" value="{{ $eoi->EOI_number }}" placeholder="EOI Number">
                                            </div>
                                            <div class="form-group">
                                                <label>Subclass</label>
                                                <input type="text" name="EOI_subclass[{{ $index }}]" value="{{ $eoi->EOI_subclass }}" placeholder="Subclass">
                                            </div>
                                            <div class="form-group">
                                                <label>Occupation</label>
                                                <input type="text" name="EOI_occupation[{{ $index }}]" value="{{ $eoi->EOI_occupation }}" placeholder="Occupation">
                                            </div>
                                            <div class="form-group">
                                                <label>Point</label>
                                                <input type="text" name="EOI_point[{{ $index }}]" value="{{ $eoi->EOI_point }}" placeholder="Point">
                                            </div>
                                            <div class="form-group">
                                                <label>State</label>
                                                <input type="text" name="EOI_state[{{ $index }}]" value="{{ $eoi->EOI_state }}" placeholder="State">
                                            </div>
                                            <div class="form-group">
                                                <label>Submission Date</label>
                                                <input type="text" name="EOI_submission_date[{{ $index }}]" value="{{ $eoi->EOI_submission_date ? date('d/m/Y', strtotime($eoi->EOI_submission_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                            </div>
                                            <div class="form-group">
                                                <label>ROI</label>
                                                <input type="text" name="EOI_ROI[{{ $index }}]" value="{{ $eoi->EOI_ROI }}" placeholder="ROI">
                                            </div>
                                            <div class="form-group">
                                                <label>Password</label>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <input type="password" name="EOI_password[{{ $index }}]" value="{{ $eoi->EOI_password }}" placeholder="Password" class="eoi-password-input" data-index="{{ $index }}">
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
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveEoiInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('eoiInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>
                </form>
            </div>
        </div>
    </div>

    <!-- Go to Top Button -->
    <button id="goToTopBtn" class="go-to-top-btn" onclick="scrollToTop()" title="Go to Top">
        <i class="fas fa-chevron-up"></i>
    </button>


    <!-- OTP Verification Modal -->
    <div id="otpVerificationModal" class="modal" style="display: none;">
        <div class="modal-content otp-modal">
            <div class="modal-header">
                <h3>Verify Phone Number</h3>
                <button type="button" class="close-btn" onclick="closeOTPModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="otp-info">
                    <p>We've sent a 6-digit verification code to:</p>
                    <p class="phone-display" id="otpPhoneDisplay"></p>
                    <p class="otp-timer" id="otpTimer">Code expires in <span id="timerCountdown">5:00</span></p>
                    <div class="otp-instruction">
                        <p><strong>Please ask the client to provide the verification code they received via SMS.</strong></p>
                    </div>
                </div>
                
                <div class="otp-input-container">
                    <input type="text" maxlength="1" class="otp-digit" data-index="0" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit" data-index="1" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit" data-index="2" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit" data-index="3" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit" data-index="4" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit" data-index="5" autocomplete="off">
                </div>
                
                <div class="otp-actions">
                    <button type="button" class="btn-resend-otp" id="resendOTPBtn" onclick="resendOTP()" disabled>
                        Resend Code
                    </button>
                    <span class="resend-timer" id="resendTimer" style="display: none;">Resend available in <span id="resendCountdown">30</span>s</span>
                </div>
                
                <div class="otp-messages">
                    <div id="otpErrorMessage" class="error-message" style="display: none;"></div>
                    <div id="otpSuccessMessage" class="success-message" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeOTPModal()">Cancel</button>
                <button type="button" class="btn btn-primary" id="verifyOTPBtn" onclick="verifyOTP()">Verify</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{asset('js/clients/edit-client.js')}}"></script>
    <script src="{{asset('js/address-autocomplete.js')}}"></script>
    {{-- Google Maps library removed - using backend proxy for address autocomplete --}}
    @endpush
@endsection
