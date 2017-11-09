@extends('layouts.mail')

@section('title', 'Account successfully registered')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Welcome <br/>
	<p>{{$messages}}</p>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush