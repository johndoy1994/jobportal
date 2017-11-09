@extends('layouts.backend')

@section('title', 'New City')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Add New City</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-city',Request::all() )}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-new-city-post')}}">
					<legend>New City</legend>
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
							<label class="control-label col-lg-3">County Name :</label>
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
								<input type="text" pattern="[A-Za-z0-9-+#(). ]+" title="Please enter valid city name" required name="name" value="{{old('name')}}" class="form-control" />
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
<script type="text/javascript">
	$(document).on('change', '#country_id', function() {
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
</script>
@endpush