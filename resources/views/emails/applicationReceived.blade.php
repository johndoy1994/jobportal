@extends('layouts.mail')

@section('title', 'Job application received')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Hello {{$employerUser->getName()}}<br/>
	<p>There is application request for job <b>{{$job->getTitle()}}</b> from <b>{{$applicantUser->getName()}}</b>.</p>
	<p>Application Ref : <u>#{{$app->getApplicationRef()}}</u></p>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush