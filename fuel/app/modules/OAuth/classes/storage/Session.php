<?php

namespace OAuth\Storage;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Entity\AuthCodeEntity;
use OAuth2\Server\Entity\ScopeEntity;
use OAuth2\Server\Entity\SessionEntity;
use OAuth2\Server\Storage\AbstractStorage;
use OAuth2\Server\Storage\SessionInterface;

class Session extends AbstractStorage implements SessionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        $AccessToken = \OAuth\Model\AccessTokens::query()->where('access_token',$accessToken->getId())->get_one();
        $session = \OAuth\Model\Sessions::find($AccessToken->session_id);
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
        $session = \OAuth\Model\Sessions::find('first',
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
        $session = \OAuth\Model\Sessions::find($session->getId(),array('related'=>array('scopes')));
        if (isset($session)) {
			$scopes = $session->scopes;
			foreach ($scopes as $scope) {
				$scope = new ScopeEntity($this->server);
                $scope->hydrate(
                    array(
                        'id' => $scope->scope,
                        'description' => $scope->description,
                    )
				);
                $Scopes[] = $scope;
                unset($scope);
			}
        }
        return $Scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
		$Client = \OAuth\Model\Clients::find('first',array(
			'where' => array(
				array('client_id',$clientId)
			)
		));
        $session = \OAuth\Model\Sessions::forge(
            array(
                'owner_type'  =>    $ownerType,
                'owner_id'    =>    $ownerId,
                'client_id'   =>    $Client->id,
				'client_redirect_uri' => $clientRedirectUri
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
        $session = \OAuth\Model\Sessions::find($session->getId(),array('related' => array('scopes')));
        $session->scopes[] = \OAuth\Model\Scopes::query()->where('scope',$scope->getId())->get_one();
        $session->save();
    }

	public function delete($sessionID){
		$session = \OAuth\Model\Sessions::find($sessionID,array('related' => array('scopes')));
		$session->scopes = null;
		$session->deleted = 1;
		$session->save();
	}
}
