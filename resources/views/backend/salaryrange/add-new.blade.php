@extends('layouts.backend')

@section('title', 'Add New - Salary Range')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Add New Salary Range</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('admin-salary-range',Request::all())}}" class="btn btn-primary">Back</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12"> <!-- Display Session Message  -->
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
            </div>
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-salary-range-add-new-post')}}">
					<legend>New Salary Range</legend>
					@if(count($errors)>0)
						<div class="alert alert-warning">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</div>
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Salary Type :</label>
							<div class="col-lg-5">
								{{Form::select('salary_type_id',$SalaryTypes,old('salary_type_id'),['placeholder'=>'Select Type','class'=>'form-control','required'=>''])}}
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Range From :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[0-9]+[.]*[0-9]+" title="Please enter valid salary range from!" required value="{{old('range_from')}}" name="range_from" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Range To :</label>
							<div class="col-lg-5">
								<input type="text" pattern="[0-9]+[.]*[0-9]+" title="Please enter valid salary range to!" required value="{{old('range_to')}}" name="range_to" class="form-control" />
							</div>
						</div>
					</fieldset>

					{{csrf_field()}} <!-- to enable form post data-->

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