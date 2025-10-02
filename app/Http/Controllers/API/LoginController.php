<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\User;
use App\Models\PasswordResetLink;
use App\Models\Admin;
use Validator;

class LoginController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function forget(Request $request){
		$requestData = $request->all();
		$client_id = @$requestData['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		  
		if($users){
			$validator = Validator::make($requestData, [
					'email' => 'required|email',
				], 
				[
					'email.required' => 'The email field is required.',
					'email.email' => 'The email must be a valid email address.',
				]
		);
 
        if($validator->fails()){
            return $this->sendError('Error', $validator->errors());       
        }
		if (User::where('email', '=', @$requestData['email'])->where('client_id',$users->id)->exists())
		{
			$existance = PasswordResetLink::where('email', '=', @$requestData['email'])->where('expire', '=', 0)->where('client_id', '=', @$users->id)->first();
			
			if(!empty($existance)){
				 return $this->sendError('Error', array('email'=>array('We have already sent reset link into your email-id, so please check your Inbox and spam folder too.')));  
					
			}
			$token = $this->generateRandomString();
			$obj				= 	new PasswordResetLink;
			$obj->email			=	@$requestData['email'];
			$obj->client_id			=	@$users->id;
			$obj->token			=	$token;

			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return $this->sendError('Error', array('email'=>array(Config::get('constants.server_error'))));
				
			}else{
				$userData = User::select('id', 'first_name', 'last_name')->where('email', '=', @$requestData['email'])->first();
				
				$link = $request->url.'/reset_link/'.$token;
				
				$replace = array('{logo}', '{customer_name}', '{link}', '{year}');					
				$replace_with = array(\URL::to('/public/img/profile_imgs').'/'.@$users->profile_img, @$userData->first_name.' '.@$userData->last_name, $link, date('Y'));
		 
				$this->send_email_template($replace, $replace_with, 'forgot-password', $requestData['email'],'Reset Password Request',$users->email);
				$success['message'] = "1";
				return $this->sendResponse($success, 'We have sent Reset link into your email-id, please check the same.');
			}
			
		}else{
			 return $this->sendError('Error', array('email'=>array('Email not exist in our database.')));
		}
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
    public function login(Request $request)
    {  
		$requestData = $request->all();
		$client_id = @$requestData['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		  
		if($users){
				$validator = Validator::make($requestData, [
					'email' => 'required|email',
					'password' => 'required'
				], 
				[
					'email.required' => 'The email field is required.',
					'email.email' => 'The email must be a valid email address.',
					'password.required' => 'The password field is required.',
					'password.min' => 'The password must be at least 6 characters.',
					'password.max' => 'The password may not be greater than 10 characters.'
				]
		);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		
		if (User::where('email', '=', @$requestData['email'])->where('client_id',$users->id)->exists())
		{
			$userDetails = User::where('email', '=', @$requestData['email'])->where('client_id',$users->id)->first();	
			 
			if (Hash::check($requestData['password'], $userDetails->password)) 
			{		 
				$success['user_data'] =  @$userDetails;
				return $this->sendResponse($success, 'Now you have login successfully.');
			}else{
				return $this->sendError('Authentication Error.', array('email'=>array('These credentials do not match our records.')));
			}
		}
		else
		{
			 return $this->sendError('Authentication Error.', array('email'=>array('These credentials do not match our records.'))); 	
		}	
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}	
    }
}