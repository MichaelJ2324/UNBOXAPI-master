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
            'data_type' => 'varchar',
            'label' => 'Login Entry Point',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'EntryPoints'
            ),
        ),
        'logout_entryPoint_id' => array(
            'data_type' => 'varchar',
            'label' => 'Logout Entry Point',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'EntryPoints'
            ),
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'type' => 'checkbox'
            ),
        ),
    );
    protected static $_relationships = array(
        'has_one' => array(
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
        'has_many' => array(
            'apis' => array(
                'key_from' => 'id',
                'model_to' => 'Apis\\Model\\Logins',
                'key_to' => 'login_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'settings' => array(
                'key_from' => 'id',
                'model_to' => 'Logins\\Model\\Settings',
                'key_to' => 'login_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        )
    );
} 