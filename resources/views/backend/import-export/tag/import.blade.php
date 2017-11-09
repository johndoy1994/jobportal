@extends('layouts.backend')

@section('title', 'Import Tag')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Import Tag</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('import-export-list')}}" class="btn btn-primary pull-right" style="margin-left:10px;">Back</a>
				<a href="{{route('import-tag-sample')}}" class="btn btn-primary pull-right">Sample CSV</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('import-tag-post')}}" enctype="multipart/form-data">
					<legend>Import Tag</legend>
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
					
					@if(session('successCount'))
						<div class="alert alert-success">
							<p>{{session('successCount')}} Records successfully Added </p>
						</div>
					@endif

					@if(session('failCount'))
						<div class="alert alert-danger">
							<p>{{session('failCount')}} Record fails to add.</p>
						</div>
					@endif
					@if(session('skipedRows'))

						@if(count(session('skipedRows'))>0)
							<div class="alert alert-warning">
								<p>Following row are remaining or already exits.
									@foreach(session('skipedRows') as $skiprow)
										<li>{{$skiprow[0]}}</li>
									@endforeach
								</p>
							</div>
						@endif
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Job category Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_category_id" name="job_category_id" required="">
									<option value="">Select job category</option>
									@foreach($categorys as $category)
										<option value="{{$category->id}}">{{$category->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Title Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_title_id" name="job_title_id" required="">
									<option value="">Select job title</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Import file :</label>
							<div class="col-lg-5">
								<input type="file" id="filename" name="filename" required=""/>	
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