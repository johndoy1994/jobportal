@extends('layouts.backend')

@section('title', 'Edit admin')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Edit Admin</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-user-list',Request::all() )}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<style type="text/css">
				.btn-remove-dp{
					position: absolute;
				}
			</style>
			<div class="col-md-12">
				<form action="{{route('admin-edit-employer-change-dp')}}" method='POST' class="hide" id="frmProfilePicture" enctype="multipart/form-data">
					<input type="file" id="inpFilePP" name="image" class="hide" />
					{{csrf_field()}}
					<input type="hidden" value="{{$User->id}}" name="userId">
				</form>
				<form class="well form-horizontal" method="post" action="{{route('admin-edit-admin-post',array_merge( ['user'=> $User->id ], Request::all()) )}}">
					<legend>Edit Admin</legend>
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
						<div class="col-lg-3 text-center">
							<img  src="{{route('account-avatar-100x100',['id'=>$User->id])}}" style="background: #333;" />

							<div class="text-left" style="margin-top:10px;">
								<a href="#changeProfile" class="btn btn-primary btn-sm change-profile-picture">Change Profile Picture</a>	
								@if($image_vallid)
									<a href="{{route('admin-edit-employer-delete-image',['user_id'=>$User->id])}}" class="btn btn-danger btn-sm pull-right btn-remove-dp" style="margin-left:3px">Delete</a>	
								@endif
							</div>
						</div>
						<div class="col-md-9">
							<div class="form-group">
								<label class="control-label col-lg-3">Name :</label>
								<div class="col-lg-7">
									<input type="text" pattern="[A-Za-z0-9 ]+"  title="Please enter valid name" required name="name" value="{{$User->name}}" class="form-control" />
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-lg-3">Phone :</label>
								<div class="col-lg-7">
									<input type="text" pattern="[0-9]+" title="Please enter valid phone number" required name="mobile_number" value="{{$User->mobile_number}}" class="form-control" />
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-lg-3">Email :</label>
								<div class="col-lg-7">
									<input type="email" pattern="[A-Za-z0-9-+.@ ]+" title="Please enter valid email" required name="email_address" value="{{$User->email_address}}" class="form-control" />
								</div>
							</div>
							{{csrf_field()}}
							<div class="form-group text-center">
								<div class="col-lg-12">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
@endsection
