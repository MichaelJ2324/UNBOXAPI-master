<?php

namespace Users\Model;

class VerificationCodes extends \Orm\Model {

	protected static $_table_name = 'verification_codes';
	protected static $_fields = array(
		'id' => array(
			'data_type' => 'int',
			'label' => 'ID',
			'auto_inc' => true,
			'null' => false,
			'unsigned' => true,
			'validation' => array(
				'required' => true,
				'max_length' => 11
			),
		),
		'code' => array(
			'data_type' => 'varchar',
			'label' => 'Verification Code',
			'null' => false,
			'validation' => array(
				'required' => true,
				'max_length' => 255
			),
		),
		'user_id' => array(
			'data_type' => 'varchar',
			'label' => 'Session ID',
			'null' => false,
			'validation' => array(
				'required' => true,
				'max_length' => 50
			),
		),
		'expire_time' => array(
			'data_type' => 'int',
			'label' => 'Expire Time',
			'null' => false,
			'unsigned' => true,
			'validation' => array(
				'required' => true,
				'max_length' => 11
			),
		),
	);
	protected static $_relationships = array(
		'has_one' => array(
			'user' => array(
				'key_from' => 'user_id',
				'model_to' => 'Users\\Model\\Users',
				'key_to' => 'id',
				'cascade_save' => false,
				'cascade_delete' => false,
			),
		)
	);

}