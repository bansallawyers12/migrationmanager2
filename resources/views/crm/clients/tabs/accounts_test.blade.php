           <!-- Accounts Test Tab -->
           <div class="tab-pane" id="accounts-test-tab">
<?php use Illuminate\Support\Facades\Storage; ?>

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
                                    if($client_doc_list){ 
                                        // Generate S3 URL from the stored filename
                                        if (filter_var($client_doc_list->myfile, FILTER_VALIDATE_URL)) {
                                            $docUrl = $client_doc_list->myfile;
                                        } else {
                                            $client_info = \App\Models\Admin::find($fetchedData->id);
                                            $matter_info = \App\Models\ClientMatter::find($rec_val->client_matter_id);
                                            $client_id = $client_info ? $client_info->client_id : '';
                                            $matter_unique_id = $matter_info ? $matter_info->client_unique_matter_no : '';
                                            
                                            if($matter_unique_id) {
                                                $filePath = $client_id.'/'.$matter_unique_id.'/accounts/'.$client_doc_list->myfile;
                                            } else {
                                                $filePath = $client_id.'/accounts/'.$client_doc_list->myfile;
                                            }
                                            $docUrl = Storage::disk('s3')->url($filePath);
                                        }
                                        ?>
                                        <a target="_blank" title="See Attached Document" class="link-primary" href="<?php echo $docUrl;?>"><i class="fas fa-file-pdf"></i></a>
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
                                        <a class="dropdown-item" href="{{URL::to('/clients/genClientFundReceipt')}}/{{$rec_val->id}}" target="_blank">
                                            <i class="fas fa-eye"></i> View Receipt
                                        </a>
                                        <a class="dropdown-item" href="{{URL::to('/clients/genClientFundReceipt')}}/{{$rec_val->id}}?download=1">
                                            <i class="fas fa-download"></i> Download PDF
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <?php if(!empty($rec_val->uploaded_doc_id)) { 
                                            $uploadedDoc = \App\Models\Document::find($rec_val->uploaded_doc_id);
                                            if($uploadedDoc && !empty($uploadedDoc->myfile)) { 
                                                // Generate S3 URL from the stored filename
                                                if (filter_var($uploadedDoc->myfile, FILTER_VALIDATE_URL)) {
                                                    // Already a full URL
                                                    $docUrl = $uploadedDoc->myfile;
                                                } else {
                                                    // Just a filename - generate S3 URL
                                                    $client_info = \App\Models\Admin::find($fetchedData->id);
                                                    $matter_info = \App\Models\ClientMatter::find($rec_val->client_matter_id);
                                                    $client_id = $client_info ? $client_info->client_id : '';
                                                    $matter_unique_id = $matter_info ? $matter_info->client_unique_matter_no : '';
                                                    
                                                    if($matter_unique_id) {
                                                        $filePath = $client_id.'/'.$matter_unique_id.'/accounts/'.$uploadedDoc->myfile;
                                                    } else {
                                                        $filePath = $client_id.'/accounts/'.$uploadedDoc->myfile;
                                                    }
                                                    $docUrl = Storage::disk('s3')->url($filePath);
                                                }
                                                ?>
                                        <a class="dropdown-item" href="<?php echo $docUrl; ?>" target="_blank">
                                            <i class="fas fa-file-alt"></i> View Uploaded Receipt
                                        </a>
                                        <?php } } ?>
                                        <a class="dropdown-item upload-clientreceipt-doc" href="javascript:;" 
                                            data-receipt-id="<?php echo $rec_val->id; ?>" 
                                            data-client-id="<?php echo $fetchedData->id; ?>"
                                            data-matter-id="<?php echo $rec_val->client_matter_id; ?>">
                                            <i class="fas fa-upload"></i> <?php echo !empty($rec_val->uploaded_doc_id) ? 'Replace' : 'Upload'; ?> Receipt Document
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
                                        
                                        <?php 
                                        // Quick Allocate button for Deposits (to allocate to invoices)
                                        if($rec_val->client_fund_ledger_type == 'Deposit') { 
                                            $isAllocated = !empty($rec_val->invoice_no);
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <?php if($isAllocated) { ?>
                                        <a class="dropdown-item" href="javascript:;" style="color: #28a745; cursor: default;" onclick="return false;">
                                            <i class="fas fa-check-circle"></i> Already Allocated to <?php echo $rec_val->invoice_no; ?>
                                        </a>
                                        <a class="dropdown-item quick-allocate-ledger" href="javascript:;"
                                            data-receipt-id="<?php echo $rec_val->id; ?>"
                                            data-receipt-amount="<?php echo $rec_val->deposit_amount; ?>"
                                            data-matter-id="<?php echo $rec_val->client_matter_id; ?>"
                                            data-client-id="<?php echo $fetchedData->id; ?>"
                                            style="padding-left: 2rem;">
                                            <i class="fas fa-sync-alt"></i> Re-allocate to Different Invoice
                                        </a>
                                        <?php } else { ?>
                                        <a class="dropdown-item quick-allocate-ledger" href="javascript:;"
                                            data-receipt-id="<?php echo $rec_val->id; ?>"
                                            data-receipt-amount="<?php echo $rec_val->deposit_amount; ?>"
                                            data-matter-id="<?php echo $rec_val->client_matter_id; ?>"
                                            data-client-id="<?php echo $fetchedData->id; ?>">
                                            <i class="fas fa-magic"></i> Quick Allocate to Invoice
                                        </a>
                                        <?php } ?>
                                        <?php } ?>
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
                                
                                // Make unpaid/partial invoices drop zones for drag & drop allocation
                                $isDropZone = in_array($inc_val->invoice_status, [0, 2]) && $inc_val->void_invoice != 1;
                                $dropZoneClass = $isDropZone ? 'invoice-drop-zone' : '';
                                ?>
                                <tr class="drow_account_invoice invoiceTrRow <?php echo $trcls;?> <?php echo $dropZoneClass;?>" 
                                    id="invoiceTrRow_<?php echo $inc_val->id;?>" 
                                    data-matterid="{{$inc_val->client_matter_id}}"
                                    data-invoice-no="{{$inc_val->trans_no}}"
                                    data-invoice-balance="{{$inc_val->balance_amount}}"
                                    data-invoice-status="{{$inc_val->invoice_status}}">
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
                                                <a class="dropdown-item" href="{{URL::to('/clients/genInvoice')}}/{{$inc_val->receipt_id}}?download=1">
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
                            $draggableAttr = $isUnallocated ? 'draggable="true"' : '';
                            ?>
                            <tr class="drow_account_office {{$unallocatedClass}}" 
                                data-matterid="{{$off_val->client_matter_id}}"
                                {{$draggableAttr}}
                                data-receipt-id="{{$off_val->id}}"
                                data-receipt-amount="{{$off_val->deposit_amount}}"
                                data-receipt-no="{{$off_val->trans_no}}">
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
                                        if($office_doc_list){ 
                                            // Generate S3 URL from the stored filename
                                            if (filter_var($office_doc_list->myfile, FILTER_VALIDATE_URL)) {
                                                $docUrl = $office_doc_list->myfile;
                                            } else {
                                                $client_info = \App\Models\Admin::find($fetchedData->id);
                                                $matter_info = \App\Models\ClientMatter::find($off_val->client_matter_id);
                                                $client_id = $client_info ? $client_info->client_id : '';
                                                $matter_unique_id = $matter_info ? $matter_info->client_unique_matter_no : '';
                                                
                                                if($matter_unique_id) {
                                                    $filePath = $client_id.'/'.$matter_unique_id.'/accounts/'.$office_doc_list->myfile;
                                                } else {
                                                    $filePath = $client_id.'/accounts/'.$office_doc_list->myfile;
                                                }
                                                $docUrl = Storage::disk('s3')->url($filePath);
                                            }
                                            ?>
                                            <br/>
                                            <a title="See Attached Document" target="_blank" class="link-primary" href="<?php echo $docUrl;?>"><i class="fas fa-file-pdf"></i> Document</a>
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
                                            <a class="dropdown-item" href="{{URL::to('/clients/genOfficeReceipt')}}/{{$off_val->id}}" target="_blank">
                                                <i class="fas fa-eye"></i> View Receipt
                                            </a>
                                            <a class="dropdown-item" href="{{URL::to('/clients/genOfficeReceipt')}}/{{$off_val->id}}?download=1">
                                                <i class="fas fa-download"></i> Download PDF
                                            </a>
                                            <?php } ?>
                                            <div class="dropdown-divider"></div>
                                            <?php if(!empty($off_val->uploaded_doc_id)) { 
                                                $uploadedDoc = \App\Models\Document::find($off_val->uploaded_doc_id);
                                                if($uploadedDoc && !empty($uploadedDoc->myfile)) { 
                                                    // Generate S3 URL from the stored filename
                                                    if (filter_var($uploadedDoc->myfile, FILTER_VALIDATE_URL)) {
                                                        $docUrl = $uploadedDoc->myfile;
                                                    } else {
                                                        $client_info = \App\Models\Admin::find($fetchedData->id);
                                                        $matter_info = \App\Models\ClientMatter::find($off_val->client_matter_id);
                                                        $client_id = $client_info ? $client_info->client_id : '';
                                                        $matter_unique_id = $matter_info ? $matter_info->client_unique_matter_no : '';
                                                        
                                                        if($matter_unique_id) {
                                                            $filePath = $client_id.'/'.$matter_unique_id.'/accounts/'.$uploadedDoc->myfile;
                                                        } else {
                                                            $filePath = $client_id.'/accounts/'.$uploadedDoc->myfile;
                                                        }
                                                        $docUrl = Storage::disk('s3')->url($filePath);
                                                    }
                                                    ?>
                                            <a class="dropdown-item" href="<?php echo $docUrl; ?>" target="_blank">
                                                <i class="fas fa-file-alt"></i> View Uploaded Receipt
                                            </a>
                                            <?php } } ?>
                                            <a class="dropdown-item upload-officereceipt-doc" href="javascript:;" 
                                                data-receipt-id="<?php echo $off_val->id; ?>" 
                                                data-client-id="<?php echo $fetchedData->id; ?>"
                                                data-matter-id="<?php echo $off_val->client_matter_id; ?>">
                                                <i class="fas fa-upload"></i> <?php echo !empty($off_val->uploaded_doc_id) ? 'Replace' : 'Upload'; ?> Receipt Document
                                            </a>
                                            <?php if(!isset($off_val->save_type) || $off_val->save_type == 'draft') { ?>
                                            <div class="dropdown-divider"></div>
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
                                            
                                            <?php 
                                            // Quick Allocate button for unallocated receipts
                                            if(empty($off_val->invoice_no)) { 
                                            ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item quick-allocate-receipt" href="javascript:;"
                                                data-receipt-id="<?php echo $off_val->id; ?>"
                                                data-receipt-amount="<?php echo $off_val->deposit_amount; ?>"
                                                data-matter-id="<?php echo $off_val->client_matter_id; ?>"
                                                data-client-id="<?php echo $fetchedData->id; ?>"
                                                style="color: #ff6b6b; font-weight: 600;">
                                                <i class="fas fa-link"></i> Quick Allocate to Invoice
                                            </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>

                                <td style="text-align: right; vertical-align: middle; color: #28a745; font-weight: 500;">
                                    {{ !empty($off_val->deposit_amount) ? '$ ' . number_format($off_val->deposit_amount, 2) : '' }}
                                    <?php 
                                    // Visual indicator for unallocated receipts
                                    if(empty($off_val->invoice_no)) { 
                                    ?>
                                    <br/>
                                    <small style="color: #dc3545; font-weight: 600;">
                                        <i class="fas fa-exclamation-circle"></i> Unallocated
                                    </small>
                                    <?php } ?>
                                </td>
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
    // SOLUTION 4: Use namespaced event with higher priority to prevent conflicts
    $(document).off('click.testmode', '.createreceipt[data-test-mode="true"]').on('click.testmode', '.createreceipt[data-test-mode="true"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation(); // Prevent other handlers from firing
        
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
            
            // Load invoices for the invoice dropdown
            loadInvoicesForOfficeReceipt(selectedMatter);
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
        
        // FIX 1: Ensure modal element exists and Bootstrap modal is available
        if ($modal.length === 0) {
            console.error('❌ Modal element #createreceiptmodal not found in DOM');
            alert('Error: Receipt modal not found. Please refresh the page.');
            return;
        }
        
        if (typeof $modal.modal !== 'function') {
            console.error('❌ Bootstrap modal plugin not loaded');
            alert('Error: Modal plugin not available. Please refresh the page.');
            return;
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
    
    // FIX 3: Filter functionality with guards for getElementById
    const applyFiltersBtn = document.getElementById('apply-filters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const depositsEl = document.getElementById('filter-deposits');
            const transfersEl = document.getElementById('filter-transfers');
            const refundsEl = document.getElementById('filter-refunds');
            
            // Guard against missing elements
            const showDeposits = depositsEl ? depositsEl.checked : false;
            const showTransfers = transfersEl ? transfersEl.checked : false;
            const showRefunds = refundsEl ? refundsEl.checked : false;
            
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
    }
    
    const resetFiltersBtn = document.getElementById('reset-filters');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            const depositsEl = document.getElementById('filter-deposits');
            const transfersEl = document.getElementById('filter-transfers');
            const refundsEl = document.getElementById('filter-refunds');
            
            // Guard against missing elements
            if (depositsEl) depositsEl.checked = false;
            if (transfersEl) transfersEl.checked = false;
            if (refundsEl) refundsEl.checked = false;
            
            document.querySelectorAll('.ledger-row').forEach(row => {
                row.style.display = '';
            });
        });
    }
    
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
    
    // Edit Office Receipt Entry Handler - REPLACED WITH DIRECT ATTACHMENT ABOVE
    // This delegated handler doesn't work because Bootstrap dropdown stops event propagation
    // Keeping it commented for reference only
    /*
    $(document).on('click', '.edit-office-receipt-entry', function(e) {
        // This code moved to attachEditOfficeReceiptHandlers() function above
    });
    */
    
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
    
    // Function to load invoices for the CREATE office receipt form
    function loadInvoicesForOfficeReceipt(matterId) {
        console.log('📋 Loading invoices for office receipt form, matter:', matterId);
        
        $.ajax({
            url: '{{ route("clients.getInvoicesByMatter") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_matter_id: matterId,
                client_id: '{{ $fetchedData->id }}'
            },
            success: function(response) {
                console.log('✅ Invoices loaded:', response);
                
                var $select = $('#office_receipt_form').find('select[name="invoice_no[]"]');
                $select.empty();
                $select.append('<option value="">Select Invoice (Optional)</option>');
                
                if(response.status && response.invoices && response.invoices.length > 0) {
                    response.invoices.forEach(function(invoice) {
                        $select.append('<option value="' + invoice.trans_no + '">' + 
                            invoice.trans_no + ' - $' + parseFloat(invoice.balance_amount).toFixed(2) + 
                            ' (' + invoice.status + ')</option>');
                    });
                    console.log('✅ Populated ' + response.invoices.length + ' invoices in dropdown');
                } else {
                    console.log('ℹ️ No unpaid invoices found for this matter');
                }
            },
            error: function(xhr) {
                console.error('❌ Failed to load invoices:', xhr);
                console.error('Response:', xhr.responseText);
                var $select = $('#office_receipt_form').find('select[name="invoice_no[]"]');
                $select.html('<option value="">Error loading invoices</option>');
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
    
    // ================================================================
    // QUICK RECEIPT BUTTON - Pre-populate office receipt from invoice
    // ================================================================
    // FIX: Add tab visibility check to prevent duplicate handlers
    $(document).on('click', '.quick-receipt-btn:not(.createreceipt)', function(e) {
        // Only handle if accounts-test tab is active
        const isTestTabActive = $('#accounts-test-tab').hasClass('active') || $('#accounts-test-tab').is(':visible');
        const isAccountsTabActive = $('#accounts-tab').hasClass('active') || $('#accounts-tab').is(':visible');
        
        // If we're in accounts-test tab, handle it here
        // If we're in regular accounts tab, let that handler deal with it
        if (isAccountsTabActive && !isTestTabActive) {
            return; // Let accounts.blade.php handler handle it
        }
        
        e.preventDefault();
        e.stopPropagation();
        
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
        
        // Wait briefly for the form to render, then populate fields
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
        
        // SOLUTION 5: Validate modal is available before opening
        if (typeof $modal.modal !== 'function') {
            console.error('❌ Bootstrap modal not available');
            alert('Error: Modal plugin not loaded. Please refresh the page.');
            return;
        }
        
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
    
    // ================================================================
    // CLIPBOARD PASTE FUNCTIONALITY
    // ================================================================
    let detectedClipboardAmount = null;
    
    // Function to extract amount from clipboard text
    function extractAmount(text) {
        if (!text) return null;
        
        // Remove currency symbols and commas
        text = text.replace(/[$,]/g, '').trim();
        
        // Try to find a number (with optional decimal)
        const match = text.match(/\d+\.?\d*/);
        if (match) {
            const amount = parseFloat(match[0]);
            return isNaN(amount) ? null : amount;
        }
        return null;
    }
    
    // Try to read clipboard when modal opens
    $('#createreceiptmodal').on('shown.bs.modal', function() {
        // Modern Clipboard API
        if (navigator.clipboard && navigator.clipboard.readText) {
            navigator.clipboard.readText()
                .then(text => {
                    const amount = extractAmount(text);
                    if (amount && amount > 0) {
                        detectedClipboardAmount = amount;
                        $('.clipboard-preview').text('($' + amount.toFixed(2) + ' detected)');
                        $('.paste-clipboard-btn').addClass('btn-outline-success').removeClass('btn-outline-primary');
                        console.log('📋 Clipboard amount detected:', amount);
                    } else {
                        detectedClipboardAmount = null;
                        $('.clipboard-preview').text('');
                    }
                })
                .catch(err => {
                    // Clipboard access denied or not available
                    console.log('Clipboard access not available');
                    detectedClipboardAmount = null;
                });
        }
    });
    
    // Handle clipboard paste button click
    $(document).on('click', '.paste-clipboard-btn', function(e) {
        e.preventDefault();
        
        if (detectedClipboardAmount && detectedClipboardAmount > 0) {
            // Find the active amount input in the visible receipt form
            const $activeForm = $('#office_receipt_form:visible');
            if ($activeForm.length > 0) {
                const $amountInput = $activeForm.find('input[name="deposit_amount[]"]').first();
                $amountInput.val(detectedClipboardAmount.toFixed(2));
                $amountInput.focus();
                
                // Visual feedback
                $amountInput.css('background-color', '#d4edda');
                setTimeout(() => {
                    $amountInput.css('background-color', '');
                }, 1000);
                
                console.log('✅ Amount pasted:', detectedClipboardAmount);
            }
        } else {
            // Try to read clipboard again
            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText()
                    .then(text => {
                        const amount = extractAmount(text);
                        if (amount && amount > 0) {
                            const $activeForm = $('#office_receipt_form:visible');
                            if ($activeForm.length > 0) {
                                const $amountInput = $activeForm.find('input[name="deposit_amount[]"]').first();
                                $amountInput.val(amount.toFixed(2));
                                $amountInput.focus();
                                console.log('✅ Amount pasted:', amount);
                            }
                        } else {
                            alert('No valid amount found in clipboard. Please copy a number first.');
                        }
                    })
                    .catch(err => {
                        alert('Could not access clipboard. Please paste manually using Ctrl+V.');
                    });
            } else {
                alert('Clipboard API not available in your browser. Please paste manually.');
            }
        }
    });
    
    // ================================================================
    // REPEAT LAST ENTRY FUNCTIONALITY
    // ================================================================
    let lastOfficeReceiptEntry = null;
    
    // Store last entry when office receipt is successfully saved
    $(document).on('submit', '#office_receipt_form', function() {
        // Capture form data before submission
        const $firstRow = $(this).find('.productitem_office tr.clonedrow_office').first();
        
        lastOfficeReceiptEntry = {
            payment_method: $firstRow.find('select[name="payment_method[]"]').val(),
            description: $firstRow.find('input[name="description[]"]').val(),
            deposit_amount: $firstRow.find('input[name="deposit_amount[]"]').val()
        };
        
        // Store in localStorage for persistence
        localStorage.setItem('lastOfficeReceiptEntry', JSON.stringify(lastOfficeReceiptEntry));
        console.log('💾 Last entry stored:', lastOfficeReceiptEntry);
    });
    
    // Load last entry from localStorage on page load
    if (localStorage.getItem('lastOfficeReceiptEntry')) {
        try {
            lastOfficeReceiptEntry = JSON.parse(localStorage.getItem('lastOfficeReceiptEntry'));
            console.log('📥 Last entry loaded from storage');
        } catch(e) {
            lastOfficeReceiptEntry = null;
        }
    }
    
    // Handle repeat last entry button click
    $(document).on('click', '.repeat-last-entry-btn', function(e) {
        e.preventDefault();
        
        if (!lastOfficeReceiptEntry) {
            alert('No previous office receipt entry found. Create one first, then use this feature.');
            return;
        }
        
        // Find the active office receipt form
        const $activeForm = $('#office_receipt_form:visible');
        if ($activeForm.length === 0) {
            alert('Please select "Direct Office Receipt" first.');
            return;
        }
        
        const $firstRow = $activeForm.find('.productitem_office tr.clonedrow_office').first();
        
        // Set today's date
        const today = new Date();
        const dateStr = ('0' + today.getDate()).slice(-2) + '/' + 
                       ('0' + (today.getMonth() + 1)).slice(-2) + '/' + 
                       today.getFullYear();
        
        $firstRow.find('input[name="trans_date[]"]').val(dateStr);
        $firstRow.find('input[name="entry_date[]"]').val(dateStr);
        
        // Populate from last entry
        $firstRow.find('select[name="payment_method[]"]').val(lastOfficeReceiptEntry.payment_method);
        $firstRow.find('input[name="description[]"]').val(lastOfficeReceiptEntry.description);
        $firstRow.find('input[name="deposit_amount[]"]').val(lastOfficeReceiptEntry.deposit_amount);
        
        // Visual feedback
        $firstRow.find('input, select').each(function() {
            $(this).css('background-color', '#d4edda');
        });
        setTimeout(() => {
            $firstRow.find('input, select').css('background-color', '');
        }, 1000);
        
        console.log('✅ Last entry repeated');
        
        // Focus on amount field for easy adjustment
        $firstRow.find('input[name="deposit_amount[]"]').focus().select();
    });
    
    // ================================================================
    // QUICK ALLOCATE - Smart invoice allocation for unallocated receipts
    // ================================================================
    function handleQuickAllocateClick(button) {
        const $btn = $(button);
        const receiptId = $btn.data('receipt-id');
        const receiptAmount = parseFloat($btn.data('receipt-amount'));
        const matterId = $btn.data('matter-id');
        const clientId = $btn.data('client-id');

        console.log('🔗 Quick Allocate clicked:', {receiptId, receiptAmount, matterId, clientId});

        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Finding matches...');

        $.ajax({
            url: '{{ route("clients.getInvoicesByMatter") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_matter_id: matterId,
                client_id: clientId
            },
            success: function(response) {
                console.log('✅ Response received:', response);
                $btn.html(originalHtml);

                if (!response.status) {
                    alert('Error: ' + (response.message || 'Failed to fetch invoices'));
                    return;
                }

                if (!response.invoices || response.invoices.length === 0) {
                    alert('No unpaid invoices found for this client/matter.');
                    return;
                }

                console.log('📋 Invoices found:', response.invoices.length);

                let exactMatch = null;
                let closeMatches = [];
                let otherInvoices = [];

                response.invoices.forEach(function(invoice) {
                    const invBalance = parseFloat(invoice.balance_amount);
                    const difference = Math.abs(invBalance - receiptAmount);
                    const percentDiff = (difference / (receiptAmount || 1)) * 100;

                    console.log('Invoice:', invoice.trans_no, 'Balance:', invBalance, 'Diff:', difference, 'Percent:', percentDiff);

                    if (difference < 0.01) {
                        exactMatch = invoice;
                    } else if (percentDiff <= 10) {
                        closeMatches.push(invoice);
                    } else {
                        otherInvoices.push(invoice);
                    }
                });

                showAllocationModal(receiptId, receiptAmount, exactMatch, closeMatches, otherInvoices);
            },
            error: function(xhr) {
                $btn.html(originalHtml);
                console.error('❌ AJAX Error:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);

                let errorMsg = 'Error loading invoices. ';
                if (xhr.status === 500) {
                    errorMsg += 'Server error (500). Check browser console for details.';
                } else if (xhr.status === 404) {
                    errorMsg += 'Route not found (404).';
                } else if (xhr.status === 419) {
                    errorMsg += 'CSRF token expired. Please refresh the page.';
                } else {
                    errorMsg += 'Status: ' + xhr.status;
                }

                alert(errorMsg);
            }
        });
    }

    // Use capture phase to intercept click before Bootstrap dropdown stops propagation
    document.addEventListener('click', function(event) {
        const target = event.target.closest('.quick-allocate-receipt');
        if (!target) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (event.stopImmediatePropagation) {
            event.stopImmediatePropagation();
        }

        handleQuickAllocateClick(target);
    }, true);
    
    // ================================================================
    // QUICK ALLOCATE FOR CLIENT FUND LEDGER - Same system for deposits
    // ================================================================
    function handleQuickAllocateLedgerClick(button) {
        const $btn = $(button);
        const receiptId = $btn.data('receipt-id');
        const receiptAmount = parseFloat($btn.data('receipt-amount'));
        const matterId = $btn.data('matter-id');
        const clientId = $btn.data('client-id');

        console.log('🔗 Quick Allocate Ledger clicked:', {receiptId, receiptAmount, matterId, clientId});

        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Finding matches...');

        $.ajax({
            url: '{{ route("clients.getInvoicesByMatter") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_matter_id: matterId,
                client_id: clientId
            },
            success: function(response) {
                console.log('✅ Response received:', response);
                $btn.html(originalHtml);

                if (!response.status) {
                    alert('Error: ' + (response.message || 'Failed to fetch invoices'));
                    return;
                }

                if (!response.invoices || response.invoices.length === 0) {
                    alert('No unpaid invoices found for this client/matter.');
                    return;
                }

                console.log('📋 Invoices found:', response.invoices.length);

                let exactMatch = null;
                let closeMatches = [];
                let otherInvoices = [];

                response.invoices.forEach(function(invoice) {
                    const invBalance = parseFloat(invoice.balance_amount);
                    const difference = Math.abs(invBalance - receiptAmount);
                    const percentDiff = (difference / (receiptAmount || 1)) * 100;

                    console.log('Invoice:', invoice.trans_no, 'Balance:', invBalance, 'Diff:', difference, 'Percent:', percentDiff);

                    if (difference < 0.01) {
                        exactMatch = invoice;
                    } else if (percentDiff <= 10) {
                        closeMatches.push(invoice);
                    } else {
                        otherInvoices.push(invoice);
                    }
                });

                showLedgerAllocationModal(receiptId, receiptAmount, exactMatch, closeMatches, otherInvoices);
            },
            error: function(xhr) {
                $btn.html(originalHtml);
                console.error('❌ AJAX Error:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);

                let errorMsg = 'Error loading invoices. ';
                if (xhr.status === 500) {
                    errorMsg += 'Server error (500). Check browser console for details.';
                } else if (xhr.status === 404) {
                    errorMsg += 'Route not found (404).';
                } else if (xhr.status === 419) {
                    errorMsg += 'CSRF token expired. Please refresh the page.';
                } else {
                    errorMsg += 'Status: ' + xhr.status;
                }

                alert(errorMsg);
            }
        });
    }

    // Capture phase listener for client fund ledger quick allocate
    document.addEventListener('click', function(event) {
        const target = event.target.closest('.quick-allocate-ledger');
        if (!target) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (event.stopImmediatePropagation) {
            event.stopImmediatePropagation();
        }

        // Check if this is a re-allocation (button text contains "Re-allocate")
        const isReallocation = target.textContent.includes('Re-allocate');
        
        if (isReallocation) {
            // Show confirmation dialog
            if (!confirm('⚠️ This deposit is already allocated to an invoice.\n\nRe-allocating will:\n• Remove the existing Fee Transfer\n• Create a new Fee Transfer for the new invoice\n• Update both invoice statuses\n\nAre you sure you want to re-allocate?')) {
                return;
            }
        }

        handleQuickAllocateLedgerClick(target);
    }, true);
    
    function showLedgerAllocationModal(receiptId, receiptAmount, exactMatch, closeMatches, otherInvoices) {
        let modalHtml = '<div class="modal fade" id="quickAllocateLedgerModal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog modal-lg" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">' +
            '<h5 class="modal-title"><i class="fas fa-magic"></i> Allocate Deposit to Invoice</h5>' +
            '<button type="button" class="close" data-dismiss="modal" style="color: white;">' +
            '<span>&times;</span>' +
            '</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="alert alert-info">' +
            '<i class="fas fa-info-circle"></i> Deposit Amount: <strong>$' + receiptAmount.toFixed(2) + '</strong>' +
            '</div>';
        
        if (exactMatch) {
            modalHtml += '<div class="alert alert-success" style="border-left: 4px solid #28a745;">' +
                '<h6><i class="fas fa-bullseye"></i> <strong>Exact Match Found!</strong></h6>' +
                '<p style="margin-bottom: 10px;">' +
                exactMatch.trans_no + ' - $' + parseFloat(exactMatch.balance_amount).toFixed(2) + 
                ' (' + exactMatch.status + ')' +
                '</p>' +
                '<button class="btn btn-success allocate-ledger-to-invoice-btn" ' +
                'data-receipt-id="' + receiptId + '" ' +
                'data-invoice-no="' + exactMatch.trans_no + '">' +
                '<i class="fas fa-check"></i> Allocate to ' + exactMatch.trans_no +
                '</button>' +
                '</div>';
        }
        
        if (closeMatches.length > 0) {
            modalHtml += '<div style="margin-top: 20px;">' +
                '<h6><i class="fas fa-star"></i> Close Matches:</h6>' +
                '<div class="list-group">';
            
            closeMatches.forEach(function(invoice) {
                const invBalance = parseFloat(invoice.balance_amount);
                const difference = Math.abs(invBalance - receiptAmount);
                const diffText = difference > 0 ? ' (diff: $' + difference.toFixed(2) + ')' : '';
                
                modalHtml += '<div class="list-group-item">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                    '<strong>' + invoice.trans_no + '</strong> - ' +
                    '$' + invBalance.toFixed(2) + 
                    ' (' + invoice.status + ')' + diffText +
                    '<br/><small class="text-muted">' + invoice.description + '</small>' +
                    '</div>' +
                    '<button class="btn btn-sm btn-primary allocate-ledger-to-invoice-btn" ' +
                    'data-receipt-id="' + receiptId + '" ' +
                    'data-invoice-no="' + invoice.trans_no + '">' +
                    '<i class="fas fa-link"></i> Allocate' +
                    '</button>' +
                    '</div>' +
                    '</div>';
            });
            
            modalHtml += '</div></div>';
        }
        
        if (otherInvoices.length > 0) {
            modalHtml += '<div style="margin-top: 20px;">' +
                '<h6><i class="fas fa-list"></i> Other Unpaid Invoices:</h6>' +
                '<div class="list-group" style="max-height: 300px; overflow-y: auto;">';
            
            otherInvoices.forEach(function(invoice) {
                const invBalance = parseFloat(invoice.balance_amount);
                const difference = Math.abs(invBalance - receiptAmount);
                const diffText = difference > 0 ? ' (diff: $' + difference.toFixed(2) + ')' : '';
                
                modalHtml += '<div class="list-group-item">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                    '<strong>' + invoice.trans_no + '</strong> - ' +
                    '$' + invBalance.toFixed(2) + 
                    ' (' + invoice.status + ')' + diffText +
                    '</div>' +
                    '<button class="btn btn-sm btn-primary allocate-ledger-to-invoice-btn" ' +
                    'data-receipt-id="' + receiptId + '" ' +
                    'data-invoice-no="' + invoice.trans_no + '">' +
                    '<i class="fas fa-link"></i> Allocate' +
                    '</button>' +
                    '</div>' +
                    '</div>';
            });
            
            modalHtml += '</div></div>';
        }
        
        // If no invoices in any category, show a message
        if (!exactMatch && closeMatches.length === 0 && otherInvoices.length === 0) {
            modalHtml += '<div class="alert alert-warning">' +
                '<i class="fas fa-exclamation-triangle"></i> No unpaid invoices found for this client/matter.' +
                '</div>';
        }
        
        modalHtml += '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        // Remove existing modal if any
        $('#quickAllocateLedgerModal').remove();
        
        // Add to body and show
        $('body').append(modalHtml);
        $('#quickAllocateLedgerModal').modal('show');
    }
    
    // Handle allocation button click in ledger modal
    $(document).on('click', '.allocate-ledger-to-invoice-btn', function(e) {
        e.preventDefault();
        
        const receiptId = $(this).data('receipt-id');
        const invoiceNo = $(this).data('invoice-no');
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Allocating...');
        
        console.log('🔗 Allocating ledger entry:', receiptId, 'to invoice:', invoiceNo);
        
        // Update the ledger entry with the invoice number
        $.ajax({
            url: '{{ route("clients.updateClientFundLedger") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: receiptId,
                client_id: '{{ $fetchedData->id }}',
                invoice_no: invoiceNo
            },
            success: function(response) {
                console.log('✅ Allocation response:', response);
                if (response.status) {
                    $('#quickAllocateLedgerModal').modal('hide');
                    
                    // Show success message
                    alert('✅ Deposit successfully allocated to ' + invoiceNo + '!');
                    
                    // Reload page to show updated allocation
                    localStorage.setItem('activeTab', 'accounts-test');
                    location.reload();
                } else {
                    $btn.prop('disabled', false).html(originalHtml);
                    alert('Error: ' + (response.message || 'Failed to allocate deposit'));
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                console.error('❌ Allocation error:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                let errorMsg = 'An error occurred while allocating. ';
                if (xhr.status === 500) {
                    errorMsg += 'Server error (500). Check browser console for details.';
                } else if (xhr.status === 404) {
                    errorMsg += 'Route not found (404).';
                } else if (xhr.status === 419) {
                    errorMsg += 'CSRF token expired. Please refresh the page.';
                } else {
                    errorMsg += 'Status: ' + xhr.status;
                }
                
                alert(errorMsg);
            }
        });
    });
    
    function showAllocationModal(receiptId, receiptAmount, exactMatch, closeMatches, otherInvoices) {
        let modalHtml = '<div class="modal fade" id="quickAllocateModal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog modal-lg" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">' +
            '<h5 class="modal-title"><i class="fas fa-magic"></i> Smart Invoice Allocation</h5>' +
            '<button type="button" class="close" data-dismiss="modal" style="color: white;">' +
            '<span>&times;</span>' +
            '</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="alert alert-info">' +
            '<i class="fas fa-info-circle"></i> Receipt Amount: <strong>$' + receiptAmount.toFixed(2) + '</strong>' +
            '</div>';
        
        if (exactMatch) {
            modalHtml += '<div class="alert alert-success" style="border-left: 4px solid #28a745;">' +
                '<h6><i class="fas fa-bullseye"></i> <strong>Exact Match Found!</strong></h6>' +
                '<p style="margin-bottom: 10px;">' +
                exactMatch.trans_no + ' - $' + parseFloat(exactMatch.balance_amount).toFixed(2) + 
                ' (' + exactMatch.status + ')' +
                '</p>' +
                '<button class="btn btn-success allocate-to-invoice-btn" ' +
                'data-receipt-id="' + receiptId + '" ' +
                'data-invoice-no="' + exactMatch.trans_no + '">' +
                '<i class="fas fa-check"></i> Allocate to ' + exactMatch.trans_no +
                '</button>' +
                '</div>';
        }
        
        if (closeMatches.length > 0) {
            modalHtml += '<div style="margin-top: 20px;">' +
                '<h6><i class="fas fa-star"></i> Close Matches:</h6>' +
                '<div class="list-group">';
            
            closeMatches.forEach(function(invoice) {
                modalHtml += '<div class="list-group-item">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                    '<strong>' + invoice.trans_no + '</strong> - ' +
                    '$' + parseFloat(invoice.balance_amount).toFixed(2) + 
                    ' (' + invoice.status + ')' +
                    '<br/><small class="text-muted">' + invoice.description + '</small>' +
                    '</div>' +
                    '<button class="btn btn-sm btn-primary allocate-to-invoice-btn" ' +
                    'data-receipt-id="' + receiptId + '" ' +
                    'data-invoice-no="' + invoice.trans_no + '">' +
                    '<i class="fas fa-link"></i> Allocate' +
                    '</button>' +
                    '</div>' +
                    '</div>';
            });
            
            modalHtml += '</div></div>';
        }
        
        if (otherInvoices.length > 0) {
            modalHtml += '<div style="margin-top: 20px;">' +
                '<h6><i class="fas fa-list"></i> Other Unpaid Invoices:</h6>' +
                '<div class="list-group" style="max-height: 300px; overflow-y: auto;">';
            
            otherInvoices.forEach(function(invoice) {
                const invBalance = parseFloat(invoice.balance_amount);
                const difference = Math.abs(invBalance - receiptAmount);
                const diffText = difference > 0 ? ' (diff: $' + difference.toFixed(2) + ')' : '';
                
                modalHtml += '<div class="list-group-item">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                    '<strong>' + invoice.trans_no + '</strong> - ' +
                    '$' + invBalance.toFixed(2) + 
                    ' (' + invoice.status + ')' + diffText +
                    '</div>' +
                    '<button class="btn btn-sm btn-primary allocate-to-invoice-btn" ' +
                    'data-receipt-id="' + receiptId + '" ' +
                    'data-invoice-no="' + invoice.trans_no + '">' +
                    '<i class="fas fa-link"></i> Allocate' +
                    '</button>' +
                    '</div>' +
                    '</div>';
            });
            
            modalHtml += '</div></div>';
        }
        
        // If no invoices in any category, show a message
        if (!exactMatch && closeMatches.length === 0 && otherInvoices.length === 0) {
            modalHtml += '<div class="alert alert-warning">' +
                '<i class="fas fa-exclamation-triangle"></i> No unpaid invoices found for this client/matter.' +
                '</div>';
        }
        
        modalHtml += '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        // Remove existing modal if any
        $('#quickAllocateModal').remove();
        
        // Add to body and show
        $('body').append(modalHtml);
        $('#quickAllocateModal').modal('show');
    }
    
    // Handle allocation button click in modal
    $(document).on('click', '.allocate-to-invoice-btn', function(e) {
        e.preventDefault();
        
        const receiptId = $(this).data('receipt-id');
        const invoiceNo = $(this).data('invoice-no');
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Allocating...');
        
        console.log('🔗 Allocating receipt:', receiptId, 'to invoice:', invoiceNo);
        
        // Update the receipt with the invoice number
        $.ajax({
            url: '{{ route("clients.updateOfficeReceipt") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: receiptId,
                client_id: '{{ $fetchedData->id }}',  // Include client_id for activity log
                invoice_no: invoiceNo,
                save_type: 'final'
            },
            success: function(response) {
                console.log('✅ Allocation response:', response);
                if (response.status) {
                    $('#quickAllocateModal').modal('hide');
                    
                    // Show success message
                    alert('✅ Receipt successfully allocated to ' + invoiceNo + '!');
                    
                    // Reload page to show updated allocation
                    localStorage.setItem('activeTab', 'accounts-test');
                    location.reload();
                } else {
                    $btn.prop('disabled', false).html(originalHtml);
                    alert('Error: ' + (response.message || 'Failed to allocate receipt'));
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                console.error('❌ Allocation error:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                let errorMsg = 'An error occurred while allocating. ';
                if (xhr.status === 500) {
                    errorMsg += 'Server error (500). Check browser console for details.';
                } else if (xhr.status === 404) {
                    errorMsg += 'Route not found (404).';
                } else if (xhr.status === 419) {
                    errorMsg += 'CSRF token expired. Please refresh the page.';
                } else {
                    errorMsg += 'Status: ' + xhr.status;
                }
                
                alert(errorMsg);
            }
        });
    });
    
    // ================================================================
    // DRAG & DROP ALLOCATION - Visual drag-and-drop receipt allocation
    // ================================================================
    let draggedReceipt = null;
    
    // Handle drag start on unallocated receipts
    $(document).on('dragstart', 'tr.unallocated-receipt[draggable="true"]', function(e) {
        draggedReceipt = {
            id: $(this).data('receipt-id'),
            amount: $(this).data('receipt-amount'),
            receiptNo: $(this).data('receipt-no')
        };
        
        $(this).css('opacity', '0.5');
        
        // Set drag data
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text/html', $(this).html());
        
        // Add visual feedback to drop zones
        $('.invoice-drop-zone').addClass('drag-active');
        
        console.log('🎯 Dragging receipt:', draggedReceipt);
    });
    
    // Handle drag end
    $(document).on('dragend', 'tr.unallocated-receipt[draggable="true"]', function(e) {
        $(this).css('opacity', '1');
        $('.invoice-drop-zone').removeClass('drag-active drag-over');
    });
    
    // Handle drag over invoice rows
    $(document).on('dragover', 'tr.invoice-drop-zone', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        
        $(this).addClass('drag-over');
        
        return false;
    });
    
    // Handle drag leave
    $(document).on('dragleave', 'tr.invoice-drop-zone', function(e) {
        $(this).removeClass('drag-over');
    });
    
    // Handle drop on invoice
    $(document).on('drop', 'tr.invoice-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $(this).removeClass('drag-over');
        $('.invoice-drop-zone').removeClass('drag-active');
        
        if (!draggedReceipt) return false;
        
        const invoiceNo = $(this).data('invoice-no');
        const invoiceBalance = parseFloat($(this).data('invoice-balance'));
        const receiptAmount = parseFloat(draggedReceipt.amount);
        
        console.log('💧 Dropped on invoice:', invoiceNo);
        
        // Show confirmation with amount info
        const amountMatch = Math.abs(invoiceBalance - receiptAmount) < 0.01;
        const matchText = amountMatch ? '✓ EXACT MATCH' : '(Partial payment)';
        const confirmMsg = `Allocate ${draggedReceipt.receiptNo} ($${receiptAmount.toFixed(2)}) to ${invoiceNo} ($${invoiceBalance.toFixed(2)})?\n\n${matchText}`;
        
        if (confirm(confirmMsg)) {
            // Perform allocation
            allocateReceiptToInvoice(draggedReceipt.id, invoiceNo);
        }
        
        draggedReceipt = null;
        return false;
    });
    
    function allocateReceiptToInvoice(receiptId, invoiceNo) {
        console.log('🔗 Allocating receipt', receiptId, 'to', invoiceNo);
        
        // Show loading indicator
        const $loadingDiv = $('<div class="allocation-loading">' +
            '<div class="spinner-border text-primary" role="status"></div>' +
            '<p>Allocating receipt to ' + invoiceNo + '...</p>' +
            '</div>');
        $('body').append($loadingDiv);
        
        $.ajax({
            url: '{{ route("clients.updateOfficeReceipt") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: receiptId,
                invoice_no: invoiceNo,
                save_type: 'final'
            },
            success: function(response) {
                $loadingDiv.remove();
                
                if (response.status) {
                    // Show success animation
                    const $successDiv = $('<div class="allocation-success">' +
                        '<div class="success-checkmark">' +
                        '<i class="fas fa-check-circle"></i>' +
                        '</div>' +
                        '<p>✅ Receipt successfully allocated to ' + invoiceNo + '!</p>' +
                        '</div>');
                    $('body').append($successDiv);
                    
                    setTimeout(function() {
                        $successDiv.fadeOut(function() {
                            $(this).remove();
                            // Reload page
                            localStorage.setItem('activeTab', 'accounts-test');
                            location.reload();
                        });
                    }, 1500);
                } else {
                    alert('Error: ' + (response.message || 'Failed to allocate receipt'));
                }
            },
            error: function(xhr) {
                $loadingDiv.remove();
                console.error('Allocation error:', xhr);
                alert('An error occurred while allocating. Please try again.');
            }
        });
    }
    
    // Ensure all existing functionality works on this test page
    console.log('🧪 Accounts Test Page loaded - Full Read/Write access enabled');
    console.log('📊 Client ID: {{ $fetchedData->id }}');
    console.log('📁 Matter ID: {{ $client_selected_matter_id ?? "N/A" }}');
    console.log('✅ All modals and forms are functional');
    console.log('✅ Office Receipt Edit functionality enabled');
    console.log('✅ Quick Receipt functionality enabled');
    console.log('✅ Quick Allocate functionality enabled');
    console.log('✅ Drag & Drop Allocation enabled');
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

.unallocated-receipt td .reference-dropdown-trigger {
    color: #dc3545;
    font-weight: 600;
}

/* ================================================================
   DRAG & DROP STYLES
   ================================================================ */

/* Draggable unallocated receipts */
tr.unallocated-receipt[draggable="true"] {
    cursor: move;
    user-select: none;
}

tr.unallocated-receipt[draggable="true"]:hover {
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    transform: translateY(-1px);
    transition: all 0.2s;
}

/* Invoice drop zones */
tr.invoice-drop-zone {
    position: relative;
    transition: all 0.3s ease;
}

/* Active drop zones (when dragging) */
tr.invoice-drop-zone.drag-active {
    background-color: #e8f4f8 !important;
    border-left: 4px solid #667eea !important;
}

tr.invoice-drop-zone.drag-active::before {
    content: "💧 Drop here to allocate";
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #667eea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    z-index: 10;
    pointer-events: none;
}

/* Hover effect when dragging over */
tr.invoice-drop-zone.drag-over {
    background-color: #d1e7dd !important;
    border-left: 6px solid #28a745 !important;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

tr.invoice-drop-zone.drag-over::before {
    content: "✓ Release to allocate";
    background: #28a745;
}

/* Loading overlay */
.allocation-loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.allocation-loading .spinner-border {
    width: 4rem;
    height: 4rem;
    border-width: 0.4rem;
}

.allocation-loading p {
    color: white;
    font-size: 18px;
    font-weight: 600;
    margin-top: 20px;
}

/* Success overlay */
.allocation-success {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(40, 167, 69, 0.95);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.allocation-success .success-checkmark {
    font-size: 100px;
    color: white;
    animation: scaleIn 0.5s ease-out;
}

.allocation-success p {
    color: white;
    font-size: 24px;
    font-weight: 600;
    margin-top: 20px;
    animation: fadeInUp 0.5s ease-out 0.3s both;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Drag cursor indicator */
tr.unallocated-receipt[draggable="true"]::after {
    content: "🖱️ Drag to invoice";
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #ff6b6b;
    color: white;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

tr.unallocated-receipt[draggable="true"]:hover::after {
    opacity: 1;
}
</style>

<!-- Upload Receipt Document Modal -->
<div class="modal fade" id="uploadReceiptDocModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload"></i> Upload Receipt Document
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadReceiptDocForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="upload_receipt_id" name="receipt_id">
                    <input type="hidden" id="upload_client_id" name="clientid">
                    <input type="hidden" id="upload_matter_id" name="client_matter_id">
                    <input type="hidden" id="upload_receipt_type" name="receipt_type">
                    <input type="hidden" name="doctype" value="receipt_uploads">
                    <input type="hidden" name="type" value="client">
                    
                    <div class="form-group">
                        <label>Select Receipt Document <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="document_upload" id="receipt_document_upload" required 
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This document will be attached to the selected receipt entry for verification purposes.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // FIX: Bootstrap dropdown stops event propagation completely
    // Solution: Directly attach handlers to each button after page load
    
    // Function to attach upload receipt handlers
    function attachUploadHandlers() {
        // Upload Client Receipt Document - direct attachment
        $('.upload-clientreceipt-doc').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let receiptId = $(this).data('receipt-id');
            let clientId = $(this).data('client-id');
            let matterId = $(this).data('matter-id');
            
            $('#upload_receipt_id').val(receiptId);
            $('#upload_client_id').val(clientId);
            $('#upload_matter_id').val(matterId);
            $('#upload_receipt_type').val('client');
            $('#uploadReceiptDocModal').modal('show');
        });
        
        // Upload Office Receipt Document - direct attachment
        $('.upload-officereceipt-doc').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let receiptId = $(this).data('receipt-id');
            let clientId = $(this).data('client-id');
            let matterId = $(this).data('matter-id');
            
            $('#upload_receipt_id').val(receiptId);
            $('#upload_client_id').val(clientId);
            $('#upload_matter_id').val(matterId);
            $('#upload_receipt_type').val('office');
            $('#uploadReceiptDocModal').modal('show');
        });
        
        // Upload Journal Receipt Document - direct attachment
        $('.upload-journalreceipt-doc').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let receiptId = $(this).data('receipt-id');
            let clientId = $(this).data('client-id');
            let matterId = $(this).data('matter-id');
            
            $('#upload_receipt_id').val(receiptId);
            $('#upload_client_id').val(clientId);
            $('#upload_matter_id').val(matterId);
            $('#upload_receipt_type').val('journal');
            $('#uploadReceiptDocModal').modal('show');
        });
    }
    
    // FIX: Move modals to body to prevent z-index/positioning issues
    // Bootstrap modals must be direct children of body to work properly
    if ($('#uploadReceiptDocModal').parent().attr('id') !== 'body' && !$('#uploadReceiptDocModal').parent().is('body')) {
        $('#uploadReceiptDocModal').appendTo('body');
        console.log('✅ Upload Receipt Modal moved to body level');
    }
    
    // Also move editOfficeReceiptModal if it's not already at body level
    if ($('#editOfficeReceiptModal').length > 0 && !$('#editOfficeReceiptModal').parent().is('body')) {
        $('#editOfficeReceiptModal').appendTo('body');
        console.log('✅ Edit Office Receipt Modal moved to body level');
    }
    
    // Also move editLedgerModal if it's not already at body level
    if ($('#editLedgerModal').length > 0 && !$('#editLedgerModal').parent().is('body')) {
        $('#editLedgerModal').appendTo('body');
        console.log('✅ Edit Ledger Modal moved to body level');
    }
    
    // Function to attach edit office receipt handlers
    function attachEditOfficeReceiptHandlers() {
        $('.edit-office-receipt-entry').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
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
    }
    
    // Attach handlers on page load
    attachUploadHandlers();
    attachEditOfficeReceiptHandlers();
    
    // Re-attach after any dynamic content updates
    $(document).on('DOMNodeInserted', function(e) {
        if ($(e.target).find('.upload-clientreceipt-doc, .upload-officereceipt-doc, .upload-journalreceipt-doc').length) {
            attachUploadHandlers();
        }
        if ($(e.target).find('.edit-office-receipt-entry').length) {
            attachEditOfficeReceiptHandlers();
        }
    });
    
    // Handle form submission
    $('#uploadReceiptDocForm').on('submit', function(e) {
        e.preventDefault();
        
        let receiptType = $('#upload_receipt_type').val();
        let formData = new FormData(this);
        let uploadUrl = '';
        
        // Determine the correct endpoint
        if (receiptType === 'client') {
            uploadUrl = '{{ route("clients.uploadclientreceiptdocument") }}';
        } else if (receiptType === 'office') {
            uploadUrl = '{{ route("clients.uploadofficereceiptdocument") }}';
        } else if (receiptType === 'journal') {
            uploadUrl = '{{ route("clients.uploadjournalreceiptdocument") }}';
        }
        
        // Show loading state
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        
        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    // Use toastr if available, otherwise use alert
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Document uploaded successfully');
                    } else {
                        alert(response.message || 'Document uploaded successfully');
                    }
                    $('#uploadReceiptDocModal').modal('hide');
                    $('#uploadReceiptDocForm')[0].reset();
                    
                    // Reload the page to show the uploaded document
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to upload document');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to upload document'));
                    }
                }
            },
            error: function(xhr) {
                console.error('Upload error:', xhr);
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred while uploading the document');
                } else {
                    alert('Error: An error occurred while uploading the document');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#uploadReceiptDocModal').on('hidden.bs.modal', function() {
        $('#uploadReceiptDocForm')[0].reset();
    });
});
</script>

</div>
<!-- End Accounts Test Tab -->