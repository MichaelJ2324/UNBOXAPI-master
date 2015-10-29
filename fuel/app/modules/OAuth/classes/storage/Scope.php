<?php

namespace Oauth\Storage;

use OAuth2\Server\Entity\ScopeEntity;
use OAuth2\Server\Storage\AbstractStorage;
use OAuth2\Server\Storage\ScopeInterface;

class Scope extends AbstractStorage implements ScopeInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($scope, $grantType = null, $clientId = null)
    {
        $scope = \OAuth\Model\Scopes::query()->where('scope',$scope)->get_one();

        if (count($scope) === 0) {
            return;
        }

        $Scope = new ScopeEntity($this->server);
        $Scope->hydrate(
            array(
                'id'            =>  $scope->scope,
                'description'   =>  $scope->description,
            )
        );
        return $Scope;
    }
}
