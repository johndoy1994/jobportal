@extends('layouts.backend')

@section('title', 'Edit Industry')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Edit Industry</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-industry',Request::all())}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-edit-industry-post', array_merge( ['Industry'=> $Industrys->id ], Request::all()) )}}">
					<legend>Edit Industry</legend>
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
							<label class="control-label col-lg-3">Industry Name :</label>
							<div class="col-lg-9">
								<input type="text"   pattern="[A-Za-z0-9-+\&\/ ]+" title="Please enter valid industry name" required name="name" class="form-control" value="{{$Industrys->name}}" />
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