<?php

namespace Entrypoints\Model;

class Entrypoints extends \UNBOXAPI\Canister\Module {

    protected static $_table_name = 'entry_points';
    protected static $_fields = array(
        'method' => array(
            'data_type' => 'tinyint',
            'label' => 'HTTP Method ID',
            'null' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'relate',
                'name' => 'method',
                'module' => 'HttpMethods'
            ),
            'filter' => true
        ),
        'url' => array(
            'data_type' => 'varchar',
            'label' => 'URL',
            'null' => false,
            'validation' => array(
                'required',
                'max_length' => 250
            ),
            'form' => array(
                'type' => 'text',
                'name' => 'url',
            ),
        ),
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'null' => true,
            'validation' => array(
                'max_length' => 2048
            ),
            'form' => array(
                'type' => 'textarea'
            ),
        ),
        'version_id' => array(
            'data_type' => 'varchar',
            'label' => 'Version ID',
            'validation' => array(
                'max_length' => 50
            ),
            'form' => false,
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'default' => 0,
            'validation' => array(
                'max_length' => 0
            ),
            'form' => false,
        ),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'login_entrypoint' => array(
                'key_from' => 'id',
                'model_to' => 'Logins\\Model\\Logins',
                'key_to' => 'login_entrypoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'logout_entrypoint' => array(
                'key_from' => 'id',
                'model_to' => 'Logins\\Model\\Logins',
                'key_to' => 'logout_entrypoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'has_one' => array(
            'httpMethod' => array(
                'key_from' => 'method',
                'model_to' => 'HttpMethods\\Model\\HttpMethods',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'version' => array(
                'key_from' => 'version_id',
                'model_to' => 'Versions\\Model\\Entrypoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'has_many' => array(
            'parameters' => array(
                'key_from' => 'id',
                'model_to' => 'Entrypoints\\Model\\Parameters',
                'key_to' => 'entrypoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'apis' => array(
                'key_from' => 'id',
                'model_to' => 'Apis\\Model\\Entrypoints',
                'key_to' => 'entrypoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),
    );

} 