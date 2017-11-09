@extends('layouts.backend')

@section('title', 'Education Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Education Listing</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-new-education',Request::all())}}" class="btn btn-primary pull-right">Add New</a>
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
            			<input type="text" class="form-control" name="search" id="search" value="{{Request::get('search')}}" />
            			<span class="input-group-btn">
            				<button type="submit" class="btn btn-default">Search</button>
            			</span>
            		</div>
            	</div>
            
        {{Form::close()}}
        {{Form::open(array('url' => route('admin-education-post'), 'method' => 'post','class'=>'row'))}}	
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
				Show <select id="recordsPerPage" data-target="education-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("education-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch)
					<a href="{{route('admin-education')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Name <a href="{{route('admin-education', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th class="text-center">Order <a href="{{route('admin-education', $sort_columns['order']['params'])}}"><i class="fa fa-angle-{{$sort_columns['order']['angle']}}"></i></a></th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody>
					@if(count($Educations)!=0)
						@foreach($Educations as $Education)
							<tr>
								<td class="col_width10 text-center">{{Form::checkbox('educationmultiple[]', $Education->id,null, ['class'=>'selectallcol1','id'=>'educationmultiple'])}}</td>
								<td>{{$Education->getName()}}</td>
								<td class="col_width10 text-center">
								@if($Education->getFirstOrder() == $Education->getLastOrder())

								@else
									@if($Education->order > $Education->getFirstOrder() && $Education->order < $Education->getLastOrder())
										<!-- Up/Down -->
										<a href="{{route('admin-education-moveorder', ['education'=>$Education->id, 'action'=>'up'])}}"><i class="fa fa-angle-up"></i></a>
										<a href="{{route('admin-education-moveorder', ['education'=>$Education->id, 'action'=>'down'])}}"><i class="fa fa-angle-down"></i></a>
									@elseif($Education->order == $Education->getFirstOrder())
										<!-- Down -->
										<a href="{{route('admin-education-moveorder', ['education'=>$Education->id, 'action'=>'down'])}}"><i class="fa fa-angle-down"></i></a>
									@elseif($Education->order == $Education->getLastOrder())
										<!-- Up -->
										<a href="{{route('admin-education-moveorder', ['education'=>$Education->id, 'action'=>'up'])}}"><i class="fa fa-angle-up"></i></a>
									@endif
								@endif
								</td>
								<td class="col_width30">
									<a class="btn btn-success btn-xs" href="{{route('admin-edit-education', array_merge( ['Education'=> $Education->id ], Request::all()) )}}">Edit</a>
									<a class="btn btn-danger btn-xs" href="{{route('admin-delete-education', array_merge( ['Education'=> $Education->id ], Request::all()) )}}" onclick="return confirm('Are you sure to delete this Education ?')">Delete</a>
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="4" class="text-center">No record(s) found.</td>
						</tr>
					@endif
					 	@if(count($Educations) > 0)
							<tr>
								<td colspan=4 class="text-center">
								{{$Educations->appends(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
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