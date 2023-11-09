<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonMiddleware
{
    /**
     * Forces JSON Accept headers to always return a JSON data.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);
        $data = $response->getData(true);
        $data['status'] = $response->getStatusCode();
        $response->setData($data);

        return $response;
    }
}
