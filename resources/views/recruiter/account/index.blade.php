@extends('layouts.recruiter')

@section('title', 'My Account')

@section('content')
	<div class="container">
		<div class="row">
			@include('includes.frontend.validation_errors')
			@include('includes.frontend.request_messages')
			<div class="col-md-3 col-sm-12">
				@include('includes.recruiter.account.sidebar')
			</div>
			<div class="col-md-9 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
	                    <div class="row">
	                        <div class="col-xs-12">
	                        	<span class="glyphicon glyphicon-home" aria-hidden="true"></span>
		                    	<label><h4><b>Dashboard</b></h4></label>
	                        </div>
	                    </div>
	                </div>
	                <style type="text/css">
	                	.my-color .glyphicon{
	                		color: darkcyan !important;
	                	}
	                	.my-color .panel-heading .glyphicon{
	                		font-size:40px;
	                	}

	                	.panel-footer{
	                		border-color:#ddd;
	                		padding: 6px 15px;
	                	}
	                	.panel-footer .glyphicon{
	                		top:3px;
	                	}
	                </style>
					<div class="panel-body">
						<div class="panel panel-default">
							<div class="panel-heading">
			                    <div class="row">
			                        <div class="col-xs-12">
			                        	<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>
				                    	<label><b>Daily Works</b></label>
			                        </div>
			                    </div>
				            </div>       
				            <div class="panel-body">
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-bell" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>
						                            	Notification!
						                            	@if(isset($NotificastionCount) && $NotificastionCount > 0) <div class="badge" style="background:green; font-weight:bold;">{{$NotificastionCount}}</div> @endif
						                            </div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-account-notifications')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Posted Jobs!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-posted-jobs')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Applications!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-application')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Candidates!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-candidates')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Post New Job!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-job', ['mode'=>'new'])}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Search CVs!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-search-cv')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						    </div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
			                    <div class="row">
			                        <div class="col-xs-12">
			                        	<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
				                    	<label><b>Settings</b></label>
			                        </div>
			                    </div>
			                </div>
			                <div class="panel-body">
								<div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Company Profile!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-company-profile')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Change Password!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-account-changepassword')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Account Setting!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-account-settings')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Delete Account!</div>
						                        </div>
						                    </div>
						                </div>
						                <a href="{{route('recruiter-account-delete')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>

						        <div class="col-lg-3 col-md-6">
						            <div class="panel panel-default my-color">
						                <div class="panel-heading">
						                    <div class="row">
						                        <div class="col-xs-3">
						                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
						                        </div>
						                        <div class="col-xs-9 text-right">
						                            <div>Invite friends from Gmail</div>
						                        </div>
						                    </div>
						                </div>
						                <!-- <a href="{{route('api-through', ['provider'=>'google', 'action'=>'contacts', 'redirect'=>'recruiter-gmail-contacts'])}}"> -->
						                	<a href="{{route('get-recruiter-import-contact')}}">
						                    <div class="panel-footer">
						                        <span class="pull-left">View Details</span>
						                        <span class="pull-right"><i class="glyphicon glyphicon-circle-arrow-right"></i></span>
						                        <div class="clearfix"></div>
						                    </div>
						                </a>
						            </div>
						        </div>
						    </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection