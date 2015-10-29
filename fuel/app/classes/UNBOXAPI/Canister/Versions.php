<?php

namespace UNBOXAPI\Canister;

class Versions extends Nested {

    protected static $_connection = 'default';
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
        'version' => array(
            'data_type' => 'varchar',
            'label' => 'Version',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 20
            ),
            'form' => array('type' => 'text'),
        ),
        'changes' => array(
            'data_type' => 'text',
            'label' => 'changes',
            'null' => false,
            'validation' => array(
                'required' => true,
            ),
            'form' => array('type' => 'text'),
        ),
        'created_by' => array(
            'data_type' => 'varchar',
            'label' => 'Created By',
            'validation' => array(
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'validation' => array(),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
        'modified_by' => array(
            'data_type' => 'varchar',
            'label' => 'Created By',
            'validation' => array(
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'validation' => array(),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
        'left_id' => array(
            'data_type' => 'int',
            'label' => 'Left ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 11
            ),
            'form' => false
        ),
        'right_id' => array(
            'data_type' => 'int',
            'label' => 'Right ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 11
            ),
            'form' => false
        ),
        'related_id' => array(
            'data_type' => 'varchar',
            'label' => 'Related ID',
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => false
        ),
    );
    protected static $_tree = array(
        'left_field'     => 'left_id',		// name of the tree node left index field
        'right_field'    => 'right_id',		// name of the tree node right index field
        'tree_field'     => 'related_id',		// name of the tree node tree index field
        'title_field'    => 'version',		//  name of the tree node title field
    );

    protected static $_relationships = array(
        'belongs_to' => array(),
        'has_one' => array(
            'creating_user' => array(
                'key_from' => 'created_by',
                'model_to' => 'Users\\Model\\Users',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'modifying_user' => array(
                'key_from' => 'modified_by',
                'model_to' => 'Users\\Model\\Users',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'has_many' => array(),
        'many_many' => array()
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property' => 'date_created',
            'overwrite' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property' => 'date_modified'
        ),
        '\\UNBOXAPI\\Observer_Guid' => array(
            'events' => array('before_insert'),
        ),
        '\\UNBOXAPI\\Observer_Modified_By' => array(
            'events' => array('before_save'),
        ),
        '\\UNBOXAPI\\Observer_CreatedBy' => array(
            'events' => array('before_insert'),
        ),
    );

}