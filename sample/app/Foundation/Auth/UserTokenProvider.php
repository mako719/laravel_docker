<?php

namespace App\Foundation\Auth;

use App\DataProvider\UserTokenProviderInterface;
use App\Entity\user;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

use function is_null;

final class UserTokenProvider implements UserProvider
{
    private $provider;

    public function __construct(UserTokenProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function retrieveById($identifier)
    {
        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {

    }

    public function retrieveByCredentials(array $credentials)
    {
        if (!isset($credentials['api_token'])) {
            return null;
        }

        $result = $this->provider->retrieveUserByToken($credentials['api_token']);
        if (is_null($result)) {
            return null;
        }

        return new User(
            $result->user_id,
            $result->api_token,
            $result->name,
            $result->email
        );
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return false;
    }
}
