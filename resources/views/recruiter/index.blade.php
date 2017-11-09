@extends('layouts.recruiter')

@section('title', 'Recruiter Login')

@section('content')
	<div class="container">
		@if(isset($is_recruiters) && $is_recruiters==0) 
			@include('includes.recruiter.request_messages')
			@include('includes.recruiter.validation_errors')
		@else
			<div class="alert alert-danger">
				Sorry, sign in failed. If you are an jobseeker, please sign 
				<a style="color:black;" href="{{route('account-signin')}}">here.</a>
			</div>
		@endif
		<div class="page-header">
			<h1>Recruiter Zone</h1>
		</div>
	</div>
@endsection