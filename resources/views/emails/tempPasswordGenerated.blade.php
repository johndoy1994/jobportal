@extends('layouts.mail')

@section('title', 'Your temporary password is here!!')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Welcome {{$name}}<br/>
	<p>Your account successfully registered with <strong>{{env('PROJECT_TITLE')}}</strong></p>
	<p>Your temporary password is : "{{$password}}"</p>
	<p><b>Please change your password more secure one!!</b></p>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush