           <!-- Accounts Tab -->
           <div class="tab-pane" id="accounts-tab">

<div class="card full-width">
    <div style="margin-bottom: 10px;">
        <a class="btn btn-primary createreceipt" href="javascript:;" role="button">Create Entry</a>
    </div>
    <div class="account-layout" style="overflow-x: hidden; max-width: 100%;">
        <!-- Client Funds Ledger Section -->
        <section class="account-section client-account">
            <div class="account-section-header">
                <h2><i class="fas fa-wallet" style="color: #28a745;"></i> Client Funds Ledger</h2>
                <div class="balance-display">
                    <div class="balance-label">Current Funds Held</div>
                    <div class="balance-amount funds-held">
                        <?php
                        //echo $id1;
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count(); //dd($matter_cnt);
                        if( isset($id1) && $id1 != "" || $matter_cnt >0 )
                        {  //dd('ifff'.$fetchedData->id);
                            //if client unique reference id is present in url
                            if( isset($id1) && $id1 != "") {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            } else {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                            }
                            //dd($matter_get_id);
                            if($matter_get_id )
                            {
                                $client_selected_matter_id = $matter_get_id->id;
                            } else {
                                $client_selected_matter_id = '';
                            } //dd($client_selected_matter_id);
                        }
                        else
                        {  //dd('elseee');
                            $client_selected_matter_id = '';
                        }
                        // Calculate balance from scratch by summing deposits and withdrawals
                        // Exclude voided fee transfers
                        $ledger_entries = DB::table('account_client_receipts')
                            ->select('deposit_amount', 'withdraw_amount', 'void_fee_transfer')
                            ->where('client_id', $fetchedData->id)
                            ->where('client_matter_id', $client_selected_matter_id)
                            ->where('receipt_type', 1)
                            ->get();
                        
                        $calculated_balance = 0;
                        foreach($ledger_entries as $entry) {
                            // Skip voided fee transfers
                            if(isset($entry->void_fee_transfer) && $entry->void_fee_transfer == 1) {
                                continue;
                            }
                            $calculated_balance += floatval($entry->deposit_amount) - floatval($entry->withdraw_amount);
                        }
                        ?>
                        {{ '$ ' . number_format($calculated_balance, 2) }}

                    </div>
                </div>
            </div>
            <p style="font-size: 0.85em; color: #6c757d; margin-top: -15px; margin-bottom: 15px;">Funds held in trust/client account on behalf of the client.</p>
            <div class="transaction-table-wrapper">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th colspan="2">Type</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th class="currency">Funds In (+)</th>
                            <th class="currency">Funds Out (-)</th>
                            <th class="currency">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="productitemList">
                        <?php
                        $receipts_lists = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',1)->orderBy('id', 'desc')->get();
                        //dd($receipts_lists);
                        if(!empty($receipts_lists) && count($receipts_lists)>0 )
                        {
                            foreach($receipts_lists as $rec_list=>$rec_val)
                            {
                                $adminR = \App\Models\Admin::select('client_id')->where('id', $rec_val->client_id)->first();
                                // Add strikethrough class for voided fee transfers
                                $rowClass = '';
                                if(isset($rec_val->void_fee_transfer) && $rec_val->void_fee_transfer == 1){
                                    $rowClass = 'strike-through';
                                }
                            ?>
                        <tr class="drow_account_ledger {{$rowClass}}" data-matterid="{{$rec_val->client_matter_id}}">
                            <td>
                                <span style="display: inline-flex;">
                                    <?php
                                    if( isset($rec_val->validate_receipt) && $rec_val->validate_receipt == '1' )
                                    { ?>
                                        <i class="fas fa-check-circle" title="Verified Receipt" style="margin-top: 7px;"></i>
                                    <?php
                                    } ?>
                                    <?php echo $rec_val->trans_date;?>

                                    <?php echo "<br/>" . (!empty($adminR->client_id) ? $adminR->client_id : 'NA'); ?>
                                </span>
                            </td>

                            <td class="type-cell">
                                <?php
                                if($rec_val->client_fund_ledger_type == 'Deposit' ){
                                    $type_icon = 'fa-arrow-down';
                                } else if($rec_val->client_fund_ledger_type == 'Fee Transfer' ){
                                    $type_icon = 'fa-arrow-right-from-bracket';
                                } else if($rec_val->client_fund_ledger_type == 'Disbursement' ){
                                    $type_icon = 'fa-arrow-up';
                                } else if($rec_val->client_fund_ledger_type == 'Refund' ){
                                    $type_icon = 'fa-arrow-up';
                                } else {
                                    $type_icon = 'fa-arrow-up';
                                }?>
                                <i class="fas {{$type_icon}} type-icon" title="{{$rec_val->client_fund_ledger_type}}"></i>
                                <span>
                                    {{$rec_val->client_fund_ledger_type}}
                                    <?php
                                    if( isset($rec_val->extra_amount_receipt) &&  $rec_val->extra_amount_receipt == 'exceed' ) { ?>
                                        <br/>
                                        {{ !empty($rec_val->invoice_no) ? '('.$rec_val->invoice_no.')' : '' }}
                                    <?php } else { ?>
                                        <br/>
                                        {{ !empty($rec_val->invoice_no) ? '('.$rec_val->invoice_no.')' : '' }}

                                    <?php
                                    }?>
                                </span>
                                <?php
                                if($rec_val->client_fund_ledger_type !== 'Fee Transfer'){?>
                                    <a title="Edit Entry" class="link-primary edit-ledger-entry" href="javascript:;"
                                    data-id="<?php echo $rec_val->id; ?>"
                                    data-receiptid="<?php echo $rec_val->receipt_id; ?>"
                                    data-trans-date="<?php echo htmlspecialchars($rec_val->trans_date, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-entry-date="<?php echo htmlspecialchars($rec_val->entry_date, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-type="<?php echo htmlspecialchars($rec_val->client_fund_ledger_type, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-description="<?php echo htmlspecialchars($rec_val->description ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    data-deposit="<?php echo htmlspecialchars($rec_val->deposit_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-withdraw="<?php echo htmlspecialchars($rec_val->withdraw_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>">
                                     <i class="fas fa-pencil-alt"></i>
                                 </a>
                                <?php
                                }?>

                                <?php
                                if(isset($rec_val->uploaded_doc_id) && $rec_val->uploaded_doc_id != ""){
                                    $client_doc_list = DB::table('documents')->select('myfile')->where('id',$rec_val->uploaded_doc_id)->first();
                                    if($client_doc_list){ ?>
                                        <a target="_blank" title="See Attached Document" class="link-primary" href="<?php echo $client_doc_list->myfile;?>"><i class="fas fa-file-pdf"></i></a>
                                    <?php
                                    }
                                } ?>
                            </td><td></td>

                            <td class="description"><?php echo $rec_val->description;?></td>

                            <!--<td><a href="#" title="View Receipt ".<?php //echo $rec_val->trans_no;?>><?php //echo $rec_val->trans_no;?></a></td>-->
                            <td><a target="_blank" href="{{URL::to('/clients/genClientFundReceipt')}}/{{$rec_val->id}}" title="View Receipt"><?php echo $rec_val->trans_no;?></a></td>

                            <td class="currency text-success">{{ !empty($rec_val->deposit_amount) ? '$ ' . number_format($rec_val->deposit_amount, 2) : '' }}</td>
                            <td class="currency">{{ !empty($rec_val->withdraw_amount) ? '$ ' . number_format($rec_val->withdraw_amount, 2) : '' }}</td>
                            <td class="currency balance">{{ !empty($rec_val->balance_amount) ? '$ ' . number_format($rec_val->balance_amount, 2) : '' }}</td>
                        </tr>
                        <?php
                            } //end foreach
                        }?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Invoicing & Office Receipts Section -->
        <section class="account-section office-account">
            <div class="account-section-header">
                <h2><i class="fas fa-file-invoice-dollar" style="color: #007bff;"></i> Invoicing & Office Receipts</h2>
                <div class="balance-display">
                    <div class="balance-label">Outstanding Balance</div>
                    <div class="balance-amount outstanding outstanding-balance">
                        <?php
                        //echo $id1;
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                        if( isset($id1) && $id1 != "" || $matter_cnt >0 )
                        {  //if client unique reference id is present in url
                            //dd('ifff'.$fetchedData->id);
                            //if client unique reference id is present in url
                            if( isset($id1) && $id1 != "") {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            } else {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                            }
                            //dd($matter_get_id);
                            if($matter_get_id )
                            {
                                $client_selected_matter_id = $matter_get_id->id;
                            } else {
                                $client_selected_matter_id = '';
                            } //dd($client_selected_matter_id);
                        }
                        else
                        {
                            $client_selected_matter_id = '';
                        }

                        $latest_outstanding_balance = DB::table('account_client_receipts')
                        ->where('client_id', $fetchedData->id)
                        ->where('client_matter_id', $client_selected_matter_id)
                        ->where('receipt_type', 3) // Invoice
                        ->where(function ($query) {
                            $query->whereIn('invoice_status', [0, 2])
                                ->orWhere(function ($q) {
                                    $q->where('invoice_status', 1)
                                        ->where('balance_amount', '!=', 0);
                                });
                        })
                        ->sum('balance_amount');
                        ?>
                        {{ is_numeric($latest_outstanding_balance) ? '$ ' . number_format($latest_outstanding_balance, 2) : '$ 0.00' }}

                        <?php if ($latest_outstanding_balance < 0): ?>
                            <a class="link-primary adjustinvoice" href="javascript:;" title="Adjust Invoice">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <p style="font-size: 0.85em; color: #6c757d; margin-top: -15px; margin-bottom: 15px;">Tracks invoices issued and payments received directly by the office.</p>
            <div class="transaction-table-wrapper">
                <h4 style="margin-top:0; margin-bottom: 10px; font-weight: 600;">Invoices Issued</h4>
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Inv #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="currency">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="productitemList_invoice">
                        <?php
                        $receipts_lists_invoice = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',3)->groupBy('receipt_id')->orderBy('id', 'desc')->get();
                        //dd($receipts_lists_invoice);
                        if(!empty($receipts_lists_invoice) && count($receipts_lists_invoice)>0 )
                        {
                            foreach($receipts_lists_invoice as $inc_list=>$inc_val)
                            {
                                if($inc_val->void_invoice == 1 ) {
                                    $trcls = 'strike-through';
                                } else {
                                    $trcls = '';
                                }  //dd(Auth::user()->role);
                                ?>
                                <tr class="drow_account_invoice invoiceTrRow <?php echo $trcls;?>" id="invoiceTrRow_<?php echo $inc_val->id;?>" data-matterid="{{$inc_val->client_matter_id}}">
                                    <td>
                                        <?php echo $inc_val->trans_no."<br/>";?>
                                        <?php
                                        if($inc_val->save_type == 'draft'){?>
                                            <a title="Edit Draft Invoice" class="link-primary updatedraftinvoice" href="javascript:;" data-receiptid="<?php echo $inc_val->receipt_id;?>"><i class="fas fa-pencil-alt"></i></a>
                                        <?php
                                        }
                                        else if($inc_val->save_type == 'final') {?>
                                            <a title="Final Invoice" target="_blank" class="link-primary" href="{{URL::to('/clients/genInvoice')}}/{{$inc_val->receipt_id}}"><i class="fas fa-file-pdf"></i></a>
                                        <?php
                                        } ?>
                                    </td>
                                    <td><?php echo $inc_val->trans_date;?></td>
                                    <td><?php echo $inc_val->description;?></td>
                                    <td class="currency">
                                        @if($inc_val->invoice_status == 1 && ($inc_val->balance_amount == 0 || $inc_val->balance_amount == 0.00))
                                            {{ !empty($inc_val->partial_paid_amount) ? '$ ' . number_format($inc_val->partial_paid_amount, 2) : '' }}
                                        @else
                                            {{ !empty($inc_val->balance_amount) ? '$ ' . number_format($inc_val->payment_type == 'Discount' ? abs($inc_val->balance_amount) : $inc_val->balance_amount, 2) : '' }}
                                        @endif
                                    </td>
                                        <?php
                                        $statusClassMap = [
                                            '0' => 'status-unpaid',
                                            '1' => 'status-paid',
                                            '2' => 'status-partial',
                                            '3' => 'status-void'
                                        ];

                                        $statusVal = [
                                            '0' => 'Unpaid',
                                            '1' => 'Paid',
                                            '2' => 'Partial',
                                            '3' => 'Void',
                                            '4' => 'Discount'

                                        ];

                                        $status = $inc_val->invoice_status;
                                        $statusClass = $statusClassMap[$status];
                                        if( isset($inc_val->payment_type) && $inc_val->payment_type == 'Discount'){
                                            $status = 4; //Discount
                                        } else {
                                            $status = $status;
                                        }
                                        $statusDes = $statusVal[$status];
                                        ?>

                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $statusDes; ?>
                                        </span>
                                        
                                        <?php 
                                        // Quick Receipt Button - Show for unpaid (0) and partial (2) invoices only
                                        if($inc_val->save_type == 'final' && in_array($inc_val->invoice_status, [0, 2]) && $inc_val->void_invoice != 1) { 
                                            $invoiceBalance = floatval($inc_val->balance_amount);
                                        ?>
                                        <br/>
                                        <button type="button" class="btn btn-sm btn-success quick-receipt-btn" 
                                                data-invoice-no="<?php echo $inc_val->trans_no; ?>"
                                                data-invoice-balance="<?php echo $invoiceBalance; ?>"
                                                data-invoice-description="<?php echo htmlspecialchars($inc_val->description ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-matter-id="<?php echo $inc_val->client_matter_id; ?>"
                                                style="margin-top: 5px; margin-bottom: 5px; font-size: 11px; padding: 3px 10px;">
                                            <i class="fas fa-money-bill-wave"></i> Quick Receipt
                                        </button>
                                        <?php } ?>
                                        
                                            <?php if($inc_val->save_type == 'final') { ?>
                                            <br>
                                            <?php 
                                            // Check if invoice has been sent to Hubdoc
                                            $hubdoc_sent = DB::table('account_client_receipts')
                                                ->where('receipt_type', 3)
                                                ->where('receipt_id', $inc_val->receipt_id)
                                                ->value('hubdoc_sent');
                                            
                                            if($hubdoc_sent) {
                                                // Already sent to Hubdoc
                                                $hubdoc_sent_at = DB::table('account_client_receipts')
                                                    ->where('receipt_type', 3)
                                                    ->where('receipt_id', $inc_val->receipt_id)
                                                    ->value('hubdoc_sent_at');
                                            ?>
                                                <button type="button" class="btn btn-sm btn-success send-to-hubdoc-btn" 
                                                        data-invoice-id="<?php echo $inc_val->receipt_id; ?>" 
                                                        style="margin-top: 5px; font-size: 11px; padding: 2px 8px;">
                                                    <i class="fas fa-check"></i> Already Sent At Hubdoc
                                                </button>
                                                <br>
                                                <small style="font-size: 9px; color: #666;">
                                                    Sent: <?php echo date('d/m/Y H:i', strtotime($hubdoc_sent_at)); ?>
                                                </small>
                                                <br>
                                                <button type="button" class="btn btn-sm btn-outline-secondary refresh-hubdoc-status" 
                                                        data-invoice-id="<?php echo $inc_val->receipt_id; ?>" 
                                                        style="margin-top: 2px; font-size: 9px; padding: 1px 4px;">
                                                    <i class="fas fa-sync-alt"></i> Refresh
                                                </button>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-sm btn-primary send-to-hubdoc-btn" 
                                                        data-invoice-id="<?php echo $inc_val->receipt_id; ?>" 
                                                        style="margin-top: 5px; font-size: 11px; padding: 2px 8px;">
                                                    <i class="fas fa-paper-plane"></i> Sent To Hubdoc
                                                </button>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php
                            } //end foreach
                        }
                        ?>

                    </tbody>
                </table>

                <h4 style="margin-top:25px; margin-bottom: 10px; font-weight: 600;">Direct Office Receipts</h4>
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th colspan="2">Method</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th class="currency" style="white-space: initial;">Amount Received</th>
                        </tr>
                    </thead>
                    <tbody class="productitemList_office">
                        <?php
                        // Sort office receipts: unallocated (no invoice_no) at the top, then by ID descending
                        $receipts_lists_office = DB::table('account_client_receipts')
                            ->where('client_matter_id',$client_selected_matter_id)
                            ->where('client_id',$fetchedData->id)
                            ->where('receipt_type',2)
                            ->orderByRaw('CASE WHEN invoice_no IS NULL OR invoice_no = "" THEN 0 ELSE 1 END')
                            ->orderBy('id', 'desc')
                            ->get();
                        //dd($receipts_lists_office);
                        if(!empty($receipts_lists_office) && count($receipts_lists_office)>0 )
                        {
                            foreach($receipts_lists_office as $off_list=>$off_val)
                            {
                            // Determine if this receipt is unallocated (not linked to an invoice)
                            $isUnallocated = empty($off_val->invoice_no);
                            $unallocatedClass = $isUnallocated ? 'unallocated-receipt' : '';
                            ?>
                            <tr class="drow_account_office {{$unallocatedClass}}" data-matterid="{{$off_val->client_matter_id}}">
                                <td>
                                    <span style="display: inline-flex;">
                                        <?php
                                        if( isset($off_val->validate_receipt) && $off_val->validate_receipt == '1' )
                                        { ?>
                                            <i class="fas fa-check-circle" title="Verified Receipt" style="margin-top: 7px;"></i>
                                        <?php
                                        } ?>
                                        <?php echo $off_val->trans_date;?>
                                    </span>
                                    <?php
                                    if(isset($off_val->uploaded_doc_id) && $off_val->uploaded_doc_id >0){
                                        $office_doc_list = DB::table('documents')->select('myfile')->where('id',$off_val->uploaded_doc_id)->first();
                                        if($office_doc_list){ ?>
                                            <a title="See Attached Document" target="_blank" class="link-primary" href="<?php echo $office_doc_list->myfile;?>"><i class="fas fa-file-pdf"></i></a>
                                        <?php
                                        }
                                    } ?>
                                </td>
                                <?php
                                $payClassMap = [
                                    'Cash' => 'fa-arrow-down',
                                    'Bank transfer' => 'fa-arrow-right-from-bracket',
                                    'EFTPOS' => 'fa-arrow-right-from-bracket',
                                    'Refund' => 'fa-arrow-right-from-bracket'
                                ];
                                ?>
                                <td class="type-cell">
                                   <i class="fas  <?php echo $payClassMap[$off_val->payment_method]; ?> type-icon"></i>
                                   <span>
                                    {{$off_val->payment_method}}
                                    

                                    <?php
                                    if( isset($off_val->extra_amount_receipt) &&  $off_val->extra_amount_receipt == 'exceed' ) {

                                    } else { ?>
                                        <br/>
                                        {{ !empty($off_val->invoice_no) ? '('.$off_val->invoice_no.')' : '' }}
                                    <?php
                                    }?>

                                   </span>
                                </td><td></td>

                                <td class="description"><?php echo $off_val->description;?></td>
                                <!--<td><a href="#" title="View Receipt {{--$off_val->trans_no--}}"><?php //echo $off_val->trans_no;?></a></td>-->
                                <td><a target="_blank" href="{{URL::to('/clients/genOfficeReceipt')}}/{{$off_val->id}}" title="View Receipt"><?php echo $off_val->trans_no;?></a></td>

                                <td class="currency text-success">{{ !empty($off_val->deposit_amount) ? '$ ' . number_format($off_val->deposit_amount, 2) : '' }}</td>
                            </tr>
                        <?php
                            } //end foreach
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
</div>

<style>
/* Unallocated Office Receipt - Red Background */
.unallocated-receipt {
    background-color: #ffe6e6 !important;
    border-left: 4px solid #dc3545 !important;
}

.unallocated-receipt:hover {
    background-color: #ffcccc !important;
}

/* Add a visual indicator to unallocated receipts */
.unallocated-receipt td {
    color: #721c24;
}

.unallocated-receipt td a {
    color: #dc3545;
    font-weight: 600;
}
</style>

<script>
jQuery(document).ready(function($) {
    // ================================================================
    // QUICK RECEIPT BUTTON - Pre-populate office receipt from invoice
    // ================================================================
    $(document).on('click', '.quick-receipt-btn', function(e) {
        e.preventDefault();
        
        const invoiceData = {
            invoiceNo: $(this).data('invoice-no'),
            balance: parseFloat($(this).data('invoice-balance')) || 0,
            description: $(this).data('invoice-description') || '',
            matterId: $(this).data('matter-id')
        };
        
        console.log('💵 Quick Receipt clicked for:', invoiceData);
        
        // Open the create receipt modal
        const $modal = $('#createreceiptmodal');
        
        // Enable Quick Receipt mode to prevent form clearing
        $modal.data('quick-receipt-mode', true);
        $modal.data('quick-receipt-invoice-data', invoiceData);
        
        // FIX: Hide invoice option - Quick Receipt is only for payments, not creating invoices
        $modal.find('input[name="receipt_type"][value="invoice_receipt"]').closest('label').hide();
        
        // Select "Direct Office Receipt" radio button
        $('input[name="receipt_type"][value="office_receipt"]').prop('checked', true).trigger('change');
        
        // Update modal title
        $modal.find('.modal-title').html('<i class="fas fa-money-bill-wave" style="color: #28a745;"></i> Quick Receipt for ' + invoiceData.invoiceNo);
        
        // Wait a moment for the form to show, then populate fields
        if (typeof window.populateQuickReceiptOfficeForm === 'function') {
            setTimeout(function() {
                window.populateQuickReceiptOfficeForm(invoiceData);
            }, 100);
        } else {
            console.error('populateQuickReceiptOfficeForm is not available');
        }
        
        // Add a badge to indicate this is from Quick Receipt
        // FIX: Remove ALL existing badges first to prevent duplication
        $modal.find('.modal-header .badge').remove();
        $modal.find('.modal-header').prepend('<span class="badge badge-success" style="margin-right: 10px;"><i class="fas fa-bolt"></i> QUICK RECEIPT</span>');
        
        // Open the modal
        $modal.modal('show');
    });
    
    // Remove Quick Receipt badge when modal closes
    $('#createreceiptmodal').on('hidden.bs.modal', function() {
        $(this).find('.badge-success').remove();
        $(this).find('.modal-title').html('Create Receipt');
        
        // FIX: Restore invoice option when modal closes (in case it was hidden by Quick Receipt)
        $(this).find('input[name="receipt_type"][value="invoice_receipt"]').closest('label').show();

        // Clear Quick Receipt state
        $(this).removeData('quick-receipt-mode');
        $(this).removeData('quick-receipt-invoice-data');
    });
    
    console.log('✅ Quick Receipt functionality enabled');
});
</script>
