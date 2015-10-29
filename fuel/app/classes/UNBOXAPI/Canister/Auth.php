<?php

namespace UNBOXAPI\Canister;

class Auth extends Hard {

    protected static $_connection = 'auth';
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
		)
    );
	protected static $_conditions = array(
		'where' => array(
			array('deleted', '=', 0)
		),
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
    );

}