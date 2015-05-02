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
        'entryPoints' => array(
            'key_from' => 'id',
            'model_to' => 'EntryPoints\\Model\\EntryPoints',
            'key_to' => 'method',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    protected static $_has_one = array();
    protected static $_has_many = array();
    //TODO::Remove this relationship, as I don't think its used anymore
    protected static $_many_many = array(
        'apis' => array(
            'key_from' => 'id',
            'key_through_from' => 'method_id', // column 1 from the table in between, should match a posts.id
            'table_through' => 'api_httpMethods', // both models plural without prefix in alphabetical order
            'key_through_to' => 'api_id', // column 2 from the table in between, should match a users.id
            'model_to' => 'Apis\\Model\\Apis',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    protected static $_observers = array();
    protected static $_conditions = array();
} 