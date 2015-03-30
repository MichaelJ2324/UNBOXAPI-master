<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/8/15
 * Time: 3:03 PM
 */

namespace Users\Model;


class Users extends \Model\Oauth{

    protected static $_table_name = 'users';
    protected static $_fields = array(
        'first_name' => array(
            'data_type' => 'varchar',
            'label' => 'First Name',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array('type' => 'text'),
        ),
        'last_name' => array(
            'data_type' => 'varchar',
            'label' => 'Last Name',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array('type' => 'text'),
        ),
        'username' => array(
            'data_type' => 'varchar',
            'label' => 'Username',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array('type' => 'text'),
        ),
        'password' => array(
            'data_type' => 'varchar',
            'label' => 'Password',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 100
            ),
            'form' => array('type' => 'password'),
        ),
        'email' => array(
            'data_type' => 'varchar',
            'label' => 'email',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 75
            ),
            'form' => array('type' => 'text'),
        ),
    );
    protected static $_relationships = array(
        'has_many' => array(
            'created_applications' => array(
                'key_from' => 'id',
                'model_to' => '\\Applications\\Model\\Applications',
                'key_to' => 'created_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'modified_applications' => array(
                'key_from' => 'id',
                'model_to' => '\\Applications\\Model\\Applications',
                'key_to' => 'modified_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'created_apis' => array(
                'key_from' => 'id',
                'model_to' => '\\Apis\\Model\\Apis',
                'key_to' => 'created_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'modified_apis' => array(
                'key_from' => 'id',
                'model_to' => '\\Apis\\Model\\Apis',
                'key_to' => 'modified_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'created_entrypoints' => array(
                'key_from' => 'id',
                'model_to' => '\\EntryPoints\\Model\\EntryPoints',
                'key_to' => 'created_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'modified_entrypoints' => array(
                'key_from' => 'id',
                'model_to' => '\\EntryPoints\\Model\\EntryPoints',
                'key_to' => 'modified_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'created_parameters' => array(
                'key_from' => 'id',
                'model_to' => '\\Parameters\\Model\\Parameters',
                'key_to' => 'created_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'modified_parameters' => array(
                'key_from' => 'id',
                'model_to' => '\\Parameters\\Model\\Parameters',
                'key_to' => 'modified_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'created_parametertypes' => array(
                'key_from' => 'id',
                'model_to' => '\\ParameterTypes\\Model\\ParameterTypes',
                'key_to' => 'created_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'modified_parametertypes' => array(
                'key_from' => 'id',
                'model_to' => '\\ParameterTypes\\Model\\ParameterTypes',
                'key_to' => 'modified_by',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        )
    );

}