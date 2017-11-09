@extends('layouts.backend')

@section('title', 'Export Jobsekeer')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Export Jobsekeer</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('import-export-list')}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
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
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('export-jobseeker-post')}}" enctype="multipart/form-data">
					{{csrf_field()}}
					<div class="form-group">
						<label class="control-label col-lg-3"></label>
						<div class="col-lg-9">
							<input type="radio" name="export_type" checked value="1" id="export_type"> Date &nbsp;&nbsp;
							<input type="radio" name="export_type"  value="0" id="export_type"> All 
						</div>
					</div>
					<div id="date_div">
						<div class="form-group">
							<label class="control-label col-lg-3">Start Date *:</label>
							<div class="col-lg-3">
								<input type="text"  required name="starting_date" id="starting_date" value="{{old('starting_date')}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">End Date *:</label>
							<div class="col-lg-3">
								<input type="text" required name="ending_date" id="ending_date" value="{{old('ending_date')}}" class="form-control" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-primary">Download</button>
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
<script src="{{asset('backend/js/datetimepicker/jquery-ui.min.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon.js')}}"></script>
<script src="{{asset('backend/js/datetimepicker/jquery-ui-timepicker-addon-i18n.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
	var dateToday = new Date();
	$('#starting_date').datepicker({
		//minDate:dateToday,
		dateFormat: 'yy-mm-d',
      	onSelect: function(selectedDate) {
            $('#ending_date').datepicker('option', 'minDate', selectedDate || new Date());
      	}
	});

	$('#ending_date').datepicker({
		//minDate:dateToday,
		dateFormat: 'yy-mm-d',
      	onSelect: function(selectedDate) {
            $('#starting_date').datepicker('option', 'maxDate', selectedDate || new Date());
      	}
	});
});
$(document).on('change','#export_type',function(){
	if($(this).val()==0){
		$('#starting_date').val('');
		$('#ending_date').val('');
		$('#date_div').hide();
		$('#date_div').removeAttr("required");
		$('#starting_date').removeAttr("required");
		$('#ending_date').removeAttr("required");
	}else{
		$('#starting_date').val('');
		$('#ending_date').val('');
		$('#date_div').show();
		$('#date_div').attr("required","required");
		$('#starting_date').attr("required","required");
		$('#ending_date').attr("required","required");
	}
});
</script>
@endpush