<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class middle_tech
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
        if(Auth::user() and Auth::user()->roles_id == '1'){
            return $next($request);
        }else{
            return route('login');
        }
        return abort(404);
    }
}
