<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Models\MessageAttachment;
use App\Models\Messages;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class MessagesController extends FrontendController
{
   public function getIndex(Request $Request){
        $user=MyAuth::user();
        $search="";
        if($Request->has("search")) {   
            $search=$Request["search"];
        }
    	$query = MessagesRepo::getConversationRef($user,0,true,$search);
        
        $myConversations=$query->paginate(10);
//echo "<pre>";print_r($myConversations);exit;
        foreach ($myConversations as $key=>$val) {
            $userId=($val['sender']==$user->id) ? $val['receiver'] : $val['sender'];
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user,$userId);
            $myConversations[$key]->messagecount=$getNewMessagesCount; 
        }      
        $isRequestSearch=$Request->has('search');
        return view('frontend.messages.index',[
    		'user'=>$user,
    		'myConversations' => $myConversations,
            'isRequestSearch'=>$isRequestSearch,
            //'sort_columns' => $sort_columns,
		]);
    }

    public function getConversation(Request $Request, $conversation_ref){
        $user=MyAuth::user();
        $userValid=MessagesRepo::isUserValidForConversation($conversation_ref,$user->id);
        
        if(!$userValid){
            return redirect()->route('frontend-message')->withInput(Input::all())->with([
                'error_message' => "You are not Authorize to View this Conversation"
            ]);
        }
    	
        $myConversations = MessagesRepo::getConversationRef($user,0,$is_paging=false);
    	$conversationMessages = MessagesRepo::getConversationMessages($conversation_ref, $user);
    	$conversationTitle = "N/A";
    	$conversationId=0;
    	foreach($myConversations as $myConversation) {
    		if($myConversation->conversation_ref == $conversation_ref) {
    			$conversationTitle = $myConversation->getConversationTitle();
    			$conversationId = $myConversation->getConversationId();
    		}
    	}
        
		
    	return view('frontend.messages.conversation',[
    		'user'=>$user,
    		'conversation_ref'=>$conversation_ref,
    		'conversationTitle' => $conversationTitle,
    		'conversationId' => $conversationId,
    		'myConversations' => $myConversations,
    		'conversationMessages' => $conversationMessages,
            'type'=>1
		]);
    }

    public function getDownloadAttachment(Request $Request, MessageAttachment $MessageAttachment, $filename){
        $error_message = "You've to login first to download attachment.";
        if(MyAuth::check()) {
            $user = MyAuth::user();
            if(MessagesRepo::isUserValidForAttachmentDownload($MessageAttachment->id, $user->id)) {
                $file = storage_path().'/app/message-attachments/'.$MessageAttachment->storage_file;
                return response()->download($file, $filename, ['content-type' => $MessageAttachment->file_mime]);        
            } else {
                $error_message = 'You are not Authorize to download this file.';
            }
        }
        return redirect()->back()->withInput(Input::all())->with([
            'error_message' => $error_message
        ]);

    }

    public function postDeleteMessages(Request $Request){

        if(Messages::whereIn('conversation_ref', $Request["Messagemultiple"])->where('is_message',1)->delete()) {
            return redirect()->back()->withInput(Input::all())->with([
                'success_message' => "Message successfully deleted!"
            ]);
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while deleting your Message, try again!"
            ]);
        }
    }
    
    
}
