<?php

namespace App\Http\Middleware;

use App\Models\Members;
use App\Models\MembersInformation;
use Closure;
use Illuminate\Http\Request;

class checkNonDisclosure
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
                $memberInformation = MembersInformation::where('member_id', $member['id'])->where('field_name', 'non_disclosure')->where('value', 1)->first();
                if(empty($memberInformation))
                    return redirect('non_disclosure');

                return $next($request);
            }
        }
        return redirect('auth');

    }
}
