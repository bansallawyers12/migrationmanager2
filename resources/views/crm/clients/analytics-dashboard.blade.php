@extends('layouts.crm_client_detail')
@section('title', 'Financial Analytics Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    /* Modern Dashboard Styling */
    .analytics-container {
        background: #f8fafc;
        min-height: 100vh;
        padding: 24px 32px;
    }

    /* Page Header */
    .analytics-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);
    }

    .analytics-header h1 {
        color: white;
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
        letter-spacing: -0.5px;
    }

    .analytics-header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 16px;
        margin: 0;
    }

    /* Date Range Selector */
    .date-range-selector {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 16px 20px;
        margin-top: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .date-range-selector label {
        color: white;
        font-weight: 600;
        margin: 0;
    }

    .date-range-selector input,
    .date-range-selector select {
        background: rgba(255, 255, 255, 0.3);
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 8px;
        color: white;
        padding: 8px 12px;
        font-weight: 500;
    }

    .date-range-selector input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .date-range-selector .btn-apply {
        background: white;
        color: #667eea;
        border: none;
        border-radius: 8px;
        padding: 8px 24px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .date-range-selector .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-card-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .stat-card-icon.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
    .stat-card-icon.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .stat-card-icon.purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .stat-card-icon.red { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
    .stat-card-icon.teal { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white; }

    .stat-card-title {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-card-value {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        line-height: 1;
    }

    .stat-card-subtitle {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 12px;
    }

    .stat-card-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-card-trend.up {
        background: #dcfce7;
        color: #166534;
    }

    .stat-card-trend.down {
        background: #fee2e2;
        color: #991b1b;
    }

    .stat-card-trend.neutral {
        background: #f1f5f9;
        color: #475569;
    }

    /* Chart Cards */
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .chart-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .table-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .analytics-table {
        width: 100%;
        border-collapse: collapse;
    }

    .analytics-table thead {
        background: #f8fafc;
    }

    .analytics-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .analytics-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #475569;
    }

    .analytics-table tr:hover {
        background: #f8fafc;
    }

    .analytics-table .client-name {
        font-weight: 600;
        color: #1e293b;
    }

    .analytics-table .amount {
        font-weight: 700;
        color: #059669;
    }

    /* Quick Links Section */
    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .quick-link-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        border: 2px solid transparent;
    }

    .quick-link-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
        text-decoration: none;
    }

    .quick-link-card i {
        font-size: 32px;
        margin-bottom: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .quick-link-card h4 {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .analytics-container {
            padding: 16px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .chart-grid {
            grid-template-columns: 1fr;
        }

        .quick-links {
            grid-template-columns: repeat(2, 1fr);
        }

        .analytics-header h1 {
            font-size: 24px;
        }
    }

    /* Loading State */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Tabs Styling */
    .analytics-tabs {
        background: white;
        border-radius: 16px;
        padding: 0;
        margin-bottom: 32px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .tabs-nav {
        display: flex;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        overflow-x: auto;
    }

    .tab-item {
        flex: 1;
        min-width: 150px;
        padding: 16px 24px;
        text-align: center;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
        white-space: nowrap;
    }

    .tab-item:hover {
        background: rgba(102, 126, 234, 0.05);
        color: #667eea;
    }

    .tab-item.active {
        color: #667eea;
        background: white;
    }

    .tab-item.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .tab-item i {
        margin-right: 8px;
        font-size: 16px;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .tabs-nav {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .tab-item {
            min-width: 120px;
            padding: 12px 16px;
            font-size: 13px;
        }
    }
</style>
@endsection

@section('content')
<div class="analytics-container">
    <!-- Page Header -->
    <div class="analytics-header">
        <h1><i class="fas fa-chart-line"></i> Financial Analytics Dashboard</h1>
        <p>Comprehensive overview of your financial performance and key metrics</p>
        
        <!-- Date Range Selector -->
        <div class="date-range-selector">
            <form method="GET" action="{{ route('clients.analytics-dashboard') }}" id="dateRangeForm" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; width: 100%;">
                @if($receiptType !== null)
                <input type="hidden" name="receipt_type" value="{{ $receiptType }}">
                @endif
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="quick_select">Quick Select:</label>
                    <select name="quick_select" id="quick_select" onchange="handleQuickSelect(this.value)">
                        <option value="">Custom Range</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="last_90_days">Last 90 Days</option>
                    </select>
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="start_date">From:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="end_date">To:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                
                <button type="submit" class="btn-apply">
                    <i class="fas fa-sync-alt"></i> Apply
                </button>
            </form>
        </div>
    </div>

    <!-- Receipt Type Tabs -->
    <div class="analytics-tabs">
        <div class="tabs-nav">
            <button class="tab-item {{ $receiptType === null ? 'active' : '' }}" data-type="" onclick="switchTab('')">
                <i class="fas fa-chart-pie"></i> All Types
            </button>
            <button class="tab-item {{ $receiptType == 1 ? 'active' : '' }}" data-type="1" onclick="switchTab('1')">
                <i class="fas fa-receipt"></i> Client Receipts
            </button>
            <button class="tab-item {{ $receiptType == 2 ? 'active' : '' }}" data-type="2" onclick="switchTab('2')">
                <i class="fas fa-building"></i> Office Receipts
            </button>
            <button class="tab-item {{ $receiptType == 3 ? 'active' : '' }}" data-type="3" onclick="switchTab('3')">
                <i class="fas fa-file-invoice-dollar"></i> Invoices
            </button>
            <button class="tab-item {{ $receiptType == 4 ? 'active' : '' }}" data-type="4" onclick="switchTab('4')">
                <i class="fas fa-book"></i> Journal Receipts
            </button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="stats-grid">
        <!-- Total Deposits -->
        @if($receiptType === null || $receiptType == 1)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Total Deposits</div>
                    <div class="stat-card-value">${{ number_format($dashboardStats['monthly_stats']['total_deposits'], 2) }}</div>
                    <div class="stat-card-subtitle">{{ $dashboardStats['monthly_stats']['deposit_count'] }} transactions</div>
                </div>
                <div class="stat-card-icon blue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            @if($dashboardStats['monthly_stats']['trends']['deposits']['direction'] != 'neutral')
            <span class="stat-card-trend {{ $dashboardStats['monthly_stats']['trends']['deposits']['direction'] }}">
                <i class="fas fa-arrow-{{ $dashboardStats['monthly_stats']['trends']['deposits']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                {{ $dashboardStats['monthly_stats']['trends']['deposits']['percentage'] }}% vs last period
            </span>
            @endif
        </div>
        @endif

        <!-- Total Fee Transfers -->
        @if($receiptType === null || $receiptType == 1)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Fee Transfers</div>
                    <div class="stat-card-value">${{ number_format($dashboardStats['monthly_stats']['total_fee_transfers'], 2) }}</div>
                    <div class="stat-card-subtitle">This period</div>
                </div>
                <div class="stat-card-icon green">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Total Office Receipts -->
        @if($receiptType === null || $receiptType == 2)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Office Receipts</div>
                    <div class="stat-card-value">${{ number_format($dashboardStats['monthly_stats']['total_office_receipts'], 2) }}</div>
                    <div class="stat-card-subtitle">{{ $dashboardStats['monthly_stats']['office_receipt_count'] }} transactions</div>
                </div>
                <div class="stat-card-icon orange">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            @if($dashboardStats['monthly_stats']['trends']['office_receipts']['direction'] != 'neutral')
            <span class="stat-card-trend {{ $dashboardStats['monthly_stats']['trends']['office_receipts']['direction'] }}">
                <i class="fas fa-arrow-{{ $dashboardStats['monthly_stats']['trends']['office_receipts']['direction'] == 'up' ? 'up' : 'down' }}"></i>
                {{ $dashboardStats['monthly_stats']['trends']['office_receipts']['percentage'] }}% vs last period
            </span>
            @endif
        </div>
        @endif

        <!-- Unallocated Receipts -->
        @if($receiptType === null || $receiptType == 1)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Unallocated Receipts</div>
                    <div class="stat-card-value">{{ $dashboardStats['receipt_stats']['unallocated_count'] }}</div>
                    <div class="stat-card-subtitle">Require attention</div>
                </div>
                <div class="stat-card-icon purple">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <span class="stat-card-trend neutral">
                {{ $dashboardStats['receipt_stats']['allocation_percentage'] }}% allocation rate
            </span>
        </div>
        @endif

        <!-- Average Days to Allocate -->
        @if($receiptType === null || $receiptType == 1)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Avg. Days to Allocate</div>
                    <div class="stat-card-value">{{ number_format($dashboardStats['allocation_metrics']['average_days_to_allocate'], 1) }}</div>
                    <div class="stat-card-subtitle">Processing time</div>
                </div>
                <div class="stat-card-icon teal">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            @if($dashboardStats['allocation_metrics']['old_unallocated_count'] > 0)
            <span class="stat-card-trend down">
                {{ $dashboardStats['allocation_metrics']['old_unallocated_count'] }} receipts > 30 days old
            </span>
            @endif
        </div>
        @endif

        <!-- Unpaid Invoices -->
        @if($receiptType === null || $receiptType == 3)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Unpaid Invoices</div>
                    <div class="stat-card-value">{{ $dashboardStats['invoice_stats']['unpaid_invoices'] }}</div>
                    <div class="stat-card-subtitle">${{ number_format($dashboardStats['invoice_stats']['unpaid_amount'], 2) }} outstanding</div>
                </div>
                <div class="stat-card-icon red">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
            @if($dashboardStats['invoice_stats']['overdue_invoices'] > 0)
            <span class="stat-card-trend down">
                {{ $dashboardStats['invoice_stats']['overdue_invoices'] }} overdue
            </span>
            @endif
        </div>
        @endif

        <!-- Invoice Payment Rate -->
        @if($receiptType === null || $receiptType == 3)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Invoice Payment Rate</div>
                    <div class="stat-card-value">{{ $dashboardStats['invoice_stats']['payment_rate'] }}%</div>
                    <div class="stat-card-subtitle">{{ $dashboardStats['invoice_stats']['paid_invoices'] }} of {{ $dashboardStats['invoice_stats']['total_invoices'] }} paid</div>
                </div>
                <div class="stat-card-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Total Invoices Issued -->
        @if($receiptType === null || $receiptType == 3)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Invoices Issued</div>
                    <div class="stat-card-value">${{ number_format($dashboardStats['monthly_stats']['total_invoices_issued'], 2) }}</div>
                    <div class="stat-card-subtitle">{{ $dashboardStats['monthly_stats']['invoice_count'] }} invoices</div>
                </div>
                <div class="stat-card-icon blue">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- Total Journal Receipts -->
        @if($receiptType === null || $receiptType == 4)
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Journal Receipts</div>
                    <div class="stat-card-value">${{ number_format($dashboardStats['monthly_stats']['total_journal_receipts'] ?? 0, 2) }}</div>
                    <div class="stat-card-subtitle">{{ $dashboardStats['monthly_stats']['journal_receipt_count'] ?? 0 }} transactions</div>
                </div>
                <div class="stat-card-icon teal">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Charts Section -->
    <div class="chart-grid">
        <!-- Trend Chart -->
        <div class="chart-card" style="grid-column: span 2;">
            <h3 class="chart-card-title">
                <i class="fas fa-chart-line"></i> 6-Month Financial Trend
            </h3>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="chart-card">
            <h3 class="chart-card-title">
                <i class="fas fa-credit-card"></i> Payment Methods
            </h3>
            <div class="chart-container">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>

        <!-- Receipt Allocation Status -->
        <div class="chart-card">
            <h3 class="chart-card-title">
                <i class="fas fa-tasks"></i> Receipt Allocation
            </h3>
            <div class="chart-container">
                <canvas id="allocationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Clients Table -->
    @if(count($dashboardStats['top_clients']) > 0)
    <div class="table-card">
        <h3 class="table-card-title">
            <i class="fas fa-trophy"></i> Top Clients by Transaction Volume
        </h3>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Client ID</th>
                    <th>Client Name</th>
                    <th>Total Deposits</th>
                    <th>Transactions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dashboardStats['top_clients'] as $index => $client)
                <tr>
                    <td>
                        @if($index == 0)
                            <i class="fas fa-trophy" style="color: #fbbf24;"></i> #{{ $index + 1 }}
                        @elseif($index == 1)
                            <i class="fas fa-medal" style="color: #94a3b8;"></i> #{{ $index + 1 }}
                        @elseif($index == 2)
                            <i class="fas fa-award" style="color: #cd7f32;"></i> #{{ $index + 1 }}
                        @else
                            #{{ $index + 1 }}
                        @endif
                    </td>
                    <td><strong>{{ $client['client_unique_id'] }}</strong></td>
                    <td class="client-name">{{ $client['name'] }}</td>
                    <td class="amount">${{ number_format($client['total_deposits'], 2) }}</td>
                    <td>{{ $client['transaction_count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Quick Links -->
    <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">
        <i class="fas fa-link"></i> Quick Access
    </h3>
    <div class="quick-links">
        <a href="{{ route('clients.clientreceiptlist') }}" class="quick-link-card">
            <i class="fas fa-receipt"></i>
            <h4>Client Receipts</h4>
        </a>
        <a href="{{ route('clients.invoicelist') }}" class="quick-link-card">
            <i class="fas fa-file-invoice-dollar"></i>
            <h4>Invoice Lists</h4>
        </a>
        <a href="{{ route('clients.officereceiptlist') }}" class="quick-link-card">
            <i class="fas fa-building"></i>
            <h4>Office Receipts</h4>
        </a>
        <a href="{{ route('clients.journalreceiptlist') }}" class="quick-link-card">
            <i class="fas fa-book"></i>
            <h4>Journal Receipts</h4>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Tab Switching Handler
function switchTab(receiptType) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    // Update receipt_type parameter
    if (receiptType === '' || receiptType === null) {
        params.delete('receipt_type');
    } else {
        params.set('receipt_type', receiptType);
    }
    
    // Preserve date range if exists
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    if (startDate) params.set('start_date', startDate);
    if (endDate) params.set('end_date', endDate);
    
    // Reload page with new parameters
    window.location.href = url.pathname + '?' + params.toString();
}

// Quick Date Select Handler
function handleQuickSelect(value) {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const today = new Date();
    
    let startDate, endDate;
    
    switch(value) {
        case 'this_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last_month':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'this_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
            break;
        case 'this_year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
        case 'last_30_days':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 30);
            endDate = today;
            break;
        case 'last_90_days':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 90);
            endDate = today;
            break;
        default:
            return;
    }
    
    if (startDate && endDate) {
        startDateInput.value = startDate.toISOString().split('T')[0];
        endDateInput.value = endDate.toISOString().split('T')[0];
    }
}

// Chart.js Configuration
const chartColors = {
    blue: 'rgba(102, 126, 234, 0.8)',
    green: 'rgba(17, 153, 142, 0.8)',
    orange: 'rgba(240, 147, 251, 0.8)',
    purple: 'rgba(79, 172, 254, 0.8)',
    red: 'rgba(250, 112, 154, 0.8)',
};

// 6-Month Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($dashboardStats['trend_data']['months']),
        datasets: [
            {
                label: 'Deposits',
                data: @json($dashboardStats['trend_data']['deposits']),
                borderColor: chartColors.blue,
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Office Receipts',
                data: @json($dashboardStats['trend_data']['office_receipts']),
                borderColor: chartColors.green,
                backgroundColor: 'rgba(17, 153, 142, 0.1)',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Invoices',
                data: @json($dashboardStats['trend_data']['invoices']),
                borderColor: chartColors.orange,
                backgroundColor: 'rgba(240, 147, 251, 0.1)',
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + context.parsed.y.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Payment Method Chart
const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
const paymentMethods = @json($paymentMethods);
new Chart(paymentMethodCtx, {
    type: 'doughnut',
    data: {
        labels: paymentMethods.map(pm => pm.method),
        datasets: [{
            data: paymentMethods.map(pm => pm.total),
            backgroundColor: [
                chartColors.blue,
                chartColors.green,
                chartColors.orange,
                chartColors.purple,
                chartColors.red,
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.parsed.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            }
        }
    }
});

// Allocation Chart
const allocationCtx = document.getElementById('allocationChart').getContext('2d');
new Chart(allocationCtx, {
    type: 'pie',
    data: {
        labels: ['Allocated', 'Unallocated'],
        datasets: [{
            data: [
                {{ $dashboardStats['receipt_stats']['allocated_count'] }},
                {{ $dashboardStats['receipt_stats']['unallocated_count'] }}
            ],
            backgroundColor: [
                chartColors.green,
                chartColors.orange,
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
@endsection

