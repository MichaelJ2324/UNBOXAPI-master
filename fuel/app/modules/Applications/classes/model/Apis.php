<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/4/15
 * Time: 9:27 PM
 */

namespace Applications\Model;


class Apis extends \Model\Relationship{

    protected static $_table_name = 'application_apis';
    protected static $_fields = array(
        'application_id' => array(
            'data_type' => 'varchar',
            'label' => 'Application ID',
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
            'application' => array(
                'key_from' => 'application_id',
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