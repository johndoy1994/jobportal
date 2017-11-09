<div class="row">
	<div class="col-md-12">
	@foreach($results as $job)
		<div class="panel">
			<div class="panel-heading clearfix" style="border-bottom: 1px solid #e1e1e1">
				<div class="pull-left">
					<h3><a href="{{route('recruiter-postedjob-details',['job'=>$job->id])}}">{{$job->title}}</a></h3>
				</div>
				<div class="pull-right">
					<!-- @if($job->status=="active")
						<a class="btn btn-danger" href="{{route('recruiter-active-inactive-job-post', array_merge( ['JobId'=> $job->id ],['action'=>'inactive']) )}}">Inactive</a>
					@endif
					
					@if($job->status=="inactive") 
						<a class="btn btn-success" href="{{route('recruiter-active-inactive-job-post', array_merge( ['JobId'=> $job->id ],['action'=>'active']) )}}">Active</a>
					@endif -->
					<?php
					$days = $job->expired_days;
					?>
					<a href="{{route('recruiter-job',array_merge( ['mode'=>'edit','job'=>$job->id] ))}}"  class="btn btn-primary">Edit</a>
					<a href="{{route('recruiter-job-delete',['job'=>$job->id])}}" onclick="return confirm('Are you sure to delete this Job ?')" class="btn btn-danger">Delete</a>	
					@if($days<0 || $job->isEnded())
						@if($job->isEnded() && $days<0)
							<label class="label label-warning">Ended and expire</lable>
						@elseif($job->isEnded())
							<label class="label label-warning">Ended</lable>
						@elseif($days<0)
							<label class="label label-warning">Expired</lable>
						@endif
						@else
							<div class="btn-group">
								<a class="btn {{($job->status=='active')? 'btn-primary' : 'btn-default'}}" href="{{route('recruiter-active-inactive-job-post', array_merge( ['JobId'=> $job->id ],['action'=>'active']) )}}">On</a>
								<a class="btn {{($job->status=='active')? 'btn-default' : 'btn-primary'}}" href="{{route('recruiter-active-inactive-job-post', array_merge( ['JobId'=> $job->id ],['action'=>'inactive']) )}}">Off</a>
							</div>
						@endif
				</div>
			</div>
			<div class="panel-body">
				<div class="col-md-9 col-sm-12">
					<ul class="list-unstyled">
						<li>
							<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
							{{$job->full_address}}
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
							{{$job->jobType_name}}
						</li>
						<li>
							<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
							{{$job->company_name}}
						</li>
						<li>
							<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
							&nbsp;{{$job->getPostedDayString()}}
							@if($job->isExpiringIn() !==false ) 
								<label class="label label-warning">Expires in {{$job->isExpiringIn()}}</label>
							@endif
						</li>
						<li>
							<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
							<a href="{{Route('recruiter-application-details',['job'=>$job->id])}}"> Application: {{$job->application_count()}}</a>
						</li>
					</ul>
				</div>
				<div class="col-md-3 col-sm-12 text-right">
					<img src="{{route('account-avatar-100x100', ['id'=>$job->userId])}}" style="background: #333; width: 100px; height:100px"/>
				</div>
				<div class="col-md-12">
					{!!$job->description!!}
				
				@if($job->isRepostable()[0])
					<a href="{{route('recruiter-job',array_merge( ['mode'=>'repost','job'=>$job->id] ))}}" class="btn btn-warning">Repost</a>
				@endif
				@if($job->isRenewable()[0]) 
					<a href="{{route('recruiter-job',array_merge( ['mode'=>'renew','job'=>$job->id] ))}}" class="btn btn-success">Renew</a>
				@endif	
				<a href="{{Route('recruiter-application-details',['job'=>$job->id])}}" class="btn btn-danger">view application</a>
			</div>	
		</div>
	</div>
	@endforeach
	<div class="col-md-12 text-center">
		
	</div>
</div>
</div>