@extends('layouts.admin_client_detail')
@section('title', 'Clients Invoice List')

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
                        <form action="{{URL::to('/admin/clients/invoicelist')}}" method="get">
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
                                <div class="col-md-12 text-center">
                                    <div class="filter-buttons-container">
                                        <button type="submit" class="btn btn-primary btn-theme-lg mr-3">Search</button>
                                        <a class="btn btn-info" href="{{URL::to('/admin/clients/invoicelist')}}">Reset</a>
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
                                    <th>Client Id</th>
                                    <th>Client Matter</th>
                                    <th>Name</th>
                                    <th>Reference</th>
                                    <th>Trans. Date</th>
                                    <th>Amount</th>
                                    <th>Voided By</th>
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
                                            <td id="voidedby_{{@$list->id}}"><?php echo $validate_by_full_name;?></td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="empty-state">
                                            <div>
                                                <i class="fas fa-inbox fa-3x mb-3" style="color: #cbd5e0;"></i>
                                                <p>No records found</p>
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
@section('scripts')
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
                    url:"{{URL::to('/')}}/admin/void_invoice",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {clickedReceiptIds:clickedReceiptIds},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        //location.reload(true);
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
});
</script>
@endsection
