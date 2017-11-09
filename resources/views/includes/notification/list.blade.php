<div class="row">
	<div class="col-md-12">
		@foreach($results as $result)
		<?php $messages=$result->getLastMessage($result->conversation_ref); ?>
			<div class="panel">
				<div class="panel-heading clearfix" style="border-bottom: 1px solid #e1e1e1">
					<div class="pull-left">
						
						<h3><input id="Messagemultiple" class="selectallcol1" type="checkbox" value="{{$result->conversation_ref}}" name="Messagemultiple[]">
						{{$result->conversation_title}} {{$result->getConversationInfo()}}
						@if($result->getNotificationCount($user) > 0) <span class="badge" style="background:red; font-weight:bold;">{{$result->getNotificationCount($user)}}</span> @endif
						</h3>

					</div>
					<div class="pull-right">
						@if($messages->sender==$user->id)
							<img src="{{asset('/imgs/sender.png')}}" style="width: 50px; height:50px"/> {{$messages->created_at->diffForHumans()}}
						@else
							<img src="{{asset('/imgs/reciever.jpeg')}}" style="width: 50px; height:50px"/> {{$messages->created_at->diffForHumans()}}
						@endif
					</div>
				</div>
				<div class="panel-body">
					<div class="col-md-9 col-sm-12">
						<ul class="list-unstyled">
							<li>
								<span><b>Last Message :</b></span>
								{{$messages->message}}

							</li><br/>
							<li>
								@if($messages->attachments)
			                        @if($messages->isAttachment())
			                    		@if($user->type=="EMPLOYER")
			                    			<a class="" href="{{$messages->attachmentLinkRecruiter()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$messages->attachmentTitle()}}</a>	
			                    		@else
			                    			<a class="" href="{{$messages->attachmentLink()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$messages->attachmentTitle()}}</a>	
			                    		@endif
			                    	@endif    
			                    @endif
							</li>
							<br/><br/>
							<a href="" style="color:red;" role="toggle-notification-message" data-target="#notification-message-{{$result->conversation_ref}}">View Full Conversation</a>
							<br/>
						</ul>
					</div>
					<div class="col-md-3 col-sm-12 text-right">
						<img src="{{route('account-avatar', ['id'=>$result->conversation_id])}}" style="background: #333; width: 80px; height:80px"/>
					</div>

					<div class="row" id="notification-message-{{$result->conversation_ref}}" style="display:none">
						<div class="col-md-12">
							<div class="modal-body" id="applicantDetail">
								<div class="col-md-12">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4>Conversation</h4>
										</div>
										<div class="panel-body" style="height: 350px;overflow-y: scroll;">
											@foreach($result->getAllMessage($result->conversation_ref) as $val)
												@if($val['sender']!=$user->id)
													<div class="chat-body clearfix">
										                <div class="header">
										                    <strong class="primary-font">{{$result->conversation_title}} @if($val["readed"]==0) <label class="label label-success">new</label> @endif</strong>
										                    <small class="pull-right text-muted">
										                        <i class="fa fa-clock-o fa-fw"></i> {{$val['created_at']->diffForHumans()}}
										                    </small>
										                </div>
										                <p class="pull-left"></p>
										                	<pre>{{$val['message']}}</pre>
										                	@if($val->attachments)
										                        @if($val->isAttachment())
										                            @if($user->type=="EMPLOYER")
										                    			<a class="" href="{{$messages->attachmentLinkRecruiter()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$messages->attachmentTitle()}}</a>	
										                    		@else
										                    			<a class="" href="{{$messages->attachmentLink()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$messages->attachmentTitle()}}</a>	
										                    		@endif
										                        @endif    
										                    @endif
										            </div>
										            <hr>
									            @else
										            <div class="chat-body clearfix">
										                <div class="header">
										                    <small class=" text-muted">
										                        <i class="fa fa-clock-o fa-fw"></i>{{$val['created_at']->diffForHumans()}}</small>
										                    <strong class="pull-right primary-font">{{$user->name}}</strong>
										                </div>
										                <p class="text-right"></p>
										                <pre class="text-left">{{$val['message']}}</pre>
										                @if($val->attachments)
										                        @if($val->isAttachment())
										                            @if($user->type=="EMPLOYER")
										                    			<a class="" href="{{$messages->attachmentLinkRecruiter()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$messages->attachmentTitle()}}</a>	
										                    		@else
										                    			<a class="" href="{{$messages->attachmentLink()}}"><img class="pull-left" src="{{asset('/imgs/download-icon.png')}}" style="width: 20px; height:20px"/>{{$messages->attachmentTitle()}}</a>	
										                    		@endif
										                        @endif    
										                    @endif
										            </div>
										             <hr>	
									            @endif
									        @endforeach    	


								        </div>
									</div>
								</div>
							</div>			
						</div>
					</div>

					<div class="col-md-12">
						<a href="" data-toggle="modal" data-target="#myModal" data-id="{{$result->conversation_id}}" class="btn btn-success msg">Reply</a>
					</div>	
				</div>
			</div>
		@endforeach
	</div>
</div>	