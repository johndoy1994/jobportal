@if(session('error_message'))
	@if(is_array(session('error_message')))
		<div class="alert alert-danger">
			@foreach(session('error_message') as $message)
				<li>{{$message}}</li>
			@endforeach
		</div>
	@else
		<div class="alert alert-danger">
			{{session('error_message')}}
		</div>
	@endif
@endif
@if(session('success_message'))
	@if(is_array(session('success_message')))
		<div class="alert alert-success">
			@foreach(session('success_message') as $message)
				<li>{{$message}}</li>
			@endforeach
		</div>
	@else
		<div class="alert alert-success">
			{{session('success_message')}}
		</div>
	@endif
@endif