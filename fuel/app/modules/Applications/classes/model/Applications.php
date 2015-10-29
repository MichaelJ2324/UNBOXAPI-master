<?php

namespace Applications\Model;

class Applications extends \UNBOXAPI\Canister\Module {

    protected static $_table_name = 'applications';
    protected static $_fields = array(
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'validation' => array(
                'max_length' => 2048
            ),
            'form' => array(
                'type' => 'textarea'
            )
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
            'apis' => array(
                'key_from' => 'id',
                'model_to' => "Applications\\Model\\Apis",
                'key_to' => 'application_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),

    );
} 