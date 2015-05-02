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
    protected static $_config;
    //TODO::Add static $_labels and label configuration to Modules

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
            'fields' => static::fields(),
            'relationships' => static::relationships(),
            'config' => static::config()
        );
    }
    public static function seeds()
    {
        $seeds = \Config::load(static::$_name."::seed");
        if (count($seeds)>0) {
            return $seeds;
        }
        return false;
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
    public static function config(){
        static::$_config = \Config::load(static::$_name."::module");
        return static::$_config;
    }
    protected static function formatResult($results){
        $view = \Input::get('view');
        $newResult = array();
        if (isset($view)){
            $viewConfig = static::getViews($view);
            $c = 0;
            foreach($results as $key => $model){
                if (is_object($model)){
                    $modelArray = $model->to_array();
                }else{
                    $modelArray = $model;
                }
                $newResult[$c] = array();
                foreach($viewConfig as $returnKey => $modelKey){
                    $newResult[$c][$returnKey] = $modelArray[$modelKey];
                }
                $c++;
            }
        }else{
            foreach ($results as $key => $model) {
                if (is_object($model)){
                    $newResult[] = $model->to_array();
                }else{
                    $newResult[] = $model;
                }
            }
        }
        return $newResult;
    }
    protected static function getViews($view=''){
        $views = \Config::load(static::$_name."::views");
        if ($view!==""){
            return $views[$view];
        }
        return $views;
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
    public static function filter(array $filters = array()){
        $model = static::model();
        $fields = static::fields();
        if (count($filters)==0){
            $filters = \Input::param("filters");
        }
        $query = $model::query();
        foreach($filters as $field => $value){
            if (array_key_exists($field,$fields)){
                if (array_key_exists('filter',$fields[$field])){
                    if ($fields[$field]['filter']===true){
                        switch ($fields[$field]['data_type']){
                            case "varchar":
                            case "text":
                                $value = $value."%";
                                $query->where(array($field,'LIKE',$value));
                                break;
                            default:
                                $query->where(array($field,'=',$value));
                        }
                    }
                }
            }
        }
        $offset = \Input::param('offset')||0;

        $total = $query->count();
        $results = $query->limit(20)->offset($offset)->get();
        $records = static::formatResult($results);
        return array(
            'total' => $total,
            'records' => $records,
            'page' => ($offset/20)
        );
    }
} 