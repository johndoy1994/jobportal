@extends('layouts.frontend')

@section('title', 'Sign in to your account')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('account-signin-post')}}">
					<legend>Sign in to your account</legend>
					<fieldset>

						@include('includes.frontend.validation_errors')
						@include('includes.frontend.request_messages')
						<?php $stored_cookies=array_filter($stored_cookies);?>
						@if(!empty($stored_cookies))
							<div class="well col-md-4 col-md-offset-4">
								<p style="font-size:20px;font-weight:bold;border-bottom:1px solid #ccc">Choose an account</p>

								@foreach($stored_cookies as $user_account)
								<div class="media user_account_div" data-id="{{Crypt::encrypt($user_account->id)}}" style="cursor:pointer">
							        <a href="#" class="pull-left">        
							          <img alt="64x64" data-src="holder.js/64x64" class="media-object img-thumbnail" style="width: 64px; height: 64px;" src="{{route('account-avatar-100x100',['id'=> $user_account->id])}}">      
							        </a>
							        <a href="#" class="pull-right"><span class="glyphicon glyphicon-chevron-right"></a>
							        <div class="media-body">
							          <h4 class="media-heading"><strong><a href="#">{{$user_account->name}}</a></strong></h4>
							          <p>{{($user_account->email_address)? $user_account->email_address : $user_account->mobile_number}}</p>
							        </div>
							    </div>   
								@endforeach
							</div>
							<div class="well well-sm col-md-4 col-md-offset-4">
								<div class="col-md-6 text-center" style="border-right:1px solid #ccc">
									<a href="{{route('selected-cookie-account')}}">Add account</a>
								</div>
								<div class="col-md-6 text-center">
									<a href="#" class="remove_button">Remove</a>
								</div>
							</div>
						@else
							<div class="clearfix"></div>
							<div class="form-group">
								<label class="col-lg-3 control-label">E-mail address or mobile number : *</label>
								<div class="col-lg-4">
									<input type="text" class="form-control" name="email_address" value="{{ !empty($userDataVerified) ? ($userDataVerified->email_address)? $userDataVerified->email_address: $userDataVerified->mobile_number : old('email_address')}}" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">Password :</label>
								<div class="col-lg-4">
									<input type="password" class="form-control" name="password" value="{{old('password')}}" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-8 col-lg-offset-3">
									<div class="btn-toolbar">
										<div class="btn-group">
											<button type="submit" value="Register" class="btn btn-primary btn-sm">Sign In</button>
											<a class="btn btn-warning btn-sm" href="{{route('account-forgotpassword')}}">Forgot password ?</a>
										</div>
										<div class="btn-group">
											<a class="btn btn-primary btn-sm" href="{{route('account-through', ['provider'=>'facebook'])}}">SignIn with Facebook</a>
											<a class="btn btn-primary btn-sm" href="{{route('account-through', ['provider'=>'linkedin'])}}">SignIn with LinkedIn</a>
										</div>
									</div>
								</div>
							</div>
						@endif
						{{csrf_field()}}
					</fieldset>
				</form>
			</div>
		</div>
	</div>
<input type="hidden" name="add_remove" id="add_remove" value="add">	
<form class="form-horizontal hidden" id="submit-cookie-account" method="get" action="{{route('selected-cookie-account')}}">
	<input type="hidden" name="key" id="key" value="">
	<input type="hidden" name="type" id="type" value="">
</form>

@endsection
@push('footer')
	<script type="text/javascript">
		$(".user_account_div").on("click",function(){
			var userId = $(this).data("id");
			if(userId)
			{
				var typeValue = $('#add_remove').val();
				if(typeValue == "remove")
				{
					if(confirm("Are you sure to remove this account?"))
					{
						$('#type').val($('#add_remove').val());
						$("#key").val(userId);
						$("#submit-cookie-account").submit();
					}
				}else{
					$('#type').val($('#add_remove').val());
					$("#key").val(userId);
					$("#submit-cookie-account").submit();	
				}
				
			}
		});

		$(".remove_button").on("click",function(){
			$('#add_remove').val('remove');
			$(".media a .glyphicon").removeClass("glyphicon-chevron-right");
			$(".media a .glyphicon").addClass("glyphicon-remove");
		});
		
	</script>
@endpush