@extends('layouts.backend')

@section('title', 'User Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>User Listing</h3>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12"> <!-- Display Session Message  -->
	            @include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')
	        </div>
	    </div>
	    <div class="form-group">
    		<div class="col-md-6">
    		<a class="{{ !Request::has('type') ? 'bold-text' : '' }}" href="{{route('admin-user-post', ['type'=>''])}}">All</a> ({{$countType[0]->countid}}) |
    		<a class="{{ Request::has('type') && Request::get('type') == 'job_seeker' ? 'bold-text' : '' }}" href="{{route('admin-user-post', ['type'=>'job_seeker'])}}">Job Seeker</a> ({{$countType[0]->job_seeker}}) | 
    		<a class="{{ Request::has('type') && Request::get('type') == 'employer' ? 'bold-text' : '' }}" href="{{route('admin-user-post', ['type'=>'employer'])}}">Employer</a> ({{$countType[0]->employer}}) | 
    		<a class="{{ Request::has('type') && Request::get('type') == 'backend_admin' ? 'bold-text' : '' }}" href="{{route('admin-user-post', ['type'=>'backend_admin'])}}">Admin</a> ({{$countType[0]->backend_admin}}) 
    		</div>
			<div class="col-md-6 text-right">
			<form>
				<input type="checkbox" name="jobseeker" id="jobseeker" value="jobseeker"> All Jobseeker
				<input type="checkbox" name="employer" id="employer" value="employer"> All Employer
				<a href="" data-toggle="modal" data-target="#myModal1" data-id="" data-email="" class="btn btn-info btn-email margin10">Email</a>
				<a href="" data-toggle="modal" data-target="#myModal" data-id="" class="btn btn-success msg margin10">Msg</a>
				<a href="" data-toggle="modal" data-target="#myModal" data-id="" class="btn btn-primary margin10 chatMul">Chat</a>
			</form>
			</div>    		
    	</div>
    	</br></br>
	    {{Form::open(array('method' => 'get','class'=>''))}}	
        	
            	<div class="form-group pull-right" style="width:200px;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="search" id="search" value="{{Request::get('search')}}" />
            			<span class="input-group-btn">
            				<button type="submit" class="btn btn-default">Search</button>
            			</span>
            		</div>
            	</div>
        {{Form::close()}}
        {{Form::open(array('url' => route('admin-user-post'), 'method' => 'post','class'=>'row'))}}	
			<div class="form-row">
            	<div class="form-group col-md-3" style="padding-right: 20px">
            		<div class="input-group">
            			{{Form::select('bulkid', array('deleted' => 'Delete'),null, ['placeholder' => 'Bulk Action','class'=>'form-control','id'=>'bulkid'])}}

            			<span class="input-group-btn">
            				<button type="submit" name="submit" value="Apply" class="btn btn-default">Apply</button>
            			</span>
            		</div>
            	</div>
            </div>
			<div class="col-md-12">
				Show <select id="recordsPerPage" data-target="admin-user-list">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("admin-user-list") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				
				@if($isRequestSearch)
					<a href="{{route('admin-user-list')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Name <a href="{{route('admin-user-list', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th>Phone <a href="{{route('admin-user-list', $sort_columns['mobile_number']['params'])}}"><i class="fa fa-angle-{{$sort_columns['mobile_number']['angle']}}"></i></a></th>
							<th>Email <a href="{{route('admin-user-list', $sort_columns['email_address']['params'])}}"><i class="fa fa-angle-{{$sort_columns['email_address']['angle']}}"></i></a></th>
							<th>user type <a href="{{route('admin-user-list', $sort_columns['type']['params'])}}"><i class="fa fa-angle-{{$sort_columns['type']['angle']}}"></i></a></th>
							<th>user level <a href="{{route('admin-user-list', $sort_columns['level']['params'])}}"><i class="fa fa-angle-{{$sort_columns['level']['angle']}}"></i></a></th>
							<th>status </th>
							
						</tr>
					</thead>
					<tbody>
					@if(count($Users)!=0)
						@foreach($Users as $User)
							<tr>
								<td class="col_width10 text-center">
								@if($User->type!='BACKEND')	
									{{Form::checkbox('usermultiple[]', $User->id,null, ['class'=>'selectallcol1','id'=>'usermultiple'])}}</td>
								@endif

								<td><a  href="{{route('admin-edit-user',array_merge( ['user'=> $User->id ], Request::all()) )}}">{{$User->name}}</a></td>
								<td>{{($User->mobile_number) ? $User->mobile_number : 'N/A'}}</td>
								<td>{{($User->email_address) ? $User->email_address : "N/A"}}</td>
								<td>{{strtolower($User->type)}}</td>
								<td>{{strtolower($User->level)}}</td>

								<td class="col_width20">
									@if($User->type!='BACKEND')	
										<div class="btn-group">
											<a class="btn {{(strtolower($User->status)=='activated')? 'btn-primary' : 'btn-default'}} btn-sm" href="{{route('admin-active-inactive-user-post', array_merge( ['user_id'=> $User->id ],['action'=>'active'] ,Request::all()) )}}">On</a>
											<a class="btn {{(strtolower($User->status)=='activated')? 'btn-default' : 'btn-primary'}} btn-sm" href="{{route('admin-active-inactive-user-post', array_merge( ['user_id'=> $User->id ],['action'=>'inactive'] ,Request::all()) )}}">Off</a>
										</div>
										<a class="btn btn-danger btn-xs" href="{{route('admin-delete-user', array_merge( ['user'=> $User->id ], Request::all()) )}}" onclick="return confirm('Are you sure to delete this User ?')">Delete</a>
									@endif


								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="8" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
						 	@if(count($Users) > 0)
								<tr>
									<td colspan=8 class="text-center">
									{{$Users->appends(['search'=>Request::get('search'),'type'=>Request::get('type'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
									</td>
								</tr>
							@endif
					</tbody>
				</table>
			</div>
		{{Form::close()}}
		
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
                            <label for="inputTask" class="col-sm-3 control-label">Subject :</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control" name="subject" id="subject" value="">
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
$(document).ready(function () {
		$('.selectallcol').click(function(event) {
       		var id=$(this).data('class');
        	if(this.checked) { 
               	$(".selectallcol1").prop('checked', true);
            }else{
            	$(".selectallcol1").prop('checked', false);      
        	}
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
    $('#subject').val('');
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
    var subject=$('#subject').val();
    $(".ajax-email-status").hide();
    $(".ajax-email-status").removeClass("alert-success");
    $(".ajax-email-status").removeClass("alert-danger");
    if(content!='' && subject!=''){
        var fd = new FormData();
        fd.append("login", 'admin');
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