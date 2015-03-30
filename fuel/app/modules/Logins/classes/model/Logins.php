<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 9:03 AM
 */

namespace Logins\Model;


class Logins extends \Model\Module{

    protected static $_table_name = 'logins';
    protected static $_fields = array(
        'login_entryPoint_id' => array(
            'data_type' => 'int',
            'label' => 'Login Entry Point ID',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => false,
        ),
        'logout_entryPoint_id' => array(
            'data_type' => 'int',
            'label' => 'Logout Entry Point ID',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => false,
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Description',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
            ),
            'form' => array(
                'type' => 'checkbox',
                'disabled' => 'disabled'
            ),
        ),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'login_entryPoint' => array(
                'key_from' => 'login_entryPoint_id',
                'model_to' => 'EntryPoints\\Model\\EntryPoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'logout_entryPoint' => array(
                'key_from' => 'logout_entryPoint_id',
                'model_to' => 'EntryPoints\\Model\\EntryPoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'many_many' => array(
            'apis' => array(
                'key_from' => 'id',
                'key_through_from' => 'login_id', // column 1 from the table in between, should match a posts.id
                'table_through' => 'api_logins', // both models plural without prefix in alphabetical order
                'key_through_to' => 'api_id', // column 2 from the table in between, should match a users.id
                'model_to' => 'Apis\\Model\\Apis',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        )
    );
} 