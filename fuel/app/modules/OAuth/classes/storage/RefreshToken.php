<?php

namespace Oauth\Storage;

use OAuth2\Server\Entity\RefreshTokenEntity;
use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Storage\AbstractStorage;
use OAuth2\Server\Storage\RefreshTokenInterface;

class RefreshToken extends AbstractStorage implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($token)
    {
		$RefreshToken = \OAuth\Model\RefreshTokens::query()->where('refresh_token',$token)->related('access_token')->get_one();
        if (count($RefreshToken) === 1) {
            $token = new RefreshTokenEntity($this->server);
			$token->setId($RefreshToken->refresh_token)
				  ->setExpireTime($RefreshToken->expire_time)
				  ->setAccessTokenId($RefreshToken->access_token->access_token);
            return $token;
        }

        return;
    }

	public function getByAccessToken(AccessTokenEntity $token){
		$RefreshToken=null;
		$AccessToken = \OAuth\Model\AccessTokens::query()->where('access_token',$token->getId())->get_one();
		$RefreshToken = \OAuth\Model\RefreshTokens::query()->where('access_token_id',$AccessToken->id)->get_one();
		if (count($RefreshToken) === 1) {
			$token = new RefreshTokenEntity($this->server);
			$token->setId($RefreshToken->refresh_token)
				  ->setExpireTime($RefreshToken->expire_time)
				  ->setAccessTokenId($token->getId());
			return $token;
		}

		return;
	}

    /**
     * {@inheritdoc}
     */
    public function create($token, $expireTime, $accessToken)
    {
        $AccessToken = \OAuth\Model\AccessTokens::query()->where('access_token',$accessToken)->get_one();

		$RefreshToken = \OAuth\Model\RefreshTokens::forge(
            array(
                'refresh_token'     =>  $token,
                'access_token_id'    =>  $AccessToken->id,
                'expire_time'   =>  $expireTime,
            )
        );
		$RefreshToken->save();
    }

	/**
	 * {@inheritdoc}
	 */
	public function save(RefreshTokenEntity $token)
	{
		$RefreshToken = \OAuth\Model\RefreshTokens::query()->where('refresh_token',$token->getId())->related('access_token')->get_one();
		$AccessToken = \OAuth\Model\AccessTokens::query()->where('access_token',$token->getAccessToken()->getId())->get_one();

		$RefreshToken->access_token_id = $AccessToken->id;
		$RefreshToken->expire_time = $token->getExpireTime();
		$RefreshToken->save();
	}

    /**
     * {@inheritdoc}
     */
    public function delete(RefreshTokenEntity $token)
    {
		$RefreshToken = \OAuth\Model\RefreshTokens::query()->where('refresh_token',$token->getId())->get_one();
		$RefreshToken->deleted = 1;
		$RefreshToken->save();
    }
}
