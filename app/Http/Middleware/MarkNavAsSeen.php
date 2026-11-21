<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\NavBadgeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkNavAsSeen
{
    public function __construct(private NavBadgeService $badges) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = $request->route()?->getName();

        if ($user instanceof User && $request->isMethod('get') && $routeName !== null) {
            $navKey = NavBadgeService::ROUTE_KEYS[$routeName] ?? null;

            if ($navKey !== null) {
                $this->badges->markSeen($user, $navKey);
            }
        }

        return $next($request);
    }
}
