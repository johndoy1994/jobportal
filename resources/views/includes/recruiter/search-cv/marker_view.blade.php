<div style="width:320px; text-align:left; max-height: 300px; overflow:auto">
	@if(count($cvs)>1)
		<h5>{{count($cvs)}} CVs</h5>
	@endif

	@foreach($cvs as $cv)
			<table class="">
				<tr>
					<td colspan="2">
						<a href="{{$cvSearchUri($filters, ['id'=>$cv->id, 'marker_ids'=> null], 'recruiter-cv-detail')}}"><strong>{{$cv->getName()}}</strong></a>
					</td>
				</tr>
				<tr>
					<td>
						<b>Education :</b> {{$cv->getHighestEducationString()}}<br/>
						<b>Recent Job :</b> {{$cv->getRecentJobTitle()}} ({{$cv->job_title}})<br/>
						<b>Experience :</b> {{$cv->getExperienceName()}} ({{$cv->getExperienceLevelName()}})<br/>
						<b>Job Type :</b> {{$cv->getJobTypeString()}}<br/>
						<b>Job Category :</b> {{$cv->job_category_name}}<br/>
						<b>Salary : </b> {{$cv->getDesiredSalaryString()}}<br/>
					</td>
					<td>
						<img style="width:100px" src="{{route('account-avatar-100x100', ['id' => $cv->id])}}" />
					</td>
				</tr>
			</table>
			@if(count($cvs) > 1)
				<hr/>
			@endif
	@endforeach

</div>