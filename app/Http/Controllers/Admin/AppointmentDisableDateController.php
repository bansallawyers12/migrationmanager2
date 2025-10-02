<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\BookService;

use Auth;
use Config;

class AppointmentDisableDateController extends Controller
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
        $query 		= BookService::where('status', 1);
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'asc'])->paginate(config('constants.limit'));
        return view('Admin.feature.appointmentdisabledate.index',compact(['lists', 'totalData']));
    }

	public function create(Request $request)
	{
		return view('Admin.feature.appointmentdisabledate.create');
	}

	public function store(Request $request)
	{
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, ['name' => 'required|max:255']);

			$requestData 		= 	$request->all();

			$obj				= 	new PartnerType;
			$obj->name	=	@$requestData['name'];
			$obj->category_id	=	@$requestData['category'];

			$saved				=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/partner-type')->with('success', 'Partner Type Added Successfully');
			}
		}

		return view('Admin.feature.appointmentdisabledate.create');
	}

	public function edit(Request $request, $id = NULL)
	{
        //check authorization end
        if ($request->isMethod('post')) {
			$requestData 		= 	$request->all(); //dd($requestData);
            $obj			= 	BookService::find(@$requestData['id']);
            $obj->disabledates	=	@$requestData['disabledates'];
			$saved				=	$obj->save();
            if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/admin/appointment-dates-disable')->with('success', 'Appointment Edited Successfully');
			}
		} else {
			if(isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
				if(BookService::where('id', '=', $id)->exists()) {
					$fetchedData = BookService::find($id);

                    $weekendd  =array();
                    if($fetchedData->weekend != ''){
                        $weekend = explode(',',$fetchedData->weekend);
                        foreach($weekend as $e){
                            if($e == 'Sun'){
                                $weekendd[] = 0;
                            }else if($e == 'Mon'){
                                $weekendd[] = 1;
                            }else if($e == 'Tue'){
                                $weekendd[] = 2;
                            }else if($e == 'Wed'){
                                $weekendd[] = 3;
                            }else if($e == 'Thu'){
                                $weekendd[] = 4;
                            }else if($e == 'Fri'){
                                $weekendd[] = 5;
                            }else if($e == 'Sat'){
                                $weekendd[] = 6;
                            }
                        }
                    }


                    if($fetchedData->disabledates != ''){
                        $disabledatesF =  array();
                        if( strpos($fetchedData->disabledates, ',') !== false ) {
                            $disabledatesArr = explode(',',$fetchedData->disabledates);
                            $disabledatesF = $disabledatesArr;
                        } else {
                            $disabledatesF = array($fetchedData->disabledates);
                        }
                    } else {
                        $disabledatesF =  array();
                    }

                    return view('Admin.feature.appointmentdisabledate.edit', compact(['fetchedData','weekendd','disabledatesF']));
				} else {
					return Redirect::to('/admin/appointment-dates-disable')->with('error', 'Appointment Not Exist');
				}
			} else {
				return Redirect::to('/admin/appointment-dates-disable')->with('error', Config::get('constants.unauthorized'));
			}
		}
    }
}
