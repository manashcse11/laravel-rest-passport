<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class ResourceModification
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
        $loggedin_user = Auth::user();
        if(!$loggedin_user->hasRole('Admin') && $loggedin_user->id != $request->user_id){
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
        return $next($request);
    }
}
