@extends('layouts.backend')

@section('title', 'Employer Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Employer Listing</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-new-employer',Request::all())}}" class="btn btn-primary pull-right">Add New</a>
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
        {{Form::open(array('url' => route('admin-employer-post'), 'method' => 'post','class'=>'row'))}}	
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
				Show <select id="recordsPerPage" data-target="employer-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("employer-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch)
					<a href="{{route('admin-employer')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Company Name <a href="{{route('admin-employer', $sort_columns['company_name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['company_name']['angle']}}"></i></a></th>
							<th>City <a href="{{route('admin-employer', $sort_columns['cityname']['params'])}}"><i class="fa fa-angle-{{$sort_columns['cityname']['angle']}}"></i></a></th>
							<th>Contact Person <a href="{{route('admin-employer', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th>Phone <a href="{{route('admin-employer', $sort_columns['mobile_number']['params'])}}"><i class="fa fa-angle-{{$sort_columns['mobile_number']['angle']}}"></i></a></th>
							<th>Email <a href="{{route('admin-employer', $sort_columns['email_address']['params'])}}"><i class="fa fa-angle-{{$sort_columns['email_address']['angle']}}"></i></a></th>
							<th>Job Posted <a href="{{route('admin-employer', $sort_columns['job_count']['params'])}}"><i class="fa fa-angle-{{$sort_columns['job_count']['angle']}}"></i></a></th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
					@if(count($Employers)!=0)
						@foreach($Employers as $Employer)
							<tr>
								<td class="col_width10 text-center">{{Form::checkbox('employermultiple[]', $Employer->id,null, ['class'=>'selectallcol1','id'=>'employermultiple'])}}</td>
								<td><a  href="{{route('admin-edit-employer',array_merge( ['Employer'=> $Employer->id ], Request::all()) )}}">{{$Employer->company_name}}</a></td>
								<td><a href="{{route('admin-employer-post', ['cityId'=>$Employer->cityId, 'search'=>Request::get('search')])}}">{{$Employer->cityname}}</a></td>
								<td>{{$Employer->name}}</td>
								<td>{{$Employer->mobile_number}}</td>
								<td><a href="mailto:{{$Employer->email_address}}">{{$Employer->email_address}}</a></td>
								<td class="text-center"><a href="{{route('admin-job-post', ['employeeId'=>$Employer->id])}}">{{$Employer->job_count}}</a></td>
								<td class="col_width30">
									<div class="btn-group">
										<a class="btn {{($Employer->status=='ACTIVATED')? 'btn-primary' : 'btn-default'}} btn-sm" href="{{route('admin-active-inactive-employer-post', array_merge( ['user_id'=> $Employer->user_id ],['action'=>'active'] ,Request::all()) )}}">On</a>
										<a class="btn {{($Employer->status=='ACTIVATED')? 'btn-default' : 'btn-primary'}} btn-sm" href="{{route('admin-active-inactive-employer-post', array_merge( ['user_id'=> $Employer->user_id ],['action'=>'inactive'] ,Request::all()) )}}">Off</a>
									</div>
									<!-- @if($Employer->status=="ACTIVATED")
										<a class="btn btn-danger btn-xs" href="{{route('admin-active-inactive-employer-post', array_merge( ['user_id'=> $Employer->user_id ],['action'=>'inactive'] ,Request::all()) )}}">Inactive</a>
									@endif
									
									@if($Employer->status=="DEACTIVATED") 
										<a class="btn btn-success btn-xs" href="{{route('admin-active-inactive-employer-post', array_merge( ['user_id'=> $Employer->user_id ],['action'=>'active'] ,Request::all()) )}}">Active</a>
									@endif -->
									<!-- {{$Employer->status}} -->
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="8" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
						 	@if(count($Employers) > 0)
								<tr>
									<td colspan=8 class="text-center">
									{{$Employers->appends(['search'=>Request::get('search'),'cityId'=>Request::get('cityId'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
									</td>
								</tr>
							@endif
					</tbody>
				</table>
			</div>
		{{Form::close()}}
	</div>
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
</script>
@endpush