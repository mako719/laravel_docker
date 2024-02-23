<?php

namespace App\Providers;

use App\Console\Commands\SendOrdersCommand;
use App\Service\ExportOrdersService;
use App\UseCases\SendOrdersUseCase;
use Carbon\Laravel\ServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Log\LogManager;

class BatchServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        // SendOrdersCommandクラスの生成方法をバインド
        $this->app->bind(
            SendOrdersCommand::class,
            function () {
                $useCase = app(SendOrdersUseCase::class);
                // send-ordersチャネルを利用するように変更
                /** @var LogManager $logger */
                $logger = app(LogManager::class);
                return new SendOrdersCommand($useCase, $logger->channel('send-orders'));
            }
        );

        $this->app->bind(SendOrdersUseCase::class, function () {
            $service = $this->app->make(ExportOrdersService::class);
            // Guzzleにログ用ミドルウェアを追加
            $guzzle = new Client(
                [
                    'handler' => tap(
                        HandlerStack::create(),
                        function (HandlerStack $v) {
                            $logger = app(LogManager::class);
                            $v->push(
                                Middleware::log(
                                    $logger->driver('send-orders'),
                                    new MessageFormatter(
                                        ">>>\n{req_headers}\n<<<\n{res_headers}\n\n{res_body}"
                                    )
                                )
                            );
                        }
                    )
                ]
            );

            return new SendOrdersUseCase($service, $guzzle);
        });
    }
}
