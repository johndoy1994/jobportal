<ul class="list-group">
	@foreach($myConversations as $myConversation)
		<li class="list-group-item"><a title="{{$myConversation->getConversationInfo()}}" href="{{route('backend-conversation', array_merge(['conversation_ref'=>$myConversation->conversation_ref],['reciever'=>$myConversation->id],['Usertype'=>$Usertype]))}}">{{ucwords($myConversation->name)}}</a></li>
	@endforeach
</ul>
