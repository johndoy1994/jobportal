<?php
$jobAlert = $job->isAlertSet(Request::get("keywords"),10);
?>
<div class="col-md-12">
	<a name="create-alert"></a>
	<form class="well well-sm text-center" @if(!$jobAlert) role="create-alert" @endif>
		@if(!$jobAlert)
		<input type="hidden" name="job_categories_id" value="{{$job->getCategoryId()}}" />
		<input type="hidden" name="job_title_id" value="{{$job->getSubCategoryId()}}" />
		<input type="hidden" name="keywords" value="{{Request::get('keywords')}}" />
		<input type="hidden" name="city" value="{{$job->getCityId()}}" />
		<input type="hidden" name="radius" value="10" />
		<div class="loader hide">
			<h4>Creating alert, please wait... <img src="{{asset('imgs/loader.gif')}}" /></h4>
		</div>
		@endif
		<div class="content">
			<h4>
				Alert me to new jobs like this 
				<b>{{$job->getTitle()}}</b> 
				in 
				<b>{{ $job->addresses ? $job->addresses->getCityName().' + ' : '' }} 10 miles</b>
			</h4>
			<br/>
			@if($isUserLive) 
				<div class="form-group">
					<label>E-mail address : {{$liveUser->email_address}} </label>
					<input type="hidden" name="email_address" value="{{$liveUser->email_address}}" />
				</div>
			@else
				<div class="form-group">
					<label>E-mail address : </label>
					<input type="text" name="email_address" class="form-control" placeholder="Your e-mail address" required />
				</div>
			@endif
			@if($jobAlert)
				<h4>You have already set alert on this kind of jobs!!</h4>
			@else
				<br/>
				<button type="submit" class="btn btn-sm btn-primary">Create alert</button>
			@endif
		</div>
	</form>
</div>