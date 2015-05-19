<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/4/15
 * Time: 9:33 AM
 */

namespace Entrypoints\Model;


class Parameters extends \Model\Relationship {

    protected static $_table_name = 'entrypoint_parameters';
    protected static $_fields = array(
        'created_by' => array(
            'data_type' => 'varchar',
            'label' => 'Created By',
            'validation' => array(
                'max_length' => 50
            ),
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
        'entrypoint_id' => array(
            'data_type' => 'varchar',
            'label' => 'Entrypoint ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'parameter_id' => array(
            'data_type' => 'varchar',
            'label' => 'Parameter ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'required' => array(
            'data_type' => 'tinyint',
            'label' => 'Required',
            'default' => 0,
            'validation' => array(
                'max_length' => 1
            ),
            'form' => array(
                'type' => 'checkbox'
            ),
        ),
        'order' => array(
            'data_type' => 'int',
            'label' => 'Order',
            'default' => 0,
            'validation' => array(),
            'form' => array(
                'type' => 'text'
            ),
        ),
        'default' => array(
            'data_type' => 'varchar',
            'label' => 'Default Value',
            'validation' => array(
                'max_length' => '1024'
            ),
            'form' => array(
                'type' => 'text'
            ),
        ),
        'login_pane' => array(
            'data_type' => 'varchar',
            'label' => 'Login Panel',
            'default' => "normal",
            'validation' => array(
                'max_length' => 10,
            ),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    array(
                        'key' => 'normal',
                        'value' => 'Default'
                    ),
                    array(
                        'key' => 'advanced',
                        'value' => 'Advanced'
                    )
                ),
                'help' => 'If this parameter is for a Login method, should the parameter show by Default on the Login Form, or be hidden in the Advanced section.'
            ),
        )
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'entrypoint' => array(
                'key_from' => 'entrypoint_id',
                'model_to' => 'Entrypoints\\Model\\Entrypoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'parameter' => array(
                'key_from' => 'parameter_id',
                'model_to' => 'Parameters\\Model\\Parameters',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
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
}