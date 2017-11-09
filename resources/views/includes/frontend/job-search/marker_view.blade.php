<div style="width:320px; text-align:left; max-height: 300px; overflow:auto">
	@if(count($jobs)>1)
		<h5>{{count($jobs)}} job(s)</h5>
	@endif

	@foreach($jobs as $job)
			<table class="">
				<tr>
					<td colspan="2">
						<a href="{{$jobSearchUri($route_params, ['jobId'=>$job->id, 'marker_ids'=> null], 'job-detail')}}"><strong>{{$job->getTitle()}}</strong></a>
					</td>
				</tr>
				<tr>
					<td>
						Salary: {{$job->getSalaryString()}}<br/>
						Date: {{$job->getrenew_date()}}<br/>
						Company: {{$job->employer->getCompanyName()}}
					</td>
					<td>
						<img style="width:100px" src="{{route('account-employer-avatar', ['id' => $job->employer_id])}}" />
					</td>
				</tr>
			</table>
			@if(count($jobs) > 1)
				<hr/>
			@endif
	@endforeach

</div>