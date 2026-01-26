@extends('layouts.crm_client_detail_dashboard')

@php
    $latestMatterRefNo = null;
    if (isset($fetchedData) && $fetchedData->type === 'client') {
        $latestMatter = \App\Models\ClientMatter::where('client_id', $fetchedData->id)
            ->where('matter_status', 1)
            ->orderByDesc('id')
            ->first();

        if ($latestMatter) {
            $latestMatterRefNo = $latestMatter->client_unique_matter_no;
        }
    }
    
    // Get company data
    $company = $fetchedData->company;
    $contactPerson = $company && $company->contact_person_id ? \App\Models\Admin::find($company->contact_person_id) : null;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address-autocomplete.css') }}">
    <link rel="stylesheet" href="{{asset('css/client-forms.css')}}">
    <link rel="stylesheet" href="{{asset('css/clients/edit-client-components.css')}}">
    {{-- Select2 CSS for contact person search --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- Flatpickr CSS for date pickers in address autocomplete --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="crm-container">
        <div class="main-content">

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
                    <h3><i class="fas fa-building"></i> {{ $fetchedData->type == 'lead' ? 'Edit Company Lead' : 'Edit Company Client' }} : {{ $company ? $company->company_name : 'Unnamed Company' }}</h3>
                    <div class="client-id">
                        {{ $fetchedData->type == 'lead' ? 'Lead ID' : ($fetchedData->type == 'client' ? 'Client ID' : '') }} : {{ $fetchedData->client_id }}
                    </div>
                </div>
                <nav class="nav-menu">
                    <button class="nav-item active" onclick="scrollToSection('companySection')">
                        <i class="fas fa-building"></i>
                        <span>Company Information</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('contactPersonSection')">
                        <i class="fas fa-user-tie"></i>
                        <span>Contact Person</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('addressSection')">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Business Address</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('contactsSection')">
                        <i class="fas fa-phone"></i>
                        <span>Contacts</span>
                    </button>
                    <button class="nav-item" onclick="scrollToSection('otherInformationSection')">
                        <i class="fas fa-info-circle"></i>
                        <span>Other Information</span>
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
                    searchContactPersonRoute: '{{ route("api.search.contact.person") }}',
                    searchAddressRoute: '{{ route("clients.searchAddressFull") }}',
                    getPlaceDetailsRoute: '{{ route("clients.getPlaceDetails") }}',
                    csrfToken: '{{ csrf_token() }}'
                };
                
                // Current client ID for excluding from search results
                window.currentClientId = '{{ $fetchedData->id }}';
                window.currentClientType = @json($fetchedData->type);
                window.latestClientMatterRef = @json($latestMatterRefNo);
            </script>

            <!-- Main Content Area -->
            <div class="main-content-area">
                <form id="editCompanyForm" action="{{ route('clients.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $fetchedData->id }}">
                    <input type="hidden" name="type" value="{{ $fetchedData->type }}">

                <!-- Company Information Section -->
                <section id="companySection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-building"></i> Company Information</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('companyInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="companyInfoSummary" class="summary-view">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">Company Name:</span>
                                    <span class="summary-value">{{ $company ? $company->company_name : 'Not set' }}</span>
                                </div>
                                @if($company && $company->trading_name)
                                <div class="summary-item">
                                    <span class="summary-label">Trading Name:</span>
                                    <span class="summary-value">{{ $company->trading_name }}</span>
                                </div>
                                @endif
                                @if($company && $company->ABN_number)
                                <div class="summary-item">
                                    <span class="summary-label">ABN:</span>
                                    <span class="summary-value">{{ $company->ABN_number }}</span>
                                </div>
                                @endif
                                @if($company && $company->ACN)
                                <div class="summary-item">
                                    <span class="summary-label">ACN:</span>
                                    <span class="summary-value">{{ $company->ACN }}</span>
                                </div>
                                @endif
                                @if($company && $company->company_type)
                                <div class="summary-item">
                                    <span class="summary-label">Business Type:</span>
                                    <span class="summary-value">{{ $company->company_type }}</span>
                                </div>
                                @endif
                                @if($company && $company->company_website)
                                <div class="summary-item">
                                    <span class="summary-label">Website:</span>
                                    <span class="summary-value">
                                        <a href="{{ $company->company_website }}" target="_blank">{{ $company->company_website }}</a>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Edit View -->
                        <div id="companyInfoEdit" class="edit-view hidden">
                            <div class="content-grid">
                                <div class="form-group">
                                    <label for="companyName">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" id="companyName" name="company_name" 
                                           value="{{ old('company_name', $company ? $company->company_name : '') }}" 
                                           required>
                                    @error('company_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="tradingName">Trading Name</label>
                                    <input type="text" id="tradingName" name="trading_name" 
                                           value="{{ old('trading_name', $company ? $company->trading_name : '') }}" 
                                           placeholder="If different from company name">
                                </div>
                                
                                <div class="form-group">
                                    <label for="abn">ABN</label>
                                    <input type="text" id="abn" name="ABN_number" 
                                           value="{{ old('ABN_number', $company ? $company->ABN_number : '') }}" 
                                           placeholder="12 345 678 901"
                                           maxlength="11">
                                    <small class="form-text text-muted">11 digits (spaces will be removed automatically)</small>
                                    @error('ABN_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="acn">ACN</label>
                                    <input type="text" id="acn" name="ACN" 
                                           value="{{ old('ACN', $company ? $company->ACN : '') }}" 
                                           placeholder="123 456 789"
                                           maxlength="9">
                                    <small class="form-text text-muted">9 digits (spaces will be removed automatically)</small>
                                    @error('ACN')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="companyType">Business Type</label>
                                    <select id="companyType" name="company_type">
                                        <option value="">Select Business Type</option>
                                        <option value="Sole Trader" {{ old('company_type', $company ? $company->company_type : '') == 'Sole Trader' ? 'selected' : '' }}>Sole Trader</option>
                                        <option value="Partnership" {{ old('company_type', $company ? $company->company_type : '') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                        <option value="Proprietary Company" {{ old('company_type', $company ? $company->company_type : '') == 'Proprietary Company' ? 'selected' : '' }}>Proprietary Company (Pty Ltd)</option>
                                        <option value="Public Company" {{ old('company_type', $company ? $company->company_type : '') == 'Public Company' ? 'selected' : '' }}>Public Company</option>
                                        <option value="Not-for-Profit" {{ old('company_type', $company ? $company->company_type : '') == 'Not-for-Profit' ? 'selected' : '' }}>Not-for-Profit Organization</option>
                                        <option value="Other" {{ old('company_type', $company ? $company->company_type : '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="companyWebsite">Company Website</label>
                                    <input type="url" id="companyWebsite" name="company_website" 
                                           value="{{ old('company_website', $company ? $company->company_website : '') }}" 
                                           placeholder="https://www.example.com">
                                    @error('company_website')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveCompanyInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('companyInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Primary Contact Person Section -->
                <section id="contactPersonSection" class="content-section">
                    <section class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-user-tie"></i> Primary Contact Person</h3>
                            <div class="section-actions">
                                <button type="button" class="edit-section-btn" onclick="toggleEditMode('contactPersonInfo')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Summary View -->
                        <div id="contactPersonInfoSummary" class="summary-view">
                            @if($contactPerson)
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <span class="summary-label">Name:</span>
                                        <span class="summary-value">
                                            <a href="{{ route('clients.detail', base64_encode(convert_uuencode($contactPerson->id))) }}">
                                                {{ $contactPerson->first_name }} {{ $contactPerson->last_name }}
                                            </a>
                                        </span>
                                    </div>
                                    @if($company && $company->contact_person_position)
                                    <div class="summary-item">
                                        <span class="summary-label">Position:</span>
                                        <span class="summary-value">{{ $company->contact_person_position }}</span>
                                    </div>
                                    @endif
                                    @if($contactPerson->email)
                                    <div class="summary-item">
                                        <span class="summary-label">Email:</span>
                                        <span class="summary-value">
                                            <a href="mailto:{{ $contactPerson->email }}">{{ $contactPerson->email }}</a>
                                        </span>
                                    </div>
                                    @endif
                                    @if($contactPerson->phone)
                                    <div class="summary-item">
                                        <span class="summary-label">Phone:</span>
                                        <span class="summary-value">{{ $contactPerson->phone }}</span>
                                    </div>
                                    @endif
                                    @if($contactPerson->client_id)
                                    <div class="summary-item">
                                        <span class="summary-label">Client ID:</span>
                                        <span class="summary-value">{{ $contactPerson->client_id }}</span>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>No contact person assigned yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit View -->
                        <div id="contactPersonInfoEdit" class="edit-view hidden">
                            <div class="content-grid">
                                <div class="form-group full-width">
                                    <label for="contactPersonSearch">Search Contact Person <span class="text-danger">*</span></label>
                                    <select id="contactPersonSearch" name="contact_person_id" 
                                            class="form-control select2-contact-person" 
                                            data-placeholder="Type phone, email, name, or client ID to search..."
                                            style="width: 100%;"
                                            required>
                                        @if($contactPerson)
                                            <option value="{{ $contactPerson->id }}" selected>
                                                {{ $contactPerson->first_name }} {{ $contactPerson->last_name }} 
                                                ({{ $contactPerson->email }}) - {{ $contactPerson->phone }}
                                            </option>
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">
                                        Search existing clients/leads by phone, email, name, or client ID. Selected person's details will auto-fill below.
                                    </small>
                                    @error('contact_person_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="contactPersonFirstName">First Name <span class="text-danger">*</span></label>
                                    <input type="text" id="contactPersonFirstName" name="contact_person_first_name" 
                                           value="{{ old('contact_person_first_name', $contactPerson ? $contactPerson->first_name : '') }}" 
                                           class="contact-person-field" required readonly>
                                    <small class="form-text text-muted">Auto-filled from selected contact person</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contactPersonLastName">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" id="contactPersonLastName" name="contact_person_last_name" 
                                           value="{{ old('contact_person_last_name', $contactPerson ? $contactPerson->last_name : '') }}" 
                                           class="contact-person-field" required readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contactPersonPosition">Position/Title</label>
                                    <input type="text" id="contactPersonPosition" name="contact_person_position" 
                                           value="{{ old('contact_person_position', $company ? $company->contact_person_position : '') }}" 
                                           placeholder="e.g., HR Manager, Director">
                                </div>
                                
                                <div class="form-group">
                                    <label for="contactPersonPhone">Phone</label>
                                    <input type="text" id="contactPersonPhone" name="contact_person_phone" 
                                           value="{{ old('contact_person_phone', $contactPerson ? $contactPerson->phone : '') }}" 
                                           class="contact-person-field" readonly>
                                    <small class="form-text text-muted">Auto-filled from selected contact person</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contactPersonEmailDisplay">Email</label>
                                    <input type="email" id="contactPersonEmailDisplay" 
                                           value="{{ old('contact_person_email_display', $contactPerson ? $contactPerson->email : '') }}" 
                                           class="contact-person-field" readonly>
                                    <small class="form-text text-muted">Auto-filled from selected contact person</small>
                                </div>
                            </div>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveContactPersonInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('contactPersonInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                <!-- Business Address Section -->
                <section id="addressSection" class="content-section">
                    <x-client-edit.address-section 
                        :clientAddresses="$clientAddresses"
                        :searchRoute="route('clients.searchAddressFull')"
                        :detailsRoute="route('clients.getPlaceDetails')"
                        :csrfToken="csrf_token()"
                    />
                </section>

                <!-- Contacts Section (Phone & Email) -->
                <section id="contactsSection" class="content-section">
                    <!-- Phone Numbers -->
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
                                            @if($contact->canVerify())
                                                @if($contact->is_verified)
                                                    <span class="verified-badge" title="Verified on {{ $contact->verified_at ? $contact->verified_at->format('M j, Y g:i A') : 'Unknown' }}">
                                                        <i class="fas fa-check-circle"></i> Verified
                                                    </span>
                                                @else
                                                    <button type="button" class="btn-verify-phone" onclick="sendOTP({{ $contact->id ?? 'null' }}, '{{ $contact->phone }}', '{{ $contact->country_code }}')" data-contact-id="{{ $contact->id ?? '' }}">
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
                        <div id="phoneNumbersEdit" class="edit-view hidden">
                            <div id="phoneNumbersContainer">
                                @foreach($clientContacts as $index => $contact)
                                    <x-client-edit.phone-number-field :index="$index" :contact="$contact" />
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
                        <div id="emailAddressesEdit" class="edit-view hidden">
                            <div id="emailAddressesContainer">
                                @foreach($emails as $index => $email)
                                    <x-client-edit.email-field :index="$index" :email="$email" />
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
                                    <span class="summary-label">NAATI/CCL Test:</span>
                                    <span class="summary-value">{{ $fetchedData->naati_test ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">NAATI/CCL Date:</span>
                                    <span class="summary-value">{{ $fetchedData->naati_date ? date('d/m/Y', strtotime($fetchedData->naati_date)) : 'Not set' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Professional Year (PY):</span>
                                    <span class="summary-value">{{ $fetchedData->py_test ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">PY Completion Date:</span>
                                    <span class="summary-value">{{ $fetchedData->py_date ? date('d/m/Y', strtotime($fetchedData->py_date)) : 'Not set' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit View -->
                        <div id="additionalInfoEdit" class="edit-view hidden">
                            <div class="content-grid">
                                <div class="form-group">
                                    <label for="naatiTest">NAATI/CCL Test</label>
                                    <select id="naatiTest" name="naati_test">
                                        <option value="0" {{ !$fetchedData->naati_test ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $fetchedData->naati_test ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="naatiDate">NAATI/CCL Date</label>
                                    <input type="text" id="naatiDate" name="naati_date" value="{{ $fetchedData->naati_date ? date('d/m/Y', strtotime($fetchedData->naati_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                </div>
                                <div class="form-group">
                                    <label for="pyTest">Professional Year (PY)</label>
                                    <select id="pyTest" name="py_test">
                                        <option value="0" {{ !$fetchedData->py_test ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $fetchedData->py_test ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pyDate">PY Completion Date</label>
                                    <input type="text" id="pyDate" name="py_date" value="{{ $fetchedData->py_date ? date('d/m/Y', strtotime($fetchedData->py_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker">
                                </div>
                            </div>
                            
                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveAdditionalInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('additionalInfo')">Cancel</button>
                            </div>
                        </div>
                    </section>
                </section>

                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Select2 JS for contact person search --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Flatpickr JS for date pickers in address autocomplete --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{asset('js/clients/edit-client.js')}}"></script>
    <script src="{{asset('js/address-autocomplete.js')}}"></script>
    <script src="{{asset('js/clients/address-regional-codes.js')}}"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize Select2 for contact person search
        $('#contactPersonSearch').select2({
            ajax: {
                url: window.editClientConfig.searchContactPersonRoute,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        exclude_id: window.currentClientId // Exclude current company being edited
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text,
                                first_name: item.first_name,
                                last_name: item.last_name,
                                email: item.email,
                                phone: item.phone,
                                client_id: item.client_id
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: 'Type phone, email, name, or client ID to search...',
            allowClear: true
        });
        
        // Auto-fill contact person details when selected
        $('#contactPersonSearch').on('select2:select', function (e) {
            const data = e.params.data;
            
            // Auto-fill fields
            $('#contactPersonFirstName').val(data.first_name);
            $('#contactPersonLastName').val(data.last_name);
            $('#contactPersonPhone').val(data.phone || '');
            $('#contactPersonEmailDisplay').val(data.email);
            
            // Add visual indicator
            $('.contact-person-field').addClass('field-auto-filled');
        });
        
        // Clear fields when selection is cleared
        $('#contactPersonSearch').on('select2:clear', function (e) {
            $('#contactPersonFirstName').val('');
            $('#contactPersonLastName').val('');
            $('#contactPersonPhone').val('');
            $('#contactPersonEmailDisplay').val('');
            $('.contact-person-field').removeClass('field-auto-filled');
        });
        
        // Format ABN/ACN input (strip non-digits)
        $('#abn, #acn').on('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });
    
    // Save functions (these should be in edit-client.js, but adding here for company-specific saves)
    function saveCompanyInfo() {
        // Submit form with section identifier
        const form = document.getElementById('editCompanyForm');
        const formData = new FormData(form);
        formData.append('section', 'company');
        
        // Use AJAX or form submission
        form.submit();
    }
    
    function saveContactPersonInfo() {
        const form = document.getElementById('editCompanyForm');
        const formData = new FormData(form);
        formData.append('section', 'contact_person');
        form.submit();
    }
    </script>
    @endpush
@endsection
