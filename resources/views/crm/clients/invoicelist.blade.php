@extends('layouts.crm_client_detail')
@section('title', 'Clients Invoice List')

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

    .listing-container .is_checked_client_void_invoice {
        background: white !important;
        color: #667eea !important;
        font-weight: 700;
    }

    .listing-container .is_checked_client_void_invoice:hover {
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

    /* Strike Through for Voided Invoices */
    .listing-container .strike-through {
        opacity: 0.6;
        text-decoration: line-through;
        background: #fee2e2 !important;
    }

    /* Modern Hubdoc Status Badges */
    .hubdoc-badge {
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

    .hubdoc-badge.badge-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .hubdoc-badge.badge-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
        border-radius: 0.25rem;
        display: inline-block;
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
                    <h4>All Clients Invoice List</h4>

                    <div class="d-flex align-items-center">
                        <a href="javascript:;" style="background: #394eea;color: white;"  class="btn btn-theme btn-theme-sm filter_btn mr-2"><i class="fas fa-filter"></i> Filter</a>
                    </div>

                    <button class="btn btn-primary is_checked_client_void_invoice" style="background-color: #394eea !important;">
                        <i class="fas fa-check-circle"></i>
                        Void Invoice
                    </button>
                </div>

                <div class="card-body">
                    <div class="filter_panel">
                        <h4>Search By Details</h4>
                        <form action="{{URL::to('/clients/invoicelist')}}" method="get">
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

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="trans_date" class="col-form-label" style="color:#000;">Date</label>
                                        <input type="text" name="trans_date" value="{{ old('trans_date', Request::get('trans_date')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Date" id="trans_date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="amount" class="col-form-label" style="color:#4a5568 !important;">Amount</label>
                                        <input type="number" name="amount" id="amount" value="{{ old('amount', Request::get('amount')) }}" class="form-control" placeholder="Enter amount" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hubdoc_status" class="col-form-label" style="color:#4a5568 !important;">Hubdoc Status</label>
                                        <select name="hubdoc_status" id="hubdoc_status" class="form-control">
                                            <option value="">All Invoices</option>
                                            <option value="1" {{ request('hubdoc_status') == '1' ? 'selected' : '' }}>Sent to Hubdoc</option>
                                            <option value="0" {{ request('hubdoc_status') == '0' ? 'selected' : '' }}>Not Sent to Hubdoc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="filter-buttons-container">
                                        <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Search</button>
                                        <a class="btn btn-info" href="{{URL::to('/clients/invoicelist')}}">Reset</a>
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
                                    <th class="sortable-header {{ request('sort_by') == 'reference' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="reference">
                                        Reference
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
                                    <th class="sortable-header {{ request('sort_by') == 'amount' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="amount">
                                        Amount
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'hubdoc_status' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="hubdoc_status">
                                        Hubdoc Status
                                        <span class="sort-icon">
                                            <i class="fas fa-caret-up"></i>
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable-header {{ request('sort_by') == 'voided_by' ? (request('sort_order') == 'desc' ? 'sort-desc' : 'sort-asc') : '' }}" data-sort="voided_by">
                                        Voided By
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


                                        $Reference = $list->trans_no ?? '-';
                                        $invoice_no = $list->invoice_no ?? '-';
                                        $trans_date = $list->trans_date ?? '-';

                                        if(isset($list->voided_or_validated_by) && $list->voided_or_validated_by != ""){
                                            $validate_by = \App\Models\Admin::select('id','first_name','last_name','user_id')->where('id', $list->voided_or_validated_by)->first();
                                            $validate_by_full_name = $validate_by->first_name.' '.$validate_by->last_name;
                                        } else {
                                            $validate_by_full_name = "-";
                                        }
                                        ?>
                                        <?php
                                        if($list->void_invoice == 1 ) {
                                            $trcls = 'class="strike-through"';
                                        } else {
                                            $trcls = 'class=""';
                                        }
                                        ?>
                                        <tr id="id_{{@$list->id}}" <?php echo $trcls;?>>
                                            <td style="white-space: initial;" class="text-center">
                                                <div class="custom-checkbox custom-control">
                                                    <input data-id="{{@$list->id}}" data-receiptid="{{@$list->receipt_id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" data-clientid="{{@$list->client_id}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input  your-checkbox" id="checkbox-{{$i}}">
                                                    <label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>{{ $client_id_display }}</td>
                                            <td>{{ $client_matter_display }}</td>
                                            <td>{{ $client_full_name }}</td>
                                            <td>{{ $Reference }}</td>
                                            <td>{{ $trans_date }}</td>
                                            <td id="deposit_{{@$list->id}}">
                                                @if($list->invoice_status == 1 && ($list->balance_amount == 0 || $list->balance_amount == 0.00))
                                                    {{ !empty($list->partial_paid_amount) ? '$ ' . number_format($list->partial_paid_amount, 2) : '' }}
                                                @else
                                                    {{ !empty($list->balance_amount) ? '$ ' . number_format($list->payment_type == 'Discount' ? abs($list->balance_amount) : $list->balance_amount, 2) : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                <?php
                                                // Check if invoice has been sent to Hubdoc
                                                if(isset($list->hubdoc_sent) && $list->hubdoc_sent == 1) {
                                                    $hubdoc_sent_at = $list->hubdoc_sent_at ?? null;
                                                    ?>
                                                    <span class="hubdoc-badge badge-success">
                                                        <i class="fas fa-check"></i> Sent
                                                    </span>
                                                    <?php if($hubdoc_sent_at) { ?>
                                                        <br>
                                                        <small style="font-size: 10px; color: #94a3b8; margin-top: 4px; display: block;">
                                                            <?php echo date('d/m/Y H:i', strtotime($hubdoc_sent_at)); ?>
                                                        </small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="hubdoc-badge badge-secondary">
                                                        <i class="fas fa-times"></i> Not Sent
                                                    </span>
                                                <?php } ?>
                                            </td>
                                            <td id="voidedby_{{@$list->id}}"><?php echo $validate_by_full_name;?></td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" style="text-align: center; padding: 60px 20px;">
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

   
    $('#trans_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    $("[data-checkboxes]").each(function () {
        var me = $(this),
        group = me.data('checkboxes'),
        role = me.data('checkbox-role');

        me.change(function () {
            var all = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
            checked = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
            dad = $('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
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
                    $('.is_checked_client').show();
                    $('.is_checked_clientn').hide();
                } else {
                    dad.prop('checked', false);
                    $('.is_checked_client').hide();
                    $('.is_checked_clientn').show();
                }
            }

        });
    });

    var clickedReceiptIds = [];
    $(document).delegate('.your-checkbox', 'click', function(){
        var clicked_receipt_id = $(this).data('receiptid');
        if ($(this).is(':checked')) {
            clickedReceiptIds.push(clicked_receipt_id);
        } else {
            var index2 = clickedReceiptIds.indexOf(clicked_receipt_id);
            if (index2 !== -1) {
                clickedReceiptIds.splice(index2, 1);
            }
        }
    });

    //merge task
    $(document).delegate('.is_checked_client_void_invoice', 'click', function(){
        if ( clickedReceiptIds.length > 0)
        {
            var mergeStr = "Are you sure want to void these invoice?";
            if (confirm(mergeStr)) {
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/void_invoice",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {clickedReceiptIds:clickedReceiptIds},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        //location.reload(true);
                        
                        if(obj.status){
                            var debugMsg = '';
                            if(obj.debug_info){
                                debugMsg = '\n\nDebug: ' + obj.debug_info.total_reversals + ' reversals created';
                            }
                            
                            if(obj.reversals_created > 0){
                                // If fee transfers were voided, reload the page to show updated balances
                                alert(obj.message + debugMsg + '\n\nReloading page to show updated balances...');
                                window.location.reload();
                                return;
                            } else {
                                // No fee transfers found - just show message
                                alert(obj.message + debugMsg);
                            }
                        }
                        
                        var record_data = obj.record_data;
                        $.each(record_data, function(index, subArray) {
                            //console.log('index=='+index);
                            //console.log('subArray=='+subArray.id);
                            $('#deposit_'+subArray.id).text("$0.00");
                            if(subArray.first_name != ""){
                                var voidedby_full_name = subArray.first_name+" "+subArray.last_name;
                            } else {
                                var voidedby_full_name = "-";
                            }
                            $('#voidedby_'+subArray.id).text(voidedby_full_name);
                            $('#id_'+subArray.id).css("text-decoration","line-through");
                            $('#id_'+subArray.id).css("color","#000");
                        });
                        $('.listing-container .custom-error-msg').text(obj.message);
                        $('.listing-container .custom-error-msg').show();
                        $('.listing-container .custom-error-msg').addClass('alert alert-success');
                    }
                });
            }
        } else {
            alert('Please select atleast 1 invoice.');
        }
    });


    $('.cb-element').change(function () {
        if ($('.cb-element:checked').length == $('.cb-element').length){
            $('#checkbox-all').prop('checked',true);
        } else {
            $('#checkbox-all').prop('checked',false);
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
