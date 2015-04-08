<?php

namespace Oauth\Model;

class Sessions extends \Model\Oauth {

    protected static $_table_name = 'oauth_sessions';
    protected static $_fields = array(
        'owner_type' => array(
            'data_type' => 'varchar',
            'label' => 'Owner Type',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'owner_id' => array(
            'data_type' => 'varchar',
            'label' => 'Owner ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'client_id' => array(
            'data_type' => 'varchar',
            'label' => 'Client ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'client_redirect_uri' => array(
            'data_type' => 'varchar',
            'label' => 'Access Token',
            'validation' => array(
                'max_length' => 50
            ),
        ),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'accessToken' => array(
                'key_from' => 'id',
                'model_to' => 'Oauth\\Model\\AccessTokens',
                'key_to' => 'session_id',
                'cascade_save' => false,
                'cascade_delete' => true,
            ),
            'authCode' => array(
                'key_from' => 'id',
                'model_to' => 'Oauth\\Model\\AuthCodes',
                'key_to' => 'session_id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        ),
        'has_one' => array(
            'client' => array(
                'key_from' => 'client_id',
                'model_to' => 'Oauth\\Model\\Clients',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        ),
        'many_many' => array(
            'scopes' => array(
                'key_from' => 'id',
                'key_through_from' => 'session_id',
                'table_through' => 'oauth_session_scopes',
                'key_through_to' => 'scope_id',
                'model_to' => 'Oauth\\Model\\Scopes',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        )
    );
}
