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
        $refresh_token = \Oauth\Model\RefreshTokens::query()->where('refresh_token',$token)->related(array('accessToken'))->get_one();
        $AccessToken = null;
        \Log::debug(serialize($refresh_token));
        if (isset($refresh_token->accessToken)) {
            $AccessToken = $refresh_token->accessToken;
        }
        if (count($refresh_token) === 1 && $AccessToken!==null) {
            $token = (new RefreshTokenEntity($this->server))
                        ->setId($refresh_token->refresh_token)
                        ->setExpireTime($refresh_token->expire_time)
                        ->setAccessTokenId($AccessToken->access_token);
            return $token;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function create($token, $expireTime, $accessToken)
    {
        $AccessToken = \Oauth\Model\AccessTokens::query()->where('access_token',$accessToken)->get_one();

        $refresh_token = \Oauth\Model\RefreshTokens::forge(
            array(
                'refresh_token'     =>  $token,
                'access_token_id'    =>  $AccessToken->id,
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
        $refresh_token = \Oauth\Model\RefreshTokens::query()->where('refresh_token',$token->getId())->get_one();
        $refresh_token->delete();
    }
}
