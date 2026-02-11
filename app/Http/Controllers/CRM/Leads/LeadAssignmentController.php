<?php
namespace App\Http\Controllers\CRM\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Lead;

class LeadAssignmentController extends Controller
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
     * Assign lead to a user/agent (deprecated - assignee column removed)
     */
    public function assign(Request $request) 
    {
        return redirect()->back()->with('info', 'Lead assignment has been deprecated.');
    }

    /**
     * Get assignable users for leads
     * Only lead owner can access
     */
    public function getAssignableUsers(Request $request)
    {
        // Check if requesting for a specific lead (ownership verification)
        $leadId = $request->input('lead_id');
        
        if ($leadId) {
            $lead = Lead::find($leadId);
            if (!$lead || $lead->user_id != Auth::user()->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        
        return Admin::select('id', 'first_name', 'last_name', 'email')
            ->where('type', '!=', 'lead')
            ->where('type', '!=', 'client')
            ->where('status', 1)
            ->get();
    }

    /**
     * Bulk assign leads to a user
     * Only super admin can perform bulk assignments
     */
    public function bulkAssign(Request $request)
    {
        // Check if user is super admin (role = 1)
        if (Auth::user()->role != 1) {
            return redirect()->back()->with('error', 'Only super admin can perform bulk assignments');
        }
        
        $requestData = $request->all();
        
        if(!isset($requestData['lead_ids']) || !isset($requestData['assign_to'])) {
            return redirect()->back()->with('error', 'Missing required data');
        }

        return redirect()->back()->with('info', 'Lead assignment has been deprecated.');
    }

    /**
     * Decode string helper method - overrides parent method
     */
    public function decodeString($string = NULL)
    {
        if (base64_encode(base64_decode($string, true)) === $string) {
            return convert_uudecode(base64_decode($string));
        }
        return $string;
    }
}
