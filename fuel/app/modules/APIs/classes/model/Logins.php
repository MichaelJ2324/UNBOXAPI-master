<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/4/15
 * Time: 9:17 PM
 */

namespace Apis\Model;


class Logins extends \Model\Relationship {

    protected static $_table_name = "api_logins";
    protected static $_fields = array(
        'api_id' => array(
            'data_type' => 'varchar',
            'label' => 'API ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'login_id' => array(
            'data_type' => 'varchar',
            'label' => 'Login ID',
            'null' => false,
            'validation' => array(
                'required' => true,
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
            'login' => array(
                'key_from' => 'login_id',
                'model_to' => 'Logins\\Model\\Logins',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
    );
}