@extends('layouts.mail')

@section('title', 'New job posted under your subcribed jobs')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	{{count($jobData)}} Job matches. 
	<div class="row">
	<?php $count=0 ?>
		@foreach($jobData as $job)
			<?php if($count>=10)
					continue;
			?>
			<div class="col-md-12">
				<div class="panel">
					<ul class="list-unstyled">
						<a href="{{route('job-detail',['jobId'=>$job->id])}}" style="color:green">{{$job->title}}</a>
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
							<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
							&nbsp;{{$job->getEmployer()->company_name}}
						</li>
					</ul>
				</div>
			</div>
			<?php $count++; ?>
		@endforeach
	</div>
	<div>
		<a class="btn btn-warning" href="{{$link}}" target="_blank">See all matching results</a>
	</div>
	
@endsection

@push('footer')
	{!!$footer!!}
@endpush