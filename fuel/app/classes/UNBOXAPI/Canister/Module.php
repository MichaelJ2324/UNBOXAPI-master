<?php

namespace UNBOXAPI\Canister;

class Module extends Soft {

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
		'name' => array(
			'data_type' => 'varchar',
			'label' => 'Name',
			'null' => false,
			'validation' => array(
				'required' => true,
				'min_length' => 3,
				'max_length' => 100
			),
			'form' => array('type' => 'text'),
			'filter' => true
		),
		'created_by' => array(
			'data_type' => 'varchar',
			'label' => 'Created By',
			'validation' => array(
				'max_length' => 50
			),
			'form' => false,
		),
		'date_created' => array(
			'data_type' => 'datetime',
			'label' => 'Date Created',
			'validation' => array(),
			'form' => false,
		),
		'modified_by' => array(
			'data_type' => 'varchar',
			'label' => 'Modified By',
			'validation' => array(
				'max_length' => 50
			),
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
		)
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
        '\\UNBOXAPI\\Observer_ModifiedBy' => array(
            'events' => array('before_save'),
        ),
        '\\UNBOXAPI\\Observer_CreatedBy' => array(
            'events' => array('before_insert'),
        ),
        '\\UNBOXAPI\\Observer_DeleteFlag' => array(
            'events' => array('before_delete'),
        ),
    );
    protected static $_conditions = array(
        'order_by' => array('name' => 'asc'),
        'where' => array(
            array('deleted', '=', 0)
        ),
    );
} 