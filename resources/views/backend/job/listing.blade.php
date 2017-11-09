@extends('layouts.backend')

@section('title', 'Job Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Job Listing</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-new-job',Request::all())}}" class="btn btn-primary pull-right">Add New</a>
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
	    {{Form::open(array('method' => 'get','class'=>'','id'=>"myform",'name'=>"myform"))}}	
        	
            	<div class="form-group pull-right" style="width:200px;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="search" id="search" value="{{Request::get('search')}}" />
            			<span class="input-group-btn">
            				<button type="submit" class="btn btn-default">Search</button>
            			</span>
            		</div>
            	</div>
        {{Form::close()}}    
        {{Form::open(array('method' => 'get','class'=>'','id'=>"myform",'name'=>"myform"))}}	
        	
            	<div class="form-group pull-right"  style="padding-right: 150px; width:50%;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="date" id="date" value="{{Request::get('date')}}" />
            			<span class="input-group-btn">
            				<button type="submit" class="btn btn-default">Filter</button>
            			</span>
            		</div>
            	</div>
        {{Form::close()}}    
        
        {{Form::open(array('url' => route('admin-job-post'), 'method' => 'post','class'=>'row'))}}	
			<div class="form-row">
            	<div class="form-group col-md-3" style="padding-right: 20px">
            		<div class="input-group">
            			{{Form::select('bulkid', array('deleted' => 'Delete','active' => 'Active','inactive' => 'Inactive'),null, ['placeholder' => 'Bulk Action','class'=>'form-control','id'=>'bulkid'])}}
            			<span class="input-group-btn">
            				<button type="submit" name="submit" value="Apply" class="btn btn-default">Apply</button>
            			</span>
            		</div>
            	</div>
            </div>

			<div class="col-md-12">
				Show <select id="recordsPerPage" data-target="job-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("job-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries

				@if($isRequestSearch || Request::get('date'))
					<a href="{{route('admin-job')}}" class="pull-right btn btn-danger btn-sm">Reset Search </a>
				@endif

				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Position Title <a href="{{route('admin-job', $sort_columns['title']['params'])}}"><i class="fa fa-angle-{{$sort_columns['title']['angle']}}"></i></a></th>
							<th>Company <a href="{{route('admin-job', $sort_columns['company_name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['company_name']['angle']}}"></i></a></th>
							<th>Job Type</th>
							<th>Category</th>
							<th>City</th>
							<th>Expires <a href="{{route('admin-job', $sort_columns['expired_days']['params'])}}"><i class="fa fa-angle-{{$sort_columns['expired_days']['angle']}}"></i></a></th>
							<th>Applications <a href="{{route('admin-job', $sort_columns['application_count']['params'])}}"><i class="fa fa-angle-{{$sort_columns['application_count']['angle']}}"></i></a></th>
							<th>Status</th>
							<th>Action</th>
							
						</tr>
					</thead>

					<tbody>

					@if(count($Jobs)!=0)
						@foreach($Jobs as $Job)
							<tr>
								<td class="col_width5 text-center">{{Form::checkbox('jobmultiple[]', $Job->id,null, ['class'=>'selectallcol1','id'=>'jobmultiple'])}}</td>
								<td>
									<a  href="{{route('admin-edit-job',array_merge( ['Job'=> $Job->id ], Request::all()) )}}">{{$Job->title}}</a>
									@if($Job->isEnded())
										<label class="label label-warning">Ended</label>
									@endif
								</td>
								<td>{{$Job->company_name}}</td>
								<td><a href="{{route('admin-job-post', ['jobTypeId'=>$Job->job_type_id, 'search'=>Request::get('search'),'jobCategoryId'=>Request::get('jobCategoryId'),'jobCityId'=>Request::get('jobCityId')])}}">{{$Job->job_type__name}}</a></td>
								<td><a href="{{route('admin-job-post', ['jobCategoryId'=>$Job->category_id, 'search'=>Request::get('search'),'jobTypeId'=>Request::get('jobTypeId'),'jobCityId'=>Request::get('jobCityId')])}}">
								@if($Job->jobTitle && $Job->jobTitle->category)	
									{{$Job->jobTitle->category->getName()}}
								@else
									N/A
								@endif	
								</a></td>
								<td><a href="{{route('admin-job-post', ['jobCityId'=>$Job->city_id, 'search'=>Request::get('search'),'jobTypeId'=>Request::get('jobTypeId'),'jobCategoryId'=>Request::get('jobCategoryId')])}}">{{($Job->jobAddresses) ? $Job->jobAddresses->getCityname() : "N/A"}}</a></td>
								<td>
									<?php
									$days = $Job->expired_days;
									?>
									@if($days == 0)
										Expires today
									@elseif($days == 1)
										Expiring next day.
									@elseif($days > 1)
										Expires in {{$days}} days.
									@else
										Expired
									@endif
								</td>
								<td class="text-center">{{($Job->jobApplication()) ? $Job->jobApplication()->count() : "N/A"}}</td>
								<?php

									$current_date = \Carbon\Carbon::now();
									$renew_date	=$Job->renew_date;	
									$renew=$renew_date->diffInDays($current_date,false);
								    //$renew=Utility::diffInDates($renew_date->format("Y-m-d"),$current_date->format("Y-m-d"));
        
								?>
								
								<td class="col_width30 text-center">
									@if($days<0 || $Job->isEnded())
										@if($Job->isEnded() && $days<0)
											Ended and expire
										@elseif($Job->isEnded())
											Ended
										@elseif($days<0)
											Expired
										@endif
									@else

										<div class="btn-group">
											<a class="btn {{($Job->status=='active')? 'btn-primary' : 'btn-default'}} btn-sm" href="{{route('admin-active-inactive-job-post', array_merge( ['JobId'=> $Job->id ],['action'=>'active'] ,Request::all()) )}}">On</a>
											<a class="btn {{($Job->status=='active')? 'btn-default' : 'btn-primary'}} btn-sm" href="{{route('admin-active-inactive-job-post', array_merge( ['JobId'=> $Job->id ],['action'=>'inactive'] ,Request::all()) )}}">Off</a>
										</div>
									@endif	
									<!-- @if($Job->status=="active")
										<a class="btn btn-danger btn-xs" href="{{route('admin-active-inactive-job-post', array_merge( ['JobId'=> $Job->id ],['action'=>'inactive'] ,Request::all()) )}}">Inactive</a>
									@endif
									
									@if($Job->status=="inactive") 
										<a class="btn btn-success btn-xs" href="{{route('admin-active-inactive-job-post', array_merge( ['JobId'=> $Job->id ],['action'=>'active'] ,Request::all()) )}}">Active</a>
									 @endif	-->
								</td>
								<td class="col_width30">	
									@if($Job->isRenewable()[0]) 
										<a class="btn btn-primary btn-xs" href="{{route('admin-renew-job', array_merge( ['Job'=> $Job->id ], Request::all()))}}">Renew</a>	
									@endif

									@if($Job->isRepostable()[0])
										<a class="btn btn-info btn-xs" href="{{route('admin-repost-job', array_merge( ['Job'=> $Job->id ], Request::all()))}}">Repost</a>
									@endif

										
									
								</td>
								
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="10" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
						 	@if(count($Jobs) > 0)
								<tr>
									<td colspan=10 class="text-center">
										{{$Jobs->appends(['date'=>Request::get('date'),'search'=>Request::get('search'),'jobCategoryId'=>Request::get('jobCategoryId'),'jobCityId'=>Request::get('jobCityId'),'jobCategoryId'=>Request::get('jobCategoryId'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
									</td>
								</tr>
							@endif
					</tbody>
				</table>
			</div>
		{{Form::close()}}
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