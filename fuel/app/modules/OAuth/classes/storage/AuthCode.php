<?php

namespace Oauth\Storage;

use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AuthCodeInterface;

class AuthCode extends AbstractStorage implements AuthCodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($code)
    {
        $auth_code = \Oauth\Model\AuthCodes::find($code,array('where' => array('expire_time','>=',time())));

        if (count($auth_code) === 1) {
            $token = new AuthCodeEntity($this->server);
            $token->setId($auth_code->auth_code);
            $token->setRedirectUri($auth_code->client_redirect_uri);
            $token->setExpireTime($auth_code->expire_time);

            return $token;
        }

        return;
    }

    public function create($token, $expireTime, $sessionId, $redirectUri)
    {
        $authCode = \Oauth\Model\AuthCodes::forge(
            array(
                'auth_code'     =>  $token,
                'client_redirect_uri'  =>  $redirectUri,
                'session_id'    =>  $sessionId,
                'expire_time'   =>  $expireTime,
            )
        );
        $authCode->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(AuthCodeEntity $token)
    {
        $auth_code = \Oauth\Model\AuthCodes::find($token->getId(),array('related',array('scopes')));
        $scopes = $auth_code->scopes;

        if (count($scopes) > 0) {
            foreach ($scopes as $scope) {
                $Scope = (new ScopeEntity($this->server))->hydrate([
                    'id'            =>  $scope->id,
                    'description'   =>  $scope->description,
                ]);
                $response[] = $Scope;
            }
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        $auth_code = \Oauth\Model\AuthCodes::find($token->getId());
        $auth_code->scopes()->attach($scope->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthCodeEntity $token)
    {
        \Oauth\Model\AuthCodes::find($token->getId())->delete();
    }
}
