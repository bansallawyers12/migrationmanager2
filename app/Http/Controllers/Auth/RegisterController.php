<?php

namespace App\Http\Controllers\Auth;

use App\Models\Admin;
// use App\Models\VerifyUser; // REMOVED: VerifyUser model has been deleted
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
	
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
		if(!empty($data['phone']))
		{	
			$data['phone'] = str_replace("-","", @$data['phone']);
		}
		
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:admins,email',
            'password' => 'required|string|min:6|max:12|confirmed',
            'phone' => 'required|string|min:10|unique:admins,phone',
        ], [
				'email.required' => 'The email field is required.',
				'email.email' => 'The email must be a valid email address.',
				'password.required' => 'The password field is required.',
				'password.min' => 'The password must be at least 6 characters.',
				'password.max' => 'The password may not be greater than 10 characters.',
				'phone.required' => 'The phone field is required.',
				'phone.min' => 'The phone must be at least 12 characters.',
			]);
    }

    /** 
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Admin
     */
    protected function create(array $data)
    {
        $result = Admin::create([
            'first_name' 	=> @$data['first_name'],
            'last_name' 	=> @$data['last_name'],
            'email' 		=> @$data['email'],
            'password' 		=> Hash::make($data['password']),
            'phone' 		=> str_replace("-","", @$data['phone']),
            'company_name' 	=> @$data['company_name'],        
            'role' 	=> 7,        
        ]);
		
		if($result)
		{	
			// Email verification disabled - VerifyUser model has been removed
			return $result;
		}
    }
	
	protected function registered(Request $request, $user)
    {
        return redirect()->route('dashboard')->with('success','welcome '. $user->name . ' you are registered. Please check your email inbox to verify email.');
    }
	 /**
	  * Verify user email
	  * DISABLED: VerifyUser model has been removed
	  */
	 public function verifyUser($token)
    {
        // Email verification disabled - VerifyUser model has been removed
        return redirect()->route('dashboard')->with('warning', "Email verification has been disabled - VerifyUser model has been removed");
    }
}
