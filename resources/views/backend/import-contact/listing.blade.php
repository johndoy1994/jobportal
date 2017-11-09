@extends('layouts.backend')

@section('title', 'Gmail Contact Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-6">
				<h3>Gmail Contact Listing</h3>
			</div>
			<div class="col-md-6 text-right padding-top-10">
				<a href="{{route('api-through', ['provider'=>'google', 'action'=>'contacts', 'redirect'=>'admin-gmail-contacts'])}}" class="btn btn-primary pull-right">Import Gmail contacts</a>
				<a href="" data-toggle="modal" data-target="#myModal1" data-id="" data-email="" class="btn btn-info btn-email margin10">Email</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12"> <!-- Display Session Message  -->
	            @if(session('success_message'))
	                <div class="alert alert-success">
	                    {{session('success_message')}}
	                </div>
	            @endif

	            @if(session('error_message'))
	                <div class="alert alert-danger">
	                    {{session('error_message')}}
	                </div>
	            @endif
	        </div>
        </div>
        {{Form::open(array('method' => 'get','class'=>''))}}	
        	
            	<div class="form-group pull-right" style="width:200px;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="search" value="{{Request::get('search')}}" />
            			<span class="input-group-btn">
            				<button type="submit"  class="btn btn-default">Search</button>
            			</span>
            		</div>
            	</div>
            
        {{Form::close()}}
        {{Form::open(array('url' => route('post-admin-import-contact'), 'method' => 'post','class'=>'row'))}}	
			<div class="form-row">
            	<div class="form-group col-md-3" style="padding-right: 20px">
            		<div class="input-group">
            			{{Form::select('bulkid', array('deleted' => 'Delete'),null, ['placeholder' => 'Bulk Action','class'=>'form-control'])}}
            			<span class="input-group-btn">
            				<button type="submit" name="submit" value="Apply" class="btn btn-default">Apply</button>
            			</span>
            		</div>
            	</div>
            </div>
			<div class="col-md-12">
				Show <select id="recordsPerPage" data-target="userContact-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("userContact-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch)
					<a href="{{route('get-admin-import-contact')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>User Name <a href="{{route('get-admin-import-contact', $sort_columns['user_name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['user_name']['angle']}}"></i></a></th>
							<th>Name <a href="{{route('get-admin-import-contact', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th>Email <a href="{{route('get-admin-import-contact', $sort_columns['email']['params'])}}"><i class="fa fa-angle-{{$sort_columns['email']['angle']}}"></i></a></th>
							<!-- <th>Status</th> -->
						</tr>
					</thead>
					<tbody>
					@if(count($UserContacts)!=0)
						@foreach($UserContacts as $UserContact)
							<tr>
								<td class="col_width10 text-center">{{Form::checkbox('usercontactmultiple[]', $UserContact->id,null, ['class'=>'selectallcol1','id'=>'usercontactmultiple'])}}</td>
								<td>{{$UserContact->user_name}}</td>
								<td>{{$UserContact->name}}</td>
								<td>{{$UserContact->email}}</td>
								<!-- <td class="col_width30">
									<div class="btn-group">
										<a class="btn {{($UserContact->status=='ACTIVATED')? 'btn-primary' : 'btn-default'}} btn-sm" href="{{route('admin-active-inactive-employer-post', array_merge( ['user_id'=> $UserContact->user_id ],['action'=>'active'] ,Request::all()) )}}">On</a>
										<a class="btn {{($UserContact->status=='ACTIVATED')? 'btn-default' : 'btn-primary'}} btn-sm" href="{{route('admin-active-inactive-employer-post', array_merge( ['user_id'=> $UserContact->user_id ],['action'=>'inactive'] ,Request::all()) )}}">Off</a>
									</div>
									
								</td> -->
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="4" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
						 	@if(count($UserContacts) > 0)
								<tr>
									<td colspan=4 class="text-center">
									{{$UserContacts->appends(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
									</td>
								</tr>
							@endif
					</tbody>
				</table>
			</div>
		{{Form::close()}}
	</div>

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
<script>
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
        fd.append("table", 'user_contacts');
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