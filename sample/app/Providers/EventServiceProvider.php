<?php

namespace App\Providers;

use App\Events\PublishProcessor;
use App\Listeners\MessageQueueSubscriber;
use App\Listeners\MessageSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            // SendEmailVerificationNotification::class,
            'App\Listeners\RegisteredListener',
        ],
        // デフォルトで用意されているlistenプロパティでイベントの登録をする場合
        PublishProcessor::class => [
            MessageSubscriber::class,
        MessageQueueSubscriber::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
        // Facadeを使用した例
        // Event::listen(
        //     PublishProcessor::class,
        //     MessageSubscriber::class,
        // );

        // フレームワークのDIコンテナにアクセスした場合
        // $this->app['events']->listen(
        //     PublishProcessor::class,
        //     MessageSubscriber::class
        // );
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
