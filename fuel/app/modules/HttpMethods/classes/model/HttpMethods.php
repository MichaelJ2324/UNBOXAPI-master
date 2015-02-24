<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 1:45 AM
 */

namespace HttpMethods\Model;


class HttpMethods extends \Model\Unbox{

    protected static $_table_name = 'http_methods';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'smallint',
            'label' => 'Login ID',
            'null' => false,
            'auto_inc' => true,
            'form' => array('type'=>'hidden')
        ),
        'method' => array(
            'data_type' => 'varchar',
            'label' => 'Method',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(3),
                'max_length' => array(50)
            ),
            'form' => array('type'=>'select'),
        ),
    );
    protected static $_has_many = array(
        'entryPoints' => array(
            'key_from' => 'id',
            'model_to' => 'EntryPoints\\Model\\EntryPoints',
            'key_to' => 'method',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );

    protected static $_observers = array();
} 