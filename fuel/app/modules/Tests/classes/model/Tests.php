<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 9:03 AM
 */

namespace Tests\Model;


class Tests extends \Model\Module{

    protected static $_table_name = 'tests';
    protected static $_fields = array(
        'application_id' => array(
            'data_type' => 'varchar',
            'label' => 'Application',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Applications'
            ),
        ),
        'api_id' => array(
            'data_type' => 'varchar',
            'label' => 'Api',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Apis',
                'dependent' => 'Applications'
            ),
        ),
        'entrypoint_id' => array(
            'data_type' => 'varchar',
            'label' => 'Entrypoint',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Entrypoints',
                'dependent' => 'Apis'
            ),
        ),
        'login_id' => array(
            'data_type' => 'varchar',
            'label' => 'Login',
            'validation' => array(
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Logins',
                'dependent' => 'Apis'
            ),
        ),
        'saved_login' => array(
            'data_type' => 'tinyint',
            'label' => 'Save Login Info?',
            'validation' => array(
                'max_length' => 1
            ),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    array(
                        'key' => 0,
                        'value' => "No"
                    ),
                    array(
                        'key' => 1,
                        'value' => "Yes"
                    ),
                    array(
                        'key' => 2,
                        'value' => "API Login"
                    ),
                ),
                'help' => 'Yes - saves login creds for this Test. API Login, uses settings defined for the entire API.'
            ),
        ),
        'web_address' => array(
            'data_type' => 'varchar',
            'label' => 'Web Address',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
            'form' => array(
                'type' => 'text',
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
            'entrypoint' => array(
                'key_from' => 'entrypoint_id',
                'model_to' => 'Entrypoints\\Model\\Entrypoints',
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
        'has_many' => array(
            'settings' => array(
                'key_from' => 'id',
                'model_to' => 'Tests\\Model\\Settings',
                'key_to' => 'test_id',
                'cascade_save' => true,
                'cascade_delete' => true,
            )
        )
    );
} 