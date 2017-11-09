@extends('layouts.frontend')

@section('title', "Job Application Sent")

@section('content')
	
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<div class="well">
					<legend><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
					@if($alreadyApplied)
						You've already applied on this job application
					@else
						Application Complete
					@endif
					</legend>
					<p>Your application for <b>{{$jobApplication->job->getTitle()}}</b> has been sent to <b>{{$jobApplication->job->getEmployerName()}}</b> at <a href="mailto:{{$jobApplication->job->getEmployerEmailAddress()}}">{{$jobApplication->job->getCompanyName()}}</a></p>
					<p><strong>Applied on :</strong> {{$jobApplication->appliedOnString()}} ({{$jobApplication->daysAgoAppliedString()}})</p>
				</div>
				<a href="{{$jobSearchUri( session()->has('search_history') ? session()->get('search_history') : [], [] )}}" class="btn btn-primary">Back to Jobs</a>
			</div>
			<div class="col-md-4">
				<div class="well">
					<legend>What happens next ?</legend>
					<p>
						You will receive an email from us confirming your application. We have also saved this job in your applications for future reference. If you’re successful, {{$jobApplication->job->getEmployerName()}} will be in contact with you.
					</p>
				</div>
			</div>
		</div>
	</div>

@endsection
