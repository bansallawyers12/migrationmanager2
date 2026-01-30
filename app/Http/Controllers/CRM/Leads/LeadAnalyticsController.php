<?php

namespace App\Http\Controllers\CRM\Leads;

use App\Http\Controllers\Controller;
use App\Services\LeadAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadAnalyticsController extends Controller
{
    protected $analyticsService;
    
    public function __construct(LeadAnalyticsService $analyticsService)
    {
        $this->middleware('auth:admin');
        $this->analyticsService = $analyticsService;
    }
    
    /**
     * Display analytics dashboard
     * Only super admin can access
     */
    public function index(Request $request)
    {
        // Check if user is super admin (role = 1)
        $user = Auth::user();
        if ($user->role != 1) {
            return redirect()->back()->with('error', 'Only super admin can view analytics.');
        }
        
        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        
        // Get comprehensive statistics
        $dashboardStats = $this->analyticsService->getDashboardStats($startDate, $endDate);
        $conversionFunnel = $this->analyticsService->getConversionFunnel($startDate, $endDate);
        $sourcePerformance = $this->analyticsService->getSourcePerformance($startDate, $endDate);
        $agentPerformance = $this->analyticsService->getAgentPerformance($startDate, $endDate);
        $leadQuality = $this->analyticsService->getLeadQualityDistribution($startDate, $endDate);
        
        return view('crm.leads.analytics.dashboard', compact(
            'dashboardStats',
            'conversionFunnel',
            'sourcePerformance',
            'agentPerformance',
            'leadQuality',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Get trends data for charts (AJAX)
     * Only super admin can access
     */
    public function getTrends(Request $request)
    {
        // Check if user is super admin (role = 1)
        if (Auth::user()->role != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $period = $request->get('period', 'month');
        $count = $request->get('count', 12);
        
        $trends = $this->analyticsService->getLeadTrends($period, $count);
        
        return response()->json($trends);
    }
    
    /**
     * Export analytics report
     * Only super admin can export
     */
    public function export(Request $request)
    {
        // Check if user is super admin (role = 1)
        if (Auth::user()->role != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        
        $data = [
            'dashboard_stats' => $this->analyticsService->getDashboardStats($startDate, $endDate),
            'conversion_funnel' => $this->analyticsService->getConversionFunnel($startDate, $endDate),
            'source_performance' => $this->analyticsService->getSourcePerformance($startDate, $endDate),
            'agent_performance' => $this->analyticsService->getAgentPerformance($startDate, $endDate),
            'lead_quality' => $this->analyticsService->getLeadQualityDistribution($startDate, $endDate),
        ];
        
        // For now, return JSON. Can be enhanced to export as PDF/Excel
        return response()->json($data);
    }
    
    /**
     * Get agent comparison data (AJAX)
     * Only super admin can compare agents
     */
    public function compareAgents(Request $request)
    {
        // Check if user is super admin (role = 1)
        if (Auth::user()->role != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $agentIds = $request->get('agent_ids', []);
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        
        $comparison = [];
        
        // Get performance for each agent
        $allAgents = $this->analyticsService->getAgentPerformance($startDate, $endDate);
        
        foreach ($allAgents as $agent) {
            if (in_array($agent['agent_id'], $agentIds)) {
                $comparison[] = $agent;
            }
        }
        
        return response()->json($comparison);
    }
}

