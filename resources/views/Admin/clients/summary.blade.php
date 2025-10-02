<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Summary - {{ $fetchedData->first_name }} {{ $fetchedData->last_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .summary-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .summary-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .summary-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        .summary-header p {
            margin: 10px 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .summary-content {
            padding: 30px;
        }
        .section {
            margin-bottom: 40px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }
        .section-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: #495057;
            font-size: 1.1rem;
        }
        .section-header i {
            margin-right: 10px;
            color: #667eea;
        }
        .section-content {
            padding: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .info-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 150px;
            margin-right: 15px;
        }
        .info-value {
            color: #212529;
            flex: 1;
        }
        .info-value i {
            margin-right: 5px;
            color: #28a745;
        }
        .info-value .unverified {
            color: #dc3545;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .points-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .point-item {
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }
        .point-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .point-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        .print-button:hover {
            background: #5a6fd8;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .summary-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .summary-header h1 {
                font-size: 2rem;
            }
            .points-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Summary
    </button>

    <div class="summary-container">
        <div class="summary-header">
            <h1>Client Summary</h1>
            <p>{{ $fetchedData->first_name }} {{ $fetchedData->last_name }}</p>
            <p>Client ID: {{ $fetchedData->client_id ?? 'N/A' }}</p>
        </div>

        <div class="summary-content">
            <!-- Points Section -->
            @if($ClientPoints && count($ClientPoints) > 0)
            <div class="points-section">
                <h3 style="margin-bottom: 20px; text-align: center;">Points Summary</h3>
                <div class="points-grid">
                    @foreach($ClientPoints as $point)
                    <div class="point-item">
                        <div class="point-value">{{ $point->calculate_point ?? 0 }}</div>
                        <div class="point-label">{{ $point->item_type ?? 'N/A' }}</div>
                        @if($point->value)
                        <div class="point-label">({{ $point->value }})</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Personal Information -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-user"></i> Personal Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Full Name:</div>
                            <div class="info-value">{{ $fetchedData->first_name }} {{ $fetchedData->last_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Age:</div>
                            <div class="info-value">
                                @if(isset($fetchedData->age) && $fetchedData->age != '')
                                    {{ $fetchedData->age }}
                                    @php
                                        $verifiedDob = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('dob_verified_date')->first();
                                    @endphp
                                    @if($verifiedDob)
                                        <i class="fas fa-check-circle"></i>
                                    @else
                                        <i class="far fa-circle unverified"></i>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Gender:</div>
                            <div class="info-value">{{ $fetchedData->gender ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Marital Status:</div>
                            <div class="info-value">{{ $fetchedData->martial_status ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Date of Birth:</div>
                            <div class="info-value">
                                @if($fetchedData->dob && $fetchedData->dob != '0000-00-00')
                                    {{ \Carbon\Carbon::parse($fetchedData->dob)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status:</div>
                            <div class="info-value">
                                <span class="status-badge {{ $fetchedData->status == 'Active' ? 'status-active' : 'status-inactive' }}">
                                    {{ $fetchedData->status ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-address-book"></i> Contact Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <!-- Emails -->
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Email Addresses:</div>
                            <div class="info-value">
                                @php
                                    if( \App\Models\ClientEmail::where('client_id', $fetchedData->id)->exists()) {
                                        $clientEmails = \App\Models\ClientEmail::select('email','email_type')->where('client_id', $fetchedData->id)->get();
                                    } else {
                                        if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                            $clientEmails = \App\Models\Admin::select('email','email_type')->where('id', $fetchedData->id)->get();
                                        } else {
                                            $clientEmails = array();
                                        }
                                    }
                                @endphp
                                @if(!empty($clientEmails) && count($clientEmails) > 0)
                                    @foreach($clientEmails as $emailVal)
                                        @php
                                            $verifiedEmail = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('email_verified_date')->first();
                                        @endphp
                                        <div style="margin-bottom: 5px;">
                                            {{ $emailVal->email }}
                                            @if(isset($emailVal->email_type) && $emailVal->email_type == "Personal")
                                                @if($verifiedEmail)
                                                    <i class="fas fa-check-circle"></i>
                                                @else
                                                    <i class="far fa-circle unverified"></i>
                                                @endif
                                            @endif
                                            @if(isset($emailVal->email_type) && $emailVal->email_type != "")
                                                <small>({{ $emailVal->email_type }})</small>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>

                        <!-- Phone Numbers -->
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Phone Numbers:</div>
                            <div class="info-value">
                                @php
                                    if( \App\Models\ClientContact::where('client_id', $fetchedData->id)->exists()) {
                                        $clientContacts = \App\Models\ClientContact::select('phone','country_code','contact_type')->where('client_id', $fetchedData->id)->where('contact_type', '!=', 'Not In Use')->get();
                                    } else {
                                        if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                            $clientContacts = \App\Models\Admin::select('phone','country_code','contact_type')->where('id', $fetchedData->id)->get();
                                        } else {
                                            $clientContacts = array();
                                        }
                                    }
                                @endphp
                                @if(!empty($clientContacts) && count($clientContacts) > 0)
                                    @foreach($clientContacts as $conVal)
                                        @php
                                            $verifiedNumber = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('phone_verified_date')->first();
                                            $country_code = isset($conVal->country_code) && $conVal->country_code != "" ? $conVal->country_code : "";
                                        @endphp
                                        <div style="margin-bottom: 5px;">
                                            {{ $country_code }}{{ $conVal->phone }}
                                            @if(isset($conVal->contact_type) && $conVal->contact_type == "Personal")
                                                @if($verifiedNumber)
                                                    <i class="fas fa-check-circle"></i>
                                                @else
                                                    <i class="far fa-circle unverified"></i>
                                                @endif
                                            @endif
                                            @if(isset($conVal->contact_type) && $conVal->contact_type != "")
                                                <small>({{ $conVal->contact_type }})</small>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Residential Address:</div>
                            <div class="info-value">
                                @php
                                    $postcode_Info = App\Models\ClientAddress::select('zip','address')->where('client_id', $fetchedData->id)->latest('id')->first();
                                @endphp
                                @if($postcode_Info && $postcode_Info->zip != "")
                                    {{ $postcode_Info->zip }}
                                @else
                                    N/A
                                @endif
                                @if($postcode_Info && $postcode_Info->address != "")
                                    / {{ $postcode_Info->address }}
                                @else
                                    / N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visa Information -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-passport"></i> Visa Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        @php
                            $visa_Info = App\Models\ClientVisaCountry::select('visa_country','visa_type','visa_expiry_date','visa_grant_date','visa_description')->where('client_id', $fetchedData->id)->latest('id')->first();
                        @endphp
                        <div class="info-item">
                            <div class="info-label">Country of Passport:</div>
                            <div class="info-value">{{ $visa_Info && $visa_Info->visa_country != "" ? $visa_Info->visa_country : 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Visa Type:</div>
                            <div class="info-value">
                                @if($visa_Info && $visa_Info->visa_type != "")
                                    @php
                                        $Matter_get = App\Models\Matter::select('id','title','nick_name')->where('id',$visa_Info->visa_type)->first();
                                    @endphp
                                    @if(!empty($Matter_get))
                                        @php
                                            $verifiedVisa = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('visa_expiry_verified_at')->first();
                                        @endphp
                                        {{ $Matter_get->title }} ({{ $Matter_get->nick_name }})
                                        @if($verifiedVisa)
                                            <i class="fas fa-check-circle"></i>
                                        @else
                                            <i class="far fa-circle unverified"></i>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Visa Expiry Date:</div>
                            <div class="info-value">
                                @if($visa_Info && $visa_Info->visa_expiry_date != "" && $visa_Info->visa_expiry_date != '0000-00-00')
                                    {{ \Carbon\Carbon::parse($visa_Info->visa_expiry_date)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Visa Grant Date:</div>
                            <div class="info-value">
                                @if($visa_Info && $visa_Info->visa_grant_date != "" && $visa_Info->visa_grant_date != '0000-00-00')
                                    {{ \Carbon\Carbon::parse($visa_Info->visa_grant_date)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Visa Description:</div>
                            <div class="info-value">{{ $visa_Info && $visa_Info->visa_description != "" ? $visa_Info->visa_description : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Occupation Information -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-briefcase"></i> Occupation Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        @php
                            $clientOccupation_Info = App\Models\ClientOccupation::select('skill_assessment','nomi_occupation','occupation_code','list','visa_subclass','dates')->where('client_id', $fetchedData->id)->latest('id')->first();
                        @endphp
                        <div class="info-item">
                            <div class="info-label">NOMI Occupation:</div>
                            <div class="info-value">{{ $clientOccupation_Info && $clientOccupation_Info->nomi_occupation != "" ? $clientOccupation_Info->nomi_occupation : 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Occupation Code:</div>
                            <div class="info-value">{{ $clientOccupation_Info && $clientOccupation_Info->occupation_code != "" ? $clientOccupation_Info->occupation_code : 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">List:</div>
                            <div class="info-value">{{ $clientOccupation_Info && $clientOccupation_Info->list != "" ? $clientOccupation_Info->list : 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Visa Subclass:</div>
                            <div class="info-value">{{ $clientOccupation_Info && $clientOccupation_Info->visa_subclass != "" ? $clientOccupation_Info->visa_subclass : 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Skill Assessment:</div>
                            <div class="info-value">{{ $clientOccupation_Info && $clientOccupation_Info->skill_assessment != "" ? $clientOccupation_Info->skill_assessment : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- English Test Scores -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-language"></i> English Test Scores
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        @php
                            $clientTest_Info = App\Models\ClientTestScore::select('test_type','listening','reading','writing','speaking','overall_score','test_date')->where('client_id', $fetchedData->id)->latest('id')->first();
                        @endphp
                        @if($clientTest_Info && $clientTest_Info->test_type != "")
                            <div class="info-item">
                                <div class="info-label">Test Type:</div>
                                <div class="info-value">{{ $clientTest_Info->test_type }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Listening:</div>
                                <div class="info-value">{{ $clientTest_Info->listening ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Reading:</div>
                                <div class="info-value">{{ $clientTest_Info->reading ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Writing:</div>
                                <div class="info-value">{{ $clientTest_Info->writing ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Speaking:</div>
                                <div class="info-value">{{ $clientTest_Info->speaking ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Overall Score:</div>
                                <div class="info-value">{{ $clientTest_Info->overall_score ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Test Date:</div>
                                <div class="info-value">
                                    @if($clientTest_Info->test_date && $clientTest_Info->test_date != '0000-00-00')
                                        {{ \Carbon\Carbon::parse($clientTest_Info->test_date)->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="info-item" style="grid-column: 1 / -1;">
                                <div class="info-label">Test Scores:</div>
                                <div class="info-value">No test scores available</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Qualifications -->
            @php
                $clientQualification_Info = App\Models\ClientQualification::select('level','name','qual_campus','finish_date')->where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
            @endphp
            @if(!empty($clientQualification_Info) && $clientQualification_Info->count() > 0)
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-graduation-cap"></i> Qualifications
                </div>
                <div class="section-content">
                    @foreach($clientQualification_Info as $qualification)
                    <div class="info-item" style="margin-bottom: 15px;">
                        <div class="info-label">Qualification {{ $loop->iteration }}:</div>
                        <div class="info-value">
                            <strong>{{ $qualification->name ?: 'N/A' }}</strong><br>
                            <small>Level: {{ $qualification->level ?: 'N/A' }}</small><br>
                            <small>Campus: {{ $qualification->qual_campus ?: 'N/A' }}</small><br>
                            <small>End Date: {{ $qualification->finish_date ? \Carbon\Carbon::parse($qualification->finish_date)->format('d/m/Y') : 'N/A' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Experience -->
            @php
                $clientExperience_Info = App\Models\ClientExperience::select('job_title','job_country','job_start_date','job_finish_date')->where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
            @endphp
            @if(!empty($clientExperience_Info) && $clientExperience_Info->count() > 0)
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-briefcase"></i> Work Experience
                </div>
                <div class="section-content">
                    @foreach($clientExperience_Info as $experience)
                    <div class="info-item" style="margin-bottom: 15px;">
                        <div class="info-label">Experience {{ $loop->iteration }}:</div>
                        <div class="info-value">
                            <strong>{{ $experience->job_title ?: 'N/A' }}</strong><br>
                            <small>Country: {{ $experience->job_country ?: 'N/A' }}</small><br>
                            <small>Duration: {{ $experience->job_start_date ? \Carbon\Carbon::parse($experience->job_start_date)->format('d/m/Y') : 'N/A' }} - {{ $experience->job_finish_date ? \Carbon\Carbon::parse($experience->job_finish_date)->format('d/m/Y') : 'Present' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Family Details -->
            @php
                $clientFamilyDetails_Info = App\Models\ClientRelationship::where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
            @endphp
            @if(!empty($clientFamilyDetails_Info) && $clientFamilyDetails_Info->count() > 0)
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-users"></i> Family Details
                </div>
                <div class="section-content">
                    @foreach($clientFamilyDetails_Info as $family)
                    <div class="info-item" style="margin-bottom: 15px;">
                        <div class="info-label">Family Member {{ $loop->iteration }}:</div>
                        <div class="info-value">
                            @php
                                if(isset($family->related_client_id) && $family->related_client_id != "") {
                                    // Existing Client - fetch from Admin table
                                    $relatedClientInfo = App\Models\Admin::select('client_id','first_name','last_name','dob')->where('id', $family->related_client_id)->first();
                                    if($relatedClientInfo) {
                                        $relatedClientName = $relatedClientInfo->first_name . ' ' . $relatedClientInfo->last_name;
                                        $relatedClientId = $relatedClientInfo->client_id;
                                        $relatedClientDob = $relatedClientInfo->dob;
                                    } else {
                                        $relatedClientName = 'N/A';
                                        $relatedClientId = 'N/A';
                                        $relatedClientDob = 'N/A';
                                    }
                                } else {
                                    // New Client - use direct fields
                                    $relatedClientName = $family->first_name . ' ' . $family->last_name;
                                    $relatedClientId = 'N/A';
                                    $relatedClientDob = $family->dob;
                                }
                            @endphp
                            <strong>{{ $relatedClientName ?: 'N/A' }}</strong><br>
                            <small>Client ID: {{ $relatedClientId ?: 'N/A' }}</small><br>
                            <small>Relationship: {{ $family->relationship_type ?: 'N/A' }}</small><br>
                            <small>Date of Birth: {{ $relatedClientDob && $relatedClientDob != '0000-00-00' ? \Carbon\Carbon::parse($relatedClientDob)->format('d/m/Y') : 'N/A' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Spouse Details -->
            @php
                $clientSpouseDetail_Info = App\Models\ClientSpouseDetail::where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
            @endphp
            @if(!empty($clientSpouseDetail_Info) && $clientSpouseDetail_Info->count() > 0)
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-heart"></i> Spouse Details
                </div>
                <div class="section-content">
                    @foreach($clientSpouseDetail_Info as $spouse)
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Spouse Name:</div>
                            <div class="info-value">{{ $spouse->spouse_name ?: 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Date of Birth:</div>
                            <div class="info-value">
                                @if($spouse->spouse_dob && $spouse->spouse_dob != '0000-00-00')
                                    {{ \Carbon\Carbon::parse($spouse->spouse_dob)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Passport Number:</div>
                            <div class="info-value">{{ $spouse->spouse_passport_number ?: 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Nationality:</div>
                            <div class="info-value">{{ $spouse->spouse_nationality ?: 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">English Test Score:</div>
                            <div class="info-value">{{ $spouse->spouse_overall_score ?: 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Skill Assessment:</div>
                            <div class="info-value">{{ $spouse->spouse_skill_assessment_status ?: 'N/A' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- EOI Reference Information -->
            @php
                $clientEoi_Info = App\Models\ClientEoiReference::where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
            @endphp
            @if(!empty($clientEoi_Info) && $clientEoi_Info->count() > 0)
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-file-alt"></i> EOI Reference Information
                </div>
                <div class="section-content">
                    @foreach($clientEoi_Info as $Eoi_Info)
                    <div class="info-item" style="margin-bottom: 15px;">
                        <div class="info-label">EOI Reference {{ $loop->iteration }}:</div>
                        <div class="info-value">
                            <strong>Subclass: {{ $Eoi_Info->EOI_subclass ?: 'N/A' }}</strong><br>
                            <small>Occupation: {{ $Eoi_Info->EOI_occupation ?: 'N/A' }}</small><br>
                            <small>Points: {{ $Eoi_Info->EOI_point ?: 'N/A' }}</small><br>
                            <small>State: {{ $Eoi_Info->EOI_state ?: 'N/A' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
