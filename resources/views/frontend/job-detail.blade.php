@extends('layouts.frontend')

@section('title', 'Job Detail')

@push('head')
	<meta property="og:image" content="{{route('account-avatar-100x100', ['id'=>($job && $job->employer)?$job->employer->user_id: 0])}}"/>
	<meta property="og:title" content="{{($job)?$job->getTitle():""}}"/>
	<meta property="og:url" content="{{route('job-detail',['jobId'=>($job)?$job->id:""])}}"/>
	<meta property="og:description" content="{($job)?!!$job->getDescription()!!:""}"/>
@endpush
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
				<a href="{{$jobSearchUri($route_params)}}" class="btn btn-default">< Back to search results</a>
				@if($valid_job)
					<a href="#showMap" role="show-job-on-map" class="btn btn-warning"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> Map</a>
				@endif
			<!-- </div>
			<div class="col-md-6 text-right">
				@if($previousJobId > 0)
					<a href="{{$jobSearchUri($route_params, ['jobId'=>$previousJobId], 'job-detail')}}" class="btn btn-default">< Previous Job</a>
				@endif
				@if($nextJobId > 0)
					<a href="{{$jobSearchUri($route_params, ['jobId'=>$nextJobId], 'job-detail')}}" class="btn btn-default">Next Job ></a>
				@endif-->
				@if($nextJobId > 0)
					<a href="{{$jobSearchUri($route_params, ['jobId'=>$nextJobId], 'job-detail')}}" class="btn btn-default pull-right" style="margin-left:5px;">></a>
				@endif
				@if($previousJobId > 0)
					<a href="{{$jobSearchUri($route_params, ['jobId'=>$previousJobId], 'job-detail')}}" class="btn btn-default pull-right" ><</a>
				@endif
			</div>
		</div>
		@if($valid_job)
			<br/>
			<div id="jobMap" style="height:300px; margin-bottom:20px" class="hide"></div>
			
			<div class="well">
				<div class="row">
					<div class="col-md-12">
						<img class="pull-left" src="{{route('account-avatar-100x100', ['id'=>$job->employer->user_id])}}" style="vertical-align:bottom" />
						<h3 style="margin-left: 10px">{{$job->getTitle()}}</h3>
					</div>
					<div class="col-md-12">
						<ul class="list-unstyled">
							<li>
								<h4>
									<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
									&nbsp;{{$job->getFullAddress()}}
								</h4>
							</li>
							<li>
								<h4>
									<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
									@if($job->getSalary() == 0)
										&nbsp;Salary : {{$job->getSalaryString()}}
									@else
										&nbsp;{{$job->getSalaryString()}}
									@endif
								</h4>
							</li>
							
							<li>
								<h4>
									<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
									<label style="color:#F17E1A;">{{$job->getEmployer()->company_name}}</label>
								</h4>
							</li>
							<li>
								<h4>
									<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
									&nbsp;Job Type: {{$job->getJobType()->name}}
								</h4>
							</li>
							<li>
								<h4>
									<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
									<label style="color:#158CBA;">{{$job->getPostedDayString()}}</label>
									@if($job->isExpiringIn() !==false ) 
										<label class="label label-warning">Expires in {{$job->isExpiringIn()}}</label>
									@endif
								</h4>
							</li>
							@if(trim($route_params['location']) != '')
							<li>
								<h4>
									<span class="glyphicon glyphicon-road" aria-hidden="true"></span>
									&nbsp;{{$job->getDistanceString()}} from <i>{{$route_params['location']}}</i>
								</h4>
							</li>
							@endif
						</ul>
						<hr/>
					</div>
					<div class="col-md-12">
						<div class="col-md-4">
							<a class="btn btn-sm btn-default" href="#create-alert">Create alert</a>
						</div>
						<div class="col-md-4 text-center">
							@if($jobApplication)
								<a class="btn btn-sm btn-success" href="javascript:alert('You have already applied')">Already applied on {{$jobApplication->created_at->format('d-m-Y')}}</a>
							@else
								<a class="btn btn-sm btn-primary" @if($showDaySelection) role="ask-days" data-target="{{$job->id}}" @endif href="{{route('frontend-job-apply', ['job'=>$job->id])}}">Apply Now</a>
							@endif
							<br/><br/>
						</div>
						<div class="col-md-4 text-right">
							@if($isJobSaved($job->id))
								<a data-action="remove" href="{{route('api-public-savejob', ['job'=>$job->id])}}" role="save-job" class="btn btn-primary btn-sm">Saved</a>
							@else
								<a data-action="save" href="{{route('api-public-savejob', ['job'=>$job->id])}}" role="save-job" class="btn btn-default btn-sm">Save</a>
							@endif
						</div>
					</div>
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

						@include('includes.frontend.job-matching')

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>Job Description</h4>
							</div>
							<div class="panel-body">
								{!!$job->getDescription()!!}
								<!-- @if($job->employer)
									{!! !empty($job->employer->description) ? "<p>".$job->employer->description."<p>" : "N/A" !!}
								@endif -->
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>About Employer</h4>
							</div>
							<div class="panel-body">
								@if($job->employer)
									<b>Company Name : </b>{{($job->employer)? $job->employer->getCompanyName() : ""}}<br>
									<b>Recruiter Type : </b>{{($job->employer && $job->employer->recruiterType)?$job->employer->recruiterType->getName(): ""}}<br>
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
									{!! !empty($job->employer->employeer_description) ? "<p>".$job->employer->employeer_description."<p>" : "N/A" !!}
								@endif

							</div>
						</div>
					</div>

					
					<div class="col-md-12">
						<div class="col-md-4">
							
						</div>
						<div class="col-md-4 text-center">
							@if($jobApplication)
								<a class="btn btn-sm btn-success" href="javascript:alert('You have already applied')">Already applied on {{$jobApplication->created_at->format('d-m-Y')}}</a>
							@else
								<a class="btn btn-sm btn-primary" @if($showDaySelection) role="ask-days" data-target="{{$job->id}}" @endif href="{{route('frontend-job-apply', ['job'=>$job->id])}}">Apply Now</a>
							@endif
						</div>
						<div class="col-md-4 text-right">
							@if($isJobSaved($job->id))
								<a data-action="remove" href="{{route('api-public-savejob', ['job'=>$job->id])}}" role="save-job" class="btn btn-primary btn-sm">Saved</a>
							@else
								<a data-action="save" href="{{route('api-public-savejob', ['job'=>$job->id])}}" role="save-job" class="btn btn-default btn-sm">Save</a>
							@endif
						</div>
					</div>
					<div class="col-md-12">
						<br/>
					</div>
					@include('includes.frontend.create-job-alert-form')
				</div>
			</div>

			<br/>
			<div class="well">
				<div class="row">
					<div class="col-md-12">
						<legend>Near By jobs</legend>
					</div>
					@foreach($nearByJobs as $nearByJob) 
						@if($nearByJob->id != $job->id)
						<div class="col-sm-6 col-md-3">
							<div class="thumbnail">
								<div class="caption">
									<h3><a href="{{$jobSearchUri($route_params, [ 'jobId'=>$nearByJob->id ], 'job-detail')}}">{{$nearByJob->getTitle()}}</a></h3>
									<ul class="list-unstyled">
										<li>
											<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
											&nbsp;{{$nearByJob->getFullAddress()}}
										</li>
										<li>
											<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
											@if($nearByJob->getSalary() == 0)
												&nbsp;Salary : {{$nearByJob->getSalaryString()}}
											@else
												&nbsp;{{$nearByJob->getSalaryString()}}
											@endif
										</li>
									</ul>
								</div>
							</div>
						</div>
						@endif
					@endforeach
				</div>
			</div>

			@include('includes.frontend.job-calendar-modal')

		@else
			<br/>
			<div class="row">
				<div class="col-md-12">
					<div class="well text-center">
						Sorry, we didn't found anything, please try again.<br/>
						<a href="{{$jobSearchUri($route_params, [ 'jobId'=>null ])}}">Go Back</a>
					</div>
				</div>
			</div>
		@endif
	</div>
@endsection

@push('footer')
	<script>
		var mapLoaded = false;
		function jobMap() {
			@if($job)
			$("a[role='show-job-on-map']").click(function() {
				if($("#jobMap").is(".hide")) {
					if(!mapLoaded) {
						var map = new google.maps.Map(document.getElementById('jobMap'), {
					    	center: {lat: {{$job->latitude}}, lng: {{$job->longitude}}},
					    	zoom: 15
					    });
					    var jobMarker = new google.maps.Marker({
							position: { lat: {{$job->latitude}}, lng: {{$job->longitude}} },
							map: map,
							title: "{{$job->getTitle()}}"
						});
					    mapLoaded = true;
					}
					$("#jobMap").removeClass("hide");
				} else {
					$("#jobMap").addClass("hide");
				}
			});
			@endif
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNmXGiokPzXN1lSHDSzB7qyN7BMvgUNYQ&callback=jobMap&libraries=geometry" async defer></script>
@endpush