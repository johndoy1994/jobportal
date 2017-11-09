@extends('layouts.recruiter')

@section('title', 'Account Settings')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.recruiter.validation_errors')
				@include('includes.recruiter.request_messages')
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Account Setting</h3>
					    </div>
					</div>
					
			</div>
		</div>
	</div>
@endsection
