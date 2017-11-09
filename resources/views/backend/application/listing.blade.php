@extends('layouts.backend')

@section('title', 'Application Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Application Listing</h3>
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
	    
	    <div class="form-group">
    		<div class="input-group">
    		<a class="{{ !Request::has('status') ? 'bold-text' : '' }}" href="{{route('admin-application-post', ['status'=>''])}}">All</a> ({{$countStatus[0]->countid}}) |
    		<a class="{{ Request::has('status') && Request::get('status') == 'in-process' ? 'bold-text' : '' }}" href="{{route('admin-application-post', ['status'=>'in-process'])}}">New</a>({{$countStatus[0]->waiting_applications}}) | 
    		<a class="{{ Request::has('status') && Request::get('status') == 'accepted' ? 'bold-text' : '' }}" href="{{route('admin-application-post', ['status'=>'accepted'])}}">Accepted</a> ({{$countStatus[0]->accepted_applications}}) | 
    		<a class="{{ Request::has('status') && Request::get('status') == 'rejected' ? 'bold-text' : '' }}" href="{{route('admin-application-post', ['status'=>'rejected'])}}">Rejected</a> ({{$countStatus[0]->rejected_applications}}) |
    		<a class="{{ Request::has('status') && Request::get('status') == 'guest' ? 'bold-text' : '' }}" href="{{route('admin-application-post', ['status'=>'guest'])}}">Guest</a> ({{$countStatus[0]->is_guest}})
    		</div>
    	</div>
	    {{Form::open(array('method' => 'get','class'=>''))}}	
        	
            	<div class="form-group pull-right" style="width:200px;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="search" id="search" value="{{Request::get('search')}}" />
            			<span class="input-group-btn">
            				<button type="submit" name="submit" value="Search" class="btn btn-default">Search</button>
            			</span>
            		</div>
            	</div>
            
        {{Form::close()}}
        {{Form::open(array('method' => 'get','class'=>'','id'=>"myform",'name'=>"myform"))}}	
        	
            	<div class="form-group pull-right" style="padding-right: 150px; width:50%;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="date" id="date" value="{{Request::get('date')}}" />
            			<span class="input-group-btn">
            				<button type="submit"  class="btn btn-default">Filter</button>
            			</span>
            		</div>
            	</div>
        {{Form::close()}} 
        {{Form::open(array('url' => route('admin-application-post'), 'method' => 'post','class'=>'row'))}}	
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
				Show <select id="recordsPerPage" data-target="application-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("application-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch || Request::get('date'))
					<a href="{{route('admin-application')}}" class="pull-right btn btn-danger btn-sm">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Applicant Name <a href="{{route('admin-application', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th>Phone <a href="{{route('admin-application', $sort_columns['mobile_number']['params'])}}"><i class="fa fa-angle-{{$sort_columns['mobile_number']['angle']}}"></i></a></th>
							<th>Email <a href="{{route('admin-application', $sort_columns['email_address']['params'])}}"><i class="fa fa-angle-{{$sort_columns['email_address']['angle']}}"></i></a></th>
							<th>Job <a href="{{route('admin-application', $sort_columns['title']['params'])}}"><i class="fa fa-angle-{{$sort_columns['title']['angle']}}"></i></a></th>
							<th>Match Status</th>
							<th>Applied</th>
							<th class="text-center">CV</th>
							<th>Status <a href="{{route('admin-application', $sort_columns['status']['params'])}}"><i class="fa fa-angle-{{$sort_columns['status']['angle']}}"></i></a></th>
							
						</tr>
					</thead>
					<tbody>
					@if(count($Applications)!=0)
						@foreach($Applications as $Application)
							<tr>
								<td class="col_width10 text-center">{{Form::checkbox('applicationmultiple[]', $Application->id,null, ['class'=>'selectallcol1','id'=>'applicationmultiple'])}}</td>
								<!-- <td><a href="#showDetails" role="show-job-application" data-application="{{$Application->id}}">{{$Application->name}}</a></td> -->
								<td><a href="{{route('admin-showjobapplication',array_merge( ['JobApplication'=> $Application->id,'type'=>1 ], Request::all()) )}}">{{$Application->name}}</a>
								@if($Application->isGuest())
									<label class="label label-warning">Guest</label>
								@endif
								</td>
								<td>
									@if(!empty($Application->mobile_number) && $Application->mobile_number!=0)
										{{$Application->mobile_number}}
									@else
										N/A
									@endif
								</td>
								<td>
									@if(!empty($Application->email_address))
										{{$Application->email_address}}
									@else
										N/A
									@endif
								</td>
								<td>{{$Application->title}}</td>
								<td class="text-center">
									<?php
									$status = $Application->getMatchingStatus();
									echo "<label class='label label-primary'>".$status[1]."/".$status[0]."</label><br/>";
									echo "<label class='label label-success'>".$status[3]."/".$status[2]."</label>";
									?>
								</td>
								<td>
									<div>{{$Application->created_at->format('Y-m-d')}}</div>
									@if(abs($Application->days)!=0)
									 {{abs($Application->days)}} days ago.
									@else
										Today
									@endif
									</td>
								<td class="text-center">
									@if($Application->filename)
									<a href="{{Route('admin-user-resumes-download',['id'=>$Application->user_id])}}" class="btn btn-primary">Download CV</a>
									@else
										N/A
									@endif
								</td>	
								<td>
									{{$Application->getStatus()}}										
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="9" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
						 	@if(count($Applications) > 0)
								<tr>
									<td colspan=9 class="text-center">
									{{$Applications->appends(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
									</td>
								</tr>
							@endif
					</tbody>
				</table>
			</div>
		{{Form::close()}}

		@include('includes.backend.job-application-modal')

	</div>
@endsection
@push('head')
<link href="{{asset('/backend/css/datetimepicker/jquery-ui.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('/backend/css/datetimepicker/jquery-ui-timepicker-addon.css')}}" rel="stylesheet" type="text/css">
@endpush
@push('footer')
<script src="{{asset('backend/js/datetimepicker/jquery-ui.min.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon-i18n.min.js')}}"></script>
<script>
$(document).ready(function () {
$("#date").datepicker({
        dateFormat: 'MM yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
        }
    });

    $("#date").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });
$('.selectallcol').click(function(event) {
       var id=$(this).data('class');
        if(this.checked) { 
               $(".selectallcol1").prop('checked', true);
            }else{
            $(".selectallcol1").prop('checked', false);      
        }
    });
});
</script>
@endpush