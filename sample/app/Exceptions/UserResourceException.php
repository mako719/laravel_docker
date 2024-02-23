<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;
use Throwable;

class UserResourceException extends RuntimeException implements Responsable
{
    public function __construct(
        string $message = '',
        int $code,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'message' => $this->message,
            'path' => $request->getRequestUri(),
            'logref' => 44,
            '_links' => [
                'about' => [
                    'href' => $request->getUri()
                ]
            ],
        ], Response::HTTP_NOT_FOUND, [
            'content-type' => 'application/vnd.error+json'
        ]);
    }

    // この例外クラスをスローすると、次のレスポンスが返却される
    // {
    //     "message": "resource not found",
    //     "path": "/home",
    //     "logref": 44,
    //     "_links": {
    //         "about": {
    //             "href": "http://localhost/home"
    //         }
    //     }
    // }
}