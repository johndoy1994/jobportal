<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{route('recruiter-home')}}">{{env('PROJECT_TITLE')}} - Recruiters</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				@if(\App\MyAuth::check('recruiter'))
					<li class="{{Route::getCurrentRoute()->getName() == 'recruiter-search-cv' ? 'active' : ''}}"><a href="{{route('recruiter-search-cv')}}">Search CVs</a></li>
				@endif
			</ul>
			<ul class="nav navbar-nav navbar-right">
				@if(\App\MyAuth::check('recruiter'))
					<li class="dropdown">
                        <a href="{{route('recruiter-message')}}">
                            Chat @if(isset($messageCount) && $messageCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$messageCount}}</span> @endif
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="{{route('recruiter-account-notifications')}}">
                            Notifications @if(isset($NotificastionCount) && $NotificastionCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$NotificastionCount}}</span> @endif
                        </a>
                    </li>
					<li class="{{Route::getCurrentRoute()->getName() == 'recruiter-home' ? 'active' : ''}}"><a href="{{route('recruiter-account-home')}}">My Account</a></li>
					<li><a href="{{route('recruiter-account-signout')}}">Sign Out</a></li>
				@else
					<li><a href="{{route('recruiter-account-signin')}}">Sign In</a></li>
					<li><a href="{{route('recruiter-account-register')}}">Register</a></li>
				@endif
			</ul>
		</div>
	</div>
</nav>