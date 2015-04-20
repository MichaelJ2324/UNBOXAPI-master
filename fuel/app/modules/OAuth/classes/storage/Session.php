<?php

namespace Oauth\Storage;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\SessionInterface;

class Session extends AbstractStorage implements SessionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        \Log::debug($accessToken->getId());
        $AccessToken = \Oauth\Model\AccessTokens::query()->where('access_token',$accessToken->getId())->get_one();
        \Log::debug(serialize($AccessToken));
        $session = \Oauth\Model\Sessions::find($AccessToken->session_id);
        if (count($session) === 1) {
            $Session = new SessionEntity($this->server);
            $Session->setId($session->id);
            $Session->setOwner($session->owner_type, $session->owner_id);

            return $Session;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        $session = \Oauth\Model\Sessions::find('first',
            array(
                'related' => array(
                    'authCode' => array(
                        'where'=> array(
                            array('auth_code',$authCode->getId())
                        )
                    )
                )
            )
        );
        if (count($session) === 1) {
            $Session = new SessionEntity($this->server);
            $Session->setId($session->id);
            $Session->setOwner($session->owner_type, $session->owner_id);

            return $Session;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(SessionEntity $session)
    {
        $Scopes = array();
        $session = \Oauth\Model\Sessions::query()->where('id',$session->getId())->related('scopes')->get_one();
        if (isset($session)&&$session!==null) {
            if (property_exists($session, "scopes")) {
                $scopes = $session->scopes;
                foreach ($scopes as $scope) {
                    $Scopes[] = (new ScopeEntity($this->server))->hydrate([
                        'id' => $scope->id,
                        'description' => $scope->description,
                    ]);
                }
            }
        }
        return $Scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        $session = \Oauth\Model\Sessions::forge(
            array(
                'owner_type'  =>    $ownerType,
                'owner_id'    =>    $ownerId,
                'client_id'   =>    $clientId,
            )
        );
        $session->save();
        return $session->id;
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $session = \Oauth\Model\Sessions::find($session->getId());
        $session->scopes[] = \Oauth\Model\Scopes::find($scope->getId());
        $session->save();
    }
}
