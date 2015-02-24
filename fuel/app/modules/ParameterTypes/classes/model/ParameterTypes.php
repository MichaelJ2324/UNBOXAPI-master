<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 1/26/15
 * Time: 9:06 AM
 */

namespace ParameterTypes\Model;


class ParameterTypes extends \Model\Unbox{

    protected static $_table_name = 'parameter_types';

    protected static $_properties = array(
        'id' => array(
            'data_type' => 'int',
            'label' => 'Parameter Type ID',
            'null' => false,
            'auto_inc' => true
        ),
        'name' => array(
            'data_type' => 'varchar',
            'label' => 'Name',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(1),
                'max_length' => array(70)
            ),
            'form' => array(
                'name' => 'name',
                'type' => 'text'
            ),
            'filter' => true
        ),
        'type' => array(
            'data_type' => 'tinyint',
            'label' => 'Type',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(1),
                'max_length' => array(10)
            ),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    0 => array(
                        'key'=>'1',
                        'value' => 'data'
                    ),
                    1 => array(
                        'key'=>'2',
                        'value'=> 'api'
                    )
                )
            ),
            'filter' => true
        ),
        'template' => array(
            'data_type' => 'text',
            'label' => 'Template',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'textarea'
            ),
        ),
    );
    protected static $_has_many = array(
        'data_type_parameters' => array(
            'key_from' => 'id',
            'model_to' => 'Parameters\\Model\\Parameters',
            'key_to' => 'data_type',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'api_type_parameters' => array(
            'key_from' => 'id',
            'model_to' => 'Parameters\\Model\\Parameters',
            'key_to' => 'api_type',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    public static function findType($type=""){
        if ($type!=="") {
            return static::find('all',array('where'=>array(array('type',$type))));
        }else{
            return static::find('all');
        }
    }
}