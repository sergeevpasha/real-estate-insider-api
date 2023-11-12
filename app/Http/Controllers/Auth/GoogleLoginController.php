<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Dto\UserAuthFields;
use App\Http\Controllers\Controller;
use App\Http\Traits\GuessLanguageTrait;
use App\Http\Traits\SocialAuthLocalhostHack;
use App\Services\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleLoginController extends Controller
{
    use GuessLanguageTrait;
    use SocialAuthLocalhostHack;

    /**
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $request->session()->put('redirect', $request->get('redirect'));
        return Socialite::driver('google')->redirect();
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function handleGoogleCallback(Request $request): JsonResponse|RedirectResponse
    {
        $hackRedirect = $this->localhostHack($request, 'google');

        if ($hackRedirect) {
            return $hackRedirect;
        }

        $googleUser = Socialite::driver('google')->user();

        $redirect = $request->session()->get('redirect');

        $user = $this->userService->setSocialUser(
            new UserAuthFields([
                'google_id'            => $googleUser->getId(),
                'avatar_url'           => $googleUser->getAvatar(),
                'email'                => $googleUser->getEmail(),
                'application_language' => $this->guessLanguage($request)
            ])
        );

        Auth::login($user);

        $request->session()->regenerate();

        if (!$redirect) {
            return response()->json($user);
        }

        return redirect($redirect);
    }
}
