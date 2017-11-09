@extends('layouts.recruiter')

@section('title', 'Posted Jobs')

@section('content')
<div class="container">
		<div class="row">
		
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>
			
			<div class="col-md-9 col-sm-12">
				<div class="panel panel-default form-horizontal">
					<div class="panel-heading">
						<span class="pull-right">Total : {{count($results)}}</span>
						<h3 class="panel-title">Posted Jobs</h3>
					</div>
			    </div>
				<div class="well well-sm">
					<div class="row">
						<div class="col-md-12">
						</div>
					</div>
					@include('includes.recruiter.posted-jobs.list')
				</div>
				<div class="col-md-12 text-center">
                	{{$results->render()}}
            	</div>	
			</div>
	</div>
</div>
@endsection
