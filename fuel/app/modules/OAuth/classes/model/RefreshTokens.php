<?php

namespace OAuth\Model;

class RefreshTokens extends \UNBOXAPI\Canister\Auth {

    protected static $_table_name = 'refresh_tokens';
    protected static $_fields = array(
        'refresh_token' => array(
            'data_type' => 'varchar',
            'label' => 'Refresh Token',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'access_token_id' => array(
            'data_type' => 'varchar',
            'label' => 'Access Token ID',
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
        'has_one' => array(
            'access_token' => array(
                'key_from' => 'access_token_id',
                'model_to' => 'OAuth\\Model\\AccessTokens',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => false,
            )
        )
    );
}
