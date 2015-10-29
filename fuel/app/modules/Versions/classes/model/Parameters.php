<?php

namespace Versions\Model;

class Parameters extends \UNBOXAPI\Canister\Versions {

    protected static $_table_name = 'parameter_versions';

    protected static $_relationships = array(
        'belongs_to' => array(
            'application' => array(
                'key_from' => 'id',
                'model_to' => 'Parameters\\Model\\Parameters',
                'key_to' => 'version_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),
        'has_one' => array(
            'related_module' => array(
                'key_from' => 'related_id',
                'model_to' => 'Parameters\\Model\\Parameters',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        )
    );

}