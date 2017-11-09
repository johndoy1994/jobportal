@extends('layouts.backend')

@section('title', 'New Employer')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Add New Employer</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-employer',Request::all() )}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-new-employer-post')}}">
					<legend>New Employer</legend>
					@if(session('success_message'))
						<div class="alert alert-success">
							{{session('success_message')}}
						</div>
					@endif

					@if(session('error_message'))
						<div class="alert alert-danger">
							{{session('error_message')}}
						</div>
					@endif

					@if(count($errors)>0)
						<div class="alert alert-warning">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</div>
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Recruiter Type :</label>
							<div class="col-lg-5">
								<select class="form-control" id="recruiter_type_id" name="recruiter_type_id" required="">
									<option value="">Select Recruiter Type</option>
									@foreach($Recruitertypes as $Recruitertype)
										<option value="{{$Recruitertype->id}}" {{(old('recruiter_type_id')==$Recruitertype->id)?"selected":""}}>{{$Recruitertype->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Company Name :</label>
							<div class="col-lg-5">
								<input type="text" required name="company_name"  value="{{old('company_name')}}" class="form-control"  />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Name :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[A-Za-z0-9 ]+"  title="Please enter valid name" required name="name" value="{{old('name')}}" class="form-control" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Phone :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[0-9]+" title="Please enter valid phone number" required name="mobile_number" value="{{old('mobile_number')}}" class="form-control" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Email :</label>
							<div class="col-lg-5">
								<input type="email" pattern="[A-Za-z0-9-+.@ ]+" title="Please enter valid email" required name="email_address" value="{{old('email_address')}}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Password :</label>
							<div class="col-lg-5">
								<input type="password" class="form-control" required name="password" value="{{old('password')}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">Confirm Password :</label>
							<div class="col-lg-5">
								<input type="password" class="form-control" required name="password_confirmation" value="{{old('password_confirmation')}}" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Country Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="country_id" name="country_id" required="">
									<option value="">Select Country</option>
									@foreach($countries as $country)
										<option value="{{$country->id}}" {{(old('country_id')==$country->id)?"selected":""}}>{{$country->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">State Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="state_id" name="state_id" required="">
									<option value="">Select State</option>
									@if($oldstateTitle)
										<option value="{{$oldstateTitle[0]}}" selected>{{$oldstateTitle[1]}}</option>
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">City Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="city_id" name="city_id" required="">
									<option value="">Select City</option>
									@if($oldCityTitle)
										<option value="{{$oldCityTitle[0]}}" selected>{{$oldCityTitle[1]}}</option>
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Street :</label>
							<div class="col-lg-5">
								<input type="text" name="street" value="{{old('street')}}" class="form-control" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Postal Code :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[A-Za-z0-9 ]+" title="Please enter valid Postal code"  name="postal_code" value="{{old('postal_code')}}" class="form-control" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Description :</label>
							<div class="col-lg-8">
								<textarea  class="form-control" title="Enter company description"  name="description" id="description">{{old('description')}}</textarea>
							</div>
						</div>	
					</fieldset>
					{{csrf_field()}}
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-primary">Submit</button>
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

	$(document).ready(function(){
		CKEDITOR.replace('description');
	});

	$(document).on('change', '#country_id', function() {
		$('#city_id').html('<option value="">Select City</option>');
			if($(this).val()==""){
				var id =0;
			}else{
				var id =$(this).val();
			}
			$.ajax({
                dataType:'json',
                url:  "{{route('api-public-states')}}",
                data: {'countryId' : id},
                success: function(data){
                    $('#state_id').html('<option value="">Select state</option>');
                    for(var i=0;i<data.length;i++){
                        $('#state_id').append('<option value="'+data[i].id+'"> '+data[i].name+'</option>');
                    }
                }
            });
        });
	$(document).on('change', '#state_id', function() {
			if($(this).val()==""){
				var id =0;
			}else{
				var id =$(this).val();
			}
			$.ajax({
                dataType:'json',
                url:  "{{route('api-public-cities')}}",
                data: {'stateId' : id},
                success: function(data){
                    $('#city_id').html('<option value="">Select City</option>');
                    for(var i=0;i<data.length;i++){
                        $('#city_id').append('<option value="'+data[i].id+'"> '+data[i].name+'</option>');
                    }
                }
            });
        });
</script>
@endpush