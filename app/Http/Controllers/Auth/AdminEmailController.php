<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use App\LoginLog;
use App\Models\Admin;

class AdminEmailController extends Controller
{
	use AuthenticatesUsers;

    /**

     * Where to redirect users after login.

     *

     * @var string

     */

    protected $redirectTo = '/email_users/dashboard';

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()
	{
		$this->middleware('guest:email_users')->except('logout');
	}

	protected function credentials(\Illuminate\Http\Request $request)
	{
        //dd($request->all());
		if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->get('email'), 'password'=>$request->get('password')];
        }
        return ['name' => $request->get('email'), 'password'=>$request->get('password')];
	}

	protected function guard()
	{
        return Auth::guard('email_users');
	}

	public function showLoginForm(){
		return view('auth.admin-emaillogin');
	}

	public function authenticated(Request $request, $user)
	{
        if(!empty($request->remember)) {
            Cookie::queue(Cookie::make('username', $request->email, 3600));
            Cookie::queue(Cookie::make('password', $request->password, 3600));
        } else {
            Cookie::queue(Cookie::forget('username'));
            Cookie::queue(Cookie::forget('password'));
		}
        return redirect()->intended($this->redirectPath());
    }

	public function logout(Request $request)
	{

		Auth::guard('email_users')->logout();

        $request->session()->flush();

        $request->session()->regenerate();

		return redirect('/email_users/login');
	}

}
