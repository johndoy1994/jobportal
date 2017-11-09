@extends('layouts.backend')

@section('title', 'Notifications')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Notifications {{$Usertype}}</h3>
			</div>
		</div>
		<hr/>
	@include('includes.backend.request_messages')
    @include('includes.backend.validation_errors')
		<div class="row">
			{{Form::open(array('method' => 'get','class'=>''))}}	
	        	<div class="form-group pull-right" style="width:200px;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="search" id="search" value="{{Request::get('search')}}" />
            			<input type="hidden" class="form-control" name="Usertype" id="Usertype" value="{{Request::get('Usertype')}}" />
            			<span class="input-group-btn">
            				<button type="submit" class="btn btn-default">Search</button>
            			</span>
            		</div>
	            </div>
	        {{Form::close()}}
	        <div class="col-lg-12">
	        	@if($isRequestSearch)
					<div class="pull-right">
						<a href="{{route('backend-notifications',['Usertype'=>Request::get('Usertype')])}}">Reset Search </a>	
					</div>
					<br>
				@endif
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    Notifications List
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
	                        <table class="table table-striped table-hover">
	                            <thead>
	                                <tr>
	                                    <th>Photo</th>
	                                    <th>Name </th>
	                                    <th>Action</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                            
	                            @if(count($myConversations) > 0)
									@foreach($myConversations as $myConversation)
	                                    
                                            <tr>
    	                                        <td><img src="{{route('account-avatar-100x100', ['id'=>$myConversation->id])}}"  alt="..." style="width:40px; height:40px;"></td>
    	                                        <td title="{{$myConversation->getConversationInfo()}}">{{$myConversation->name}} @if(isset($myConversation->messagecount) && $myConversation->messagecount > 0) <span class="badge" style="display: inline;  background:red; font-weight:bold;">{{$myConversation->messagecount}}</span> @endif</td>
    	                                        <td>
    	                                        <a href="" data-toggle="modal" data-target="#myModal1" data-id="{{$myConversation->id}}" class="btn btn-sm btn-primary msg" role="button">Send</a>
    	                                        @if($myConversation->is_viewConversation)		
    	                                        	<a href="" data-toggle="modal" id="btn-conversation" role="toggle-notification-message"  data-reciever="{{$myConversation->id}}" data-usertype="{{Request::get('Usertype')}}" data-target="#myModal" data-conversationref="{{$myConversation->conversation_ref}}" data-email="" class="btn btn-sm btn-danger">View</a>
    	                                        @endif	
    	                                        </td>
    	                                    </tr>
                                       
	                                @endforeach
	                            @else
	                            <tr>
									<td colspan="3" class="text-center">No record(s) found.</td>
								</tr>
	                            @endif  
	                            @if(count($myConversations) > 0)
									<tr>
										<td colspan=3 class="text-center">
										{{$myConversations->appends(['Usertype'=>Request::get('Usertype'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
										</td>
									</tr>
								@endif  
	                            </tbody>
	                        </table>
	                    </div>
	                </div>
	            </div>
	        </div>
		</div>
	</div>
	<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Conversation</h4>
                </div>
                <div class="modal-body">
                <div id="conversation-div" style="height:300px; overflow-y:scroll; padding:10px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Message</h4>
                </div>
                <div class="modal-body">
                    <div class="alert ajax-message-status" hidden=""></div>
                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                    	<div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">File :</label>
                            <div class="col-sm-9">
                            	<input type="file" name="file" id="file">
                            </div>
                        </div>
                    	<div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">Message :</label>
                            <div class="col-sm-9">
                            	<input type="hidden" name="receiverId" id="receiverId" value="">
                            	<textarea type="text" class="form-control" name="message" id="message" rows="5" placeholder="Enter message..." required=""></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <img src="{{asset('/imgs/spin.gif')}}" style="height:auto; width:40px; display:none;" id="spinner_message" />
                    <button type="button" id="btn-chat" class="btn btn-primary">Send</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection
@push('footer')
<script type="text/javascript">

$(document).on('click', '.msg', function() {
	$(".ajax-message-status").hide();
	$('#message').val('');
	$('#file').val('');
	$('#receiverId').val($(this).data('id'));
});
$(document).on('click', '#btn-conversation', function() {
    var conversationref=$(this).data('conversationref');
    var reciever=$(this).data('reciever');
    var Usertype=$(this).data('usertype');
    
		$.ajax({
            dataType:'html',
            type:'get',
            url:  "{{route('api-get-notificationConversation')}}",
            data: {'conversationref' : conversationref, 'Usertype': Usertype,'reciever':reciever,'is_messages':1},
            success: function(data){
            	$('#conversation-div').html(data);
                $("#conversation-div").scrollTop($("#conversation-div").prop('scrollHeight'));
            }
        });
});
$(document).on('click', '#btn-chat', function() {
	var message = $('#message').val();
	var receiverId=$('#receiverId').val();
	var file = $("input[name='file']").prop("files")[0];
    $(".ajax-message-status").hide();
    $(".ajax-message-status").removeClass("alert-success");
    $(".ajax-message-status").removeClass("alert-danger");
    if(message!=''){
        $("#message").parent().parent().removeClass("has-error");

        var fd = new FormData();
        fd.append("message", message);
        fd.append("receiverId", receiverId);
        fd.append("type", 3);
        fd.append("is_message", 1);
        fd.append("_token", "{{csrf_token()}}");
        fd.append("file", file);
        $('#btn-chat').hide();
        $('#spinner_message').show();
        $.ajax({
            processData: false,
            contentType: false,
            type:'post',
            url:  "{{route('api-messages-newmessage')}}",
            data: fd,
            success: function(data){
                $('#btn-chat').show();
                $('#spinner_message').hide();
                $(".ajax-message-status").show();
                $('#message').val('');
                $('#file').val('');
                if(data[0]) {
                	$(".ajax-message-status").addClass("alert-success");
		    		$(".ajax-message-status").html("Message send successfully...");
		    	} else {
		    		$(".ajax-message-status").addClass("alert-danger");
		    		$(".ajax-message-status").html('There was an error while send your message, try again');
		    	}
            }
        });
    }else{
        $(".ajax-message-status").show();
    	$(".ajax-message-status").addClass("alert-danger");
    	$(".ajax-message-status").html('Please enter message..');

    }
});

</script>
@endpush