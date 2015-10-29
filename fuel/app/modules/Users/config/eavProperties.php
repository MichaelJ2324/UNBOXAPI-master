<?php
return array(
	'first_name' => array(
		'data_type' => 'varchar',
		'label' => 'First Name',
		'validation' => array(
			'required' => true,
			'max_length' => 50
		),
		'form' => array(
			'type' => 'text'
		)
	),
	'last_name' => array(
		'data_type' => 'varchar',
		'label' => 'Last Name',
		'validation' => array(
			'required' => true,
			'max_length' => 50
		),
		'form' => array(
			'type' => 'text'
		)
	),
	'username' => array(
		'data_type' => 'varchar',
		'label' => 'User Name',
		'validation' => array(
			'required' => true,
			'max_length' => 50
		),
		'form' => array(
			'type' => 'text'
		)
	),
	'show_full_name' => array(
		'data_type' => 'tinyint',
		'label' => 'Show Full Name?',
		'validation' => array(
			'max_length' => 1
		),
		'form' => array(
			'type' => 'checkbox'
		)
	),
	'primary_email' => array(
		'data_type' => 'varchar',
		'label' => 'Primary Email',
		'validation' => array(
			'required' => true,
			'max_length' => 100
		),
		'form' => array(
			'type' => 'text'
		)
	),
	'secondary_email' => array(
		'data_type' => 'varchar',
		'label' => 'Backup Email',
		'validation' => array(
			'max_length' => 100
		),
		'form' => array(
			'type' => 'text'
		)
	),
	'default_module' => array(
		'data_type' => 'varchar',
		'label' => 'Last Name',
		'validation' => array(
			'max_length' => 25
		),
		'form' => array(
			'type' => 'select',
			'options' => array(
				array(
					'key' => 'Home',
					'value' => 'Home'
				),
				array(
					'key' => 'Tester',
					'value' => 'Tester'
				),
				array(
					'key' => 'Manager',
					'value' => 'Manager'
				),
			)
		)
	),
	'verified' => array(
		'data_type' => 'tinyint',
		'label' => 'Verified',
		'validation' => array(
			'max_length' => 1
		),
		'form' => false
	),
);