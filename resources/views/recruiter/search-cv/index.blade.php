@extends('layouts.recruiter')

@section('title', 'Search CVs')

@section('content')
	<div class="container">
		<div class="page-header">
			<h1>{{$cvs->total()}} CV(s) found</h1>
			<small>Search CVs - select filters from left side bar...</small>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="well well-sm">
					<legend>Filters <a href="{{$cvSearchUri([])}}" class="pull-right">reset all</a></legend>
					<form>
					@foreach($filters as $key=>$value)
						@if($key!="keywords" && $key!="location")
							<input type='hidden' name='{{$key}}' value="{{$value}}" />
						@endif
					@endforeach
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">Who :</span>
							<input type="text" class="form-control" name="keywords" value="{{$filters["keywords"]}}" />
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">Update</button>
							</span>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">Where :</span>
							<input type="text" class="form-control" name="location" value="{{$filters["location"]}}" />
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">Update</button>
							</span>
						</div>
					</div>
					</form>

					<legend>Job Category <a href="{{$cvSearchUri($filters,['jobCategory'=>0])}}" class="pull-right"><small>reset</small></a></legend>
					<ul class="list-unstyled">
					@foreach($jobCategories as $jobCategory)
						<?php
						$jobCategoryCount = $cvSearchCount($filters, ['jobCategory'=>$jobCategory->id]);
						?>
						@if($jobCategoryCount > 0)
							@if($jobCategory->id == $filters["jobCategory"])	
								<li><b><a href="{{$cvSearchUri($filters,['jobCategory'=>$jobCategory->id])}}">{{$jobCategory->getName()}}</a> ({{$jobCategoryCount}})</b></li>
							@else
								<li><a href="{{$cvSearchUri($filters,['jobCategory'=>$jobCategory->id])}}">{{$jobCategory->getName()}}</a> ({{$jobCategoryCount}})</li>
							@endif
						@endif
					@endforeach
					</ul>

					<legend>Job Type <a href="{{$cvSearchUri($filters,['jobType'=>0])}}" class="pull-right"><small>reset</small></a></legend>
					<ul class="list-unstyled">
					@foreach($jobTypes as $jobType)	
						<?php
						$jobTypeCount = $cvSearchCount($filters, ['jobType'=>$jobType->id]);
						?>
						@if($jobTypeCount > 0)
							@if($jobType->id == $filters["jobType"])	
								<li><b><a href="{{$cvSearchUri($filters,['jobType'=>$jobType->id])}}">{{$jobType->getName()}}</a> ({{$jobTypeCount}})</b></li>
							@else
								<li><a href="{{$cvSearchUri($filters,['jobType'=>$jobType->id])}}">{{$jobType->getName()}}</a> ({{$jobTypeCount}})</li>
							@endif
						@endif
					@endforeach
					</ul>

					<legend>Salary <a href="{{$cvSearchUri($filters,['salaryType'=>0, 'salaryRate'=>0, 'salaryRateTo'=>0])}}" class="pull-right"><small>reset</small></a></legend>
					<ul class="list-unstyled">
					@foreach($salaryTypes as $salaryType)
						<li> {{$salaryType->getName()}}
							<ul class="">
							@foreach($salaryType->salaryRange as $salaryRange)
								<?php
								$salaryRateCount = $cvSearchCount($filters, ['salaryType'=>$salaryType->id, 'salaryRate'=>$salaryRange->range_from, 'salaryRateTo'=>$salaryRange->range_to]);
								?>
								@if($salaryRateCount > 0)
									@if($salaryType->id==$filters["salaryType"] && $salaryRange->range_from==$filters["salaryRate"] && $salaryRange->range_to==$filters["salaryRateTo"])
										<li><b><a href="{{$cvSearchUri($filters,['salaryType'=>$salaryType->id, 'salaryRate'=>$salaryRange->range_from, 'salaryRateTo'=>$salaryRange->range_to])}}">{{$salaryRange->range()}}</a> ({{$salaryRateCount}})</b></li>
									@else
										<li><a href="{{$cvSearchUri($filters,['salaryType'=>$salaryType->id, 'salaryRate'=>$salaryRange->range_from, 'salaryRateTo'=>$salaryRange->range_to])}}">{{$salaryRange->range()}}</a> ({{$salaryRateCount}})</li>
									@endif
								@endif
							@endforeach
							</ul>
						</li>
					@endforeach
					</ul>

				</div>
			</div>
			<div class="col-md-8">
				<div class="well well-sm">
					<div class="row">
						<div class="col-md-12">
							<form name='frmSort'>
								@if($filters["viewMode"] == "map")
									Showing {{count($cvs)}} seeker(s) on map...
								@else
									@foreach($filters as $key=>$param)
										@if($key!=="sortBy")
											<input type='hidden' name='{{$key}}' value="{{$param}}" />
										@endif
									@endforeach
									<div class="form-group col-lg-4 col-lg-offset-6">
										<div class="input-group">
											<span class="input-group-addon">Sort By</span>	
											<select name="sortBy" onChange="document.forms['frmSort'].submit()" class="form-control">
												<option value="relevance" {{$filters['sortBy'] == "relevance" ? "selected" : ""}}>Relevance</option>
												<option value="name" {{$filters['sortBy'] == "name" ? "selected" : ""}}>Name</option>
											</select>
										</div>
									</div>
								@endif
								<div class="form-group col-lg-2 {{ $filters['viewMode'] == 'map' ? 'col-lg-offset-10' : '' }}">
									<div class="btn-group btn-group-justified">
										<a href="{{$cvSearchUri($filters, ['viewMode'=>'list'])}}" class="btn {{ $filters['viewMode'] == 'list' ? 'btn-primary' : 'btn-default' }}"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></a>
										<a href="{{$cvSearchUri($filters, ['viewMode'=>'map'])}}" class="btn {{ $filters['viewMode'] == 'map' ? 'btn-primary' : 'btn-default' }}"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span></a>
									</div>
								</div>
							</form>
						</div>
					</div>

					@if($filters["viewMode"] == "map") 
						@include('includes.recruiter.search-cv.map')
					@else
						@include('includes.recruiter.search-cv.list')
					@endif

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
                            	<input type="text" class="form-control" name="subject" id="subject" value="" placeholder="Enter subject...">
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
