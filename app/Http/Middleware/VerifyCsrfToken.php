<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    protected function addCookieToResponse($request, $response): Response
    {
        $config = config('session');

        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        return $response;
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param Request $request
     * @return string
     */
    protected function getTokenFromRequest($request): string
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            try {
                $token = CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
            } catch (DecryptException $e) {
                $token = '';
            }
        }

        return $token;
    }
}
