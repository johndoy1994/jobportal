<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DeleteAccountController extends RecruiterController
{
    //delete account
    public function getIndex() {
    	return view('recruiter.account.delete-account.index',[
            'user' => MyAuth::user('recruiter')
            ]);
    }

    public function postDeleteAccount(Request $request, UserRepo $repo){

    	list($result, $message) = $repo->deleteAccount(MyAuth::user('recruiter')->id);
    	if($result) {
    		return redirect()->route('recruiter-account-signout')->with([
				'success_message' => $message
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => $message
			]);
		}

    }
}
