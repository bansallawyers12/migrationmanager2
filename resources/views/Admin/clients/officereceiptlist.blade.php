@extends('layouts.admin_client_detail')
@section('title', 'Office Receipt List')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-datepicker.css') }}">
<style>
    /* Page-specific styles can be added here if needed */
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
                        <form action="{{URL::to('/admin/clients/officereceiptlist')}}" method="get">
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
                                        <a class="btn btn-info" href="{{URL::to('/admin/clients/officereceiptlist')}}">Reset</a>
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
                                    <th>Client Id</th>
                                     <th>Client Matter</th>
                                    <th>Name</th>
                                    <th>Trans. Date</th>
                                    <!--<th>Entry Date</th>-->
                                    <th>Reference</th>
                                    <th>Invoice No</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <th>Receipt Validate</th>
                                    <th>Validate By</th>
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
                                                <span class="{{ $receipt_validate == 'Yes' ? 'text-success' : 'text-danger' }}">
                                                    {{ $receipt_validate }}
                                                </span>
                                            </td>
                                            <td id="validateby_{{@$list->id}}"><?php echo $validate_by_full_name;?></td> <!-- New field data -->
                                        </tr>
                                        <?php $i++; ?>
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
                    url:"{{URL::to('/')}}/admin/validate_receipt",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {clickedReceiptIds:clickedReceiptIds,receipt_type:2},
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
