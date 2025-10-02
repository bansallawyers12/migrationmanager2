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
                    <button class="btn btn-primary" type="submit" form="editClientForm"><i class="fas fa-save"></i> Save Changes</button>
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
                <button class="tab-button" onclick="openTab(event, 'contactTab')"><i class="fas fa-address-book"></i> Contact</button>
                <button class="tab-button" onclick="openTab(event, 'visaAddressTab')"><i class="fas fa-passport"></i> Visas & Addresses</button>
                <button class="tab-button" onclick="openTab(event, 'skillsHistoryTab')"><i class="fas fa-briefcase"></i> Skills & History</button>
                <button class="tab-button" onclick="openTab(event, 'testsPointsTab')"><i class="fas fa-clipboard-check"></i> Tests & Points</button>
            </div>

            <form id="editClientForm" action="{{ route('admin.clients.edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $fetchedData->id }}">

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
                                <label for="dob">Date of Birth</label>
                                <input type="text" id="dob" name="dob" value="{{ $fetchedData->dob ? date('d/m/Y', strtotime($fetchedData->dob)) : '' }}" class="date-picker" placeholder="dd/mm/yyyy">
                                @error('dob')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="text" id="age" name="age" value="{{ $fetchedData->age }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="clientId">{{ $fetchedData->type == 'lead' ? 'Lead ID' : ($fetchedData->type == 'client' ? 'Client ID' : '') }}</label>
                                <input type="text" id="clientId" name="client_id" value="{{ $fetchedData->client_id }}" readonly>
                                @error('client_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
                                </select>
                                @error('martial_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-passport"></i> Passport Information</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label for="passportCountry">Country of Passport</label>
                                <!--<input type="text" id="passportCountry" name="visa_country[]" value="{{--$fetchedData->country_passport--}}">-->
                                <select id="passportCountry" name="visa_country[]">
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
                                @error('visa_country.0')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Contact Tab -->
                <div id="contactTab" class="tab-content">
                    <section class="form-section">
                        <h3><i class="fas fa-phone"></i> Phone Numbers</h3>
                        <div id="phoneNumbersContainer">
                            @foreach($clientContacts as $index => $contact)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Phone"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="contact_id[{{ $index }}]" value="{{ $contact->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="contact_type_hidden[{{ $index }}]">
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
                                            <div class="input-group">
                                                <input type="text" name="country_code[{{ $index }}]" value="{{ $contact->country_code }}" style="width: 60px;">
                                                <input type="tel" name="phone[{{ $index }}]" value="{{ $contact->phone }}" placeholder="Enter Phone Number">
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
                        <button type="button" class="add-item-btn" onclick="addPhoneNumber()"><i class="fas fa-plus"></i> Add Phone Number</button>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-envelope"></i> Email Addresses</h3>
                        <div id="emailAddressesContainer">
                            @foreach($emails as $index => $email)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Email"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="email_id[{{ $index }}]" value="{{ $email->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="email_type_hidden[{{ $index }}]">
                                                <option value="Personal" {{ $email->email_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                                <option value="Work" {{ $email->email_type == 'Work' ? 'selected' : '' }}>Work</option>
                                                <option value="Business" {{ $email->email_type == 'Business' ? 'selected' : '' }}>Business</option>

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
                        <button type="button" class="add-item-btn" onclick="addEmailAddress()"><i class="fas fa-plus"></i> Add Email Address</button>
                    </section>
                </div>

                <!-- Visas & Addresses Tab -->
                <div id="visaAddressTab" class="tab-content">
                    <section class="form-section">
                        <h3 id="visaDetailsContainerHeading"><i class="fas fa-file-contract"></i> Visa Details</h3>
                        <div id="visaDetailsContainer">
                            @foreach($visaCountries as $index => $visa)
                                <div class="repeatable-section">
                                    <button type="button" class="remove-item-btn" title="Remove Visa"><i class="fas fa-times-circle"></i></button>
                                    <input type="hidden" name="visa_id[{{ $index }}]" value="{{ $visa->id }}">
                                    <div class="content-grid">
                                        <div class="form-group">
                                            <label>Visa Type / Subclass</label>
                                            <select name="visa_type_hidden[{{ $index }}]">
                                                <option value="">Select Visa Type</option>
                                                @foreach(\App\Models\Matter::select('id','title','nick_name')->where('status',1)->orderby('id','ASC')->get() as $matterlist)
                                                <option value="{{ $matterlist->id }}" {{ $visa->visa_type == $matterlist->id ? 'selected' : '' }}>{{ $matterlist->title }} ({{ @$matterlist->nick_name }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Visa Expiry Date</label>
                                            <input type="text" name="visa_expiry_date[{{ $index }}]" value="{{ $visa->visa_expiry_date ? date('d/m/Y', strtotime($visa->visa_expiry_date)) : '' }}" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                                        </div>
                                        <div class="form-group">
                                            <label>Visa Description</label>
                                            <input type="text" name="visa_description[{{ $index }}]" value="{{ $visa->visa_description }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addVisaDetail()"><i class="fas fa-plus"></i> Add Visa Detail</button>
                    </section>

                    <!-- Updated Addresses Section -->
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
                                    <button type="button" class="remove-item-btn" title="Remove Address"><i class="fas fa-times-circle"></i></button>
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
                </div>

                <!-- Skills & History Tab -->
                <div id="skillsHistoryTab" class="tab-content">
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
                                                <option value="11" {{ $qualification->level == '11' ? 'selected' : '' }}>+2</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name[{{ $index }}]" value="{{ $qualification->name }}" placeholder="e.g., B. Engineering">
                                        </div>
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country_hidden[{{ $index }}]">
                                                <option value="India" {{ @$qualification->country == 'India' ? 'selected' : '' }}>India</option>
                                                <option value="Australia" {{ @$qualification->country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                <?php
                                                foreach (\App\Models\Country::all() as $list) {
                                                    // Skip India and Australia since they've already been added manually
                                                    if ($list->name == 'India' || $list->name == 'Australia') {
                                                        continue;
                                                    }
                                                    $selected = (@$qualification->country == $list->name) ? 'selected' : '';
                                                    ?>
                                                    <option value="{{ $list->name }}" {{ $selected }}>{{ $list->name }}</option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
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
                                            <label>Country</label>
                                            <select name="job_country_hidden[{{ $index }}]">
                                                <option value="India" {{ @$experience->job_country == 'India' ? 'selected' : '' }}>India</option>
                                                <option value="Australia" {{ @$experience->job_country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                <?php
                                                foreach (\App\Models\Country::all() as $list) {
                                                    // Skip India and Australia since they've already been added manually
                                                    if ($list->name == 'India' || $list->name == 'Australia') {
                                                        continue;
                                                    }
                                                    $selected = (@$experience->job_country == $list->name) ? 'selected' : '';
                                                    ?>
                                                    <option value="{{ $list->name }}" {{ $selected }}>{{ $list->name }}</option>
                                                    <?php
                                                }
                                                ?>
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
                                            <div class="autocomplete-items"></div> <!-- Container for autocomplete suggestions -->
                                        </div>
                                        <div class="form-group">
                                            <label for="list">Occupation List</label>
                                            <input type="text" name="list[{{ $index }}]" class="list" value="{{ $occupation->list }}" placeholder="e.g., ACS, VETASSESS">
                                        </div>
                                        <div class="form-group">
                                            <label for="occupationCode">Occupation Code (ANZSCO)</label>
                                            <input type="text" name="occupation_code[{{ $index }}]" class="occupation_code" value="{{ $occupation->occupation_code }}" placeholder="Enter Code">
                                        </div>

                                        <div class="form-group">
                                            <label for="visaSubclass">Target Visa Subclass</label>
                                            <input type="text" name="visa_subclass[{{ $index }}]" class="visa_subclass" value="{{ $occupation->visa_subclass }}" placeholder="e.g., 189, 190">
                                        </div>

                                        <div class="form-group" style="display: flex; gap: 10px;">
                                            <div style="flex: 1;">
                                                <label for="dates" style="font-size: 0.85em;">Assessment Date</label>
                                                <input type="text" name="dates[{{ $index }}]" class="dates date-picker" value="{{ $occupation->dates ? date('d/m/Y', strtotime($occupation->dates)) : '' }}" placeholder="dd/mm/yyyy" style="font-size: 0.9em;">
                                            </div>
                                            <div style="flex: 1;text-align: right;margin-right: 15px;">
                                                <label for="expiry_dates" style="font-size: 0.85em;">Expiry Date</label>
                                                <input type="text" name="expiry_dates[{{ $index }}]" class="expiry_dates date-picker" value="{{ $occupation->expiry_dates ? date('d/m/Y', strtotime($occupation->expiry_dates)) : '' }}" placeholder="dd/mm/yyyy" style="font-size: 0.9em;">
                                            </div>
                                        </div>

                                        <div class="form-group" style="align-items: center;">
                                            <label>Relevant Occupation?</label>
                                            <label class="switch">
                                                <input type="checkbox" name="relevant_occupation_hidden[{{ $index }}]" value="1" {{ $occupation->relevant_occupation ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-item-btn" onclick="addOccupation()"><i class="fas fa-plus"></i> Add Occupation</button>
                    </section>
                </div>

                <!-- Tests & Points Tab -->
                <div id="testsPointsTab" class="tab-content">
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
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3><i class="fas fa-calculator"></i> Points Calculation</h3>
                        <div class="content-grid">
                            <div class="form-group">
                                <label>Estimated Total Points</label>
                                <div class="points-display">{{ $fetchedData->total_points }}</div>
                                <input type="hidden" name="total_points" value="{{ $fetchedData->total_points }}">
                            </div>
                            <div class="form-group">
                                <label>Source</label>
                                <select name="source" id="source">
                                    <option value="SubAgent" {{ $fetchedData->source == 'SubAgent' ? 'selected' : '' }}>SubAgent</option>
                                    <option value="Others" {{ $fetchedData->source == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <input type="text" name="type" value="{{ $fetchedData->type }}" placeholder="e.g., Client Type" readonly>
                            </div>
                        </div>
                    </section>

                    <!-- Spouse Details Section -->
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
                                    <input type="text" name="spouse_test_date" value="{{ optional($ClientSpouseDetail)->spouse_test_date ? date('d/m/Y', strtotime($ClientSpouseDetail->spouse_test_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker spouse-test-date">
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
                                    <input type="text" name="spouse_skill_assessment_status" value="{{ optional($ClientSpouseDetail)->spouse_skill_assessment_status ?? '' }}" placeholder="e.g., Completed">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Nominated Occupation</label>
                                    <input type="text" name="spouse_nomi_occupation" value="{{ optional($ClientSpouseDetail)->spouse_nomi_occupation ?? '' }}" placeholder="Enter Occupation">
                                </div>
                                <div class="form-group">
                                    <label>Spouse Assessment Date</label>
                                    <input type="text" name="spouse_assessment_date" value="{{ optional($ClientSpouseDetail)->spouse_assessment_date ? date('d/m/Y', strtotime($ClientSpouseDetail->spouse_assessment_date)) : '' }}" placeholder="dd/mm/yyyy" class="date-picker spouse-assessment-date">
                                    @error('spouse_assessment_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
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
        </style>
    @endpush

    @push('scripts')
    <script>
        // Initialize arrays to track IDs of records marked for deletion
        let phoneNumbersToDelete = [];
        let emailsToDelete = [];

        // Function to toggle Visa Details section based on Country of Passport
        function toggleVisaDetails() {
            const passportCountry = document.getElementById('passportCountry').value;
            const visaDetailsSection = document.getElementById('visaDetailsContainer');
            const visaDetailsContainerHeading = document.getElementById('visaDetailsContainerHeading');
            const addVisaButton = document.querySelector('#visaAddressTab .add-item-btn');

            if (passportCountry === 'Australia') {
                visaDetailsSection.style.display = 'none';
                visaDetailsContainerHeading.style.display = 'none';
                addVisaButton.style.display = 'none';
                // Remove required validation for visa fields
                document.querySelectorAll('.visa-type-field, .visa-expiry-field, .visa-description-field').forEach(field => {
                    field.removeAttribute('required');
                });
            } else {
                visaDetailsSection.style.display = 'block';
                addVisaButton.style.display = 'block';
                // Add required validation for visa fields
                document.querySelectorAll('.visa-type-field').forEach(field => {
                    field.setAttribute('required', 'required');
                });
            }
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleVisaDetails();
        });

        // Run on passport country change
        document.getElementById('passportCountry').addEventListener('change', function() {
            toggleVisaDetails();
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

        // Add Phone Number
        /*function addPhoneNumber() {
            const container = document.getElementById('phoneNumbersContainer');
            const index = container.children.length;
            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Phone" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                    <div class="content-grid">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="contact_type_hidden[${index}]">
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
                            <div class="input-group">
                                <input type="text" name="country_code[${index}]" value="+61" style="width: 60px;">
                                <input type="tel" name="phone[${index}]" placeholder="Enter Phone Number">
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }*/

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
                            <div class="input-group">
                                <input type="text" name="country_code[${index}]" value="+61" style="width: 60px;" class="country-code-input">
                                <input type="tel" name="phone[${index}]" placeholder="Enter Phone Number" class="phone-number-input">
                            </div>
                        </div>
                    </div>
                </div>
            `);
            validatePersonalPhoneNumbers(); // Validate after adding a new phone number
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


        // Add Email Address
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
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email[${index}]" placeholder="Enter Email Address">
                        </div>
                    </div>
                </div>
            `);
            validatePersonalEmailTypes(); // Validate after adding a new email address
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

        // Add Visa Detail (updated to fetch visa types dynamically and ensure date format)
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

            // Insert the new visa detail section with the dynamically populated dropdown
            container.insertAdjacentHTML('beforeend', `
                <div class="repeatable-section">
                    <button type="button" class="remove-item-btn" title="Remove Visa" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                    <div class="content-grid">
                        <div class="form-group">
                            <label>Visa Type / Subclass</label>
                            <select name="visa_type_hidden[${index}]" class="visa-type-field">
                                ${optionsHtml}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Visa Expiry Date</label>
                            <input type="text" name="visa_expiry_date[${index}]" placeholder="dd/mm/yyyy" class="visa-expiry-field date-picker">
                        </div>
                        <div class="form-group">
                            <label>Visa Description</label>
                            <input type="text" name="visa_description[${index}]" class="visa-description-field">
                        </div>
                    </div>
                </div>
            `);

            // Reinitialize datepicker for the newly added field
            initializeDatepickers();
            toggleVisaDetails(); // Ensure the new fields respect the toggle
        }

        // Function to initialize datepickers (updated to use class "date-picker")
        function initializeDatepickers() {
            $('.date-picker').each(function() {
                // If already initialized, destroy previous instance to avoid conflicts
                if ($(this).data('daterangepicker')) {
                    $(this).data('daterangepicker').remove();
                }
                $(this).daterangepicker({
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
                    minDate: '01/01/1000', // Set minimum date to 1000-01-01
                    minYear: 1000, // Restrict years before 1000
                    maxYear: parseInt(moment().format('YYYY')) + 50 // Allow future dates up to 50 years
                });
            });
        }

        // Initialize datepickers on page load
        $(document).ready(function() {
            // Clean up any existing \n in Target Visa Subclass field on page load
            $('.visa_subclass').each(function() {
                var currentValue = $(this).val();
                if (currentValue) {
                    // Remove newlines and trim whitespace
                    $(this).val(currentValue.replace(/(\r\n|\n|\r)/gm, '').trim());
                }
            });

            initializeDatepickers();

            // Autocomplete functionality
            /*$(document).on('input', '.nomi_occupation', function() {
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
                                    var $item = $('<div class="autocomplete-item"></div>')
                                        .text(suggestion.occupation)
                                        .data('occupation', {
                                            occupation_code: suggestion.occupation_code || '',
                                            list: suggestion.list || '',
                                            visa_subclass: suggestion.visa_subclass || '',
                                            dates: suggestion.dates || ''
                                        })
                                        .appendTo($autocomplete);

                                    $item.on('click', function(e) {
                                        e.stopPropagation(); // Prevent click from bubbling up
                                        var occupationData = $(this).data('occupation');
                                        $row.find('.nomi_occupation').val($(this).text());
                                        $row.find('.occupation_code').val(occupationData.occupation_code);
                                        $row.find('.list').val(occupationData.list);
                                        $row.find('.visa_subclass').val(occupationData.visa_subclass);
                                        $row.find('.dates').val(occupationData.dates ? moment(occupationData.dates).format('DD/MM/YYYY') : '');
                                        $autocomplete.empty();

                                        // Reinitialize datepicker for the Assessment Date field in this row
                                        $row.find('.dates').daterangepicker({
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
                }
            });*/

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
                                            dates: suggestion.dates || ''
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
                                        $row.find('.dates').val(occupationData.dates ? moment(occupationData.dates).format('DD/MM/YYYY') : '');
                                        $autocomplete.empty();

                                        // Reinitialize datepicker for the Assessment Date field in this row
                                        $row.find('.dates').daterangepicker({
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
                }
            });

            // Close the autocomplete dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nomi_occupation').length && !$(e.target).closest('.autocomplete-items').length) {
                    $('.autocomplete-items').empty();
                }
            });
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
                    <button type="button" class="remove-item-btn" title="Remove Address" onclick="this.parentElement.remove(); updateCurrentAddressCheckbox();"><i class="fas fa-times-circle"></i></button>
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
                    <button type="button" class="remove-item-btn" title="Remove Qualification" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
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
                                <option value="11">+2</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name[${index}]" placeholder="e.g., B. Engineering">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select name="country_hidden[${index}]">
                                ${countryOptionsHtml}
                            </select>
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
                    <button type="button" class="remove-item-btn" title="Remove Experience" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
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
                            <label>Country</label>
                            <select name="job_country_hidden[${index}]">
                                ${countryOptionsHtml}
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
                    <button type="button" class="remove-item-btn" title="Remove Occupation" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
                    <div class="content-grid">
                        <div class="form-group">
                            <label>Nominated Occupation</label>
                            <input type="text" name="nomi_occupation[${index}]" class="nomi_occupation" placeholder="Enter Occupation">
                            <div class="autocomplete-items"></div>
                        </div>
                         <div class="form-group">
                            <label>Occupation List</label>
                            <input type="text" name="list[${index}]" class="list" placeholder="e.g., ACS, VETASSESS">
                        </div>
                        <div class="form-group">
                            <label>Occupation Code (ANZSCO)</label>
                            <input type="text" name="occupation_code[${index}]" class="occupation_code" placeholder="Enter Code">
                        </div>

                        <div class="form-group">
                            <label>Target Visa Subclass</label>
                            <input type="text" name="visa_subclass[${index}]" class="visa_subclass" placeholder="e.g., 189, 190">
                        </div>

                        <div class="form-group" style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label for="dates" style="font-size: 0.85em;">Assessment Date</label>
                                <input type="text" name="dates[${index}]" class="dates date-picker" placeholder="dd/mm/yyyy" style="font-size: 0.9em;">
                            </div>
                            <div style="flex: 1;text-align: right;margin-right: 15px;">
                                <label for="expiry_dates" style="font-size: 0.85em;">Expiry Date</label>
                                <input type="text" name="expiry_dates[${index}]" class="expiry_dates date-picker" placeholder="dd/mm/yyyy" style="font-size: 0.9em;">
                            </div>
                        </div>


                        <div class="form-group" style="align-items: center;">
                            <label>Relevant Occupation?</label>
                            <label class="switch">
                                <input type="checkbox" name="relevant_occupation_hidden[${index}]" value="1">
                                <span class="slider"></span>
                            </label>
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
                    <button type="button" class="remove-item-btn" title="Remove Test" onclick="this.parentElement.remove();"><i class="fas fa-times-circle"></i></button>
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
        });
        document.getElementById('pyGiven').addEventListener('change', function() {
            document.getElementById('pyDate').style.display = this.checked ? 'block' : 'none';
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

        document.getElementById('naatiGiven').addEventListener('change', function() {
            document.getElementById('naatiDate').style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('pyGiven').addEventListener('change', function() {
            document.getElementById('pyDate').style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById('naatiDate').style.display = document.getElementById('naatiGiven').checked ? 'block' : 'none';
        document.getElementById('pyDate').style.display = document.getElementById('pyGiven').checked ? 'block' : 'none';

        // Ensure the correct tab is active after a validation error
        @if ($errors->has('email') || $errors->has('email.*') || $errors->has('phone') || $errors->has('phone.*'))
            document.getElementById('contactTab').style.display = 'block';
            document.getElementById('personalTab').style.display = 'none';
            document.querySelector('button[onclick="openTab(event, \'contactTab\')]').classList.add('active');
            document.querySelector('button[onclick="openTab(event, \'personalTab\')]').classList.remove('active');
        @endif
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqqYzCiIEXQZbdxCCoTW0FMCeHRLrgtJ4&libraries=places&callback=initGoogleMaps" async defer></script>
@endpush
@endsection
