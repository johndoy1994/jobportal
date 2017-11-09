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

class DeleteAccountController extends FrontendController
{
	public function __construct() {
    	parent::__construct();
    }

    //delete account
    public function getIndex() {
    	return view('frontend.account.delete-account.index', [
    		'user'=>MyAuth::user()
		]);
    }

    public function postDeleteAccount(Request $request, UserRepo $repo){

    	list($result, $message) = $repo->deleteAccount(MyAuth::user()->id);
    	if($result) {
    		return redirect()->route('account-signout')->with([
				'success_message' => $message
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => $message
			]);
		}

    }
}
