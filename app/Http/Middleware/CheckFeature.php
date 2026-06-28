<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your current plan does not include this feature.'], 403);
            }

            return redirect()->route('supplier-panel.subscription.index')
                ->with('error', 'Your current plan does not include this feature. Please upgrade to access it.');
        }

        return $next($request);
    }
}
