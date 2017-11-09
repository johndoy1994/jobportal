@extends('layouts.backend')

@section('title', 'Delete Job Type -  Listing')

@section('content')
	<div id="page-wrapper">
		<br/>
		<div class="row">
			<div class="col-md-12">
				<a href="{{route('admin-jobtype')}}" class="btn btn-primary">Back</a>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-delete-jobtype', ['jobtype'=>$jobtypes->id])}}">
					<legend>Delete Job ype</legend>
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
							<label class="control-label col-lg-3">JobType Name :</label>
							<div class="col-lg-9">
								<input readonly type="text" name="name" class="form-control" value="{{$jobtypes->name}}" />
							</div>
						</div>	
					</fieldset>
					{{csrf_field()}}
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-primary">Click here to delete this jobtype >></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection