<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientPortalBillingController extends Controller
{
    /**
     * List of Billing (Invoices)
     * GET /api/billing/list
     *
     * Returns invoices where:
     * - client_application_sent = 1 (application sent to client)
     * - invoice_status = 0 (Pending) OR invoice_status = 1 (Paid)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $invoices = DB::table('account_client_receipts')
                ->select(
                    'trans_no',
                    'receipt_id',
                    DB::raw('MAX(COALESCE(balance_amount, withdraw_amount, 0)) as balance_amount'),
                    DB::raw('MAX(COALESCE(invoice_status, 0)) as invoice_status'),
                    DB::raw('MAX(description) as description'),
                    DB::raw('MAX(trans_date) as latest_trans_date'),
                    DB::raw('MAX(client_application_sent_at) as client_application_sent_at'),
                    DB::raw('MAX(client_matter_id) as client_matter_id')
                )
                ->where('client_id', $clientId)
                ->where('receipt_type', 3)
                ->where('client_application_sent', 1)
                ->whereIn('invoice_status', [0, 1])
                ->where(function ($query) {
                    $query->whereNull('void_invoice')
                        ->orWhere('void_invoice', 0);
                })
                ->groupBy('trans_no', 'receipt_id')
                ->orderByDesc('latest_trans_date')
                ->get();

            $statusMap = [0 => 'Pending', 1 => 'Paid'];

            $invoices = $invoices->map(function ($invoice) use ($statusMap) {
                return [
                    'trans_no' => $invoice->trans_no,
                    'receipt_id' => $invoice->receipt_id,
                    'balance_amount' => (float) $invoice->balance_amount,
                    'invoice_status' => (int) $invoice->invoice_status,
                    'status' => $statusMap[$invoice->invoice_status] ?? 'Unknown',
                    'description' => $invoice->description,
                    'trans_date' => $invoice->latest_trans_date,
                    'client_application_sent_at' => $invoice->client_application_sent_at,
                    'client_matter_id' => $invoice->client_matter_id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'invoices' => $invoices,
                    'count' => $invoices->count(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Billing List API Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch billing list',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
