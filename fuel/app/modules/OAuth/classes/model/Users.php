<?php

namespace OAuth\Model;

class Users extends \UNBOXAPI\Canister\Auth {

    protected static $_table_name = 'users';
    protected static $_to_array_exclude = array('password');
    protected static $_fields = array(
        'username' => array(
            'data_type' => 'varchar',
            'label' => 'Username',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array('type' => 'text'),
        ),
        'password' => array(
            'data_type' => 'varchar',
            'label' => 'Password',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 2048
            ),
            'form' => array('type' => 'password'),
        ),
        'email' => array(
            'data_type' => 'varchar',
            'label' => 'Email',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 100
            ),
            'form' => array('type' => 'text'),
        ),
		'api_client_id' => array(
			'data_type' => 'varchar',
			'label' => 'API Client ID',
			'validation' => array(
				'required' => true,
				'max_length' => 50
			),
		),
    );
	protected static $_relationships = array(
		'has_one' => array(
			'api_client' => array(
				'key_from' => 'id',
				'model_to' => 'OAuth\\Model\\Clients',
				'key_to' => 'api_client_id',
				'cascade_save' => false,
				'cascade_delete' => false,
			)
		)
	);
}