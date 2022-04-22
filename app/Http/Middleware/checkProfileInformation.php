<?php

namespace App\Http\Middleware;

use App\Models\Members;
use App\Models\MembersInformation;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class checkProfileInformation
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
            if(!empty($member)) {
                $value_names = ['name', 'max_start_sum', 'whos_i'];
                $memberInformation = MembersInformation::where('member_id', $member['id'])->whereIn('field_name', $value_names)->count();
                if($memberInformation < 3)
                    return redirect('profile');

                return $next($request);
            }
        }
        return redirect('auth');

    }
}
