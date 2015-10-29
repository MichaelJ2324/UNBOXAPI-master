<?php

namespace OAuth\Model;

class Scopes extends \UNBOXAPI\Canister\Auth {

    protected static $_table_name = 'scopes';
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
                'table_through' => 'access_token_scopes',
                'key_through_to' => 'access_token_id',
                'model_to' => 'OAuth\\Model\\AccessTokens',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => false,
            ),
            'authCodes' => array(
                'key_from' => 'id',
                'key_through_from' => 'scope_id',
                'table_through' => 'auth_code_scopes',
                'key_through_to' => 'auth_code_id',
                'model_to' => 'OAuth\\Model\\AuthCodes',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => false,
            ),
            'sessions' => array(
                'key_from' => 'id',
                'key_through_from' => 'scope_id',
                'table_through' => 'session_scopes',
                'key_through_to' => 'session_id',
                'model_to' => 'OAuth\\Model\\Sessions',
                'key_to' => 'id',
                'cascade_save' => false,
                'cascade_delete' => false,
            ),
        )
    );
}
