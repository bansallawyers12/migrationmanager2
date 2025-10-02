<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\BaseController as BaseController;

use Config;
use App\Models\Admin;
use App\Models\PasswordResetLink;
use App\Models\User;
use Validator;
use DB;

class UserController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function ResetPassword(Request $request){
		$client_id = $request->client_id;
		
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
		$requestData 		= 	$request->all();
			
			$userData = User::select('id')->where('email', '=', trim(@$requestData['email']))->first();
			
			$obj				= 	User::find(@$userData->id);
			$obj->password		=	Hash::make(trim($requestData['fpassword']));

			$saved				=	$obj->save();
			if(!$saved)
			{
				return $this->sendError('Error', array('client_id'=>array('Please try again'))); 
			}
			else
			{
				$objReset				= 	PasswordResetLink::find(@$requestData['id']);
				$objReset->expire		=	1; //expired
				
				$savedReset				=	$objReset->save();
				
				
				$success['msg'] = "Your Password has been changed successfully.";
				return $this->sendResponse($success, '');
			}
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function PasswordReset(Request $request){
		 $client_id = $request->client_id;
		
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$data = PasswordResetLink::where('token', '=', trim(@$request->token))->where('expire', '=', 0)->where('client_id', '=', $users->id)->first();
		if(empty($data)){
			return $this->sendError('Error', array('client_id'=>array('Reset Link has been expired, so you can not proceed further.'))); 
		}else 
			{
				if($data->count() == 0)
				{
					return $this->sendError('Error', array('client_id'=>array('Reset Link has been expired, so you can not proceed further.'))); 
				}
			}
			$success['users'] =  @$data;
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function user(Request $request){
		$client_id = $request->client_id;
		
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			DB::enableQueryLog(); 
			$userss = User::where('client_id',$users->id)->where('id',$request->user_id)->first();
			
			$success['users'] =  @$userss;
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	
	public function countusers(Request $request){
		$client_id = $request->client_id;
		
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			//DB::enableQueryLog(); 
			$userss = User::where('client_id',$users->id)->count();
			
			$success['users'] =  str_pad(@$userss, 5, "20000", STR_PAD_LEFT);
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	
	public function editProfile(Request $request){
		$requestData 		= 	$request->all();
		$client_id = @$requestData['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$validator = Validator::make($requestData, [
				'name' => 'required|max:255',
				'email' => 'required',

			  ],[
				'name.required' => 'Name is required',
			  ]);
			  if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		$isuser = User::where('client_id',$users->id)->where('id',$requestData['user_id'])->first();
		if($request->email != $isuser->email){
			
			$isexist = User::where('client_id',$users->id)->where('email',$request->email)->exists();
			if($isexist){
				return $this->sendError('Validation Error.', array('email'=>array('The email has already been taken.'))); 
			}
		} 
		
			  $obj					= 	User::find(@$requestData['user_id']);
			$obj->name		=	@$requestData['name'];
			$obj->email			=	@$requestData['email'];
			if(!empty(@$requestData['password']))
				{		
					$obj->password				=	Hash::make(@$requestData['password']);
				}
			$obj->gender				=	@$requestData['gender'];
			$obj->marital_status		=	@$requestData['marital_staus'];
			$obj->phone				=	@$requestData['phone'];
			$obj->state				=	@$requestData['state'];
			$obj->city				=	@$requestData['city'];
			$obj->address			=	@$requestData['address'];
			$obj->zip				=	@$requestData['zipcode'];
			$obj->country				=	@$requestData['country'];
			$obj->dob				=	date('Y-m-d', strtotime(@$requestData['dob']));
			
			$saved				=	$obj->save();
			$success['user_data'] =  @$obj;
			return $this->sendResponse($success, 'Congrats! You have successfully register into our system.');
		 }else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	  
}