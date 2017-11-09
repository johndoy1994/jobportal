@extends('layouts.mail')

@section('title', 'Account successfully registered')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Welcome {{$name}}<br/>
	<p>Your account successfully registered with <strong>{{env('PROJECT_TITLE')}}</strong></p>
	@if($through)
		<p>In short time you will receive your temporary password!!</p>
	@else
		<p>Before stepping ahead please verify your account.</p>
		<p>You can login to your account to verify.</p>
	@endif
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush