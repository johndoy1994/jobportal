@extends('layouts.recruiter')

@section('title', 'Candidates')

@section('content')
<style type="text/css">
    .calendar-day-selectable1{
        background: none repeat scroll 0 0 #EAC697 !important;
    }

    .fc-today {
        background: none repeat scroll 0 0 #64dcf4 !important;
    }
    .fc-day, .fc-head-container.fc-widget-header, .fc-view.fc-month-view.fc-basic-view{
        border:1px solid #ccc !important;
    }
    .fc-head-container.fc-widget-header
    {
        font-size:15px;
        font-weight: bold;
        color:#333333;
    }
    .fc-day-number {
       text-align: left!important;
       font-size:15px;
       color:#333333;
    }
    .seeker-day-count{
        bottom: 0;
        float: right;
        font-size: 20px;
        font-weight: bold;
        margin-left: 77px;
        position: absolute;
        color: #C1E318;

    }
    .today{
        background: #64dcf4 !important;
    }
    .seeker-day-background1{
        background: linear-gradient(135deg, #EAC697 20px, transparent 0px, transparent) repeat scroll 0 0 #5a9c5a;
    }


    .seeker-day-background {
          /*background: linear-gradient(135deg, red 15px, transparent 0, transparent);*/
          background: linear-gradient(140deg, #EAC697 65px, transparent 67px, transparent) repeat scroll 0 0 #5a9c5a;
    }
</style>
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

			@include('includes.recruiter.validation_errors')
			@include('includes.recruiter.request_messages')
			    <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <h4>Candidate Details</h4>
                    </div>

                    <div class="panel-body">
                        <h3>{{$UserData->name}}</h3>
                        <?php
                            $status = $UserData->getMatchingStatus($UserData,$Job);
                            echo "<span class='glyphicon glyphicon-saved' aria-hidden='true'></span> Basic match : <label class='label label-primary'>".$status[1]."/".$status[0]."</label><br/>";
                            echo "<span class='glyphicon glyphicon-saved' aria-hidden='true'></span> Requirement match : <label class='label label-success'>".$status[3]."/".$status[2]."</label>";
                        ?>
                        <hr>
                        
                        <b>Phone :</b> {{($UserData->mobile_number)?$UserData->mobile_number:"N/A"}}<br>
                        <b>Email :</b> {{($UserData->email_address)?$UserData->email_address:"N/A"}}<br>
                        <b>Address :</b> {{($UserData->getResidanceAddress())?$UserData->getResidanceAddress():"N/A"}} <br>
                        <b>City :</b> @if(isset($UserData->addresses[0])){{$UserData->addresses[0]->getCityName()}} @else N/A @endif<br>
                        <b>State :</b>@if(isset($UserData->addresses[0])) {{$UserData->addresses[0]->getStateName()}} @else N/A @endif<br>
                        <b>Country :</b>@if(isset($UserData->addresses[0])) {{$UserData->addresses[0]->getCountryName()}} @else N/A @endif<br>
                        <b>Postal Code :</b>@if(isset($UserData->addresses[0])) {{$UserData->addresses[0]->postal_code}} @else N/A @endif<br>
                        <hr>
                        <b>Education :</b> {{($UserData->getEducationName())? $UserData->getEducationName() : 'N/A'}}<br>
                        <b>Recent Job Title :</b>{{($UserData->getRecentJobTitle())? $UserData->getRecentJobTitle() : 'N/A'}} <br>
                        <b>Experience :</b> {{($UserData->getExperienceName())? $UserData->getExperienceName() : 'N/A'}}<br>
                        <b>Experience Level :</b> {{($UserData->getExperienceLevelName())? $UserData->getExperienceLevelName() : 'N/A'}}<br>
                        <b>Current Salary :</b> {{($UserData->getCurrentSalaryString())? $UserData->getCurrentSalaryString() : 'N/A'}}<br>
                        <b>Certificates :</b> {{(rtrim($UserData->getCertificatesLine(),", "))? rtrim($UserData->getCertificatesLine(),", ") : "N/A"}}<br>
                        <b>Skills :</b> {{(rtrim($UserData->getSkillsLine(),", ")) ? rtrim($UserData->getSkillsLine(),", ") : "N/A"}}<br>
                        <b>Desired Job Title :</b> {{($UserData->getDesiredJobTitleName()) ? $UserData->getDesiredJobTitleName() : 'N/A' }}<br>
                        <b>Desired Salary :</b> {{($UserData->getDesiredSalaryString()) ? $UserData->getDesiredSalaryString() : 'N/A'}}<br>
                        <hr>
                        <b>About Me :</b> @if($UserData->profile){{($UserData->profile->about_me) ? $UserData->profile->about_me : "N/A"}} @else 'N/A' @endif<br>
                        <hr>
                        @if($jobDetails->day_selection==1)
                        <?php $arrayDays=array(); 
                        ?>
                        @foreach($jobDetails->applications as $applicationdata)
                            @if($view=='candidate')
                                <?php $statusCal='accepted'; ?>
                            @else
                                <?php $statusCal='in-process'; ?>
                            @endif

                            @if($applicationdata->status==$statusCal)
                                @if($applicationdata->user_id==$UserData->id)
                                    <?php $data= $jobDetails->dayCountJsondecode($applicationdata['meta']); 
                                        if(isset($data['days'])){
                                            foreach ($data['days'] as  $value) {
                                                array_push($arrayDays, $value);
                                            }
                                        }
                                    ?> 
                                @endif
                            @endif                                 
                        @endforeach
                        <?php $countsDays=array_count_values($arrayDays); ?>
                        <select name="dates[]" multiple class="hide" >
                            @if(isset($jobDetails->jMeta()['days']))
                                @foreach($jobDetails->jMeta()['days'] as $date)
                                    <option value="{{$date}}" data-count="{{isset($countsDays[$date])? $countsDays[$date] : 0 }}" selected>{{$date}}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="panel panel-default">
                            <div id="job-details-cal" class="panel-heading" style="padding:5px;">
                                <h4><a href="">Availability Calender</a></h4>
                            </div>

                            <div id="job-details-cal-toggle" class="panel-body">
                                <div id="calendar" style="background:white!important"></div>
                                </br>
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="today"></div>
                                        ToDay
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="calendar-day-selectable1"></div>
                                        Recruiter selected Days
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="seeker-day-background1"></div>
                                        Applicant selected days count
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                        <hr>
                        <div class="text-center">
                            <h4>Job Match Status</h4>    
                        </div>
                        <br>

                        <div class="form-group">
                            <label class="control-label col-lg-4">Select a job to view match status :</label>
                            <div class="col-lg-7">
                                <select class="form-control" id="job_id" name="job_id" onchange="getStatusMatch();">
                                    @foreach($postedJobs as $postedJob)
                                        <option value="{{$postedJob->id}}" {{($Job->id==$postedJob->id)?"selected":""}}>Job ID : {{$postedJob->id.' '.$postedJob->getExperienceLevelString().' '.$postedJob->title}}</option>
                                    @endforeach    
                                </select>
                            </div>
                        </div>
                        <br>
                        <div id="match-status"></div>
                        </br>
                        <div class="col-md-12 text-center">
                            <a href="{{route('recruiter-conversation',['conversation_ref'=>$UserData->UserChatgetRef($UserData->id)])}}" class="btn btn-primary">Chat</a>
                            <a href="" data-toggle="modal" data-target="#myModal" data-id="{{$UserData->id}}" class="btn btn-success msg">Msg</a>
                            @if($UserData->mobile_number)
                                <a href="tel:{{$UserData->mobile_number}}" class="btn btn-warning">Call</a>
                            @endif  
                            @if($UserData->email_address)
                            <? $applicationData=$UserData->getUserApplication($UserData,$Job);?>
                                <a href="" data-toggle="modal" data-target="#myModal1" data-id="{{$applicationData->id}}" data-email="{{$UserData->email_address}}" class="btn btn-info btn-email">Email</a>
                                
                            @endif
                        
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
$(document).ready(function(){
    getStatusMatch();
    var select = "select[name='dates[]']";
    $(".panel-body #calendar").fullCalendar({
        dayRender: function(date, cell) {
            var todayFormat = "{{\Carbon\Carbon::now()->format('Y-m-d')}}";
            var dateFormat = date.format('Y-MM-DD');
            if($(select+" option[value='"+dateFormat+"']").text()) {
                if($(select+" option[value='"+dateFormat+"']").data('count') > 0){
                    cell.addClass('seeker-day-background'); 
                    // var url = "{{route('recruiter-postedjob-details',array_merge(['job'=>$jobDetails->id,'date'=>'dateval']))}}";
                    // url = url.replace('dateval', dateFormat);
                    // cell.html("<label class='seeker-day-count'><a  href='"+url+"'>"+$(select+" option[value='"+dateFormat+"']").data('count')+"</a></lable>");
                    
                }else{
                    cell.addClass('calendar-day-selectable1');  
                }
            }
        }
    });
    $(".panel-body #calendar").fullCalendar('render');
});
$("#job-details-cal").click(function() {
        $("#job-details-cal-toggle").toggle();
        return false;
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
        fd.append("subject", subject);
        fd.append("email", userEmail);
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
        //$(".ajax-email-status").html('Please enter content..');
        if(content=='' && subject==''){
            $(".ajax-email-status").html('Please enter content and subjects..');
        }else if(content==''){
            $(".ajax-email-status").html('Please enter content..');
        }else{
            $(".ajax-email-status").html('Please enter subjects..');
        }

    }
});
function getStatusMatch(){
    var UserId={{$UserData->id}};
    var JobId=$('#job_id option:selected').val();
    
    $.ajax({
            dataType:'html',
            type:'get',
            url:  "{{route('api-job-matchstatus')}}",
            data: {'UserId' : UserId,'JobId':JobId},
            success: function(data){
               $('#match-status').html(data);
            }
        });
}
</script>
@endpush