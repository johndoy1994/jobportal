@extends('layouts.recruiter')

@section('title', 'Register account')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('recruiter-account-register-post')}}">
					<legend>Register an account</legend>
					<fieldset>

						@include('includes.recruiter.validation_errors')
						@include('includes.recruiter.request_messages')

						
						<div class="form-group">
							<label class="col-lg-3 control-label">Recruiter Type : *</label>
							<div class="col-lg-4">
								<select class="form-control" id="recruiterType" name="recruiterType" required="">
									<option value="">Select Recruiter</option>
									@foreach($recruiterTypes as $recruiterType)
										<option value="{{$recruiterType->id}}" {{(old('recruiterType')==$recruiterType->id)?"selected":""}}>{{$recruiterType->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Company Name : *</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" required name="company_name" value="{{old('company_name')}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Name : *</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" required name="name" value="{{old('name')}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">E-mail address : *</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" required name="email_address" value="{{old('email_address')}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Mobile Number :</label>
							<div class="col-lg-4">
								<input value="{{old('mobile_number')}}" type="text" class="form-control" name="mobile_number" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Password : *</label>
							<div class="col-lg-4">
								<input type="password" class="form-control" required name="password" value="{{old('password')}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Confirm Password : *</label>
							<div class="col-lg-4">
								<input type="password" class="form-control" required name="password_confirmation" value="{{old('password_confirmation')}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Yes i accept your <a href="{{route('api-public-view-footer-links',['pageId'=>$termAndcpndition->id])}}">{{$termAndcpndition->page_title}}</a> :</label>
							<div class="col-lg-4">
								<input type="checkbox" name="terms_accept" />
							</div>
						</div>
						{{csrf_field()}}
					</fieldset>
					<div class="form-group">
						<div class="col-lg-4 col-lg-offset-3">
							<button type="submit" value="Register" class="btn btn-primary">Register</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection