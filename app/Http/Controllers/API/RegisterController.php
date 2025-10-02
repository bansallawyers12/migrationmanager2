<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
 
use App\Models\User;
use App\Models\Admin;
use Validator;
use Config;

class RegisterController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
		 $requestData = $request->all();
	
		$client_id = @$requestData['client_id'];
	
		$users = Admin::where('client_id', '=', $client_id)->first();
		
		if($users){
			$validator = Validator::make($requestData, [
				'name' => 'required',
				'company_name' => '',

				'email' => 'required|string|email|max:191',
				'password' => 'required|confirmed|string|min:6|max:12',
				'cc' => '',

			], 
			[
				'email.required' => 'The email field is required.',
				//'company_name.required' => 'The Company Name field is required.',
				'name.required' => 'The Full Name field is required.',
				'email.email' => 'The email must be a valid email address.',
				'password.required' => 'The password field is required.',
				'password.min' => 'The password must be at least 6 characters.',
				'password.max' => 'The password may not be greater than 10 characters.',
				//'cc.required' => 'Please accept term of use'
			]
); 

        if($validator->fails()){
            $errors = $validator->errors();
			//print_r($errors);
			if ($errors->has('name')){
				return response()->json([ 'success' => false, 'errors' => $errors->first('name')]); 
			}
			
			if ($errors->has('email')){
				return response()->json([ 'success' => false, 'errors' => $errors->first('email')]); 
			}
			
			if ($errors->has('password')){
				return response()->json([ 'success' => false, 'errors' => $errors->first('password')]); 
			}     
        }
		
		$isexist = User::where('client_id',$users->id)->where('email',$request->email)->exists();
		if($isexist){
			
			return response()->json([ 'success' => false, 'errors' => 'The email has already been taken.']); 
		}
$wedding_anniversary = '';
			if(@$requestData['wedding_anniversary'] != ''){
				$wedding_anniversary			=	date('Y-m-d', strtotime(@$requestData['wedding_anniversary']));
			}
		$user = User::create([
            'name' 	=> @$requestData['name'],
            'email' 		=> @$requestData['email'],
            'password' 		=> Hash::make($requestData['password']),
            'company_name' 		=> @$requestData['company_name'],
            'phone' 		=> @$requestData['phone'],
            'city' 		=> @$requestData['city'],
            'address' 		=> @$requestData['location'],
			
            'dob' 		=> date('Y-m-d', strtotime(@$requestData['dob']));,
            'wedding_anniversary' 		=> @$wedding_anniversary,
            
            'client_id' 	=> $users->id  
        ]); 
		 
		/*if($user)
		{
			 $replace = array('{logo}', '{first_name}', '{last_name}', '{year}');					
			$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @$requestData['first_name'], @$requestData['last_name'], date('Y'));
			
			$this->send_email_template($replace, $replace_with, 'signup', @$requestData['email']); 
		
		} */ 
		$userDetails = User::where('id', '=', @$user->id)->first();
         $success['user_data'] =  @$user; 

        return $this->sendResponse($success, 'Congrats! You have successfully register into our system.');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
    }
}