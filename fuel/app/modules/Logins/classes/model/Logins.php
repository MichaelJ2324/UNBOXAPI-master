<?php

namespace Logins\Model;

class Logins extends \UNBOXAPI\Canister\Module{

    protected static $_table_name = 'logins';
    protected static $_fields = array(
        'login_entrypoint_id' => array(
            'data_type' => 'varchar',
            'label' => 'Login Entry Point',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Entrypoints'
            ),
        ),
        'logout_entrypoint_id' => array(
            'data_type' => 'varchar',
            'label' => 'Logout Entry Point',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Entrypoints'
            ),
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'type' => 'checkbox'
            ),
        ),
    );
    protected static $_relationships = array(
        'has_one' => array(
            'login_entrypoint' => array(
                'key_from' => 'login_entrypoint_id',
                'model_to' => 'Entrypoints\\Model\\Entrypoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'logout_entrypoint' => array(
                'key_from' => 'logout_entrypoint_id',
                'model_to' => 'Entrypoints\\Model\\Entrypoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'has_many' => array(
            'apis' => array(
                'key_from' => 'id',
                'model_to' => 'Apis\\Model\\Logins',
                'key_to' => 'login_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'settings' => array(
                'key_from' => 'id',
                'model_to' => 'Logins\\Model\\Settings',
                'key_to' => 'login_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        )
    );
} 