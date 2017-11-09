<?php

namespace App\Http\Middleware;

use App\MyAuth;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class BackendVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       // return $next($request);
        $redirectTo='admin-login';
        $guard="admin";
        $type="BACKEND";
        $level="BACKEND_ADMIN";
        if(MyAuth::check($guard)) {

            $user = MyAuth::user($guard);

            if($user->validateUser($type, $level)) {

                return $next($request);
                
            } else {
                $module=[
                    'admin-change-password',
                    'admin-change-password-post',
                    'admin-application',
                    'admin-application-post',
                    'admin-showjobapplication',
                    'api-public-applicationStatus',
                    'admin-user-resumes-download',
                    'admin-candidate',
                    'admin-candidate-post',
                    'admin-employer',
                    'admin-employer-post',
                    'admin-new-employer',
                    'admin-new-employer-post',
                    'admin-edit-employer',
                    'admin-edit-employer-post',
                    'admin-edit-employer-delete-image',
                    'admin-active-inactive-employer-post',
                    'admin-edit-employer-change-dp',
                    'admin-job',
                    'admin-job-post',
                    'admin-new-job',
                    'admin-new-job-post',
                    'admin-edit-job',
                    'admin-edit-job-post',
                    'admin-active-inactive-job-post',
                    'admin-repost-job',
                    'admin-repost-job-post',
                    'admin-renew-job',
                    'admin-renew-job-post',
                    'backend-message',
                    'backend-conversation',
                    'backend-downloadAttachment',
                    'admin-user-list',
                    'admin-user-post',
                    'admin-active-inactive-user-post',
                    'admin-login',
                    'admin-login-post',
                    'admin-home',
                    'admin-logout',
                    'admin-user-list',
                    'admin-user-post',
                    'admin-active-inactive-user-post',
                    'admin-edit-user',
                    'admin-edit-EmployerUser-post',
                    'admin-edit-admin-post',
                    'admin-edit-jobseeker-post',
                    'admin-delete-user',
                    'backend-notifications',
                    'backend-conversation',
                    'backend-downloadAttachment'
                ];

                if(in_array($request->route()->getName(),$module)){
                    return $next($request);    
                }else{
                    return redirect()->route($redirectTo)->with([
                        'error_message' => "You didn't have rights to go there"
                    ]);
                }
            }

        } else {

            return redirect()->route($redirectTo)->with([
                'error_message' => "You are not authorized1"
            ]);
        } 
    }
}
