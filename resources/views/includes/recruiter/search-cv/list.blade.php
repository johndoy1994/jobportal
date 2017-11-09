<div class="row">
	@foreach($cvs as $cv)
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-heading clearfix" style="border-bottom: 1px solid #e1e1e1">
					<div class="pull-left">
						<h3><a href="{{$cvSearchUri($filters, [ 'id'=>$cv->id ], 'recruiter-cv-detail')}}">{{$cv->getPersonTitle()}} {{$cv->getFirstName()}}</a></h3>
					</div>
					<div class="pull-right" style="margin-top: 10px">
						@if($cv->profile_privacy==1 && $cv->is_resume($cv))
							<a href="{{Route('recruiter-user-resumes-download',['id'=>$cv->id])}}" class="btn btn-primary">Download CV</a>
						@endif
						@if($cv->isUserValidForConversation($cv->UserChatgetRef($cv->id),'recruiter'))
							<a href="{{route('recruiter-conversation',['conversation_ref'=>$cv->UserChatgetRef($cv->id)])}}" class="btn btn-primary">Chat</a>
						@endif
						<a href="" data-toggle="modal" data-target="#myModal" data-id="{{$cv->id}}" class="btn btn-success msg">Msg</a>
						@if($cv->mobile_number)
							<a href="tel:{{$cv->mobile_number}}" class="btn btn-warning">Call</a>
						@endif	
						@if($cv->email_address)
							<a href="" data-toggle="modal" data-target="#myModal1" data-id="{{$cv->id}}" data-email="{{$cv->email_address}}" class="btn btn-info btn-email">Email</a>
						@endif
					</div>
				</div>
				<div class="panel-body">
					<div class="col-md-3 col-sm-12">
						<img src="{{route('account-avatar-100x100', ['id'=>$cv->id])}}" style="border:1px solid #ccc;padding:3px;border-radius:5px" />
					</div>
					<div class="col-md-9 col-sm-12">
						<ul class="list-unstyled">
							<li><b>Full Name :</b>  {{$cv->getPersonTitle()}} {{$cv->getName()}}</li>
							<li><b>Education :</b> {{$cv->getHighestEducationString()}}</li>
							<li><b>Recent Job :</b> {{$cv->getRecentJobTitle()}} ({{$cv->job_title}})</li>
							<li><b>Experience :</b> {{$cv->getExperienceName()}} ({{$cv->getExperienceLevelName()}})</li>
							<li><b>Job Type :</b> {{$cv->getJobTypeString()}}</li>
							<li><b>Job Category :</b> {{$cv->job_category_name}}</li>
							<li><b>Salary : </b> {{$cv->getDesiredSalaryString()}}</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	@endforeach
	<div class="col-md-12 text-center">
		{{$cvs->appends($filters)->render()}}	
	</div>
</div>