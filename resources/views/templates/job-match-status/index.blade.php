<div class="modal-body" id="jobMatchDetail">
	<?php
	$job = $job;
	$user = $user;
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
							<th class="text-center" style="background:green; color:white;">Item</th>
							<th class="text-center" style="background:green; color:white;">Required</th>
							<th class="text-center" style="background:green; color:white;">Candidate</th>
							<th class="text-center" style="background:green; color:white;">Match</th>
							<tr>
								<td><b>Job category :</b></td>
								<td>
									{{$job->jobTitle->getTitle()}} ({{$job->jobTitle->getCategoryTitle()}})
								</td>	
								<td>
									@if($user->experiences && isset($user->experiences[0]) && $user->experiences[0]->desired_job_title)
										{{$user->experiences[0]->desired_job_title->getTitle()}} ({{$user->experiences[0]->desired_job_title->getCategoryTitle()}})
									@endif
								</td>
								<td>
									@if($job->fieldMatch("job_title_id",null,$user))
										<span id="match_job_title_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_job_title_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
								
							</tr>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td><b>Job Type:</b></td>
								<td>{{$job->getJobTypeName()}}</td>
								<td>
								<?php $data=""; ?>
								@if($user->job_types)
									@foreach($user->job_types as $jobType)
										<?php $data .= $jobType->getTypeName().", " ?>
									@endforeach
								@endif
								{{rtrim($data,", ")}}
								</td>
								<td>
									@if($job->fieldMatch("job_type_id",null,$user))
										<span id="match_job_type_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_job_type_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
								
							</tr>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td><b>Location :</b></td>
								<td>
									{{$job->getCityName()}}
									<small>{{$job->getFullAddress()}}</small>
								</td>
								<td>
								<?php $addressArray=''; ?>
								@if($user->addresses)
									@foreach($user->addresses as $Useraddress)
										<div class="well well-sm">{{$Useraddress->getFullLine()}}</div>
									@endforeach
								@endif
								
								</td>
								<td>
									@if($job->fieldMatch('city_id',null,$user)) 
										<span id="match_city_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_city_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif

									<br/>
									
								</td>
								
							</tr>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td><b>Salary :</b></td>
								<td>
									{{$job->salary}} per {{$job->salaryType->perWord()}}
								</td>
								<td>{{$user->getDesiredSalaryString()}}	</td>
								<td>
									@if($job->fieldMatch('salary',null,$user)) 
										<span id="match_salary" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_salary" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
								
							</tr>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td><b>Education :</b></td>
								<td>
									{{$job->getEducationName()}}
								</td>
								<td>{{$user->getEducationName()}}	</td>
								<td>
									@if($job->fieldMatch('education_id',null,$user)) 
										<span id="match_education_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_education_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
								
							</tr>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td><b>Experience :</b></td>
								<td>
									{{$job->getExperienceString()}}
								</td>
								<td>{{$user->getExperienceName()}}	</td>
								<td>
									@if($job->fieldMatch('experience_id',null,$user)) 
										<span id="match_experience_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_experience_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
								
							</tr>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td><b>Level :</b></td>
								<td>
									{{$job->getExperienceLevelString()}}
								</td>
								<td>{{$user->getExperienceLevelName()}}	</td>
								<td>
									@if($job->fieldMatch('experience_level_id',null,$user)) 
										<span id="match_experience_level_id" class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span id="match_experience_level_id" class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
								
							</tr>
							<?php 	$userCertiData= $user->certificateArray($user->certificates);
									$jobCertiData= $job->jobcertificateArray($job->certificates); 
								 	$mergeArray=array_merge($jobCertiData,$userCertiData);
								 	$mergeArray=array_unique($mergeArray);
								 	$mergeArray=array_values($mergeArray);

							?>
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td rowspan="{{count($mergeArray) + 1}}"><b>Certificates :</b></td>
								@if($job->certificates()->count() == 0)
									<td>
										---	
									</td>
								@endif
								@if($user->certificates()->count() == 0)
									<td>
										---	
									</td>
								@endif
							</tr>
							
							@foreach($mergeArray as $mergeVal)
							
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td>
									@if(in_array($mergeVal,$jobCertiData))
										{{$mergeVal}}
									@else
										---	
									@endif
								</td>
								<td>
									@if(in_array($mergeVal,$userCertiData))
										{{$mergeVal}}
									@else
										---	
									@endif
								</td>
								<td>
									@if(in_array($mergeVal,$userCertiData) && in_array($mergeVal,$jobCertiData))
										<span  class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span  class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
							</tr>
							
							@endforeach

							<tr style="background:#FF9900;" class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td colspan="4"><b>Basic Match :</b>
								 	<?php
			                            $status = $user->getMatchingStatus($user,$job);
			                            echo "<label class='label label-primary pull-right'>".$status[1]."/".$status[0]."</label><br/>";
			                        ?>
								</td>
							</tr>
						</table>
					</div>
						
					<div class="table-responsive">
						<table class="table table-bordered table-match">
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<!-- <td rowspan="{{$job->skills()->count() + 1}}"><b>Requirement Tags :</b></td> -->
								@if($job->skills()->count() == 0)
									<td colspan="4">
										No skills
									</td>
								@endif
							</tr>
							<?php
								$jobSkills  = $job->getAllSkillNamesAsArray($job->skills);
								$userSkills = $user->getAllSkillNamesAsArray($user->skills);
								$allSkills 	= array_unique(array_merge($jobSkills, $userSkills));
								// echo "<pre>";
								// print_r($jobSkills);
								// print_r($userSkills);
								// print_r($allSkills);
							?>
							@foreach($allSkills as $skill)
							<tr class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td>
									{{$skill}}
								</td>
								<td class="text-center">
									@if(in_array($skill,$jobSkills))
										<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										---
									@endif
								</td>
								<td class="text-center">
									@if(in_array($skill,$userSkills))
										<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										---
									@endif
								</td>
								<td>
									@if(in_array($skill,$jobSkills) && in_array($skill,$userSkills))
										<span class="pull-right glyphicon glyphicon-ok" aria-hidden="true"></span>
									@else
										<span class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></span>
									@endif
								</td>
							</tr>
							@endforeach
							<tr style="background:#FF9900;" class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? '' : 'hide' }}">
								<td colspan="4"><b>Skills Match :</b>
									<?php
			                            $status = $user->getMatchingStatus($user,$job);
			                            echo "<label class='label label-success pull-right'>".$status[3]."/".$status[2]."</label>";
			                        ?>
								</td>
							</tr>
							<tr style="background:#FF9900;" class="tr-match {{ $job->fieldMatch('job_title_id',null,$user) ? 'hide' : '' }}">
								<td colspan="4">
									<p class="text-center">No Category Match</p>
								</td>
							</tr>
						</table>
					</div>	
				</div>
			</div>
		</div>
	<br/>
	@endif
	<!-- <center><a href="#" onClick="javascript:showApplicantDetail()" class="btn btn-default btn-sm">Show Applicant Detail</a></center> -->
</div>
