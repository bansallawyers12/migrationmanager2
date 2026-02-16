<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Services\StaffLoginAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffLoginAnalyticsController extends Controller
{
    public function __construct(
        protected StaffLoginAnalyticsService $analytics
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display the staff login analytics dashboard
     */
    public function index(Request $request)
    {
        return view('crm.staff-login-analytics.index');
    }

    /**
     * Get daily login data
     */
    public function daily(Request $request): JsonResponse
    {
        $staffIdParam = $request->query('staff_id') ?? $request->query('user_id');
        $staffId = $staffIdParam !== null && $staffIdParam !== '' ? (int) $staffIdParam : null;
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

        $data = $this->analytics->getDailyLogins($staffId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get weekly login data
     */
    public function weekly(Request $request): JsonResponse
    {
        $staffIdParam = $request->query('staff_id') ?? $request->query('user_id');
        $staffId = $staffIdParam !== null && $staffIdParam !== '' ? (int) $staffIdParam : null;
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

        $data = $this->analytics->getWeeklyLogins($staffId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get monthly login data
     */
    public function monthly(Request $request): JsonResponse
    {
        $staffIdParam = $request->query('staff_id') ?? $request->query('user_id');
        $staffId = $staffIdParam !== null && $staffIdParam !== '' ? (int) $staffIdParam : null;
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

        $data = $this->analytics->getMonthlyLogins($staffId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get hourly distribution
     */
    public function hourly(Request $request): JsonResponse
    {
        $staffIdParam = $request->query('staff_id') ?? $request->query('user_id');
        $staffId = $staffIdParam !== null && $staffIdParam !== '' ? (int) $staffIdParam : null;
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

        $data = $this->analytics->getHourlyDistribution($staffId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $staffIdParam = $request->query('staff_id') ?? $request->query('user_id');
        $staffId = $staffIdParam !== null && $staffIdParam !== '' ? (int) $staffIdParam : null;
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

        $data = $this->analytics->getSummary($staffId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get top staff by login count
     */
    public function topStaff(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

        $data = $this->analytics->getTopStaff((int) $limit, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get trends comparison
     */
    public function trends(Request $request): JsonResponse
    {
        $staffIdParam = $request->query('staff_id') ?? $request->query('user_id');
        $staffId = $staffIdParam !== null && $staffIdParam !== '' ? (int) $staffIdParam : null;
        $period = $request->query('period', 'month'); // day, week, month

        $data = $this->analytics->getTrends($staffId, $period);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
