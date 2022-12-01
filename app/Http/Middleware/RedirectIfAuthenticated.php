<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check() && Auth::user()->role_id == 1) {
            //return redirect()->route('admin.dashboard');
            return redirect()->route('users.index');
        } elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 2){
            return redirect()->route('teacher.dashboard');
        } elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 3){
            //return redirect()->route('student.dashboard');
            return redirect()->route('getStudentExamList');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 5){
            //return redirect()->route('student.dashboard');
            return redirect()->route('report.class-test-reports.correct-incorrect-answer');
        }elseif(Auth::guard($guard)->check() && Auth::user()->role_id == 7){
            return redirect()->route('principal.dashboard');
        }else{
            return $next($request);
        }
    }
}
