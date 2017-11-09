@extends('layouts.frontend')

@section('title', 'Verify your account')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('account-verification-post')}}">
					<legend>Account Verification</legend>
					<fieldset>

						@include('includes.frontend.validation_errors')
						@include('includes.frontend.request_messages')



						@if($user->verification)
							
							@if($user->verification->status == \App\Models\UserVerification::NOT_VERIFIED)
								<div class="form-group">
									<label class="col-lg-7 text-center">
										<strong>Please verify your account</strong>
									</label>
								</div>

								<div class="form-group">
									<label class="col-lg-3 control-label">
										<strong>Verification method : </strong>
									</label>
									<div class="col-lg-4">
										<input type="text" readonly value="{{ucwords($user->verification->method)}}" class="form-control" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-lg-3 control-label">
										<strong>Verification Code : </strong>
									</label>
									<div class="col-lg-4">
										<input type="text" name="verification_code" class="form-control" placeholder="Paste verification code here..." />
									</div>
								</div>

								<div class="form-group">
									<div class="col-lg-4 col-lg-offset-3">
										<button name="action" type="submit" class="btn btn-primary btn-xs" value="verify">Verify Now</button>
										<button name="action" type="submit" class="btn btn-warning btn-xs" value="resend">Re-Send Verification Code</button>
									</div>
								</div>
							@else

								<div class="alert alert-success">
									You have verified account, thank you.
								</div>
								
								<a class="btn btn-primary" href="{{route('job-search',array_merge( ['keywords'=>'','location'=>'','radius'=>'0']))}}">View Jobs</a>
								<a class="btn btn-warning"  href="{{route('account-myprofile')}}">Complete Profile</a>
								

							@endif

						@else
							
							<div class="form-group">
								<label class="col-lg-7 text-center">
									<strong>Please verify your account</strong>
								</label>
							</div>
							@if($user->email_address)
							<div class="form-group">
								<label for="inputEmail" class="control-label col-lg-3">Verify by E-mail :</label>
								<div class="col-lg-4">
									<input checked type="radio" id="inputEmail" name="verify-by" value="email" />
								</div>
							</div>
							@endif

							@if($user->mobile_number > 0)
								<div class="form-group">
									<label id="inputMobile" class="control-label col-lg-3">Verify by Mobile number :</label>
									<div class="col-lg-4">
										<input {{($user->email_address)? "" : "checked" }} type="radio" id="inputMobile" name="verify-by" value="mobile" />
									</div>
								</div>
							@endif
							
							<div class="form-group">
								<div class="col-lg-4 col-lg-offset-3">
									<button name="action" type="submit" class="btn btn-primary" value="send">Send Verification Code</button>
								</div>
							</div>

						@endif
						{{csrf_field()}}
					</fieldset>
				</form>
			</div>
		</div>
	</div>
@endsection