@extends('layouts.frontend')

@section('title', $alert ? 'Edit Alert' : 'Create Alert')

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
						<h3 class="panel-title">{{ $alert ? "Edit" : "Create" }} Alert</h3>
				    </div>
					<div class="panel-body">
						<form method="post" action="{{route('account-settings-save-jobalert')}}">
							{{csrf_field()}}
							@if($alert)
								<input type="hidden" name="alert_id" value="{{$alert->id}}" />
							@endif
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Job Category :</label>
									<select name="job_categories_id" class="form-control">
										<option value=0>Any</option>
										@foreach($jobCategories as $jobCategory)
											@if(($alert && $alert->job_categories_id == $jobCategory->id) || (old("job_categories_id") == $jobCategory->id))
												<option value="{{$jobCategory->id}}" selected>{{$jobCategory->getName()}}</option>
											@else
												<option value="{{$jobCategory->id}}">{{$jobCategory->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Job Title :</label>
									<select name="job_title_id" class="form-control">
										<option value=0>Any</option>
										@if($alert && $alert->jobTitle)
											<option value="{{$alert->jobTitle->id}}" selected>{{$alert->jobTitle->getTitle()}}</option>
										@elseif(old('job_title_id')!==false)
											{{old('job_title_id')}}
											@if($jobTitle = \App\Repos\API\PublicRepo::getJobTitle(old('job_title_id')))
												<option value="{{$jobTitle->id}}" selected>{{$jobTitle->getTitle()}}</option>
											@endif
										@endif
									</select>
									<br/><br/>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Keywords :</label>
									<input type="text" name="keywords" placeholder="Keywords comma separated" class="form-control" value="{{ old("keywords") ? old("keywords") : ( $alert ? $alert->keywords : "" )  }}" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Location :</label>
									<select name="city_id" class="form-control" id="selLocations">
										<option value="0" selected>Any</option>
										@if($alert && $alert->city)
											<option value="{{$alert->city->id}}" selected>{{$alert->city->fetchFullAddress()}}</option>
										@endif
									</select>
									<br/><br/>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Radius :</label>
									<select name="radius" class="form-control">
										<option value=0>Any</option>
										@foreach($searchMiles as $searchMile)
											@if(($alert && $alert->radius == $searchMile->mile) || (old("radius") == $searchMile->mile))
												<option value="{{$searchMile->mile}}" selected>{{$searchMile->mile}} miles</option>
											@else
												<option value="{{$searchMile->mile}}">{{$searchMile->mile}} miles</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Salary :</label>
									<select name="salary_range_from" class="form-control">
										<option value=0>Any</option>
										@foreach($salaryTypes as $salaryType)
											<optgroup label="{{$salaryType->getTypeName()}}">
												@foreach($salaryType->salaryRange as $salaryRange)
													@if($alert && $alert->salary_type_id == $salaryType->id && $salaryRange->range_from == $alert->salary_range_from)
														<option value="{{$salaryRange->id}}" selected>at least {{$salaryRange->range_from}}</option>s
													@else
														<option value="{{$salaryRange->id}}">at least {{$salaryRange->range_from}}</option>
													@endif
												@endforeach
											</optgroup>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Job Type :</label>
									<select name="job_type_id" class="form-control">
										<option value=0>Any</option>
										@foreach($jobTypes as $jobType)
											@if(($alert && $alert->job_type_id == $jobType->id) || (old("job_type_id") == $jobType->id))
												<option value="{{$jobType->id}}" selected>{{$jobType->getName()}}</option>
											@else
												<option value="{{$jobType->id}}">{{$jobType->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Work Area :</label>
									<select name="industries_id" class="form-control">
										<option value=0>Any</option>
										@foreach($industries as $industry)
											@if(($alert && $alert->industries_id == $industry->id) || (old("industries_id") == $industry->id))
												<option value="{{$industry->id}}" selected>{{$industry->getName()}}</option>
											@else
												<option value="{{$industry->id}}">{{$industry->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<br/>
									<button type="submit" class="btn btn-primary">
									@if($alert)
										Save
									@else
										Create
									@endif
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection

@push('footer')
<script type="text/javascript">
$(document).ready(function() {

	$("select[name='job_categories_id']").change(function() {
		$("select[name='job_title_id']").empty();
	});

	$("select[name='job_title_id']").select2({
		placeholder : "- Job Title - ",
		minimumResultsForSearch: Infinity,
		ajax: {
			url: "{{route('api-public-jobtitles')}}",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		    	var id = $("select[name='job_categories_id']").val();
		        return {
		    		jobCategoryId: id
		    	};
		    },
		    processResults: function (data, params) {
		    	data.push({ title: "Any", id:"0" });
		    	return {
	                results: $.map(data, function (item) {
	                    return {
	                        text: item.title,
	                        id: item.id
	                    }
	                })
	            };
		    },
		    cache: true
		}
	});

	$("#selLocations").select2({
		placeholder : "- Location - ",
		minimumInputLength: 3,
		ajax: {
			url: "{{route('api-public-locations')}}",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		    	var q = params.term;
		        return {
		    		q: q,
		    		limit : 10
		    	};
		    },
		    processResults: function (data, params) {
		    	return {
	                results: $.map(data.data, function (item) {
	                    return {
	                        text: item.name+", "+item.state_name+", "+item.country_name,
	                        id: item.id
	                    }
	                })
	            };
		    },
		    cache: true
		}
	});
});
</script>
@endpush
