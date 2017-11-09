<ul class="list-group">
	<li class="list-group-item text-center">
		<img src="{{route('account-avatar-100x100',['id'=>$user->id])}}" style="background: #333; "/>
		<h3 id="account-name">{{\App\MyAuth::user("recruiter")->getName()}}</h3> 
		<div class="">
			<a class="btn btn-default btn-xs" href="{{route('recruiter-company-profile')}}">Company Profile</a>
		</div>
	</li>
</ul>
<ul class="list-group">
	<li class="list-group-item">
		<a href="#changeProfile" class="change-profile-picture">Change Company Logo</a> 
		<form action="{{route('recruiter-account-save-profilepicture')}}" method='POST' class="hide" id="frmProfilePicture" enctype="multipart/form-data">
			<input type="file" id="inpFilePP" name="image" class="hide" />
			{{csrf_field()}}
		</form>
	</li>
</ul>
<ul class="list-group">
	<li class="list-group-item" style="background:#f5f5f5;border:1px solid #ccc"><b>Daily Works</b></li>
	<li class="list-group-item"><a href="{{route('recruiter-account-notifications')}}">Notifications
	@if(isset($NotificastionCount) && $NotificastionCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$NotificastionCount}}</span> @endif		
	</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-posted-jobs')}}">Posted Jobs</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-application')}}">Applications</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-candidates')}}">Candidates</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-job', ['mode'=>'new'])}}">Post New Job</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-search-cv')}}">Search CVs</a></li>
</ul>
<ul class="list-group">
	<li class="list-group-item" style="background:#f5f5f5;border:1px solid #ccc"><b>Settings</b></li>
	<li class="list-group-item"><a href="{{route('recruiter-company-profile')}}">Company Profile</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-account-changepassword')}}">Change Password</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-account-settings')}}">Account Settings</a></li>
	<li class="list-group-item"><a href="{{route('recruiter-account-delete')}}">Delete Account</a></li>
	<li class="list-group-item"><a href="{{route('get-recruiter-import-contact')}}">Invite friends from Gmail</a></li>
</ul>
