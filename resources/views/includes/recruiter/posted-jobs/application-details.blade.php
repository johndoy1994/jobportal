<div class="row">
	<div class="col-md-12">
	@if(count($results)>0)
		@foreach($results as $application)
			<div class="panel">
				<div class="panel-heading clearfix" style="border-bottom: 1px solid #e1e1e1">
					<div>
						<div class="pull-left">	
							<h3><a href="{{route('recruiter-candidatesdetails',array_merge( ['UserId'=> $application->user_id ], ['jobId'=> $application->job_id],['view'=>$view]))}}">{{$application->name}}</a></h3>
						</div>
						<div class="pull-right">	
							@if($application->is_visible_cv($application->user_id) && $application->is_uploade_resume($application->user_id))
								<a href="{{Route('recruiter-user-resumes-download',['id'=>$application->user_id])}}" class="btn btn-primary">Download CV</a>
							@endif
						</div>
					</div>
					<div class="pull-right">
					@if($view=="application")
						@if($application->status!="rejected" && $application->status!="cancelled")
							<label class='label {{$application->status=="accepted" ? "label-success" : "label-danger"}}'>{{$application->status=="accepted" ? "Candidate" : "Non Candidate"}}</label>
							@if($application->status=="accepted")
								<a href="{{route('recruiter-applicationdetails-saved', ['JobApplication'=>$application->id,'status'=>'1'])}}"  class="btn btn-danger btn-sm">Cancel</a>
							@else
							<a href="{{route('recruiter-applicationdetails-saved', ['JobApplication'=>$application->id,'status'=>'0'])}}"  class="btn btn-primary btn-sm">Save</a>		
							@endif
						@else
							<label class='label label-danger'>{{$application->status=="rejected" ? "rejected" : "cancelled"}}</label>
						@endif
					@endif
					</div>
				</div>
				<div class="panel-body">
					<div class="col-md-9 col-sm-12">
						<ul class="list-unstyled">
							<li>
								<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
								@if($application->user && $application->user->addresses && isset($application->user->addresses[0]) && $application->user->addresses[0]->city)
										City: {{$application->user->addresses[0]->city->getName()}}
								@else
									City: N/A	
								@endif
							</li>
							<li>
								<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
								Email: {{($application->email_address) ? $application->email_address : "N/A"}}
							</li>
							<li>
								<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
								phone: {{($application->mobile_number) ? $application->mobile_number : "N/A"}}
							</li>
							<li>
								<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
								Created Date: {{$application->created_at}}
							</li>
							<li>
								<?php
									$status = $application->getMatchingStatus();
									echo "<span class='glyphicon glyphicon-saved' aria-hidden='true'></span> Basic match : <label class='label label-primary'>".$status[1]."/".$status[0]."</label><br/>";
									echo "<span class='glyphicon glyphicon-saved' aria-hidden='true'></span> Requirement match : <label class='label label-success'>".$status[3]."/".$status[2]."</label>";
								?>
							</li>
							<br/>
							<a href="" role="toggle-job-details" data-target="#job-details-{{$application->user_id.$application->job_id}}">View Details</a>
							<br/>
						</ul>
					</div>
					<div class="col-md-3 col-sm-12 text-right">
						<img src="{{route('account-avatar-100x100', ['id'=>$application->user_id])}}" style="background: #333; width: 100px; height:100px"/>
					</div>

					<div class="row" id="job-details-{{$application->user_id.$application->job_id}}" style="display:none">
						<div class="col-md-12">
							@if($application && $application->job && $application->user)

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
															{{$application->user->getResidanceAddress()}}<br/>
														</td>
														<td class="col_width40">
															<b>Education :</b><br/>
															{{$application->user->getEducationName()}}<br/>
														</td>
														<td class="col_width40">
															<b>Recent Job Title :</b><br/>
															{{$application->user->getRecentJobTitle()}}<br/>
														</td>
													</tr>
													
													<tr>
														<td class="col_width40">
															<b>Experience :</b><br/>
															{{$application->user->getExperienceName()}}<br/>
														</td>
														<td class="col_width40">
															<b>Experience Level :</b><br/>
															{{$application->user->getExperienceLevelName()}}<br/>
														</td>
														<td class="col_width40">
															<b>Current Salary :</b><br/>
															{{$application->user->getCurrentSalaryString()}}<br/>
														</td>
													</tr>
													
													
													<tr>
														<td class="col_width40">
															<b>Certificates :</b><br/>
															{{rtrim($application->user->getCertificatesLine(),", ")}}<br/>
														</td>
														<td class="col_width40">
															<b>Skills :</b><br/>
															{{rtrim($application->user->getSkillsLine(),", ")}}<br/>
														</td>
														<td class="col_width40">
															<b>Selected Days :</b><br/>
															@if($application->job->jobType && $application->job->jobType->day_selection == 1)
																	@if(isset($application->job->jMeta()["days"]))
																		@foreach($application->job->jMeta()["days"] as $date)
																			{{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y') }}, 
																		@endforeach
																	@endif
															@endif
														</td>
													</tr>
													

													<tr>
														<td>
															<b>Desired Job Title :</b><br/>
															{{$application->user->getDesiredJobTitleName()}}<br/>
														</td>
														<td>
															<b>Desired Salary :</b><br/>
															{{$application->user->getDesiredSalaryString()}}<br/>
														</td>
														<td>
															<b>Status :</b><br/>
															{{$application->getStatus()}}<br/>
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
									$job = $application->job;
									$user = $application->user;
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
					

					<div class="col-md-12">
						<a href="{{route('recruiter-conversation',['conversation_ref'=>$application->getRef($application->user_id)])}}" class="btn btn-primary">Chat</a>
						<a href="" data-toggle="modal" data-target="#myModal" data-id="{{$application->user_id}}" class="btn btn-success msg">Msg</a>
						@if($application->mobile_number)
							<a href="tel:{{$application->mobile_number}}" class="btn btn-warning">Call</a>
						@endif	
						@if($application->email_address)
							<a href="" data-toggle="modal" data-target="#myModal1" data-id="{{$application->id}}" data-email="{{$application->email_address}}" class="btn btn-info btn-email">Email</a>
							<!-- <a href="mailto:{{$application->email_address}}" class="btn btn-info">Email</a> -->
						@endif
						<a href="{{route('recruiter-applicationdetails-delete', ['JobApplication'=>$application->id])}}" onclick="return confirm('Are you sure to delete this application ?')" class="btn btn-danger">Delete</a>
					</div>	
			</div>
		</div>
		@endforeach
		<div class="col-md-12 text-center">
			
		</div>
	@else

		@if($type=="application")
			No application founds....
		@else
			No candidate founds....
		@endif	
	@endif
</div>
</div>