@extends('layouts.frontend')

@section('title', 'Location selector')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.request_messages')
				@include('includes.frontend.validation_errors')
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="page-header">
					<h2>Location Selector</h2>
				</div>
			</div>
			<div class="col-md-12">
				<center>
					Sorry we couldn't found your exact address, please select city...
				</center>
				<br/>
			</div>
			<div class="col-md-12">
				@if(count($cities) > 0)
					<form class="form-horizontal" action="{{route('job-search')}}">
						@foreach(Request::all() as $key => $field)
							@if($key!="location")
								<input type="hidden" name="{{$key}}" value="{{$field}}" />
							@endif
						@endforeach
						
							<?php
							$FlgTmp = 0;
							$DispCombo = '';
							$DispCombo = '<div class="form-group">
							<label class="control-label col-lg-3">Select City : </label>
							<div class="col-lg-8">';
							$DispCombo .= '<select name="location" class="form-control">';

							foreach($cities as $city)
							{
								
									$jobCount = $city->jobCount();
								if($jobCount > 0)
								{
									//$DispCombo .= '<option value="'.$city->full_address.'">'.$city->full_address}} ({{$jobCount}})</option>';
									$DispCombo .= '<option value="'.$city->full_address.'">'.$city->full_address.'</option>';
									$FlgTmp = 1;
								}
								
							}
							$DispCombo .= '<select/>';
							
							
							$DispCombo .= '</div>
						</div>	
							
						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<div class="col-lg-8">
								<button type="submit" class="btn btn-primary">Proceed</button>
							</div>
						</div>';
						if($FlgTmp == 1)
							{
								echo $DispCombo;
							}
						?>
					</form>
				@endif
			</div>
		</div>
	</div>
@endsection