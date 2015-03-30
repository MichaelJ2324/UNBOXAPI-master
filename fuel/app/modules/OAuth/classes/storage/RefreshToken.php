<?php

namespace Oauth\Storage;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

class RefreshToken extends AbstractStorage implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($token)
    {
        $refresh_token = \Oauth\Model\RefreshTokens::find($token);

        if (count($refresh_token) === 1) {
            $token = (new RefreshTokenEntity($this->server))
                        ->setId($refresh_token->refresh_token)
                        ->setExpireTime($refresh_token->expire_time)
                        ->setAccessTokenId($refresh_token->access_token);

            return $token;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function create($token, $expireTime, $accessToken)
    {
        $refresh_token = \Oauth\Model\RefreshTokens::forge(
            array(
                'refresh_token'     =>  $token,
                'access_token'    =>  $accessToken,
                'expire_time'   =>  $expireTime,
            )
        );
        $refresh_token->save();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RefreshTokenEntity $token)
    {
        $refresh_token = \Oauth\Model\RefreshTokens::find($token->getId());
        $refresh_token->delete();
    }
}
