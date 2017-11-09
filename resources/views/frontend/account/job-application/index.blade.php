@extends('layouts.frontend')

@section('title', 'My Job Application')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">My Job Application</h3>
					</div>
				
					<div class="panel-body">
						<p>Here are the jobs you have applied for within the last three months:</p>

						@foreach($jobApplications as $jobApplication)
							<div class="col-md-6">
									<div class="panel {{ $jobApplication->isCancelled() ? 'panel-warning' : ( $jobApplication->isAccepted() ? 'panel-success' : ( $jobApplication->isRejected() ? 'panel-danger' : 'panel-default' ) ) }}">
									@if($jobApplication->job)
										<div class="panel-heading">
											<h3 class="panel-title">
												@if($jobApplication->job->isExpiredJob() && $jobApplication->job->isEndedJob() && $jobApplication->job->status='active')
													<a href="{{$jobSearchUri([],['jobId'=>$jobApplication->job_id],'job-detail')}}">{{$jobApplication->getJobTitle()}}</a>
												@else
													{{$jobApplication->getJobTitle()}}
												@endif
												@if(!$jobApplication->isCancelled())
													<form class="hide" name="cancelApp{{$jobApplication->id}}" method="post" action="{{route('account-cancel-job-application')}}">
														<input type="hidden" name="appId" value="{{$jobApplication->id}}" />
														{{csrf_field()}}
													</form>
													<a href="#"  style="margin-left:10px;" onClick="if(confirm('Are you sure to cancel this application ?')) { document.forms['cancelApp{{$jobApplication->id}}'].submit() } else { return false; }" class="pull-right label label-danger">Cancel</a>
												@endif	
												@if($jobApplication->isAccepted())
													<b class="pull-right">Accepted</b>
												@elseif($jobApplication->isRejected())
													<b class="pull-right">Rejected</b>
												@elseif($jobApplication->isCancelled())
													<b class="pull-right">Cancelled</b>
												@endif
												
											</h3>
										</div>
										<div class="panel-body">

											<div style="color:red; background-color:yellow;" class="pull-right">
												<?php 
													$job_message= "( This Job is ";
													$mes_data=array();
												?>
												@if(!$jobApplication->job->isExpiredJob())
													<?php  $mes_data[]='Expired'; ?>
												@endif

												@if(!$jobApplication->job->isEndedJob())
													<?php  $mes_data[]='Ended'; ?>
												@endif

												@if($jobApplication->job->status!='active')
													<?php  $mes_data[]='Inactive'; ?>
												@endif

												<?php 
													$resultMsg=implode($mes_data,',');
													if(!$jobApplication->job->isExpiredJob() || !$jobApplication->job->isEndedJob() || $jobApplication->job->status!='active'){
														echo $job_message.$resultMsg.' )';
													}

												 ?>
											</div>
											<div class="clearfix"></div>
											<ul class="list-unstyled">
												<li><b>Date Posted :</b> {{$jobApplication->job->created_at->format("d-m-Y H:i:s")}}</li>
												<li><b>Application Date :</b> {{$jobApplication->created_at->format('d-m-Y H:i:s')}}</li>
												@if($jobApplication->job->employer) 
													<li><b>Company :</b> {{$jobApplication->job->employer->getCompanyName()}}</li>
													@if($jobApplication->job->employer->user)
														<li><b>Contact :</b> <a href="mailto:{{$jobApplication->job->employer->user->email_address}}">{{$jobApplication->job->employer->user->email_address}}</a></li>
													@else
														<li><b>Contact :</b> N/A</li>
													@endif
												@else
													N/A
												@endif
												@if($jobApplication->job->jobType && $jobApplication->job->jobType->day_selection == 1)
													@if($jobApplication->isInProcess())
														<li>
														<form class="form-inline">
															<div class="form-group">
																<label class="control-label"><b>Selected Dates :</b></label>
																<div class="hide loader"><label>Saving</label> <img src="{{asset('imgs/loader.gif')}}" /></div>
																
																<select class="form-control selectedDates" style="width:100%" multiple id="selectedDates{{$jobApplication->id}}" data-application="{{$jobApplication->id}}">
																	<?php
																	if(count($jobApplication->job->getMetaDays()) > 0) {
																		$days = $jobApplication->job->getMetaDays();
																		$appDays = json_decode($jobApplication->meta, true);
																		if(isset($appDays["days"])) {
																			$appDays=$appDays["days"];
																		}
																		for($i=0;$i<count($days);$i++) {
																			try {
																				$date = \Carbon\Carbon::createFromFormat("Y-m-d", $days[$i])->format('d-m-Y');
																				$_date = \Carbon\Carbon::createFromFormat("Y-m-d", $days[$i])->format('Y-m-d');
																				$selected = in_array($_date, $appDays) ? 'selected' : '';
																				echo '<option value="'.$_date.'" '.$selected.'>'.$date.'</option>';
																			} catch(\Exception $e) {}
																		}
																	}
																	?>
																</select>
															</div>	
														</form>	
														</li>
														<script>
														$(document).ready(function() {
															$("#selectedDates{{$jobApplication->id}}").select2();
														});
														</script>
													@else
														<?php
														$jMeta = json_decode($jobApplication->meta, true);
														if(is_array($jMeta) && isset($jMeta["days"])) {
															echo '<li><b>Selected Dates:</b>';
															echo '<br/><div style="max-height:100px;overflow:auto">';
															for($i=0;$i<count($jMeta["days"]);$i++) {
																try {
																	echo \Carbon\Carbon::createFromFormat("Y-m-d", $jMeta["days"][$i])->format('d-m-Y');
																	if($i<count($jMeta["days"])-1) {
																		echo ", ";
																	}
																} catch(\Exception $e) {}
															}
															echo '</div></li>';
														} else {
															echo '<li><b>Selected Dates:</b> N/A</li>';
														}
														?>
													@endif
												@endif
											</ul>
										</div>
									@else
									@endif
								</div>
							</div>
						@endforeach

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('footer')
<script>
$(document).ready(function() {
	$("select.selectedDates").change(function() {
		var dateString = $(this).val();
		var appId = $(this).attr('data-application');
		var t = $(this);
		$(t).parent().find(".loader, .loader img").removeClass('hide');
		$(t).parent().find(".loader label").html("Saving");
		$(t).parent().find(".loader label").removeClass("label label-warning label-success label-danger");
		$(t).parent().find(".loader label").addClass("label label-warning");
		$(t).attr('disabled','disabled');

		$.ajax({
			url : "{{route('api-secure-updatedatesjobapplication')}}",
			type:"POST",
			data: {
				appId: appId,
				dateString: dateString,
				_token: "{{csrf_token()}}"
			},
			dataType: "json",
			success: function(json) {
				$(t).parent().find(".loader img").addClass("hide");
				$(t).parent().find(".loader label").html(json.message);
				if(json.success) {
					$(t).parent().find(".loader label").removeClass("label label-warning label-success label-danger");
					$(t).parent().find(".loader label").addClass("label label-success");
				} else {
					$(t).parent().find(".loader label").removeClass("label label-warning label-success label-danger");
					$(t).parent().find(".loader label").addClass("label label-danger");
				}
				$(t).removeAttr('disabled');
			},
			error: function() {
				$(t).parent().find(".loader").addClass("hide");
				$(t).removeAttr('disabled');
				alert("There was an error while saving dates, please try again.");
			}
		});

		//$(this).removeAttr('disabled');

	});
});
</script>
@endpush