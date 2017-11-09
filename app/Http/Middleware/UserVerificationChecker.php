<?php

namespace App\Http\Middleware;

use App\MyAuth;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class UserVerificationChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected $bypass_routes = ['account-verification','account-verification-post','account-signout'];

    public function handle($request, Closure $next, $guard="web")
    {
        if(!in_array($request->route()->getName(), $this->bypass_routes) && MyAuth::check($guard)) {

            $user = MyAuth::user($guard);            

            if($user->validateUser('job_seeker', 'frontend')) {

                if($user->verification) {

                    if($user->verification->status == \App\Models\UserVerification::NOT_VERIFIED) {
                        return redirect()->route('account-verification');
                    }

                } else {

                    return redirect()->route('account-verification');

                }

            }

        }

        return $next($request);
    }
}
