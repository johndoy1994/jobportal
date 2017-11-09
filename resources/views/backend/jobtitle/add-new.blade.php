@extends('layouts.backend')

@section('title', 'Add New - Job Title')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Add New Job Title</h3>
			</div>
			<div class="col-md-3 text-right">
				<a href="{{route('admin-job-title',Request::all())}}" class="btn btn-primary margin-top-10">Back</a>
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
				<form class="well form-horizontal" method="post" action="{{route('admin-job-title-add-new-post')}}">
					<legend>New Job Title</legend>
					@if(count($errors)>0)
						<div class="alert alert-warning">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</div>
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Category :</label>
							<div class="col-lg-5">
								{{Form::select('name',$JobCategory,old('name'),['placeholder'=>'Select Category','class'=>'form-control','required'=>''])}}
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Job Title :</label>
							<div class="col-lg-5">
								{{Form::text('title','',
									array('
										placeholder'=>'Enter Job Title',
										'class'=>'form-control',
										'pattern' => '[A-Za-z0-9-+ ]{2,}',
										'title' => 'Please enter valid job title',
										'required' => 'required'
									)
								)}}
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