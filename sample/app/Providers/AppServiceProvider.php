<?php

namespace App\Providers;

use App\BlowfishEncrypter;
use App\Domain\Repository\PublisherRepository;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->app->singleton(
            'encrypter',
            function ($app) {
                $config = $app->make('config')->get('app');

                return new Blowfishencrypter($this->parseKey($config));
            }
        );
    }

    protected function parseKey(array $config)
    {
        if (Str::startsWith($key = $this->key($config), $prefix = 'base64:')) {
            $key = base64_decode((Str::after($key, $prefix)));
        }

        return $key;
    }

    protected function key(array $config)
    {
        return tap(
            $config['key'],
            function ($key) {
                if (empty($key)) {
                    throw new MissingAppKeyException;
                }
            }
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
