@extends('layouts.backend')
@section('title', "Reset your password")
@section('backend-sidebar')
@endsection
@push('head')
    <style type="text/css">
    body {
        background: #DDD;
        background-size:cover;
    }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel" style="background: rgba(255,255,255,0.8); color:black;  ">
                    @if($passwordReset && $passwordReset->code == $code)
                        <div class="panel-heading">
                            <h3 class="panel-title"><b>Set new password</b></h3>
                        </div>
                        <div class="panel-body">
                            @include('includes.frontend.validation_errors')
                            @include('includes.frontend.request_messages')
                            @if(!session('hideForm'))
                                <form  method="post" action="{{route('api-public-resetpasswordlink-post', ['code' => $code])}}">
                                    <fieldset>
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <label class="control-label">New Password :</label>
                                            <input type="password" class="form-control" name="password" placeholder="New password..." />
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Confirm Password :</label>
                                            <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm your new password..." />
                                        </div>
                                    </fieldset>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-md btn-primary btn-block">Save</button>
                                    </div>
                                </form>
                            @else
                                <Center>
                                
                                <a href="{{route($type)}}">Goto Homepage</a>
                                </Center>
                                <br/>
                            @endif
                            <div class="text-center">
                                <small class="text-muted">
                                    &copy; <?php echo date("Y"); ?> {{env('PROJECT_TITLE')}}
                                </small>
                            </div>
                        </div>
                    @else
                        @if(session('hideForm'))
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">Your new password saved successfully.</h3>
                            <br/>
                            <Center>
                            
                            <a href="{{route($type)}}">Goto Homepage</a>
                            </Center>
                            <br/>
                        </div>    
                        @else
                        <div class="panel-heading">
                            <h3 class="panel-title">Sorry your code doesn't belongs to any request, please try again.</h3>
                            <br/>
                            
                            <a href="{{route($type)}}">Back</a>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

