@extends('layouts.frontend')

@section('title', 'Saved Jobs')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.request_messages')
				@include('includes.frontend.validation_errors')
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				@if(isset($isSubscribedJobs))
					<legend>Subscribed Jobs</legend>
				@else
					<legend>Saved Jobs</legend>
				@endif
				<div class="well">
					@if(count($jobs) == 0)
						@if(isset($isSubscribedJobs))
							<center>
								No subscribed jobs.
							</center>
						@else
							<center>
								<b>To save a job, click <a class="btn btn-default"><span class="glyphicon glyphicon-heart" aria-hidden="true"></span> Save</a> button on job you like.</b>
								<br/>The jobs you saved will be stored here.
							</center>
						@endif
					@else
						@if(isset($isSubscribedJobs))
							<legend>
								You have {{count($jobs)}} subscribed job(s)								
								@if(isset($jobAlert))
									<small class="pull-right">
										Criteria : <u>{{$jobAlert->getAlertTitle()}}</u>
									</small>
								@endif
							</legend>
						@else
							<legend>You have {{count($jobs)}} saved job(s)</legend>
						@endif
						@foreach($jobs as $job)
							<div class="panel">
								<div class="panel-heading clearfix">
									<h4>
										<a href="{{$jobSearchUri([],['jobId'=>$job->id], 'job-detail')}}">
											<img src="{{route('account-avatar-100x100', ['id'=>$job->employer->user_id])}}"/>
											{{$job->getTitle()}}
											
										</a>
										@if(isset($isSubscribedJobs))
											@if(!$job->isReadedByUser())
												<small><label class="label label-success">NEW</label></small>
											@endif
										@endif 
										<div class="pull-right">
											<div class="btn-group">
												@if($jobApp=$job->isApplied())
													<a class="btn btn-success" href="javascript:alert('You have already applied to this job!!')">Already applied on {{$jobApp->created_at->format('d-m-Y')}}</a>
												@else
													<a @if($job->jobType && $job->jobType->day_selection == 1) role="ask-days" data-target="{{$job->id}}" @endif href="{{route('frontend-job-apply', ['job'=>$job->id])}}" class="btn btn-primary">Apply</a>
												@endif

												@if(isset($isSubscribedJobs))
													
												@else
													<a href="{{route('api-public-savejob', ['job'=>$job->id])}}" role='save-job' data-action='remove' data-refresh='true' class="btn btn-default pull-right">Remove</a>
												@endif
											</div>
										</div>
									</h4>
								</div>
								<div class="panel-body clearfix">
									<div class="col-md-6">
										<ul class="list-unstyled">
											<li>
												<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
												&nbsp;{{$job->getFullAddress()}}
											</li>
											<li>
												<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
												@if($job->getSalary() == 0)
													&nbsp;Salary : {{$job->getSalaryString()}}
												@else
													&nbsp;{{$job->getSalaryString()}}
												@endif
											</li>
											<li>
												<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
												&nbsp;{{$job->getJobType()->name}}
											</li>
											<li>
												<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
												&nbsp;{{$job->getEmployer()->company_name}}
											</li>
											<li>
												<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
												&nbsp;{{$job->getPostedDayString()}}
												@if($job->isExpiringIn() !==false ) 
													<label class="label label-warning">Expires in {{$job->isExpiringIn()}}</label>
												@endif
											</li>
										</ul>
									</div>
									<div class="col-md-6">

									</div>
									<div class="col-md-12">
										<a href="" role="toggle-job-details" data-target="#job-details-{{$job->id}}">View Details</a>
										<br/>
										<div id="job-details-{{$job->id}}" style="display:none">
											<div class="col-md-12">
												<div class="panel panel-default">
													<div class="panel-heading">
														<h4>About Job</h4>
													</div>
													<div class="panel-body">
														<b>Title :</b> {!!$job->getTitle()!!}<br/>
														<b>Vacancies :</b> {{$job->vacancies}}<br/>
														@if($job->jobTitle)
															<b>Job Category :</b> {{$job->jobTitle->getTitle()}} 
															@if($job->jobTitle->category)
																({{$job->jobTitle->category->getName()}})
															@endif
														@endif<br/>
														<b>Location :</b> {{$job->getFullAddress()}}<br/>
														@if($job->employer)
															<b>Employer :</b> {{$job->employer->getCompanyName()}} ({{$job->employer->getEmailAddress()}})<br/>
														@endif
														<b>Salary :</b> {{$job->getSalaryString()}}<br/>
														<b>Benefits :</b> {{$job->benefits}}<br/>
														@if($job->starting_date)
															<b>Start Date :</b> from {{$job->starting_date->format('d-m-Y')}}
															@if($job->ending_date)
																to {{$job->ending_date->format('d-m-Y')}}
															@endif
															<br/>
														@endif
														@if(($startTime = \App\Utility::parseCarbonDate($job->work_schedule_from, "H:i:s")) && ($toTime = \App\Utility::parseCarbonDate($job->work_schedule_to, "H:i:s")))
															<b>Work Schedule :</b> {{$startTime->format('h:i A')}} to {{$toTime->format("h:i A")}}<br/>
														@endif
														@if($job->created_at)
															<b>Posted Date :</b> {{$job->created_at->format('d-m-Y')}} ({{$job->getPostedDayString()}})<br/>
														@endif
														@if($job->updated_at)
															<b>Last Updated :</b> {{$job->updated_at->format('d-m-Y')}}
														@endif
													</div>
												</div>
												<div class="panel panel-default">
													<div class="panel-heading">
														<h4>Job Requirements</h4>
													</div>
													<div class="panel-body">
														@if($job->education)
															<b>Education :</b> {{$job->education->getName()}}<br/>
														@endif
														@if($job->experience)
															<b>Experience :</b> {{$job->experience->getName()}}<br/>
														@endif
														@if($job->experience_level)
															<b>Experience Level :</b> {{$job->experience_level->getName()}}<br/>
														@endif
														@if($job->certificates)
															<b>Certificates :</b> 
															@foreach($job->certificates as $jobCertificate)
																{{$jobCertificate->certificate}}, 
															@endforeach
															<br/>
														@endif
														@if($job->skills)
															<b>Skills :</b> 
															@foreach($job->skills as $jobSkill)
																@if($jobSkill->tag)
																	{{$jobSkill->tag->getName()}}, 
																@endif
															@endforeach
															<br/>
														@endif
													</div>
												</div>
												<div class="panel panel-default">
													<div class="panel-heading">
														<h4>Job Description</h4>
													</div>
													<div class="panel-body">
														{!!$job->getDescription()!!}
													</div>
												</div>

												<div class="panel panel-default">
													<div class="panel-heading">
														<h4>About Employer</h4>
													</div>
													<div class="panel-body">
														@if($job->employer)
															<b>Company Name : </b>{{$job->employer->getCompanyName()}}<br>
															<b>Recruiter Type : </b>{{$job->employer->recruiterType->getName()}}<br>
														@endif

														@if($job->employer && $job->employer->user && $job->employer->user->addresses[0] && $job->employer->user->addresses[0])
															<b>City :</b> {{$job->employer->user->addresses[0]->getCityName()}}<br/>
															<b>State :</b> {{$job->employer->user->addresses[0]->getStateName()}}<br/>
															<b>Country :</b> {{$job->employer->user->addresses[0]->getCountryName()}}<br/>
														@else
															<b>Location </b>: N/A<br>
														@endif

														@if($job->employer)
															<b>Company Description : </b>
															{!! !empty($job->employer->description) ? "<p>".$job->employer->description."<p>" : "N/A" !!}
														@endif

													</div>
												</div>
											</div>
											@include('includes.frontend.create-job-alert-form')
										</div>
									</div>
								</div>
							</div>
						@endforeach
					@endif
				</div>
			</div>
		</div>
	</div>
	@include('includes.frontend.job-calendar-modal')
@endsection

@push('footer')
<script>
$(document).ready(function() {
	$("a[role='toggle-job-details']").click(function() {
		var target=$(this).attr('data-target');
		$(target).toggle();
		return false;
	});
});
</script>
@endpush