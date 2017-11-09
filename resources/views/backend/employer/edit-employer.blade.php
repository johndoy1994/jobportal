@extends('layouts.backend')

@section('title', 'Edit Employer')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Edit Employer</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-employer',Request::all() )}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<style type="text/css">
				.btn-remove-dp{
					position: absolute;
				}
			</style>
			<div class="col-md-12">
				<form action="{{route('admin-edit-employer-change-dp')}}" method='POST' class="hide" id="frmProfilePicture" enctype="multipart/form-data">
					<input type="file" id="inpFilePP" name="image" class="hide" />
					{{csrf_field()}}
					<input type="hidden" value="{{$employers->user_id}}" name="userId">
				</form>
				<form class="well form-horizontal" method="post" action="{{route('admin-edit-employer-post',array_merge( ['Employer'=> $employers->id ], Request::all()) )}}">
					<legend>Edit Employer</legend>
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
						<div class="col-lg-3 text-center">
							<img  src="{{route('account-avatar-100x100',['id'=>$employers->user_id])}}" style="background: #333;" />

							<div class="text-left" style="margin-top:10px;">
								<a href="#changeProfile" class="btn btn-primary btn-sm change-profile-picture">Change Profile Picture</a>	
								@if($image_vallid)
									<a href="{{route('admin-edit-employer-delete-image',['user_id'=>$employers->user->id])}}" class="btn btn-danger btn-sm pull-right btn-remove-dp" style="margin-left:3px">Delete</a>	
								@endif
							</div>
						</div>
						<div class="col-md-9">
							<div class="form-group">
								<label class="control-label col-lg-3">Recruiter Type :</label>
								<div class="col-lg-7">
									<select class="form-control" id="recruiter_type_id" name="recruiter_type_id" required="">
										<option value="">Select Recruiter Type</option>
										@foreach($Recruitertypes as $Recruitertype)
											<option value="{{$Recruitertype->id}}" {{($employers->recruiter_type_id==$Recruitertype->id)? "selected" : ""}}>{{$Recruitertype->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Company Name :</label>
								<div class="col-lg-7">
									<input type="text" required name="company_name"  value="{{$employers->company_name}}" class="form-control"  />
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-lg-3">Name :</label>
								<div class="col-lg-7">
									<input type="text" pattern="[A-Za-z0-9 ]+"  title="Please enter valid name" required name="name" value="{{$User->name}}" class="form-control" />
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-lg-3">Phone :</label>
								<div class="col-lg-7">
									<input type="text" pattern="[0-9]+" title="Please enter valid phone number" required name="mobile_number" value="{{$User->mobile_number}}" class="form-control" />
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-lg-3">Email :</label>
								<div class="col-lg-7">
									<input type="email" pattern="[A-Za-z0-9-+.@ ]+" title="Please enter valid email" required name="email_address" value="{{$User->email_address}}" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Country Name :</label>
								<div class="col-lg-7">
									<select class="form-control" id="country_id" name="country_id" required="">
										<option value="">Select Country</option>
										@foreach($countries as $country)
											@if($userAddress)
												<option value="{{$country->id}}" {{$userAddress->getCountryId()==$country->id ? "selected" : ""}}>{{$country->name}}</option>
											@else
												<option value="{{$country->id}}">{{$country->name}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">State Name :</label>
								<div class="col-lg-7">
									<select class="form-control" id="state_id" name="state_id" required="">
										<option value="">Select State</option>
										@if($userAddress)
											<!-- <option value="{{$userAddress->getStateId()}}" selected="">{{$userAddress->getStateName()}}</option> -->
											@if($userAddress->country())
												@foreach($userAddress->country()->States as $states)
													@if($states->id != $userAddress->getStateId())
														<option value="{{$states->id}}">{{$states->name}}</option>
													@else
														<option value="{{$states->id}}" selected>{{$states->name}}</option>
													@endif
												@endforeach
											@endif
										@endif
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">City Name:</label>
								<div class="col-lg-7">
									<select class="form-control" id="city_id" name="city_id" required="">
										<option value="">Select City</option>
										@if($userAddress)
											<!-- <option value="{{$userAddress->getCityId()}}" selected="">{{$userAddress->getCityName()}}</option> -->
											@if($userAddress->state())
												@foreach($userAddress->state()->Cities as $city)
													@if($city->id != $userAddress->getCityId())
														<option value="{{$city->id}}">{{$city->name}}</option>
													@else
														<option value="{{$city->id}}" selected>{{$city->name}}</option>
													@endif
												@endforeach
											@endif	
										@endif	
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Street :</label>
								<div class="col-lg-7">
									@if($userAddress)
										<input type="text" name="street" value="{{($userAddress)?$userAddress->street:''}}" class="form-control" />
									@else
										<input type="text" name="street" value="" class="form-control" />	
									@endif
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-lg-3">Postal Code :</label>
								<div class="col-lg-7">
									@if($userAddress)
										<input type="text" pattern="[A-Za-z0-9 ]+" title="Please enter valid Postal code"  name="postal_code" value="{{($userAddress)?$userAddress->postal_code:''}}" class="form-control" />
									@else
										<input type="text" pattern="[A-Za-z0-9 ]+" title="Please enter valid Postal code"  name="postal_code" value="" class="form-control" />	
									@endif	
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Description :</label>
								<div class="col-lg-9">
									<textarea  class="form-control" title="Enter company description"  name="description" id="description">{{$employers->description}}</textarea>
								</div>
							</div>	
							{{csrf_field()}}
							<div class="form-group text-center">
								<div class="col-lg-12">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</fieldset>
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