<?php

namespace App\Http\Controllers;

use App\Helpers\Notifier;
use App\Helpers\Time;
use App\Http\Requests;
use App\Models\Job;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class GuestController extends Controller
{
    public function __construct() {
        parent::__construct();
        $getNewMessagesCount = 0;
        if(MyAuth::check()) {
            $user=MyAuth::user();
            
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user);
        }
        View::share("messageCount", $getNewMessagesCount);
    }
	
    public function getHome(Request $request) {
        $is_jobseeker=0;
        if($request->has('is_jobseeker')){
            $is_jobseeker=($request->is_jobseeker=='1')? "1" :"0";
        }
        $login_level=$request->has('login_level') ? $request->login_level : 0;
    	$states = PublicRepo::getStatesWithJobCount();
    	$companies = PublicRepo::getCompaniesWithJobCount();
        $publicSearch = PublicRepo::getPublicSearch();
    	return view('frontend.index', [
    		'searchMiles' => PublicRepo::allSearchMiles(),
    		'jobCategories' => PublicRepo::allJobCategories(),
    		'states' => $states,
    		'companies' => $companies,
            'publicSearch' => $publicSearch,
            'login_level'=>$login_level,
            'is_jobseeker'=>$is_jobseeker
		]);
    }

}
