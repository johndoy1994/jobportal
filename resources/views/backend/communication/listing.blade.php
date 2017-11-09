@extends('layouts.backend')

@section('title', 'Communication')

@section('content')
<style type="text/css">
	.multiselect-container {
		height: 200px;
		overflow-y: scroll;
	}
	.margin10{
		margin-right:10px;
	}
</style>
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-12">
				<h3>Communication</h3>
			</div>
			
		</div>
		<hr/>

			<div class="col-md-12">
				<div class="col-md-6">
					<div class="well">
					    <b>Job Seeker:</b>
						<select class="form-control" id="jobseeker" name="user[]" multiple="multiple">
							@foreach($Users as $user)
								@if($user->type=='JOB_SEEKER')
									<option value="{{$user->id}}">{{$user->name}}</option>
								@endif	
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="well">
					    <b>Employer:</b>
						<select class="form-control" id="employer" name="user[]" multiple="multiple">
							@foreach($Users as $user)
								@if($user->type=='EMPLOYER')
									<option value="{{$user->id}}">{{$user->name}}</option>
								@endif	
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-12 text-center">
				<a href="" data-toggle="modal" data-target="#myModal1" data-id="" data-email="" class="btn btn-info btn-email margin10">Email</a>
				<a href="" data-toggle="modal" data-target="#myModal" data-id="" class="btn btn-success msg margin10">Msg</a>
				<a href="" data-toggle="modal" data-target="#myModal" data-id="" class="btn btn-primary margin10 chatMul">Chat</a>
			</div>
		</div>


<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                            	<input type="hidden" name="ismessage" id="ismessage" value="">
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

    <!-- Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Email</h4>
                </div>
                <div class="modal-body">
                    <div class="alert ajax-email-status" hidden=""></div>
                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        <div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">Content :</label>
                            <div class="col-sm-9">
                            	<input type="hidden" name="receiverId" id="receiverId" value="">
                                <textarea type="text" class="form-control" name="content" id="content" rows="5" placeholder="Enter content..." required=""></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <img src="{{asset('/imgs/spin.gif')}}" style="height:auto; width:40px; display:none;" id="spinner" />
                    <button type="button" id="btn-content" class="btn btn-primary">Send</button>
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

$(document).ready(function() {
       $('#jobseeker').multiselect({ 
         includeSelectAllOption: true,
           enableFiltering:true,
           enableCaseInsensitiveFiltering : true         
    	});
       $('#employer').multiselect({ 
         includeSelectAllOption: true,
           enableFiltering:true,
           enableCaseInsensitiveFiltering : true         
           
     });
     
});
$(document).on('click', '.msg', function() {
	$(".ajax-message-status").hide();
	$('#message').val('');
	$('#ismessage').val(1);
	$('#file').val('');
	var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });
    $('#receiverId').val(val);    
});
$(document).on('click', '.chatMul', function() {
	$(".ajax-message-status").hide();
	$('#message').val('');
	$('#ismessage').val(0);
	$('#file').val('');
	var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });
    $('#receiverId').val(val);    
});

$(document).on('click', '.btn-email', function() {
    $(".ajax-email-status").hide();
    $('#content').val('');
    var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });
    $('#receiverId').val(val); 
});

$(document).on('click', '#btn-chat', function() {
	var message = $('#message').val();
	var receiverId=$('#receiverId').val();
	var is_message=$('#ismessage').val();
    
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
        fd.append("is_message", is_message);
        fd.append("_token", "{{csrf_token()}}");
        fd.append("file", file);
        $('#btn-chat').hide();
        $('#spinner_message').show();
		$.ajax({
            processData: false,
            contentType: false,
            type:'post',
            url:  "{{route('api-messages-multiplenewmessage')}}",
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

$(document).on('click', '#btn-content', function() {
    var content = $('#content').val();
    var receiverId=$('#receiverId').val();
    $(".ajax-email-status").hide();
    $(".ajax-email-status").removeClass("alert-success");
    $(".ajax-email-status").removeClass("alert-danger");
    if(content!=''){
        var fd = new FormData();
        fd.append("content", content);
        fd.append("receiverId", receiverId);
        fd.append("_token", "{{csrf_token()}}");
        $('#btn-content').hide();
        $('#spinner').show();
        $.ajax({
            processData: false,
            contentType: false,
            type:'post',
            url:  "{{route('api-email-contentmultiuser')}}",
            data: fd,
            success: function(data){
                $('#btn-content').show();
                $('#spinner').hide();
                $(".ajax-email-status").show();
                $('#content').val('');
                if(data[0]) {
                    $(".ajax-email-status").addClass("alert-success");
                    $(".ajax-email-status").html("email send successfully...");
                } else {
                    $(".ajax-email-status").addClass("alert-danger");
                    $(".ajax-email-status").html('There was an error while send your message, try again');
                }
            }
        });
    }else{
        $(".ajax-email-status").show();
        $(".ajax-email-status").addClass("alert-danger");
        $(".ajax-email-status").html('Please enter content..');

    }
});
</script>
@endpush