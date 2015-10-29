<?php

namespace Packages\Model;

class Apis extends \UNBOXAPI\Canister\Relationship{

    protected static $_table_name = 'packages_apis';
    protected static $_fields = array(
        'package_id' => array(
            'data_type' => 'varchar',
            'label' => 'Package ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'api_id' => array(
            'data_type' => 'varchar',
            'label' => 'API ID',
            'null' => false,
            'validation' => array(
                'required',
                'max_length' => 50
            ),
        ),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'package' => array(
                'key_from' => 'package_id',
                'model_to' => 'Applications\\Model\\Applications',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'api' => array(
                'key_from' => 'api_id',
                'model_to' => 'Apis\\Model\\Apis',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
    );

}