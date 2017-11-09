@if($app && $app->job && $app->user)
	<div class="modal-header">
		<h4 class="modal-title">{{$app->user->getName()}} <small>applied for <b>{{$app->job->getTitle()}}</b> on <b>{{$app->created_at->format('d-m-Y')}}</b></small></h4>
	</div>
	<div class="modal-body hide" id="jobMatchDetail">
		<?php
		$job = $app->job;
		$user = $app->user;
		$hideModifyLink=true;
		?>
		@include('includes.frontend.job-matching')
		<br/>
		<center><a href="#" onClick="javascript:showApplicantDetail()" class="btn btn-default btn-sm">Show Applicant Detail</a></center>
	</div>
	<div class="modal-body" id="applicantDetail">
		<div class="row">
			<div class="col-md-4">
				<b>Full Name :</b><br/>
				{{$app->user->getPersonTitle()}} {{$app->user->getName()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Email address :</b><br/>
				{{$app->user->getEmailAddress()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Mobile Number :</b><br/>
				{{$app->user->getMobileNumber()}}<br/>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-4">
				<b>Location :</b><br/>
				{{$app->user->getResidanceAddress()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Education :</b><br/>
				{{$app->user->getEducationName()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Recent Job Title :</b><br/>
				{{$app->user->getRecentJobTitle()}}<br/>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-4">
				<b>Experience :</b><br/>
				{{$app->user->getExperienceName()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Experience Level :</b><br/>
				{{$app->user->getExperienceLevelName()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Current Salary :</b><br/>
				{{$app->user->getCurrentSalaryString()}}<br/>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-6">
				<b>Certificates :</b><br/>
				{{$app->user->getCertificatesLine()}}<br/>
			</div>
			<div class="col-md-6">
				<b>Skills :</b><br/>
				{{$app->user->getSkillsLine()}}<br/>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-4">
				<b>Desired Job Title :</b><br/>
				{{$app->user->getDesiredJobTitleName()}}<br/>
			</div>
			<div class="col-md-4">
				<b>Desired Salary :</b><br/>
				{{$app->user->getDesiredSalaryString()}}<br/>
			</div>
			<div class="col-md-4 bg-success">
				<b>Status :</b><br/>
				{{$app->getStatus()}}<br/>
			</div>
		</div>
		@if($app->job->jobType && $app->job->jobType->day_selection == 1)
		<br/>
		<div class="row">
			<div class="col-md-12">
				<b>Selected Days :</b><br/>
				@if(isset($app->job->jMeta()["days"]))
					@foreach($app->job->jMeta()["days"] as $date)
						{{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y') }}, 
					@endforeach
				@endif
			</div>
		</div>
		@endif
		<br/>
		<center><a href="#" onClick="javascript:showJobMatching()" class="btn btn-default btn-sm">Show Job Matching</a></center>
	</div>	
@else
	<div class="modal-body">
		<center>
			<p class="bg-danger">
				Job application not found, please try again.
			</p>
		</center>
	</div>
@endif
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<script>
function showJobMatching() {
	$("#applicationModal").find("#jobMatchDetail").removeClass('hide');
	$("#applicationModal").find("#applicantDetail").addClass('hide');
}
function showApplicantDetail() {
	$("#applicationModal").find("#jobMatchDetail").addClass('hide');
	$("#applicationModal").find("#applicantDetail").removeClass('hide');	
}
</script>