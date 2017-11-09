<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
	public function __construct() {
		$this->middleware("role:BACKEND_ADMIN,*,admin,admin-login", [
			'only' => ["getLogout","getChangePassword","postChangePassword"]
		]);
	}

    public function getIndex() {
    	
    	if(MyAuth::user("admin")){
    		return redirect()->route('admin-home');
    	}
    	return view('backend.guest.login');
    }

    public function postIndex(Request $request){
    	$this->validate($request, [
    		'email'=> 'required|email',
            'password' => 'required'
        ],[
    		'email.required'=>'Please enter email.',
            'password.required'=>'Please enter password.',
        ]);

        if(MyAuth::attempt(["email_address"=>$request->email, "password"=>$request->password], "admin")) {
            $user = MyAuth::user("admin");
            if($user && !$user->isAdministrator()) {
                MyAuth::logout("admin");
                return redirect()->route('admin-home')->withErrorMessage("Sorry, you're not allowed sign here.");
            }
            if($user && !$user->isActivated()) {
                MyAuth::logout("admin");
                return redirect()->route('admin-home')->withErrorMessage("Sorry, your account is not activated yet, please try again after sometime.");
            }
        	return redirect()->route('admin-home');
        } else {
        	return redirect()->back()->withErrorMessage("Email/Password didn't matched, try again.");
        }
    }

    //Change Password
    public function getChangePassword(){
    	return view('backend.account.change-password');
    }

    public function postChangePassword(Request $request){
    	$this->validate($request, [
            "currentPassword" => "required",
            "password" => "required|confirmed",
            "password_confirmation" => "required"
        ]);

    	list($status[0],$message[0])=AdminRepo::changePassword($request->all());
    	if($status[0]){
    		return redirect()->back()->with([
    			'success_message' => $message[0]
    			]);
    	}else{
    		return redirect()->back()->with([
    			'error_message' => $message[0]
    			]);
    	}
    }

    public function getForgotPassword() {
        return view("backend.guest.forgot-password");
    }

    public function postForgotPassword(Request $request) {

        if($request->has('code')) {

            return redirect()->route('api-public-resetpasswordlink', ['code' => Utility::hash_hmac($request->code),'type'=>3]);

        } else {
            list($success, $message) = PublicRepo::processResetPasswordRequest($request);
            if($success) {
                return redirect()->route('admin-account-forgotpassword')->withSuccessMessage($message)->with([
                    'email_address' => $request->email_address,
                    'mobile_number' => $request->mobile_number
                ]);
            } else {
                return redirect()->route('admin-account-forgotpassword')->withErrorMessage($message);
            }
        }

    }
    //LogOut 
    public function getLogout() {
    	MyAuth::logout("admin");
    	return redirect()->route('admin-login');
    }


}
