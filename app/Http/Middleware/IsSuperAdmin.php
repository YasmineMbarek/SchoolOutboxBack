<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */

    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()) {
            if (auth()->user()->role->name == Role::ROLE_SUPERADMIN) {
            return $next($request);
        }}

        abort(403);
    }
}
