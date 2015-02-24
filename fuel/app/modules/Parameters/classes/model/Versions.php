<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 1:03 PM
 */

namespace Parameters\Model;


class Versions extends \Model\Unbox{

    protected static $_table_name = 'parameter_versions';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'int',
            'label' => 'parameter Version ID',
            'null' => false,
            'auto_inc' => true
        ),
        'past_parameter_id' => array(
            'data_type' => 'int',
            'label' => 'Past parameter ID',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => false,
        ),
        'new_parameter_id' => array(
            'data_type' => 'int',
            'label' => 'New parameter ID',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => false,
        ),
        'change_description' => array(
            'type' => 'varchar',
            'label' => 'Description',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required',
                'max_length' => array(500)
            ),
            'form' => array('type' => 'text'),
        ),
        'change_data' => array(
            'type' => 'text',
            'label' => 'Description',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required',
                'max_length' => array(500)
            ),
            'form' => false,
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => array('type' => 'text'),
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => array('type' => 'text'),
        ),
    );
    protected static $_belongs_to = array(
        'pastparameters' => array(
            'key_from' => 'past_parameter_id',
            'model_to' => 'Parameters\\Model\\Parameters',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'newparameters' => array(
            'key_from' => 'new_parameter_id',
            'model_to' => 'Parameters\\Model\\Parameters',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );
} 