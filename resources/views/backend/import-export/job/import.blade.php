@extends('layouts.backend')

@section('title', 'Import Job')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Import Job</h3>
			</div>
			<div class="col-md-3 text-right padding-top-10">
				<a href="{{route('import-export-list')}}" class="btn btn-primary pull-right" style="margin-left:10px;">Back</a>
				<a href="{{route('import-job-sample')}}" class="btn btn-primary pull-right">Sample CSV</a>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('import-job-post')}}" enctype="multipart/form-data">
					<legend>Import Job</legend>
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
					@if(session('successCount'))
						<div class="alert alert-success">
							<p>{{session('successCount')}} Records successfully Added </p>
						</div>
					@endif

					@if(session('failCount'))
						<div class="alert alert-danger">
							<p>{{session('failCount')}} Record fails to add.</p>
						</div>
					@endif

					@if(session('skipedRows'))
						@if(count(session('skipedRows'))>0)
							<div class="alert alert-warning">
									@foreach(session('skipedRows') as $key => $skiprow)
										<li>
											@foreach($skiprow as $val)
												@foreach($val as $rec)
													{{$rec}},
												@endforeach	
											@endforeach

											@if(session("msg")[$key])
												<div class="alert alert-danger">
													@foreach(session("msg")[$key] as $val)
														<p>{{$val}}</p>
													@endforeach
												</div>
											@endif
										</li>
									@endforeach
							</div>
						@endif
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Import file :</label>
							<div class="col-lg-5">
								<input type="file" id="filename" name="filename" required="" />	
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
