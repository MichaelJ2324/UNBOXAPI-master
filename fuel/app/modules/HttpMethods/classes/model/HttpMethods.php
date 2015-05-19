<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 1:45 AM
 */

namespace HttpMethods\Model;


class HttpMethods extends \Model\Module{

    protected static $_table_name = 'http_methods';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'tinyint',
            'label' => 'HTTP Method ID',
            'null' => false,
            'auto_inc' => true,
            'validation' => array(
                'max_length' => 1
            )
        ),
        'method' => array(
            'data_type' => 'varchar',
            'label' => 'Method Name',
            'null' => false,
            'validation' => array(
                'required' => true,
                'min_length' => 3,
                'max_length' => 20
            ),
            'filter' => true,
            'form' => array('type'=>'select'),
        ),
    );
    protected static $_relationship_properties = array(
        'apis' => array(
            'method_id' => array(
                'data_type' => 'tinyint',
                'label' => 'Order',
                'validation' => array(
                    'required' => true,
                    'max_length' => 1
                ),
            ),
        )
    );
    protected static $_belongs_to = array(
        'entrypoints' => array(
            'key_from' => 'id',
            'model_to' => 'Entrypoints\\Model\\Entrypoints',
            'key_to' => 'method',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    protected static $_has_one = array();
    protected static $_has_many = array();
    protected static $_many_many = array();
    protected static $_observers = array();
    protected static $_conditions = array();
} 