<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth:admin');
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show the dashboard
     */
    public function index(DashboardRequest $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData($request);
        
        return view('Admin.dashboard-optimized', $dashboardData);
    }

    /**
     * Save column preferences
     */
    public function saveColumnPreferences(Request $request)
    {
        $this->dashboardService->saveColumnPreferences($request);
        
        return response()->json([
            'success' => true,
            'message' => 'Column preferences saved successfully'
        ]);
    }

    /**
     * Update client matter stage
     */
    public function updateStage(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer',
            'stage_id' => 'required|integer',
        ]);

        $result = $this->dashboardService->updateClientMatterStage(
            $request->item_id, 
            $request->stage_id
        );

        return response()->json($result);
    }

    /**
     * Get dashboard notifications
     */
    public function fetchNotifications(Request $request)
    {
        $notifications = $this->dashboardService->getNotifications();
        
        return response()->json([
            'unseen_notification' => $notifications['count']
        ]);
    }

    /**
     * Get office visit notifications
     */
    public function fetchOfficeVisitNotifications(Request $request)
    {
        $notifications = $this->dashboardService->getOfficeVisitNotifications();
        
        return response()->json($notifications);
    }

    /**
     * Mark notification as seen
     */
    public function markNotificationSeen(Request $request)
    {
        $result = $this->dashboardService->markNotificationAsSeen($request->notification_id);
        
        return response()->json($result);
    }

    /**
     * Extend note deadline
     */
    public function extendDeadlineDate(Request $request)
    {
        $this->validate($request, [
            'note_id' => 'required|integer',
            'unique_group_id' => 'required|string',
            'description' => 'required|string',
            'note_deadline' => 'required|date'
        ]);

        $result = $this->dashboardService->extendNoteDeadline($request->all());
        
        return response()->json($result);
    }

    /**
     * Update task completion status
     */
    public function updateTaskCompleted(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'unique_group_id' => 'required|string'
        ]);

        $result = $this->dashboardService->updateTaskCompleted(
            $request->id, 
            $request->unique_group_id
        );
        
        return response()->json($result);
    }

    /**
     * Get visa expiry messages
     */
    public function fetchVisaExpiryMessages(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|integer'
        ]);

        $message = $this->dashboardService->getVisaExpiryMessage($request->client_id);
        
        return $message;
    }

    /**
     * Check checkin status
     */
    public function checkCheckinStatus(Request $request)
    {
        try {
            $checkinLog = \App\Models\CheckinLog::where('id', $request->checkin_id)->first();
            
            if ($checkinLog) {
                return response()->json([
                    'success' => true,
                    'status' => $checkinLog->status
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Checkin not found']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error checking checkin status']);
        }
    }

    /**
     * Update checkin status
     */
    public function updateCheckinStatus(Request $request)
    { 
        try {
            $checkinLog = \App\Models\CheckinLog::where('id', $request->checkin_id)->first();
            
            if ($checkinLog) {
                $checkinLog->status = $request->status;
                
                if ($request->has('wait_type')) {
                    $checkinLog->wait_type = $request->wait_type;
                }
                
                $saved = $checkinLog->save();
                
                if ($saved) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status updated successfully to ' . $request->status
                    ]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Failed to save status update']);
                }
            }

            return response()->json(['success' => false, 'message' => 'Checkin not found']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating checkin status: ' . $e->getMessage()]);
        }
    }

    /**
     * Get in-person waiting count
     */
    public function fetchInPersonWaitingCount(Request $request)
    {
        $InPersonwaitingCount = \App\Models\CheckinLog::where('status', 0)->count();
        
        return response()->json(['InPersonwaitingCount' => $InPersonwaitingCount]);
    }

    /**
     * Get total activity count
     */
    public function fetchTotalActivityCount(Request $request)
    {
        if (\Auth::user()->role == 1) {
            $assigneesCount = \App\Models\Note::where('type', 'client')
                ->whereNotNull('client_id')
                ->where('folloup', 1)
                ->where('status', 0)
                ->count();
        } else {
            $assigneesCount = \App\Models\Note::where('assigned_to', \Auth::user()->id)
                ->where('type', 'client')
                ->where('folloup', 1)
                ->where('status', 0)
                ->count();
        }
        
        return response()->json(['assigneesCount' => $assigneesCount]);
    }
}
