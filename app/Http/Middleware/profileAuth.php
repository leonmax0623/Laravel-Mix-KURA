<?php

namespace App\Http\Middleware;

use App\Models\Members;
use Closure;
use Illuminate\Http\Request;

class profileAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (isset($_COOKIE['auth_hash']) && $_COOKIE['auth_hash']) {
            $member = Members::where('auth_hash', $_COOKIE['auth_hash'])->first();
            if(!empty($member))
                return $next($request);
        }
        return redirect('auth');
    }
}
