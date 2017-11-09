@extends('layouts.backend')

@section('title', 'Edit Tag')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Edit Tag</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-tag',Request::all())}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-edit-tag-post',array_merge( ['Tag'=> $Tags->id ], Request::all()) )}}">
					<legend>Edit Tag</legend>
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

					@if(count($errors)>0)
						<div class="alert alert-warning">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</div>
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Job category Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_category_id" name="job_category_id" required="">
									<option value="">Select job category</option>
									@foreach($categorys as $category)
										@if($Tags->JobTitle && $Tags->JobTitle->category)
											<option {{($Tags->JobTitle->category->id==$category->id)? "selected" : ""}} value="{{$category->id}}">{{$category->name}}</option>
										@else
											<option value="{{$category->id}}">{{$category->name}}</option>	
										@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Title Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_title_id" name="job_title_id" required="">
									<option value="">Select job title</option>
									<!-- @if($Tags->JobTitle)
									<option value="{{$Tags->JobTitle->id}}" selected>{{$Tags->JobTitle->title}}</option>
									@endif -->
									@if($Tags->JobTitle && $Tags->JobTitle->category)
										@foreach($Tags->JobTitle->category->jobtitles as $jobTitle)
											@if($jobTitle->id != $Tags->JobTitle->id)
												<option value="{{$jobTitle->id}}">{{$jobTitle->title}}</option>
											@else
												<option value="{{$jobTitle->id}}" selected>{{$jobTitle->title}}</option>
											@endif
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Tag Name :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[A-Za-z0-9-+.# ]+" title="Please enter valid tag name" required name="name" class="form-control" value="{{$Tags->name}}" />
							</div>
						</div>	
							
					</fieldset>
					{{csrf_field()}}
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('footer')
<script type="text/javascript">
	$(document).on('change', '#job_category_id', function() {
			if($(this).val()==""){
				var id =0;
			}else{
				var id =$(this).val();
			}
            $.ajax({
                dataType:'json',
                url:  "{{route('api-public-jobtitles')}}",
                data: {'jobCategoryId' : id},
                success: function(data){
                    $('#job_title_id').html('<option value="">Select job title</option>');
                    for(var i=0;i<data.length;i++){
                        $('#job_title_id').append('<option value="'+data[i].id+'"> '+data[i].title+'</option>');
                    }
                }
            });
        });
</script>
@endpush