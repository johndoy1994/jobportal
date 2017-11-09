@extends('layouts.backend')
@section('title', "Login")
@section('backend-sidebar')
@endsection
@push('head')
    <style type="text/css">
    body {
        background: url({{asset('backend/images/login_background.png')}}) no-repeat 0% 0% fixed;
        background-size:cover;
    }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel" style="background: rgba(255,255,255,0.8); color:black; margin-top: 45%; ">

                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form  method="post" action="{{route('admin-login-post')}}">
                            <fieldset>
                                <!-- Display Session Message  -->
                                    @if(session('success_message'))
                                        <div class="alert alert-success">
                                            {{session('success_message')}}
                                        </div>
                                    @endif

                                    @if(session('error_message'))
                                        <div class="alert alert-danger">
                                            {{session('error_message')}}
                                        </div>
                                    @endif
                                <!-- Display Session Message end-->
                                {{csrf_field()}}
                                <div class="form-group">
                                    <input class="form-control" pattern="[A-Za-z0-9-+.@ ]+" title="Please enter valid email" required placeholder="E-mail" name="email" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" required value="">
                                </div>
                            </fieldset>
                            <div class="form-group">
                                    <a href="{{route('admin-account-forgotpassword')}}">Forgot password ?</a>
                                    <button type="submit" class="btn btn-md btn-primary btn-block">Submit</button>

                            </div>
                        </form>
                        <div class="text-center">
                            <small class="text-muted">
                                &copy; <?php echo date("Y"); ?> {{env('PROJECT_TITLE')}}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

