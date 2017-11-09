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
    <link rel='stylesheet' href='{{asset("css/style.css")}}' />

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/jquery-ui.js')}}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
@endpush

@section('master-content')
	<div id="page">
		<header>
			@include('includes.frontend.navbar')			
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
            $("a[role='ask-days']").click(function() {
                var $jobId = $(this).attr('data-target');

                $("#mainModal").modal('show');
                $("#mainModal").find(".modal-content.loader").removeClass("hide");
                $("#mainModal").find(".modal-content.calendar").addClass("hide");

                $.ajax({
                    url : "{{route('api-public-jobdays')}}",
                    type: "POST",
                    data: { job: $jobId, _token: "{{csrf_token()}}" },
                    dataType: "json",
                    success: function(json) {
                        if(json.success) {                            
                            if(json.days && json.days.length<=0) {
                                $("#mainModal").modal('hide');
                                alert(json.message);
                                return;
                            }

                            $("#mainModal").find(".modal-content.loader").addClass("hide");
                            $("#mainModal").find(".modal-content.calendar").removeClass("hide");

                            $("#mainModal").find("form").attr("action", json.apply_uri);

                            var calendar = ".modal-content.calendar #calendar";
                            $("#mainModal").find(".modal-content.calendar .modal-body #calendar").remove();
                            $("#mainModal").find(".modal-content.calendar .modal-body").append("<div id='calendar'></div>");

                            $(calendar).fullCalendar({
                                defaultDate: json.defaultDate,
                                dayRender: function(date, cell) {
                                    var dateFormat = date.format('Y-MM-DD');
                                    $isThere = $.inArray(dateFormat, json.days);
                                    if($isThere>-1) {
                                        cell.css('backgroundColor','#ff851b!important');
                                        cell.css('cursor','pointer');
                                    }
                                    cell.css('border','1px solid #DDD');

                                    $("#mainModal").find("form select option").each(function(i,e) {
                                        if($(e).val() == dateFormat) {
                                            cell.css('backgroundColor','#158cba!important');
                                            cell.addClass('date-selected');
                                        }
                                    });

                                },
                                dayClick: function(date) {
                                    var dateFormat = date.format('Y-MM-DD');
                                    $isThere = $.inArray(dateFormat, json.days);
                                    if($isThere>-1) {
                                        if($(this).is(".date-selected")) { // remove date
                                            $(this).css('backgroundColor','#ff851b!important');
                                            //$(this).css('backgroundColor','#158cba!important');
                                            $(this).removeClass('date-selected');
                                            $("#mainModal form select option[value='"+dateFormat+"']").remove();
                                        } else { // add date
                                            $(this).css('backgroundColor','#158cba!important');
                                            $(this).addClass('date-selected');
                                            //$(this).css('backgroundColor','#ff851b!important');
                                            $("#mainModal").find("form select").append(new Option(dateFormat,dateFormat));
                                            $("#mainModal").find("form select option[value='"+dateFormat+"']").attr('selected','selected');
                                        }
                                    }
                                }
                            });
                        } else {
                            alert(json.message);
                            $("#mainModal").modal("hide");
                        }
                    },
                    error: function() {
                        $("#mainModal").find(".modal-content.loader .modal-body p").html("<p>There was an error while fetching job calendar, please try again.");
                    }
                });

                return false;
            });

            $("form[role='create-alert']").submit(function() {
                var job_categories_id = $(this).find("input[name='job_categories_id']").val();
                var job_title_id = $(this).find("input[name='job_title_id']").val();
                var keywords = $(this).find("input[name='keywords']").val();
                var city = $(this).find("input[name='city']").val();
                var radius = $(this).find("input[name='radius']").val();
                var email_address = $(this).find("input[name='email_address']").val();

                var t = $(this);

                $(t).find(".loader").removeClass('hide');
                $(t).find(".content").addClass('hide');

                $.ajax({
                    url : "{{route('api-public-createalert')}}",
                    dataType: "json",
                    data : {
                        job_categories_id: job_categories_id,
                        job_title_id: job_title_id,
                        keywords: keywords,
                        city: city,
                        radius: radius,
                        email_address : email_address
                    },
                    success: function(json) {
                        if(json.success) { } else {
                            alert(json.message);
                        }
                        $(t).find('.loader').html(json.message);
                    },
                    error: function(er) {
                        var json =  jQuery.parseJSON(er.responseText);
                        if(json && json.email_address) {
                            alert(json.email_address);
                        } else {
                            alert("Error while creating alert, try again.");
                        }
                        $(t).find(".content").removeClass("hide");
                        $(t).find(".loader").addClass("hide");
                    }
                });

                return false;
            });

            $(".change-profile-picture").click(function() {
                var input = $("#inpFilePP");
                $(input).change(function() {
                    $("#frmProfilePicture").submit();
                });
                $(input).click();
                return false;  
            });

            $.role_saveJobClick = function() {
                var t = $(this);
                var href = $(this).attr('href');
                var action = $(this).attr('data-action');
                if(href && action) {
                    $(t).addClass('disabled');
                    var data = {};
                    if(action === "remove") {
                        data = {remove:'yes'};
                    }
                    $.ajax({
                        url : href,
                        data: data,
                        dataType: "json",
                        success: function(json) {
                            var newT = "a[href='"+href+"']";
                            if(action==="remove") {
                                $(newT).attr('data-action', 'save');
                                $(newT).removeClass('btn-primary');
                                $(newT).addClass('btn-default');
                                $(newT).html("Save");
                            } else {
                                $(newT).attr('data-action', 'remove');
                                $(newT).removeClass('btn-default');
                                $(newT).addClass('btn-primary');
                                $(newT).html("Saved");
                            }

                            if($(t).attr('data-refresh')) {
                                $(t).parent().parent().parent().parent().parent().remove();
                                document.location.reload();
                            }
                        },
                        complete: function() {
                            $(t).removeClass('disabled');
                        }
                    });
                }
                return false;
            };

            $("a[role='save-job']").click($.role_saveJobClick);
            @if(!Request::get('days'))
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
            @endif
        });
    </script>
@endpush