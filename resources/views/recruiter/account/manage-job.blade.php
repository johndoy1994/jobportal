@extends('layouts.recruiter')

@section('title', $page_title)

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.recruiter.validation_errors')
				@include('includes.recruiter.request_messages')
					
				<div class="page-header">
					<h2>{{$page_title}}</h2>
					<small>Fields marked * must be completed.</small>
				</div>

				<div class="row">
					<div class="col-md-12">
						<form id="frmJob" method="post" action="{{route('recruiter-job', ['mode'=> $_mode, 'job'=> $_jobId ])}}">
							<div class="col-md-6">
								<div class="form-group @if($errors->has('job_title')) has-error @endif">
									<label class="post-new-job-field-label">Title: *</label>
									<input type="text" required name="job_title" class="form-control" placeholder="Title that describes job and should be one line." value="{{$values['job_title']}}" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group @if($errors->has('vacancies')) has-error @endif">
									<label class="post-new-job-field-label">Vacancies: *</label>
									<input type="text" pattern="[0-9]+" required name="vacancies" placeholder="Number of vacancies you've." class="form-control" value="{{$values['vacancies']}}" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group @if($errors->has('countries')) has-error @endif">
									<label class="post-new-job-field-label">Country : *</label>
									<select required name="countries" class="form-control">
										@if($values["countries"])
											<option value="{{$values['countries'][0]}}">{{$values["countries"][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group @if($errors->has('states')) has-error @endif">
									<label class="post-new-job-field-label">Province/State : *</label>
									<select required name="states" class="form-control">
										@if($values["states"])
											<option value="{{$values['states'][0]}}">{{$values["states"][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group @if($errors->has('cities')) has-error @endif">
									<label class="post-new-job-field-label">City : *</label>
									<select required name="cities" class="form-control">
										@if($values["cities"])
											<option value="{{$values['cities'][0]}}">{{$values["cities"][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group @if($errors->has('street')) has-error @endif">
									<label class="post-new-job-field-label">Street :</label>
									<input type="text" name="street" placeholder="Street address" class="form-control" value="{{$values['street']}}" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group @if($errors->has('postal_code')) has-error @endif">
									<label class="post-new-job-field-label">Postal Code :</label>
									<input type="text" name="postal_code" placeholder="Postal code of job location" class="form-control" value="{{$values['postal_code']}}" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group @if($errors->has('job_categories')) has-error @endif">
									<label class="post-new-job-field-label">Job Category : *</label>
									<select required name="job_categories" class="form-control">
										@if($values["job_categories"])
											<option value="{{$values['job_categories'][0]}}">{{$values["job_categories"][1]}}</option>
										@endif
									</select>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group @if($errors->has('job_titles')) has-error @endif">
									<label class="post-new-job-field-label">Job Title : *</label>
									<select required name="job_titles" class="form-control">
										@if($values["job_titles"])
											<option value="{{$values['job_titles'][0]}}">{{$values["job_titles"][1]}}</option>
										@endif
									</select>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group @if($errors->has('requirements')) has-error @endif">
									<label class="post-new-job-field-label">Job Requirements :</label>
									<div class="checkbox">
										<div class="loader hide"><img src="{{asset('imgs/loader.gif')}}"/></div>
										<div class="job-requirements">
											@if($values['requirements'])
												@foreach($values['requirements'] as $tag)
													<label style="margin: 5px"><input type="checkbox" name='requirements[]' value="{{$tag[0]}}" {{$tag[2]?'checked':''}} /> {{$tag[1]}}</label>
												@endforeach
											@else
												Select job title first!!
											@endif
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group @if($errors->has('requirements')) has-error @endif">
									<label class="post-new-job-field-label">Additional Job Requirement :</label>
									<select name="requirements[]" class="form-control" multiple>
										@if(isset($values["newRequirements"]))
											@foreach($values["newRequirements"] as $req)
												<option value="{{$req}}" selected>{{$req}}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>

							<div class="col-md-12">&nbsp;</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('education')) has-error @endif">
									<label class="post-new-job-field-label">Education required : *</label>
									<select name="education" class="form-control" required>
										<option value=0 selected style="display:none">Select Education</option>
										@foreach($educations as $education)
											<option value="{{$education->id}}" {{ $values['education'] == $education->id ? 'selected' : '' }}>{{$education->getName()}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group @if($errors->has('experiences')) has-error @endif">
									<label class="post-new-job-field-label">Experience required : *</label>
									<select name="experiences" class="form-control" required>
										<option value=0 selected style="display:none">Select Experience</option>
										@foreach($experiences as $experience)
											<option value="{{$experience->id}}" {{ $values['experiences'] == $experience->id ? 'selected' : '' }}>{{$experience->getName()}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group @if($errors->has('experience_levels')) has-error @endif">
									<label class="post-new-job-field-label">Experience Level required : *</label>
									<select name="experience_levels" class="form-control" required>
										<option value=0 selected style="display:none">Select Experience Level</option>
										@foreach($experienceLevels as $experienceLevel)
											<option value="{{$experienceLevel->id}}" {{ $values['experience_levels'] == $experienceLevel->id ? 'selected' : '' }}>{{$experienceLevel->getName()}}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('job_types')) has-error @endif">
									<label class="post-new-job-field-label">Job Type : *</label>
									<select name="job_types" class="form-control" required>
										<option value="0">Select job type</option>
										@foreach($jobTypes as $jobType)
											<option  value="{{$jobType->id}}" show-calendar="{{$jobType->day_selection}}" {{ $values['job_types'] == $jobType->id ? 'selected' : '' }}>{{$jobType->getName()}}</option>
										@endforeach
									</select>	
								</div>
							</div>

							<div class="col-md-8">
								<div class="form-group @if($errors->has('certificates')) has-error @endif">
									<label class="post-new-job-field-label">Certificates : <small>(comma separated)</small></label>
									<input type="text" class="form-control" name="certificates" value="{{$values['certificates']}}" />
								</div>
							</div>

							<div class="col-md-12 job-calendar hide">
								<div class="form-group">
									<select name="dates[]" multiple class="hide">
										@if($values['dates'])
											@foreach($values['dates'] as $date)
												<option value='{{$date}}' selected>{{$date}}</option>
											@endforeach
										@endif
									</select>
									<label class="post-new-job-field-label">Set working days : *</label>
									<div id="calendar" style="background:#FFF;padding:10px 20px"></div>
									<br/>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('starting_date')) has-error @endif">
									<label class="post-new-job-field-label">Starting Date : *</label>
									<input type="text" name="starting_date" class="form-control" placeholder="yyyy-mm-dd" value="{{$values['starting_date']}}" />
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('ending_date')) has-error @endif">
									<label class="post-new-job-field-label">Ending Date : *</label><label class="pull-right"><input type="checkbox" name="no_ending_date" value="1" {{$values['no_ending_date'] == 1 ? 'checked' : '' }} /> No ending date</label>
									<input type="text" name="ending_date" class="form-control" placeholder="yyyy-mm-dd"  value="{{$values['no_ending_date'] == 0 ? $values['ending_date'] : '' }}" {{$values['no_ending_date'] == 1 ? 'disabled' : '' }} />
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group  @if($errors->has('weekdays')) has-error @endif">
									<label class="post-new-job-field-label">Weekdays :<small>(optional)</small></label>
									<select name="weekdays[]" class="form-control" multiple>
										@if(isset($values['weekdays']))
											@for($i=1;$i<=7;$i++)
												@if(in_array($i, $values['weekdays']))
													<option value={{$i}} selected>{{$convertDayNumber($i, true)}}</option>	
												@else
													<option value={{$i}}>{{$convertDayNumber($i, true)}}</option>	
												@endif
											@endfor
										@endif
									</select>
								</div>
							</div>

							<div class="col-md-12">&nbsp;</div>

							<div class="col-md-3">
								<div class="form-group @if($errors->has('schedule_from')) has-error @endif">
									<label class="post-new-job-field-label">Work Schedule : *</label>
									<input type="text" name="schedule_from" class="form-control" placeholder="Schedule from..." value="{{$values['schedule_from']}}" />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group @if($errors->has('schedule_to')) has-error @endif">
									<label class="post-new-job-field-label">&nbsp;</label>
									<input type="text" name="schedule_to" class="form-control" placeholder="Schedule to..." value="{{$values['schedule_to']}}" />
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group @if($errors->has('pay_bies')) has-error @endif">
									<label class="post-new-job-field-label">Pay By : *</label>
									<select name="pay_bies" class="form-control">
										<option value=0 style="display:none">Select Payby...</option>
										@foreach($payBies as $payBy)
											@if($values['pay_bies'] == $payBy->id)
												<option value="{{$payBy->id}}" selected>{{$payBy->getName()}}</option>
											@else
												<option value="{{$payBy->id}}">{{$payBy->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group @if($errors->has('pay_periods')) has-error @endif">
									<label class="post-new-job-field-label">Pay Period : *</label>
									<select name="pay_periods" class="form-control">
										<option value=0 style="display:none">Select pay periods...</option>
										@foreach($payPeriods as $payPeriod)
											@if($values['pay_periods'] == $payPeriod->id)
												<option value="{{$payPeriod->id}}" selected>{{$payPeriod->getName()}}</option>
											@else
												<option value="{{$payPeriod->id}}">{{$payPeriod->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('salary_types')) has-error @endif">
									<label class="post-new-job-field-label">Salary Type : *</label>
									<select name="salary_types" class="form-control">
										<option value=0 style="display:none">Select salary type...</option>
										@foreach($salaryTypes as $salaryType)
											@if($values['salary_types'] == $salaryType->id)
												<option value="{{$salaryType->id}}" selected>{{$salaryType->getName()}}</option>
											@else
												<option value="{{$salaryType->id}}">{{$salaryType->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('salary')) has-error @endif">
									<label class="post-new-job-field-label">Salary : *</label>
									<label class="pull-right"><input type="checkbox" name="salary_negotiable" value=1 {{$values['salary_negotiable']==1 ? 'checked' : ''}} /> Negotiable</label>
									<input type="text" name="salary" class="form-control" placeholder="Job salary..." value="{{$values['salary_negotiable']==0 ? $values['salary'] : '0'}}" {{$values['salary_negotiable']==1 ? 'disabled' : ''}} />
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group @if($errors->has('expiration_date')) has-error @endif">
									<label class="post-new-job-field-label">Expiration Date : *</label>
									<input type="text" name="expiration_date" class="form-control" placeholder="yyyy-mm-dd" value="{{$values['expiration_date']}}" />
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group @if($errors->has('benefits')) has-error @endif">
									<label class="post-new-job-field-label">Benefits :</label>
									<textarea name="benefits" class="form-control">{{$values['benefits']}}</textarea>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group @if($errors->has('description')) has-error @endif">
									<label class="post-new-job-field-label">Description : *</label>
									<textarea name="description" class="form-control">{{$values['description']}}</textarea>
								</div>
							</div>
							
							<div class="col-md-12">
								<hr/>
								<small style="color:#999">* There will be something here for job specification or terms.</small>
								<div class="form-group">
									<br/>
									{{csrf_field()}}
									<button type="submit" class="btn btn-primary">Submit</button>
									<button type="reset" onclick="return confirm('Are you sure to reset fields ?')" class="btn btn-default">Reset</button>
								</div>
							</div>

						</form>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection

@push('head')
<link href="{{asset('backend/css/datetimepicker/jquery-ui.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('backend/css/datetimepicker/jquery-ui-timepicker-addon.css')}}" rel="stylesheet" type="text/css">
<script src="{{asset('backend/js/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui.min.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon-i18n.min.js')}}"></script>
<style type="text/css">
td.fc-day {
	border:1px solid #DDD!important;
}
</style>
@endpush

@push('footer')
<script>
$(document).ready(function() {
	var select = "select[name='dates[]']";
	CKEDITOR.replace( 'description' );

	$("#frmJob").submit(function() {



	});

	$("input[name='salary_negotiable']").change(function() {
		if($("input[name='salary']").attr("disabled")) {
			$("input[name='salary']").removeAttr("disabled");
		} else {
			$("input[name='salary']").val("0");
			$("input[name='salary']").attr("disabled","disabled");
		}
	});

	$("input[name='schedule_from']").timepicker({
    	'showDuration': true,
    	'timeFormat': 'HH:mm:ss',
    	onSelect: function(selectedDate) {
            $("input[name='schedule_to']").datepicker('option', 'minTime', selectedDate || new Date());
      	}
	});

	$("input[name='schedule_to']").timepicker({
    	'showDuration': true,
    	'timeFormat': 'HH:mm:ss',
    	onSelect: function(selectedDate) {
            $("input[name='schedule_from']").datepicker('option', 'maxTime', selectedDate || new Date());
      	}
	});

	$("input[name='starting_date']").datepicker({
		minDate: new Date(),
		dateFormat: 'yy-mm-d',
		onSelect: function(selectedDate) {
            $("input[name='ending_date']").datepicker('option', 'minDate', selectedDate || new Date());
      	}
	});

	$("input[name='no_ending_date']").change(function() {
		if($("input[name='ending_date']").attr("disabled")) {
			$("input[name='ending_date']").removeAttr("disabled");
		} else {
			$("input[name='ending_date']").val("");
			$("input[name='ending_date']").attr("disabled","disabled");
		}
	});

	$("input[name='ending_date']").datepicker({
		minDate: new Date(),
		dateFormat: 'yy-mm-d',
		onSelect: function(selectedDate) {
            $("input[name='starting_date']").datepicker('option', 'maxDate', selectedDate || new Date());
      	}
	});

	$("input[name='expiration_date']").datepicker({
		minDate: new Date(),
		dateFormat: 'yy-mm-d'
	});

	$("select[name='weekdays[]']").select2({});

	$("select[name='job_types']").change(function() {
		var jobTypeId = $(this).val();
		var show_calendar = $(this).find("option[value='"+jobTypeId+"']").attr('show-calendar');
		if(show_calendar == 1) {
			//$(select).attr("required",'required');
			$(".job-calendar").removeClass("hide");
			$(".job-calendar #calendar").fullCalendar({
				dayRender: function(date, cell) {
					var todayFormat = "{{\Carbon\Carbon::now()->format('Y-m-d')}}";
					var dateFormat = date.format('Y-MM-DD');

					if($(select+" option[value='"+dateFormat+"']").text()) {
						cell.addClass('calendar-date-selected');
					}

					if(moment(dateFormat).isAfter(todayFormat,'day')) {
						cell.css('cursor','pointer');
						cell.addClass('calendar-day-selectable');
					} else {
						cell.css('cursor','not-allowed');
						cell.addClass('calendar-day-not-selectable');
					}
				},

				dayClick: function(date) {
					var dateFormat = date.format('Y-MM-DD');
					if($(this).is(".calendar-day-not-selectable")) {
						alert("Sorry, you cannot select past days.");
						return;
					}
					if($(this).is(".calendar-date-selected")) {
						$(this).removeClass("calendar-date-selected");
						$(select+" option[value='"+dateFormat+"']").remove();
					} else {
						$(this).addClass("calendar-date-selected");
						$(select).append(new Option(dateFormat,dateFormat));
	                    $(select+" option[value='"+dateFormat+"']").attr('selected','selected');
					}
				}
			});
			$(".job-calendar #calendar").fullCalendar('render');
		} else {
			//$(select).removeAttr("required");
			$(".job-calendar").addClass("hide");
		}
	});

	@if(isset($values['show_calendar']) && $values['show_calendar'])
		$("select[name='job_types']").change();
	@endif

	$("select[name='job_categories']").select2({
		placeholder: "Select job category",
		ajax: {
			url: "{{route('api-public-jobcategories')}}",
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
	                        text: item.name,
	                        id: item.id
	                    }
	                })
	            };
		    },
		    cache: true
		}
	});

	$("select[name='job_categories']").change(function() {
		$("select[name='job_titles']").empty();
		$(".job-requirements").html("Please select job title first!!");
	});

	$("select[name='job_titles']").select2({
		placeholder : "Select job title..",
		ajax: {
			url: "{{route('api-public-jobtitles')}}",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		    	var cId = 0;
		    	if($("select[name='job_categories']").val()) {
		    		cId = $("select[name='job_categories']").val();
		    	}
		        return {
		    		jobCategoryId: cId,
		  			q: params.term,
					limit : 10
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

	$("select[name='job_titles']").change(function() {
		$(".job-requirements").html("");
		$(".job-requirements").parent().find(".loader").removeClass("hide");

		if($(this).val()) {
			var jobTitleId = $(this).val();
			$.ajax({
				url : "{{route('api-public-skills')}}",
				data: {
					jobTitleId: jobTitleId
				},
				dataType: "json",
				success: function(skills) {
					skills.forEach(function(skill, index) {
						var label = "<label style='margin: 5px'><input type='checkbox' name='requirements[]' value='"+skill.id+"' /> "+skill.name+"</label>";
						$(".job-requirements").append(label);
					});
				},
				error: function(er) {
					$(".job-requirements").html("There was an error while fetching requirements, try again.");
				},
				complete: function() {
					$(".job-requirements").parent().find(".loader").addClass("hide");
				}
			});
		}
	});

	$("select[name='requirements[]']").select2({
		placeholder : "Additional requirements",
		minimumResultsForSearch: Infinity,
		tags: true,
		tokenSeparators: [','],
		ajax: {
			url: "{{route('api-public-skills')}}",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		    	var jobTitleId = $("select[name='job_titles']").val();
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

	$("select[name='countries']").change(function() {
		$("select[name='states']").empty();
		$("select[name='cities']").empty();
	});

	$("select[name='countries']").select2({
		placeholder: "Search countries...",
		minimumInputLength: 2,
		ajax: {
			url: "{{route('api-public-countries')}}",
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
	                        text: item.name,
	                        id: item.id
	                    }
	                })
	            };
		    },
		    cache: true
		}
	});

	$("select[name='states']").change(function() {
		$("select[name='cities']").empty();
	});
	$("select[name='states']").select2({
		placeholder : "- Select State - ",
		ajax: {
			url: "{{route('api-public-states')}}",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		    	var cId = 0;
		    	if($("select[name='countries']").val()) {
		    		cId = $("select[name='countries']").val();
		    	}
		        return {
		        	q: params.term,
					limit : 10,
		    		countryId: cId
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

	$("select[name='cities']").select2({
		placeholder : "- Select City - ",
		ajax: {
			url: "{{route('api-public-cities')}}",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		    	var sId = 0;
		    	if($("select[name='states']").val()) {
		    		sId=$("select[name='states']").val();
		    	}
		        return {
		        	q: params.term,
					limit : 10,
		    		stateId: sId
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
});
</script>
@endpush
