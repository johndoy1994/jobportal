@extends('layouts.recruiter')

@section('title', 'Company Profile')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.recruiter.validation_errors')
				@include('includes.recruiter.request_messages')

				<form class="form-horizontal" method="post" id="frmProfile" action="{{route('recruiter-company-profile-post')}}">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Employer Details</h3>
					    </div>
						<div class="panel-body">
							
							<div class="form-group @if($errors->has('first_name')) has-error @endif">
								<label class="control-label col-lg-3">First Name *</label>
								<div class="col-lg-4">
									<input required title="First name is must!!" type="text" class="form-control" name="first_name" placeholder="Your first name..." value="{{ $values['first_name'] }}" required />
								</div>
							</div>
							<div class="form-group @if($errors->has('surname')) has-error @endif">
								<label class="control-label col-lg-3">Surname *</label>
								<div class="col-lg-4">
									<input required title="Surname is must!!" type="text" class="form-control" name="surname" placeholder="Your surname..." value="{{ $values['surname'] }}" required />
								</div>
							</div>
								
							<div class="form-group @if($errors->has('email_address')) has-error @endif">
								<label class="control-label col-lg-3">Email *</label>
								<div class="col-lg-4">
									<input required title="Email address is must!!" type="text" class="form-control" name="email_address" placeholder="Your e-mail address..." value="{{ $values['email_address'] }}" required="" />
								</div>
							</div>		

							<div class="form-group @if($errors->has('mobile_number')) has-error @endif">
								<label class="control-label col-lg-3">Mobile Number</label>
								<div class="col-lg-4">
									<input type="text" title="Mobile number is must!!" class="form-control" name="mobile_number" placeholder="Your mobile number..." value="{{ $values['mobile_number'] }}" />
								</div>
							</div>

						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Company details</h3>
					    </div>
						<div class="panel-body">
							<div class="form-group @if($errors->has('recruiter_type_id')) has-error @endif">
								<label class="control-label col-lg-3">Recruiter Type *</label>
								<div class="col-lg-4">
									<select name="recruiter_type_id" class="form-control">
										<option value=0>- Select Recruiter Type -</option>
										@foreach($recruiterTypes as $recruiterType)
											@if($values["recruiter_type_id"] == $recruiterType->id)
												<option value="{{$recruiterType->id}}" selected>{{$recruiterType->name}}</option>
											@else
												<option value="{{$recruiterType->id}}">{{$recruiterType->name}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							
							<div class="form-group @if($errors->has('company_name')) has-error @endif">
								<label class="control-label col-lg-3">Company Name *</label>
								<div class="col-lg-4">
									<input required title="Company name is must!!" type="text" class="form-control" name="company_name" placeholder="Your Company name..." value="{{ $values['company_name'] }}" required />
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">Company Description</label>
								<div class="col-lg-8">
									<textarea class="form-control" placeholder="Your Company description..." name="cmp_description">{{$values['cmp_description']}}</textarea>
								</div>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Address</h3>
					    </div>
						<div class="panel-body">
							<div class="form-group @if($errors->has('country')) has-error @endif">
								<label class="control-label col-lg-3">Country *</label>
								<div class="col-lg-4">
									<select name="country" class="form-control">
										<option value=0>- Select Country -</option>
										@foreach($countries as $country)
											@if($values["country_id"] == $country->id)
												<option value="{{$country->id}}" selected>{{$country->getName()}}</option>
											@else
												<option value="{{$country->id}}">{{$country->getName()}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('state')) has-error @endif">
								<label class="control-label col-lg-3">Province/State *</label>
								<div class="col-lg-4">
									<select name="state" class="form-control">
										<option value=0>- Select State -</option>
										@if($values["state"])
											<option value="{{$values['state'][0]}}" selected>{{$values['state'][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('city')) has-error @endif">
								<label class="control-label col-lg-3">City *</label>
								<div class="col-lg-4">
									<select name="city" class="form-control">
										<option value=0>- Select City -</option>
										@if($values["city"])
											<option value="{{$values['city'][0]}}" selected>{{$values['city'][1]}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group @if($errors->has('street')) has-error @endif">
								<label class="control-label col-lg-3">Street</label>
								<div class="col-lg-4">
									<input type="text" class="form-control" name="street" value="{{$values['street']}}" />
								</div>
							</div>
							<div class="form-group @if($errors->has('postalcode')) has-error @endif">
								<label class="control-label col-lg-3">Postal Code </label>
								<div class="col-lg-4">
									<input type="text" class="form-control" name="postalcode" value="{{$values['postalcode']}}" />
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="form-group">
								{{csrf_field()}}
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('footer')
<script src="{{asset('backend/js/ckeditor/ckeditor.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function() {

		CKEDITOR.replace('cmp_description');

		$("#frmProfile").submit(function() {

			var goSubmit = true;

			if($("select[name='recruiter_type_id']").val() <= 0) {
				$("select[name='recruiter_type_id']").parent().parent().addClass('has-error');
				$("select[name='recruiter_type_id']").focus();
				goSubmit=false;
			}

			if($("select[name='country']").val() <= 0) {
				$("select[name='country']").parent().parent().addClass('has-error');
				$("select[name='country']").focus();
				goSubmit=false;
			}

			if($("select[name='state']").val() <= 0) {
				$("select[name='state']").parent().parent().addClass('has-error');
				$("select[name='state']").focus();
				goSubmit=false;
			}

			if($("select[name='city']").val() <= 0) {
				$("select[name='city']").parent().parent().addClass('has-error');
				$("select[name='city']").focus();
				goSubmit=false;
			}

			return goSubmit;

		});

		$("select").select2();

		$("select[name='country']").change(function() {
			$("select[name='state']").empty();
			$("select[name='city']").empty();
		});

		$("select[name='state']").change(function() {
			$("select[name='city']").empty();
		});

		$("select[name='state']").select2({
			placeholder : "- Select State - ",
			
			ajax: {
				url: "{{route('api-public-states')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		countryId: $("select[name='country']").val(),
			    		q: params.term,
			    		limit : 10
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data.data, function (item) {
		                    return {
		                        text: item.name,
		                        id: item.id
		                    }
		                })
		            };
			    },
			    cache: true
			}
		});


		$("select[name='city']").select2({
			placeholder : "- Select City - ",
			
			ajax: {
				url: "{{route('api-public-cities')}}",
			    dataType: 'json',
			    delay: 250,
			    data: function (params) {
			        return {
			    		stateId: $("select[name='state']").val(),
			    		q: params.term,
			    		limit : 10
			    	};
			    },
			    processResults: function (data, params) {
			    	return {
		                results: $.map(data.data, function (item) {
		                    return {
		                        text: item.name,
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