@extends('layouts.frontend')

@section('title', 'Delete Account')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				<form class="form-horizontal" method="post" action="{{route('account-delete-post')}}">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Delete Account</h3>
					    </div>
						
					</div>
					
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="form-group">
								{{csrf_field()}}
								<button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure to delete this account ?')">Click here to delete this account</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
