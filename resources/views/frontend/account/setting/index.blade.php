@extends('layouts.frontend')

@section('title', 'Account Settings')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				@include('includes.frontend.account.sidebar')
			</div>

			<div class="col-md-9 col-sm-12">

				@include('includes.frontend.validation_errors')
				@include('includes.frontend.request_messages')

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Account Settings</h3>
				    </div>
					<div class="panel-body">
						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
                                Change email address
                            </button>
	                    </div>
						<hr>

						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<a  class="btn btn-primary btn-sm" href="{{route('account-changepassword')}}">Change password</a>
						</div>
						<hr>

						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<a class="btn btn-primary btn-sm" href="{{route('account-delete')}}">Close my account</a>
						</div>
						<hr>
						
						<h3>My profile</h3>
						<hr>
						<span>Create a profile to highlight your experience and skills. This will make it easier for the right employer to find you.</span>
						@if($hasResume)
						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<a href="{{Route('account-user-resumes-download',['id'=>$user->id])}}">Download CV</a>
						</div>
						@endif
						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<a href="{{Route('account-user-resumes')}}" class="btn btn-sm btn-primary">Add / Change your CV</a>
							<a class="btn btn-primary btn-sm" href="{{route('account-myprofile')}}">Edit profile</a>
						</div>
					</div>
				</div>


				<form class="form-horizontal" method="post" action="{{route('account-settings-save-profilevisibility')}}">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Profile visibility</h3>
					    </div>
					    <br>

					    <div class="panel-body">
							<span>You can make your profile searchable to recruiters here.</span>
							<br>

							<div class="form-group">
								<label class="control-label col-lg-3"></label>
									<input name="profile_privacy" value="1" type="radio" @if($profileVisibility==1) checked @endif name="radio" ><b>Searchable</b>(Recommended)
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3"></label>
									<input name="profile_privacy" value="2" type="radio" @if($profileVisibility==2) checked @endif  name="radio"><b>Searchable</b> but hide my CV and personal deteails
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3"></label>
									<input name="profile_privacy" value="3" type="radio" @if($profileVisibility==3) checked @endif name="radio"><b>Not searchable</b>
							</div>
							<div class="col-md-7 text-center">
								<div class="form-group">
									<button type="submit" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
						{{csrf_field()}}
					</div>
				</form>


				@include('frontend.account.job-alerts.partial')


				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Instant Job Match</h3>
				    </div>
					
					<div class="panel-body">
						<span>Receive relevant jobs as soon as they’re advertised, so you can be the first to see them. You decide the maximum number of matches you want to receive each day and can pause them at any time.</span>
					</div>

					<div class="panel-heading">
						<h3 class="panel-title">Instant job match setting</h3>
				    </div>
					<div class="panel-body">
						<form class="form-horizontal" method="post" action="{{route('account-settings-save-instantmatch')}}">
							{{csrf_field()}}
							<div class="form-group">
								<label class="control-label col-lg-3"><strong>Status :</strong></label>
								<div class="col-lg-7">
									<div class="radio">
										<label style="margin:5px"><input type="radio" name="status" value=1 @if($instantMatch) {{ $instantMatch->status == 1 ? 'checked' : '' }} @endif /> On</label>
										<label style="margin:5px"><input type="radio" name="status" value=0 @if($instantMatch) {{ $instantMatch->status == 0 ? 'checked' : '' }} @endif/> Off</label>
									</div>
								</div>
							</div>
							
							
							<div class="form-group">
								<label class="control-label col-lg-3"><strong>Email Frequency :</strong></label>
								<div class="col-lg-7">
									<div class="radio">
										<label style="margin:5px"><input type="radio" name="email_frequency" value=1 @if($instantMatch) {{ $instantMatch->email_frequency == 1 ? 'checked' : '' }} @endif /> When job posted</label>
										<label style="margin:5px"><input type="radio" name="email_frequency" value=2 @if($instantMatch) {{ $instantMatch->email_frequency == 2 ? 'checked' : '' }} @endif/> Daily</label>
										<label style="margin:5px"><input type="radio" name="email_frequency" value=3 @if($instantMatch) {{ $instantMatch->email_frequency == 3 ? 'checked' : '' }} @endif/> Weekly</label>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3"><strong>Push Frequency :</strong></label>
								<div class="col-lg-7">
									<div class="radio">
										<label style="margin:5px"><input type="radio" name="push_frequency" value=1 @if($instantMatch) {{ $instantMatch->push_frequency == 1 ? 'checked' : '' }} @endif /> When job posted</label>
										<label style="margin:5px"><input type="radio" name="push_frequency" value=2 @if($instantMatch) {{ $instantMatch->push_frequency == 2 ? 'checked' : '' }} @endif/> Daily</label>
										<label style="margin:5px"><input type="radio" name="push_frequency" value=3 @if($instantMatch) {{ $instantMatch->push_frequency == 3 ? 'checked' : '' }} @endif/> Weekly</label>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3"><strong>Pause :</strong></label>
								@if($instantMatch)
									@if($instantMatch->inPause())
										Paused till {{$instantMatch->pause->format('d-m-Y h:i A')}} 
										<label><input type="checkbox" name="cancel_pause" /> Cancel</label>
									@else
										<div class="col-lg-3">
											<div class="radio">
												<label style="margin:5px"><input type="radio" name="pause" value=1  /> 1 Hour</label>
												<br/>
												<label style="margin:5px"><input type="radio" name="pause" value=2 /> 4 Hours</label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="radio">
												<label style="margin:5px"><input type="radio" name="pause" value=3 /> 1 Day</label>
												<br/>
												<label style="margin:5px"><input type="radio" name="pause" value=4 /> 2 Days</label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="radio">
												<label style="margin:5px"><input type="radio" name="pause" value=5 /> 1 Week</label>
												<br/>
												<label style="margin:5px"><input type="radio" name="pause" value=6 /> 2 Weeks</label>
											</div>
										</div>
									@endif
								@endif
							</div>

							<div class="form-group">
								<div class="col-lg-7 col-lg-offset-3">
									<button type="submit" class="btn btn-sm btn-primary">Save</button>
								</div>
							</div>
						</form>
					</div>
				</div>


				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">My Applications</h3>
				    </div>

					<div class="panel-body">
						<h3 class="panel-title pull-left">
							See all of the jobs you've applied for and contact details for the employers involved.
						</h3>
						<a class="btn btn-primary pull-right" href="{{route('account-job-application')}}">See application</a>
					</div>
				</div>

			</div>
		</div>
	</div>

<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                    	<div class="form-group error">
                            <label for="inputTask" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="email" id="email"placeholder="Your e-mail address..." value="{{$user->getEmailAddress()}}" required="" />
                            </div>
                        </div>
                    </form>
                    <div class="alert ajax-change-email"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="btn-save" class="btn btn-primary">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->	
@endsection

@push('footer')
<script type="text/javascript">
$(document).ready(function() {
	$('#btn-save').click(function(){
		
		var formData = {
            email: $('#email').val(),
        };
		$.ajax({
			url: "{{route('api-update-email-address')}}",
			type: "POST",
		    dataType: 'json',
		    data: {
		    	email: $("#email").val(),
		    	_token : "{{csrf_token()}}"
		    },
		    success: function (data) {
		    	console.log(data);
		    	if(data[0]) {
		    		$(".ajax-change-email").addClass("alert-success");
		    	} else {
		    		$(".ajax-change-email").addClass("alert-danger");
		    	}
		    	$(".ajax-change-email").html(data[1]);
		    },
		    
		});
	});
});

</script>
@endpush
