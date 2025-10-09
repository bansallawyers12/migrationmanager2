<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\PersonalDocumentType;

use Auth;
use Config;

class PersonalDocumentTypeController extends Controller
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
		//check authorization end
        $query 		= PersonalDocumentType::where('status', 1);
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
        return view('AdminConsole\.features\.personaldocumenttype.index',compact(['lists', 'totalData']));
    }

	public function create(Request $request)
	{
		return view('AdminConsole\.features\.personaldocumenttype.create');
	}

	public function store(Request $request)
	{
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request,
                [ 'title' => 'required|unique:personal_document_types,title']
            );

            $requestData  = 	$request->all(); //dd($requestData);
            $obj		 = 	new PersonalDocumentType;
			$obj->title	=	@$requestData['title'];
			$obj->status	= 1;
			$saved	=	$obj->save();
            if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/personal-document-type')->with('success', 'Personal Document Type Created Successfully');
			}
		}
        return view('AdminConsole\.features\.personaldocumenttype.create');
	}

	public function edit(Request $request, $id = NULL)
	{
        //check authorization end
        if ($request->isMethod('post')) {
            $requestData 		= 	$request->all(); //dd($requestData);
            $this->validate($request,
                ['title' => 'required|unique:personal_document_types,title,'.$requestData['id']]
            );

            $obj		= 	PersonalDocumentType::find(@$requestData['id']);
            $obj->title	=	@$requestData['title'];
            $saved	=	$obj->save();
            if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/admin/personal-document-type')->with('success', 'Personal Document Type Updated Successfully');
			}
		} else {
			if(isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
				if(PersonalDocumentType::where('id', '=', $id)->exists()) {
					$fetchedData = PersonalDocumentType::find($id);
                    return view('AdminConsole\.features\.personaldocumenttype.edit', compact(['fetchedData']));
				} else {
					return Redirect::to('/admin/personal-document-type')->with('error', 'Personal Document Type Not Exist');
				}
			} else {
				return Redirect::to('/admin/personal-document-type')->with('error', Config::get('constants.unauthorized'));
			}
		}
    }



}


