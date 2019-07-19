<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class IsAdmin
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
        if(!Auth::user()->hasRole('Admin')){
            return response()->json(['error'=>'Unauthorised'], 401); 
            // return redirect('unauthorized');
        }
        return $next($request);
    }
}
