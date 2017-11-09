@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@if(isset($login_level) && $login_level==1)
				<div class="alert alert-success">
					You're successfully signed out.</br>
					You were signed in with Facebook, please click <b><a style="color:black" href="https://facebook.com" target="_blank"> here </a></b> to sign out from Facebook.
				</div>
				@elseif(isset($login_level) && $login_level==2)
				<div class="alert alert-success">
					You're successfully signed out.</br>
					You were signed in with Linkedin, please click <b><a style="color:black" href="https://linkedin.com/" target="_blank"> here </a></b> to sign out from Linkedin.
				</div>
				@else
					@if(isset($is_jobseeker) && $is_jobseeker==0)
						@include('includes.frontend.request_messages')
						@include('includes.frontend.validation_errors')
					@else
						<div class="alert alert-danger">
							Sorry, sign in failed. If you are an employer, please sign
							<a href="{{route('recruiter-account-signin')}}" style="color:black;">here.</a>
						</div>
					@endif
				@endif
			</div>
			<div class="col-md-12">
				<div class="well">
					<legend>Search Job</legend>
					<form class="row" action="{{route('job-search')}}">
						<div class="col-md-5 col-sm-12">
							<div class="form-group">
								<label class="control-label">What:</label>
								<div class="input-group">
									<span class="input-group-addon">
										<input type="text" class="form-control" name="keywords" />
									</span>
								</div>
							</div>						
						</div>
						<div class="col-md-5 col-sm-12">
							<div class="form-group">
								<label class="control-label">Where:</label>
								<div class="input-group">
									<span class="input-group-addon">
										<input type="text" class="form-control col-lg-6" name="location" />
									</span>
									<span class="input-group-addon">
										<select name="radius" class="form-control col-lg-6">
											@foreach($searchMiles as $searchMile)
												<option value="{{$searchMile->mile}}">{{($searchMile->mile==-1) ? "All": $searchMile->mile. " mile(s)"}}</option>
											@endforeach
										</select>
									</span>
								</div>
							</div>						
						</div>
						<div class="col-md-2 col-sm-12">
							<div class="form-group">
								<label class="control-label"></label>
								<div class="input-group">
									<span class="input-group-btn">
										<button style="margin: 23px 0px" type="submit" class="btn btn-primary">Search</button>
									</span>
								</div>
							</div>						
						</div>
					</form>
				</div>
				<div class="">
					<ul class="nav nav-tabs">
						<li><a href="#by-category" data-toggle="tab">Jobs by category</a></li>
						<li class="active"><a href="#by-location" data-toggle="tab">Jobs by location</a></li>
						<li><a href="#by-companies" data-toggle="tab">Jobs by companies</a></li>
						<li><a href="#by-popular-searches" data-toggle="tab">Popular searches</a></li>
					</ul>
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade" id="by-category">
							<div class="panel">
								<div class="panel-body">
									@foreach($jobCategories as $jobCategory)
										@if($jobCategory->getJobCount() > 0)
											<a class="btn btn-sm " href="{{$jobSearchUri([],['keywords'=>$jobCategory->getName()])}}">{{$jobCategory->getName()}} ({{$jobCategory->getJobCount()}})</a>
										@endif
									@endforeach
								</div>
							</div>
						</div>
						<div class="tab-pane fade active in" id="by-location">
							<div class="panel">
								<div class="panel-body">
									<div class="row">
										@foreach($states as $state)
											<div class="col-md-3">
												<div class="well well-sm">
													<legend>{{$state->getName()}} <small>({{$state->jobCount}})</small></legend>
													<p>
														<?php
														$cities = $state->Cities()->whereIn('id', $state->getCityIds())->get();
														foreach($cities as $city) {
															$location = $city->getName().", ".$state->getName();
															$link = $jobSearchUri([],['location' => $location]);
															echo '<a class="btn btn-sm " href="'.$link.'">'.$city->getName().'</a>';
														}
														?>
													</p>
												</div>
											</div>
										@endforeach
									</div>									
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="by-companies">
							<div class="panel">
								<div class="panel-body">
									@foreach($companies as $company)
										<a class="btn btn-sm " href="{{$jobSearchUri([],['employer'=>$company->id])}}">{{$company->getCompanyName()}} ({{$company->jobCount}})</a>
									@endforeach
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="by-popular-searches">
							<div class="panel">
								<div class="panel-body">
									@foreach($publicSearch as $search)
										<a class="btn btn-sm" href="{{$jobSearchUri([],$search->getParams())}}">{{$search->getDisplayString()}} ({{$search->results}})</a>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('footer')
<script>
$(document).ready(function() {
	$("input[name='keywords']").autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "{{route('api-public-jobkeywords')}}",
				dataType: "json",
				data: {
					q: request.term
				},
				success: function( data ) {
					response($.map(data, function(v,i){
					    return {
		                	//label: v.keyword + " ("+v.jobCount+")",
		                	label: v.keyword,
		                	value: v.keyword
		                };
					}));
				}
			});
		}
	});

	$("input[name='location']").autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "{{route('api-public-locations')}}",
				dataType: "json",
				data: {
					q: request.term,
					limit : 10
				},
				success: function( data ) {
					response($.map(data.data, function(v,i){
					    return {
		                	label: v.name+', '+v.state_name+', '+v.country_name,
		                	value: v.name+', '+v.state_name+', '+v.country_name
		                };
					}));
				}
			});
		}
	});
});
</script>
@endpush