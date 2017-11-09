@extends('layouts.frontend')

@section('title', 'Account Settings')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				@include('frontend.account.job-alerts.partial')
				
			</div>
		</div>
	</div>
@endsection
