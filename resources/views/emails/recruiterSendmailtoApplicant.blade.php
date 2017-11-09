@extends('layouts.mail')

@section('title', 'Recruiter send mail!!')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Welcome {{$name}}<br/>
	<p>{{$content}}</p>
	
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush