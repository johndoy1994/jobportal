@extends('layouts.frontend')

@section('title', 'Job Search')

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
				<h5>
					{{$results->total()}}  
					@if($route_params["keywords"] != "")
						<mark>{{$route_params["keywords"]}}</mark>
					@endif
					jobs
					@if($route_params["location"] != "")
						in <mark>{{$route_params["location"]}}</mark>
						@if($route_params["radius"] > 0)
							+ <mark>{{$route_params["radius"]}} miles</mark>
						@endif
					@endif
					@if(isset($jobCategoryName))
						{{($route_params["location"] != "") ? "+" : "in" }}  <mark>{{$jobCategoryName}}</mark>
					@endif
					@if(isset($jobTypeName))
						+ <mark>{{$jobTypeName}}</mark>
					@endif
					@if(isset($recruiterTypeName))
						+ <mark>{{$recruiterTypeName}}</mark>
					@endif
					@if(isset($salaryTypeName))
						+ <mark>{{$salaryTypeName}}</mark>
					@endif
					@if(isset($daysAgo) && $daysAgo!=0)
						+ <mark>{{$daysAgo}} Days ago</mark>
					@endif
					@if(isset($companyName) && $companyName)
						posted by <mark>{{$companyName}}</mark>
					@endif
				</h5>
				<br/>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-sm-12">
				<a href="{{$jobSearchUri([])}}" class="btn btn-default btn-block">Reset All</a><br/>
				<div class="well well-sm">
					<legend>Explore results <a href="{{$jobSearchUri($route_params,['keywords'=>'','location'=>''])}}" class="pull-right"><small>reset</small></a></legend>
					<form>
						@foreach($route_params as $key=>$value)
							@if($key!="keywords" && $key!="location")
								<input type='hidden' name='{{$key}}' value="{{$value}}" />
							@endif
						@endforeach
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">What :</span>
								<input type="text" class="form-control" name="keywords" value="{{$route_params['keywords']}}" />
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">Update</button>
								</span>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">Where :</span>
								<input type="text" class="form-control" name="location" value="{{$route_params['location']}}" />
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">Update</button>
								</span>
							</div>
						</div>

						<div class="form-group">
							<legend>Locations  <a href="{{$jobSearchUri($route_params,['location'=>''])}}" class="pull-right"><small>reset</small></a></legend>
							<ul>
							
								@foreach($locations as $location)
									@if($Extcities)
										@foreach($Extcities as $Extcitie)
											@if($Extcitie->id==$location->city_id)
												<?php
													$address=$location->city_name.", ".$location->state_name.", ".$location->country_name;
													$jobLocationCount = $jobSearchCount($route_params, array_merge(['location'=>$address,'radius_data'=>$radius_data]));
												?>
												
												@if($jobLocationCount > 0)
												<li>
													<a href="{{$jobSearchUri($route_params, ['location'=>$address])}}">
														@if(strtolower($route_params["location"]) == strtolower($location->city_name) || strtolower($route_params["location"]) == strtolower($location->city_name.", ".$location->state_name) || strtolower($route_params["location"]) == strtolower($address))
															<b>{{$address}} ({{$jobLocationCount}})</b>
														@else
															{{$address}} ({{$jobLocationCount}})
														@endif
													</a>
												</li>
												@endif
											@endif
										@endforeach
									@else
											<?php
													$address=$location->city_name.", ".$location->state_name.", ".$location->country_name;
													$jobLocationCount = $jobSearchCount($route_params, array_merge(['location'=>$address,'radius_data'=>$radius_data]));
												?>
												
												@if($jobLocationCount > 0)
												<li>
													<a href="{{$jobSearchUri($route_params, ['location'=>$address])}}">
														@if(strtolower($route_params["location"]) == strtolower($location->city_name) || strtolower($route_params["location"]) == strtolower($location->city_name.", ".$location->state_name) || strtolower($route_params["location"]) == strtolower($address))
															<b>{{$address}} ({{$jobLocationCount}})</b>
														@else
															{{$address}} ({{$jobLocationCount}})
														@endif
													</a>
												</li>
												@endif
									@endif
								@endforeach
							</ul>
						</div>

						<div class="form-group">
							<legend>Miles Radius  <a href="{{$jobSearchUri($route_params,['radius'=>0])}}" class="pull-right"><small>reset</small></a></legend>
							<div class="btn-group btn-group-justified">
								@foreach($searchMiles as $searchMile)
									<a href="{{$jobSearchUri($route_params, ['radius'=>$searchMile->mile])}}" class="btn @if(trim($route_params['location']) == '') disabled @elseif($searchMile->mile == $route_params['radius']) btn-primary @else btn-default @endif">{{($searchMile->mile==-1)? "All" : $searchMile->mile}}</a>
								@endforeach
							</div>
						</div>
						<div class="form-group">
							<legend>Salaries  <a href="{{$jobSearchUri($route_params,['salaryType' => 0, 'salaryRate'=>0])}}" class="pull-right"><small>reset</small></a></legend>
							<ul class="list-unstyled">
								@if(($negotiableCount = $jobSearchCount($route_params, ['salaryType'=>0, 'salaryRate'=>0, 'onlyNegotiable'=>'yes']))>0)
									<li>
										@if(Request::has('onlyNegotiable')) <b> @endif
										<a href="{{$jobSearchUri($route_params, ['salaryType'=>0, 'salaryRate'=>0, 'onlyNegotiable'=>'yes'])}}">Negotiable ({{$negotiableCount}})</a>
										@if(Request::has('onlyNegotiable')) </b> @endif
									</li>
								@endif
								@foreach($salaryTypes as $salaryType)
									<li>
										<strong>{{$salaryType->getTypeName()}}</strong>
										<ul>
											@foreach($salaryType->salaryRange as $salaryRange)
												<?php
												$salaryRangeCount = $jobSearchCount($route_params, ['salaryType'=>$salaryType->id, 'salaryRate'=>$salaryRange->range_from, 'salaryRateTo'=>$salaryRange->range_to]);
												?>
												@if($salaryRangeCount > 0)
												<li>
													<a href="{{$jobSearchUri($route_params, ['salaryType'=>$salaryType->id, 'salaryRate'=>$salaryRange->range_from, 'salaryRateTo'=>$salaryRange->range_to])}}">
														@if($route_params['salaryType'] == $salaryType->id && $route_params['salaryRate'] == $salaryRange->range_from)
															<b>{{$salaryRange->range_from}}-{{$salaryRange->range_to}} ({{$salaryRangeCount}})</b>
														@else
															{{$salaryRange->range_from}}-{{$salaryRange->range_to}} ({{$salaryRangeCount}})
														@endif
													</a>
												</li>
												@endif
											@endforeach
										</ul>
									</li>
								@endforeach
							</ul>
						</div>
						<div class="form-group">
							<legend>Date posted  <a href="{{$jobSearchUri($route_params,['daysAgo'=>0])}}" class="pull-right"><small>reset</small></a></legend>
							<ul>
								@foreach($searchDayAgos as $searchDayAgo)
									<?php
									$daysAgoCount = $jobSearchCount($route_params, ['daysAgo'=>$searchDayAgo->day]);
									?>
									@if($daysAgoCount > 0)
									<li>
										<a href="{{$jobSearchUri($route_params, ['daysAgo'=>$searchDayAgo->day])}}">
										@if($route_params["daysAgo"] == $searchDayAgo->day)
											<b>{{$searchDayAgo->label}} ({{$daysAgoCount}})</b>
										@else
											{{$searchDayAgo->label}} ({{$daysAgoCount}})
										@endif
										</a>
									</li>
									@endif
								@endforeach
							</ul>
						</div>
						<div class="form-group">
							<legend>Recruiters  <a href="{{$jobSearchUri($route_params,['recruiterType'=>0])}}" class="pull-right"><small>reset</small></a></legend>
							<ul>
								@foreach($recruiterTypes as $recruiterType)
									<?php
									$recruiterTypeCount = $jobSearchCount($route_params, ['recruiterType'=>$recruiterType->id]);
									?>
									@if($recruiterTypeCount > 0)
									<li>
										<a href="{{$jobSearchUri($route_params, ['recruiterType'=>$recruiterType->id])}}">
											@if($route_params["recruiterType"] == $recruiterType->id)
												<b>{{$recruiterType->getName()}} ({{$recruiterTypeCount}})</b>
											@else
												{{$recruiterType->getName()}} ({{$recruiterTypeCount}})
											@endif
										</a>
									</li>
									@endif
								@endforeach
							</ul>
						</div>
						<div class="form-group">
							<legend>Job Types  <a href="{{$jobSearchUri($route_params,['jobType'=>0])}}" class="pull-right"><small>reset</small></a></legend>
							<ul>
								@foreach($jobTypes as $jobType)
									<?php
									$jobTypeCount = $jobSearchCount($route_params, ['jobType'=>$jobType->id]);
									?>
									@if($jobTypeCount > 0)
									<li>
										<a href="{{$jobSearchUri($route_params, ['jobType'=>$jobType->id])}}">
											@if($route_params["jobType"] == $jobType->id)
												<b>{{$jobType->getName()}} ({{$jobTypeCount}})</b>
											@else
												{{$jobType->getName()}} ({{$jobTypeCount}})
											@endif
										</a>
									</li>
									@endif
								@endforeach
							</ul>
						</div>

						<div class="form-group">
							<legend>Category  <a href="{{$jobSearchUri($route_params,['jobCategory'=>0])}}" class="pull-right"><small>reset</small></a></legend>
							<ul>
								@foreach($jobCategory as $category)
									<?php
									$jobCategoryCount = $jobSearchCount($route_params, ['jobCategory'=>$category->id]);
									?>
									@if($jobCategoryCount > 0)
									<li>
										<a href="{{$jobSearchUri($route_params, ['jobCategory'=>$category->id])}}">
											@if($route_params["jobCategory"] == $category->id)
												<b>{{$category->getName()}} ({{$jobCategoryCount}})</b>
											@else
												{{$category->getName()}} ({{$jobCategoryCount}})
											@endif
										</a>
									</li>
									@endif
								@endforeach
							</ul>
						</div>

					</form>
				</div>
				<a href="{{$jobSearchUri([])}}" class="btn btn-default btn-block">Reset All</a>
			</div>
			<div class="col-md-9 col-sm-12">
				<div class="well well-sm">
					<div class="row">
						<div class="col-md-12">
							<form name='frmSort'>
								@if($route_params["viewMode"] == "map")
									Showing {{count($results)}} job(s) on map...
								@else
									@foreach($route_params as $key=>$param)
										@if($key!=="sortBy")
											<input type='hidden' name='{{$key}}' value="{{$param}}" />
										@endif
									@endforeach
									<div class="form-group col-lg-4 col-lg-offset-6">
										<div class="input-group">
											<span class="input-group-addon">Sort By</span>	
											<select name="sortBy" onChange="document.forms['frmSort'].submit()" class="form-control">
												<option value="relevance" {{$route_params['sortBy'] == "relevance" ? "selected" : ""}}>Relevance</option>
												<option value="date" {{$route_params['sortBy'] == "date" ? "selected" : ""}}>Date</option>
												<option value="salary-low-to-high" {{$route_params['sortBy'] == "salary-low-to-high" ? "selected" : ""}}>Salary - Low to High</option>
												<option value="salary-high-to-low" {{$route_params['sortBy'] == "salary-high-to-low" ? "selected" : ""}}>Salary - High to Low</option>
												@if(trim($route_params['location']) != '')
												<option value="distance" {{$route_params['sortBy'] == "distance" ? "selected" : ""}}>Distance</option>
												@endif
											</select>
										</div>
									</div>
								@endif
								<div class="form-group col-lg-2 {{ $route_params['viewMode'] == 'map' ? 'col-lg-offset-10' : '' }}">
									<div class="btn-group btn-group-justified">
										<a href="{{$jobSearchUri($route_params, ['viewMode'=>'list'])}}" class="btn {{ $route_params['viewMode'] == 'list' ? 'btn-primary' : 'btn-default' }}"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></a>
										<a href="{{$jobSearchUri($route_params, ['viewMode'=>'map'])}}" class="btn {{ $route_params['viewMode'] == 'map' ? 'btn-primary' : 'btn-default' }}"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span></a>
									</div>
								</div>
							</form>
						</div>
					</div>

					@if($route_params["viewMode"] == "map") 
						@include('includes.frontend.job-search.map')
					@else
						@include('includes.frontend.job-search.list')
					@endif

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