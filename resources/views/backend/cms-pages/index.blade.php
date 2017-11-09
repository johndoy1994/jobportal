@extends('layouts.backend')

@section('title', 'CMS Page Listing')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>CMS Page Listing</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-new-cmspage',Request::all())}}" class="btn btn-primary pull-right">Add New</a>
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
        
        {{Form::open(array('url' => route('admin-cms-index-post'), 'method' => 'post','class'=>'row'))}}	
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
				@if(!empty($recordsPerPage))
					Show <select id="recordsPerPage" data-target="cms-page-listing">
						@foreach($recordsPerPage as $perPage)
							@if($perPage==0)
								<option value="0">All</option>
							@else
								<option value="{{$perPage}}" {{(session()->get("job-listing") == $perPage) ? 'selected' : ''}}>{{$perPage}}</option>
							@endif
						@endforeach
					</select> entries
				@endif

				@if($isRequestSearch)
					<a href="{{route('admin-cms-page')}}" class="pull-right btn btn-danger btn-sm">Reset Search </a><br><br>
				@endif

				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="text-center">{{Form::checkbox('catname', '',null, ['class'=>'selectallcol'])}}</th>
							<th>Page Title <a href="{{route('admin-cms-page', $sort_columns['page_title']['params'])}}"><i class="fa fa-angle-{{$sort_columns['page_title']['angle']}}"></i></a></th>
							<th>Page Name <a href="{{route('admin-cms-page', $sort_columns['page_name']['params'])}}"><i class="fa fa-angle-{{$sort_columns['page_name']['angle']}}"></i></a></th>
							<th>Page Content</th>
							<th class="text-center">Status</th>
						</tr>
					</thead>

					<tbody>
					{{$data}}
					@if(count($data)!=0)
						@foreach($data as $key=>$cmspage)
							<tr>
								<td class="col_width5 text-center">
								@if($cmspage->page_name!='email_hearder' && $cmspage->page_name!='email_footer' && $cmspage->page_name!='terms')	
									{{Form::checkbox('cmspagemultiple[]', $cmspage->id,null, ['class'=>'selectallcol1','id'=>'cmspagemultiple'])}}
								@endif	
								</td>
								<td>
									<a  href="{{route('admin-edit-cmspage',array_merge( ['id'=> $cmspage->id,'page'=>$cmspage->page_name ]))}}">{{$cmspage->page_title}}</a>
								</td>
								<td>{{$cmspage->page_name}}</td>
								
								<td>
									<a href="#" class="cms-page-content" data-pageName="{{$cmspage->page_name}}" data-content="{{$cmspage->page_content}}">View Content</a>
									<!-- @if(strlen($cmspage->page_content) > 50)
										{{substr($cmspage->page_content,0,50)}}
										<a href="#" class="cms-page-content" data-pageName="{{$cmspage->page_name}}" data-content="{{$cmspage->page_content}}">View More</a>
									@else
										{{$cmspage->page_content}}
									@endif -->

								</td>
								
								<td class="col_width30 text-center">
									@if($cmspage->page_name!='email_hearder' && $cmspage->page_name!='email_footer' && $cmspage->page_name!='terms')	
										<div class="btn-group">
											<a class="btn {{($cmspage->status=='1')? 'btn-primary disabled' : 'btn-default'}} btn-sm" href="{{route('admin-active-inactive-cms-page', array_merge( ['PageId'=> $cmspage->id ],['action'=>'1'] ,Request::all()) )}}">On</a>
											<a class="btn {{($cmspage->status=='1')? 'btn-default' : 'btn-primary disabled'}} btn-sm" href="{{route('admin-active-inactive-cms-page', array_merge( ['PageId'=> $cmspage->id ],['action'=>'0'] ,Request::all()) )}}">Off</a>
										</div>
									@endif
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="5" class="text-center">No record(s) found.</td>
						</tr>
					@endif
						
				 	@if(count($data) > 0)
						<tr>
							<td colspan=5 class="text-center">
								{{$data->appends(['search'=>Request::get('search'),'page'=>Request::get('[page]'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
							</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		{{Form::close()}}
	</div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Page title</h4>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@push('footer')
	<script>
	$(".cms-page-content").on("click", function(){
		var pageTitle = $(this).data("pagename");
		var pageContent = $(this).data("content");

		$('#myModal .modal-header .modal-title').html(pageTitle);
		$('#myModal .modal-body').html(pageContent);
		$('#myModal').modal('show');
	});

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