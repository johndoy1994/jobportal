@extends('layouts.recruiter')

@section('title', 'CV Detail')

@push('head')
	<meta property="og:image" content="{{route('account-avatar-100x100', ['id'=>($cv)? $cv->id : ""])}}"/>
	<meta property="og:title" content="{{($cv)?$cv->getName():""}}"/>
	<meta property="og:url" content=""/>
	<meta property="og:description" content=""/>
@endpush
@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.request_messages')
				@include('includes.frontend.validation_errors')
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<a href="{{$cvSearchUri($filters)}}" class="btn btn-default">< Back to search results</a>
			</div>
			<div class="col-md-6 text-right">
				@if(isset($prevCv))
					<a href="{{$cvSearchUri($filters, ['id'=>$prevCv->id], 'recruiter-cv-detail')}}" class="btn btn-default">< Previous CV</a>
				@endif
				@if(isset($nextCv))
					<a href="{{$cvSearchUri($filters, ['id'=>$nextCv->id], 'recruiter-cv-detail')}}" class="btn btn-default">Next CV ></a>
				@endif
			</div>
		</div>
		@if($cv)		
			<br/>

			<div class="well">
				<div class="row">
					<div class="col-md-12">
						<h3>{{$cv->getName()}}</h3>
					</div>
					<div class="col-md-8">
						<ul class="list-unstyled">
							<li><b>Full Name :</b>  {{$cv->getPersonTitle()}} {{$cv->getName()}}</li>
							<li><b>Education :</b> {{$cv->getHighestEducationString()}}</li>
							<li><b>Recent Job :</b> {{$cv->getRecentJobTitle()}} ({{$cv->job_title}})</li>
							<li><b>Experience :</b> {{$cv->getExperienceName()}} ({{$cv->getExperienceLevelName()}})</li>
							<li><b>Job Type :</b> {{$cv->getJobTypeString()}}</li>
							<li><b>Job Category :</b> {{$cv->job_category_name}}</li>
							<li><b>Salary : </b> {{$cv->getDesiredSalaryString()}}</li>
						</ul>
					</div>
					<div class="col-md-4 text-center">
						<img src="{{route('account-avatar-100x100', ['id'=>$cv->id])}}" style="vertical-align:bottom" />
					</div>
					<div class="col-md-12">
						@if($cv->isUserValidForConversation($cv->UserChatgetRef($cv->id),'recruiter'))
							<a href="{{route('recruiter-conversation',['conversation_ref'=>$cv->UserChatgetRef($cv->id)])}}" class="btn btn-primary">Chat</a>
						@endif
						<a href="" data-toggle="modal" data-target="#myModal" data-id="{{$cv->id}}" class="btn btn-success msg">Msg</a>
						@if($cv->mobile_number)
							<a href="tel:{{$cv->mobile_number}}" class="btn btn-warning">Call</a>
						@endif	
						@if($cv->email_address)
							<a href="" data-toggle="modal" data-target="#myModal1" data-id="{{$cv->id}}" data-email="{{$cv->email_address}}" class="btn btn-info btn-email">Email</a>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">	
						<br/>
						<div class="well well-sm">
							<b>Certificates:</b><br/>{{$cv->getCertificatesLine()}}<br/>
							<br/><b>Skills:</b><br/>{{$cv->getSkillsLine()}}<br/>
							<br/><b>Residance Detail:</b><br/>{{$cv->getResidanceAddress()}}<br/>
							<br/><b>Desired Location(s):</b><br/>
								<ul class="list-unstyled">
									@foreach($cv->getDesiredLocations() as $desiredLocation)
										<li>{{$desiredLocation}}</li>
									@endforeach
								</ul>
							<br/><b>About:</b><br/>{{$cv->getAboutMe()}}
						</div>
						@if($cv->showContactDetails())
							<br/>
							<div class="well well-sm">
								<b>Contact Details:</b><br/>
								E-mail : <a href="">{{$cv->email_address}}</a><br/>
								@if($cv->mobile_number > 0)
									Mobile : <a href="">{{$cv->mobile_number}}</a>
								@endif
							</div>
						@endif
					</div>
				</div>
			</div>

		@else
			<br/>
			<div class="row">
				<div class="col-md-12">
					<div class="well text-center">
						Sorry, we didn't found anything, please try again.<br/>
						<a href="{{$cvSearchUri($filters, [ 'id'=>null ])}}">Go Back</a>
					</div>
				</div>
			</div>
		@endif
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
                            	<input type="text" class="form-control" name="subject" placeholder="Enter subject..." id="subject" value="">
                            </div>
                        </div>
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
$(document).on('click', '.msg', function() {
	$(".ajax-message-status").hide();
	$('#message').val('');
	$('#file').val('');
	$('#receiverId').val($(this).data('id'));
});

$(document).on('click', '.btn-email', function() {
    $(".ajax-email-status").hide();
    $('#content').val('');
    $('#subject').val('');
    $('#userEmail').val($(this).data('email'));
    $('#receiverId').val($(this).data('id')); 
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
    var receiverId=$('#receiverId').val();
    var subject=$('#subject').val();
    $(".ajax-email-status").hide();
    $(".ajax-email-status").removeClass("alert-success");
    $(".ajax-email-status").removeClass("alert-danger");
    if(content!='' && subject!=''){
        var fd = new FormData();
        fd.append("login", 'recruiter');
        fd.append("content", content);
        fd.append("subject", subject);
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
                $('#subject').val('');
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