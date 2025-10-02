<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\WebsiteSetting;
use App\Models\SeoPage;
use PDF;
use App\Models\InvoicePayment;
use App\Models\Setting;
use Auth;
use Config;
use App\Models\ActivitiesLog;
use App\Models\Note;
use App\Models\ClientMatter;
use Carbon\Carbon;
use App\Models\ClientVisaCountry;
use App\Services\EmailService;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->middleware('auth:admin');
        $this->emailService = $emailService;
    }
    // Dashboard functionality moved to DashboardController

    public function fetchnotification(Request $request){
        // $notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->orderby('created_at','DESC')->paginate(5);
         $notificalistscount = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count();
        /* $output = '';
	    foreach($notificalists as $listnoti){
	        $output .= '<a href="'.$listnoti->url.'?t='.$listnoti->id.'" class="dropdown-item dropdown-item-unread">
						<span class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</span>
						<span class="dropdown-item-desc">'.$listnoti->message.' <span class="time">'.date('d/m/Y h:i A',strtotime($listnoti->created_at)).'</span></span>
					</a>';
	    }*/

	    $data = array(
           //'notification' => $output,
           'unseen_notification'  => $notificalistscount
        );
        echo json_encode($data);
    }

    public function fetchmessages(Request $request){
        $notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('seen', 0)->first();
        if($notificalists){
            $obj = \App\Models\Notification::find($notificalists->id);
            $obj->seen = 1;
            $obj->save();
            return $notificalists->message;
        }else{
            return 0;
        }
    }

    // Moved to DashboardController

    // Moved to DashboardController

    // Dashboard notification methods moved to DashboardController

    /**
     * My Profile.
     *
     * @return \Illuminate\Http\Response
     */
	public function returnsetting(Request $request){
		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();
			$obj							= 	Admin::find(Auth::user()->id);
			if(@$requestData['is_business_gst'] == 'yes'){
			$obj->is_business_gst				=	@$requestData['is_business_gst'];
			$obj->gstin					=	@$requestData['gstin'];
			$obj->gst_date						=	@$requestData['gst_date'];
			}else{
				$obj->is_business_gst				=	@$requestData['is_business_gst'];
			$obj->gstin					=	'';
			$obj->gst_date						=	'';
			}
			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/settings/taxes/returnsetting')->with('success', 'Your Profile has been edited successfully.');
			}
		}else{
			//return view('Admin.my_profile', compact(['fetchedData', 'countries']));
			return view('Admin.settings.returnsetting');
		}
	}
	public function myProfile(Request $request)
	{
		/* Get all Select Data */
			$countries = array();
		/* Get all Select Data */

		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();

			$this->validate($request, [
										'first_name' => 'required',
										'last_name' => 'nullable',
										'country' => 'required',
										'phone' => 'required',
										'state' => 'required',
										'city' => 'required',
										'address' => 'required',
										'zip' => 'required'
									  ]);

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

			$obj							= 	Admin::find(Auth::user()->id);

			$obj->first_name				=	@$requestData['first_name'];
			$obj->last_name					=	@$requestData['last_name'];
			$obj->phone						=	@$requestData['phone'];
			$obj->country					=	@$requestData['country'];
			$obj->state						=	@$requestData['state'];
			$obj->city						=	@$requestData['city'];
			$obj->address					=	@$requestData['address'];
			$obj->zip						=	@$requestData['zip'];
			$obj->company_fax						=	@$requestData['company_fax'];
			$obj->company_name						=	@$requestData['company_name'];
			$obj->company_website						=	@$requestData['company_website'];
			$obj->profile_img				=	@$profile_img;

			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/my_profile')->with('success', 'Your Profile has been edited successfully.');
			}
		}
		else
		{
			$id = Auth::user()->id;
			$fetchedData = Admin::find($id);

			return view('Admin.my_profile', compact(['fetchedData', 'countries']));
		}
	}
	/**
     * Change password and Logout automatiaclly.
     *
     * @return \Illuminate\Http\Response
     */
	public function change_password(Request $request)
	{
		//check authorization start
			/* $check = $this->checkAuthorizationAction('Admin', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */
		//check authorization end

		if ($request->isMethod('post'))
		{
			$this->validate($request, [
										'old_password' => 'required|min:6',
										'password' => 'required|confirmed|min:6',
										'password_confirmation' => 'required|min:6'
									  ]);

			$requestData 	= 	$request->all();
			$admin_id = Auth::user()->id;

			$fetchedData = Admin::where('id', '=', $admin_id)->first();
			if(!empty($fetchedData))
				{
					if($admin_id == trim($requestData['admin_id']))
						{
							 if (!(Hash::check($request->get('old_password'), Auth::user()->password)))
								{
									return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
								}
							else
								{
									$admin = Admin::find($requestData['admin_id']);
									$admin->password = Hash::make($requestData['password']);
									if($admin->save())
										{
											Auth::guard('admin')->logout();
											$request->session()->flush();

											return redirect('/admin')->with('success', 'Your Password has been changed successfully.');
										}
									else
										{
											return redirect()->back()->with('error', Config::get('constants.server_error'));
										}
								}
						}
					else
						{
							return redirect()->back()->with('error', 'You can change the password only your account.');
						}
				}
			else
				{
					return redirect()->back()->with('error', 'User is not exist, so you can not change the password.');
				}
		}
		return view('Admin.change_password');
	}


	public function websiteSetting(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('Admin', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end

		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();

			$this->validate($request, [
										'phone' => 'required|max:20',
										'ofc_timing' => 'nullable|max:255',
										'email' => 'required|max:255'
									  ]);

			/* Logo Upload Function Start */
				if($request->hasfile('logo'))
				{
					/* Unlink File Function Start */
						if(@$requestData['logo'] != '')
							{
								$this->unlinkFile(@$requestData['old_logo'], Config::get('constants.logo'));
							}
					/* Unlink File Function End */

					$logo = $this->uploadFile($request->file('logo'), Config::get('constants.logo'));
				}
				else
				{
					$logo = @$requestData['old_logo'];
				}
			/* Logoe Upload Function End */

			if(!empty(@$requestData['id']))
			{
				$obj				= 	WebsiteSetting::find(@$requestData['id']);
			}
			else
			{
				$obj				= 	new WebsiteSetting;
			}
			$obj->phone				=	@$requestData['phone'];
			$obj->ofc_timing		=	@$requestData['ofc_timing'];
			$obj->email				=	@$requestData['email'];
			$obj->logo				=	@$logo;

			$saved							=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/website_setting')->with('success', 'Website Setting has been edited successfully.');
			}
		}
		else
		{
			$fetchedData = WebsiteSetting::where('id', '!=', '')->first();

			return view('Admin.website_setting', compact(['fetchedData']));
		}
	}

	public function editapi(Request $request)
	{
		//check authorization start
			$check = $this->checkAuthorizationAction('api_key', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}
		//check authorization end
		if ($request->isMethod('post'))
		{
			$obj	= 	Admin::find(Auth::user()->id);
			$obj->client_id	=	md5(Auth::user()->id.time());
			$saved				=	$obj->save();
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/api-key')->with('success', 'Api Key'.Config::get('constants.edited'));
			}
		}else{
			return view('Admin.apikey');
		}
	}

	public function updateAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);
			$requestData['current_status'] = trim($requestData['current_status']);
			$requestData['table'] = trim($requestData['table']);
			$requestData['col'] = trim($requestData['colname']);

			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['current_status']) && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{
							if($requestData['current_status'] == 0)
							{
								$updated_status = 1;
								$message = 'Record has been enabled successfully.';
							}
							else
							{
								$updated_status = 0;
								$message = 'Record has been disabled successfully.';
							}
							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update([$requestData['col'] => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		 die;

	}

	public function moveAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);
			$requestData['col'] = trim($requestData['col']);

				if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update([$requestData['col'] => 0]);
							if($response)
							{
								$status = 1;
								$message = 'Record successfully moved';
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}

		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function declinedAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);

			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 2;
								$message = 'Record has been disabled successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function approveAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);

			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 1;
								$message = 'Record has been approved successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error').'sss';
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function processAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);

			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 4;
								$message = 'Record has been processed successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $updated_status]);
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error').'sss';
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function archiveAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			$requestData['table'] = trim($requestData['table']);

			$astatus = '';
			$role = Auth::user()->role;
			if($role == 1 || $role == 7)
			{
				if(isset($requestData['id']) && !empty($requestData['id'])  && isset($requestData['table']) && !empty($requestData['table']))
				{
					$tableExist = Schema::hasTable(trim($requestData['table']));

					if($tableExist)
					{
						$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();

						if($recordExist)
						{

								$updated_status = 1;
								$message = 'Record has been archived successfully.';

							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['is_archive' => $updated_status]);
							$getarchive 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->first();
							if($getarchive->status == 0){
								$astatus = '<span title="draft" class="ui label uppercase">Draft</span><span> (Archived)</span>';
							}else if($getarchive->status == 1){
								$astatus = '<span title="draft" class="ui label uppercase yellow">Sent</span><span> (Archived)</span>';
							}else if($getarchive->status == 2){
								$astatus = '<span title="draft" class="ui label uppercase text-danger">Declined</span><span> (Archived)</span>';
							}
							if($response)
							{
								$status = 1;
							}
							else
							{
								$message = Config::get('constants.server_error');
							}
						}
						else
						{
							$message = 'ID does not exist, please check it once again.';
						}
					}
					else
					{
						$message = 'Table does not exist, please check it once again.';
					}
				}
				else
				{
					$message = 'Id OR Current Status OR Table does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'You are not authorized person to perform this action.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'astatus'=>$astatus));
		die;
	}

	public function deleteAction(Request $request)
	{
		$status 			= 	0;
		$method 			= 	$request->method();
		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all(); //dd($requestData);
            $requestData['id'] = trim($requestData['id']);
			$requestData['table'] = trim($requestData['table']);
            $role = Auth::user()->role;
            if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['table']) && !empty($requestData['table']))
			{
				$tableExist = Schema::hasTable(trim($requestData['table']));
                if($tableExist)
				{
					$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
                    if($recordExist)
					{
						if($requestData['table'] == 'admins'){
                            $o = \App\Models\Admin::where('id', $requestData['id'])->first();
							if($o->status == 1){
								$is_status = 0;
							}else{
								$is_status = 1;
							}
							$response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['status' => $is_status, 'updated_at' => date('Y-m-d H:i:s')]);
							if($response) {
								$status = 1;
                                if($is_status == 0 ) {
                                    $message = 'Record has been inactive successfully.';
                                } else {
                                    $message = 'Record has been active successfully.';
                                }
                            }
							else {
								$message = Config::get('constants.server_error');
							}
						}
                        else if($requestData['table'] == 'client_matters'){
                            $response = DB::table($requestData['table'])->where('id', $requestData['id'])->update(['matter_status' => 0]);
							if($response) {
								$status = 1;
								$message = 'Record has been enabled successfully.';
							} else {
								$message = Config::get('constants.server_error');
							}
						}
                        else if($requestData['table'] == 'quotations'){
                            $response 	= 	DB::table($requestData['table'])->where('id', $requestData['id'])->update(['is_archive' => 1]);
							if($response) {
								$status = 1;
								$message = 'Record has been enabled successfully.';
							} else {
								$message = Config::get('constants.server_error');
							}
						}
						else if($requestData['table'] == 'currencies'){
                            $isexist	=	$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
                            if($isexist){
                                $response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
                                if($response) {
                                    $status = 1;
                                    $message = 'Record has been deleted successfully.';
                                } else {
                                    $message = Config::get('constants.server_error');
                                }
                            }else{
                                $message = 'ID does not exist, please check it once again.';
                            }
						}
                        else if($requestData['table'] == 'templates'){
                            $isexist	=	$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
                            if($isexist){
                                $response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
                                DB::table('template_infos')->where('quotation_id', @$requestData['id'])->delete();
                                if($response) {
                                    $status = 1;
                                    $message = 'Record has been deleted successfully.';
                                } else {
                                    $message = Config::get('constants.server_error');
                                }
                            }else{
                                $message = 'ID does not exist, please check it once again.';
                            }
						}
                        else if($requestData['table'] == 'invoice_schedules'){
                            $response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
                            DB::table('schedule_items')->where('schedule_id', @$requestData['id'])->delete();

                            if($response)
                                {
                                    $status = 1;
                                    $message = 'Record has been deleted successfully.';
                                }
                                else
                                {
                                    $message = Config::get('constants.server_error');
                                }
                        }
                        else if($requestData['table'] == 'agents'){
                            $response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->update(['is_acrchived' => 1]);

                            if($response) {
                                $status = 1;
                                $message = 'Record has been Archived successfully.';
                            } else {
                                $message = Config::get('constants.server_error');
                            }
						}
                        else if($requestData['table'] == 'products'){
                            $applicationisexist	= DB::table('applications')->where('product_id', $requestData['id'])->exists();

                            if($applicationisexist){
                                $message = "Can't Delete its have relation with other records";
                            }else{
                                $isexist	=	$recordExist = DB::table($requestData['table'])->where('id', $requestData['id'])->exists();
                                if($isexist){
                                $response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
                                DB::table('template_infos')->where('quotation_id', @$requestData['id'])->delete();

                                if($response) {
                                    $status = 1;
                                    $message = 'Record has been deleted successfully.';
                                } else {
                                    $message = Config::get('constants.server_error');
                                }
                                }else{
                                    $message = 'ID does not exist, please check it once again.';
                                }
                            }
                        }
                        else{
                            $response	=	DB::table($requestData['table'])->where('id', @$requestData['id'])->delete();
                            if($response) {
                                $status = 1;
                                $message = 'Record has been deleted successfully.';
                            } else {
                                $message = Config::get('constants.server_error');
                            }
                        }
					}
                    else
                    {
                        $message = 'ID does not exist, please check it once again.';
                    }
                }
                else
                {
                    $message = 'Table does not exist, please check it once again.';
                }
            }
            else
            {
                $message = 'Id OR Table does not exist, please check it once again.';
            }
        }
		else {
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}

	public function getStates(Request $request)
	{
		$status 			= 	0;
		$data				=	array();
		$method 			= 	$request->method();

		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			if(isset($requestData['id']) && !empty($requestData['id']))
			{
				$recordExist = Country::where('id', $requestData['id'])->exists();

				if($recordExist)
				{
					$data 	= 	State::where('country_id', '=', $requestData['id'])->get();

					if($data)
					{
						$status = 1;
						$message = 'Record has been fetched successfully.';
					}
					else
					{
						$message = Config::get('constants.server_error');
					}
				}
				else
				{
					$message = 'ID does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'ID does not exist, please check it once again.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'data'=>$data));
		die;
	}

	public function getChapters(Request $request)
	{
		$status 			= 	0;
		$data				=	array();
		$method 			= 	$request->method();

		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim($requestData['id']);

			if(isset($requestData['id']) && !empty($requestData['id']))
			{
				$recordExist = McqSubject::where('id', $requestData['id'])->exists();

				if($recordExist)
				{
					$data 	= 	McqChapter::where('subject_id', '=', $requestData['id'])->get();

					if($data)
					{
						$status = 1;
						$message = 'Record has been fetched successfully.';
					}
					else
					{
						$message = Config::get('constants.server_error');
					}
				}
				else
				{
					$message = 'ID does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'ID does not exist, please check it once again.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'data'=>$data));
		die;
	}

	public function addCkeditiorImage(Request $request)
	{
		echo "<pre>";
		print_r($_FILES);die;

		$status 			= 	0;
		$method 			= 	$request->method();

		if ($request->isMethod('post'))
		{
			$requestData 	= 	$request->all();

			echo "<pre>";
			print_r($requestData);die;

			if(isset($requestData['id']) && !empty($requestData['id']))
			{
				$recordExist = Country::where('id', $requestData['id'])->exists();

				if($recordExist)
				{
					$data 	= 	State::where('country_id', '=', $requestData['id'])->get();

					if($data)
					{
						$status = 1;
						$message = 'Record has been fetched successfully.';
					}
					else
					{
						$message = Config::get('constants.server_error');
					}
				}
				else
				{
					$message = 'ID does not exist, please check it once again.';
				}
			}
			else
			{
				$message = 'ID does not exist, please check it once again.';
			}
		}
		else
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message, 'data'=>$data));
		die;
	}

	public function sessions(Request $request)
	{
		return view('Admin.sessions');
	}


	public function getpartnerbranch(Request $request){
		$catid = $request->cat_id;
		// Partner functionality removed - no partners available
		ob_start();
		?>
		<option value="">No partners available</option>
		<?php
		// Partner functionality removed
		// foreach($listsbranchs as $listsbranch){
		// ?>
		// <option value="<?php echo $listsbranch->id; ?>_<?php echo $list->id; ?>"><?php echo $list->partner_name.' ('.$listsbranch->name.')'; ?></option>
		// <?php
		// }
		echo ob_get_clean();
	}

	public function gettemplates(Request $request){
		$id = $request->id;
		$CrmEmailTemplate = \App\Models\CrmEmailTemplate::where('id',$id)->first();
		if($CrmEmailTemplate){
			echo json_encode(array('subject'=>$CrmEmailTemplate->subject, 'description'=>$CrmEmailTemplate->description));
		}else{
			echo json_encode(array('subject'=>'','description'=>''));
		}
	}

    public function sendmail(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$reciept_id = '';
		$array = array();

        if(isset($requestData['receipt'])){
            $fetchedData = InvoicePayment::where('id', '=', $requestData['receipt'])->first();
            $reciept_id = $fetchedData->id;
            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                'logOutputFile' => storage_path('logs/log.htm'),
                'tempDir' => storage_path('logs/')
            ])->loadView('emails.reciept', compact('fetchedData'));
            $output = $pdf->output();
            $invoicefilename = 'receipt_'.$reciept_id.'.pdf';
            file_put_contents('/home/bansaledu/public/invoices/'.$invoicefilename, $output);
            $array['file'] = '/home/bansaledu/public/invoices/'.$invoicefilename;
            $array['file_name'] = $invoicefilename;
        }

        if(isset($requestData['invreceipt'])){
            $invoicedetail = \App\Models\Invoice::where('id', '=', $requestData['invreceipt'])->first();
            if($invoicedetail->type == 3){
                $workflowdaa = \App\Models\Workflow::where('id', $invoicedetail->application_id)->first();
                $applicationdata = array();
                $partnerdata = array();
                $productdata = array();
                $branchdata = array();
            }else{
                $applicationdata = \App\Models\Application::where('id', $invoicedetail->application_id)->first();
                
                $workflowdaa = \App\Models\Workflow::where('id', @$applicationdata->workflow)->first();
            }

			$clientdata = \App\Models\Admin::where('role', 7)->where('id', $invoicedetail->client_id)->first();
			$admindata = \App\Models\Admin::where('role', 1)->where('id', $invoicedetail->user_id)->first();

            $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
            ])->loadView('emails.invoice',compact(['applicationdata','workflowdaa','clientdata','productdata','invoicedetail','admindata']));
            $reciept_id = $invoicedetail->id;

            $output = $pdf->output();
            $invoicefilename = 'invoice_'.$reciept_id.'.pdf';
            file_put_contents(public_path('invoices/'.$invoicefilename), $output);

            $array['file'] = public_path() . '/' .'invoices/'.$invoicefilename;
            $array['file_name'] = $invoicefilename;
        }

		$obj = new \App\Models\MailReport;
		$obj->user_id 		=  $user_id;
		$obj->from_mail 	=  $requestData['email_from'];
		$obj->to_mail 		=  implode(',',$requestData['email_to']);
		if(isset($requestData['email_cc'])){
		    $obj->cc 			=  implode(',',@$requestData['email_cc']);
		}
		$obj->template_id 	=  $requestData['template'];
		$obj->reciept_id 	=  $reciept_id;
		$obj->subject		=  $requestData['subject'];
		if(isset($requestData['type'])){
		    $obj->type 			=  @$requestData['type'];
		}
		$obj->message		 =  $requestData['message'];
        $obj->mail_type		 =  $requestData['mail_type'];
        $obj->client_id		 =  $requestData['client_id'];
        $obj->client_matter_id	=  $requestData['compose_client_matter_id'];

		$attachments = array();
		if(isset($requestData['checklistfile'])){
            if(!empty($requestData['checklistfile'])){
                $checklistfiles = $requestData['checklistfile'];
                $attachments = array();
                foreach($checklistfiles as $checklistfile){
                    $filechecklist =  \App\Models\UploadChecklist::where('id', $checklistfile)->first();
                    if($filechecklist){
                        $attachments[] = array('file_name' => $filechecklist->name,'file_url' => $filechecklist->file);
                    }
                }
                //$obj->attachments = json_encode($attachments);
            }
        }

        $attachments2 = array();
        if(isset($requestData['checklistfile_document'])){
            if(!empty($requestData['checklistfile_document'])){
                $checklistfiles_documents = $requestData['checklistfile_document'];
                $attachments2 = array();
                foreach($checklistfiles_documents as $checklistfile1){
                    $filechecklist_doc =  \App\Models\Document::where('id', $checklistfile1)->first();
                    if($filechecklist_doc){
                        if( $filechecklist_doc->doc_type == "education" || $filechecklist_doc->doc_type == "migration" ){
                            $attachments2[] = array('file_name' => $filechecklist_doc->name,'file_url' => $filechecklist_doc->file);
                        }
                        else if( $filechecklist_doc->doc_type == "documents")  {
                            $attachments2[] = array('file_name' => $filechecklist_doc->file_name,'file_url' => $filechecklist_doc->myfile);
                        }
                    }
                }
                //$obj->attachments = json_encode($attachments);
            }
        }

        $attachments = array_merge($attachments, $attachments2);
        if(!empty($attachments) && count($attachments) >0){
            $obj->attachments = json_encode($attachments);
        }

        $saved	=	$obj->save();
        if(isset($requestData['checklistfile'])){
            if(!empty($requestData['checklistfile'])){
                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $obj->to_mail;
                $objs->created_by = Auth::user()->id;
                $objs->subject = "Checklist sent to client";
                $objs->save();
            }
        }

        if(isset($requestData['checklistfile_document'])){
            if(!empty($requestData['checklistfile_document'])){
                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $obj->to_mail;
                $objs->created_by = Auth::user()->id;
                $objs->subject = "Document Checklist sent to client";
                $objs->save();
            }
        }

		$subject = $requestData['subject'];
		$message = $requestData['message'];
		foreach($requestData['email_to'] as $l){
			if(@$requestData['type'] == 'agent'){
				$client = \App\Models\AgentDetails::Where('id', $l)->first();
			    $subject = str_replace('{Client First Name}',$client->full_name, $subject);
			    $message = str_replace('{Client First Name}',$client->full_name, $message);
			}else{
				$client = \App\Models\Admin::Where('id', $l)->first();
			    $subject = str_replace('{Client First Name}',$client->first_name, $subject);
			    $message = str_replace('{Client First Name}',$client->first_name, $message);
			}

			$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
			$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
			$ccarray = array();
			if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
				foreach($requestData['email_cc'] as $cc){
					$clientcc = \App\Models\Admin::Where('id', $cc)->first();
					$ccarray[] = $clientcc;
				}
			}

			if(isset($requestData['checklistfile'])){
    		    if(!empty($requestData['checklistfile'])){
    		       $checklistfiles = $requestData['checklistfile'];
    		        foreach($checklistfiles as $checklistfile){
    		           $filechecklist =  \App\Models\UploadChecklist::where('id', $checklistfile)->first();
    		           if($filechecklist){
    		            $array['files'][] =  public_path() . '/' .'checklists/'.$filechecklist->file;
    		           }
    		        }
    		    }
		    }

            if(isset($requestData['checklistfile_document'])){
                if(!empty($requestData['checklistfile_document'])){
                    $checklistfiles_documents = $requestData['checklistfile_document'];
                    foreach($checklistfiles_documents as $checklistfile1){
                        $filechecklist_doc =  \App\Models\Document::where('id', $checklistfile1)->first();
                        if($filechecklist_doc){
                            if( $filechecklist_doc->doc_type == "education" || $filechecklist_doc->doc_type == "migration" ){
                                $array['files'][] =  public_path() . '/' .'img/documents/'.$filechecklist_doc->myfile;
                            }
                            else if( $filechecklist_doc->doc_type == "documents") {
                                $fileUrl = $filechecklist_doc->myfile; // AWS S3 link

                                // Check if it's a URL
                                if(filter_var($fileUrl, FILTER_VALIDATE_URL)){
                                    // Download and save to a temporary location
                                    $tempPath = sys_get_temp_dir() . '/' . basename($fileUrl);
                                    file_put_contents($tempPath, file_get_contents($fileUrl));
                                    $array['files'][] = $tempPath; // Attach the temp file
                                } else {
                                    $array['files'][] = $fileUrl; // Local file
                                }
                            }
                        }
                    }
                }
            }
            //echo "<pre>array=";print_r($array);die;

		    /*if($request->hasfile('attach'))
            {
                 $array['filesatta'][] =  $request->attach;
            }*/

            // Process Uploaded Files
            if ($request->hasFile('attach')) {
                foreach ($request->file('attach') as $file1) {
                    $array['filesatta'][] =  $file1;
                }
            }

            //dd($client->email,  $requestData['email_from']);
            //$this->send_compose_template($client->email, $subject, $requestData['email_from'], $message, '', $array,@$ccarray);

            try {
                $attachments = [];
                //dd($array['filesatta']);
                if(isset($array['files'])){
                    $attachments = array_merge($attachments, $array['files']);
                }

                if(isset($array['filesatta'])){
                    foreach($array['filesatta'] as $file) {
                        $filename = time().'_'.$file->getClientOriginalName(); // Unique filename
                        $filePath = storage_path('app/uploads/'.$filename); // Save in storage/uploads folder

                        // Move the file to storage folder
                        $file->move(storage_path('app/uploads'), $filename);

                        // Add saved file path to attachments
                        $attachments[] = $filePath;
                    }
                }

                $ccarray = [];
                if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
                    foreach($requestData['email_cc'] as $cc){
                        $clientcc = \App\Models\Admin::Where('id', $cc)->first();
                        if($clientcc) {
                            $ccarray[] = $clientcc->email;
                        }
                    }
                }
                //dd($attachments);
                $this->emailService->sendEmail(
                    'emails.template',
                    ['content' => $message],
                    $client->email,
                    $subject,
                    $requestData['email_from'],
                    $attachments,
                    $ccarray
                );

                return redirect()->back()->with('success', 'Email sent successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage())->withInput();
            }
        }
        if(!empty($array['file'])){
            unset($array['file']);
        }
        if(!$saved) {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
        } else {
            return redirect()->back()->with('success', 'Email Sent Successfully');
        }
	}


	public function getsubcategories(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\SubCategory::where('cat_id', $catid)->get();
		ob_start();
		?>

		<?php
		foreach($lists as $list){

			?>
			<option value="<?php echo $list->sub_id; ?>"><?php echo $list->name; ?></option>
			<?php

		}
		echo ob_get_clean();
	}

		public function getpartnerajax(Request $request){
	    // Partner functionality removed - no partners available
	    $agents = array();
		echo json_encode($agents);
	}

		public function getassigneeajax(Request $request){
		    $squery = $request->likevalue;
		     $fetchedData = \App\Models\Admin::where('role', '!=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");

            })
            ->get();

		$agents = array();
		foreach($fetchedData as $list){
			$agents[] = array(
				'id' => $list->id,
				'agent_id' => $list->first_name.' '.$list->last_name,
				'assignee' => $list->first_name.' '.$list->last_name,
			);
		}

		echo json_encode($agents);
	}

	public function gensettings(Request $request){
	   $setting = Setting::where('office_id', Auth::user()->office_id)->first();
		return view('Admin.gensettings.index', compact('setting'));
	}

    public function gensettingsupdate(Request $request){
        if(Setting::where('office_id', Auth::user()->office_id)->exists()){
           $setting = Setting::where('office_id', Auth::user()->office_id)->first();
            $objs = Setting::find($setting->id);
            $objs->date_format = $request->date_format;
             $objs->time_format = $request->time_format;
            $objs->save();
        }else{
             $objs = new Setting;
            $objs->date_format = $request->date_format;
            $objs->office_id = Auth::user()->office_id;
             $objs->time_format = $request->time_format;
            $objs->save();
        }

        	return Redirect::to('/admin/gen-settings')->with('success', 'Record updated successfully');
    }

    public function checkclientexist(Request $request){
        if($request->type == 'email'){
         $clientexists = \App\Models\Admin::where('email', $request->vl)->where('role',7)->exists();
            if($clientexists){
                echo 1;
            }else{
                echo 0;
            }
        }else if($request->type == 'clientid'){
         $clientexists = \App\Models\Admin::where('client_id', $request->vl)->where('role',7)->exists();
            if($clientexists){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            $clientexists = \App\Models\Admin::where('phone', $request->vl)->where('role',7)->exists();
            if($clientexists){
                echo 1;
            }else{
                echo 0;
            }
        }
    }

	public function allnotification(Request $request){
		$lists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->orderby('created_at','DESC')->paginate(20);
		return view('Admin.notifications', compact(['lists']));
	}

    // Dashboard methods moved to DashboardController

	//Get matter templates
	public function getmattertemplates(Request $request){
		$id = $request->id;
		$CrmEmailTemplate = \App\Models\MatterEmailTemplate::where('id',$id)->first();
		if($CrmEmailTemplate){
			echo json_encode(array('subject'=>$CrmEmailTemplate->subject, 'description'=>$CrmEmailTemplate->description));
		}else{
			echo json_encode(array('subject'=>'','description'=>''));
		}
	}

    // Column preferences method moved to DashboardController

    /**
     * Get office visit notifications
     */
    public function fetchOfficeVisitNotifications(Request $request)
    {
        $notifications = \App\Models\Notification::with(['sender:id,first_name,last_name'])
            ->where('receiver_id', Auth::id())
            ->where('notification_type', 'officevisit')
            ->where('receiver_status', 0)
            ->orderBy('created_at', 'DESC')
            ->get();

        $data = [];
        foreach ($notifications as $notification) {
            $checkinLog = \App\Models\CheckinLog::find($notification->module_id);
            
            if (!$checkinLog || $checkinLog->status != 0) {
                continue;
            }

            // Get client information
            $client = $checkinLog->contact_type == 'Lead' 
                ? \App\Models\Lead::find($checkinLog->client_id)
                : Admin::where('role', '7')->find($checkinLog->client_id);

            $data[] = [
                'id' => $notification->id,
                'checkin_id' => $checkinLog->id,
                'message' => $notification->message,
                'sender_name' => $notification->sender 
                    ? $notification->sender->first_name . ' ' . $notification->sender->last_name 
                    : 'System',
                'client_name' => $client 
                    ? $client->first_name . ' ' . $client->last_name 
                    : 'Unknown Client',
                'visit_purpose' => $checkinLog->visit_purpose,
                'created_at' => $notification->created_at->format('d/m/Y h:i A'),
                'url' => $notification->url
            ];
        }

        return response()->json($data);
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
        if (Auth::user()->role == 1) {
            $assigneesCount = \App\Models\Note::where('type', 'client')
                ->whereNotNull('client_id')
                ->where('folloup', 1)
                ->where('status', 0)
                ->count();
        } else {
            $assigneesCount = \App\Models\Note::where('assigned_to', Auth::user()->id)
                ->where('type', 'client')
                ->where('folloup', 1)
                ->where('status', 0)
                ->count();
        }
        
        return response()->json(['assigneesCount' => $assigneesCount]);
    }

    /**
     * Get visa expiry messages
     */
    public function fetchvisaexpirymessages(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|integer'
        ]);

        $visaInfo = \App\Models\ClientVisaCountry::where('client_id', $request->client_id)
            ->latest('id')
            ->first();

        if (!$visaInfo || !$visaInfo->visa_expiry_date) {
            return '';
        }

        $visaExpiredAt = \Carbon\Carbon::parse($visaInfo->visa_expiry_date);
        $today = \Carbon\Carbon::now();
        $sevenDaysFromNow = \Carbon\Carbon::now()->addDays(7);

        if ($visaExpiredAt->lt($today)) {
            return "Your visa has expired. Please contact us immediately.";
        } elseif ($visaExpiredAt->gte($today) && $visaExpiredAt->lte($sevenDaysFromNow)) {
            $daysRemaining = $visaExpiredAt->diffInDays($today);
            return "Your visa is expiring in next $daysRemaining day" . ($daysRemaining == 1 ? '' : 's');
        }

        return '';
    }

    /**
     * Mark notification as seen
     */
    public function markNotificationSeen(Request $request)
    {
        $notification = \App\Models\Notification::find($request->notification_id);
        
        if (!$notification || $notification->receiver_id != Auth::id()) {
            return response()->json(['status' => 'error']);
        }

        $notification->receiver_status = 1;
        $notification->save();

        return response()->json(['status' => 'success']);
    }
}