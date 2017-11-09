@extends('layouts.recruiter')

@section('title', 'Candidates')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.recruiter.validation_errors')
				@include('includes.recruiter.request_messages')
					@foreach($jobs as $key=>$val)
						<?php $results=$candidate[$key]; ?>
							<div class="well well-sm">
							<div class="panel panel-default">
								<div class="panel-heading">
									<span class="pull-right">Total : {{count($results)}}</span>
									<h3 class="panel-title">Candidate for <b>{{$val['title']}}</b></h3>
								</div>
							</div>
								<div class="row">
									<div class="col-md-12">
									</div>
								</div>
								@include('includes.recruiter.posted-jobs.application-details')
							</div>
					@endforeach
			</div>
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
                            <label for="inputTask" class="col-sm-3 control-label">Email :</label>
                            <div class="col-sm-9">
                            <input type="hidden" name="applicationId" id="applicationId" value="">
                                <input type="text" class="form-control" readonly="" name="userEmail" id="userEmail" value="">
                            </div>
                        </div>
                        <div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">Subject :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="subject" id="subject" value="" placeholder="Enter subject..." required="">
                            </div>
                        </div>
                        <div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">Content :</label>
                            <div class="col-sm-9">
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
    $("a[role='toggle-job-details']").click(function() {
        var target=$(this).attr('data-target');
        $(target).toggle();
        return false;
    });
});
$(document).on('click', '.msg', function() {
	$(".ajax-message-status").hide();
	$('#message').val('');
	$('#file').val('');
	$('#receiverId').val($(this).data('id'));
});

$(document).on('click', '.btn-email', function() {
    $(".ajax-email-status").hide();
    $('#content').val('');
    $('#userEmail').val($(this).data('email'));
    $('#applicationId').val($(this).data('id'));
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
        fd.append("type", 2);
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

$(document).on('click', '#btn-content', function() {
    var content = $('#content').val();
    var email=$('#userEmail').val();
    var applicationId=$('#applicationId').val();
    var subject=$('#subject').val();
    $(".ajax-email-status").hide();
    $(".ajax-email-status").removeClass("alert-success");
    $(".ajax-email-status").removeClass("alert-danger");
    if(content!='' && subject!=''){
        var fd = new FormData();
        fd.append("content", content);
        fd.append("email", userEmail);
        fd.append("subject", subject);
        fd.append("applicationId", applicationId);
        fd.append("_token", "{{csrf_token()}}");
        $('#btn-content').hide();
        $('#spinner').show();
        $.ajax({
            processData: false,
            contentType: false,
            type:'post',
            url:  "{{route('api-email-content')}}",
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
        
        if(content=='' && subject==''){
            $(".ajax-email-status").html('Please enter content and subjects..');
        }else if(content==''){
            $(".ajax-email-status").html('Please enter content..');
        }else{
            $(".ajax-email-status").html('Please enter subjects..');
        }

    }
});

</script>
@endpush