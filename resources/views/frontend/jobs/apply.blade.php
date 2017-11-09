@extends('layouts.frontend')

@section('title', $job->getTitle().' - Application')

@section('content')
<style type="text/css">
	.border-red{
		border-color:red; 
	}
</style>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.request_messages')
				@include('includes.frontend.validation_errors')
			</div>
		</div>
		@if($job->isExpired())
			@include('frontend.jobs.job_expired')
		@elseif($job->isEnded())
			@include('frontend.jobs.job_ended')
		@else
			<br/>
			<div class="row">
				<div class="col-md-6">
					<div class="well">
						<legend>
							<h3>Apply as Guest</h3>
						</legend>
						<form class="form-horizontal" enctype="multipart/form-data" method="post" action="{{route('frontend-job-applyasguest', ['job'=>$job->id])}}">
							@if($jobDays)
							<select name="days[]" multiple class="hide">
								@foreach($jobDays as $jobDay)
									<option value="{{$jobDay}}" selected>{{$jobDay}}</option>
								@endforeach
							</select>
							@endif
							<div class="form-group">
								<label class="col-lg-3 control-label">Your name *</label>
								<div class="col-lg-6">
									<input type="text" name="name" placeholder="Your name.." class="form-control" required title="Name should not be empty!" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">E-mail address *</label>
								<div class="col-lg-6">
									<input type="email" name="email_address" placeholder="e-mail address.." class="form-control" required title="E-mail address should not be empty, and it should be valid e-mail address." />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">Resume (CV) <small>(optional)</small></label>
								<div class="col-lg-6">
									<input type="file" name="cv" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-6 col-lg-offset-3">
									<button type="submit" class="btn btn-primary">
										Apply Now
									</button>
								</div>
							</div>
							{{csrf_field()}}
						</form>
					</div>
				</div>
				<div class="col-md-6">
					@if(session('is_olsUser'))
						<div class="well border-red">
					@else
						<div class="well">
					@endif
					

						<legend>
							<h3>Already has account with us ?</h3>
						</legend>
						<form class="form-horizontal" method="post" action="{{route('account-signin', ['redirect' => url()->current().'?'.Request::getQueryString() ])}}">
							<div class="form-group">
								<label class="col-lg-3 control-label">E-mail address *</label>
								<div class="col-lg-6">
									<input type="email" name="email_address" placeholder="Your registered e-mail address.." class="form-control" required title="E-mail address should not be empty, and it should be valid e-mail address." />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">Password *</label>
								<div class="col-lg-6">
									<input type="password" name="password" placeholder="Password..." class="form-control" required title="Password should not be empty." />
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-6 col-lg-offset-3">
									<a href="{{route('account-through', ['provider'=>'facebook', 'action'=>'apply', 'jobId' => $job->id ])}}" class="btn btn-xs btn-default">SignIn with Facebook</a>
									<a href="{{route('account-through', ['provider'=>'linkedin', 'action'=>'apply', 'jobId' => $job->id ])}}" class="btn btn-xs btn-default">SignIn with LinkedIn</a>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-12 col-lg-offset-3">
									<button type="submit" class="btn btn-primary">
										Sign In & Apply
									</button>
									<a class="btn btn-warning" href="{{route('account-forgotpassword')}}">Forgot password ?</a>
								</div>

							</div>
							{{csrf_field()}}
						</form>
					</div>
				</div>
			</div>
		@endif
	</div>
@endsection

@push('footer')
<script>
$(document).ready(function() {

});
</script>
@endpush