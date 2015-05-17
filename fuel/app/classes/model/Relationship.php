<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 2:15 AM
 */

namespace Model;


class Relationship extends \Orm\Model_Soft{

    protected static $_connection = 'default';
    protected static $_table_name;
    protected static $_primary_key = array('id');
    protected static $_soft_delete = array(
        'deleted_field' => 'deleted_at',
        'mysql_timestamp' => true,
    );
    //fields array is an override array for properties
    protected static $_fields = array(
        'id' => array(
            'data_type' => 'varchar',
            'label' => 'ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'validation' => array(),
            'form' => false,
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Modified',
            'validation' => array(),
            'form' => false,
        ),
        'deleted' => array(
            'data_type' => 'tinyint',
            'label' => 'Deleted',
            'default' => 0,
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 1
            ),
            'form' => false
        ),
        'deleted_at' => array(
            'data_type' => 'datetime',
            'label' => 'Deleted At',
            'validation' => array(
                'required' => true,
            ),
            'form' => false
        ),
    );
    protected static $_properties = array();
    //Some relationships require extra properties on the table
    // Define them in the $_relationship_properties array
    // Key = relationship name
    // Value = Array of field properties, similar to $_properties/$_fields
    protected static $_relationship_properties = array();
    //relationshps is an override array for all relationship arrays
    protected static $_relationships = array(
        'belongs_to' => array(),
        'has_one' => array(),
        'has_many' => array(),
        'many_many' => array()
    );
    protected static $_belongs_to = array();
    protected static $_has_one = array();
    protected static $_has_many = array();
    protected static $_many_many = array();
    //hooks is an override array for extra observers
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
        '\\UNBOXAPI\\Observer_DeleteFlag' => array(
            'events' => array('before_delete'),
        ),
    );
    protected static $_conditions = array(
        'where' => array(
            array('deleted', '=', 0)
        ),
    );
    public static function properties(){
        self::$_properties = array_merge(self::$_fields,static::$_fields);
        return parent::properties();
    }
    public static function relations($specific = false){
        if (isset(static::$_relationships['belongs_to'])) {
            self::$_belongs_to = array_merge(self::$_relationships['belongs_to'],static::$_relationships['belongs_to']);
        }else{
            self::$_belongs_to = self::$_relationships['belongs_to'];
        }
        if (isset(static::$_relationships['has_one'])) {
            self::$_has_one = array_merge(self::$_relationships['has_one'],static::$_relationships['has_one']);
        }else{
            self::$_has_one = self::$_relationships['has_one'];
        }
        if (isset(static::$_relationships['has_many'])) {
            self::$_has_many = array_merge(self::$_relationships['has_many'],static::$_relationships['has_many']);
        }else{
            self::$_has_many = self::$_relationships['has_many'];
        }
        if (isset(static::$_relationships['many_many'])) {
            self::$_many_many = array_merge(self::$_relationships['many_many'],static::$_relationships['many_many']);
        }else{
            self::$_many_many = self::$_relationships['many_many'];
        }
        return parent::relations($specific);
    }
} 