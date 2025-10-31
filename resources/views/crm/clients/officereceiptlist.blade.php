@extends('layouts.crm_client_detail')
@section('title', 'Office Receipt List')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Modern Page Styling */
    .listing-container {
        background: #f8fafc;
        min-height: 100vh;
    }

    .listing-section {
        padding-top: 24px !important;
    }

    /* Modern Card Styling */
    .listing-container .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
        background: white;
    }

    /* Modern Header with Gradient */
    .listing-container .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 24px 32px;
        border-bottom: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .listing-container .card-header h4 {
        color: white;
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
        flex: 1;
    }

    /* Modern Button Styling */
    .listing-container .btn {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .listing-container .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .listing-container .btn:active {
        transform: translateY(0);
    }

    .listing-container .btn-theme {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(10px);
    }

    .listing-container .btn-theme:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .listing-container .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .listing-container .Validate_Receipt {
        background: white !important;
        color: #667eea !important;
        font-weight: 700;
    }

    .listing-container .Validate_Receipt:hover {
        background: rgba(255, 255, 255, 0.95) !important;
    }

    /* Modern Filter Panel */
    .listing-container .filter_panel {
        background: #f8fafc;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        display: none;
        border: 1px solid #e2e8f0;
    }

    .listing-container .filter_panel h4 {
        color: #1e293b;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #667eea;
        display: inline-block;
    }

    /* Modern Form Inputs */
    .listing-container .form-group label {
        color: #475569 !important;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .listing-container .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .listing-container .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .listing-container .select2-container--default .select2-selection--single {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        height: 44px;
        padding: 6px 16px;
    }

    .listing-container .select2-container--default .select2-selection--single:focus {
        border-color: #667eea;
    }

    .listing-container .filter-buttons-container {
        margin-top: 20px;
    }

    .listing-container .btn-info {
        background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
        color: white;
    }

    /* Modern Table Styling */
    .listing-container .card-body {
        padding: 32px;
    }

    .listing-container .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 0 1px #e2e8f0;
    }

    .listing-container .table {
        margin-bottom: 0;
        font-size: 14px;
    }

    .listing-container .table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .listing-container .table thead th {
        border: none;
        padding: 16px 20px;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        white-space: nowrap;
    }

    .listing-container .table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .listing-container .table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.001);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .listing-container .table tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        border: none;
        color: #334155;
    }

    /* Modern Status Badges */
    .modern-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-badge.badge-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .modern-badge.badge-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    /* Modern Icons */
    .listing-container .fas.fa-check-circle {
        color: #10b981;
        font-size: 16px;
        margin-right: 6px;
    }

    /* Modern Checkbox */
    .listing-container .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }

    /* Modern Error/Success Messages */
    .listing-container .custom-error-msg {
        border-radius: 12px;
        margin: 0 32px 20px;
        padding: 16px 20px;
        font-weight: 600;
        display: none;
    }

    .listing-container .custom-error-msg.alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 2px solid #10b981;
    }

    /* Modern Pagination */
    .listing-container .card-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 20px 32px;
        border-radius: 0 0 16px 16px;
    }

    .listing-container .pagination {
        margin: 0;
    }

    .listing-container .pagination .page-link {
        border: 2px solid #e2e8f0;
        color: #667eea;
        margin: 0 4px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .listing-container .pagination .page-link:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
        transform: translateY(-2px);
    }

    .listing-container .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }

    /* No Records State */
    .listing-container .table tbody td[colspan] {
        padding: 60px 20px !important;
        text-align: center;
        color: #94a3b8;
        font-size: 16px;
        font-weight: 600;
    }

    /* Amount Styling */
    .listing-container .table tbody td[id^="deposit_"] {
        font-weight: 700;
        color: #059669;
        font-family: 'Courier New', monospace;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .listing-container .card-header {
            padding: 20px;
        }

        .listing-container .card-header h4 {
            font-size: 20px;
            width: 100%;
        }

        .listing-container .card-body {
            padding: 20px;
        }

        .listing-container .filter_panel {
            padding: 20px;
        }

        .listing-container .table {
            font-size: 12px;
        }

        .listing-container .table thead th,
        .listing-container .table tbody td {
            padding: 12px 10px;
        }
    }

    /* Animation for filter panel */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .listing-container .filter_panel {
        animation: slideDown 0.3s ease;
    }

    /* Sortable Column Headers */
    .listing-container .sortable-header {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 30px !important;
        transition: all 0.2s ease;
    }

    .listing-container .sortable-header:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .listing-container .sort-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        display: inline-flex;
        flex-direction: column;
        gap: 2px;
        opacity: 0.3;
        transition: all 0.2s ease;
    }

    .listing-container .sortable-header:hover .sort-icon {
        opacity: 0.6;
    }

    .listing-container .sort-icon i {
        font-size: 8px;
        line-height: 1;
        color: #475569;
    }

    .listing-container .sortable-header.sort-asc .sort-icon {
        opacity: 1;
    }

    .listing-container .sortable-header.sort-asc .sort-icon .fa-caret-up {
        color: #667eea;
        font-size: 10px;
    }

    .listing-container .sortable-header.sort-desc .sort-icon {
        opacity: 1;
    }

    .listing-container .sortable-header.sort-desc .sort-icon .fa-caret-down {
        color: #667eea;
        font-size: 10px;
    }
</style>
@endsection

@section('content')
<div class="listing-container">
    <section class="listing-section" style="padding-top: 40px;">
        <div class="listing-section-body">
            @include('../Elements/flash-message')

            <div class="card">
                <div class="custom-error-msg">
                </div>
                <div class="card-header">
                    <h4>All Offices Receipt List</h4>
                    <div class="d-flex align-items-center">
                        <a href="javascript:;" style="background: #394eea;color: white;"  class="btn btn-theme btn-theme-sm filter_btn mr-2"><i class="fas fa-filter"></i> Filter</a>
                    </div>

                    <button class="btn btn-primary Validate_Receipt" style="background-color: #394eea !important;">
                        <i class="fas fa-check-circle"></i>
                        Validate Receipt
                    </button>
                </div>

                <div class="card-body">

                    <div class="filter_panel">
                        <h4>Search By Details</h4>
                        <form action="{{URL::to('/clients/officereceiptlist')}}" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="client_id" class="col-form-label" style="color:#4a5568 !important;">Client ID</label>
                                        <select name="client_id" id="client_id" class="form-control select2">
                                            <option value="">Select Client</option>
                                            @foreach($clientIds as $client)
                                                <option value="{{ $client->client_id }}" {{ request('client_id') == $client->client_id ? 'selected' : '' }}>
                                                    {{ $client->first_name.' '.$client->last_name.'('.$client->client_unique_id.')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="client_matter_id" class="col-form-label" style="color:#4a5568 !important;">Client Matter ID</label>
                                        <select name="client_matter_id" id="client_matter_id" class="form-control select2">
                                            <option value="">Select Matter</option>
                                            @foreach($matterIds as $matter)
                                                <option value="{{ $matter->client_matter_id }}" {{ request('client_matter_id') == $matter->client_matter_id ? 'selected' : '' }}>
                                                    {{ $matter->client_unique_id }}-{{ $matter->client_unique_matter_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="trans_date" class="col-form-label" style="color:#000;">Date</label>
                                        <input type="text" name="trans_date" value="{{ old('trans_date', Request::get('trans_date')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Date" id="trans_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="amount" class="col-form-label" style="color:#4a5568 !important;">Amount</label>
                                        <input type="text" name="amount" value="{{ old('amount', Request::get('amount')) }}" class="form-control" placeholder="Amount" id="amount">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="validate_receipt" class="col-form-label" style="color:#4a5568 !important;">Validate Receipt</label>
                                        <select name="validate_receipt" id="validate_receipt" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="1" {{ request('validate_receipt') == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ request('validate_receipt') == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="filter-buttons-container">
                                        <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Search</button>
                                        <a class="btn btn-info" href="{{URL::to('/clients/officereceiptlist')}}">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="custom-checkbox custom-checkbox-table custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
                                            <label for="checkbox-all" class="custom-control-label"></label>
                                        </div>
                                    </th>
                                    <!--<th>SNo.</th>-->
                                    <th class="sortable-header {{ request('sort_by') == 'client_id' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="client_id">
                                        Client Id
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'client_matter' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="client_matter">
                                        Client Matter
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'name' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="name">
                                        Name
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'trans_date' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="trans_date">
                                        Trans. Date
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <!--<th>Entry Date</th>-->
                                    <th class="sortable-header {{ request('sort_by') == 'reference' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="reference">
                                        Reference
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'invoice_no' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="invoice_no">
                                        Invoice No
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'payment_method' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="payment_method">
                                        Payment Method
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'amount' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="amount">
                                        Amount
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'validate_receipt' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="validate_receipt">
                                        Receipt Validate
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'validated_by' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="validated_by">
                                        Validate By
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="tdata">
                                @if(@$totalData !== 0)
                                    <?php $i=0; ?>
                                    @foreach (@$lists as $list)
                                        <?php
                                        $client_info = \App\Models\Admin::select('id','first_name','last_name','client_id')->where('id', $list->client_id)->first();
                                        $client_full_name = $client_info ? $client_info->first_name.' '.$client_info->last_name : '-';
                                        $client_id_display = $client_info ? $client_info->client_id : '-';

                                        $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', $list->client_matter_id)->first();
                                        $client_matter_display = $client_matter_info ? $client_id_display.'-'.$client_matter_info->client_unique_matter_no : '-';

                                        $id = $list->id ?? '-';
                                        $trans_date = $list->trans_date ?? '-';
                                        //$entry_date = $list->entry_date ?? '-';
                                        $Reference = $list->trans_no ?? '-';
                                        $invoice_no = $list->invoice_no ?? '-';
                                        $payment_method = $list->payment_method ?? '-';
                                        $deposit_amount = $list->deposit_amount ?? '-';


                                        if(isset($list->voided_or_validated_by) && $list->voided_or_validated_by != ""){
                                            $validate_by = \App\Models\Admin::select('id','first_name','last_name','user_id')->where('id', $list->voided_or_validated_by)->first();
                                            $validate_by_full_name = $validate_by->first_name.' '.$validate_by->last_name;
                                        } else {
                                            $validate_by_full_name = "-";
                                        }?>
                                        <?php
                                        $receipt_validate = ($list->validate_receipt == 1) ? 'Yes' : 'No';
                                        ?>

                                        <tr id="id_{{@$list->id}}">
                                            <td class="text-center">
                                                <div class="custom-checkbox custom-control">
                                                    <input data-id="{{@$list->id}}" data-receiptid="{{@$list->receipt_id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" data-clientid="{{@$list->client_id}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input  your-checkbox" id="checkbox-{{$i}}">
                                                    <label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
                                                </div>
                                            </td>

                                            <!--<td><?php //echo $list->receipt_id;?></td>-->
                                            <td>{{ $client_id_display }}</td>
                                             <td>{{ $client_matter_display }}</td>
                                            <td>{{ $client_full_name }}</td>
                                            <td id="verified_{{@$list->id}}">
                                                <?php
                                                if( $receipt_validate == 'Yes' )
                                                { ?>
                                                <span style="display: inline-flex;">
                                                    <i class="fas fa-check-circle" title="Verified Receipt" style="margin-top: 4px;"></i>
                                                </span>
                                                <?php
                                                } ?>
                                                {{ $trans_date }}
                                            </td>
                                            <td>{{ $Reference }}</td>
                                            <td>{{ $invoice_no }}</td>
                                            <td>{{ $payment_method }}</td>
                                            <td id="deposit_{{@$list->id}}">{{ is_numeric($deposit_amount) ? '$'.number_format((float)$deposit_amount, 2) : '-' }}</td>
                                            <?php
                                            if($list->validate_receipt == 1) {
                                                $color = "color:blue;";
                                            } else {
                                                $color = "color:red;";
                                            }
                                            ?>
                                            <td id="validate_{{@$list->id}}">
                                                <span class="modern-badge {{ $receipt_validate == 'Yes' ? 'badge-success' : 'badge-danger' }}">
                                                    @if($receipt_validate == 'Yes')
                                                        <i class="fas fa-check"></i>
                                                    @else
                                                        <i class="fas fa-times"></i>
                                                    @endif
                                                    {{ $receipt_validate }}
                                                </span>
                                            </td>
                                            <td id="validateby_{{@$list->id}}"><?php echo $validate_by_full_name;?></td> <!-- New field data -->
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" style="text-align: center; padding: 60px 20px;">
                                            <div style="opacity: 0.5;">
                                                <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                                                <div style="font-size: 18px; font-weight: 600; color: #64748b;">No Records Found</div>
                                                <div style="font-size: 14px; color: #94a3b8; margin-top: 8px;">Try adjusting your filters to find what you're looking for</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                    {!! $lists->appends(\Request::except('page'))->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script>
jQuery(document).ready(function($){
     $('.listing-container .filter_btn').on('click', function(){
		$('.listing-container .filter_panel').slideToggle();
	});

    // Initialize Select2 for searchable dropdowns
    $('.listing-container .select2').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%',
        dropdownParent: $('body') // Ensure dropdown appears above other elements
    }).on('select2:open', function() {
        // Ensure dropdown is visible
        $('.select2-dropdown').css('z-index', '9999');
    });

    $('.listing-container #trans_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    $('.listing-container [data-checkboxes]').each(function () {
        var me = $(this),
        group = me.data('checkboxes'),
        role = me.data('checkbox-role');

        me.change(function () {
            var all = $('.listing-container [data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
            checked = $('.listing-container [data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
            dad = $('.listing-container [data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
            total = all.length,
            checked_length = checked.length;
            if (role == 'dad') {
                if (me.is(':checked')) {
                    all.prop('checked', true);

                } else {
                    all.prop('checked', false);

                }
            } else {
                if (checked_length >= total) {
                    dad.prop('checked', true);
                    $('.listing-container .is_checked_client').show();
                    $('.listing-container .is_checked_clientn').hide();
                } else {
                    dad.prop('checked', false);
                    $('.listing-container .is_checked_client').hide();
                    $('.listing-container .is_checked_clientn').show();
                }
            }

        });
    });

    var clickedReceiptIds = [];
    $(document).delegate('.listing-container .your-checkbox', 'click', function(){
        var clicked_receipt_id = $(this).data('id');
        if ($(this).is(':checked')) {
            clickedReceiptIds.push(clicked_receipt_id);
        } else {
            var index2 = clickedReceiptIds.indexOf(clicked_receipt_id);
            if (index2 !== -1) {
                clickedReceiptIds.splice(index2, 1);
            }
        }
    });

    //validate receipt
    $(document).delegate('.listing-container .Validate_Receipt', 'click', function(){
        if ( clickedReceiptIds.length > 0)
        {
            var mergeStr = "Are you sure want to validate these receipt?";
            if (confirm(mergeStr)) {
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/validate_receipt",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {clickedReceiptIds:clickedReceiptIds,receipt_type:2},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        //location.reload(true);
                        var record_data = obj.record_data;
                        $.each(record_data, function(index, subArray) {
                            //console.log('index=='+index);
                            //console.log('subArray=='+subArray.id);
                            $('.listing-container #validate_' + subArray.id +' span')
                                .removeClass('badge-danger')
                                .addClass('modern-badge badge-success')
                                .html('<i class="fas fa-check"></i> Yes');
                            if(subArray.first_name != ""){
                                var validateby_full_name = subArray.first_name+" "+subArray.last_name;
                            } else {
                                var validateby_full_name = "-";
                            }
                            $('.listing-container #validateby_'+subArray.id).text(validateby_full_name);

                            // Add check-circle icon to verified cell
                            $('.listing-container #verified_' + subArray.id).html(
                                '<span style="display: inline-flex;">' +
                                '<i class="fas fa-check-circle" title="Verified Receipt" style="margin-top: 4px; margin-right: 5px;"></i>' +
                                '</span>' + subArray.trans_date
                            );
                        });
                        $('.listing-container .custom-error-msg').text(obj.message);
                        $('.listing-container .custom-error-msg').show();
                        $('.listing-container .custom-error-msg').addClass('alert alert-success');
                    }
                });
            }
        } else {
            alert('Please select atleast 1 receipt.');
        }
    });


    $('.listing-container .cb-element').change(function () {
        if ($('.listing-container .cb-element:checked').length == $('.listing-container .cb-element').length){
            $('.listing-container #checkbox-all').prop('checked',true);
        } else {
            $('.listing-container #checkbox-all').prop('checked',false);
        }
    });

    // Sortable column headers
    $('.listing-container .sortable-header').on('click', function() {
        var sortBy = $(this).data('sort');
        var currentUrl = new URL(window.location.href);
        var currentSortBy = currentUrl.searchParams.get('sort_by');
        var currentSortOrder = currentUrl.searchParams.get('sort_order');
        
        // Determine new sort order
        var newSortOrder = 'asc';
        if (currentSortBy === sortBy && currentSortOrder === 'asc') {
            newSortOrder = 'desc';
        }
        
        // Set sort parameters
        currentUrl.searchParams.set('sort_by', sortBy);
        currentUrl.searchParams.set('sort_order', newSortOrder);
        
        // Redirect to new URL
        window.location.href = currentUrl.toString();
    });
});
</script>
@endpush
