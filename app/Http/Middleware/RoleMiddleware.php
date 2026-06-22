<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userType = UserType::from($role);

        if (! Auth::check() || Auth::user()->type !== $userType->value) {
            return redirect('/login')->with('error', $this->errorMessage($userType));
        }

        return $next($request);
    }

    private function errorMessage(UserType $userType): string
    {
        return match ($userType) {
            UserType::Admin => 'Access denied. Admin privileges required.',
            UserType::User => 'Access denied. User privileges required.',
        };
    }
}
