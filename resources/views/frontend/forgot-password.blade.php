@extends('layouts.frontend')

@section('title', 'Forgot password ?')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<form class="well form-horizontal" method="post" action="{{route('account-forgotpassword-post')}}">
					<legend>Reset password</legend>
					<fieldset>

						<div class="form-group">
							<label class="col-lg-3 control-label">E-mail address : *</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="email_address" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label"></label>
							<div class="col-lg-8">
								- OR -
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Mobile Number : *</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="mobile_number" />
							</div>
						</div>

						{{csrf_field()}}
					</fieldset>
					<div class="form-group">
						<div class="col-lg-8 col-lg-offset-3">
							<div class="btn-toolbar">
								<div class="btn-group">
									<button type="submit" value="Register" class="btn btn-primary btn-sm">Reset Password</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-6">
				<form class="well form-horizontal" method="post" action="{{route('account-forgotpassword-post')}}">
					<legend>Already have secret code ?</legend>
					<fieldset>

						<div class="form-group">
							<label class="col-lg-3 control-label">Code : *</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="code" />
							</div>
						</div>

						{{csrf_field()}}
					</fieldset>
					<div class="form-group">
						<div class="col-lg-8 col-lg-offset-3">
							<div class="btn-toolbar">
								<div class="btn-group">
									<button type="submit" value="Register" class="btn btn-primary btn-sm">Next</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection