<?php

namespace App\Providers;

use App\DataProvider\UserToken;
use App\Foundation\Auth\CacheUserProvider;
use App\Foundation\Auth\UserTokenProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // キャッシュ併用認証（6-1）
        // $this->app->make('auth')->provider(
        //     'cache_eloquent',
        //     function (Application $app, array $config) {
        //         return new CacheUserProvider(
        //             $app->make('hash'),
        //             $config['model'],
        //             $app->make('cache')->driver()
        //         );
        //     }
        // );

        $this->app->make('auth')->provider(
            'user_token',
            function (Application $app, array $config) {
                return new UserTokenProvider(new UserToken($app->make('db')));
            }
        );
    }
}
