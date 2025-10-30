           <!-- Accounts Test Tab -->
           <div class="tab-pane" id="accounts-test-tab">

<div class="card full-width">
    <div class="alert alert-warning">
        <strong>🧪 ACCOUNTS TEST PAGE - Local Development Mode</strong>
        <p><i class="fas fa-exclamation-triangle"></i> This page has FULL READ/WRITE access to the database. Safe for local testing.</p>
        <small>All changes made here will affect the actual database tables (account_client_receipts, etc.)</small>
    </div>

    <div style="margin-bottom: 10px;">
        <a class="btn btn-primary createreceipt" href="javascript:;" role="button" data-test-mode="true">Create Entry</a>
        
        <!-- Additional Test Controls -->
        <button class="btn btn-info" id="export-to-excel" style="margin-left: 10px;">
            <i class="fas fa-file-excel"></i> Export Test Data
        </button>
        <button class="btn btn-secondary" id="view-raw-json" style="margin-left: 10px;">
            <i class="fas fa-code"></i> View Raw Data
        </button>
        
        <!-- Test Controls -->
        <button class="btn btn-success" id="test-python-processing" style="margin-left: 20px;">
            <i class="fas fa-flask"></i> Test Python Processing
        </button>
    </div>

    <!-- TEST: Performance Metrics Display -->
    <div class="alert alert-secondary" id="performance-metrics" style="display:none; margin-bottom: 15px;">
        <h5>⚡ Performance Metrics</h5>
        <div class="row">
            <div class="col-md-3">
                <strong>Processing Time:</strong> <span id="processing-time">-</span>
            </div>
            <div class="col-md-3">
                <strong>Records Processed:</strong> <span id="records-count">-</span>
            </div>
            <div class="col-md-3">
                <strong>Method:</strong> <span id="processing-method">-</span>
            </div>
            <div class="col-md-3">
                <strong>Status:</strong> <span id="processing-status">-</span>
            </div>
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
                        $latest_balance = DB::table('account_client_receipts')
                        ->where('client_id', $fetchedData->id)
                        ->where('client_matter_id', $client_selected_matter_id)
                        ->where('receipt_type', 1)
                        ->orderBy('id', 'desc') // or 'created_at', if you have it
                        ->value('balance_amount');
                        ?>
                        {{ is_numeric($latest_balance) ? '$ ' . number_format($latest_balance, 2) : '$ 0.00' }}

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
                            ?>
                        <tr class="drow_account_ledger ledger-row" data-type="{{$rec_val->client_fund_ledger_type}}" data-matterid="{{$rec_val->client_matter_id}}">
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
                                    <a title="Edit Entry (Test Mode)" class="link-primary edit-ledger-entry" href="javascript:;"
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

                            <td><a target="_blank" href="{{URL::to('/clients/genClientFundLedgerInvoice')}}/{{$rec_val->id}}" title="View Receipt"><?php echo $rec_val->trans_no;?></a></td>

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
    // Test Python Processing Button
    document.getElementById('test-python-processing')?.addEventListener('click', function() {
        const metricsDiv = document.getElementById('performance-metrics');
        metricsDiv.style.display = 'block';
        
        const startTime = performance.now();
        document.getElementById('processing-method').textContent = 'PHP/Python Hybrid';
        document.getElementById('processing-status').textContent = 'Processing...';
        
        // Call the test endpoint
        $.ajax({
            url: "{{ route('clients.test-python-accounting') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_id: '{{ $fetchedData->id }}',
                matter_id: '{{ $client_selected_matter_id ?? "" }}',
                processing_type: 'analytics'
            },
            success: function(response) {
                const endTime = performance.now();
                const totalTime = (endTime - startTime).toFixed(2);
                
                document.getElementById('processing-time').textContent = response.data.processing_time_ms + ' ms (Backend) + ' + totalTime + ' ms (Total)';
                document.getElementById('records-count').textContent = response.data.records_count;
                document.getElementById('processing-method').textContent = response.data.python_service_available ? 'Python Service' : 'PHP (Test Mode)';
                document.getElementById('processing-status').textContent = '✓ Complete';
                
                // Show detailed results
                let message = '✅ Test Completed Successfully!\n\n';
                message += '📊 Results:\n';
                message += '- Backend Processing: ' + response.data.processing_time_ms + ' ms\n';
                message += '- Total Time: ' + totalTime + ' ms\n';
                message += '- Records Processed: ' + response.data.records_count + '\n';
                message += '- Method: ' + (response.data.python_service_available ? 'Python Service' : 'PHP') + '\n\n';
                message += '💡 ' + response.note;
                
                alert(message);
            },
            error: function(xhr, status, error) {
                document.getElementById('processing-status').textContent = '❌ Error';
                alert('Error during processing:\n' + (xhr.responseJSON?.message || error));
            }
        });
    });
    
    // Export to Excel functionality
    document.getElementById('export-to-excel')?.addEventListener('click', function() {
        const clientId = '{{ $fetchedData->id }}';
        const matterId = '{{ $client_selected_matter_id ?? "" }}';
        
        // TODO: Implement Excel export via Python service
        alert('🔄 Export to Excel\n\nThis will be implemented to:\n- Export all accounting data to Excel\n- Use Python pandas for fast processing\n- Include charts and summaries\n\nComing soon!');
    });
    
    // View Raw JSON functionality
    document.getElementById('view-raw-json')?.addEventListener('click', function() {
        $.ajax({
            url: "{{ route('clients.test-python-accounting') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_id: '{{ $fetchedData->id }}',
                matter_id: '{{ $client_selected_matter_id ?? "" }}',
                processing_type: 'raw_data'
            },
            success: function(response) {
                // Create a modal to show JSON
                const jsonStr = JSON.stringify(response, null, 2);
                const modal = $('<div class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">' +
                    '<div class="modal-header"><h5 class="modal-title">Raw Accounting Data (JSON)</h5>' +
                    '<button type="button" class="close" data-dismiss="modal">&times;</button></div>' +
                    '<div class="modal-body"><pre style="max-height: 500px; overflow-y: auto; background: #f5f5f5; padding: 15px; border-radius: 5px;">' + 
                    jsonStr + '</pre></div>' +
                    '<div class="modal-footer">' +
                    '<button class="btn btn-secondary" onclick="navigator.clipboard.writeText(\'' + jsonStr.replace(/'/g, "\\'") + '\')">Copy JSON</button>' +
                    '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>' +
                    '</div></div></div></div>');
                $('body').append(modal);
                modal.modal('show');
                modal.on('hidden.bs.modal', function() { modal.remove(); });
            }
        });
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
    
    // Ensure all existing functionality works on this test page
    console.log('🧪 Accounts Test Page loaded - Full Read/Write access enabled');
    console.log('📊 Client ID: {{ $fetchedData->id }}');
    console.log('📁 Matter ID: {{ $client_selected_matter_id ?? "N/A" }}');
    console.log('✅ All existing modals and forms will work with this page');
});

// Make sure this test page works with all existing modal popups
// The existing JavaScript from the main page will handle:
// - Create Entry modal
// - Edit Entry modal  
// - Save functions
// - All AJAX calls
// These all use class selectors, so they'll work on this test page too!
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

/* Performance metrics styling */
#performance-metrics {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

</div>

