<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/4/14
 * Time: 12:56 PM
 */

namespace UNBOXAPI;


class Module {
    protected static $_name = "";
    protected static $_label = "";
    protected static $_label_plural = "";
    protected static $_enabled = false;
    protected static $_options = array(
        'versioning' => false
    );

    public $id;
    public $name;
    public $deleted = 0;
    public $deleted_at;
    public $date_created;
    public $created_by;
    public $date_modified;
    public $modified_by;

    protected $model;

    function __construct(){
        $this->model = static::model();
    }
    /**
     * @param $resource = Could be a Model\Module object or a GUID Id
     */
    public function load($resource){
        $model = $this->model;
        if (is_object($resource)){
            if ($resource instanceof \Model\Module){
                return $this->loadFromModel($resource);
            }else if ($resource instanceof \UNBOXAPI\Data\Util\Guid){
                $id = $resource->get();
                $record = $model::find($id);
                return $this->loadFromModel($record);
            }
        }else{
            //assume string ID
            $id = $resource;
            $record = $model::find($id);
            return $this->loadFromModel($record);
        }
    }
    protected function loadFromModel($record){
        $model = $this->model;
        $fields = $model::properties();
        foreach ($fields as $field => $definition){
            $this->{$field} = $record->{$field};
        }
        return true;
    }
    //Metadata methods
    public static function metadata(){
        return array(
            'name' => static::$_name,
            'label' => static::$_label,
            'label_plural' => static::$_label_plural,
            'enabled' => static::$_enabled,
            'fields' => static::fields(),
            'relationships' => static::relationships(),
            'options' => static::options()
        );
    }
    public static function fields(){
        $model = static::model();
        return $model::fields();
    }
    public static function relationships(){
        $model = static::model();
        return $model::relationships();
    }
    public static function model(){
        $module = static::$_name;
        return "$module\\Model\\$module";
    }
    public static function options(){
        return static::$_options;
    }
    protected static function formatResult($model){
        $rows = array();
        foreach ($model as $i => $obj) {
            $rows[] = $obj->to_array();
        }
        return $rows;
    }

    //CRUD Methods
    public static function create(Module $record = null){
        $model = static::model();
        if ($record==null) {
            $application = $model::forge(\Input::json());
        }else{
            $properties = get_object_vars($record);
            $application = $model::forge($properties);
        }
        $application->save();
        return $application;
    }
    public static function update($id,$properties = null){
        $model = static::model();
        $record = $model::find($id);
        if ($properties==null) {
            $properties = array();
            foreach (\Input::json() as $key => $value) {
                if (!($key == "id" || $key == "date_created" || $key == "date_modified" || $key == "deleted_at")) {
                    $properties[$key] = $value;
                }
            }
        }
        $record->set($properties);
        $record->save();
        return $record;
    }
    public static function get($id=""){
        $model = static::model();
        if ($id==""){
            $id='all';
            $record = $model::find($id);
            $record = static::formatResult($record);
        }else {
            $record = $model::find($id);
        }
        return $record;
    }
    public static function delete($id){
        $model = static::model();
        $record = $model::find($id);
        $record->delete();
        return $record;
    }
    public static function filter(array $filters){
        $model = static::model();
        $fields = static::fields();
        //TODO Add base filter method for Module
    }
} 