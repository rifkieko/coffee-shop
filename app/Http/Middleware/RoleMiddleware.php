<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  iterable<string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        $allowedRoles = collect($roles)
            ->map(function (string $role) {
                try {
                    return UserRole::from($role);
                } catch (\ValueError) {
                    return null;
                }
            })
            ->filter()
            ->values();

        if ($allowedRoles->isEmpty() || ! $allowedRoles->contains($user->role)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}

