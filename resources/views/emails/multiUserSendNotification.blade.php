@extends('layouts.mail')

@section('title', 'Recruiter send mail!!')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Hello, <br/>
	<p>{{$messages}}</p>
	
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush