@extends('layouts.admin_client_detail')
@section('title', 'Client Receipt List')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Reduce spacing in filter section */
    .filter_panel {
        padding: 10px 15px !important;
    }
    .filter_panel h4 {
        margin-bottom: 8px !important;
    }
    .filter_panel .form-group {
        margin-bottom: 5px !important;
    }
    .filter_panel .col-form-label {
        margin-bottom: 3px !important;
        padding-bottom: 0 !important;
        font-size: 13px;
    }
    .filter_panel .form-control {
        padding: 5px 10px !important;
        height: auto !important;
    }
    .filter_panel .row {
        margin-left: -5px !important;
        margin-right: -5px !important;
    }
    .filter_panel [class*="col-"] {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }
    .filter_panel .btn {
        padding: 5px 15px !important;
        font-size: 13px !important;
    }
    
    /* Fixed header and filter panel when filters are active */
    .listing-container.filters-active,
    .listing-container.filters-active .listing-section,
    .listing-container.filters-active .listing-section-body,
    .listing-container.filters-active .card,
    .listing-container.filters-active .card-body {
        overflow: visible !important;
    }
    
    .listing-container.filters-active .card-header {
        position: -webkit-sticky;
        position: sticky;
        top: 60px; /* Below top navbar */
        background-color: #fff;
        z-index: 100;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin: 0;
    }
    
    .listing-container.filters-active .filter_panel {
        position: -webkit-sticky;
        position: sticky;
        top: 145px; /* Below top navbar + card header */
        background-color: #f8f9fa;
        z-index: 99;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 0 !important;
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
                    <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                        <h4 style="margin-bottom: 0; flex-shrink: 0;">All Clients Receipt List</h4>
                        
                        <div class="d-flex align-items-center" style="margin-left: auto;">
                            <select name="per_page" id="per_page" class="form-control" style="width: auto; min-width: 80px; border-radius: 0; border: 1px solid #ddd; padding: 6px 12px; margin-right: 10px;">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                                <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                            </select>
                            
                            <a href="javascript:;" style="background: #394eea;color: white; margin-right: 10px;"  class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
                            
                            @if (Auth::user()->role == '1' && Auth::user()->email == 'celestyparmar.62@gmail.com')
                                <button class="btn btn-danger Delete_Receipt" style="margin-right: 10px;">
                                    <i class="fas fa-trash-alt"></i>
                                    Delete Receipt
                                </button>
                            @endif

                            <button class="btn btn-primary Validate_Receipt" style="background-color: #394eea !important;">
                                <i class="fas fa-check-circle"></i>
                                Validate Receipt
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="filter_panel">
                        <h4 style="margin-top: 0; margin-bottom: 8px;">Search By Details</h4>
                        <form action="{{URL::to('/admin/clients/clientreceiptlist')}}" method="get">
                            <div class="row d-flex align-items-end">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="client_id" class="col-form-label" style="color:#4a5568 !important;">Client ID</label>
                                        <select name="client_id" id="client_id" class="form-control select2" style="max-width: 150px;">
                                            <option value="">Select Client</option>
                                            @foreach($clientIds as $client)
                                                <option value="{{ $client->client_id }}" {{ request('client_id') == $client->client_id ? 'selected' : '' }}>
                                                    {{ $client->first_name.' '.$client->last_name.'('.$client->client_unique_id.')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="client_matter_id" class="col-form-label" style="color:#4a5568 !important;">Client Matter ID</label>
                                        <select name="client_matter_id" id="client_matter_id" class="form-control select2" style="max-width: 150px;">
                                            <option value="">Select Matter</option>
                                            @foreach($matterIds as $matter)
                                                <option value="{{ $matter->client_matter_id }}" {{ request('client_matter_id') == $matter->client_matter_id ? 'selected' : '' }}>
                                                    {{ $matter->client_unique_id }}-{{ $matter->client_unique_matter_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="amount" class="col-form-label" style="color:#000;">Amount</label>
                                        <input type="number" name="amount" value="{{ old('amount', Request::get('amount')) }}" class="form-control" step="0.01" placeholder="Enter amount" style="max-width: 120px;">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="trans_date" class="col-form-label" style="color:#000;">Date</label>
                                        <input type="text" name="trans_date" value="{{ old('trans_date', Request::get('trans_date')) }}" class="form-control" data-valid="" autocomplete="off" placeholder="Date" id="trans_date" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="client_fund_ledger_type" class="col-form-label" style="color:#000;">Type</label>
                                        <select class="form-control" name="client_fund_ledger_type" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="Deposit" {{ request('client_fund_ledger_type') == 'Deposit' ? 'selected' : '' }}>Deposit</option>
                                            <option value="Fee Transfer" {{ request('client_fund_ledger_type') == 'Fee Transfer' ? 'selected' : '' }}>Fee Transfer</option>
                                            <option value="Disbursement" {{ request('client_fund_ledger_type') == 'Disbursement' ? 'selected' : '' }}>Disbursement</option>
                                            <option value="Refund" {{ request('client_fund_ledger_type') == 'Refund' ? 'selected' : '' }}>Refund</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="receipt_validate" class="col-form-label" style="color:#000;">Receipt Validate</label>
                                        <select class="form-control" name="receipt_validate" style="width: 100%;">
                                            <option value="">Select</option>
                                            <option value="1" {{ request('receipt_validate') == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ request('receipt_validate') == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-form-label" style="color: transparent;">&nbsp;</label>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Search</button>
                                            <a class="btn btn-info" href="{{URL::to('/admin/clients/clientreceiptlist')}}">Reset</a>
                                        </div>
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
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
                                            <label for="checkbox-all" class="custom-control-label"></label>
                                        </div>
                                    </th>
                                    <!--<th>SNo.</th>-->
                                    <th>Client ID</th>
                                    <th>Client Matter</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <!--<th>Entry Date</th>-->
                                    <th>Type</th>
                                    <th>Reference</th>

                                    <th>Funds In (+)</th>
                                    <th>Funds Out (-)</th>
                                    <th>Receipt Validate</th>
                                    <th>Validated By</th>
                                </tr>
                            </thead>
                            <tbody class="tdata">
                                @if(@$totalData !== 0)
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
                                        $client_fund_ledger_type = $list->client_fund_ledger_type ?? '-';
                                        $invoice_no = !empty($list->invoice_no) ? '('.$list->invoice_no.')' : '';
                                        $total_fund_in_amount = $list->deposit_amount ?? '-';
                                        $total_fund_out_amount = $list->withdraw_amount ?? '-';
                                        $receipt_validate = ($list->validate_receipt == 1) ? 'Yes' : 'No';

                                        if(isset($list->voided_or_validated_by) && $list->voided_or_validated_by != ""){
                                            $validate_by = \App\Models\Admin::select('id','first_name','last_name','user_id')->where('id', $list->voided_or_validated_by)->first();
                                            $validate_by_full_name = $validate_by->first_name.' '.$validate_by->last_name;
                                        } else {
                                            $validate_by_full_name = "-";
                                        }
                                        ?>
                                        <tr id="id_{{@$list->id}}">
                                            <td class="text-center">
                                                <div class="custom-checkbox custom-control">
                                                    <input data-id="{{@$list->id}}"
                                                           data-email="{{@$list->email ?? ''}}"
                                                           data-name="{{$client_full_name}}"
                                                           data-clientid="{{$client_id_display}}"
                                                           data-receiptid="{{@$list->receipt_id}}"
                                                           type="checkbox"
                                                           data-checkboxes="mygroup"
                                                           class="cb-element custom-control-input your-checkbox"
                                                           id="checkbox-{{$loop->index}}">
                                                    <label for="checkbox-{{$loop->index}}" class="custom-control-label"></label>
                                                </div>
                                            </td>
                                            <!--<td>{{--$id--}}</td>-->
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
                                            <td>{{ $client_fund_ledger_type }} {{$invoice_no }}</td>
                                            <td>{{ $Reference }}</td>
                                            <td id="deposit_{{@$list->id}}">{{ is_numeric($total_fund_in_amount) ? '$'.number_format((float)$total_fund_in_amount, 2) : '-' }}</td>
                                            <td id="withdraw_{{@$list->id}}">{{ is_numeric($total_fund_out_amount) ? '$'.number_format((float)$total_fund_out_amount, 2) : '-' }}</td>
                                            <td id="validate_{{@$list->id}}">
                                                <span class="{{ $receipt_validate == 'Yes' ? 'text-success' : 'text-danger' }}">
                                                    {{ $receipt_validate }}
                                                </span>
                                            </td>

                                            <td id="validateby_{{@$list->id}}">{{ $validate_by_full_name }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" style="text-align: center; padding: 20px;">
                                            No Record Found
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
    // Check if any filter has a value and keep panel open
    function checkFilterValues() {
        var hasFilterValue = false;
        
        // Check all filter inputs
        $('.filter_panel input, .filter_panel select').each(function() {
            if ($(this).val() && $(this).val() !== '') {
                hasFilterValue = true;
                return false; // Break the loop
            }
        });
        
        // If any filter has a value, show the panel and make header/filter fixed
        if (hasFilterValue) {
            $('.listing-container .filter_panel').show();
            $('.listing-container').addClass('filters-active');
            console.log('Filters active class added to listing-container');
        } else {
            $('.listing-container').removeClass('filters-active');
            console.log('Filters active class removed from listing-container');
        }
    }
    
    // Check on page load
    checkFilterValues();
    
    // Re-check when any filter input changes
    $('.filter_panel input, .filter_panel select').on('change keyup', function() {
        checkFilterValues();
    });

    // Handle records per page dropdown change
    $('#per_page').on('change', function() {
        var perPage = $(this).val();
        var currentUrl = new URL(window.location);
        currentUrl.searchParams.set('per_page', perPage);
        currentUrl.searchParams.delete('page'); // Reset to first page when changing per_page
        window.location.href = currentUrl.toString();
    });

    $('.listing-container .filter_btn').on('click', function(){
		$('.listing-container .filter_panel').slideToggle();
	});

    // Initialize Select2 for searchable dropdowns
    $('.listing-container .select2').select2({
        placeholder: "Search...",
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
            //clickedReceiptIds.push(clicked_receipt_id);

            // For deletion, allow only one receipt to be selected
            if ($('.listing-container .Delete_Receipt').length > 0) {
                $('.listing-container .your-checkbox').not(this).prop('checked', false);
                clickedReceiptIds = [clicked_receipt_id];
            } else {
                clickedReceiptIds.push(clicked_receipt_id);
            }
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
                    url:"{{URL::to('/')}}/admin/validate_receipt",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {clickedReceiptIds:clickedReceiptIds,receipt_type:1},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        //location.reload(true);
                        var record_data = obj.record_data;
                        $.each(record_data, function(index, subArray) {
                            //console.log('index=='+index);
                            //console.log('subArray=='+subArray.id);
                            $('.listing-container #validate_' + subArray.id +' span').removeClass('text-danger').addClass('text-success').text('Yes');
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

    // Delete receipt by super admin
    $(document).delegate('.listing-container .Delete_Receipt', 'click', function(){
        if (clickedReceiptIds.length === 0) {
            alert('Please select a receipt to delete.');
            return;
        }
        if (clickedReceiptIds.length > 1) {
            alert('Please select only one receipt to delete.');
            return;
        }
        var mergeStr = "Are you sure want to delete this receipt?";
        if (confirm(mergeStr)) {
            $.ajax({
                type: 'post',
                url: "{{URL::to('/')}}/admin/delete_receipt",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { receiptId: clickedReceiptIds[0], receipt_type: 1 },
                success: function(response) {
                    var obj = $.parseJSON(response);
                    if (obj.status) {
                        $('.listing-container #id_' + clickedReceiptIds[0]).remove();
                        clickedReceiptIds = [];
                        $('.listing-container .custom-error-msg').text(obj.message);
                        $('.listing-container .custom-error-msg').show();
                        $('.listing-container .custom-error-msg').addClass('alert alert-success');
                        // Check if table is empty after deletion
                        if ($('.listing-container .tdata tr').length === 0) {
                            $('.listing-container .tdata').html('<tr><td colspan="11" style="text-align: center; padding: 20px;">No Record Found</td></tr>');
                        }
                    } else {
                        $('.listing-container .custom-error-msg').text(obj.message);
                        $('.listing-container .custom-error-msg').show();
                        $('.listing-container .custom-error-msg').addClass('alert alert-danger');
                    }
                },
                error: function() {
                    $('.listing-container .custom-error-msg').text('An error occurred while deleting the receipt.');
                    $('.listing-container .custom-error-msg').show();
                    $('.listing-container .custom-error-msg').addClass('alert alert-danger');
                }
            });
        }
    });

    $('.listing-container .cb-element').change(function () {
        if ($('.listing-container .cb-element:checked').length == $('.listing-container .cb-element').length){
            $('.listing-container #checkbox-all').prop('checked',true);
        } else {
            $('.listing-container #checkbox-all').prop('checked',false);
        }
    });
});
</script>
@endpush
