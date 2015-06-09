<?php

namespace Packages\Model;

class Packages extends \Model\Module {

	protected static $_table_name = 'packages';
	protected static $_relationships = array(
		'has_many' => array(
			'application' => array(
				'key_from' => 'id',
				'model_to' => 'Packages\\Model\\Applications',
				'key_to' => 'package_id',
				'cascade_save' => true,
				'cascade_delete' => false,
			),
			'api' => array(
				'key_from' => 'id',
				'model_to' => 'Packages\\Model\\Apis',
				'key_to' => 'package_id',
				'cascade_save' => true,
				'cascade_delete' => false,
			),
		)
	);


}