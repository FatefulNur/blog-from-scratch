<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Membership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $membership = Setting::firstOrNew(['id' => 1]);
        $membership->fill(['membership' => 0]);
        $membership->save();

        if(!$membership) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
