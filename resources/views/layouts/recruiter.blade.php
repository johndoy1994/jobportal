@extends('layouts.master')

@section('master-title')
	@yield('title') - {{env('PROJECT_TITLE')}}
@endsection

@push('head')
	<!-- Bootstrap -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="{{asset('html5shiv.min.js')}}"></script>
      <script src="{{asset('respond.min.js')}}"></script>
    <![endif]-->
    
    <!-- for social media share -->
    <link rel="stylesheet" href="{{asset('/css/font-awesome-4.6.3/css/font-awesome.min.css')}}">
    <!-- for social media share -->

    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/jquery-ui.min.css')}}" rel="stylesheet">
    <link rel='stylesheet' href='{{asset("fullcalendar-2.8.0/fullcalendar.css")}}' />
    <link rel='stylesheet' href='{{asset("recruiter/css/style.css")}}' />

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/jquery-ui.js')}}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
@endpush

@section('master-content')
	<div id="page">
		<header>
			@include('includes.recruiter.navbar')			
		</header>
		@yield('content')

        <footer>
            @include('includes.frontend.footer')            
        </footer>
	</div>
@endsection

@push('footer')
    <script src='{{asset("fullcalendar-2.8.0/lib/moment.min.js")}}'></script>
    <script src='{{asset("fullcalendar-2.8.0/fullcalendar.js")}}'></script>
    <script src="{{asset('js/select2.full.min.js')}}"></script>
    <script type="text/javascript">

        // Share window popup
            var popupSize = {
                width: 780,
                height: 550
            };
            $(document).on('click', '.social-buttons > a', function(e){

                var
                    verticalPos = Math.floor(($(window).width() - popupSize.width) / 2),
                    horisontalPos = Math.floor(($(window).height() - popupSize.height) / 2);

                var popup = window.open($(this).prop('href'), 'social',
                    'width='+popupSize.width+',height='+popupSize.height+
                    ',left='+verticalPos+',top='+horisontalPos+
                    ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

                if (popup) {
                    popup.focus();
                    e.preventDefault();
                }
            });
        // Share window popup End
        
        $(document).ready(function() {
            $(".change-profile-picture").click(function() {
                var input = $("#inpFilePP");
                $(input).change(function() {
                    $("#frmProfilePicture").submit();
                });
                $(input).click();
                return false;  
            });

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
        });
        </script>
@endpush