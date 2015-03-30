<?php

namespace Oauth\Storage;

use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ClientInterface;

class Client extends AbstractStorage implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $query = \Oauth\Model\Clients::query()->where('client_id','=',$clientId);

        if ($clientSecret !== null) {
            $query->where('secret','=',$clientSecret);
        }

        if ($redirectUri !== null) {
            $query->related('redirect_uri')->where('redirect_uri.redirect_uri', $redirectUri);
        }
        $client = $query->get_one();
        if (count($client) === 1) {
            $Client = new ClientEntity($this->server);
            $Client->hydrate(
                array(
                    'id'    =>  $client->client_id,
                    'name'  =>  $client->name,
                )
            );

            return $Client;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySession(SessionEntity $session)
    {
        $client = \Oauth\Model\Clients::find('first',
            array(
                'related' => array(
                    'session' => array(
                        'where'=> array(
                            'id',$session->getId()
                        )
                    )
                )
            )
        );

        if (count($client) === 1) {
            $Client = new ClientEntity($this->server);
            $Client->hydrate([
                'id'    =>  $client->id,
                'name'  =>  $client->name,
            ]);

            return $Client;
        }

        return;
    }
}
