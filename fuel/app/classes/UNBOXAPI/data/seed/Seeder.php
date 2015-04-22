<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/29/15
 * Time: 2:16 PM
 */

namespace UNBOXAPI\Data\Seed;


class Seeder {

    protected static $_module;
    protected static $_model;
    /**
     * @var = array(
     *      'model_field' => 'value'
     * )
     */
    protected static $_records;
    /**
     * @var = array(
     *      'id' => 'Model ID',
     *      'name' => 'Relationship Name',
     *      'related_id' => 'Related ID'
     * )
     */
    protected static $_relationships;

    private $module;
    private $Model;

    /**
     * DEPRECATED
     */
    public static function run(){
        $module = static::$_module;
        $model = (isset(static::$_model)?"$module\\Model\\".static::$_model:"$module\\Model\\$module");
        $recordArray = array();
        foreach (static::$_records as $record => $values){
            $Record = $model::forge($values);
            $Record->save();
            $recordArray[] = $Record;
        }
    }
    protected static function records(){
        return static::$_records;
    }
    protected static function relationships(){
        return static::$_relationships;
    }
    protected static function model(){
        $module = static::$_module;
        return (isset(static::$_model)?"{$module}\\Model\\".static::$_model:"{$module}\\Model\\{$module}");
    }
    public function __construct(){
        $this->module = static::$_module;
        $this->Model = static::model();
    }
    public function seed($relationships=false,$relationshipsOnly=false){
        if (!$relationshipsOnly) {
            $this->insert($this->Model);
        }
        if ($relationships) {
            $this->relate($this->Model);
        }
    }
    public function insert($model){
        $records = static::records();
        foreach ($records as $record => $values){
            $Record = $model::forge($values);
            $Record->save();
        }
    }
    public function relate($model){
        $relationships = static::relationships();
        $count = 1;
        foreach ($relationships as $relationship => $properties){
            //get record based on ID
            $Record = $model::find($properties['id'],array('relations' => array($properties['name'])));
            //Get relationship object
            $Relationship = $model::relations($properties['name']);
            $relatedModel = $Relationship->__get('model_to');
            $RelatedRecord = $relatedModel::find($properties['related_id']);
            //Find related Record for relationship
            $Record->{$properties['name']}[] = $RelatedRecord;
            $Record->save();
            $count++;
        }
    }
}