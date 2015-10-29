<?php

namespace Users\Model;

class Preferences extends \UNBOXAPI\Canister\Hard {

	protected static $_table_name = 'user_preferences';
	// list of properties for this model
	protected static $_fields = array(
		'id' => array(
			'data_type' => 'int',
			'label' => 'ID',
			'null' => false,
			'auto_inc' => true,
			'validation' => array(
				'required' => true,
				'max_length' => 11
			),
			'form' => false
		),
		'user_id' => array(
			'data_type' => 'varchar',
			'label' => 'User ID',
			'null' => false,
			'validation' => array(
				'required' => true,
				'max_length' => 50
			),
			'form' => false
		),
		'attribute' => array(
			'data_type' => 'varchar',
			'label' => 'Attribute',
			'null' => false,
			'validation' => array(
				'required' => true,
				'max_length' => 255
			),
			'form' => false
		),
		'value' => array(
			'data_type' => 'varchar',
			'label' => 'Value',
			'null' => false,
			'validation' => array(
				'required' => true,
				'max_length' => 2048
			),
			'form' => false
		)
	);

	protected static $_relationships = array(
		'belongs_to' => array(
			'user' => array(
				'key_from' => 'user_id',
				'model_to' => 'Users\\Model\\Users',
				'key_to' => 'id',
				'cascade_save' => true,
				'cascade_delete' => true,
			)
		)
	);
}