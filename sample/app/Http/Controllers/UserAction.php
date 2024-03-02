<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class userAction extends Controller
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    public function __invoke(Request $request)
    {
        // 認証したユーザー情報へのアクセス
        // Authファサードを利用してもOK
        $user = $this->authManager->guard('api')->user();

        return new JsonResponse([
            'id' => $user->getAuthIdentifier(),
            'name' => $user->getName()
        ]);
    }
}
