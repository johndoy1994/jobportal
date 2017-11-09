<?php
$showCalendar = \App\Models\JobType::isDaySelection(old('job_type_id')) || count($oldDates) > 0;
if(count($oldDates) > 0) {
	$job_type_id = old('job_type_id') ? old('job_type_id') : 0;
	if($job_type_id == 0 && isset($Jobs) && $Jobs) {
		$job_type_id = $Jobs->job_type_id;
	}
	$showCalendar = \App\Models\JobType::isDaySelection($job_type_id);
}
if(!$showCalendar && isset($Jobs) && $Jobs) {
	$showCalendar = \App\Models\JobType::isDaySelection($Jobs->job_type_id);
}

?>

<div class="form-group @if($showCalendar) showa @else hide @endif job-calendar">
	<label class="control-label col-lg-3">Day Selection *</label>
	<div class="col-lg-7">
		<select name="dates[]" multiple class="hide">
			@if($oldDates)
				@foreach($oldDates as $date)
					<option value="{{$date}}" selected>{{$date}}</option>
				@endforeach
			@endif
		</select>
		<div id="calendar" style="background:white!important"></div>
		<br/>
		<div class="row">
			<div class="col-md-3 text-center">
				<div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="calendar-day-not-selectable"></div>
				Past Days
			</div>
			<div class="col-md-3 text-center">
				<div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="calendar-date-selected"></div>
				Selection
			</div>
			<div class="col-md-3 text-center">
				<div style="width:32px;height:32px; margin:0px auto; border: 1px solid #AAA" class="calendar-day-not-selectable calendar-date-selected"></div>
				Selected Past Day
			</div>
			<div class="col-md-3 text-center">
				<div style="width:32px;height:32px; margin:0px auto; background: white; border: 1px solid #AAA"></div>
				Selectable
			</div>
		</div>
	</div>
</div>

@push('after-footer')
<script>
	$(document).ready(function() {
		var select = "select[name='dates[]']";
		
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
				if($(this).is(".calendar-day-selectable")) {
					var dateFormat = date.format('Y-MM-DD');
					if($(this).is(".calendar-date-selected")) {
						$(this).removeClass('calendar-date-selected');
						$(select+" option[value='"+dateFormat+"']").remove();
					} else {
						$(this).addClass('calendar-date-selected');
						$(select).append(new Option(dateFormat,dateFormat));
	                    $(select+" option[value='"+dateFormat+"']").attr('selected','selected');
					}
				} else {
					alert("Sorry, you can't select past days.");
				}
			}
		});
		$(".job-calendar #calendar").fullCalendar('render');
		

		$("select[name='job_type_id']").change(function() {
			var value = $(this).val();
			var show_calendar = $(this).find("option[value='"+value+"']").attr('data-show-calendar');
			if(show_calendar==1) {
				$(".job-calendar").removeClass('hide');
				$(".job-calendar #calendar").fullCalendar('render');		
			} else {
				$(".job-calendar").addClass('hide');
			}
		});
	});
</script>
@endpush