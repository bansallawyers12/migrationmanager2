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
use Config;

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
            return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
        } */
		//check authorization end

		//$query 		= Workflow::where('id', '!=', '');
        $query 		= WorkflowStage::where('id', '!=', '');
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'asc'])->paginate(config('constants.limit')); //dd($lists);
        return view('AdminConsole\.features\.workflow.index',compact(['lists', 'totalData']));
    }

	public function create(Request $request)
	{
		//check authorization end
		return view('AdminConsole\.features\.workflow.create');
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
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/admin/workflow')->with('success', 'Workflow Stages Added Successfully');
			}
		}
    }

	public function edit(Request $request, $id = NULL)
	{
		//check authorization end
		if ($request->isMethod('post')) {
			$requestData = 	$request->all(); //dd($requestData);
			//$this->validate($request, ['name' => 'required|max:255']);
            $this->validate($request, [
                //'name' => 'required|max:255',
                'stage_name' => 'required|array', // Ensure it is an array
                'stage_name.*' => 'required|string|max:255', // Validate each item in the array
            ]);
			/*$obj			= 	Workflow::find(@$requestData['id']);
			$obj->name	=	@$requestData['name'];
			$saved		=	$obj->save();*/
			/*$stages = $requestData['stage_name'];
            $remove = WorkflowStage::where('id' , $requestData['id'])->delete();
			foreach($stages as $stage){
				$o = new WorkflowStage;
				$o->w_id = $requestData['id'];
				$o->name = $stage;
				$save	=	$o->save();
			}*/
            $saved = WorkflowStage::where('id', $requestData['id'])->update(['name' => $requestData['stage_name'][0]]);
			if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/admin/workflow')->with('success', 'Workflow Stages Edited Successfully');
			}
		}
		else
		{
			if(isset($id) && !empty($id)) {
				$id = $this->decodeString($id);
				if(WorkflowStage::where('id', '=', $id)->exists()) {
					$fetchedData = WorkflowStage::find($id); //dd($fetchedData);
					return view('AdminConsole\.features\.workflow.edit', compact(['fetchedData']));
				} else {
					return Redirect::to('/admin/workflow')->with('error', 'Workflow Stages Not Exist');
				}
			} else {
				return Redirect::to('/admin/workflow')->with('error', Config::get('constants.unauthorized'));
			}
		}
	}
}


