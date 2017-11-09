<?php

namespace App\Models;

use App\Models\MessageAttachment;
use App\Repos\API\MessagesRepo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Messages extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['readed'];

    public function getLastMessage($conversation_ref) {
        return MessagesRepo::getLastMessage($conversation_ref);
    }

    public function getAllMessage($conversation_ref) {
        return MessagesRepo::getAllMessage($conversation_ref);
    }
    public function getNotificationCount($user){
        $userId=($this->sender==$user->id) ? $this->receiver : $this->sender;
        return MessagesRepo::getNotificationCount($user,$userId);
    }
    public function senderUser() {
    	return $this->hasOne('\App\Models\User','id','sender');
    }

    public function receiverUser() {
    	return $this->hasOne('\App\Models\User','id','receiver');
    }

    public function isSender($user_id) {
    	return $this->sender == $user_id;
    }

    public function isReceiver($user_id) {
    	return $this->receiver == $user_id;
    }

    public function getSenderName() {
    	if($this->senderUser) {
    		return $this->senderUser->getName();
    	}
    	return "N/A";
    }

    public function getReceiverName() {
    	if($this->receiverUser) {
    		return $this->receiverUser->getName();
    	}
    	return "N/A";
    }

    public function getConversationTitle() {
        if($this->conversation_title) {
            return ucwords($this->conversation_title);
        }
        return "N/A";
    }

    public function getConversationInfo() {
        
        $data=array();
        if($this->conversation_mobile) {
            $data[] =$this->conversation_mobile;
        }
        if($this->conversation_email) {
            $data[] =$this->conversation_email;
        }
        $info =' ('.implode(', ', $data).')';
        return $info;
    }    

    public function getConversationId() {
        if($this->conversation_id) {
            return ucwords($this->conversation_id);
        }
        return 0;
    }

    public function attachments() {
        return $this->hasMany('\App\Models\MessageAttachment','message_id','id');
    }

    public function isAttachment() {
        if($this->attachments) {
            return count($this->attachments) > 0;
        }
        return false;
    }

    public function attachmentLink() {
        $messageAttachment = $this->attachments()->first();
        if($messageAttachment) {
            return route('frontend-downloadAttachment', ['MessageAttachment'=>$messageAttachment->id, 'filename'=>$messageAttachment->filename]);
        }
        return "";
    }
    public function attachmentLinkRecruiter() {
        $messageAttachment = $this->attachments()->first();
        if($messageAttachment) {
            return route('recruiter-downloadAttachment', ['MessageAttachment'=>$messageAttachment->id, 'filename'=>$messageAttachment->filename]);
        }
        return "";
    }

    public function attachmentLinkAdmin() {
        $messageAttachment = $this->attachments()->first();
        if($messageAttachment) {
            return route('backend-downloadAttachment', ['MessageAttachment'=>$messageAttachment->id, 'filename'=>$messageAttachment->filename]);
        }
        return "";
    }

    public function attachmentTitle() {
        $messageAttachment = $this->attachments()->first();
        if($messageAttachment) {
            return $messageAttachment->filename;
        }
        return "";
    }

    

}
