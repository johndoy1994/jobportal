@extends('layouts.backend')

@section('title', 'Job Category Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Job Category Listing</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-job-category-add-new',Request::all())}}" class="btn btn-primary pull-right">Add New</a>
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
            				<button type="submit" class="btn btn-default">Search</button>
            			</span>
            		</div>
            	</div>
            
        {{Form::close()}}
        {{Form::open(array('url' => route('admin-job-category-post'), 'method' => 'post','class'=>'row'))}}	
			<div class="form-row">
            	<div class="form-group col-md-3" style="padding-right: 20px">
            		<div class="input-group">
            			{{Form::select('bulk_action', array('delete' => 'Delete'),null, ['placeholder' => 'Bulk Action','class'=>'form-control'])}}
            			<span class="input-group-btn">
            				<button type="submit" name="action" value="Apply" class="btn btn-default">Apply</button>
            			</span>
            		</div>
            	</div>
            </div>
			<div class="col-md-12">
			Show <select id="recordsPerPage" data-target="job-category-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("job-category-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch)
					<a href="{{route('admin-job-category')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('', '',null, ['class'=>'category_selectall'])}}</th>
							<th>Category name <a href="{{route('admin-job-category', $sort_columns['name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['name']['angle']}}"></i></a></th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody>
						@if(count($JobCategory) > 0)
							@foreach($JobCategory as $item)
								<tr>
									<td class="col_width10 text-center">{{Form::checkbox('category-id[]', $item->id,null, ['class'=>'chkcategory'])}}</td>
									<td>{{$item->name}}</td>
									<td class="col_width30">
										<a href="{{route('admin-edit-job-category', array_merge( ['item'=> $item->id ], Request::all()))}}" class="btn btn-success btn-xs">Edit</a>
										<a href="{{route('admin-delete-job-category', array_merge( ['item'=> $item->id ], Request::all()))}}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure to delete this item ?')">Delete</a>
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="3" class="text-center">No record(s) found.</td>
							</tr>
						@endif
						@if(count($JobCategory) > 0)
							<tr>
								<td colspan=3 class="text-center">
								{{$JobCategory->appends(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
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
$('.category_selectall').click(function(event) {
       var id=$(this).data('class');
        if(this.checked) { 
               $(".chkcategory").prop('checked', true);
            }else{
            $(".chkcategory").prop('checked', false);      
        }
    });
});
</script>
@endpush