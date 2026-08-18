<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class TenancyScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->organization_id) {
            $org = $user->organization;

            if ($org) {
                app()->instance('currentOrganization', $org);
                session(['organization_id' => $org->id]);
            }
        }

        return $next($request);
    }
}
