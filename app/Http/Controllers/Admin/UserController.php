<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\UserRole;
use App\Models\UserType;

use Auth;
use Config;

class UserController extends Controller
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
		$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 1)->with(['usertype']);
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('id','DESC')->paginate(config('constants.limit'));
		return view('Admin.users.active',compact(['lists', 'totalData']));
	}

	public function create(Request $request)
	{
        //check authorization start
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if($check)
        {
            return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		$usertype 		= UserRole::all();
		return view('Admin.users.create',compact(['usertype']));
	}

	public function store(Request $request)
	{
		//check authorization start
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if($check)
        {
            return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|max:255|unique:admins',
                'password' => 'required|max:255|confirmed',
                'phone' => 'required',
                'role' => 'required',
            ]);

            $obj				= 	new Admin;

			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->email		=	@$requestData['email'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->position		=	@$requestData['position'];
			$obj->password		=	Hash::make(@$requestData['password']);

			$obj->phone			=	@$requestData['phone'];
			$obj->role			=	@$requestData['role'];
			$obj->office_id		=	@$requestData['office'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->team		    =	@$requestData['team'];
			if(isset($requestData['show_dashboard_per'])){
			    $obj->show_dashboard_per		=	1;
			}else{
			     $obj->show_dashboard_per		=	0;
			}

            if(isset($requestData['permission']) && is_array($requestData['permission']) ){
                $obj->permission		=	implode(",",$requestData['permission']);
			}else{
			    $obj->permission		=	"";
			}


            //Script start for generate client_id
            if( $requestData['role'] == 7 ) { //if user is of client type
                if (strlen($requestData['first_name']) >= 4) {
                    $firstFourLetters = strtoupper(substr($requestData['first_name'], 0, 4));
                } else {
                    $firstFourLetters = strtoupper($requestData['first_name']);
                }
                $clientLatestArr = DB::table('admins')->select('id','client_counter')->where('role',7)->orderBy('id', 'desc')->first();
                //dd($clientLatestArr);
                if($clientLatestArr){
                    $client_latest_counter = $clientLatestArr->client_counter;
                } else {
                    $client_latest_counter = "00000";
                }
                $client_current_counter = $this->getNextCounter($client_latest_counter);

                $obj->client_counter = $client_current_counter;
                $obj->client_id = $firstFourLetters.date('y').$client_current_counter;
            }
            //Script end for generate client_id

			$saved				=	$obj->save();

            /*if($requestData['role'] == 7){ //role type = client(7)
		    	$objs				= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($requestData['first_name']).date('ym').$objs->id;
		    	$saveds				=	$objs->save();
			}*/

			if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/admin/users/active')->with('success', 'User added Successfully');
			}
		}
        return view('Admin.users.create');
	}

    public function getNextCounter($currentCounter) {
        // Convert current counter to an integer
        $counter = intval($currentCounter);

        // Increment the counter
        $counter++;

        // If the counter exceeds 99999, reset it to 1
        if ($counter > 99999) {
            $counter = 1;
        }

        // Format the counter as a 5-digit number with leading zeros
        return str_pad($counter, 5, '0', STR_PAD_LEFT);
    }

	public function edit(Request $request, $id = NULL)
	{
		//check authorization start
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if($check)
        {
            return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		$usertype 		= UserRole::all();
		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();

			$this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'phone' => 'required|max:255',
            ]);

			$obj				= 	Admin::find(@$requestData['id']);

			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->email		=	@$requestData['email'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->position		=	@$requestData['position'];

			$obj->phone			=	@$requestData['phone'];
			$obj->role			=	@$requestData['role'];
			$obj->office_id		=	@$requestData['office'];
			$obj->telephone		=	@$requestData['country_code'];
			$obj->team		    =	@$requestData['team'];

            if( isset($requestData['permission']) && $requestData['permission'] !="" ){
			    $obj->permission		=	implode(",", $requestData['permission'] );
			}else{
			    $obj->permission		=	"";
			}

			if(isset($requestData['show_dashboard_per'])){
			    $obj->show_dashboard_per		=	1;
			}else{
			     $obj->show_dashboard_per		=	0;
			}
			if(!empty(@$requestData['password']))
            {
                $obj->password				=	Hash::make(@$requestData['password']);
            }

			$obj->phone						=	@$requestData['phone'];
			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			else
			{
				return Redirect::to('/admin/users/view/'.@$requestData['id'])->with('success', 'User Edited Successfully');
			}
		}

		else
		{ //dd('elseee'.$id);
			if(isset($id) && !empty($id))
			{

				//$id = $this->decodeString($id);
				if(Admin::where('id', '=', $id)->exists())
				{
					$fetchedData = Admin::find($id); //dd($fetchedData);
					return view('Admin.users.edit', compact(['fetchedData', 'usertype']));
				}
				else
				{
					return Redirect::to('/admin/users')->with('error', 'User Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/users')->with('error', Config::get('constants.unauthorized'));
			}
		}

	}

	public function savezone(Request $request)
	{

		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();

			$obj							= 	Admin::find(@$requestData['user_id']);

			$obj->time_zone				=	@$requestData['timezone'];

			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			else
			{
				return Redirect::to('/admin/users/view/'.@$requestData['user_id'])->with('success', 'User Edited Successfully');
			}
		}


	}


	public function view(Request $request, $id)
	{
		if(isset($id) && !empty($id))
        {
            if(Admin::where('id', '=', $id)->exists())
            {
                $fetchedData = Admin::find($id);
                return view('Admin.users.view', compact(['fetchedData']));
            }
            else
            {
                return Redirect::to('/admin/users/active')->with('error', 'User Not Exist');
            }
        }
	}

	public function clientlist(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		$query 		= Admin::where('role', '=', '7');

		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('Admin.users.clientlist',compact(['lists', 'totalData']));

		//return view('Admin.users.index');
	}
	public function createclient(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		return view('Admin.users.createclient');
	}

	public function storeclient(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, [
										'company_name' => 'required|max:255',
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'company_website' => 'required|max:255',
										'email' => 'required|max:255|unique:admins',
										'password' => 'required|max:255',
										'phone' => 'required|max:255',
										'profile_img' => 'required|max:255'
									  ]);

			$requestData 		= 	$request->all();

			$obj				= 	new Admin;
			$obj->company_name	=	@$requestData['company_name'];
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->company_website		=	@$requestData['company_website'];
			$obj->email			=	@$requestData['email'];
			$obj->password	=	Hash::make(@$requestData['password']);
			$obj->phone	=	@$requestData['phone'];
			$obj->country	=	@$requestData['country'];
			$obj->city	=	@$requestData['city'];
			$obj->gst_no	=	@$requestData['gst_no'];
			$obj->verified	=	1;
			$obj->role	=	7;
			/* Profile Image Upload Function Start */
					if($request->hasfile('profile_img'))
					{
						$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
					}
					else
					{
						$profile_img = NULL;
					}
				/* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved				=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/users/clientlist')->with('success', 'Client Added Successfully');
			}
		}

		return view('Admin.users.createclient');
	}

	public function editclient(Request $request, $id = NULL)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		$usertype 		= UserType::all();
		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();

			$this->validate($request, [
										'company_name' => 'required|max:255',
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'company_website' => 'required|max:255',
										'email' => 'required|max:255|unique:admins,email,'.$requestData['id'],
										'password' => 'required|max:255',
										'phone' => 'required|max:255'
									  ]);

			$obj							= 	Admin::find(@$requestData['id']);

			$obj->company_name	=	@$requestData['company_name'];
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->company_website		=	@$requestData['company_website'];
			$obj->email			=	@$requestData['email'];
			$obj->password	=	Hash::make(@$requestData['password']);

			if(!empty(@$requestData['password']))
				{
					$obj->password				=	Hash::make(@$requestData['password']);
					//$objAdmin->decrypt_password		=	@$requestData['password'];
				}
			$obj->phone	=	@$requestData['phone'];
			$obj->country	=	@$requestData['country'];
			$obj->city	=	@$requestData['city'];
			$obj->gst_no	=	@$requestData['gst_no'];
			$obj->role	=	7;

			/* Profile Image Upload Function Start */
			if($request->hasfile('profile_img'))
			{
				/* Unlink File Function Start */
					if($requestData['profile_img'] != '')
						{
							$this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
						}
				/* Unlink File Function End */

				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}
		/* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			else
			{
				return Redirect::to('/admin/users/clientlist')->with('success', 'Client Edited Successfully');
			}
		}

		else
		{
			if(isset($id) && !empty($id))
			{

				$id = $this->decodeString($id);
				if(Admin::where('id', '=', $id)->exists())
				{
					$fetchedData = Admin::find($id);
					return view('Admin.users.editclient', compact(['fetchedData', 'usertype']));
				}
				else
				{
					return Redirect::to('/admin/users/clientlist')->with('error', 'Client Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/users/clientlist')->with('error', Config::get('constants.unauthorized'));
			}
		}

	}

	public function active(Request $request)
	{
        //dd($request->all());
        $req_data = $request->all();
        if( isset($req_data['search_by'])  && $req_data['search_by'] != ""){
            $search_by = $req_data['search_by'];
        } else {
            $search_by = "";
        }
        //dd($search_by);
        if($search_by) { //if search string is present
            $query 		= Admin::Where('role', '!=', '7')
            ->Where('status', '=', 1)
            ->where(function($q) use($search_by) {
                $q->where('first_name', 'LIKE', '%'.$search_by.'%')
                ->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
            })->with(['usertype']);

        } else {
            $query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 1)->with(['usertype']);
        }
		//$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 1)->with(['usertype']);
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('id','DESC')->paginate(config('constants.limit')); //dd($lists);
		return view('Admin.users.active',compact(['lists', 'totalData']));
	}

	public function inactive(Request $request)
	{
		$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 0)->with(['usertype']);
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('id','DESC')->paginate(config('constants.limit'));
		return view('Admin.users.inactive',compact(['lists', 'totalData']));
	}

	public function invited(Request $request)
	{
		$query 		= Admin::Where('role', '!=', '7')->with(['usertype']);
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('id','DESC')->paginate(config('constants.limit'));
		return view('Admin.users.invited',compact(['lists', 'totalData']));
	}
}
