<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 1/26/15
 * Time: 9:06 AM
 */

namespace ParameterTypes\Model;


class ParameterTypes extends \Model\Module{

    protected static $_table_name = 'parameter_types';

    protected static $_fields = array(
        'type' => array(
            'data_type' => 'tinyint',
            'label' => 'Type',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 10
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
    protected static $_relationships = array(
        'has_many' => array(
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
        )
    );

    public static function types($type=""){
        return static::find('all',array('where'=>array(array('type',$type))));
    }
    public static function findByName($name,$type=""){
        $query = static::query()->where(array('name',$name));
        if ($type!==""){
            $query->where(array("type",$type));
        }
        return $query->get_one();
    }
}