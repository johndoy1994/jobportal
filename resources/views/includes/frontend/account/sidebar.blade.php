<ul class="list-group">
	<li class="list-group-item text-center">
		<img src="{{route('account-avatar-100x100',['id'=>$user->id])}}" style="background: #333;" />
		<h3 id="account-name">{{$user->getName()}}</h3>
		<div class="">
			<a class="btn btn-default btn-xs" href="{{route('account-myprofile')}}">My Profile</a>
			<a class="btn btn-default btn-xs" href="{{route('account-changepassword')}}">Change Password</a>
		</div>
	</li>
</ul>
<ul class="list-group">
	<li class="list-group-item">
		<a href="#changeProfile" class="change-profile-picture">Change profile image</a> 
		<form action="{{route('account-save-profilepicture')}}" method='POST' class="hide" id="frmProfilePicture" enctype="multipart/form-data">
			<input type="file" id="inpFilePP" name="image" class="hide" />
			{{csrf_field()}}
		</form>
	</li>
</ul>
<ul class="list-group">
	<li class="list-group-item" style="background:#f5f5f5;border:1px solid #ccc"><b>Daily Works</b></li>
	<li class="list-group-item"><a href="{{route('account-notification')}}">My Notifications 
	@if(isset($NotificastionCount) && $NotificastionCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$NotificastionCount}}</span> @endif	
	</a></li>
	<li class="list-group-item"><a href="{{route('account-job-application')}}">My Applications</a></li>
	<li class="list-group-item"><a href="{{route('saved-jobs')}}">Saved Jobs</a></li>
	<li class="list-group-item"><a href="{{route('account-job-alerts')}}">Job Alerts</a></li>
	<li class="list-group-item"><a href="{{route('account-user-resumes')}}">My Resumes</a></li>
	<li class="list-group-item"><a href="{{route('job-search',array_merge( ['keywords'=>'','location'=>'','radius'=>'0']))}}">Job List</a></li>
</ul>
<ul class="list-group">
	<li class="list-group-item" style="background:#f5f5f5;border:1px solid #ccc"><b>Settings</b></li>
	<li class="list-group-item"><a href="{{route('account-myprofile')}}">My Profile</a></li>
	<li class="list-group-item"><a href="{{route('account-changepassword')}}">Change Password</a></li>
	<li class="list-group-item"><a href="{{route('account-settings')}}">Account Settings</a></li>
	<li class="list-group-item"><a href="{{route('account-delete')}}">Delete Account</a></li>
	<li class="list-group-item"><a href="{{route('get-jobseeker-import-contact')}}">Invite friends from Gmail</a></li>
</ul>