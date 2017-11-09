<?php

namespace App\Http\Middleware;

use App\MyAuth;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $level, $type, $guard="web",$redirectTo = "front-home")
    {
        //return $next($request);

        if(MyAuth::check($guard)) {

            $user = MyAuth::user($guard);

            if($user->validateUser($type, $level)) {
                
                return $next($request);
                
            } else {
                return redirect()->route($redirectTo)->with([
                    'error_message' => "You didn't have rights to go there"
                ]);
            }

        } else {

            return redirect()->route($redirectTo)->with([
                'error_message' => "You are not authorized"
            ]);

        }
    }
}
