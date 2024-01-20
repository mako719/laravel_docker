<?php

namespace App\Providers;

use App\Domain\Repository\PublisherRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\DataProvider\PublisherRepositoryInterface::class,
            \App\Domain\Repository\PublisherRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
