@extends('layouts.backend')

@section('title', 'Salary Range Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Salary Range Listing</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-salary-range-add-new',Request::all())}}" class="btn btn-primary pull-right">Add New</a>
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
        {{Form::open(array('url' => route('admin-salary-range-post'), 'method' => 'post','class'=>'row'))}}	
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
				Show <select id="recordsPerPage" data-target="salaryrange-listing">
					@foreach($recordsPerPage as $perPage)
						@if($perPage==0)
							<option value="0">All</option>
						@else
							<option value="{{$perPage}}" {{(session()->get("salaryrange-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
						@endif
					@endforeach
				</select> entries
				@if($isRequestSearch)
					<a href="{{route('admin-salary-range')}}" class="pull-right">Reset Search </a>
				@endif
				<br/><br/>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('', '',null, ['class'=>'category_selectall'])}}</th>
							<th>Salary Type <a href="{{route('admin-salary-range', $sort_columns['order']['params'])}}"><i class="fa fa-angle-{{$sort_columns['order']['angle']}}"></i></a></th>
							<th>Range From <a href="{{route('admin-salary-range', $sort_columns['range_from']['params'])}}"><i class="fa fa-angle-{{$sort_columns['range_from']['angle']}}"></i></a></th>
							<th>Range To <a href="{{route('admin-salary-range', $sort_columns['range_to']['params'])}}"><i class="fa fa-angle-{{$sort_columns['range_to']['angle']}}"></i></a></th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody>
						@if(count($SalaryRange) > 0)
							@foreach($SalaryRange as $item)
								<tr>
									<td class="col_width10 text-center">{{Form::checkbox('salaryrange-ids[]', $item->id,null, ['class'=>'chkcategory'])}}</td>
									<td>{{($item->salaryType) ? $item->salaryType->salary_type_name : "N/A"}}</td>
									<td>{{$item->range_from}}</td>
									<td>{{$item->range_to}}</td>
									<td class="col_width30">
										<a href="{{route('admin-edit-salary-range', array_merge( ['item'=> $item->id ], Request::all()) )}}" class="btn btn-success btn-xs">Edit</a>
										<a href="{{route('admin-delete-salary-range', array_merge( ['item'=> $item->id ], Request::all()) )}}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure to delete this item ?')">Delete</a>
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="5" class="text-center">No record(s) found.</td>
							</tr>
						@endif
						@if(count($SalaryRange) > 0)
							<tr>
								<td colspan=5 class="text-center">
								{{$SalaryRange->appends(['search'=>Request::get('search'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
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