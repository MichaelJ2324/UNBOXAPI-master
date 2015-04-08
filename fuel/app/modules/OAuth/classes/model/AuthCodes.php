<?php

namespace Oauth\Model;

class AuthCodes extends \Model\Oauth {

    protected static $_table_name = 'oauth_auth_codes';
    protected static $_fields = array(
        'auth_code' => array(
            'data_type' => 'varchar',
            'label' => 'Authorization Code',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'session_id' => array(
            'data_type' => 'varchar',
            'label' => 'Session ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'expire_time' => array(
            'data_type' => 'int',
            'label' => 'Expire Time',
            'null' => false,
            'unsigned' => true,
            'validation' => array(
                'required' => true,
                'max_length' => 11
            ),
        ),
        'client_redirect_uri' => array(
            'data_type' => 'varchar',
            'label' => 'Redirect URI ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
    );
    protected static $_relationships = array(
        'has_one' => array(
            'session' => array(
                'key_from' => 'session_id',
                'model_to' => 'Oauth\\Model\\Sessions',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        ),
        'many_many' => array(
            'scopes' => array(
                'key_from' => 'id',
                'key_through_from' => 'auth_code_id',
                'table_through' => 'oauth_auth_code_scopes',
                'key_through_to' => 'scope_id',
                'model_to' => 'Oauth\\Model\\Scopes',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        )
    );
}

?>
