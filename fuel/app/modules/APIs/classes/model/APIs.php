<?php

namespace Apis\Model;

class Apis extends \UNBOXAPI\Canister\Module {

    protected static $_table_name = 'apis';
    protected static $_fields = array(
        'url' => array(
            'data_type' => 'varchar',
            'label' => 'URL',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 250
            ),
            'form' => array('type' => 'text'),
        ),
        'login_required' => array(
            'data_type' => 'tinyint',
            'label' => 'Login Required?',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 1
            ),
            'form' => array('type' => 'checkbox'),
        ),
        'type' => array(
            'data_type' => 'varchar',
            'label' => 'Type',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 10
            ),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    0 => array(
                        'key'=>'REST',
                        'value' => 'REST'
                    ),
                    1 => array(
                        'key'=>'SOAP',
                        'value'=> 'SOAP'
                    )
                )
            )
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'default' => 0,
            'validation' => array(
                'max_length' => 0
            ),
            'form' => array(
                'type' => 'checkbox',
                'disabled' => 'disabled'
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
    );
    protected static $_relationships = array(
        'has_one' => array(
            'version' => array(
                'key_from' => 'version_id',
                'model_to' => "Versions\\Model\\Applications",
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),
        'has_many' => array(
            'applications' => array(
                'key_from' => 'id',
                'model_to' => 'Applications\\Model\\Apis',
                'key_to' => 'api_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'entrypoints' => array(
                'key_from' => 'id',
                'model_to' => 'Apis\\Model\\Entrypoints',
                'key_to' => 'api_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'logins' => array(
                'key_from' => 'id',
                'model_to' => 'Apis\\Model\\Logins',
                'key_to' => 'api_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        )
    );
} 