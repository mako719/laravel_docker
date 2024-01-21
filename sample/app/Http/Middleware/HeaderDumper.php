<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

use function info;
use function strval;

class HeaderDumper
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // リクエストインスタンスがhandleメソッドに渡された場合に、headersプロパティにアクセスしてリクエストヘッダを取得
        // 文字列にキャストしてinfoメソッドでログ書き込みをする
        $this->logger->info(
            'request',
            [
                'header' => strval($request->headers)
            ]
        );

        // ヘルパ関数を利用する場合は以下の通り
        // info('request', ['header] => strval($request->headers));

        $response = $next($request);

        // レスポンスヘッダを$responseから取得し、同様にログを書き込む
        $this->logger->info(
            'response',
            [
                'header' => strval($response->headers)
            ]
        );
        return $response;
    }
}
