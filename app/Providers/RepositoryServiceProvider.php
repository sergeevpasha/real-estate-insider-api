<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Passkey;
use App\Models\User;
use App\Repositories\Contracts\PasskeyRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\Eloquent\PasskeyRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            UserRepositoryContract::class,
            fn() => new UserRepository(new User())
        );
        $this->app->singleton(
            PasskeyRepositoryContract::class,
            fn() => new PasskeyRepository(new Passkey())
        );
    }
}
