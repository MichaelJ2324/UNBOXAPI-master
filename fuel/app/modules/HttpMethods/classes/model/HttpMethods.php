<?php

namespace HttpMethods\Model;

class HttpMethods extends \UNBOXAPI\Canister\Hard {

    protected static $_table_name = 'http_methods';
    protected static $_fields = array(
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
    protected static $_relationships = array(
        'belongs_to' => array(
            'entrypoints' => array(
                'key_from' => 'id',
                'model_to' => 'Entrypoints\\Model\\Entrypoints',
                'key_to' => 'method',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        )
    );
} 