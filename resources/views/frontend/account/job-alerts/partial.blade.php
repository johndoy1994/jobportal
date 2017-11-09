<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">My jobs alerts</h3>
    </div>
	<div class="panel-body">
	
		<div class="col-md-12 ">
			<div class="form-group">
				<label>Get all the latest jobs matching your criteria sent to your inbox in one alert every morning. You can create up to 3 jobs by email alerts.</label>
				<a class="btn btn-primary" href="{{route('account-settings-create-jobalert')}}">Create email alert</a>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label col-lg-3">How whould you like to receive your email?</label>
				<div class="form-group">
					<div class="loader hide">
						<img src="{{asset('imgs/loader.gif')}}" />
					</div>
					<div class="radio">
						<label><input type="radio" name="job_alert_content_type" value="HTML" {{$user && $user->profile && $user->profile->alert_content_type == 'HTML' ? 'checked' : ''}} > HTML</label>
						<label><input type="radio" name="job_alert_content_type" value="PLAIN" {{$user && $user->profile && $user->profile->alert_content_type == 'PLAIN' ? 'checked' : ''}}> Text</label>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<br/>

			@foreach($jobAlerts as $jobAlert)
				<div class="col-md-6 col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<strong>{{$jobAlert->getAlertTitle()}}</strong>

							@if($jobAlert->city)
								in <strong>{{$jobAlert->city->getName()}}</strong>
							@endif

							@if($jobAlert->radius > 0)
								+ <strong>{{$jobAlert->radius}} miles</strong>
							@endif
						</div>
						<div class="panel-body">

							<strong>Status :</strong><br/><br/>
							<div class="btn-group">
								<a href="{{route('account-settings-update-jobalert-status', ['alert'=>$jobAlert->id, 'action'=>'on'])}}" class="btn btn-{{ $jobAlert->deleted_at ? 'default' : 'primary' }}">On</a>
								<a href="{{route('account-settings-update-jobalert-status', ['alert'=>$jobAlert->id, 'action'=>'off'])}}" class="btn btn-{{ $jobAlert->deleted_at ? 'primary' : 'default' }}">Off</a>
							</div>

							<br/><br/>

							<strong>Criteria :</strong> <br/>
							
							@if(strlen(trim($jobAlert->keywords)) > 0)
								{{$jobAlert->keywords}}
							@endif

							@if($jobAlert->city)
								in {{$jobAlert->city->getName()}}
							@endif

							@if($jobAlert->radius > 0)
								+ {{$jobAlert->radius}} miles
							@endif

							@if($jobAlert->salary_range_from > 0)
								, at least {{$jobAlert->salary_range_from}} salary
								@if($jobAlert->salaryType)
									per {{$jobAlert->salaryType->perWord()}}
								@endif
							@endif

						</div>
						<div class="panel-footer">
							<a href="{{route('account-settings-edit-jobalert', ['alert'=>$jobAlert->id])}}">Edit creteria</a> | 
							<a href="{{route('frontend-subscribedjobs', ['alert'=>$jobAlert->id])}}">View my subscribed jobs</a>
							<a href="{{route('account-settings-update-jobalert-status', ['alert'=>$jobAlert->id, 'action'=>'delete'])}}" onClick="return confirm('Are you sure to delete this alert ?')">Delete alert</a>
						</div>
					</div>
				</div>
			@endforeach

		</div>
	</div>					
</div>

@push('footer')
<script>
$(document).ready(function() {

	$("input[name='job_alert_content_type']").change(function() {
		var value = $(this).val();
		var t = $(this);

		$(t).parent().parent().addClass("hide");
		$(t).parent().parent().parent().find(".loader").removeClass("hide");

		$.ajax({
			url : "{{route('api-public-savejobalertcontenttype')}}",
			dataType: "json",
			type: "post",
			data : {
				alert_content_type: value,
				_token : "{{csrf_token()}}"
			},
			success: function(json) {
				if(json.success) {

				} else {
					alert(json.message);		
				}
			},
			error: function() {
				alert("Failed to set alert content type, please try again.");
			},
			complete: function() {
				$(t).parent().parent().removeClass("hide");
				$(t).parent().parent().parent().find(".loader").addClass("hide");
			}
		});

	});

});
</script>
@endpush