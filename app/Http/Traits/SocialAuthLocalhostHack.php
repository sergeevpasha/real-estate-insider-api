<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait SocialAuthLocalhostHack
{
    /**
     * @param Request $request
     * @param string $service
     * @return RedirectResponse|null
     */
    private function localhostHack(Request $request, string $service): RedirectResponse|null
    {
        if (App::environment('local')) {
            $request->session()->put('state', $request->get('state'));
            config([
                'session.domain' => env('SESSION_DOMAIN')
            ]);

            if ($request->getHost() === config('app.ngrok_domain')) {
                return redirect(
                    config('app.url') . "/login/$service/callback?redirect=" . $request->get(
                        'redirect'
                    ) . "&code=" . $request->get('code') . "&state=" . $request->get('state')
                );
            }
        }

        return null;
    }
}
