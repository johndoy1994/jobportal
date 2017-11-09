@extends('layouts.frontend')

@section('title', 'Conversation View')

@section('content')
	<div class="container">
		<div class="row padding-top-10">
            <div class="col-md-9">
                <h3>Conversation with {{$conversationTitle}}</h3>
            </div>
            <div class="col-md-3 text-right padding-top-10">
                <a href="{{route('frontend-message',array_merge(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder'),'page'=>Request::get('page')]) )}}" class="btn btn-primary pull-right">Back</a>
            </div>
        </div>
        <hr/>
        @include('includes.frontend.request_messages')
        @include('includes.frontend.validation_errors')
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.messages.sidebar')
			</div>
            <div class="col-md-9 col-sm-12">
				<div class="chat-panel panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-comments fa-fw"></i>
                        Chat log
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                Options <i class="glyphicon glyphicon-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu slidedown">
                                <li>
                                    <a href="" onClick="printReceipt('printableArea')">
                                        <i class="glyphicon glyphicon-print" ></i> Print
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body" id="printableArea">
                        <ul class="chat" role="autochat" data-type="1" data-ref="{{$conversation_ref}}">
                           @include('templates.messages.conversation')
                        </ul>
                    </div>
                    <!-- /.panel-body -->
                    <div class="panel-footer">
                        <div class="input-group col-md-12">
                            <div class="col-md-1">
                                <img src="{{asset('/imgs/attach.png')}}" style="width: 30px; height:30px" onclick="openFileOption();"/>    
                                <input type="file" name="file" id="file" style="display:none">
                            </div>
                            <div class="col-md-10">
                                <textarea id="message" type="text" class="form-control input-sm" placeholder="Type your message here..." rows="1" cols="100"></textarea>
                             </div>
                             <div class="col-md-1">
                                <span class="input-group-btn">
                                    <button class="btn btn-warning btn-sm" id="btn-chat">
                                        Send
                                    </button>
                                </span>
                            </div>
                            <label id="file_text"></label>
                        </div>
                    </div>
                    <!-- /.panel-footer -->
                </div>
                <!-- /.panel .chat-panel -->
			</div>
		</div>
	</div>
@endsection
@push('footer')
<script>
function printReceipt(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>
<script>
  function openFileOption()
{
  document.getElementById("file").click();
}
</script>
<script type="text/javascript">
    $(document).on('change', '#file', function() {

        $('#file_text').html($(this).val());
    });
	$(document).ready(function() {
		$(".chat-panel .panel-body").scrollTop($(".chat-panel .panel-body").prop('scrollHeight'));
		var conversation_ref={{$conversation_ref}};
		viewmessageStatusUpdate(conversation_ref);
	});
	$(document).on('click', '#btn-chat', function() {
			var message = $('#message').val();
			var receiverId={{$conversationId}};
            var file = $("input[name='file']").prop("files")[0];
            
            if(message!=''){
                $("#message").parent().parent().removeClass("has-error");

                var fd = new FormData();
                fd.append("message", message);
                fd.append("receiverId", receiverId);
                fd.append("type", 1);
                fd.append("_token", "{{csrf_token()}}");
                fd.append("file", file);

                $.ajax({
                    processData: false,
                    contentType: false,
                    type:'post',
                    url:  "{{route('api-messages-newmessage')}}",
                    //data: {'message' : message ,'receiverId' : receiverId, 'type':1 ,'_token': "{{csrf_token()}}"},
                    data: fd,
                    success: function(data){
                        $('#message').val('');
                        $('#file').val('');
                        $('#file_text').html('');
                        if(data[0]){
                            $('.chat').append(data[1]);
                        	var top = $("#msg"+data[2]).offset().top;
                        	var currentTop = $(".chat-panel .panel-body").scrollTop();
                        	$('.chat-panel .panel-body').scrollTop(currentTop + top);
                        }

                    }
                });

            }else{
                $("#message").parent().parent().addClass('has-error');
            }
        });

	function viewmessageStatusUpdate($conversation_ref){
		
		$.ajax({
                type:'post',
                url:  "{{route('api-messages-statusUpdate')}}",
                data: {'conversation_ref' : $conversation_ref,'type':1,'_token': "{{csrf_token()}}"},
                success: function(data){
                }
            });
	}
</script>
@endpush