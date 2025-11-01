<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\clientServiceTaken;
use App\Models\AccountClientReceipt;
use App\Mail\HubdocInvoiceMail;
use Auth;
use PDF;
use Carbon\Carbon;

/**
 * ClientAccountsController
 * 
 * Handles all accounting operations including invoices, receipts,
 * ledger management, and financial transactions.
 * 
 * Maps to: resources/views/Admin/clients/tabs/accounts.blade.php
 */
class ClientAccountsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Methods to be moved from ClientsController:
     * 
     * - saveaccountreport() - Save client fund receipt
     * - saveinvoicereport() - Save invoice
     * - saveadjustinvoicereport() - Save adjust invoice
     * - saveofficereport() - Save office receipt
     * - savejournalreport() - Save journal receipt
     * - createservicetaken() - Create service taken
     * - removeservicetaken() - Remove service taken
     * - getservicetaken() - Get service taken
     * - genInvoice() - Generate invoice PDF
     * - uploadclientreceiptdocument() - Upload client receipt document
     * - uploadofficereceiptdocument() - Upload office receipt document
     * - uploadjournalreceiptdocument() - Upload journal receipt document
     * - invoicelist() - List invoices
     * - void_invoice() - Void invoice
     * - clientreceiptlist() - List client receipts
     * - officereceiptlist() - List office receipts
     * - journalreceiptlist() - List journal receipts
     * - validate_receipt() - Validate receipt
     * - delete_receipt() - Delete receipt
     * - printPreview() - Print preview
     * - isAnyInvoiceNoExistInDB() - Check if invoice number exists
     * - listOfInvoice() - Get list of invoices
     * - clientLedgerBalanceAmount() - Get client ledger balance
     * - getInfoByReceiptId() - Get receipt information by ID
     * - getTopReceiptValInDB() - Get top receipt value
     * - getTopInvoiceNoFromDB() - Get top invoice number
     * - genClientFundReceipt() - Generate client fund receipt
     * - genofficereceiptInvoice() - Generate office receipt invoice
     * - updateClientFundsLedger() - Update client funds ledger
     * - getInvoiceAmount() - Get invoice amount
     * - createTransactionNumber() (private) - Create transaction number
     * - createInvoiceNumber() (private) - Create invoice number
     * - generateTransNo() (private) - Generate transaction number
     * - generateInvoiceNo() (private) - Generate invoice number
     * - getNextReceiptId() (private) - Get next receipt ID
     */
}

