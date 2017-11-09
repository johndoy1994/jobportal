<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;

class NotificationsController extends BackendController
{
	public function getNotificationsIndex(Request $Request){
        $user=MyAuth::user('admin');
        $search="";
        if($Request->has("search")) {   
            $search=$Request["search"];
        }
        if($Request->has('Usertype') && ($Request->Usertype=='jobseeker' || $Request->Usertype=='recruiter')){
        	$userType = ($Request->Usertype == 'jobseeker') ? "JOB_SEEKER" : "EMPLOYER";
            $query = PublicRepo::getJobSeekerOrRecruiterForAdminChat($userType,$search,$Request,$is_messages=1);
            $myConversations=$query->paginate(10);
        }else{
        	return redirect()->route('admin-home')->with([
        			'error_message' => "You are not Authorize to View this Conversation"
        	]);
        }	
     
        foreach ($myConversations as $key=>$val) {
            $refNumber=MessagesRepo::makeConversationRef([$val->id,$user->id]);
            $myConversations[$key]->conversation_ref=$refNumber;
            $messagedata=MessagesRepo::getMessageOfreferenceNumber($refNumber,1);
            if($messagedata && isset($messagedata[0])){
                $userId=($messagedata[0]->sender==$user->id) ? $messagedata[0]->receiver : $messagedata[0]->sender;
                $getNewMessagesCount = MessagesRepo::getNotificationCount($user,$userId);
                $myConversations[$key]->messagecount=$getNewMessagesCount;
                $myConversations[$key]->conversation_ref=$messagedata[0]->conversation_ref; 
                $myConversations[$key]->is_viewConversation=MessagesRepo::is_viewCOnversation($messagedata[0]->conversation_ref);
            }else{
                $myConversations[$key]->messagecount=0;
                $myConversations[$key]->is_viewConversation=MessagesRepo::is_viewCOnversation($refNumber);
            }
        }    

        $isRequestSearch=$Request->has('search');
        return view('backend.notification.index',[
    		'user'=>$user,
    		'myConversations' => $myConversations,
    		'Usertype'=>$Request->Usertype,
            'isRequestSearch'=>$isRequestSearch,
        ]);
    }

}
