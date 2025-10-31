           <!-- Accounts Test Tab -->
           <div class="tab-pane" id="accounts-test-tab">

<div class="card full-width">
    <div class="alert alert-warning">
        <strong>🧪 ACCOUNTS TEST PAGE - Local Development Mode</strong>
        <p><i class="fas fa-exclamation-triangle"></i> This page has FULL READ/WRITE access to the database. Safe for local testing.</p>
        <small>All changes made here will affect the actual database tables (account_client_receipts, etc.)</small>
    </div>

    <div style="margin-bottom: 10px;">
        <!-- Create Entry Buttons - Split by Receipt Type -->
        <div style="display: inline-block; margin-right: 20px; padding: 5px; background: #f8f9fa; border-radius: 5px;">
            <strong style="font-size: 12px; color: #666; margin-right: 10px;">Create Entry:</strong>
            <a class="btn btn-success createreceipt" href="javascript:;" role="button" data-test-mode="true" data-receipt-type="1" style="margin-right: 5px;">
                <i class="fas fa-wallet"></i> Client Funds Ledger
            </a>
            <a class="btn btn-primary createreceipt" href="javascript:;" role="button" data-test-mode="true" data-receipt-type="2" style="margin-right: 5px;">
                <i class="fas fa-hand-holding-usd"></i> Direct Office Receipt
            </a>
            <a class="btn btn-info createreceipt" href="javascript:;" role="button" data-test-mode="true" data-receipt-type="3">
                <i class="fas fa-file-invoice-dollar"></i> Invoice
            </a>
        </div>
    </div>

    <div class="account-layout" style="overflow-x: hidden; max-width: 100%;">
        <!-- Client Funds Ledger Section -->
        <section class="account-section client-account">
            <div class="account-section-header">
                <h2><i class="fas fa-wallet" style="color: #28a745;"></i> Client Funds Ledger (Test)</h2>
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
            <p style="font-size: 0.85em; color: #6c757d; margin-top: -15px; margin-bottom: 15px;">
                🧪 TEST MODE: Funds held in trust/client account on behalf of the client.
            </p>
            
            <!-- TEST: Add filtering options -->
            <div class="test-filters" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>🔍 Test Filters:</strong>
                <label style="margin-left: 10px;">
                    <input type="checkbox" id="filter-deposits"> Show Only Deposits
                </label>
                <label style="margin-left: 10px;">
                    <input type="checkbox" id="filter-transfers"> Show Only Fee Transfers
                </label>
                <label style="margin-left: 10px;">
                    <input type="checkbox" id="filter-refunds"> Show Only Refunds
                </label>
                <button class="btn btn-sm btn-primary" id="apply-filters" style="margin-left: 10px;">Apply Filters</button>
                <button class="btn btn-sm btn-secondary" id="reset-filters">Reset</button>
            </div>

            <div class="transaction-table-wrapper">
                <table class="transaction-table" id="test-client-ledger-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Date</th>
                            <th colspan="2" style="text-align: left;">Type</th>
                            <th style="text-align: left;">Description</th>
                            <th style="text-align: center;">Reference</th>
                            <th style="text-align: right;">Funds In (+)</th>
                            <th style="text-align: right;">Funds Out (-)</th>
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
                            // Add strikethrough class for voided fee transfers
                            $rowClass = '';
                            if(isset($rec_val->void_fee_transfer) && $rec_val->void_fee_transfer == 1){
                                $rowClass = 'strike-through';
                            }
                            ?>
                        <tr class="drow_account_ledger ledger-row {{$rowClass}}" data-type="{{$rec_val->client_fund_ledger_type}}" data-matterid="{{$rec_val->client_matter_id}}">
                            <td style="text-align: left; vertical-align: middle;">
                                <span style="display: inline-flex; align-items: center;">
                                    <?php
                                    if( isset($rec_val->validate_receipt) && $rec_val->validate_receipt == '1' )
                                    { ?>
                                        <i class="fas fa-check-circle" title="Verified Receipt" style="margin-right: 5px; color: #28a745;"></i>
                                    <?php
                                    } ?>
                                    <?php echo $rec_val->trans_date;?>
                                </span>
                            </td>

                            <td class="type-cell" style="text-align: left; vertical-align: middle;">
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
                                if(isset($rec_val->uploaded_doc_id) && $rec_val->uploaded_doc_id != ""){
                                    $client_doc_list = DB::table('documents')->select('myfile')->where('id',$rec_val->uploaded_doc_id)->first();
                                    if($client_doc_list){ ?>
                                        <a target="_blank" title="See Attached Document" class="link-primary" href="<?php echo $client_doc_list->myfile;?>"><i class="fas fa-file-pdf"></i></a>
                                    <?php
                                    }
                                } ?>
                            </td>
                            <td style="text-align: left; vertical-align: middle;"></td>

                            <td class="description" style="text-align: left; vertical-align: middle;"><?php echo $rec_val->description;?></td>

                            <td style="text-align: center; vertical-align: middle;">
                                <div class="dropdown d-inline-block">
                                    <span class="reference-dropdown-trigger" id="dropdownReceipt{{$rec_val->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo $rec_val->trans_no;?> <i class="fas fa-caret-down" style="font-size: 11px; opacity: 0.6; margin-left: 3px;"></i>
                                    </span>
                                    <div class="dropdown-menu" aria-labelledby="dropdownReceipt{{$rec_val->id}}">
                                        <a class="dropdown-item" href="{{URL::to('/clients/genClientFundLedgerInvoice')}}/{{$rec_val->id}}" target="_blank">
                                            <i class="fas fa-eye"></i> View Receipt
                                        </a>
                                        <a class="dropdown-item" href="{{URL::to('/clients/genClientFundLedgerInvoice')}}/{{$rec_val->id}}" download>
                                            <i class="fas fa-download"></i> Download PDF
                                        </a>
                                        <?php if($rec_val->client_fund_ledger_type !== 'Fee Transfer'){ ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item edit-ledger-entry" href="javascript:;"
                                            data-id="<?php echo $rec_val->id; ?>"
                                            data-receiptid="<?php echo $rec_val->receipt_id; ?>"
                                            data-trans-date="<?php echo htmlspecialchars($rec_val->trans_date, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-entry-date="<?php echo htmlspecialchars($rec_val->entry_date, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-type="<?php echo htmlspecialchars($rec_val->client_fund_ledger_type, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-description="<?php echo htmlspecialchars($rec_val->description ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            data-deposit="<?php echo htmlspecialchars($rec_val->deposit_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-withdraw="<?php echo htmlspecialchars($rec_val->withdraw_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fas fa-edit"></i> Edit Entry
                                        </a>
                                        <?php } ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item copy-reference" href="javascript:;" data-reference="<?php echo $rec_val->trans_no;?>">
                                            <i class="fas fa-copy"></i> Copy Reference
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td style="text-align: right; vertical-align: middle; color: #28a745; font-weight: 500;">{{ !empty($rec_val->deposit_amount) ? '$ ' . number_format($rec_val->deposit_amount, 2) : '' }}</td>
                            <td style="text-align: right; vertical-align: middle; font-weight: 500;">{{ !empty($rec_val->withdraw_amount) ? '$ ' . number_format($rec_val->withdraw_amount, 2) : '' }}</td>
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
                <h2><i class="fas fa-file-invoice-dollar" style="color: #007bff;"></i> Invoicing & Office Receipts (Test)</h2>
                <div class="balance-display">
                    <div class="balance-label">Outstanding Balance</div>
                    <div class="balance-amount outstanding outstanding-balance">
                        <?php
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
            <p style="font-size: 0.85em; color: #6c757d; margin-top: -15px; margin-bottom: 15px;">
                🧪 TEST MODE: Tracks invoices issued and payments received directly by the office.
            </p>
            <div class="transaction-table-wrapper">
                <h4 style="margin-top:0; margin-bottom: 10px; font-weight: 600;">Invoices Issued</h4>
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Inv #</th>
                            <th style="text-align: left;">Date</th>
                            <th style="text-align: left;">Description</th>
                            <th style="text-align: right;">Amount</th>
                            <th style="text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody class="productitemList_invoice">
                        <?php
                        $receipts_lists_invoice = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',3)->groupBy('receipt_id')->orderBy('id', 'desc')->get();
                        
                        if(!empty($receipts_lists_invoice) && count($receipts_lists_invoice)>0 )
                        {
                            foreach($receipts_lists_invoice as $inc_list=>$inc_val)
                            {
                                if($inc_val->void_invoice == 1 ) {
                                    $trcls = 'strike-through';
                                } else {
                                    $trcls = '';
                                }
                                ?>
                                <tr class="drow_account_invoice invoiceTrRow <?php echo $trcls;?>" id="invoiceTrRow_<?php echo $inc_val->id;?>" data-matterid="{{$inc_val->client_matter_id}}">
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div class="dropdown d-inline-block">
                                            <span class="reference-dropdown-trigger" id="dropdownInvoice{{$inc_val->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php echo $inc_val->trans_no;?> <i class="fas fa-caret-down" style="font-size: 11px; opacity: 0.6; margin-left: 3px;"></i>
                                            </span>
                                            <div class="dropdown-menu" aria-labelledby="dropdownInvoice{{$inc_val->id}}">
                                                <?php if($inc_val->save_type == 'final') { ?>
                                                <a class="dropdown-item" href="{{URL::to('/clients/genInvoice')}}/{{$inc_val->receipt_id}}" target="_blank">
                                                    <i class="fas fa-eye"></i> View Invoice
                                                </a>
                                                <a class="dropdown-item" href="{{URL::to('/clients/genInvoice')}}/{{$inc_val->receipt_id}}" download>
                                                    <i class="fas fa-download"></i> Download PDF
                                                </a>
                                                <?php } ?>
                                                <?php if($inc_val->save_type == 'draft'){ ?>
                                                <a class="dropdown-item updatedraftinvoice" href="javascript:;" data-receiptid="<?php echo $inc_val->receipt_id;?>">
                                                    <i class="fas fa-edit"></i> Edit Draft Invoice
                                                </a>
                                                <?php } ?>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item copy-reference" href="javascript:;" data-reference="<?php echo $inc_val->trans_no;?>">
                                                    <i class="fas fa-copy"></i> Copy Invoice #
                                                </a>
                                                
                                                <?php if($inc_val->save_type == 'final') { ?>
                                                <div class="dropdown-divider"></div>
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
                                                    <a class="dropdown-item send-to-hubdoc-btn" href="javascript:;" data-invoice-id="<?php echo $inc_val->receipt_id; ?>" style="color: #28a745;">
                                                        <i class="fas fa-check"></i> Already Sent to Hubdoc
                                                    </a>
                                                    <div class="dropdown-item-text" style="font-size: 11px; color: #666; padding: 0.25rem 1rem;">
                                                        Sent: <?php echo date('d/m/Y H:i', strtotime($hubdoc_sent_at)); ?>
                                                    </div>
                                                    <a class="dropdown-item refresh-hubdoc-status" href="javascript:;" data-invoice-id="<?php echo $inc_val->receipt_id; ?>">
                                                        <i class="fas fa-sync-alt"></i> Refresh Status
                                                    </a>
                                                <?php } else { ?>
                                                    <a class="dropdown-item send-to-hubdoc-btn" href="javascript:;" data-invoice-id="<?php echo $inc_val->receipt_id; ?>">
                                                        <i class="fas fa-paper-plane"></i> Send to Hubdoc
                                                    </a>
                                                <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: left; vertical-align: middle;"><?php echo $inc_val->trans_date;?></td>
                                    <td style="text-align: left; vertical-align: middle;"><?php echo $inc_val->description;?></td>
                                    <td style="text-align: right; vertical-align: middle; font-weight: 500;">
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

                                    <td style="text-align: left; vertical-align: middle;">
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $statusDes; ?>
                                        </span>
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
                            <th style="text-align: left;">Date</th>
                            <th colspan="2" style="text-align: left;">Method</th>
                            <th style="text-align: left;">Description</th>
                            <th style="text-align: center;">Reference</th>
                            <th style="text-align: right;">Amount Received</th>
                        </tr>
                    </thead>
                    <tbody class="productitemList_office">
                        <?php
                        $receipts_lists_office = DB::table('account_client_receipts')->where('client_matter_id',$client_selected_matter_id)->where('client_id',$fetchedData->id)->where('receipt_type',2)->orderBy('id', 'desc')->get();
                        //dd($receipts_lists_office);
                        if(!empty($receipts_lists_office) && count($receipts_lists_office)>0 )
                        {
                            foreach($receipts_lists_office as $off_list=>$off_val)
                            {
                            ?>
                            <tr class="drow_account_office" data-matterid="{{$off_val->client_matter_id}}">
                                <td style="text-align: left; vertical-align: middle;">
                                    <span style="display: inline-flex; align-items: center;">
                                        <?php
                                        if( isset($off_val->validate_receipt) && $off_val->validate_receipt == '1' )
                                        { ?>
                                            <i class="fas fa-check-circle" title="Verified Receipt" style="margin-right: 5px; color: #28a745;"></i>
                                        <?php
                                        } ?>
                                        <?php echo $off_val->trans_date;?>
                                    </span>
                                    <?php
                                    if(isset($off_val->uploaded_doc_id) && $off_val->uploaded_doc_id >0){
                                        $office_doc_list = DB::table('documents')->select('myfile')->where('id',$off_val->uploaded_doc_id)->first();
                                        if($office_doc_list){ ?>
                                            <br/>
                                            <a title="See Attached Document" target="_blank" class="link-primary" href="<?php echo $office_doc_list->myfile;?>"><i class="fas fa-file-pdf"></i> Document</a>
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
                                <td class="type-cell" style="text-align: left; vertical-align: middle;">
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
                                </td>
                                <td style="text-align: left; vertical-align: middle;"></td>

                                <td class="description" style="text-align: left; vertical-align: middle;"><?php echo $off_val->description;?></td>
                                
                                <td style="text-align: center; vertical-align: middle;">
                                    <div class="dropdown d-inline-block">
                                        <span class="reference-dropdown-trigger" id="dropdownOffice{{$off_val->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo $off_val->trans_no;?> <i class="fas fa-caret-down" style="font-size: 11px; opacity: 0.6; margin-left: 3px;"></i>
                                        </span>
                                        <div class="dropdown-menu" aria-labelledby="dropdownOffice{{$off_val->id}}">
                                            <?php if(isset($off_val->save_type) && $off_val->save_type == 'final') { ?>
                                            <a class="dropdown-item" href="{{URL::to('/clients/genofficereceiptInvoice')}}/{{$off_val->id}}" target="_blank">
                                                <i class="fas fa-eye"></i> View Receipt
                                            </a>
                                            <a class="dropdown-item" href="{{URL::to('/clients/genofficereceiptInvoice')}}/{{$off_val->id}}" download>
                                                <i class="fas fa-download"></i> Download PDF
                                            </a>
                                            <?php } ?>
                                            <?php if(!isset($off_val->save_type) || $off_val->save_type == 'draft') { ?>
                                            <a class="dropdown-item edit-office-receipt-entry" href="javascript:;"
                                                data-id="<?php echo $off_val->id; ?>"
                                                data-receiptid="<?php echo $off_val->receipt_id; ?>"
                                                data-trans-date="<?php echo htmlspecialchars($off_val->trans_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-entry-date="<?php echo htmlspecialchars($off_val->entry_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-payment-method="<?php echo htmlspecialchars($off_val->payment_method, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-description="<?php echo htmlspecialchars($off_val->description ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-deposit="<?php echo htmlspecialchars($off_val->deposit_amount ?? 0, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-invoice-no="<?php echo htmlspecialchars($off_val->invoice_no ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-matter-id="<?php echo $off_val->client_matter_id; ?>"
                                                data-uploaded-doc-id="<?php echo $off_val->uploaded_doc_id ?? ''; ?>">
                                                <i class="fas fa-edit"></i> Edit Draft Receipt
                                            </a>
                                            <?php } ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item copy-reference" href="javascript:;" data-reference="<?php echo $off_val->trans_no;?>">
                                                <i class="fas fa-copy"></i> Copy Reference
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <td style="text-align: right; vertical-align: middle; color: #28a745; font-weight: 500;">{{ !empty($off_val->deposit_amount) ? '$ ' . number_format($off_val->deposit_amount, 2) : '' }}</td>
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

<!-- Test JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Improved Create Receipt Button Click Handler
    // Automatically selects the correct form based on which button was clicked
    // Using event delegation with higher priority to override the default handler
    $(document).off('click', '.createreceipt[data-test-mode="true"]').on('click', '.createreceipt[data-test-mode="true"]', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation(); // Prevent the default handler from firing
        
        const receiptType = $(this).data('receipt-type');
        const $modal = $('#createreceiptmodal');
        
        console.log('🎯 Test Mode Button Clicked - Receipt Type:', receiptType);
        
        // Hide the radio button selection section (not needed since button already indicates type)
        $modal.find('.form-group:has(input[name="receipt_type"])').hide();
        
        // Update modal title based on receipt type
        const modalTitles = {
            '1': '<i class="fas fa-wallet" style="color: #28a745;"></i> Create Client Funds Ledger Entry',
            '2': '<i class="fas fa-hand-holding-usd" style="color: #007bff;"></i> Create Direct Office Receipt',
            '3': '<i class="fas fa-file-invoice-dollar" style="color: #17a2b8;"></i> Create Invoice'
        };
        
        $modal.find('.modal-title').html(modalTitles[receiptType] || 'Create Receipt');
        
        // First, explicitly hide ALL forms to prevent double display
        $('#client_receipt_form, #invoice_receipt_form, #office_receipt_form').hide();
        console.log('🧹 All forms hidden');
        
        // Get the selected matter ID
        let selectedMatter;
        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            selectedMatter = $('#sel_matter_id_client_detail').val();
        }
        console.log('📁 Selected Matter ID:', selectedMatter);
        
        // Select the appropriate radio button and trigger change event
        // The change handler in detail-main.js will hide all forms and show the correct one
        if (receiptType == '1') {
            // Client Funds Ledger
            console.log('📝 Selecting Client Funds Ledger Form');
            
            // Set the matter ID for client ledger
            $('#client_matter_id_ledger').val(selectedMatter);
            console.log('📁 Set client_matter_id_ledger to:', selectedMatter);
            
            $('input[name="receipt_type"][value="client_receipt"]').prop('checked', true).trigger('change');
        } else if (receiptType == '2') {
            // Direct Office Receipt
            console.log('📝 Selecting Direct Office Receipt Form');
            
            // Set the matter ID for office receipt
            $('#client_matter_id_office').val(selectedMatter);
            console.log('📁 Set client_matter_id_office to:', selectedMatter);
            
            $('input[name="receipt_type"][value="office_receipt"]').prop('checked', true).trigger('change');
        } else if (receiptType == '3') {
            // Invoice
            console.log('📝 Selecting Invoice Form');
            
            // CRITICAL: Set function_type to "add" for new invoices
            $('#function_type').val('add');
            console.log('✏️ Set function_type to: add');
            
            // Set the matter ID
            $('#client_matter_id_invoice').val(selectedMatter);
            console.log('📁 Set client_matter_id_invoice to:', selectedMatter);
            
            $('input[name="receipt_type"][value="invoice_receipt"]').prop('checked', true).trigger('change');
        }
        
        // Ensure only the correct form is visible after a brief delay
        setTimeout(function() {
            // Hide all forms again
            $('#client_receipt_form, #invoice_receipt_form, #office_receipt_form').hide();
            
            // Show only the selected form
            const formIdMap = { '1': 'client_receipt_form', '2': 'office_receipt_form', '3': 'invoice_receipt_form' };
            const formId = formIdMap[receiptType];
            if (formId) {
                $('#' + formId).show();
                console.log('✅ Showing only:', formId);
            }
            
            // Re-ensure critical fields are set (in case change event cleared them)
            if (receiptType == '3') {
                $('#function_type').val('add');
                $('#client_matter_id_invoice').val(selectedMatter);
                console.log('🔄 Re-verified invoice form settings');
            } else if (receiptType == '1') {
                $('#client_matter_id_ledger').val(selectedMatter);
                console.log('🔄 Re-verified ledger form settings');
            } else if (receiptType == '2') {
                $('#client_matter_id_office').val(selectedMatter);
                console.log('🔄 Re-verified office receipt form settings');
            }
        }, 100);
        
        // Add a badge to indicate test mode
        if ($(this).data('test-mode')) {
            $modal.find('.modal-header').prepend('<span class="badge badge-warning" style="margin-right: 10px;">🧪 TEST MODE</span>');
        }
        
        // Open the modal
        $modal.modal('show');
        
        // Log for debugging
        console.log('✅ Modal opened successfully');
    });
    
    // Reset modal when closed (cleanup for next use)
    $('#createreceiptmodal').on('hidden.bs.modal', function() {
        // Show radio buttons again (in case user opens from a different page)
        $(this).find('.form-group:has(input[name="receipt_type"])').show();
        
        // Remove test mode badge
        $(this).find('.badge-warning').remove();
        
        // Reset modal title to default
        $(this).find('.modal-title').html('Create Receipt');
        
        console.log('✅ Modal reset for next use');
    });
    
    // Filter functionality
    document.getElementById('apply-filters')?.addEventListener('click', function() {
        const showDeposits = document.getElementById('filter-deposits').checked;
        const showTransfers = document.getElementById('filter-transfers').checked;
        const showRefunds = document.getElementById('filter-refunds').checked;
        
        const rows = document.querySelectorAll('.ledger-row');
        
        rows.forEach(row => {
            const type = row.getAttribute('data-type');
            let show = true;
            
            if (showDeposits || showTransfers || showRefunds) {
                show = false;
                if (showDeposits && type === 'Deposit') show = true;
                if (showTransfers && type === 'Fee Transfer') show = true;
                if (showRefunds && type === 'Refund') show = true;
            }
            
            row.style.display = show ? '' : 'none';
        });
    });
    
    document.getElementById('reset-filters')?.addEventListener('click', function() {
        document.getElementById('filter-deposits').checked = false;
        document.getElementById('filter-transfers').checked = false;
        document.getElementById('filter-refunds').checked = false;
        
        document.querySelectorAll('.ledger-row').forEach(row => {
            row.style.display = '';
        });
    });
    
    // Copy Reference Functionality
    $(document).on('click', '.copy-reference', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const reference = $(this).data('reference');
        const $item = $(this);
        const originalHtml = $item.html();
        
        // Modern clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(reference).then(() => {
                // Show success feedback
                $item.html('<i class="fas fa-check"></i> Copied!');
                $item.css({'background-color': '#d4edda', 'color': '#155724'});
                
                // Reset after 1.5 seconds
                setTimeout(() => {
                    $item.html(originalHtml);
                    $item.css({'background-color': '', 'color': ''});
                }, 1500);
                
                console.log('✅ Copied to clipboard:', reference);
            }).catch(err => {
                console.error('Failed to copy:', err);
                $item.html('<i class="fas fa-times"></i> Failed');
                $item.css({'background-color': '#f8d7da', 'color': '#721c24'});
                setTimeout(() => {
                    $item.html(originalHtml);
                    $item.css({'background-color': '', 'color': ''});
                }, 1500);
            });
        } else {
            // Fallback for older browsers
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(reference).select();
            
            try {
                document.execCommand('copy');
                $temp.remove();
                
                // Show success feedback
                $item.html('<i class="fas fa-check"></i> Copied!');
                $item.css({'background-color': '#d4edda', 'color': '#155724'});
                
                setTimeout(() => {
                    $item.html(originalHtml);
                    $item.css({'background-color': '', 'color': ''});
                }, 1500);
                
                console.log('✅ Copied to clipboard (fallback):', reference);
            } catch(err) {
                $temp.remove();
                $item.html('<i class="fas fa-times"></i> Failed');
                $item.css({'background-color': '#f8d7da', 'color': '#721c24'});
                setTimeout(() => {
                    $item.html(originalHtml);
                    $item.css({'background-color': '', 'color': ''});
                }, 1500);
            }
        }
    });
    
    // Edit Office Receipt Entry Handler
    $(document).on('click', '.edit-office-receipt-entry', function(e) {
        e.preventDefault();
        
        var $row = $(this).closest('tr');
        var id = $(this).data('id');
        var receiptId = $(this).data('receiptid');
        var transDate = $(this).data('trans-date');
        var entryDate = $(this).data('entry-date');
        var paymentMethod = $(this).data('payment-method');
        var description = $(this).data('description');
        var deposit = $(this).data('deposit');
        var invoiceNo = $(this).data('invoice-no');
        var matterId = $(this).data('matter-id');
        var uploadedDocId = $(this).data('uploaded-doc-id');
        
        console.log('✏️ Editing Office Receipt:', {id, receiptId, transDate, paymentMethod, deposit, invoiceNo});
        
        // Populate modal fields
        $('#editOfficeReceiptForm input[name="id"]').val(id);
        $('#edit_office_receipt_id').val(receiptId);
        $('#edit_office_client_matter_id').val(matterId);
        $('#edit_office_trans_date').val(transDate);
        $('#edit_office_entry_date').val(entryDate);
        $('#edit_office_payment_method').val(paymentMethod);
        $('#edit_office_deposit_amount').val(deposit);
        $('#edit_office_description').val(description);
        
        // Initialize datepickers
        $('#edit_office_trans_date, #edit_office_entry_date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
        
        // Load invoices for the matter and select the current one
        loadInvoicesForEdit(matterId, invoiceNo);
        
        // Show current document if exists
        if(uploadedDocId && uploadedDocId != '') {
            $('#current_document_display').html('<p class="text-info"><i class="fas fa-file-pdf"></i> Document attached (ID: ' + uploadedDocId + ')</p>');
        } else {
            $('#current_document_display').html('');
        }
        
        // Show modal
        $('#editOfficeReceiptModal').modal('show');
    });
    
    // Function to load invoices for the edit modal
    function loadInvoicesForEdit(matterId, selectedInvoice) {
        $.ajax({
            url: '{{ route("clients.getInvoicesByMatter") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_matter_id: matterId,
                client_id: '{{ $fetchedData->id }}'
            },
            success: function(response) {
                var $select = $('#edit_office_invoice_no');
                $select.empty();
                $select.append('<option value="">Select Invoice (Optional)</option>');
                
                if(response.invoices && response.invoices.length > 0) {
                    response.invoices.forEach(function(invoice) {
                        var selected = (invoice.trans_no == selectedInvoice) ? 'selected' : '';
                        $select.append('<option value="' + invoice.trans_no + '" ' + selected + '>' + 
                            invoice.trans_no + ' - $' + parseFloat(invoice.balance_amount).toFixed(2) + 
                            ' (' + invoice.status + ')</option>');
                    });
                }
                
                console.log('✅ Loaded invoices for matter:', matterId);
            },
            error: function(xhr) {
                console.error('Failed to load invoices:', xhr);
                $('#edit_office_invoice_no').html('<option value="">No invoices available</option>');
            }
        });
    }
    
    // Update Office Receipt - Save as Draft
    $('#updateOfficeReceiptDraftBtn').on('click', function() {
        updateOfficeReceipt('draft');
    });
    
    // Update Office Receipt - Save and Finalize
    $('#updateOfficeReceiptFinalBtn').on('click', function() {
        updateOfficeReceipt('final');
    });
    
    function updateOfficeReceipt(saveType) {
        var form = $('#editOfficeReceiptForm')[0];
        var formData = new FormData(form);
        formData.append('save_type', saveType);
        formData.append('_token', '{{ csrf_token() }}');
        
        console.log('💾 Updating office receipt as:', saveType);
        
        $.ajax({
            type: 'POST',
            url: '{{ route("clients.updateOfficeReceipt") }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    $('#editOfficeReceiptModal').modal('hide');
                    
                    // Show success message
                    alert(response.message || 'Office receipt updated successfully!');
                    
                    // Reload page to show updated data
                    localStorage.setItem('activeTab', 'accounts-test');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to update office receipt'));
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while updating. Please try again.');
                console.error('AJAX error:', status, error, xhr.responseText);
            }
        });
    }
    
    // Document upload handlers for edit modal
    $('.add-document-btn-edit').on('click', function(e) {
        e.preventDefault();
        $('.docofficereceiptupload_edit').click();
    });
    
    $('.docofficereceiptupload_edit').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if(fileName) {
            $('.file-selection-hint-edit').text('Selected: ' + fileName);
        }
    });
    
    // Ensure all existing functionality works on this test page
    console.log('🧪 Accounts Test Page loaded - Full Read/Write access enabled');
    console.log('📊 Client ID: {{ $fetchedData->id }}');
    console.log('📁 Matter ID: {{ $client_selected_matter_id ?? "N/A" }}');
    console.log('✅ All modals and forms are functional');
    console.log('✅ Office Receipt Edit functionality enabled');
});

// All existing modal popups and forms work seamlessly with this test page
// They use class selectors, so all functionality is preserved
</script>

<style>
/* Test page specific styles */
#accounts-test-tab .transaction-table tbody tr {
    transition: background-color 0.3s;
}

#accounts-test-tab .transaction-table tbody tr:hover {
    background-color: #f0f8ff !important;
}

#accounts-test-tab .alert-warning {
    border-left: 4px solid #ffc107;
}

/* Highlight test mode buttons */
[data-test-mode="true"] {
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.4);
}

/* Reference Dropdown Trigger - Clean Text Style */
.reference-dropdown-trigger {
    cursor: pointer;
    color: #495057;
    font-weight: 500;
    font-size: 13px;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    display: inline-block;
    position: relative;
}

.reference-dropdown-trigger:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

/* Dropdown Menu Styling */
.transaction-table .dropdown-menu {
    min-width: 200px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    padding: 6px 0;
    margin-top: 2px;
    font-size: 13px;
}

/* Dropdown Items */
.transaction-table .dropdown-item {
    padding: 8px 16px;
    color: #495057;
    transition: all 0.15s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.transaction-table .dropdown-item:hover {
    background-color: #f1f3f5;
    color: #007bff;
    padding-left: 20px;
}

.transaction-table .dropdown-item:active {
    background-color: #e9ecef;
    color: #0056b3;
}

.transaction-table .dropdown-item i {
    width: 18px;
    margin-right: 10px;
    text-align: center;
    font-size: 13px;
    color: #6c757d;
}

.transaction-table .dropdown-item:hover i {
    color: #007bff;
}

/* Dropdown Divider */
.transaction-table .dropdown-divider {
    margin: 4px 0;
    border-top: 1px solid #e9ecef;
}
</style>

</div>
<!-- End Accounts Test Tab -->