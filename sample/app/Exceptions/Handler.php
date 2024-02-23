<?php

namespace App\Exceptions;

use Fluent\Logger\FluentLogger;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $exception)
    {
        // Illemniate\Foundation\Exceptions\Handlerクラスのreportメソッドを実行
        Parent::report($exception);
        $fluentLogger = $this->container->make(FluentLogger::class);
        $fluentLogger->post('report', ['error' => $exception->getMessage()]);
    }

    public function render($request, Throwable $exception)
    {
        // 送出されたExceptionクラスを継承したインスタンスのうち特定の例外のみ処理を変更
        if ($exception instanceof QueryException) {
            // カスタムヘッダを利用してエラーレスポンス、ステータスコード500を返却
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR, [
                'X-App-Message' => 'An error occurred.'
            ]);
        }

        return parent::render($request, $exception);
    }
}
