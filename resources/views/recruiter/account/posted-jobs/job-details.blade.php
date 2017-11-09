@extends('layouts.recruiter')

@section('title', 'Posted Jobs')

@section('content')
<style type="text/css">
	.calendar-day-selectable1{
		background: none repeat scroll 0 0 #EAC697 !important;
	}

	.fc-today {
    	background: none repeat scroll 0 0 #64dcf4 !important;
	}
	.fc-day, .fc-head-container.fc-widget-header, .fc-view.fc-month-view.fc-basic-view{
		border:1px solid #ccc !important;
	}
	.fc-head-container.fc-widget-header
	{
		font-size:15px;
		font-weight: bold;
		color:#333333;
	}
	.fc-day-number {
 	   text-align: left!important;
 	   font-size:15px;
 	   color:#333333;
	}
	.seeker-day-count{
	    bottom: 0;
	    float: right;
	    font-size: 20px;
	    font-weight: bold;
	    margin-left: 77px;
	    position: absolute;
	    color: #C1E318;

	}
	.today{
		background: #64dcf4 !important;
	}
	.seeker-day-background1{
		background: linear-gradient(135deg, #EAC697 20px, transparent 0px, transparent) repeat scroll 0 0 #5a9c5a;
	}


	 .seeker-day-background {
		  /*background: linear-gradient(135deg, red 15px, transparent 0, transparent);*/
		  background: linear-gradient(140deg, #EAC697 65px, transparent 67px, transparent) repeat scroll 0 0 #5a9c5a;
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-md-3 col-sm-12">
			@include('includes.recruiter.account.sidebar')
		</div>
		<div class="col-md-9 col-sm-12">
			<div class="well well-sm">
				<div class="row">
					<div class="col-md-12">
						<div class="panel">
						@if($jobDetails)
							<div class="panel-heading clearfix" style="border-bottom: 1px solid #e1e1e1">
									<div class="pull-left">
										<h3>{{$jobDetails->title}}</h3>
									</div>	
									<div class="pull-right">
										<a href="#showMap" role="show-job-on-map" class="btn btn-warning pull-right"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> Map</a>
									</div>

							</div>
								
							<div class="panel-body">
								<div class="col-md-12 col-sm-12">
								<div id="jobMap" style="height:300px; margin-bottom:20px" class="hide"></div>
									<ul class="list-unstyled">
										<li>
											<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
											{{$jobDetails->full_address}}
										</li>
										<li>
											<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
											@if($jobDetails->getSalary() == 0)
												&nbsp;Salary : {{$jobDetails->getSalaryString()}}
											@else
												&nbsp;{{$jobDetails->getSalaryString()}}
											@endif
										</li>
										<li>
											<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
											{{$jobDetails->jobType_name}}
										</li>
										<li>
											<span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
											{{$jobDetails->company_name}}

										</li>
										<li>
											<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
											&nbsp;{{$jobDetails->getPostedDayString()}}
											@if($jobDetails->isExpiringIn() !==false ) 
												<label class="label label-warning">Expires in {{$jobDetails->isExpiringIn()}}</label>
											@endif
										</li>
									</ul>
									

									<div  class="panel panel-default">
										<div id="job-details" class="panel-heading" style="padding:5px;">
											<h4><a href="">Job Description</a></h4>
										</div>
										<div id="job-details-toggle" class="panel-body">
											<p>
											@if($jobDetails->description)
												{!!$jobDetails->description!!}
											@else
												N/A
											@endif	
											</p>
										</div>
									</div>

									@if($jobDetails->day_selection==1)
										<?php $arrayDays=array(); 
										?>
										@foreach($jobDetails->applications as $applicationdata)
											@if($applicationdata->status=='in-process')
												<?php $data= $jobDetails->dayCountJsondecode($applicationdata['meta']); 
													if(isset($data['days'])){
														foreach ($data['days'] as  $value) {
															array_push($arrayDays, $value);
														}
													}
												?>									
											@endif
										@endforeach
										<?php $countsDays=array_count_values($arrayDays); ?>
										<select name="dates[]" multiple class="hide" >
											@if(isset($jobDetails->jMeta()['days']))
												@foreach($jobDetails->jMeta()['days'] as $date)
													<option value="{{$date}}" data-count="{{isset($countsDays[$date])? $countsDays[$date] : 0 }}" selected>{{$date}}</option>
												@endforeach
											@endif
										</select>
										<div class="panel panel-default">
											<div id="job-details-cal" class="panel-heading" style="padding:5px;">
												<h4><a href="">Availability Calender</a></h4>
											</div>

											<div id="job-details-cal-toggle" class="panel-body">
												<div id="calendar" style="background:white!important"></div>
												</br>
												<div class="row">
													<div class="col-md-3 text-center">
														<div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="today"></div>
														ToDay
													</div>
													<div class="col-md-3 text-center">
														<div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="calendar-day-selectable1"></div>
														Recruiter selected Days
													</div>
													<div class="col-md-3 text-center">
														<div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="seeker-day-background1"></div>
														Applicant selected days count
													</div>
												</div>
											</div>
										</div>
									@endif
									

									<div  class="panel panel-default">
										<div id="job-details-application" class="panel-heading" style="padding:5px;">
											<h4>
												<a href="">Applications Received</a>
											</h4>
										</div>
										<div id="job-details-application-toggle" class="panel-body">
										@if(Request::get('date'))
											<a style="margin-bottom:10px;" href="{{route('recruiter-postedjob-details',['job'=>$jobDetails->id])}}" class="btn btn-info pull-right">Reset</a>
										@endif	
											<table class="table table-bordered table-striped table-hover">
												<thead>
													<tr>
														<th>Name </th>
														<th class="text-center">Basic </th>
														<th class="text-center">requires </th>
													</tr>
												</thead>
												
												<tbody>
												@if($jobDetails->getAllJobApplication() && count($jobDetails->getAllJobApplication())>0)
													
													@foreach($jobDetails->getAllJobApplication() as $val)
														@if($val->status=='in-process')
															<?php
																$Data = array(); 

																if($val->meta) {
														            $Data= json_decode($val->meta, true);
														        }
														        
														        if(isset($Data['days'])){
																	$Data = $Data['days'];
																}
																
															if(Request::get('date')){
																if(in_array(Request::get('date'), $Data)){
																	$default=true;	
																}else{
																	$default=false;	
																}	
															}else{
																$default=true;
															}
																
															?>	
															@if($default)
																<?php $status = $val->getMatchingStatus(); ?>
																	<tr>
																		<td><a href="{{route('recruiter-candidatesdetails',array_merge( ['UserId'=> $val->user_id], ['jobId'=> $val->job_id],['view'=>'applicant']))}}">{{$val->name}}</a></td>
																		<td class="text-center">
																			<?php
																				echo "<label class='label label-primary'>".$status[1]."/".$status[0]."</label>";
																			?>
																		</td>
																		<td class="text-center">
																			<?php 
																				echo "<label class='label label-success'>".$status[3]."/".$status[2]."</label>";
																			?>
																		</td>
																	</tr>
															@endif
														@endif	
													@endforeach
												@else
													<tr>
														<td colspan="3" class="text-center">No application record(s) found.</td>
													</tr>
												@endif	
												</tbody>
												
											</table>
										</div>
									</div>
									
								</div>
							</div>
						@else
							No Result founds.
						@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('footer')
<script type="text/javascript">
$(document).ready(function() {
	var select = "select[name='dates[]']";
    $(".panel-body #calendar").fullCalendar({
		dayRender: function(date, cell) {
			var todayFormat = "{{\Carbon\Carbon::now()->format('Y-m-d')}}";
			var dateFormat = date.format('Y-MM-DD');
			if($(select+" option[value='"+dateFormat+"']").text()) {
				if($(select+" option[value='"+dateFormat+"']").data('count') > 0){
					cell.addClass('seeker-day-background');	
					var url = "{{route('recruiter-postedjob-details',array_merge(['job'=>$jobDetails->id,'date'=>'dateval','#job-details-application']))}}";
					url = url.replace('dateval', dateFormat);
					cell.html("<label class='seeker-day-count'><a  href='"+url+"'>"+$(select+" option[value='"+dateFormat+"']").data('count')+"</a></lable>");
					
				}else{
					cell.addClass('calendar-day-selectable1');	
				}
			}
        }
	});
	$(".panel-body #calendar").fullCalendar('render');

	$("#job-details").click(function() {
        $("#job-details-toggle").toggle();
        return false;
    });

    $("#job-details-cal").click(function() {
        $("#job-details-cal-toggle").toggle();
        return false;
    });

    $("#job-details-application").click(function() {
        $("#job-details-application-toggle").toggle();
        return false;
    });
});

var mapLoaded = false;
function jobMap() {
	@if($jobDetails)
	$("a[role='show-job-on-map']").click(function() {
		if($("#jobMap").is(".hide")) {
			if(!mapLoaded) {
				var map = new google.maps.Map(document.getElementById('jobMap'), {
			    	center: {lat: {{$jobDetails->latitude}}, lng: {{$jobDetails->longitude}}},
			    	zoom: 15
			    });
			    var jobMarker = new google.maps.Marker({
					position: { lat: {{$jobDetails->latitude}}, lng: {{$jobDetails->longitude}} },
					map: map,
					title: "{{$jobDetails->getTitle()}}"
				});
			    mapLoaded = true;
			}
			$("#jobMap").removeClass("hide");
		} else {
			$("#jobMap").addClass("hide");
		}
	});
	@endif
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNmXGiokPzXN1lSHDSzB7qyN7BMvgUNYQ&callback=jobMap&libraries=geometry" async defer></script>
@endpush



