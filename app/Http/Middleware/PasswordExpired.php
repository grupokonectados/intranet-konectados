<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $password_changed_at = new Carbon(($user->password_changed_at) ? $user->password_changed_at : $user->created_at);

        if ($user->password_changed_at === null) {
            return redirect()->route('password.expired');
        }else if(Carbon::now()->diffInDays($password_changed_at) >= 90){
            return redirect()->route('password.expired');
        }

        return $next($request);
    }
}
