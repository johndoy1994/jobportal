@extends('layouts.frontend')

@section('title', 'User Resume')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				<form action="{{route('account-user-resumes-post')}}" method='POST' id="userResume" enctype="multipart/form-data">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">User Resume</h3>
					    </div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label col-lg-3">Resume *</label>
								<div class="col-lg-4">
									<input type="file" id="resume" name="resume"/>
								</div>
							</div>
						</div>

						@if($userResume)
						<?php $filename = 'resumes/'.$userResume->filename; ?>
						
							@if(Storage::exists($filename))
							<div class="panel-body">
								<div class="form-group">
									<label class="control-label col-lg-3"></label>
									<div class="col-lg-4">
										<a href="{{route('account-user-resumes-download', ['id'=>$userResume->user_id])}}" class="thumbnail" target="blank"><img  src="{{Route('resume-image')}}" width="100" height="100"></a>

									</div>
								</div>
							</div>
							@endif
						@endif

					</div>

					<div class="row">
						<div class="col-md-12 text-center">
							<div class="form-group">
								{{csrf_field()}}
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
