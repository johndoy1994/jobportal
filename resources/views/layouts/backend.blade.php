@extends('layouts.master')

@section('master-title')
	@yield('title') - {{env('PROJECT_TITLE')}}
@endsection

@push('head')
	<link href="{{asset('/backend/bower_components/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="{{asset('/backend/bower_components/metisMenu/dist/metisMenu.min.css')}}" rel="stylesheet">
    <!-- Timeline CSS -->
    <link href="{{asset('/backend/dist/css/timeline.css')}}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{asset('/backend/dist/css/sb-admin-2.css')}}" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="{{asset('/backend/bower_components/morrisjs/morris.css')}}" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="{{asset('/backend/bower_components/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link rel='stylesheet' href='{{asset("fullcalendar-2.8.0/fullcalendar.css")}}' />

    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('backend/css/style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('backend/css/bootstrap-multiselect.css')}}" rel="stylesheet" type="text/css">
@endpush

@section('master-content')
	<div id="wrapper">
        <!-- sidebar  -->
        @section('backend-sidebar')
		  @include('includes.backend.sidebar')
        @show
        <div style="margin-top:50px">
            @yield('content')
        </div>
	</div>
@endsection

@push('footer')
	<!-- jQuery -->
    <script src="{{asset('backend/bower_components/jquery/dist/jquery.js')}}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{{asset('backend/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="{{asset('backend/bower_components/metisMenu/dist/metisMenu.min.js')}}"></script>

    <!-- Morris Charts JavaScript -->
    <script src="{{asset('backend/bower_components/raphael/raphael-min.js')}}"></script>
    <!-- <script src="{{asset('backend/bower_components/morrisjs/morris.min.js')}}"></script> -->
    <!-- <script src="{{asset('backend/js/morris-data.js')}}"></script> -->

    <!-- Custom Theme JavaScript -->
    <script src="{{asset('backend/dist/js/sb-admin-2.js')}}"></script>
    <script src="{{asset('js/select2.full.min.js')}}"></script>    
    <script src='{{asset("fullcalendar-2.8.0/lib/moment.min.js")}}'></script>
    <script src='{{asset("fullcalendar-2.8.0/fullcalendar.js")}}'></script>
    <script src="{{asset('backend/js/bootstrap-multiselect.js')}}"></script>
    <script>
    $(document).ready(function() {

        $("#recordsPerPage").change(function() {
            var target = $(this).attr('data-target');
            var value = $(this).val();
            $.ajax({
                url:  "{{route('api-public-recordPerPage')}}",
                data: {
                    target: target,
                    value: value
                },
                success: function(msg){
                   if(msg['success']){
                    document.location = "{{route(Route::getCurrentRoute()->getName(), array_merge(Request::all(), ['page'=>1]))}}";
                   }else{
                    alert('Please try again!');
                   }
                }
            });
        });

        $(".change-profile-picture").click(function() {
                var input = $("#inpFilePP");
                $(input).change(function() {
                    $("#frmProfilePicture").submit();
                });
                $(input).click();
                return false;  
            });
    });
    </script>

@endpush