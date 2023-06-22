<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthGates
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
        $user = Auth::user();

        if (!app()->runningInConsole() && $user) {
            $roles = Role::with('permissions')->get();

            foreach ($roles as $role) {
                foreach ($role->permissions as $permission) {
                    $permissionsArray[$permission->name][] = $role->id;
                }

                foreach ($permissionsArray as $permissionName => $roles) {
                    Gate::define($permissionName, function (User $user) use ($roles) {
                        return count(
                            array_intersect($user->roles->pluck('id')->toArray(), $roles)
                        ) > 0;
                    });
                }
            }
        }

        return $next($request);
    }
}
