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
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GithubLoginController extends Controller
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
     * Redirect the user to the GitHub authentication page.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectToGithub(Request $request): RedirectResponse
    {
        $request->session()->put('redirect', $request->get('redirect'));
        return Socialite::driver('github')->redirect();
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function handleGithubCallback(Request $request): JsonResponse|RedirectResponse
    {
        $hackRedirect = $this->localhostHack($request, 'github');

        if ($hackRedirect) {
            return $hackRedirect;
        }

        $authUser = $request->user();

        $githubUser = Socialite::driver('github')->user();

        $redirect = $request->session()->get('redirect');

        $user = $this->userService->setSocialUser(
            new UserAuthFields([
                'github_id'            => (string) $githubUser->getId(),
                'github_nickname'      => $githubUser->getNickname(),
                'email'                => $githubUser->getEmail(),
                'avatar_url'           => $githubUser->getAvatar(),
                'github_token'         => $githubUser->token,
                'application_language' => $this->guessLanguage($request)
            ]),
            $authUser,
        );

        if (!$user && $authUser) {
            return redirect("$redirect?error=github");
        }

        if (!$authUser) {
            Auth::login($user);
        }


        $request->session()->regenerate();

        if (!$redirect) {
            return response()->json($user);
        }

        return redirect($redirect);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function invalidateGithub(Request $request): JsonResponse
    {
        $this->userService->invalidateGithubToken($request->user());

        return $this->jsonResponse();
    }
}
