<?php

namespace App\Repos\API;

use App\Helpers\Time;
use App\Models\MessageAttachment;
use App\Models\Messages;
use Illuminate\Support\Facades\DB;

class MessagesRepo {
	
	public static function getUnreadedMessagesOf($user, $sender) {
		$q = Messages::where(function($q) use($user, $sender) {
			//$q->orWhere('sender', $user->id);
			$q->where('sender', $sender);
			$q->where('receiver', $user->id);
		});
		$q->where('readed',0);
		$q->where('is_message',0);
		return $q->get();
	}

	public static function makeConversationRef($ids) {
		for($i=0;$i<count($ids);$i++) {
			for($j=0;$j<count($ids);$j++) {
				if($ids[$i] < $ids[$j]) {
					$temp = $ids[$i];
					$ids[$i] = $ids[$j];
					$ids[$j] = $temp;
				}
			}
		}
		return implode('', $ids);
	}

	public static function addMessage($user,$data,$is_message=0){
		
		$messages = New Messages();
		$messages->sender = $user->id;
		$messages->receiver = $data['receiverId'];
		$messages->message = $data['message'];
		$messages->is_message = $is_message;
		$messages->readed = 0;
		$messages->conversation_ref = self::makeConversationRef([$user->id, $data['receiverId']]);
		if($messages->save()){
			//return [true , $messages];
			if($data['filename']){
				$file_attachment=self::addfileAttechment($messages->id,$data);
			}
			$fmt_messages=self::getConversationMessages($messages->conversation_ref,$user,$messages->id,true);
			return [true,$fmt_messages]; 
			
		}else{
			return [false , "fail",null];
		}

	}

	public static function addfileAttechment($messageID,$data){
		//print_r(get_class_methods($data["file"]));exit;
		$Message_attachment = New MessageAttachment();
		$Message_attachment->message_id = $messageID;
		$Message_attachment->filename = $data["file"]->getClientOriginalName();
		$Message_attachment->file_mime = $data["file"]->getClientMimeType();
		$Message_attachment->storage_file = $data["filename"];
		if($Message_attachment->save()){
			return true;
		}else{
			return false;
		}
	}

	public static function getConversationMessages($conversation_ref, $user,$lastMessageId=0,$lastmessage=false,$onlyNew = false,$is_message=0){
		$fmt_messages = [];
		if($lastmessage){
			$messages=Messages::where('is_message',$is_message)->where('id', $lastMessageId)->first();
			//$messages = Messages::find($lastMessageId);
				if($messages){
					$fmt_messages[] = [
						'senderName' => $messages->getSenderName(),
						'isRight' => $messages->isSender($user->id),
						'message' => $messages->message,
						//'time' => $messages->created_at->diffForHumans(),
						'time' => $messages->created_at->format('M d, Y h:i:s'),
						'imgId' => $messages->sender,
						'readed' => $messages->readed,
						'msgObject' => $messages
					];
				}
		}else{

			//->whereDate('created_at', '=',Time::now()->format("Y-m-d"))
			//Messages::update(array('readed' => 1))->where('conversation_ref',$conversation_ref);
			$q = Messages::where('conversation_ref', $conversation_ref)->where('is_message',$is_message);
			if($onlyNew) {
				if($is_message==0){
					$q->where('readed',0);
				}
			}
			$messages = $q->get();
			
			foreach($messages as $message) {
				if($onlyNew) {
					if($is_message==0){
						if($message->receiver != $user->id) {
							continue;
						}
					}
				}
				$fmt_messages[] = [
					'senderName' => $message->getSenderName(),
					'isRight' => $message->isSender($user->id),
					'message' => $message->message,
					//'time' => $message->created_at->diffForHumans(),
					'time' =>$message->created_at->format('M d, Y h:i:s'),
					'imgId' => $message->sender,
					'readed' => $message->readed,
					'msgObject' => $message
				];
				if($onlyNew) {
					$message->readed=1;
					$message->update();
				}
			}
		}
		
		return $fmt_messages;
		// return Messages::where('conversation_ref', 2731)->get();
	}

	public static function getChatUser($user){
		$allUser = Messages::where('sender',$user->id)->Orwhere('receiver',$user->id)->groupBy('conversation_ref')->get();		
		return $allUser;
	}

	public static function getLastMessage($conversation_ref) {
		return Messages::where('conversation_ref', $conversation_ref)->where('is_message', 1)->orderBy('messages.id','desc')->first();
	}

	public static function getAllMessage($conversation_ref){
		return Messages::where('conversation_ref', $conversation_ref)->where('is_message',1)->get();
	}


	public static function getConversationRef($user,$is_message=0,$is_paging=true,$search="") {

			$q = Messages::select(
				DB::raw("max(messages.id) as last_message_id"),
				"conversation_ref",
				"sender",
				"receiver",
				"message",
				"is_message",
				"messages.created_at as createddAt",
				DB::raw("IF(messages.sender=".$user->id.",receiver_user.name,sender_user.name) as conversation_title"),
				DB::raw("IF(messages.sender=".$user->id.",receiver_user.mobile_number,sender_user.mobile_number) as conversation_mobile"),
				DB::raw("IF(messages.sender=".$user->id.",receiver_user.email_address,sender_user.email_address) as conversation_email"),
				DB::raw("IF(messages.sender=".$user->id.",receiver_user.id,sender_user.id) as conversation_id"),
				
				//last modify start date 20-09-2016//	
				DB::raw('(SELECT count(*) FROM messages where messages.receiver = "'.$user->id.'" AND messages.readed = "0" AND (messages.sender = sender_user.id OR messages.sender = receiver_user.id) AND is_message = "'.$is_message.'") as msg_count')
				
				//DB::raw('(SELECT created_at FROM messages where receiver = "'.$user->id.'" AND readed = "0" AND (messages.sender = sender_user.id OR messages.sender = receiver_user.id) AND is_message = "'.$is_message.'") as msg_created_at')
				//last modify end date 20-09-2016//
			);
			$q->join(DB::raw("(select * from users) as sender_user"),"sender_user.id",'=','messages.sender');
			$q->join(DB::raw("(select * from users) as receiver_user"),"receiver_user.id",'=','messages.receiver');
			$q->where(function($q) use($user) {
				$q->orWhere('messages.sender', $user->id);
				$q->orWhere('messages.receiver', $user->id);
			});
			if($is_message){
				$q->where('messages.is_message',1);
			}else{
				$q->where('receiver_user.name','like','%'.$search.'%');
				$q->where('messages.is_message',0);
			}
			//last modify start date 20-09-2016//
			$q->orderBy("msg_count", 'desc');
			$q->orderBy("last_message_id",'desc');
			
			//last modify end date 20-09-2016//
			$q->groupBy("messages.conversation_ref");
			if($is_paging){
				return $q;
			}else{
				return $q->get();
			}


			// $q = Messages::select(
			// 	DB::raw("max(messages.id) as last_message_id"),
			// 	"conversation_ref",
			// 	"sender",
			// 	"receiver",
			// 	"message",
			// 	"is_message",
			// 	"messages.created_at as createddAt",
			// 	DB::raw("IF(messages.sender=".$user->id.",receiver_user.name,sender_user.name) as conversation_title"),
			// 	DB::raw("IF(messages.sender=".$user->id.",receiver_user.mobile_number,sender_user.mobile_number) as conversation_mobile"),
			// 	DB::raw("IF(messages.sender=".$user->id.",receiver_user.email_address,sender_user.email_address) as conversation_email"),
			// 	DB::raw("IF(messages.sender=".$user->id.",receiver_user.id,sender_user.id) as conversation_id")
			// );
			// $q->join(DB::raw("(select * from users) as sender_user"),"sender_user.id",'=','messages.sender');
			// $q->join(DB::raw("(select * from users) as receiver_user"),"receiver_user.id",'=','messages.receiver');
			// $q->where(function($q) use($user) {
			// 	$q->orWhere('messages.sender', $user->id);
			// 	$q->orWhere('messages.receiver', $user->id);
			// });
			// if($is_message){
			// 	$q->where('messages.is_message',1);
			// }else{
			// 	$q->where('receiver_user.name','like','%'.$search.'%');
			// 	$q->where('messages.is_message',0);
			// }
			// $q->groupBy("messages.conversation_ref");
			// if($is_paging){
			// 	return $q;
			// }else{
			// 	return $q->get();
			// }	
			
	}

	public static function MessageStatusUpdate($conversation_ref,$user){
		$record = Messages::where('conversation_ref', $conversation_ref)->first();
		if($record){	
			if($record->sender==$user->id || $record->receiver==$user->id){
				$Messages = Messages::where('conversation_ref',$conversation_ref)->where('is_message',0)->update(['readed'=>1]);
				 if($Messages){
				 	return [true ,"true"];
				 }else{
				 	return [false ,"fail"];
				 }
			}
		}
		return [false ,"fail"];
	}

	public static function notificationStatusUpdate($user_id,$user){
		$record = Messages::where('receiver', $user_id)->where('is_message',1)->first();
		if($record){	
			if($record->sender==$user->id || $record->receiver==$user->id){
				$Messages = Messages::where('receiver',$user_id)->where('is_message',1)->update(['readed'=>1]);
				 if($Messages){
				 	return [true ,"true"];
				 }else{
				 	return [false ,"fail"];
				 }
			}
		}
		return [false ,"fail"];
	}

	public static function isUserValidForAttachmentDownload($attachmentId, $user_id){
        $q = Messages::select("messages.*")->join("message_attachments", "message_attachments.message_id", '=',"messages.id");
        $q->where("message_attachments.id", "=", $attachmentId);
        $msg = $q->first();        
        if($msg) {
            return $msg->isReceiver($user_id) || $msg->isSender($user_id);
        }
        return false;
    }

    public static function isUserValidForConversation($conversation_ref,$user_id){
    	$messages = Messages::where('conversation_ref', $conversation_ref)->where('is_message', "!=",1)->first();
    	if($messages){
    		return $messages->isReceiver($user_id) || $messages->isSender($user_id);	
    	}
    	return false;
    }

    public static function getNewMessagesCount($user, $sender = 0){
    	
    	$q = Messages::where(function($q) use($user, $sender) {
			//$q->orWhere('sender', $user->id);
    		if($sender > 0) {
    			$q->where('sender', $sender);
    		}
			$q->where('receiver', $user->id);
		});
		$q->where('readed',0);
		$q->where('is_message',0);
		return $q->count('messages.id');
    }

    public static function getNotificationCount($user, $sender = 0){
    	
    	$q = Messages::where(function($q) use($user, $sender) {
			//$q->orWhere('sender', $user->id);
    		if($sender > 0) {
    			$q->where('sender', $sender);
    		}
			$q->where('receiver', $user->id);
		});
		$q->where('readed',0);
		$q->where('is_message',1);
		return $q->count('messages.id');
    }

    public static function getJobseekerMessagesCount($user, $sender = 0){
    	
    	$q = Messages::select('messages.*','users.id as userId')->join("users", "users.id", '=',"messages.sender");
    		$q->where(function($q) use($user, $sender) {
			if($sender > 0) {
    			$q->where('messages.sender', $sender);
    		}
    		$q->where('users.type', 'JOB_SEEKER');
			$q->where('messages.receiver', $user->id);
		});
		$q->where('messages.readed',0);
		$q->where('is_message',0);
		return $q->count('messages.id');
    }

    public static function getRecruiterMessagesCount($user, $sender = 0){
    	
    	$q = Messages::select('messages.*','users.id as userId')->join("users", "users.id", '=',"messages.sender");
    		$q->where(function($q) use($user, $sender) {
			if($sender > 0) {
    			$q->where('messages.sender', $sender);
    		}
    		$q->where('users.type', 'EMPLOYER');
			$q->where('messages.receiver', $user->id);
		});
		$q->where('messages.readed',0);
		$q->where('is_message',0);
		return $q->count('messages.id');
    }

    public static function getJobseekerNotificationCount($user, $sender = 0){
    	
    	$q = Messages::select('messages.*','users.id as userId')->join("users", "users.id", '=',"messages.sender");
    		$q->where(function($q) use($user, $sender) {
			if($sender > 0) {
    			$q->where('messages.sender', $sender);
    		}
    		$q->where('users.type', 'JOB_SEEKER');
			$q->where('messages.receiver', $user->id);
		});
		$q->where('messages.readed',0);
		$q->where('is_message',1);
		return $q->count('messages.id');
    }

    public static function getRecruiterNotificationCount($user, $sender = 0){
    	
    	$q = Messages::select('messages.*','users.id as userId')->join("users", "users.id", '=',"messages.sender");
    		$q->where(function($q) use($user, $sender) {
			if($sender > 0) {
    			$q->where('messages.sender', $sender);
    		}
    		$q->where('users.type', 'EMPLOYER');
			$q->where('messages.receiver', $user->id);
		});
		$q->where('messages.readed',0);
		$q->where('is_message',1);
		return $q->count('messages.id');
    }

    public static function getMessageOfreferenceNumber($refNumber,$is_message=0){
  			$q = Messages::select(
				DB::raw("max(messages.id)"),
				"conversation_ref",
				"sender",
				"receiver",
				"message",
				"is_message",
				"messages.created_at as createddAt"
			);
			$q->where('conversation_ref', $refNumber);
			$q->where('is_message', $is_message);
		 	$q->groupBy("conversation_ref");
		 	return $q->get();
  
  //   	$q = Messages::select('*');
  //   	$q->where('conversation_ref', $refNumber);
		// $q->groupBy("conversation_ref");
		// return $q->get();
    }
    public static function is_viewCOnversation($ref){
    	$result =Messages::where('conversation_ref',"=",$ref)->where('is_message',"=",1)->first();
    	if($result){
    		return true;
    	}else{
    		return false;
    	}
    }
}