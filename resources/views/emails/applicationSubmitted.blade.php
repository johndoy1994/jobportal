@extends('layouts.mail')

@section('title', 'Job application submitted')

@push('header')
	{!!$header!!}
@endpush

@section('content')
	Hello {{$applicantUser->getName()}}<br/>
	<p>Your application request for job <b>"{{$job->getTitle()}}"</b> has been successfully sent to <b>"{{$employer->company_name}}"</b>.</p>
	<p>Application Ref : <u>#{{$app->getApplicationRef()}}</u></p>
	<p>Thank you.</p>
@endsection

@push('footer')
	{!!$footer!!}
@endpush