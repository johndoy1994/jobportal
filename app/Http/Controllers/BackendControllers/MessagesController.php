<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\MessageAttachment;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;

class MessagesController extends BackendController
{   
    
    public function getMessageIndex(Request $Request){
        $user=MyAuth::user('admin');
        $search="";
        if($Request->has("search")) {   
            $search=$Request["search"];
        }
        if($Request->has('Usertype') && ($Request->Usertype=='jobseeker' || $Request->Usertype=='recruiter')){
            $userType = ($Request->Usertype == 'jobseeker') ? "JOB_SEEKER" : "EMPLOYER";
        	$query = PublicRepo::getJobSeekerOrRecruiterForAdminChat($userType,$search,$Request);
            $myConversations=$query->paginate(10);
        }else{
        	return redirect()->route('admin-home')->with([
        			'error_message' => "You are not Authorize to View this Conversation"
        	]);
        }	
    	//echo'<pre>';print_r($myConversations);exit;
        foreach ($myConversations as $key=>$val) {
            $refNumber=MessagesRepo::makeConversationRef([$val->id,$user->id]);
            $myConversations[$key]->conversation_ref=$refNumber;
            $messagedata=MessagesRepo::getMessageOfreferenceNumber($refNumber,0);
            //echo"<pre>";print_r($messagedata);exit;
            if($messagedata && isset($messagedata[0])){
                $userId=($messagedata[0]->sender==$user->id) ? $messagedata[0]->receiver : $messagedata[0]->sender;
                $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user,$userId);
                $myConversations[$key]->messagecount=$getNewMessagesCount; 
            }else{
                $myConversations[$key]->messagecount=0;
            }
        }    

        $isRequestSearch=$Request->has('search');
        return view('backend.messages.index',[
    		'user'=>$user,
    		'myConversations' => $myConversations,
    		'Usertype'=>$Request->Usertype,
            'isRequestSearch'=>$isRequestSearch,
            //'sort_columns' => $sort_columns,
		]);
    }

    public function getConversation(Request $Request, $conversation_ref){
        $user=MyAuth::user('admin');
        //echo strlen($user->id);exit;
        if($Request->has('reciever')){
        	$reciever=PublicRepo::getUser($Request->reciever);
        	if($reciever){
        		$refNumber=MessagesRepo::makeConversationRef([$reciever->id,$user->id]);
	        		if($refNumber!=$conversation_ref){
	        			return redirect()->route('admin-home')->with([
	        			'error_message' => "You are not Authorize to View this Conversation"
	        		]);
        		}	
        	}else{
        		return redirect()->route('admin-home')->with([
        			'error_message' => "You are not Authorize to View this Conversation"
        		]);
        	}
        }else{
        	return redirect()->route('admin-home')->with([
				'error_message' => "You are not Authorize to View this Conversation"
			]);
        }
        
    	
        //$myConversations = MessagesRepo::getConversationRef($user);
        if($Request->has('Usertype') && $Request->has('reciever') && ($Request->Usertype=='jobseeker' || $Request->Usertype=='recruiter')){
        	$myConversations = PublicRepo::getJobSeekerOrRecruiter($Request->Usertype,$search="",$is_paging=false);
        	$reciever=PublicRepo::getUser($Request->reciever);
        	if($reciever) {
    			$conversationTitle = $reciever->name;
    			$conversationId = $reciever->id;
    		}
        }else{
        	return redirect()->route('admin-home')->with([
				'error_message' => "You are not Authorize to View this Conversation"
			]);	
        }	
    	
        	
        foreach ($myConversations as $key=>$val) {
            	$myConversations[$key]->conversation_ref=MessagesRepo::makeConversationRef([$val->id,$user->id]);
        } 
    	$conversationMessages = MessagesRepo::getConversationMessages($conversation_ref, $user);
    	
    	// foreach($myConversations as $myConversation) {
    	// 	if($myConversation->conversation_ref == $conversation_ref) {
    	// 		$conversationTitle = $myConversation->getConversationTitle();
    	// 		$conversationId = $myConversation->getConversationId();
    	// 	}
    	// }
        
		
    	return view('backend.messages.conversation',[
    		'user'=>$user,
    		'conversation_ref'=>$conversation_ref,
    		'conversationTitle' => $conversationTitle,
    		'conversationId' => $conversationId,
    		'myConversations' => $myConversations,
    		'conversationMessages' => $conversationMessages,
            'type'=>3,
            'Usertype'=>$Request->Usertype
		]);
    }

    public function getDownloadAttachment(Request $Request, MessageAttachment $MessageAttachment, $filename){
        $error_message = "You've to login first to download attachment.";
        if(MyAuth::check('admin')) {
            $user = MyAuth::user('admin');
            if(MessagesRepo::isUserValidForAttachmentDownload($MessageAttachment->id, $user->id)) {
                $file = storage_path().'/app/message-attachments/'.$MessageAttachment->storage_file;
                return response()->download($file, $filename, ['content-type' => $MessageAttachment->file_mime]);        
            } else {
                $error_message = 'You are not Authorize to download this file.';
            }
        }
        return redirect()->route('admin-home')->with(Input::all())->with([
            'error_message' => $error_message
        ]);

    }
}
