@extends('layouts.mail')

@section('title', 'Please verify your account!!')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Welcome {{$name}}<br/>
	<p>Your account successfully registered with <strong>{{env('PROJECT_TITLE')}}</strong></p>
	<p>Before stepping ahead please verify your account.</p>
	<p>You can login to your account to verify.</p>
	<p>You will need this code for verification : "{{$token}}"</p>
	<br/>
	<blockquote>{{$token}}</blockquote>
	<br/>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush