<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;

class NotificationsController extends RecruiterController
{
    
	public function getIndex() {
    	if(MyAuth::check('recruiter')) {
            $user=MyAuth::user('recruiter');
            $results = MessagesRepo::getConversationRef($user,$is_message=1,false);
        }
        //echo'<pre>';print_r($results);exit;
    	return view('recruiter.account.notification.index', [
    		'user'=>MyAuth::user('recruiter'),
    		'results'=>$results	
		]);
    }
}
