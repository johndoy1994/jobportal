<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class FrontendController extends Controller
{
    public function __construct() {
    	parent::__construct();
    	
    	$getNewMessagesCount = 0;
        $getNotificationCount=0;
        if(MyAuth::check()) {
            $user=MyAuth::user();
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user);
            $getNotificationCount = MessagesRepo::getNotificationCount($user);
        }

        View::share("messageCount", $getNewMessagesCount);
        View::share("NotificastionCount",$getNotificationCount);

        $this->middleware('role:frontend,job_seeker');
    }
}
