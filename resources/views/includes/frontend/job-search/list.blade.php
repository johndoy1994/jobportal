<div class="row">
	@foreach($results as $job)
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading clearfix" style="border-bottom: 1px solid #e1e1e1">
				<div class="pull-left">
					<h3><a href="{{$jobSearchUri($route_params, ['jobId'=>$job->id], 'job-detail')}}">{{$job->title}}</a></h3>
				</div>
				<div class="pull-right">
					@if($isJobSaved($job->id))
						<a data-action="remove" href="{{route('api-public-savejob', ['job'=>$job->id])}}" role="save-job" class="btn btn-primary">Saved</a>
					@else
						<a data-action="save" href="{{route('api-public-savejob', ['job'=>$job->id])}}" role="save-job" class="btn btn-default">Save</a>
					@endif
				</div>
			</div>
			<div class="panel-body">
				<div class="col-md-9 col-sm-12">
					<ul class="list-unstyled">
						<li>
							<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
							&nbsp;{{$job->getFullAddress()}}
						</li>
						<li>
							<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
							@if($job->getSalary() == 0)
								&nbsp;<label style="color:#F17E1A;">Salary : {{$job->getSalaryString()}}</label>
							@else
								&nbsp;<label style="color:#F17E1A;">{{$job->getSalaryString()}}</label>
							@endif
						</li>
						
						<li>
							<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
							&nbsp;{{$job->getEmployer()->company_name}}
						</li>
						<li>
							<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
							&nbsp;Job Type: {{$job->getJobType()->name}}
						</li>
						<li>
							<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
							&nbsp;<label style="color:#158CBA;">{{$job->getPostedDayString()}}</label>
							@if($job->isExpiringIn() !==false ) 
								<label class="label label-warning">Expires in {{$job->isExpiringIn()}}</label>
							@endif
						</li>
						@if(trim($route_params['location']) != '')
						<li>
							<span class="glyphicon glyphicon-road" aria-hidden="true"></span>
							&nbsp;{{$job->getDistanceString()}} 
						</li>
						@endif
					</ul>
				</div>
				<div class="col-md-3 col-sm-12 text-right">
					<img src="{{route('account-avatar-100x100', ['id'=>$job->employer->user_id])}}" style="border:1px solid #ccc;padding:3px;border-radius:5px" />
				</div>
				<div class="col-md-12">
					{!!$job->getExcerptDescription()!!}
				</div>
			</div>	
		</div>
	</div>
	@endforeach
	<div class="col-md-12 text-center">
		{{$results->appends($route_params)->render()}}	
	</div>
</div>