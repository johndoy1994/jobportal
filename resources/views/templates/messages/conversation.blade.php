@foreach($conversationMessages as $conversationMessage)
    @if($conversationMessage["isRight"])
    	<li class="right clearfix conversation-msg" id="msg{{$conversationMessage['msgObject']->id}}">
            <span class="chat-img pull-right">
                <img src="{{route('account-avatar-100x100',['id'=>$conversationMessage['imgId']])}}" alt="User Avatar" class="img-circle" style="background: #333; width: 50px; height:50px" />
            </span>
            <div class="chat-body clearfix">
                <div class="header">
                    <small class=" text-muted">
                        <i class="fa fa-clock-o fa-fw"></i> {{$conversationMessage["time"]}}</small>
                    <strong class="pull-right primary-font">{{$conversationMessage["senderName"]}} @if($conversationMessage["readed"]==0) <label class="label label-success">new</label> @endif</strong>
                </div>
                <p class="">
                    <pre class="">{{$conversationMessage["message"]}}</pre>
                    @if($conversationMessage["msgObject"]->attachments)
                        @if($conversationMessage["msgObject"]->isAttachment())
                            @if($type==1)
                                <a class="" href="{{$conversationMessage['msgObject']->attachmentLink()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a>
                            @elseif($type==2)
                                <a class="" href="{{$conversationMessage['msgObject']->attachmentLinkRecruiter()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a>
                            @else    
                                <a class="" href="{{$conversationMessage['msgObject']->attachmentLinkAdmin()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a>
                            @endif             
                        @endif    
                    @endif
                </p>
            </div>
        </li>
    @else
    	<li class="left clearfix conversation-msg" id="msg{{$conversationMessage['msgObject']->id}}">
            <span class="chat-img pull-left">
                <img src="{{route('account-avatar-100x100',['id'=>$conversationMessage['imgId']])}}" alt="User Avatar" class="img-circle" style="background: #333; width: 50px; height:50px" />
            </span>
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font">{{$conversationMessage["senderName"]}} @if($conversationMessage["readed"]==0) <label class="label label-success">new</label> @endif</strong>
                    <small class="pull-right text-muted">
                        <i class="fa fa-clock-o fa-fw"></i> {{$conversationMessage["time"]}}
                    </small>
                </div>
                <p class="pull-left">
                    <pre>{{$conversationMessage["message"]}}</pre>
                    @if($conversationMessage["msgObject"]->attachments)
                        @if($conversationMessage["msgObject"]->isAttachment())
                            @if($type==1)
                                <a class="pull-left" href="{{$conversationMessage['msgObject']->attachmentLink()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a>
                            @elseif($type==2)
                                <a class="pull-left" href="{{$conversationMessage['msgObject']->attachmentLinkRecruiter()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a>
                            @else    
                                <a class="pull-left" href="{{$conversationMessage['msgObject']->attachmentLinkAdmin()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a>
                            @endif 
                            <!-- <a class="pull-left" href="{{$conversationMessage['msgObject']->attachmentLink()}}"><img src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$conversationMessage["msgObject"]->attachmentTitle()}}</a> -->
                        @endif
                    @endif
                </p>
            </div>
        </li>
    @endif
@endforeach