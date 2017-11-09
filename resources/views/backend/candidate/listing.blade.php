@extends('layouts.backend')

@section('title', 'Candidate Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Candidate Listing</h3>
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
    		<a class="{{ !Request::has('status') ? 'bold-text' : '' }}" href="{{route('admin-candidate-post', ['status'=>''])}}">All</a> ({{$countStatus[0]->countid}}) |
    		<a class="{{ Request::has('status') && Request::get('status') == 'guest' ? 'bold-text' : '' }}" href="{{route('admin-candidate-post', ['status'=>'guest'])}}">Guest</a> ({{$countStatus[0]->is_guest}})
    		</div>
    	</div>
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
        {{Form::open(array('url' => route('admin-candidate-post'), 'method' => 'post','class'=>'row'))}}	
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
				Show <select id="recordsPerPage" data-target="candidate-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("candidate-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch)
					<a href="{{route('admin-candidate')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Name <a href="{{route('admin-candidate', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th>Phone <a href="{{route('admin-candidate', $sort_columns['mobile_number']['params'])}}"><i class="fa fa-angle-{{$sort_columns['mobile_number']['angle']}}"></i></a></th>
							<th>Email <a href="{{route('admin-candidate', $sort_columns['email_address']['params'])}}"><i class="fa fa-angle-{{$sort_columns['email_address']['angle']}}"></i></a></th>
							<th>Job <a href="{{route('admin-candidate', $sort_columns['title']['params'])}}"><i class="fa fa-angle-{{$sort_columns['title']['angle']}}"></i></a></th>
							<th>Applied</th>
							<th class="text-center">CV</th>
							<th>Match Status</th>
						</tr>
					</thead>
					<tbody>
					@if(count($Candidates)!=0)
						@foreach($Candidates as $Candidate)
							<tr>
								<td class="col_width10 text-center">{{Form::checkbox('candidatemultiple[]', $Candidate->id,null, ['class'=>'selectallcol1','id'=>'candidatemultiple'])}}</td>
								<td><a href="{{route('admin-showjobapplication',array_merge( ['JobApplication'=> $Candidate->id,'type'=>2 ], Request::all()) )}}">{{$Candidate->name}}</a>
								@if($Candidate->isGuest())
									<label class="label label-warning">Guest</label>
								@endif	
								</td>
								<td>
									@if(!empty($Candidate->mobile_number) && $Candidate->mobile_number!=0)
										{{$Candidate->mobile_number}}
									@else
										N/A
									@endif
								</td>
								<td>
									@if(!empty($Candidate->email_address))
										{{$Candidate->email_address}}
									@else
										N/A
									@endif
								</td>
								<td>{{$Candidate->title}}</td>
								<td>
									<div>{{$Candidate->created_at->format('Y-m-d')}}</div>
									@if(abs($Candidate->days)!=0)
									 	{{abs($Candidate->days)}} days ago.
									@else
										today
									@endif
								</td>
								<td class="text-center">
									@if($Candidate->filename)
									<a href="{{Route('admin-user-resumes-download',['id'=>$Candidate->user_id])}}" class="btn btn-primary">Download CV</a>
									@else
										N/A
									@endif
								</td>
								<td class="text-center">
									<?php
									$status = $Candidate->getMatchingStatus();
									echo "<label class='label label-primary'>".$status[1]."/".$status[0]."</label><br/>";
									echo "<label class='label label-success'>".$status[3]."/".$status[2]."</label>";
									?>
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="8" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
						 	@if(count($Candidates) > 0)
								<tr>
									<td colspan=8 class="text-center">
									{{$Candidates->appends(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
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