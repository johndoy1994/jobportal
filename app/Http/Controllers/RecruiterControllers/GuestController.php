<?php

namespace App\Http\Controllers\RecruiterControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class GuestController extends Controller
{
	public function __construct() {
        parent::__construct();
        $getNewMessagesCount = 0;
        if(MyAuth::check('recruiter')) {
            $user=MyAuth::user('recruiter');
            
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user);
        }
        View::share("messageCount", $getNewMessagesCount);
    }
    public function getIndex(Request $request) {
        $is_recruiters=0;
        if($request->has('is_recruiter')){
            $is_recruiters=($request->is_recruiter=='1')? "1" :"0";
        }
        return view('recruiter.index',[
            'is_recruiters'=>$is_recruiters
            ]);
    }
}
