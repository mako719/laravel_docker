<?php

namespace App\DataProvider;

use stdClass;

interface UserTokenProviderInterface
{
    /**
     * tokenを引数に利用するユーザー情報取得（データベースを操作するクラスで実装）
     * @param string $token
     * @return stdClass|null
     */
    public function retrieveUserByToken(
        string $token
    ): ?stdClass;
}
