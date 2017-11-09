@extends('layouts.backend')
@section('title', "Dashboard")
@section('content')
<div id="page-wrapper">
    <div class="row">
        </br>
        @include('includes.backend.request_messages')
        @include('includes.backend.validation_errors')
    </div>

    <!-- Dashboard -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">My Dashboard</h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Daily Works</h3>
                </div>
                <div class="panel-body">
                    <!-- /.row -->
                    <div class="row">
                        <div class="panel-heading">
                            <h3 class="panel-title">Jobs & Employers</h3>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-default my-color">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-life-saver fa-2x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>Job!</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{Route('admin-job')}}">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                            <i class="fa fa-user-md fa-2x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>All Users!</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{Route('admin-user-list')}}">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                            <i class="fa fa-user-md fa-2x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>Employer!</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{Route('admin-employer')}}">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                            <i class="fa fa-file-o fa-2x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>Application!</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{Route('admin-application')}}">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                            <i class="fa fa-users fa-2x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>Candidate!</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{Route('admin-candidate')}}">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                       <!-- /.row -->
                    </div>
                </div>
            </div>
            <?php 
            $user = \App\MyAuth::check('admin') ? \App\MyAuth::user('admin') : null;
            ?>
            @if($user && $user->type != 'SALES')
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">My Settings</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="panel-heading">
                                <h3 class="panel-title">Address Management</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-flag fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Country!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-country')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-area-chart fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>State/Province!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-state')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-home fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>City!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-city')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="panel-heading">
                                <h3 class="panel-title">Job Management</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-list fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Job Category!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-job-category')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-tasks fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Job Title !</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-job-title')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-th-list fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>JobType!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-jobtype')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-tumblr fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Tag!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-tag')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="panel-heading">
                                <h3 class="panel-title">Salary Management</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-money fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Salary Type!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-salary-type')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-money fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Salary Range!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-salary-range')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="panel-heading">
                                <h3 class="panel-title">Experience Management</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-tasks fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Experience!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-experience')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-tasks fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Experience Level!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-exp-level')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
            
                            <div class="panel-heading">
                                <h3 class="panel-title">Other Management</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-building-o fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Industry!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-industry')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-graduation-cap fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Education!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-education')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-stethoscope fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Degree!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-degree')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
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
                                                <i class="fa fa-users fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Recruiter Type!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('admin-recruitertype')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="panel-heading">
                                <h3 class="panel-title">Import / Export Management</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-file-excel-o fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Import / Export!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{Route('import-export-list')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="panel-heading">
                                <h3 class="panel-title">Invite friends from</h3>
                            </div>
                            <!-- /.col-lg-12 -->
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-default my-color">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-remove fa-2x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"></div>
                                                <div>Gmail Contacts!</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <a href="{{route('api-through', ['provider'=>'google', 'action'=>'contacts', 'redirect'=>'admin-gmail-contacts'])}}"> -->
                                    <a href="{{route('get-admin-import-contact')}}">
                                        <div class="panel-footer">
                                            <span class="pull-left">View Details</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection