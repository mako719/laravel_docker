<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use RuntimeException;
use Throwable;

class AppException extends RuntimeException implements Responsable
{
    // 特定の例外とレスポンス（Blade）を紐づける

    protected $error = 'error';

    private $factory;

    public function __construct(
        View $factory,
        string $message,
        int $code = 0,
        Throwable $previous = null
    )
    {
        $this->factory = $factory;
        parent::__construct($message, $code, $previous);
    }

    public function toResponse($request): Response
    {
        return new Response(
            $this->factory->with($this->error, $this->message)
        );
    }

    // 呼び出し側は以下のように実装する
    // 第一引数で指定したテンプレートに、第二引数で指定したメッセージが渡される

    // public function index()
    // {
    //     throw new \App\Exceptions\AppException(view('errors.page'), 'error message');
    //     -[略]-
    // }
}
