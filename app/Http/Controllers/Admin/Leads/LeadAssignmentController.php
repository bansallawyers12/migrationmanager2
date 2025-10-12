<?php
namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\Lead;
use Auth;
use Config;

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
     * Assign lead to a user/agent
     */
    public function assign(Request $request) 
    {
        $requestData = $request->all();
        $id = $this->decodeString($requestData['mlead_id']);
        
        // Using Lead model
        if(Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->exists())
        {
            $lead = Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->first();
            
            if($lead->assignee != ''){
                if($lead->assignee == $requestData['assignto']){
                    return redirect()->back()->with('error', 'Already Assigned to this user');
                }else{
                    $assignfrom = Admin::where('id',$lead->assignee)->first();
                    $assignto = Admin::where('id',$requestData['assignto'])->first();
                    
                    // Use Lead model method
                    $lead->assignToUser($requestData['assignto']);
                    return redirect()->back()->with('success', 'Lead transfer successfully');
                }
            }else{
                // Use Lead model method
                $saved = $lead->assignToUser($requestData['assignto']);
                if(!$saved)
                {
                    return redirect()->back()->with('error', 'Please try again');
                }else{
                    return redirect()->back()->with('success', 'Lead Assigned successfully');
                }
            }
        }else{
            return redirect()->back()->with('error', 'Not Found');
        }
    }

    /**
     * Get assignable users for leads
     */
    public function getAssignableUsers()
    {
        return Admin::select('id', 'first_name', 'last_name', 'email')
            ->where('type', '!=', 'lead')
            ->where('type', '!=', 'client')
            ->where('status', 1)
            ->get();
    }

    /**
     * Bulk assign leads to a user
     */
    public function bulkAssign(Request $request)
    {
        $requestData = $request->all();
        
        if(!isset($requestData['lead_ids']) || !isset($requestData['assign_to'])) {
            return redirect()->back()->with('error', 'Missing required data');
        }

        $leadIds = $requestData['lead_ids'];
        $assignTo = $requestData['assign_to'];
        $assignedCount = 0;

        foreach($leadIds as $leadId) {
            $lead = Lead::find($leadId);
            if($lead && $lead->user_id == Auth::user()->id) {
                $lead->assignToUser($assignTo);
                $assignedCount++;
            }
        }

        return redirect()->back()->with('success', "Successfully assigned {$assignedCount} leads");
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
