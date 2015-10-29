<?php

namespace OAuth\Model;

class AccessTokens extends \UNBOXAPI\Canister\Auth {

    protected static $_table_name = 'access_tokens';
    protected static $_fields = array(
        'access_token' => array(
            'data_type' => 'varchar',
            'label' => 'Access Token',
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
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'refreshToken' => array(
                'key_from' => 'id',
                'model_to' => 'OAuth\\Model\\RefreshTokens',
                'key_to' => 'access_token_id',
                'cascade_save' => false,
                'cascade_delete' => false,
            )
        ),
        'has_one' => array(
            'session' => array(
                'key_from' => 'session_id',
                'model_to' => 'OAuth\\Model\\Sessions',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => false,
            ),
        ),
        'many_many' => array(
            'scopes' => array(
                'key_from' => 'id',
                'key_through_from' => 'access_token_id',
                'table_through' => 'access_token_scopes',
                'key_through_to' => 'scope_id',
                'model_to' => 'OAuth\\Model\\Scopes',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => false,
            )
        )
    );

}
