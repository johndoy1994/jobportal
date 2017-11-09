@extends('layouts.backend')

@section('title', 'Import Export')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-9">
				<h3>Import Export Listing</h3>
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
	    </div>
	    
	    	<div class="col-md-12">

				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody>
						@foreach($ieModules as $module_name => $module_label)
							<tr>
								<td>{{$module_label}}</td>
								<td class="col_width30">
									<a class="btn btn-success btn-xs" href="{{route('import-'.$module_name)}}">Import</a>
									<a class="btn btn-danger btn-xs" href="{{route('export-'.$module_name)}}">Export</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
	</div>
@endsection