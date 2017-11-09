<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{route('front-home')}}">{{env('PROJECT_TITLE')}}</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="{{ Route::getCurrentRoute()->getName() == 'job-search' ? 'active' : '' }}"><a href="{{route('job-search',array_merge( ['keywords'=>'','location'=>'','radius'=>'0']))}}">Job List</a></li>
				<li class="{{ Route::getCurrentRoute()->getName() == 'saved-jobs' ? 'active' : '' }}"><a href="{{route('saved-jobs')}}">Saved Jobs</a></li>
				@if(\App\MyAuth::check())
					<li class="{{ Route::getCurrentRoute()->getName() == 'frontend-subscribedjobs' ? 'active' : '' }}"><a href="{{route('frontend-subscribedjobs')}}">Subscribed Jobs</a></li>
				@endif
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="{{ Route::getCurrentRoute()->getName() == 'front-home' ? 'active' : '' }}"><a href="{{route('front-home')}}">Search</a></li>
				@if(\App\MyAuth::check())
					<li class="dropdown">
                        <a href="{{route('frontend-message')}}">
                            Chat @if(isset($messageCount) && $messageCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$messageCount}}</span> @endif
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="{{route('account-notification')}}">
                            Notifications @if(isset($NotificastionCount) && $NotificastionCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$NotificastionCount}}</span> @endif	
                        </a>
                    </li>
                <!-- /.dropdown -->
					<li class="{{Route::getCurrentRoute()->getName() == 'account-home' ? 'active' : ''}}"><a href="{{route('account-home')}}">My Account</a></li>
					<li><a href="{{route('account-signout')}}">Sign Out</a></li>
				@else
					<li><a href="{{route('account-signin')}}">Sign In</a></li>
					<li><a href="{{route('account-register')}}">Register</a></li>
				@endif
			</ul>
		</div>
	</div>
</nav>