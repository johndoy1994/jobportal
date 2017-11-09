@extends('layouts.backend')

@section('title', 'New Tag')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Add New tag</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-tag',Request::all())}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-new-tag-post')}}">
					<legend>New Tag</legend>
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
									@foreach($Categories as $Categorie)
										<option value="{{$Categorie->id}}" {{(old('job_category_id')==$Categorie->id)?"selected":""}}>{{$Categorie->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Title Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_title_id" name="job_title_id" required="">
									<option value="">Select job title</option>
									@if($oldJobTitle)
										<option value="{{$oldJobTitle[0]}}" selected>{{$oldJobTitle[1]}}</option>
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Tag Name :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[A-Za-z0-9-+.# ]+" title="Please enter valid tag name"  name="name" value="{{old('name')}}" class="form-control" />
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