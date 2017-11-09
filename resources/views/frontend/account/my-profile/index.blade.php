@extends('layouts.frontend')

@section('title', 'My Profile')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				<form class="form-horizontal" method="post" id="frmProfile" action="{{route('account-save-myprofile')}}">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">My Profile</h3>
					    </div>
						<div class="panel-body">
							<div class="form-group @if($errors->has('title')) has-error @endif">
								<label class="control-label col-lg-3">Title *</label>
								<div class="col-lg-4">
									<select name="title" class="form-control">
										<option value="0">- Select Title -</option>
										@foreach($person_titles as $person_title)
											@if($values["person_title_id"] == $person_title->id)
												<option value="{{$person_title->id}}" selected>{{$person_title->person_title}}</option>
											@else
												<option value="{{$person_title->id}}">{{$person_title->person_title}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('first_name')) has-error @endif">
								<label class="control-label col-lg-3">First Name *</label>
								<div class="col-lg-4">
									<input required title="First name is must!!" type="text" class="form-control" name="first_name" placeholder="Your first name..." value="{{ $values['first_name'] }}" required />
								</div>
							</div>
							<div class="form-group @if($errors->has('surname')) has-error @endif">
								<label class="control-label col-lg-3">Surname *</label>
								<div class="col-lg-4">
									<input required title="Surname is must!!" type="text" class="form-control" name="surname" placeholder="Your surname..." value="{{ $values['surname'] }}" required />
								</div>
							</div>
							<div class="form-group @if($errors->has('email_address')) has-error @endif">
								<label class="control-label col-lg-3">Email *</label>
								<div class="col-lg-4">
									<input required title="Email address is must!!" type="text" class="form-control" name="email_address" placeholder="Your e-mail address..." value="{{ $values['email_address'] }}" required="" />
								</div>
							</div>
							<div class="form-group @if($errors->has('mobile_number')) has-error @endif">
								<label class="control-label col-lg-3">Mobile Number</label>
								<div class="col-lg-4">
									<input type="text" title="Mobile number is must!!" class="form-control" name="mobile_number" placeholder="Your mobile number..." value="{{ $values['mobile_number'] }}" />
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Residance Address</h3>
					    </div>
						<div class="panel-body">
							<div class="form-group @if($errors->has('country')) has-error @endif">
								<label class="control-label col-lg-3">Country *</label>
								<div class="col-lg-4">
									<select name="country" id="country" class="form-control">
										<option value=0>- Select Country -</option>
										@foreach($countries as $country)
											@if($values["country_id"] == $country->id)
												<option value="{{$country->id}}" selected>{{$country->getName()}}</option>
											@else
												<option value="{{$country->id}}">{{$country->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('state')) has-error @endif">
								<label class="control-label col-lg-3">Province/State *</label>
								<div class="col-lg-4">
									<select name="state" id="state" class="form-control">
										<option value=0>- Select State -</option>
										@if($values["state"])
											<option value="{{$values['state'][0]}}" selected>{{$values['state'][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('city')) has-error @endif">
								<label class="control-label col-lg-3">City *</label>
								<div class="col-lg-4">
									<select name="city" id="city" class="form-control">
										<option value=0>- Select City -</option>
										@if($values["city"])
											<option value="{{$values['city'][0]}}" selected>{{$values['city'][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('street')) has-error @endif">
								<label class="control-label col-lg-3">Street</label>
								<div class="col-lg-4">
									<input type="text" class="form-control" name="street" value="{{$values['street']}}" />
								</div>
							</div>
							<div class="form-group @if($errors->has('postalcode')) has-error @endif">
								<label class="control-label col-lg-3">Postal Code </label>
								<div class="col-lg-4">
									<input type="text" class="form-control" name="postalcode" value="{{$values['postalcode']}}" />
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Qualification(s)</h3>
					    </div>
						<div class="panel-body">
							<div class="form-group @if($errors->has('education')) has-error @endif">
								<label class="control-label col-lg-3">Highest level of education *</label>
								<div class="col-lg-4">
									<select name="education" class="form-control" title="Education is must!!">
										<option value=0>- Select Education -</option>
										@foreach($educations as $education)
											<option value="{{$education->id}}" @if($values['education_id']==$education->id) selected @endif >{{$education->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('certificates')) has-error @endif">
								<label class="control-label col-lg-3">Certificates</label>
								<div class="col-lg-4">
									<input type="text" name="certificates" placeholder="Certificates separated by commas" class="form-control" value="{{$values['certificates']}}" />
								</div>
							</div>
							<div class="form-group @if($errors->has('recent_job_title')) has-error @endif">
								<label class="control-label col-lg-3">Current/most recent job title *</label>
								<div class="col-lg-4">
									<input type="text" name="recent_job_title" class="form-control" value="{{$values['recent_job_title']}}" title="Current/most recent job title is must!!" required />
								</div>
							</div>
							<div class="form-group @if($errors->has('current_salary_type')) has-error @endif">
								<label class="control-label col-lg-3">Current/most recent salary *</label>
								<div class="col-lg-2">
									<select class="form-control" name="current_salary_type" data-child="current_salary_range">
										<option value="">- Type -</option>
										@foreach($salaryTypes as $salaryType)
											<option value="{{$salaryType->id}}" @if($values['current_salary_type_id'] == $salaryType->id) selected @endif>{{$salaryType->getTypeName()}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-lg-2">
									<select class="form-control" name="current_salary_range">
										<option value="">- Range -</option>
										@if($values["current_salary_range"])
											<option value="{{$values["current_salary_range"][0]}}" selected>{{$values["current_salary_range"][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('desired_job_category')) has-error @endif">
								<label class="control-label col-lg-3">Desired job category *</label>
								<div class="col-lg-4">
									<select name="desired_job_category" class="form-control">
										<option value=0>- Select Desired Job Category -</option>
										@foreach($jobCategories as $jobCategory)
											@if($values['desired_job_category_id'] == $jobCategory->id)
												<option value="{{$jobCategory->id}}" selected>{{$jobCategory->getName()}}</option>
											@else
												<option value="{{$jobCategory->id}}">{{$jobCategory->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('desired_job_title')) has-error @endif">
								<label class="control-label col-lg-3">Desired job title *</label>
								<div class="col-lg-4">
									<select name="desired_job_title" class="form-control">
										<option value=0>- Select Desired Job Title -</option>
										@if($values['desired_job_title'])
											<option value="{{$values['desired_job_title'][0]}}" selected>{{$values['desired_job_title'][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('skills')) has-error @endif">
								<label class="control-label col-lg-3">My Skills :</label>
								<div class="col-lg-4">
									<div class="checkbox my-skills">
										@foreach($values["job_title_skills"] as $skill)
											<?php $isSelected = false; ?>
											@foreach($values["skills"] as $sel_skill)
												@if($sel_skill[0] == $skill->id)
													<?php $isSelected = true; ?>
												@endif
											@endforeach
											<label style="margin: 5px"><input type="checkbox" name="skills[]" value="{{$skill->id}}" @if($isSelected) checked @endif> {{$skill->getName()}}</label>
										@endforeach
									</div>
								</div>
							</div>
							<div class="form-group @if($errors->has('skills')) has-error @endif">
								<div class="col-lg-4 col-lg-offset-3">
									<div class="input-group">
										<span class="input-group-addon">Skills</span>	
										<select name="skills[]" class="form-control" id="selSkills" multiple="true"></select>
									</div>
								</div>
							</div>
							<div class="form-group @if($errors->has('desired_locations')) has-error @endif">
								<label class="control-label col-lg-3">Desired city *</label>
								<div class="col-lg-4">
									<button onClick="addResidenceLocation()" type="button" class="btn btn-default btn-xs">Add to Residence Address</button>
									<label>Add desired city :</label>
									<select id="selDesiredLocation" class="form-control"></select>
									<br/>
									<label>Miles Radius :</label>
									<select id="selMilesRadius" class="form-control">
										@foreach($searchMiles as $searchMile)
											<option value="{{$searchMile->mile}}">{{$searchMile->mile}} miles</option>
										@endforeach
									</select>
									<button onClick="addDesiredLocation()" type="button" class="btn btn-default btn-xs pull-right">Add to desired city</button>
								</div>
								<div class="col-lg-5">								
									<select name="desired_locations[]" multiple class="form-control">
										@foreach($values['desired_locations'] as $desired_location)
											<option value="{{$desired_location[0]}}" selected>{{$desired_location[1]}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('desired_salary_type')) has-error @endif">
								<label class="control-label col-lg-3">Desired Salary *</label>
								<div class="col-lg-2">
									<select class="form-control" name="desired_salary_type" data-child="desired_salary_range">
										<option value="">- Type -</option>
										@foreach($salaryTypes as $salaryType)
											@if($values["desired_salary_type_id"] == $salaryType->id)
												<option value="{{$salaryType->id}}" selected>{{$salaryType->getTypeName()}}</option>
											@else
												<option value="{{$salaryType->id}}">{{$salaryType->getTypeName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
								<div class="col-lg-2">
									<select class="form-control" name="desired_salary_range">
										<option value="">- Range -</option>
										@if($values["desired_salary_range"])
											<option value="{{$values['desired_salary_range'][0]}}" selected>{{$values["desired_salary_range"][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('job_types')) has-error @endif">
								<label class="control-label col-lg-3">Job Type *</label>
								<div class="col-lg-4">
									<select class="form-control" name="job_types[]" multiple="">
										<option value=0>- Job Type -</option>
										@foreach($jobTypes as $jobType)
											@if(in_array($jobType->id, $values["job_types"]))
												<option value="{{$jobType->id}}" selected>{{$jobType->getName()}}</option>
											@else
												<option value="{{$jobType->id}}">{{$jobType->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('experience')) has-error @endif">
								<label class="control-label col-lg-3">Experience *</label>
								<div class="col-lg-4">
									<select class="form-control" name="experience">
										<option value=0>- Experience -</option>
										@foreach($experiences as $experience)
											@if($values['experience_id'] == $experience->id)
												<option value="{{$experience->id}}" selected>{{$experience->getName()}}</option>
											@else
												<option value="{{$experience->id}}">{{$experience->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('experience_level')) has-error @endif">
								<label class="control-label col-lg-3">Level *</label>
								<div class="col-lg-4">
									<select class="form-control" name="experience_level">
										<option value=0>- Experience Level -</option>
										@foreach($experienceLevels as $experienceLevel)
											@if($values['experience_level_id'] == $experienceLevel->id)
												<option value="{{$experienceLevel->id}}" selected>{{$experienceLevel->getName()}}</option>
											@else
												<option value="{{$experienceLevel->id}}">{{$experienceLevel->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('about_me')) has-error @endif">
								<label class="control-label col-lg-3">About Me </label>
								<div class="col-lg-4">
									<textarea class="form-control" name="about_me" placeholder="Tell recruiter about your skills, experience, specifications and so on...">{{$values['about_me']}}</textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Profile privacy options</h3>
					    </div>
					    <div class="panel-body">
					    	<div class="form-group @if($errors->has('profile_privacy')) has-error @endif">
					    		<label class="control-label col-lg-3">
					    			* Do you want to be visible to potential employers searching for candidates ?
					    		</label>
					    		<div class="col-lg-4">
					    			<div class="radio">
					    				<label><input name="profile_privacy" @if($values['profile_privacy'] == 1) checked @endif value="1" type="radio" /> Yes - I want my profile and CV to be visible to potential employers <strong>(Recommended)</strong></label>
					    			</div>
					    			<div class="radio">
					    				<label><input name="profile_privacy" @if($values['profile_privacy'] == 2) checked @endif value="2" type="radio" /> Yes - I want my profile to be visible to potential employers, but keep my personal information and CV hidden.</label>
					    			</div>
					    			<div class="radio">
					    				<label><input name="profile_privacy" @if($values['profile_privacy'] == 3) checked @endif value="3" type="radio" /> No - Please do not make my profile searchable</label>
					    			</div>
					    		</div>
					    	</div>
					    </div>
				    </div>
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="form-group">
								{{csrf_field()}}
								<button type="submit" class="btn btn-primary">Save Profile</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('footer')
<script type="text/javascript">
	$(document).ready(function() {

		$("#frmProfile").submit(function() {

			var goSubmit = true;

			if($("select[name='title']").val() <= 0) {
				$("select[name='title']").parent().parent().addClass('has-error');
				$("select[name='title']").focus();
				goSubmit=false;
			}

			if($("select[name='country']").val() <= 0) {
				$("select[name='country']").parent().parent().addClass('has-error');
				$("select[name='country']").focus();
				goSubmit=false;
			}

			if($("select[name='state']").val() <= 0) {
				$("select[name='state']").parent().parent().addClass('has-error');
				$("select[name='state']").focus();
				goSubmit=false;
			}

			if($("select[name='city']").val() <= 0) {
				$("select[name='city']").parent().parent().addClass('has-error');
				$("select[name='city']").focus();
				goSubmit=false;
			}

			if($("select[name='education']").val() <= 0) {
				$("select[name='education']").parent().parent().addClass('has-error');
				$("select[name='education']").focus();
				goSubmit=false;
			}

			if($("select[name='current_salary_type']").val() <= 0) {
				$("select[name='current_salary_type']").parent().parent().addClass('has-error');
				$("select[name='current_salary_type']").focus();
				goSubmit=false;
			}

			if($("select[name='current_salary_range']").val() <= 0) {
				$("select[name='current_salary_range']").parent().parent().addClass('has-error');
				$("select[name='current_salary_range']").focus();
				goSubmit=false;
			}

			if($("select[name='desired_salary_type']").val() <= 0) {
				$("select[name='desired_salary_type']").parent().parent().addClass('has-error');
				$("select[name='desired_salary_type']").focus();
				goSubmit=false;
			}

			if($("select[name='desired_salary_range']").val() <= 0) {
				$("select[name='desired_salary_range']").parent().parent().addClass('has-error');
				$("select[name='desired_salary_range']").focus();
				goSubmit=false;
			}

			if($("select[name='desired_job_category']").val() <= 0) {
				$("select[name='desired_job_category']").parent().parent().addClass('has-error');
				$("select[name='desired_job_category']").focus();
				goSubmit=false;
			}

			if($("select[name='desired_job_title']").val() <= 0) {
				$("select[name='desired_job_title']").parent().parent().addClass('has-error');
				$("select[name='desired_job_title']").focus();
				goSubmit=false;
			}
			
			if($("select[name='desired_locations[]']").val() <= 0) {
				$("select[name='desired_locations[]']").parent().parent().addClass('has-error');
				$("select[name='desired_locations[]']").focus();
				goSubmit=false;
			}

			if($("select[name='job_type']").val() <= 0) {
				$("select[name='job_type']").parent().parent().addClass('has-error');
				$("select[name='job_type']").focus();
				goSubmit=false;
			}
			
			if($("select[name='experience']").val() <= 0) {
				$("select[name='experience']").parent().parent().addClass('has-error');
				$("select[name='experience']").focus();
				goSubmit=false;
			}

			if($("select[name='experience_level']").val() <= 0) {
				$("select[name='experience_level']").parent().parent().addClass('has-error');
				$("select[name='experience_level']").focus();
				goSubmit=false;
			}

			return goSubmit;

		});

		$("select").select2();

		$("select[name='country']").change(function() {
			$("select[name='state']").empty();
			$("select[name='city']").empty();
		});

		$("select[name='state']").change(function() {
			$("select[name='city']").empty();
		});

		$("select[name='state']").select2({
			placeholder : "- Select State - ",
			
			ajax: {
				url: "{{route('api-public-states')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		countryId: $("select[name='country']").val(),
			    		q: params.term,
			    		limit : 10
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data.data, function (item) {
		                    return {
		                        text: item.name,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});


		$("select[name='city']").select2({
			placeholder : "- Select City - ",
			
			ajax: {
				url: "{{route('api-public-cities')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		stateId: $("select[name='state']").val(),
			    		q: params.term,
			    		limit : 10
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data.data, function (item) {
		                    return {
		                        text: item.name,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

		$("#selCertificates").select2({
			placeholder : "- Select Certificate(s) - ",
			minimumResultsForSearch: Infinity,
			ajax: {
				url: "{{route('api-public-certificates')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		stateId: $("select[name='state']").val()
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.name,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

		$("select[name='current_salary_type'],select[name='desired_salary_type']").change(function() {
			$("select[name='"+$(this).attr('data-child')+"']").empty();
		});

		$("select[name='current_salary_range']").select2({
			placeholder : "- Type - ",
			minimumResultsForSearch: Infinity,
			ajax: {
				url: "{{route('api-public-salaryranges')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		salaryTypeId: $("select[name='current_salary_type']").val()
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.range_from + " > " + item.range_to,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

		$("select[name='desired_salary_range']").select2({
			placeholder : "- Type - ",
			minimumResultsForSearch: Infinity,
			ajax: {
				url: "{{route('api-public-salaryranges')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		salaryTypeId: $("select[name='desired_salary_type']").val()
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.range_from + " > " + item.range_to,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

		$("select[name='desired_job_category']").change(function() {
			$("select[name='desired_job_title']").empty();
			$(".my-skills").html("");
			$("#selSkills").empty();
		});

		$("select[name='desired_job_title']").change(function() {
			$("#selSkills").empty();
		});

		$("select[name='desired_job_title']").select2({
			placeholder : "- Job Title - ",
			minimumResultsForSearch: Infinity,
			ajax: {
				url: "{{route('api-public-jobtitles')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		jobCategoryId: $("select[name='desired_job_category']").val()
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.title,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

		$("#selSkills").select2({
			placeholder : "- Skills - ",
			minimumResultsForSearch: Infinity,
			tags: true,
			tokenSeparators: [','],
			ajax: {
				url: "{{route('api-public-skills')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			    	var jobTitleId = $("select[name='desired_job_title']").val();
			    	if(!jobTitleId) {
			    		jobTitleId = 0;	
			    	}
			        return {
			    		jobTitleId: jobTitleId
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data, function (item) {
		                    return {
		                        text: item.name,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

		$("select[name='desired_job_title']").change(function() {
			$(".my-skills").html("<img src='{{asset('imgs/loader.gif')}}' />");
			$.ajax({
				url : "{{route('api-public-skills')}}",
				data: {
					jobTitleId : $("select[name='desired_job_title']").val()
				},
				cache: true,
				success: function(data) {
					for(i=0;i<data.length;i++) {
						var item = data[i];
						var line = '<label style="margin:5px"><input type="checkbox" name="skills[]" value="'+item.id+'" /> '+item.name+'</label>';
						$(".my-skills").append(line);
					}					
				},
				complete: function() {
					$(".my-skills").find("img").remove();
				}
			});
		});

		$("#selDesiredLocation").select2({
			placeholder: "Search Desired Locations...",
			minimumInputLength: 3,
			ajax: {
				url: "{{route('api-public-locations')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
						q: params.term,
						limit : 10
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data.data, function (item) {
		                    return {
		                        text: item.name+", "+item.state_name+", "+item.country_name,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});

	});

	function addDesiredLocation() {
		var $city_id = $("#selDesiredLocation").val();
		var $miles = $("#selMilesRadius").val();

		if($city_id && $miles) {
			var $city_title = $("#selDesiredLocation option[value="+$city_id+"]").text();
			if($miles>0) {
				$city_title = $city_title + " + " + $miles + " miles";
			}
			var $option_id = $city_id+","+$miles;

			if($("select[name='desired_locations[]'] option[value='"+$option_id+"']").val()) {
				
			} else {
				$("select[name='desired_locations[]']").append(new Option($city_title, $option_id));
				$("select[name='desired_locations[]'] option[value='"+$option_id+"']").attr('selected','selected');
			}
		} else {
			alert("Please select valid location and miles radius");
		}
	}

	function addResidenceLocation() {
		var $city_id = $("#city").val();
		var $state_id = $("#state").val();
		var $country_id = $("#country").val();
		var $miles = $("#selMilesRadius").val();

		if($city_id && $state_id && $country_id && $miles) {
			var $city_title = $("#city option[value="+$city_id+"]").text();
			var $state_title = $("#state option[value="+$state_id+"]").text();
			var $country_title = $("#country option[value="+$country_id+"]").text();
			$city_title=$city_title+","+$state_title+","+$country_title;
			if($miles>0) {
				$city_title = $city_title + " + " + $miles + " miles";
			}
			var $option_id = $city_id+","+$miles;

			if($("select[name='desired_locations[]'] option[value='"+$option_id+"']").val()) {
				
			} else {
				$("select[name='desired_locations[]']").append(new Option($city_title, $option_id));
				$("select[name='desired_locations[]'] option[value='"+$option_id+"']").attr('selected','selected');
			}
		} else {
			alert("Please select valid Residence Address and miles radius");
		}
	}

</script>
@endpush