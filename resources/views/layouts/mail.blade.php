<html>
<head>
	<title>@yield('title')</title>
</head>
<body>
	@stack('header')
	<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('/css/font-awesome-4.6.3/css/font-awesome.min.css')}}">
    <link href="{{asset('css/jquery-ui.min.css')}}" rel="stylesheet">
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/jquery-ui.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
	<br/>
	@yield('content')
	<br/>
	@stack('footer')
</body>
</html>
