<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends FrontendController
{
    public function __construct() {
    	parent::__construct();
    }

    //job application listing
    public function getIndex() {
    	if(MyAuth::check()) {
            $user=MyAuth::user();
            $results = MessagesRepo::getConversationRef($user,$is_message=1,false);
        }
        //echo'<pre>';print_r($results);exit;
    	return view('frontend.account.notification.index', [
    		'user'=>MyAuth::user(),
    		'results'=>$results	
		]);
    }
}
