@extends('layouts.mail')

@section('title', 'Your Account is activated!!')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Welcome {{$name}}<br/>
	<p>Your account successfully Activated with <strong>{{env('PROJECT_TITLE')}}</strong></p>
	<p>Your email is : "{{$email}}"</p>
	<p>Link to login your account : <br/> <a href="{{$link}}">{{$link}}</a> </p>
	<p><b> For security reasons first please change your password</b></p>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush