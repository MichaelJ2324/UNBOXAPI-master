<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 2:15 AM
 */

namespace Model;


class Unbox extends \Orm\Model{

    protected static $_connection = 'default';
    protected static $_primary_key = array('id');
    protected static $_properties = array();
    protected static $_belongs_to = array();
    protected static $_has_one = array();
    protected static $_has_many = array();
    protected static $_many_many = array();
    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property' => 'date_created',
            'overwrite' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property' => 'date_modified',
            'relations' => array(),
        ),
    );
    public static function fields(){
        $fields = array();
        foreach (static::$_properties as $field=>$attributes){
            $fields[$field] = array(
                'data_type' => $attributes['data_type'],
                'label' => $attributes['label'],
                'auto_inc' => $attributes['auto_inc'],
                'required' => isset($attributes['validation']['required'])?$attributes['validation']['required']:false,
                'validation' => isset($attributes['validation'])?$attributes['validation']:array(),
                'form' => isset($attributes['form'])?$attributes['form']:array()
            );
        }
        return $fields;
    }
    public static function relationships(){
        $relationships = array();
        foreach (static::$_has_many as $relationship=>$attributes){


        }
        return $relationships;
    }
} 