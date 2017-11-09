@extends('layouts.mail')

@section('title', 'New job posted under your subcribed jobs')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Hello,<br/>
	<div style="background: #EEE; color:black">
		<h2><a href="{{route('job-detail', ['jobId'=>$job->id])}}">{{$job->getTitle()}}</a></h2>
		<ul>
			<li>
				<b>Address :</b> {{$job->getFullAddress()}}
			</li>
			<li>
				<b>Salary :</b> {{$job->getSalaryString()}}
			</li>
			<li>
				<b>Type :</b> {{$job->getJobType()->name}}
			</li>
			<li>
				<b>Company :</b> {{$job->getEmployer()->company_name}}
			</li>
			<li>
				<b>Posted :</b> {{$job->getPostedDayString()}}
				@if($job->isExpiringIn() !==false ) 
					<br/>
					<b>Expires in</b> {{$job->isExpiringIn()}}
				@endif
			</li>
		</ul>
	</div>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush