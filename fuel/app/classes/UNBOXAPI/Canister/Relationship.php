<?php

namespace UNBOXAPI\Canister;

class Relationship extends Soft {

    protected static $_connection = 'default';
    protected static $_soft_delete = array(
        'deleted_field' => 'deleted_at',
        'mysql_timestamp' => true,
    );
    protected static $_default_fields = array(
        'id' => array(
            'data_type' => 'varchar',
            'label' => 'ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'validation' => array(),
            'form' => false,
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Modified',
            'validation' => array(),
            'form' => false,
        ),
        'deleted' => array(
            'data_type' => 'tinyint',
            'label' => 'Deleted',
            'default' => 0,
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 1
            ),
            'form' => false
        ),
        'deleted_at' => array(
            'data_type' => 'datetime',
            'label' => 'Deleted At',
            'validation' => array(
                'required' => true,
            ),
            'form' => false
        ),
    );
    //hooks is an override array for extra observers
    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property' => 'date_created',
            'overwrite' => true,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property' => 'date_modified',
            'overwrite' => true
        ),
        '\\UNBOXAPI\\Observer_Guid' => array(
            'events' => array('before_insert'),
        ),
        '\\UNBOXAPI\\Observer_DeleteFlag' => array(
            'events' => array('before_delete'),
        ),
    );
    protected static $_conditions = array(
        'where' => array(
            array('deleted', '=', 0)
        ),
    );
} 