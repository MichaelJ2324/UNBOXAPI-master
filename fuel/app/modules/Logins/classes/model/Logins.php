<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 9:03 AM
 */

namespace Logins\Model;


class Logins extends \Model\Unbox{

    protected static $_table_name = 'logins';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'smallint',
            'label' => 'Login ID',
            'null' => false,
            'auto_inc' => true,
            'form' => array('type'=>'hidden')
        ),
        'name' => array(
            'data_type' => 'varchar',
            'label' => 'Name',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(3),
                'max_length' => array(50)
            ),
            'form' => array('type' => 'text'),
        ),
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
        'deleted' => array(
            'data_type' => 'tinyint',
            'label' => 'Description',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => array(500)
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
                'max_length' => array(500)
            ),
            'form' => array(
                'type' => 'checkbox',
                'disabled' => 'disabled'
            ),
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
    );
    protected static $_belongs_to = array(
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
    );
    protected static $_many_many = array(
        'apis' => array(
            'key_from' => 'id',
            'key_through_from' => 'login_id', // column 1 from the table in between, should match a posts.id
            'table_through' => 'api_logins', // both models plural without prefix in alphabetical order
            'key_through_to' => 'api_id', // column 2 from the table in between, should match a users.id
            'model_to' => 'APIs\\Model\\APIs',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );
} 