<?php

namespace App\Http\Middleware;

use App\UserVisitLog;
use Closure;

class ApiLog
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
        $userVisitLog = new UserVisitLog();
        $userVisitLog->user_id = $request->user()->user_id;
        $userVisitLog->action  = $request->path();
        $userVisitLog->save();

        return $next($request);
    }
}
