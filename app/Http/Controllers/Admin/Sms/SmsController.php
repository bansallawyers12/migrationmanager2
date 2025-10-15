<?php

namespace App\Http\Controllers\Admin\Sms;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Services\Sms\UnifiedSmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SmsController
 * 
 * Handles SMS dashboard, history, and manual sending
 * Sprint 4 will expand this with full UI
 */
class SmsController extends Controller
{
    protected $smsManager;

    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->middleware('auth:admin');
        $this->smsManager = $smsManager;
    }

    /**
     * Show SMS dashboard (Sprint 4)
     */
    public function dashboard(Request $request)
    {
        // Get today's statistics
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        
        $stats = [
            'total_today' => SmsLog::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
            'cellcast_today' => SmsLog::whereBetween('created_at', [$todayStart, $todayEnd])
                                      ->where('provider', 'cellcast')->count(),
            'twilio_today' => SmsLog::whereBetween('created_at', [$todayStart, $todayEnd])
                                    ->where('provider', 'twilio')->count(),
            'failed_today' => SmsLog::whereBetween('created_at', [$todayStart, $todayEnd])
                                    ->where('status', 'failed')->count(),
        ];
        
        // Get recent SMS activity (last 10)
        $recentSms = SmsLog::with(['client', 'contact', 'sender'])
                           ->orderBy('created_at', 'desc')
                           ->limit(10)
                           ->get();
        
        return view('Admin.sms.dashboard', compact('stats', 'recentSms'));
    }

    /**
     * Show SMS history (Sprint 4)
     */
    public function history(Request $request)
    {
        // TODO: Implement in Sprint 4
        // Will show:
        // - Filterable SMS log table
        // - Search by phone, client, date range
        // - Export functionality
        
        $query = SmsLog::with(['client', 'contact', 'sender'])
            ->orderBy('created_at', 'desc');

        // Add filters when implemented
        // if ($request->filled('client_id')) { ... }
        // if ($request->filled('date_from')) { ... }

        $smsLogs = $query->paginate(50);

        return view('Admin.sms.history.index', compact('smsLogs'));
    }

    /**
     * Show single SMS details (Sprint 4)
     */
    public function show($id)
    {
        // TODO: Implement in Sprint 4
        $smsLog = SmsLog::with(['client', 'contact', 'sender'])->findOrFail($id);
        
        return view('Admin.sms.history.show', compact('smsLog'));
    }

    /**
     * Get SMS statistics (API endpoint)
     */
    public function statistics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $stats = $this->smsManager->getStatistics($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Check SMS delivery status (API endpoint)
     */
    public function checkStatus($smsLogId)
    {
        $result = $this->smsManager->getDeliveryStatus($smsLogId);

        return response()->json($result);
    }
}

