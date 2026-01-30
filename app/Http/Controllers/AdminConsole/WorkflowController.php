<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Workflow;
use App\Models\WorkflowStage;

use Auth;

class WorkflowController extends Controller
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
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request)
	{
		//check authorization start
        /* if($check)
        {
            return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
        } */
		//check authorization end

		//$query 		= Workflow::where('id', '!=', '');
        $query 		= WorkflowStage::query();
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'asc'])->paginate(config('constants.limit')); //dd($lists);
        return view('AdminConsole.features.workflow.index',compact(['lists', 'totalData']));
    }

	public function create(Request $request)
	{
		//check authorization end
		return view('AdminConsole.features.workflow.create');
	}

	public function store(Request $request)
	{
		//check authorization end
		if ($request->isMethod('post')) {
			//$this->validate($request, ['name' => 'required|max:255']);
            $this->validate($request, [
                //'name' => 'required|max:255',
                'stage_name' => 'required|array', // Ensure it is an array
                'stage_name.*' => 'required|string|max:255', // Validate each item in the array
            ]);
            $requestData = 	$request->all(); //dd($requestData);
            /*$obj		 = 	new Workflow;
			$obj->name	 =	@$requestData['name'];
            $saved		 =	$obj->save();*/
			$stages = $requestData['stage_name'];
            foreach($stages as $stage){
				$o = new WorkflowStage;
				//$o->w_id = $obj->id;
				$o->name = $stage;
				$save	 =	$o->save();
			}
            if(!$save) {
				return redirect()->back()->with('error', config('constants.server_error'));
			} else {
				return redirect()->route('adminconsole.features.workflow.index')->with('success', 'Workflow Stages Added Successfully');
			}
		}
    }

	/**
	 * Show the form for editing the specified workflow stage.
	 */
	public function edit($id)
	{
		//check authorization end
		
		if(isset($id) && !empty($id)) {
			$id = $this->decodeString($id);
			if(WorkflowStage::where('id', '=', $id)->exists()) {
				$fetchedData = WorkflowStage::find($id);
				return view('AdminConsole.features.workflow.edit', compact(['fetchedData']));
			} else {
				return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow Stages Not Exist');
			}
		} else {
			return redirect()->route('adminconsole.features.workflow.index')->with('error', config('constants.unauthorized'));
		}
	}

	/**
	 * Update the specified workflow stage in storage.
	 */
	public function update(Request $request, $id)
	{
		//check authorization end
		
		$requestData = $request->all();
		$this->validate($request, [
			'stage_name' => 'required|array', // Ensure it is an array
			'stage_name.*' => 'required|string|max:255', // Validate each item in the array
		]);
		
		$saved = WorkflowStage::where('id', $id)->update(['name' => $requestData['stage_name'][0]]);
		if(!$saved) {
			return redirect()->back()->with('error', config('constants.server_error'));
		} else {
			return redirect()->route('adminconsole.features.workflow.index')->with('success', 'Workflow Stages Updated Successfully');
		}
	}
}


