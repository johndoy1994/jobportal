@extends('layouts.backend')

@section('title', 'Change Password')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Change Password</h3>
			</div>
			<!-- <div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-country', Request::all())}}" class="btn btn-primary pull-right">Back</a>
			</div> -->
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-change-password-post')}}">
					<legend>Change Password</legend>
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
							<label class="control-label col-lg-3">Current Password :</label>
							<div class="col-lg-5">
								<input type="password" required name="currentPassword" value="" class="form-control" autocomplete="off" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-3 control-label">New Password :</label>
							<div class="col-lg-5">
								<input type="password" required class="form-control" name="password" value="" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Confirm Password :</label>
							<div class="col-lg-5">
								<input type="password" required class="form-control" name="password_confirmation" value="" />
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