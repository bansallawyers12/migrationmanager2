<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\UserRole;
use App\Models\UserType;

use Auth;
use App\Services\ClientReferenceService;

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
		return view('AdminConsole.system.users.active',compact(['lists', 'totalData']));
	}

	public function create(Request $request)
	{
        //check authorization start
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if($check)
        {
            return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		$usertype 		= UserRole::all();
		return view('AdminConsole.system.users.create',compact(['usertype']));
	}

	public function store(Request $request)
	{
		//check authorization start
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if($check)
        {
            return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:admins',
                'password' => 'required|max:255|confirmed',
                'phone' => 'required',
                'role' => 'required',
                'office' => 'required',
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

			// Migration Agent Fields
			$obj->is_migration_agent = isset($requestData['is_migration_agent']) ? 1 : 0;
			
			if (isset($requestData['is_migration_agent'])) {
				$obj->marn_number = @$requestData['marn_number'];
				$obj->legal_practitioner_number = @$requestData['legal_practitioner_number'];
				$obj->exempt_person_reason = @$requestData['exempt_person_reason'];
				$obj->company_name = @$requestData['company_name'];
				$obj->business_address = @$requestData['business_address'];
				$obj->business_phone = @$requestData['business_phone'];
				$obj->business_mobile = @$requestData['business_mobile'];
				$obj->business_email = @$requestData['business_email'];
				$obj->business_fax = @$requestData['business_fax'];
				$obj->tax_number = @$requestData['tax_number'];
			}


            //Script start for generate client_id
            if( $requestData['role'] == 7 ) { //if user is of client type
                // Generate client_counter and client_id using centralized service
                // This prevents race conditions and duplicate references
                $referenceService = app(ClientReferenceService::class);
                $reference = $referenceService->generateClientReference($requestData['first_name']);
                
                $obj->client_counter = $reference['client_counter'];
                $obj->client_id = $reference['client_id'];
            }
            //Script end for generate client_id

			$saved				=	$obj->save();

            /*if($requestData['role'] == 7){ //role type = client(7)
		    	$objs				= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($requestData['first_name']).date('ym').$objs->id;
		    	$saveds				=	$objs->save();
			}*/

			if(!$saved) {
				return redirect()->back()->with('error', config('constants.server_error'));
			} else {
				return redirect()->route('adminconsole.system.users.active')->with('success', 'User added Successfully');
			}
		}
        return view('AdminConsole.system.users.create');
	}

	/**
	 * Show the form for editing the specified user.
	 */
	public function edit($id)
	{
		//check authorization start
        $check = $this->checkAuthorizationAction('user_management', 'edit', Auth::user()->role);
        if($check)
        {
            return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		$usertype = UserRole::all();
		
		if(isset($id) && !empty($id))
		{
			//$id = $this->decodeString($id);
			if(Admin::where('id', '=', $id)->exists())
			{
				$fetchedData = Admin::find($id);
				return view('AdminConsole.system.users.edit', compact(['fetchedData', 'usertype']));
			}
			else
			{
				return redirect()->route('adminconsole.system.users.index')->with('error', 'User Not Exist');
			}
		}
		else
		{
			return redirect()->route('adminconsole.system.users.index')->with('error', config('constants.unauthorized'));
		}
	}

	/**
	 * Update the specified user in storage.
	 */
	public function update(Request $request, $id)
	{
		//check authorization start
        $check = $this->checkAuthorizationAction('user_management', 'update', Auth::user()->role);
        if($check)
        {
            return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
        }
		//check authorization end
		
		$requestData = $request->all();

		$this->validate($request, [
			'first_name' => 'required|max:255',
			'last_name' => 'required|max:255',
			'phone' => 'required|max:255',
		]);

		$obj = Admin::find($id);
		if (!$obj) {
			return redirect()->route('adminconsole.system.users.index')->with('error', 'User Not Found');
		}

		$obj->first_name = @$requestData['first_name'];
		$obj->last_name = @$requestData['last_name'];
		$obj->email = @$requestData['email'];
		$obj->telephone = @$requestData['country_code'];
		$obj->position = @$requestData['position'];

		$obj->phone = @$requestData['phone'];
		$obj->role = @$requestData['role'];
		$obj->office_id = @$requestData['office'];
		$obj->telephone = @$requestData['country_code'];
		$obj->team = @$requestData['team'];

		if( isset($requestData['permission']) && $requestData['permission'] !="" ){
			$obj->permission = implode(",", $requestData['permission'] );
		}else{
			$obj->permission = "";
		}

		if(isset($requestData['show_dashboard_per'])){
			$obj->show_dashboard_per = 1;
		}else{
			 $obj->show_dashboard_per = 0;
		}

		// Migration Agent Fields
		$obj->is_migration_agent = isset($requestData['is_migration_agent']) ? 1 : 0;
		
		if (isset($requestData['is_migration_agent'])) {
			$obj->marn_number = @$requestData['marn_number'];
			$obj->legal_practitioner_number = @$requestData['legal_practitioner_number'];
			$obj->exempt_person_reason = @$requestData['exempt_person_reason'];
			$obj->company_name = @$requestData['company_name'];
			$obj->business_address = @$requestData['business_address'];
			$obj->business_phone = @$requestData['business_phone'];
			$obj->business_mobile = @$requestData['business_mobile'];
			$obj->business_email = @$requestData['business_email'];
			$obj->business_fax = @$requestData['business_fax'];
			$obj->tax_number = @$requestData['tax_number'];
		} else {
			// Clear agent fields if checkbox is unchecked
			$obj->marn_number = null;
			$obj->legal_practitioner_number = null;
			$obj->exempt_person_reason = null;
			$obj->business_address = null;
			$obj->business_phone = null;
			$obj->business_mobile = null;
			$obj->business_email = null;
			$obj->business_fax = null;
			$obj->tax_number = null;
		}

		if(!empty(@$requestData['password']))
		{
			$obj->password = Hash::make(@$requestData['password']);
		}

		$obj->phone = @$requestData['phone'];
		$saved = $obj->save();

		if(!$saved)
		{
			return redirect()->back()->with('error', config('constants.server_error'));
		}
		else
		{
			return redirect()->route('adminconsole.system.users.view', $id)->with('success', 'User Updated Successfully');
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
				return redirect()->back()->with('error', config('constants.server_error'));
			}

			else
			{
				return redirect()->route('adminconsole.system.users.view', $requestData['user_id'])->with('success', 'User Edited Successfully');
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
                return view('AdminConsole.system.users.view', compact(['fetchedData']));
            }
            else
            {
                return redirect()->route('adminconsole.system.users.active')->with('error', 'User Not Exist');
            }
        }
	}

	public function clientlist(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		$query 		= Admin::where('role', '=', '7');

		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('AdminConsole.system.users.clientlist',compact(['lists', 'totalData']));

		//return view('AdminConsole.system.users.index');
	}
	public function createclient(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		return view('AdminConsole.system.users.createclient');
	}

	public function storeclient(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
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
						$profile_img = $this->uploadFile($request->file('profile_img'), config('constants.profile_imgs'));
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
				return redirect()->back()->with('error', config('constants.server_error'));
			}
			else
			{
				return redirect()->route('adminconsole.system.users.clientlist')->with('success', 'Client Added Successfully');
			}
		}

		return view('AdminConsole.system.users.createclient');
	}

	/**
	 * Show the form for editing the specified client.
	 */
	public function editclient($id)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', 'editclient', Auth::user()->role);
			if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		$usertype = UserType::all();
		
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(Admin::where('id', '=', $id)->exists())
			{
				$fetchedData = Admin::find($id);
				return view('AdminConsole.system.users.editclient', compact(['fetchedData', 'usertype']));
			}
			else
			{
				return redirect()->route('adminconsole.system.users.clientlist')->with('error', 'Client Not Exist');
			}
		}
		else
		{
			return redirect()->route('adminconsole.system.users.clientlist')->with('error', config('constants.unauthorized'));
		}
	}

	/**
	 * Update the specified client in storage.
	 */
	public function updateclient(Request $request, $id)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('user_management', 'updateclient', Auth::user()->role);
			if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		
		$requestData = $request->all();

		$this->validate($request, [
			'company_name' => 'required|max:255',
			'first_name' => 'required|max:255',
			'last_name' => 'required|max:255',
			'company_website' => 'required|max:255',
			'email' => 'required|max:255|unique:admins,email,'.$id,
			'password' => 'required|max:255',
			'phone' => 'required|max:255'
		]);

		$obj = Admin::find($id);
		if (!$obj) {
			return redirect()->route('adminconsole.system.users.clientlist')->with('error', 'Client Not Found');
		}

		$obj->company_name = @$requestData['company_name'];
		$obj->first_name = @$requestData['first_name'];
		$obj->last_name = @$requestData['last_name'];
		$obj->company_website = @$requestData['company_website'];
		$obj->email = @$requestData['email'];

		if(!empty(@$requestData['password']))
		{
			$obj->password = Hash::make(@$requestData['password']);
		}
		
		$obj->phone = @$requestData['phone'];
		$obj->country = @$requestData['country'];
		$obj->city = @$requestData['city'];
		$obj->gst_no = @$requestData['gst_no'];
		$obj->role = 7;

		/* Profile Image Upload Function Start */
		if($request->hasfile('profile_img'))
		{
			/* Unlink File Function Start */
				if($requestData['profile_img'] != '')
					{
						$this->unlinkFile($requestData['old_profile_img'], config('constants.profile_imgs'));
					}
			/* Unlink File Function End */

			$profile_img = $this->uploadFile($request->file('profile_img'), config('constants.profile_imgs'));
		}
		else
		{
			$profile_img = @$requestData['old_profile_img'];
		}
		/* Profile Image Upload Function End */
		
		$obj->profile_img = @$profile_img;
		$saved = $obj->save();

		if(!$saved)
		{
			return redirect()->back()->with('error', config('constants.server_error'));
		}
		else
		{
			return redirect()->route('adminconsole.system.users.clientlist')->with('success', 'Client Updated Successfully');
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
		return view('AdminConsole.system.users.active',compact(['lists', 'totalData']));
	}

	public function inactive(Request $request)
	{
		$query 		= Admin::Where('role', '!=', '7')->Where('status', '=', 0)->with(['usertype']);
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('id','DESC')->paginate(config('constants.limit'));
		return view('AdminConsole.system.users.inactive',compact(['lists', 'totalData']));
	}

	public function invited(Request $request)
	{
		$query 		= Admin::Where('role', '!=', '7')->with(['usertype']);
		$totalData 	= $query->count();	//for all data
		$lists		= $query->orderby('id','DESC')->paginate(config('constants.limit'));
		return view('AdminConsole.system.users.invited',compact(['lists', 'totalData']));
	}
}


