@extends('layouts.admin_client_detail')
@section('title', 'Client Detail - Modern Design')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{URL::asset('css/bootstrap-datepicker.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

<style>
    /* Modern Design System */
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --background-color: #f8fafc;
        --card-background: #ffffff;
        --border-color: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
    }

    body {
        background: var(--background-color);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Left Sidebar Header */
    .left-sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 350px;
        height: 100vh;
        background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
        color: white;
        padding: 2rem;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .left-sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .client-header-content {
        position: relative;
        z-index: 1;
    }

    .client-id {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .client-name {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        letter-spacing: -0.025em;
        line-height: 1.2;
    }

    .client-subtitle {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 1.5rem;
    }

    .header-actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-lg);
        font-size: 0.875rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.2s ease;
        justify-content: center;
    }

    .status-badge:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .status-badge.active {
        background: var(--success-color);
        color: white;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
        width: 100%;
    }

    .btn-primary-modern {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary-modern:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary-modern {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary-modern:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    /* Main content area adjustment */
    .main-content-wrapper {
        margin-left: 350px;
        padding: 2rem;
        min-height: 100vh;
    }

    /* Original Tab Navigation Style */
    .content-tabs {
        display: flex;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 4px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .content-tabs::-webkit-scrollbar {
        display: none;
    }

    .tab-button {
        flex: 1;
        padding: 12px 16px;
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 120px;
        border-radius: 6px;
        font-size: 14px;
    }

    .tab-button:hover {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
    }

    .tab-button.active {
        background: white;
        color: #007bff;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .tab-button i {
        font-size: 14px;
    }

    /* Content Grid */
    .content-grid-modern {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .main-content-area {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sidebar-area {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Responsive adjustments for left sidebar */
    @media (max-width: 1200px) {
        .left-sidebar {
            width: 300px;
        }
        
        .main-content-wrapper {
            margin-left: 300px;
        }
    }

    @media (max-width: 768px) {
        .left-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding: 1rem;
        }
        
        .main-content-wrapper {
            margin-left: 0;
            padding: 1rem;
        }
        
        .content-grid-modern {
            grid-template-columns: 1fr;
        }
        
        .sidebar-area {
            order: -1;
        }
    }

    /* Cards */
    .card-modern {
        background: var(--card-background);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .card-modern:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .card-header-modern {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: #f8fafc;
    }

    .card-title-modern {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .card-icon {
        width: 20px;
        height: 20px;
        color: var(--primary-color);
    }

    .card-body-modern {
        padding: 1.5rem;
    }

    /* Field Groups */
    .field-group-modern {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
        align-items: center;
    }

    .field-group-modern:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .field-label-modern {
        font-weight: 500;
        color: var(--text-secondary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .field-value-modern {
        color: var(--text-primary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: #dcfce7;
        color: #166534;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .unverified-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: #fef3c7;
        color: #92400e;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Tables */
    .table-modern {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .table-modern th,
    .table-modern td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-modern th {
        background: #f8fafc;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .table-modern td {
        color: var(--text-primary);
        font-weight: 500;
    }

    .table-modern tbody tr:hover {
        background: #f8fafc;
    }

    /* Activity Feed */
    .activity-feed-modern {
        background: var(--card-background);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }

    .activity-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .activity-list {
        max-height: 600px;
        overflow-y: auto;
        padding: 1rem;
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }

    .activity-meta {
        display: flex;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .activity-date {
        font-weight: 500;
    }

    .activity-type {
        background: #e0e7ff;
        color: var(--primary-color);
        padding: 0.125rem 0.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .content-grid-modern {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .sidebar-area {
            order: -1;
        }
        
        .activity-feed-modern {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .client-name {
            font-size: 2rem;
        }
        
        .header-actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .tab-nav {
            flex-wrap: wrap;
        }
        
        .tab-button-modern {
            flex: none;
            min-width: auto;
        }
        
        .field-group-modern {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
    }

    /* Animation Classes */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-in {
        animation: slideIn 0.4s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<!-- Left Sidebar -->
<div class="left-sidebar">
    <div class="client-header-content">
        <div class="client-id">{{ $fetchedData->client_unique_reference_id ?? 'N/A' }}</div>
        <h1 class="client-name">{{ $fetchedData->first_name ?? '' }} {{ $fetchedData->last_name ?? '' }}</h1>
        <div class="client-subtitle">Initial consultation</div>
        
        <div class="header-actions">
            <div class="status-badge active">
                <i class="fas fa-user"></i>
                Client
            </div>
            
            <div class="action-buttons">
                <a href="#" class="btn-modern btn-secondary-modern">
                    <i class="fas fa-envelope"></i>
                    Message
                </a>
                <a href="#" class="btn-modern btn-secondary-modern">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="#" class="btn-modern btn-primary-modern">
                    <i class="fas fa-star"></i>
                    Star
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="main-content-wrapper">
    <!-- Flash Messages -->
    <div class="row">
        <div class="col-12">
            @include('../Elements/flash-message')
        </div>
    </div>

    <!-- Tab Navigation (Original Style) -->
    <nav class="content-tabs">
        <button class="tab-button active" data-tab="personaldetails">
            <i class="fas fa-user"></i>
            Personal Details
        </button>
        <button class="tab-button" data-tab="noteterm">
            <i class="fas fa-sticky-note"></i>
            Notes
        </button>
        <button class="tab-button" data-tab="documentalls">
            <i class="fas fa-folder"></i>
            Documents
        </button>
        <button class="tab-button" data-tab="accounts">
            <i class="fas fa-calculator"></i>
            Accounts
        </button>
        <button class="tab-button" data-tab="conversations">
            <i class="fas fa-envelope"></i>
            Emails
        </button>
        <button class="tab-button" data-tab="formgenerations">
            <i class="fas fa-file-alt"></i>
            Form Generation
        </button>
        <button class="tab-button" data-tab="appointments">
            <i class="fas fa-calendar"></i>
            Appointments
        </button>
        <button class="tab-button" data-tab="application">
            <i class="fas fa-globe"></i>
            Client Portal
        </button>
    </nav>

        <!-- Tab Content -->
        <div class="tab-content-modern">
            <div class="tab-pane-modern active" id="personaldetails-tab">
                <div class="content-grid-modern">
                    <!-- Main Content Area -->
                    <div class="main-content-area">
                        <!-- Personal Information Card -->
                        <div class="card-modern fade-in">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-user card-icon"></i>
                                    Personal Information
                                </h3>
                            </div>
                            <div class="card-body-modern">
                                <div class="field-group-modern">
                                    <div class="field-label-modern">Age</div>
                                    <div class="field-value-modern">
                                        {{ $fetchedData->age ?? 'N/A' }}
                                        @if(isset($fetchedData->age) && $fetchedData->age != '')
                                            <span class="verified-badge">
                                                <i class="fas fa-check"></i>
                                                Verified
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Gender</div>
                                    <div class="field-value-modern">{{ $fetchedData->gender ?? 'N/A' }}</div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Marital Status</div>
                                    <div class="field-value-modern">{{ $fetchedData->marital_status ?? 'N/A' }}</div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Client Email</div>
                                    <div class="field-value-modern">
                                        @if($emails->count() > 0)
                                            @foreach($emails as $email)
                                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                    {{ $email->email }}
                                                    <span class="verified-badge">
                                                        <i class="fas fa-check"></i>
                                                        Verified
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Client Phone</div>
                                    <div class="field-value-modern">
                                        @if($clientContacts->count() > 0)
                                            @foreach($clientContacts as $contact)
                                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                    {{ $contact->phone }}
                                                    <span class="verified-badge">
                                                        <i class="fas fa-check"></i>
                                                        Verified
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Residential Address</div>
                                    <div class="field-value-modern">
                                        @if($clientAddresses->count() > 0)
                                            @foreach($clientAddresses as $address)
                                                <div>{{ $address->address ?? 'N/A' }}</div>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visa Card -->
                        <div class="card-modern slide-in">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-passport card-icon"></i>
                                    Visa Information
                                </h3>
                            </div>
                            <div class="card-body-modern">
                                <div class="field-group-modern">
                                    <div class="field-label-modern">Country Of Passport</div>
                                    <div class="field-value-modern">{{ $fetchedData->country_of_passport ?? 'N/A' }}</div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Visa Type / Stream</div>
                                    <div class="field-value-modern">
                                        {{ $fetchedData->visa_type ?? 'N/A' }}
                                        <span class="verified-badge">
                                            <i class="fas fa-check"></i>
                                            Verified
                                        </span>
                                    </div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Visa Expiry Date</div>
                                    <div class="field-value-modern">{{ $fetchedData->visa_expiry_date ?? 'N/A' }}</div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">Visa Grant Date</div>
                                    <div class="field-value-modern">{{ $fetchedData->visa_grant_date ?? 'N/A' }}</div>
                                </div>

                                <div class="field-group-modern">
                                    <div class="field-label-modern">English Test Score</div>
                                    <div class="field-value-modern">
                                        @if($testScores->count() > 0)
                                            @foreach($testScores as $score)
                                                {{ $score->test_type ?? '' }} A: L{{ $score->listening ?? '' }} R{{ $score->reading ?? '' }} W{{ $score->writing ?? '' }} S{{ $score->speaking ?? '' }} O{{ $score->overall ?? '' }}
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Qualifications Card -->
                        @if($qualifications->count() > 0)
                        <div class="card-modern slide-in">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-graduation-cap card-icon"></i>
                                    Qualifications
                                </h3>
                            </div>
                            <div class="card-body-modern">
                                <table class="table-modern">
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Name</th>
                                            <th>Campus</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($qualifications as $qualification)
                                        <tr>
                                            <td>{{ $qualification->level ?? 'N/A' }}</td>
                                            <td>{{ $qualification->name ?? 'N/A' }}</td>
                                            <td>{{ $qualification->campus ?? 'N/A' }}</td>
                                            <td>{{ $qualification->end_date ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Work Experience Card -->
                        @if($experiences->count() > 0)
                        <div class="card-modern slide-in">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-briefcase card-icon"></i>
                                    Work Experience
                                </h3>
                            </div>
                            <div class="card-body-modern">
                                <table class="table-modern">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Country</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($experiences as $experience)
                                        <tr>
                                            <td>{{ $experience->job_title ?? 'N/A' }}</td>
                                            <td>{{ $experience->country ?? 'N/A' }}</td>
                                            <td>{{ $experience->start_date ?? 'N/A' }}</td>
                                            <td>{{ $experience->end_date ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Relationships Card -->
                        @if($clientFamilyDetails->count() > 0)
                        <div class="card-modern slide-in">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-users card-icon"></i>
                                    Relationships
                                </h3>
                            </div>
                            <div class="card-body-modern">
                                <table class="table-modern">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Relation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientFamilyDetails as $family)
                                        <tr>
                                            <td>{{ $family->name ?? 'N/A' }}</td>
                                            <td>{{ $family->relation ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar Area -->
                    <div class="sidebar-area">
                        <!-- Activity Feed -->
                        <div class="activity-feed-modern">
                            <div class="activity-header">
                                <h3 class="activity-title">
                                    <i class="fas fa-history card-icon"></i>
                                    Activity Feed
                                </h3>
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted); cursor: pointer;">
                                    <input type="checkbox" id="increase-activity-feed-width" style="margin: 0;">
                                    Wide Mode
                                </label>
                            </div>
                            <div class="activity-list">
                                <!-- Sample Activity Items -->
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">Vipul uploaded allowed checklist document</div>
                                        <div class="activity-meta">
                                            <span class="activity-type">Passport</span>
                                            <span class="activity-date">30 Sep 2025, 15:31 PM</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">Super1 added client funds ledger. Reference no-FEE-1627-</div>
                                        <div class="activity-meta">
                                            <span class="activity-type">Financial</span>
                                            <span class="activity-date">27 Sep 2025, 21:11 PM</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">Super1 added Invoice. Reference no-INV-1931-</div>
                                        <div class="activity-meta">
                                            <span class="activity-type">Invoice</span>
                                            <span class="activity-date">27 Sep 2025, 19:38 PM</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">Super1 added office receipt. Reference -REC-162-</div>
                                        <div class="activity-meta">
                                            <span class="activity-type">Receipt</span>
                                            <span class="activity-date">27 Sep 2025, 19:28 PM</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- Close main-content-wrapper -->
</div> <!-- Close container-fluid -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane-modern');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            const targetPane = document.getElementById(targetTab + '-tab');
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Activity feed width toggle
    const widthToggle = document.getElementById('increase-activity-feed-width');
    const activityFeed = document.querySelector('.activity-feed-modern');
    const mainContent = document.querySelector('.main-content-area');

    if (widthToggle && activityFeed && mainContent) {
        widthToggle.addEventListener('change', function() {
            if (this.checked) {
                activityFeed.classList.add('wide-mode');
                mainContent.classList.add('compact-mode');
            } else {
                activityFeed.classList.remove('wide-mode');
                mainContent.classList.remove('compact-mode');
            }
        });
    }

    // Add smooth scrolling to elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards for animation
    document.querySelectorAll('.card-modern').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

@endsection
