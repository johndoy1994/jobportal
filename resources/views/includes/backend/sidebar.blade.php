<?php
    $user = \App\MyAuth::check('admin') ? \App\MyAuth::user('admin') : null;
?>
<!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{Route('admin-home')}}">{{env('PROJECT_TITLE')}}</a>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-weixin fa-fw"></i> Chat @if(isset($messageCount) && $messageCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$messageCount}}</span> @endif <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <!-- <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li> -->
                        <li><a href="{{Route('backend-message',['Usertype'=>'jobseeker'])}}">
                            <i class="fa fa-weixin fa-fw"></i> Job Seeker  @if(isset($JobseekermessageCount) && $JobseekermessageCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$JobseekermessageCount}}</span> @endif</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="{{Route('backend-message',['Usertype'=>'recruiter'])}}">
                            <i class="fa fa-weixin fa-fw"></i> Recruiter @if(isset($RecruitermessageCount) && $RecruitermessageCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$RecruitermessageCount}}</span> @endif</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->

                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-exclamation-circle fa-fw"></i> Notifications @if(isset($notificationCount) && $notificationCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$notificationCount}}</span> @endif <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <!-- <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li> -->
                        <li><a href="{{Route('backend-notifications',['Usertype'=>'jobseeker'])}}">
                            <i class="fa fa-exclamation-circle fa-fw"></i> Job Seeker  @if(isset($JobseekernotificationCount) && $JobseekernotificationCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$JobseekernotificationCount}}</span> @endif</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="{{Route('backend-notifications',['Usertype'=>'recruiter'])}}">
                            <i class="fa fa-exclamation-circle fa-fw"></i> Recruiter @if(isset($RecruiternotificationCount) && $RecruiternotificationCount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$RecruiternotificationCount}}</span> @endif</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>{{($user) ? $user->name : "N/A"}}<i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <!-- <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li> -->
                        <li><a href="{{Route('admin-change-password-post')}}"><i class="fa fa-gear fa-fw"></i> Change Password</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="{{Route('admin-logout')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                
            </ul>
            <!-- /.navbar-top-links -->
           

            <div class="navbar-default affix sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="{{Route('admin-home')}}"><i class="fa fa-dashboard"></i> Dashboard</a>
                        </li>
                       
                        <?php  
                            $segment1 = Request::segment(2);
                        ?>
                        
                        
                        <li class="<?php echo ($segment1 == "job-list" || $segment1 == "employer-list" ||  $segment1 == "application-list" ||  $segment1 == "candidate-list" ||  $segment1 == "user-details" || $segment1 == "user-list") ? " active " : ""; ?>">
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Jobs & Employers<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                
                                <li>
                                    <a class="<?php echo ($segment1 == "job-list") ? " active " : ""; ?>" href="{{route('admin-job')}}"><i class="fa fa-life-saver"></i> Job List</a>
                                </li>
                                 <li>
                                    <a class="<?php echo ($segment1 == "user-list") ? " active " : ""; ?>" href="{{Route('admin-user-list')}}"><i class="fa fa-user-md"></i> User List</a>
                                </li>
                                <li>
                                    <a class="<?php echo ($segment1 == "employer-list") ? " active " : ""; ?>" href="{{route('admin-employer')}}"><i class="fa fa-user-md"></i> Employer List</a>
                                </li>

                                <li>
                                    <a class="<?php echo ($segment1 == "application-list") ? " active " : ""; ?>" href="{{route('admin-application')}}"><i class="fa fa-file-o"></i> Application List</a>
                                </li>

                                <li>
                                    <a class="<?php echo ($segment1 == "candidate-list") ? " active " : ""; ?>" href="{{route('admin-candidate')}}"><i class="fa fa-users"></i> Candidate List</a>
                                </li>

                            </ul>   
                        </li>
                        
                        @if($user && $user->type != 'SALES')
                        <li class="<?php echo ($segment1 == "country-list" || $segment1 == "state-list" || $segment1 == "city-list" || $segment1 == "job-category" || $segment1 == "job-title" || $segment1 == "jobtypes" || $segment1 == "tags" || $segment1 == "salary-type" || $segment1 == "salary-range" || $segment1 == "experience" || $segment1 == "experience-level" || $segment1 == "recruitertypes" || $segment1 == "industry-list" || $segment1 == "education-list" || $segment1 == "degree-list" || $segment1 == "import-contacts" ||  $segment1 == "import-export") ? " active " : ""; ?>">
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Settings<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <li class="<?php echo ($segment1 == "country-list" || $segment1 == "state-list" || $segment1 == "city-list") ? " active " : ""; ?>">
                                    <a href="#"><i class="fa fa-location-arrow fa-fw"></i> Address Management<span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a class="<?php echo ($segment1 == "country-list") ? " active " : ""; ?>" href="{{route('admin-country')}}"><i class="fa fa-flag"></i> Country List</a>

                                        </li>
                                
                                        <li>
                                            <a class="<?php echo ($segment1 == "state-list") ? " active " : ""; ?>" href="{{route('admin-state')}}"><i class="fa fa-area-chart"></i> State/Province List</a>
                                        </li>
                                
                                        <li>
                                            <a class="<?php echo ($segment1 == "city-list") ? " active " : ""; ?>" href="{{route('admin-city')}}"><i class="fa fa-home"></i> City List</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="<?php echo ($segment1 == "job-category" || $segment1 == "job-title" || $segment1 == "jobtypes" || $segment1 == "tags") ? " active " : ""; ?>">
                                    <a href="#"><i class="fa fa-list-alt fa-fw"></i> Job Management<span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a class="<?php echo ($segment1 == "job-category") ? " active " : ""; ?>" href="{{route('admin-job-category')}}"><i class="fa fa-list"></i> Job Category List</a>
                                        </li>

                                        <li>
                                            <a class="<?php echo ($segment1 == "job-title") ? " active " : ""; ?>" href="{{route('admin-job-title')}}"><i class="fa fa-list"></i> Job Title List</a>
                                        </li> 

                                        <li>
                                            <a class="<?php echo ($segment1 == "jobtypes") ? " active " : ""; ?>" href="{{route('admin-jobtype')}}"><i class="fa fa-th-list"></i> JobType List</a>
                                        </li>

                                        <li>
                                            <a class="<?php echo ($segment1 == "tags") ? " active " : ""; ?>" href="{{route('admin-tag')}}"><i class="fa fa-tumblr"></i> Tag List</a>
                                        </li>  
                                        
                                    </ul>
                                </li>

                                <li class="<?php echo ($segment1 == "salary-type" || $segment1 == "salary-range") ? " active " : ""; ?>">
                                    <a href="#"><i class="fa fa-dollar fa-fw"></i> Salary Management<span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a class="<?php echo ($segment1 == "salary-type") ? " active " : ""; ?>" href="{{route('admin-salary-type')}}"><i class="fa fa-money"></i> Salary Type</a>
                                        </li>
                                        
                                        <li>
                                            <a class="<?php echo ($segment1 == "salary-range") ? " active " : ""; ?>" href="{{route('admin-salary-range')}}"><i class="fa fa-money"></i> Salary Range</a>
                                        </li>  
                                        
                                    </ul>
                                </li>

                                <li class="<?php echo ($segment1 == "experience" || $segment1 == "experience-level") ? " active " : ""; ?>">
                                    <a href="#"><i class="fa fa-arrows-alt fa-fw"></i> Experience Management<span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a class="<?php echo ($segment1 == "experience") ? " active " : ""; ?>" href="{{route('admin-experience')}}"><i class="fa fa-tasks"></i> Experience</a>
                                        </li>
                                        
                                        <li>
                                            <a class="<?php echo ($segment1 == "experience-level") ? " active " : ""; ?>" href="{{route('admin-exp-level')}}"><i class="fa fa-tasks"></i> Experience Level</a>
                                        </li> 
                                        
                                    </ul>
                                </li>

                                <li class="<?php echo ($segment1 == "recruitertypes" || $segment1 == "industry-list" || $segment1 == "education-list" || $segment1 == "degree-list") ? " active " : ""; ?>">
                                    <a href="#"><i class="fa fa-plus-circle fa-fw"></i> Other Management<span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a class="<?php echo ($segment1 == "recruitertypes") ? " active " : ""; ?>" href="{{route('admin-recruitertype')}}"><i class="fa fa-users"></i> Recruiter Type List</a>
                                        </li>

                                        <li>
                                            <a class="<?php echo ($segment1 == "industry-list") ? " active " : ""; ?>" href="{{route('admin-industry')}}"><i class="fa fa-building-o"></i> Industry List</a>
                                        </li>

                                        <li>
                                            <a class="<?php echo ($segment1 == "education-list") ? " active " : ""; ?>" href="{{route('admin-education')}}"><i class="fa fa-graduation-cap"></i> Education List</a>
                                        </li>

                                        <li>
                                            <a class="<?php echo ($segment1 == "degree-list") ? " active " : ""; ?>" href="{{route('admin-degree')}}"><i class="fa fa-stethoscope"></i> Degree List</a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <li class="<?php echo ($segment1 == "import-contacts") ? " active " : ""; ?>">
                                    <a class="<?php echo ($segment1 == "import-contacts ") ? " active " : ""; ?>" href="{{route('get-admin-import-contact')}}"><i class="fa fa-users"></i> Invite friends from Gmail!</a>
                                </li>
                                <li>
                                    <a class="<?php echo ($segment1 == "import-export") ? " active " : ""; ?>" href="{{route('import-export-list')}}"><i class="fa fa-file-excel-o fa-fw"></i> Import / Export</a>
                                </li>
                            </ul>
                        </li> 

                        <li>
                            <a href="{{route('admin-cms-page')}}"><i class="fa fa-files-o fa-fw"></i> CMS Pages</a>
                        </li>
                        @endif
                        <!-- <li>
                            <a href="{{route('admin-communication')}}"><i class="fa fa-files-o fa-fw"></i> communication</a>
                        </li> -->
                        
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>