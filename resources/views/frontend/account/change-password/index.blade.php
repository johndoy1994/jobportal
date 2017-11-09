@extends('layouts.frontend')

@section('title', 'Change Password')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				<form class="form-horizontal" method="post" action="{{route('account-save-changepassword')}}">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Change Password</h3>
					    </div>
						<div class="panel-body">
							
							<div class="form-group">
								<label class="control-label col-lg-3">Current Password *</label>
								<div class="col-lg-4">
									<input type="password" class="form-control" name="password" placeholder="Your Current password..."  required />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">New password *</label>
								<div class="col-lg-4">
									<input type="password" class="form-control" name="password_new" placeholder="New password..."  required />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Confirm Password *</label>
								<div class="col-lg-4">
									<input type="password" class="form-control" name="password_new_confirmation" placeholder="Confirm Password..."  required="" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="form-group">
								{{csrf_field()}}
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
