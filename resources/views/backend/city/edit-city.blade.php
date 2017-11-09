@extends('layouts.backend')

@section('title', 'Edit City')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Edit City</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-city',Request::all() )}}" class="btn btn-primary pull-right">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-edit-city-post', array_merge( ['City'=> $Cities->id ], Request::all()) )}}">
					<legend>Edit City</legend>
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
										@if($Cities->State && $Cities->State->country)
											<option {{($Cities->State->country->id==$country->id)? "selected" : ""}} value="{{$country->id}}">{{$country->name}}</option>
										@else
											<option  value="{{$country->id}}">{{$country->name}}</option>	
										@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">State Name :</label>
							<div class="col-lg-5">
								<select class="form-control" id="state_id" name="state_id" required="">
									<option value="">Select State</option>
									@if($Cities->State)
									<!-- <option value="{{$Cities->State->id}}" selected>{{$Cities->State->name}}</option> -->
										@if($Cities->State->Country)
											@foreach($Cities->State->Country->States as $states)
												@if($states->id != $Cities->State->id)
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
							<label class="control-label col-lg-3">City Name :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[A-Za-z0-9-+#(). ]+" title="Please enter valid City name" required name="name" class="form-control" value="{{$Cities->name}}" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Status :</label>
							<div class="col-lg-9">
								<input type="radio" name="status" value="0" <?php echo ($Cities->status=="0") ? "checked" : ""; ?>> Active
								<input type="radio" name="status" value="1" <?php echo ($Cities->status=="1") ? "checked" : ""; ?>> Inactive
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