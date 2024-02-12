<?php

namespace App\Providers;

use App\BlowfishEncrypter;
use App\DataProvider\Database\RegisterReviewDataProvider;
use App\DataProvider\RegisterReviewProviderInterface;
use App\Domain\Repository\PublisherRepository;
use App\Foundation\ElasticsearchClient;
use Knp\Snappy\Pdf;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Foundation\Application;
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

        // メソッドインジェクション、コンストラクタインジェクションで
        // 利用するクラスにオブジェクトが渡される。
        $this->app->bind(Pdf::class, function () {
            return new Pdf('/usr/local/bin/wkhtmltopdf');
        });

        $this->app->bind(RegisterReviewProviderInterface::class, function () {
            return new RegisterReviewDataProvider();
        });

        $this->app->singleton(ElasticsearchClient::class, function (Application $app) {
            $config = $app['config']->get('elasticsearch');
            return new ElasticsearchClient($config['hosts']);
        });
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
