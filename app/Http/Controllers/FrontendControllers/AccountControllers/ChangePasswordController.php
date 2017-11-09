<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ChangePasswordController extends FrontendController
{
    public function __construct() {
    	parent::__construct();
    }

    //Change password
    public function getIndex() {
    	return view('frontend.account.change-password.index', [
    		'user'=>MyAuth::user()
		]);
    }

    public function postSave(Request $request, UserRepo $repo){

    	$this->validate($request, [
    		"password" => "required",
    		"password_new" => "required|min:5|confirmed",
    		"password_new_confirmation" => "required|min:5"
		],[
            'password_new.min'=>'The new password must be at least 5 characters.',
            'password_new.confirmed'=>'The new password and confirm password does not match.'
        ]);

    	list($result, $message) = $repo->updatePassword(MyAuth::user()->id, $request->password, $request->password_new);
    	if($result) {
    		return redirect()->back()->with([
				'success_message' => $message
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => $message
			]);
		}

    }
}
