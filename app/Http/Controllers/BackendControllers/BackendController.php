<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class BackendController extends Controller
{
    
    public function __construct() {
    	parent::__construct();
    	$getNewMessagesCount = 0;
    	$getJobseekerMessagesCount = 0;
    	$getRecruiterMessagesCount = 0;
        $notificationCount = 0;
        $JobseekernotificationCount = 0;
        $RecruiternotificationCount = 0;
        if(MyAuth::check('admin')) {
            $user=MyAuth::user('admin');
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user);
            $getJobseekerMessagesCount = MessagesRepo::getJobseekerMessagesCount($user);
            $getRecruiterMessagesCount = MessagesRepo::getRecruiterMessagesCount($user);

            $notificationCount = MessagesRepo::getNotificationCount($user);
            $JobseekernotificationCount = MessagesRepo::getJobseekerNotificationCount($user);
            $RecruiternotificationCount = MessagesRepo::getRecruiterNotificationCount($user);
            //echo $getRecruiterMessagesCount;exit;
        }
        View::share("messageCount", $getNewMessagesCount);
        View::share("JobseekermessageCount", $getJobseekerMessagesCount);
        View::share("RecruitermessageCount", $getRecruiterMessagesCount);
        View::share("notificationCount", $notificationCount);
        View::share("JobseekernotificationCount", $JobseekernotificationCount);
        View::share("RecruiternotificationCount", $RecruiternotificationCount);
    	$this->middleware("role:BACKEND_ADMIN,*,admin,admin-login");
        $this->middleware("BackendVerification");
    }

    public function getIndex() {
    	return view('backend.index');
    }

}
