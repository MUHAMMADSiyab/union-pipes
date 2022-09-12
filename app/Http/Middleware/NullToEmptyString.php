<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NullToEmptyString
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        foreach (request()->all() as $field => $value) {
            if ($value === "null" || $value === "undefined") {
                request()->merge([$field => NULL]);
            }
        }

        return $next($request);
    }
}
