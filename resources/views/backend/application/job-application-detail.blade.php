@extends('layouts.backend')

@section('title', 'Job Application Details')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-6">
				<h3>Job Application Details</h3>
			</div>

			<div class="col-md-6 text-right padding-top-10">
				<a href="{{route($type,Request::all() )}}" class="btn btn-primary pull-right" style="margin-left:10px;">Back</a>
				<a href="{{Route('admin-user-resumes-download',['id'=>$app->user->id])}}" class="btn btn-primary pull-right" style="margin-left:10px;">Download CV</a>
				 <button type="button" class="btn btn-primary pull-right margin-right10" onClick="printReceipt('printableArea')" title="Print Receipt">Print Profile</button>
			</div>
		</div>
		<hr/>
		@if(session('success_message'))
				<div class="alert alert-success">
					{{session('success_message')}}
				</div>
			@endif

			@if(session('error_message'))
				<div class="alert alert-danger">
					{{session('error_message')}}
				</div>
			@endif

			@if(count($errors)>0)
				<div class="alert alert-warning">
					@foreach($errors->all() as $error)
						<li>{{$error}}</li>
					@endforeach
				</div>
			@endif
		<div class="row" id="printableArea">
			<div class="col-md-12">
				@if($app && $app->job && $app->user)
					<div class="modal-header">
						<img src="{{route('account-avatar',['id'=>$app->user->id])}}" class="pull-right" style="background: #333; width: 100px; height:100px" />
						<h4 class="modal-title">{{$app->user->getName()}} </h4>
						<div><h5>Applied for : <b>{{$app->job->getTitle()}}</b></h5></div>
						<h5>Applied date : <b>{{$app->created_at->format('d-m-Y')}}</b></h5>
					</div>
					<div class="modal-body" id="applicantDetail">
						<div class="col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									
									<h4>User Details</h4>
								</div>
								<div class="panel-body">
									<div class="table-responsive">
									<table class="table table-bordered table-match">
										<tr>
											<td class="col_width40">
												<b>Location :</b><br/>
												{{$app->user->getResidanceAddress()}}<br/>
											</td>
											<td class="col_width40">
												<b>Education :</b><br/>
												{{$app->user->getEducationName()}}<br/>
											</td>
											<td class="col_width40">
												<b>Recent Job Title :</b><br/>
												{{$app->user->getRecentJobTitle()}}<br/>
											</td>
										</tr>
										
										<tr>
											<td class="col_width40">
												<b>Experience :</b><br/>
												{{$app->user->getExperienceName()}}<br/>
											</td>
											<td class="col_width40">
												<b>Experience Level :</b><br/>
												{{$app->user->getExperienceLevelName()}}<br/>
											</td>
											<td class="col_width40">
												<b>Current Salary :</b><br/>
												{{$app->user->getCurrentSalaryString()}}<br/>
											</td>
										</tr>
										
										
										<tr>
											<td class="col_width40">
												<b>Certificates :</b><br/>
												{{rtrim($app->user->getCertificatesLine(),", ")}}<br/>
											</td>
											<td class="col_width40">
												<b>Skills :</b><br/>
												{{rtrim($app->user->getSkillsLine(),", ")}}<br/>
											</td>
											<td class="col_width40">
												<b>Selected Days :</b><br/>
												@if($app->job->jobType && $app->job->jobType->day_selection == 1)
														@if(isset($app->job->jMeta()["days"]))
															@foreach($app->job->jMeta()["days"] as $date)
																{{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y') }}, 
															@endforeach
														@endif
												@endif
											</td>
										</tr>
										

										<tr>
											<td>
												<b>Desired Job Title :</b><br/>
												{{$app->user->getDesiredJobTitleName()}}<br/>
											</td>
											<td>
												<b>Desired Salary :</b><br/>
												{{$app->user->getDesiredSalaryString()}}<br/>
											</td>
											<td class="bg-success">
												<b>Status :</b><br/>
												<input type="hidden" name="id" id="id" value="{{$app->id}}">
												<input type="hidden" name="status_old" id="status_old" value="{{$app->status}}">
												<!-- {{$app->getStatus()}}<br/> -->
												<select id="status">
													<option value="in-process" {{($app->status=="in-process")? "selected" : ""}}>InProcess</option>
													<option value="accepted" {{($app->status=="accepted")? "selected" : ""}}>Accept</option>
													<option value="rejected" {{($app->status=="rejected")? "selected" : ""}}>Reject</option>
													<option value="cancelled" {{($app->status=="cancelled")? "selected" : ""}}>Cancel</option>
												</select>
											</td>
										</tr>
									</table>
								  </div>
								</div>
							</div>
						</div>
						<!-- <center><a href="#" onClick="javascript:showJobMatching()" class="btn btn-default btn-sm">Show Job Matching</a></center> -->
					</div>		

					<div class="modal-body" id="jobMatchDetail">
						<?php
						$job = $app->job;
						$user = $app->user;
						$hideModifyLink=true;
						
						if(isset($user)) {
							
						} else {
							$user = \App\MyAuth::check() ? \App\MyAuth::user() : null;
						}
						if(isset($hideModifyLink)) {

						} else {
							$hideModifyLink=false;
						}
						?>
						@if(isset($user))
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4>Your job match status</h4>
									</div>
									<div class="panel-body">
										<div class="table-responsive">
										<table class="table table-bordered table-match">
											<tr>
												<td><b>Job category :</b></td>
												<td>
													{{$job->jobTitle->getTitle()}} ({{$job->jobTitle->getCategoryTitle()}})
													@if($job->fieldMatch("job_title_id",null,$user))
														<span id="match_job_title_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_job_title_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("job_title_id",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_job_title_id" data-api="{{route('api-public-modifyprofile', ['target'=>'job_title_id', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td><b>Job Type:</b></td>
												<td>
													{{$job->getJobTypeName()}}
													@if($job->fieldMatch("job_type_id",null,$user))
														<span id="match_job_type_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_job_type_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("job_type_id",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_job_type_id" data-api="{{route('api-public-modifyprofile', ['target'=>'job_type_id', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td><b>Location :</b></td>
												<td>
													{{$job->getCityName()}}

													@if($job->fieldMatch('city_id',null,$user)) 
														<span id="match_city_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_city_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif

													<br/>
													<small>{{$job->getFullAddress()}}</small>
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("city_id",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_city_id" data-api="{{route('api-public-modifyprofile', ['target'=>'city_id', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td><b>Salary :</b></td>
												<td>
													{{$job->salary}} per {{$job->salaryType->perWord()}}
													@if($job->fieldMatch('salary',null,$user)) 
														<span id="match_salary" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_salary" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("salary",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_salary" data-api="{{route('api-public-modifyprofile', ['target'=>'salary', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td><b>Education :</b></td>
												<td>
													{{$job->getEducationName()}}
													@if($job->fieldMatch('education_id',null,$user)) 
														<span id="match_education_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_education_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("education_id",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_education_id" data-api="{{route('api-public-modifyprofile', ['target'=>'education_id', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td><b>Experience :</b></td>
												<td>
													{{$job->getExperienceString()}}
													@if($job->fieldMatch('experience_id',null,$user)) 
														<span id="match_experience_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_experience_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("experience_id",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_experience_id" data-api="{{route('api-public-modifyprofile', ['target'=>'experience_id', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td><b>Level :</b></td>
												<td>
													{{$job->getExperienceLevelString()}}
													@if($job->fieldMatch('experience_level_id',null,$user)) 
														<span id="match_experience_level_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_experience_level_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch("experience_level_id",null,$user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_experience_level_id" data-api="{{route('api-public-modifyprofile', ['target'=>'experience_level_id', 'job'=>$job->id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td rowspan="{{$job->certificates()->count() + 1}}"><b>Certificates :</b></td>
												@if($job->certificates()->count() == 0)
													<td colspan="3">
														No Certificates
													</td>
												@endif
											</tr>
											@foreach($job->certificates as $jobCertificate)
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td>
													{{$jobCertificate->getCertificateString()}}
													@if($job->fieldMatch('certificate', $jobCertificate->certificate, $user))
														<span id="match_certificate_{{$jobCertificate->id}}" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_certificate_{{$jobCertificate->id}}" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch('certificate', $jobCertificate->certificate, $user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_certificate_{{$jobCertificate->id}}" data-api="{{route('api-public-modifyprofile', ['target'=>'certificate', 'job'=>$job->id, 'data'=> $jobCertificate->certificate ])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											@endforeach
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td rowspan="{{$job->skills()->count() + 1}}"><b>Requirement Tags :</b></td>
												@if($job->skills()->count() == 0)
													<td colspan="3">
														No skills
													</td>
												@endif
											</tr>
											@foreach($job->skills as $jobSkill)
											<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
												<td>
													{{$jobSkill->getTagTitle()}}
													@if($job->fieldMatch('tag', $jobSkill->tag_id, $user))
														<span id="match_tag_{{$jobSkill->id}}" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
													@else
														<span id="match_tag_{{$jobSkill->id}}" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
													@endif
												</td>
												<td class="{{$hideModifyLink ? 'hide' : 'showa'}}">
													@if(!$job->fieldMatch('tag', $jobSkill->tag_id, $user))
														@if(!$hideModifyLink)
															<a href="#modify-profile" data-target="#match_tag_{{$jobSkill->id}}" data-api="{{route('api-public-modifyprofile', ['target'=>'tag', 'job'=>$job->id, 'data'=>$jobSkill->tag_id])}}" role="modify-profile">Modify profile</a>
														@endif
													@endif
												</td>
											</tr>
											@endforeach
										</table>
										</div>	
									</div>
								</div>
							</div>
						<br/>
						@endif
						<!-- <center><a href="#" onClick="javascript:showApplicantDetail()" class="btn btn-default btn-sm">Show Applicant Detail</a></center> -->
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
				<!-- <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div> -->

			</div>
		</div>
	</div>
@endsection
@push('footer')
<script>
function printReceipt(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>
<script type="text/javascript">
	$(document).on('change', '#status', function() {
		var status=$('#status').val();
		var status_old=$('#status_old').val();
		var id=$('#id').val();
		
		if(status=="rejected" || status=="in-process" || status=="accepted" || status=="cancelled"){
	        $.ajax({
	        	type:'post',
	            dataType:'json',
	            url:  "{{route('api-public-applicationStatus')}}",
	            data: {'status' : status,'id': id,'_token': "{{csrf_token()}}"},
	            success: function(data){
	                if(!data){
	                	$('#status').val(status_old).attr("selected", "selected");
	                	alert("Not successfully updated, please try again.");
	                }else{
	                	alert("Successfully updated");
	                }
	            }
	        });
	    }
    });
</script>
@endpush


























