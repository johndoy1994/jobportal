<ul class="list-group">
	@foreach($myConversations as $myConversation)
		<li class="list-group-item"><a title="{{$myConversation->getConversationInfo()}}" href="{{route('frontend-conversation', ['conversation_ref'=>$myConversation->conversation_ref])}}">{{ucwords($myConversation->getConversationTitle())}}</a></li>
	@endforeach
</ul>
