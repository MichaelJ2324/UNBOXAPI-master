<?php

namespace Oauth\Model;

class Scopes extends \Model\Oauth {

    protected static $_table_name = 'oauth_scopes';
    protected static $_fields = array(
        'scope' => array(
            'data_type' => 'varchar',
            'label' => 'Scope',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
    );
    protected static $_relationships = array(
        'many_many' => array(
            'accessTokens' => array(
                'key_from' => 'id',
                'key_through_from' => 'scope_id',
                'table_through' => 'oauth_access_token_scopes',
                'key_through_to' => 'access_token_id',
                'model_to' => 'Oauth\\Model\\AccessTokens',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            ),
            'authCodes' => array(
                'key_from' => 'id',
                'key_through_from' => 'scope_id',
                'table_through' => 'oauth_auth_code_scopes',
                'key_through_to' => 'auth_code_id',
                'model_to' => 'Oauth\\Model\\AuthCodes',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            ),
            'sessions' => array(
                'key_from' => 'id',
                'key_through_from' => 'scope_id',
                'table_through' => 'oauth_session_scopes',
                'key_through_to' => 'session_id',
                'model_to' => 'Oauth\\Model\\Sessions',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        )
    );
}
