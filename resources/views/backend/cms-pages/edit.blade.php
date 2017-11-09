@extends('layouts.backend')

@section('title', $data->page_name)

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>{{$data->page_name}}</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-cms-page',Request::all())}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-cms-page-post')}}">
					<legend>
						Edit Page
					</legend>
					@if(session('success_message'))
						<div class="alert alert-success">
							{!!session('success_message')!!}
						</div>
					@endif

					@if(session('error_message'))
						<div class="alert alert-danger">
							{!!session('error_message')!!}
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
						<input type="hidden" name="action" value="edit">
						<input type="hidden" name="page_name" value="{{$data->page_name}}">
						<div class="form-group">
							<label class="control-label col-lg-3">Title *</label>
							<div class="col-lg-5">
								<input type="text" required name="page_title" value="{{$data->page_title}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Description *:</label>
							<div class="col-lg-8">
								<textarea name="page_content" id="page_content" class="form-control">{{$data->page_content}}</textarea>
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
<!-- <script src="//cdn.ckeditor.com/4.5.9/standard/ckeditor.js"></script>  -->
<script src="{{asset('backend/js/ckeditor-full/ckeditor.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui.min.js')}}"></script>
<script type="text/javascript">

$(document).ready(function(){
	CKEDITOR.replace( 'page_content' );
});
</script>
@endpush
