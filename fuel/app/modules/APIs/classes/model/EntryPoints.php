<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/4/15
 * Time: 9:29 PM
 */

namespace Apis\Model;


class Entrypoints extends \Model\Relationship {

    protected static $_table_name = 'api_entrypoints';
    protected static $_fields = array(
        'api_id' => array(
            'data_type' => 'varchar',
            'label' => 'Entrypoint ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'entrypoint_id' => array(
            'data_type' => 'varchar',
            'label' => 'Parameter ID',
            'null' => false,
            'validation' => array(
                'required',
                'max_length' => 50
            ),
        ),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'api' => array(
                'key_from' => 'api_id',
                'model_to' => 'Apis\\Model\\Apis',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'entrypoint' => array(
                'key_from' => 'entrypoint_id',
                'model_to' => 'Entrypoints\\Model\\Entrypoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
    );

}