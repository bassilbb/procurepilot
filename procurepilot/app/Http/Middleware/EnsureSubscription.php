<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->organization_id && ! $user->organization?->hasActiveSubscription()) {
            return redirect()->route('billing.plan')->with('warning', 'Please choose a subscription plan to continue.');
        }

        return $next($request);
    }
}
