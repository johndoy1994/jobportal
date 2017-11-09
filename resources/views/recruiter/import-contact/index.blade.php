@extends('layouts.recruiter')

@section('title', 'Import Contacts')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-12">
				<a href="{{route('api-through', ['provider'=>'google', 'action'=>'contacts', 'redirect'=>'recruiter-gmail-contacts'])}}" class="btn btn-primary">Import from G-Mail</a>
			</div>
		</div>
	</div>
@endsection

@push('footer')
@endpush
