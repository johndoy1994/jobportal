<?php

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
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Your job match status</h4>
			</div>
			<div class="panel-body">
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
	

	@push('footer')
	<script>
	$(document).ready(function() {
		$("a[role='modify-profile']").click(function() {
			var api = $(this).attr('data-api');
			var t = $(this);
			var success = false;
			var target = $(t).attr('data-target');

			$(t).addClass('hide');
			$(t).parent().append("<img src='{{asset('imgs/loader.gif')}}' />");
			$.ajax({
				url : api,
				type: "post",
				dataType: "json",
				data: {
					_token: "{{csrf_token()}}"
				},
				success: function(json) {
					if(json.success) {
						success=true;
						$(t).parent().html("<label class='label label-success'>"+json.message+"</label>");
						if(json.show_all) {
							$(".table-match .tr-match.hide").removeClass('hide');
						}
					} else {
						success=false;
						alert(json.message);
					}
				},
				error: function(er) {
					alert("Sorry, there was an error while modifying your profile, please try again.");
				},
				complete: function(data) {
					if(success) {
						$(target).removeClass("glyphicon-remove");
						$(target).addClass("glyphicon-ok");
						$(t).removeClass('hide');
					} else {
						$(t).removeClass('hide');
					}
					$(t).parent().find("img").remove();
				}
			});
			return false;
		});
	});
	</script>
	@endpush
@endif