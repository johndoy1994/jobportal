@extends('layouts.backend')

@section('title', 'Edit Job')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Edit Job</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-job',Request::all())}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-edit-job-post', array_merge( ['Job'=> $Jobs->id ], Request::all()) )}}">
					<legend>Edit Job</legend>
					@if(session('success_message'))
						<div class="alert alert-success">
							{!!session('success_message')!!}
						</div>
					@endif

					@if(session('error_message'))
						<div class="alert alert-danger">
							{!!session('error_message')!!}
						</div>
					@endif

					@if(count($errors)>0)
						<div class="alert alert-warning">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</div>
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Employers *</label>
							<div class="col-lg-5">
								<select class="form-control" id="employer_id" name="employer_id" required="">
									<option value="">Select Employers</option>
									@foreach($employers as $employer)
										<option value="{{$employer->id}}" {{($employer->id==$Jobs->employer_id)? "selected" : ""}}>{{$employer->company_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Title *</label>
							<div class="col-lg-5">
								<input type="text" required name="title" value="{{$Jobs->title}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Number of Vacancies *</label>
							<div class="col-lg-5">
								<input type="text" pattern="[0-9]+" title="Please enter valid vacancies" required name="vacancies" value="{{$Jobs->vacancies}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job category *</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_category_id" name="job_category_id" required="">
									<option value="">Select job category</option>
									@foreach($Categories as $Category)
										@if($Jobs->jobTitle && $Jobs->jobTitle->category)
											<option value="{{$Category->id}}" {{($Jobs->jobTitle->category->id==$Category->id)? "selected" : ""}}>{{$Category->name}}</option>
										@else
											<option value="{{$Category->id}}">{{$Category->name}}</option>
										@endif	
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Title *</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_title_id" name="job_title_id" required="">
									<option value="">Select job title</option>
									@if($Jobs->jobTitle && $Jobs->jobTitle->category)
										<!-- <option value="{{$Jobs->jobTitle->id}}" selected>{{$Jobs->jobTitle->title}}</option> -->
										@foreach($Jobs->jobTitle->category->jobtitles as $jobTitle)
											@if($jobTitle->id != $Jobs->jobTitle->id)
												<option value="{{$jobTitle->id}}">{{$jobTitle->title}}</option>
											@else
												<option value="{{$jobTitle->id}}" selected>{{$jobTitle->title}}</option>
											@endif
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Skills </label>
							<div class="col-lg-5">
								<div class="checkbox my-skills">
									@foreach($TagsAllskill as $skill)
										<?php $isSelected = false; ?>
										@foreach($jobskills as $sel_skill)
											@if($sel_skill->tag_id == $skill->id)
												<?php $isSelected = true; ?>
											@endif
										@endforeach
										<label style="margin: 5px"><input type="checkbox" name="skills[]" value="{{$skill->id}}" @if($isSelected) checked @endif>{{$skill->getName()}}</label>
									@endforeach
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-4 col-lg-offset-3">
								<div class="input-group">
									<span class="input-group-addon">Skills</span>	
									<select name="skills[]" class="form-control" id="selSkills" multiple="true"></select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Education *</label>
							<div class="col-lg-5">
								<select class="form-control" id="education_id" name="education_id" required="">
									<option value="">Select Educations</option>
									@foreach($educations as $education)
										<option value="{{$education->id}}" {{($education->id==$Jobs->education_id)? "selected" : ""}}>{{$education->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Experience *</label>
							<div class="col-lg-5">
								<select class="form-control" id="experience_id" name="experience_id" required="">
									<option value="">Select Experience</option>
									@foreach($experiences as $experience)
										<option value="{{$experience->id}}" {{($experience->id==$Jobs->experience_id)? "selected" : ""}}>{{$experience->exp_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Experience Level *</label>
							<div class="col-lg-5">
								<select class="form-control" id="experience_level_id" name="experience_level_id" required="">
									<option value="">Select Experience Level</option>
									@foreach($experienceLevels as $experienceLevel)
										<option value="{{$experienceLevel->id}}" {{($experienceLevel->id==$Jobs->experience_level_id)? "selected" : ""}}>{{$experienceLevel->level}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Type *</label>
							<div class="col-lg-5">
								<select class="form-control" id="job_type_id" name="job_type_id" required="">
									<option value="">Select Job Type</option>
									@foreach($jobTypes as $jobType)
										<option data-show-calendar="{{$jobType->day_selection}}"  value="{{$jobType->id}}" {{($jobType->id==$Jobs->job_type_id)? "selected" : ""}}>{{$jobType->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						@include("includes.backend.job-calendar-field")
						<div class="form-group">
							<label class="control-label col-lg-3">Country *</label>
							<div class="col-lg-5">
								<select class="form-control" id="country_id" name="country_id" required="">
									<option value="">Select Country</option>
									@foreach($Countries as $Countrie)
										@if($Jobs->jobAddresses)
											<option value="{{$Countrie->id}}" {{($Jobs->jobAddresses->getCountryId()==$Countrie->id)? "selected" : ""}}>{{$Countrie->name}}</option>
										@else
											<option value="{{$Countrie->id}}">{{$Countrie->name}}</option>
										@endif											
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">State *</label>
							<div class="col-lg-5">
								<select class="form-control" id="state_id" name="state_id" required="">
									<option value="">Select state</option>
									@if($Jobs->jobAddresses)
									<!-- <option value="{{$Jobs->jobAddresses->getStateId()}}" selected>{{$Jobs->jobAddresses->getStateName()}}</option> -->
										@if($Jobs->jobAddresses->country())
											@foreach($Jobs->jobAddresses->country()->States as $states)
												@if($states->id != $Jobs->jobAddresses->getStateId())
													<option value="{{$states->id}}">{{$states->name}}</option>
												@else
													<option value="{{$states->id}}" selected>{{$states->name}}</option>
												@endif
											@endforeach
										@endif
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">City *</label>
							<div class="col-lg-5">
								<select class="form-control" id="city_id" name="city_id" required="">
									<option value="">Select city</option>
									@if($Jobs->jobAddresses)
										<!-- <option value="{{$Jobs->jobAddresses->getCityId()}}" selected>{{$Jobs->jobAddresses->getCityName()}}</option> -->
										@if($Jobs->jobAddresses->state())
											@foreach($Jobs->jobAddresses->state()->Cities as $city)
												@if($city->id != $Jobs->jobAddresses->getCityId())
													<option value="{{$city->id}}">{{$city->name}}</option>
												@else
													<option value="{{$city->id}}" selected>{{$city->name}}</option>
												@endif
											@endforeach
										@endif
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Street :</label>
							<div class="col-lg-5">
								@if($Jobs->jobAddresses)
									<input type="text"  name="street" value="{{$Jobs->jobAddresses->getStreetName()}}" class="form-control" />
								@else
									<input type="text"  name="street" value="" class="form-control" />	
								@endif	
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Postal code :</label>
							<div class="col-lg-5">
							@if($Jobs->jobAddresses)
								<input type="text" pattern="[A-Za-z0-9 ]+" title="Please enter valid Postal code"  name="postal_code" value="{{$Jobs->jobAddresses->getPostalCode()}}" class="form-control" />
							@else
								<input type="text" pattern="[A-Za-z0-9 ]+" title="Please enter valid Postal code"  name="postal_code" value="" class="form-control" />	
							@endif									
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Certificates :</label>
							<div class="col-lg-5">
								<input type="text" name="certificates" placeholder="Certificates separated by commas"  value="{{$certificates}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Keyword *</label>
							<div class="col-lg-5">
								<textarea name="keyword" class="form-control" required rows="5" cols="42">{{$Keywords}}</textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Start Date *</label>
							<div class="col-lg-5">
								<input type="text"  required name="starting_date" id="starting_date" value="{{$Jobs->starting_date->format('Y-m-d')}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">End Date :</label>
							<div class="col-lg-5">
								<input type="text" name="ending_date" id="ending_date" value="{{$Jobs->ending_date ? $Jobs->ending_date->format('Y-m-d') : ""}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Post Expiration Date *</label>
							<div class="col-lg-5">
								<input type="text"  required name="expiration_date" id="expiration_date" value="{{$Jobs->expiration_date->format('Y-m-d')}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Work Schedule From *</label>
							<div class="col-lg-5">
								<input type="text"  required name="work_schedule_from" id="work_schedule_from" value="{{$Jobs->work_schedule_from}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Work Schedule To *</label>
							<div class="col-lg-5">
								<input type="text"  required name="work_schedule_to" id="work_schedule_to" value="{{$Jobs->work_schedule_to}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Salary Type *</label>
							<div class="col-lg-5">
								<select class="form-control" id="salary_type_id" name="salary_type_id" required="">
									<option value="">Select Salary Type</option>
									@foreach($salaryTypes as $salaryType)
										<option value="{{$salaryType->id}}" {{($Jobs->salary_type_id==$salaryType->id)? "selected" : ""}}>{{$salaryType->salary_type_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<div class="col-lg-5">
								<input type="radio" name="salary_check" value="1" id="salary_check" {{($Jobs->salary!=0)? "checked" : ""}}> Salary
								<input type="radio" name="salary_check" value="0" id="salary_check" {{($Jobs->salary==0)? "checked" : ""}}> Negotiable
							</div>
						</div>
						<!-- <div class="form-group">
							<label class="control-label col-lg-3">Salary Range *</label>
							<div class="col-lg-5">
								<select class="form-control" id="salary_range_id" name="salary_range_id" required="">
									<option value="">Select salary ranage</option>
									@if($Jobs->SalaryRange)
									<option value="{{$Jobs->SalaryRange->rangeId()}}" selected>{{$Jobs->SalaryRange->range()}}</option>
									@endif
								</select>
							</div>
						</div> -->
						<div class="form-group" id="salary_div" {{($Jobs->salary==0) ? "hidden" : ""}}>
							<label class="control-label col-lg-3">Salary *</label>
							<div class="col-lg-5">
								<input type="text" pattern="[0-9]+[.]*[0-9]*" title="Please enter valid salry" name="salary" id="salary" value="{{$Jobs->salary}}" class="form-control" {{($Jobs->salary!=0) ? "required" : ""}} />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">PayBy *</label>
							<div class="col-lg-5">
								<select class="form-control" id="pay_by_id" name="pay_by_id" required="">
									<option value="">Select PayBy</option>
									@foreach($payBys as $payBy)
										<option value="{{$payBy->id}}" {{($Jobs->pay_by_id==$payBy->id)? "selected" : ""}}>{{$payBy->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">PayPeriod *</label>
							<div class="col-lg-5">
								<select class="form-control" id="pay_period_id" name="pay_period_id" required="">
									<option value="">Select PayPeriod</option>
									@foreach($payPeriods as $payPeriod)
										<option value="{{$payPeriod->id}}" {{($Jobs->pay_period_id==$payPeriod->id)? "selected" : ""}}>{{$payPeriod->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Benefits :</label>
							<div class="col-lg-5">
								<textarea name="benefits" class="form-control" rows="5" cols="42" >{{$Jobs->benefits}}</textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Description *:</label>
							<div class="col-lg-8">
								<textarea name="description" id="description" class="form-control" rows="5" cols="42">{{$Jobs->description}}</textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Weekely :</label>
							<div class="col-lg-5">
								@for($i=1;$i<=7;$i++)
									<?php
									$checked = false;
									foreach($jobWeekdays as $weekday) {
										if($weekday->day == $i) {
											$checked = true;
										}
									}
									?>
									<input type="checkbox" name="weekly[]" value="{{$i}}" {{ $checked ? "checked" : "" }}> {{$convertDayNumber($i)}}
								@endfor
							</div>
						</div>
					</fieldset>
					{{csrf_field()}}
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('head')
<link href="{{asset('/backend/css/datetimepicker/jquery-ui.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('/backend/css/datetimepicker/jquery-ui-timepicker-addon.css')}}" rel="stylesheet" type="text/css">
@endpush

@push('footer')
<script src="{{asset('backend/js/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui.min.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon-i18n.min.js')}}"></script>
<script type="text/javascript">

$(document).ready(function(){
	CKEDITOR.replace( 'description' );
	var dateToday = new Date();
	
	// $( "#starting_date" ).datepicker();
	 $('#expiration_date').datepicker({
	 	minDate:dateToday,
	 	dateFormat: 'yy-mm-d'
	 });
	
	$('#work_schedule_from').timepicker({
    	'showDuration': true,
    	'timeFormat': 'HH:mm:ss',
    	onSelect: function(selectedDate) {
            $('#work_schedule_to').datepicker('option', 'minTime', selectedDate || new Date());
      	}
	});
	$('#work_schedule_to').timepicker({
    	'showDuration': true,
    	'timeFormat': 'HH:mm:ss',
    	onSelect: function(selectedDate) {
            $('#work_schedule_from').datepicker('option', 'maxTime', selectedDate || new Date());
      	}
	});


	$('#starting_date').datepicker({
		minDate:dateToday,
		dateFormat: 'yy-mm-d',
      	onSelect: function(selectedDate) {
            $('#ending_date').datepicker('option', 'minDate', selectedDate || new Date());
      	}
	});

	$('#ending_date').datepicker({
		minDate:dateToday,
		dateFormat: 'yy-mm-d',
      	onSelect: function(selectedDate) {
            $('#starting_date').datepicker('option', 'maxDate', selectedDate || new Date());
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
			    	var jobTitleId = $("select[name='job_title_id']").val();
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

	
});
$(document).on('change','#salary_check',function(){
	if($(this).val()==0){
		$('#salary').val('');
		$('#salary_div').hide();
		$('#salary_div input').removeAttr("required");
	}else{
		$('#salary').val('');
		$('#salary_div').show();
		$('#salary_div input').attr("required","required");
	}
});
// $(document).on('change', '#salary_type_id', function() {
// 			if($(this).val()==""){
// 				var id =0;
// 			}else{
// 				var id =$(this).val();
// 			}
// 			$.ajax({
//                 dataType:'json',
//                 url:  "{{route('api-public-salaryranges')}}",
//                 data: {'salaryTypeId' : id},
//                 success: function(data){
//                     $('#salary_range_id').html('<option value="">Select salary ranage</option>');
//                     for(var i=0;i<data.length;i++){
//                         $('#salary_range_id').append('<option value="'+data[i].id+'">'+data[i].range_from+'-'+data[i].range_to+'</option>');
//                     }
//                 }
//             });
//         });

$(document).on('change', '#country_id', function() {
		$('#city_id').html('<option value="">Select City</option>');
			if($(this).val()==""){
				var id =0;
			}else{
				var id =$(this).val();
			}
			$.ajax({
                dataType:'json',
                url:  "{{route('api-public-states')}}",
                data: {'countryId' : id},
                success: function(data){
                    $('#state_id').html('<option value="">Select state</option>');
                    for(var i=0;i<data.length;i++){
                        $('#state_id').append('<option value="'+data[i].id+'"> '+data[i].name+'</option>');
                    }
                }
            });
        });

		$(document).on('change', '#state_id', function() {
			if($(this).val()==""){
				var id =0;
			}else{
				var id =$(this).val();
			}
			$.ajax({
                dataType:'json',
                url:  "{{route('api-public-cities')}}",
                data: {'stateId' : id},
                success: function(data){
                    $('#city_id').html('<option value="">Select City</option>');
                    for(var i=0;i<data.length;i++){
                        $('#city_id').append('<option value="'+data[i].id+'"> '+data[i].name+'</option>');
                    }
                }
            });
        });

	

	$(document).on('change', '#job_category_id', function() {
			$(".my-skills").html("");
			$("#selSkills").empty();
			//$('#skill_class').hide();
			if($(this).val()==""){
				var id =0;
			}else{
				var id =$(this).val();
			}
            $.ajax({
                dataType:'json',
                url:  "{{route('api-public-jobtitles')}}",
                data: {'jobCategoryId' : id},
                success: function(data){
                    $('#job_title_id').html('<option value="">Select job title</option>');
                    for(var i=0;i<data.length;i++){
                        $('#job_title_id').append('<option value="'+data[i].id+'"> '+data[i].title+'</option>');
                    }
                }
            });
        });

	$("select[name='job_title_id']").change(function() {
			$(".my-skills").html("");
			$("#selSkills").empty();
			$(".my-skills").html("<img src='{{asset('imgs/loader.gif')}}' />");
			//$('#skill_class').show();
			if($(this).val()==""){
				var id =0;
				//$('#skill_class').hide();
			}else{
				var id =$(this).val();
			}
			$.ajax({
				url : "{{route('api-public-skills')}}",
				data: {
					jobTitleId : id
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


</script>
@endpush
