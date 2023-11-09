<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevelopmentSessionDomains
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() === config('app.ngrok_domain')) {
            config([
                'session.domain' => config('app.ngrok_domain')
            ]);
        }
        return $next($request);
    }
}

