<?php

namespace App\Http\Controllers\RecruiterControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\MessageAttachment;
use App\Models\Messages;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class MessagesController extends RecruiterController
{
    public function getIndex(Request $Request){

        $user=MyAuth::user('recruiter');
        $search="";
        if($Request->has("search")) {   
            $search=$Request["search"];
        }
    	$query = MessagesRepo::getConversationRef($user,0,true,$search);
            if($Request->has('sortBy')) {
                $sortOrder = "asc";
                if($Request->has('sortOrder')) {
                    $sortOrder=$Request["sortOrder"];
                }
                $query->orderBy($Request['sortBy'], $sortOrder);
            } else {
                $query->orderBy('messages.readed','desc');
                //$query->orderBy('messages.created_at','desc');
                //$query->orderBy('receiver_user.name');
            }
            $myConversations=$query->paginate(10);

            $page = $Request->has('page') ? $Request->page : 1;

            $columns = ['conversation_title'];
            $sort_columns = [];
            foreach ($columns as $column) {
                $sort_columns[$column]["params"] = [
                    'page' => $page,
                    'sortBy' => $column
                ];
                if($Request->has('sortOrder')) {
                    $sort_columns[$column]["params"]["sortOrder"] = $Request["sortOrder"] == "asc" ? "desc" : "asc";
                    if($sort_columns[$column]["params"]["sortOrder"] == "asc") {
                        $sort_columns[$column]["angle"] = "up";
                    } else {
                        $sort_columns[$column]["angle"] = "down";
                    }
                } else {
                    $sort_columns[$column]["params"]["sortOrder"] = "desc";
                    $sort_columns[$column]["angle"] = "down";
                }

                if($Request->has("search")) {
                    $sort_columns[$column]["params"]["search"] = $Request->search;
                }
            }
        foreach ($myConversations as $key=>$val) {
            $userId=($val['sender']==$user->id) ? $val['receiver'] : $val['sender'];
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user,$userId);
            $myConversations[$key]->messagecount=$getNewMessagesCount; 
        }      
       
        $isRequestSearch=$Request->has('search');
        return view('recruiter.messages.index',[
    		'user'=>$user,
    		'myConversations' => $myConversations,
            'isRequestSearch'=>$isRequestSearch,
            'sort_columns' => $sort_columns,
		]);
    }

    public function getConversation(Request $Request, $conversation_ref){
        $user=MyAuth::user('recruiter');
        $userValid=MessagesRepo::isUserValidForConversation($conversation_ref,$user->id);
        
        if(!$userValid){
            return redirect()->route('recruiter-message')->withInput(Input::all())->with([
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
        
		
    	return view('recruiter.messages.conversation',[
    		'user'=>$user,
    		'conversation_ref'=>$conversation_ref,
    		'conversationTitle' => $conversationTitle,
    		'conversationId' => $conversationId,
    		'myConversations' => $myConversations,
    		'conversationMessages' => $conversationMessages,
    		'type'=>2
		]);
    }

    public function getDownloadAttachment(Request $Request, MessageAttachment $MessageAttachment, $filename){
        $error_message = "You've to login first to download attachment.";
        if(MyAuth::check('recruiter')) {
            $user = MyAuth::user('recruiter');
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
