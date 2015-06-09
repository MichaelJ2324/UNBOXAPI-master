<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/8/15
 * Time: 3:03 PM
 */

namespace Users\Model;


class Users extends \Orm\Model{

    protected static $_table_name = 'user';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'varchar',
            'label' => 'User ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => false
        ),
    );
	protected static $_eav = array(
		'statistics' => array(
			  'model_to' => '\\Users\\Model\\Preferences',
			  'attribute' => 'attribute',		// the key column in the related table contains the attribute
			  'value' => 'value',			// the value column in the related table contains the value
		)
	);
    protected static $_has_many = array(
		'preferences' => array(
			'key_from' => 'id',			// key in this model
			'model_to' => 'Users\\Model\\Preferences',      // related model
			'key_to' => 'user_id',		// key in the related model
			'cascade_save' => true,		// update the related table on save
			'cascade_delete' => true,		// delete the related data when deleting the parent
		),
	);
	protected static $_belongs_to = array(
		'created_applications' => array(
			'key_from' => 'id',
			'model_to' => '\\Applications\\Model\\Applications',
			'key_to' => 'created_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'modified_applications' => array(
			'key_from' => 'id',
			'model_to' => '\\Applications\\Model\\Applications',
			'key_to' => 'modified_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'created_apis' => array(
			'key_from' => 'id',
			'model_to' => '\\Apis\\Model\\Apis',
			'key_to' => 'created_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'modified_apis' => array(
			'key_from' => 'id',
			'model_to' => '\\Apis\\Model\\Apis',
			'key_to' => 'modified_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'created_entrypoints' => array(
			'key_from' => 'id',
			'model_to' => '\\Entrypoints\\Model\\Entrypoints',
			'key_to' => 'created_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'modified_entrypoints' => array(
			'key_from' => 'id',
			'model_to' => '\\Entrypoints\\Model\\Entrypoints',
			'key_to' => 'modified_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'created_entrypoint_parameters' => array(
			'key_from' => 'id',
			'model_to' => '\\Entrypoints\\Model\\Parameters',
			'key_to' => 'created_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'modified_entrypoint_parameters' => array(
			'key_from' => 'id',
			'model_to' => '\\Entrypoints\\Model\\Parameters',
			'key_to' => 'modified_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'created_parameters' => array(
			'key_from' => 'id',
			'model_to' => '\\Parameters\\Model\\Parameters',
			'key_to' => 'created_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'modified_parameters' => array(
			'key_from' => 'id',
			'model_to' => '\\Parameters\\Model\\Parameters',
			'key_to' => 'modified_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'created_parameterTypes' => array(
			'key_from' => 'id',
			'model_to' => '\\ParameterTypes\\Model\\ParameterTypes',
			'key_to' => 'created_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'modified_parameterTypes' => array(
			'key_from' => 'id',
			'model_to' => '\\ParameterTypes\\Model\\ParameterTypes',
			'key_to' => 'modified_by',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
    );
	protected static $_observers = array(
		'\\UNBOXAPI\\Observer_Guid' => array(
			'events' => array('before_insert'),
		),
	);

}