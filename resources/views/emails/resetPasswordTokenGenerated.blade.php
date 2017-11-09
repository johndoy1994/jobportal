@extends('layouts.mail')

@section('title', 'Reset Password Request')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Hello {{$user->getName()}}<br/>
	<p>We've request from you to generate reset password page for you. So simply follow the link to reset your password.</p>
	<p>If you do not wanted to reset your password please ignore this. (May be someone trying to reset your password)</p>
	<p>Link to reset password : <br/> <a href="{{$link}}">{{$link}}</a> </p>
	<br/>
	<p>You can even copy and paste this code into reset password form to get your new password : </p>
	<blockquote>{{$token}}</blockquote>
	<p>Note: This link and code will expire after 1 hour.</p>
	<br/>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush