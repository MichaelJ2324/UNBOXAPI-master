<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/4/14
 * Time: 12:56 PM
 */

namespace UNBOXAPI;


abstract class Module {
    protected static $_name = "";
    protected static $_label = "";
    protected static $_label_plural = "";
    protected static $_config;
    protected static $_models = array();
    //TODO::Add static $_labels and label configuration to Modules

    public $id;
    public $name;
    public $deleted = 0;
    public $deleted_at;
    public $date_created;
    public $created_by;
    public $date_modified;
    public $modified_by;

    protected $model; //Model object
    protected $_model; //Name of model

    //Static Metadata Methods
    /**
     * Gets the Metadata array for the module
     * @return array
     */
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

    /**
     * Gets the config for the Module
     * @return array
     */
    public static function config(){
        static::$_config = \Config::load(static::$_name."::module");
        return static::$_config;
    }

    /**
     * Gets the seeders configured for the module
     * @return mixed - False if no seeders configured, Array of seeders otherwise
     */
    public static function seeds()
    {
        $seeds = \Config::load(static::$_name."::seed");
        if (count($seeds)>0) {
            return $seeds;
        }
        return false;
    }

    /**
     * Get the fields configured on the module
     * @return array
     */
    public static function fields(){
        $model = static::model(true);
        return $model::fields();
    }
    /**
     * Get the relationships configured on the module
     * @return array
     */
    public static function relationships(){
        $model = static::model(true);
        return $model::relationships();
    }

    /**
     * Get the models for this module
     * @param mixed $model - False, gets all Models. True, gets primary model. ModelName returns specific model
     * @return array|string - Either array of Model Name, or the primary Model name
     */
    public static function model($model = false){
        $module = static::$_name;
        if (count($models = static::$_models)>0){
            if ($model===true||count($models)===1){
                $models = "$module\\Model\\".$models[0];
            }else {
                foreach ($models as $key => $modelName) {
                    if ($model === $modelName) {
                        $models = "$module\\Model\\$modelName";
                        break;
                    }
                    $models[$key] = "$module\\Model\\$modelName";
                }
            }
        }else{
            $models = "$module\\Model\\$module";
        }
        return $models;
    }
    /**
     * Get the views configured on the module
     * @return array
     */
    public static function views($view=''){
        $views = \Config::load(static::$_name."::views");
        if ($view!==""){
            return $views[$view];
        }
        return $views;
    }

    /**
     * Format Model results based on a view or to a standard array for passing via JSON
     * @param $results
     * @return array
     */
    protected static function formatResult($results,$view=null){
        if ($view==null){
            $view = \Input::get('view');
        }
        $newResult = array();
        if (isset($view)){
            $viewConfig = static::views($view);
            $c = 0;
            foreach($results as $key => $model){
                if (is_object($model)){
                    $modelArray = $model->to_array();
                }else{
                    $modelArray = $model;
                }
                $newResult[] = array();
                foreach($viewConfig as $returnKey => $modelKey){
                    $newResult[$c][$returnKey] = $modelArray[$modelKey];
                }
                $c++;
            }
        }else{
            if (is_array($results)) {
                foreach ($results as $key => $model) {
                    if (is_object($model)) {
                        $newResult[] = $model->to_array();
                    } else {
                        $newResult[] = $model;
                    }
                }
            }else{
                if (is_object($results)){
                    $newResult = $results->to_array();
                }else{
                    $newResult = $results;
                }
            }
        }
        return $newResult;
    }
    //Static CRUD Methods
    /**
     * Create a record for the module
     * @param Module $record (optional) - If not passed POSTED Json data is used
     * @return Model
     */
    public static function create(Module $record = null){
        $model = static::model(true);
        if ($record==null) {
            $record = $model::forge(\Input::json());
        }else{
            $properties = get_object_vars($record);
            $record = $model::forge($properties);
        }
        $record->save();
        return $record;
    }

    /**
     * @param $id
     * @param array $properties
     * @return Model
     */
    public static function update($id,array $properties){
        $model = static::model(true);
        $record = $model::find($id);
        foreach (\Input::json() as $key => $value) {
            if (!($key == "id" || $key == "date_created" || $key == "date_modified" || $key == "deleted_at" || $key == "created_by" )) {
                $properties[$key] = $value;
            }
        }
        $record->set($properties);
        $record->save();
        return $record;
    }

    /**
     * Get a record
     * @param string $id - Not specifying will get all
     * @return array
     */
    public static function get($id="all"){
        $model = static::model(true);
        $records = $model::find($id);
        $records = static::formatResult($records);
        return $records;
    }

    /**
     * Delete a record based on ID
     * @param $id
     * @return Deleted Model
     */
    public static function delete($id){
        $model = static::model(true);
        $record = $model::find($id);
        $record->delete();
        return $record;
    }

    /**
     * Filter a Module based on filter array
     * @param array $filters
     * @return array = [
     *      int 'total' => number of total records,
     *      array 'records' => the records queried for
     *      int 'page' => current page number
     * ]
     */
    public static function filter(array $filters = array()){
        \Log::debug("Filter Method");
        $model = static::model(true);
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
        $offset = \Input::param('offset')||1;

        $total = $query->count();
        $results = $query->limit(20)->offset($offset)->get();
        $records = static::formatResult($results);
        return array(
            'total' => $total,
            'records' => $records,
            'page' => ($offset/20)
        );
    }

    /**
     * Relate a record to another
     * @param $record_id
     * @param $relationship
     * @param $related_id
     * @return bool
     */
    public static function relate($record_id,$relationship,$related_id){
        $model = static::model(true);
        $Relationship = $model::relations($relationship);
        if ($Relationship!==false){
            $relationshipModel = $Relationship->__get('model_to');
            if (get_parent_class($relationshipModel) == 'Model\Relationship'){
                //Many to Many relationship handling
                $foreignKey = $Relationship->__get('key_to');
                $relatedKey = substr($relationship, 0, -1)."_id";
                $record = $relationshipModel::query()->where($foreignKey,$record_id)->where($relatedKey,$related_id)->get_one();
                if ($record==null){
                    $relationshipObject = $relationshipModel::forge(array(
                        "$foreignKey" => $record_id,
                        "$relatedKey" => $related_id
                    ));
                    $relationshipObject->save();
                    return true;
                }else{
                    \Log::info("Relationship between $record_id and $related_id for Relationship $relationship, already exists.");
                }
            }else{
                $record = $model::find($record_id,array('related'=>array($relationship)));
                $exists = false;
                foreach ($record->{$relationship} as $key => $model) {
                    if ($related_id == $model->id) {
                        $exists = true;
                        \Log::debug("Relationship between $record_id and $related_id for Relationship $relationship, already exists.");
                    }
                }
                if (!$exists) {
                    $record->{$relationship}[] = $relationshipModel::find($related_id);
                    $record->save();
                    return true;
                }
            }
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::Relate");
        }
        return false;
    }

    /**
     * Gets the Model(s) from the related module
     * @param $record_id
     * @param $relationship
     * @param string $related_id
     * @return bool|void
     */
    public static function related($record_id,$relationship,$related_id=""){
        $model = static::model(true);
        $Relationship = $model::relations($relationship);
        if ($Relationship!==false){
            $relationshipModel = $Relationship->__get('model_to');
            if (strpos(get_parent_class($relationshipModel),'Relationship')!==false){
                //Many to Many relationship handling
                $pivotRelationship = substr($relationship, 0, -1);
                $relatedKey = $pivotRelationship."_id";
                $query = $model::query()->related($relationship)->related("$relationship.$pivotRelationship");
                $query->where('id',$record_id);
                if ($related_id!==""){
                    $query->where("$relationship.$relatedKey",$related_id);
                }
                $record = $query->get();
                $records = array();
                foreach($record->{$relationship} as $relateRecord => $relationModel){
                    $records[] = $relationModel->$pivotRelationship;
                }
            }else{
                $query = $model::query()->related($relationship);
                $query->where('id',$record_id);
                if ($related_id!==""){
                    $query->where("$relationship.id",$related_id);
                }
                $record = $query->get();
                $records = $record->{$relationship};
            }
            if (count($records)>0) {
                return static::formatResult($records);
            }
            \Log::debug("No related records found for $record_id".($related_id==""?"":" and $related_id")." on relationship $relationship");
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::Relate");
            return false;
        }
    }


    //Module Object Methods
    function __construct(){
        $this->setupModel();
    }
    protected function setupModel(){
        $model = static::model(true);
        $this->_model = $model;
        $this->model = new $model();
    }
    /**
     * Load a module Record into object
     * @param $resource = Could be a Model\Module object, a GUID Id, or a String of the Record ID
     * @return boolean
     */
    public function load($resource){
        $model = $this->_model;
        if (is_object($resource)){
            if ($resource instanceof \Model\Module){
                $this->model = $resource;
            }else if ($resource instanceof \UNBOXAPI\Data\Util\Guid){
                $id = $resource->get();
                $this->model = $model::find($id);
            }
        }else{
            //assume string ID
            $id = $resource;
            $this->model = $model::find($id);
        }
        return $this->loadFromModel($this->model);
    }

    /**
     * Loads the data from the Model object into $this
     * @param $record - Model\Module object
     * @return boolean
     */
    protected function loadFromModel($record){
        $model = $this->_model;
        $fields = $model::properties();
        foreach ($fields as $field => $definition){
            $this->{$field} = $record->{$field};
        }
        return true;
    }
    //TODO::Build out Save function for module objects
    public function save(){
        if ($this->model->is_new()){

        }
        $this->model->save();
    }

    /**
     * Create a ManyMany or HasMany relationship, and add it to Model relation property
     * @param $relationship
     * @param $related_id
     * @param array $related_properties
     * @return bool
     */
    public function attach($relationship,$related_id,$related_properties = array()){
        if (isset($this->id)){
            $Relationship = $this->model->relations($relationship);
            if ($relationship!==false){
                $relationshipModel = $Relationship->__get('model_to');
                if (!$this->model->is_fetched($relationship)){
                    $this->model->get($relationship);
                }
                $exists = false;
                if (strpos(get_parent_class($relationshipModel), "Relationship") !== false) {
                    $key_to = $Relationship->__get('key_to');
                    $foreignKey = $key_to[0];
                    //TODO::Find something better than this HACK.
                    $relatedKey = substr($relationship, 0, -1) . "_id";
                    foreach ($this->model->{$relationship} as $key => $model) {
                        if (($this->id == $model->{$foreignKey})&&($related_id==$model->{$relatedKey})) {
                            $exists = true;
                            \Log::debug("Relationship between {$this->id} and $related_id for Relationship $relationship, already exists.");
                        }
                    }
                    if (!$exists) {
                        $properties = array(
                            "$foreignKey" => $this->id,
                            "$relatedKey" => $related_id
                        );

                        $properties = array_merge($properties, $related_properties);
                        $relatedObject = $relationshipModel::forge($properties);
                        $this->model->{$relationship}[] = $relatedObject;
                        return $relatedObject;
                    }
                } else {
                    //classic many to many relationship
                    $relatedObject = $relationshipModel::find($related_id);
                    $this->model->{$relationship}[] = $relatedObject;
                    return $relatedObject;
                }
            }else{
                \Log::error("Invalid relationship [$relationship] provided for attach.");
            }
        }else{
            \Log::error("Cannot relate record to non-saved record");
        }
        return false;
    }

    /**
     * Detach a ManyMany or HasMany relationship
     * @param $relationship
     * @param $related_id
     * @return bool
     */
    public function detach($relationship,$related_id){
        if (isset($this->id)&&$this->model->is_new()){
            $Relationship = $this->model->relations($relationship);
            if ($Relationship!==false){
                if (!$this->model->is_fetched($relationship)) {
                    $this->model->get($relationship);
                }
                foreach ($this->model->{$relationship} as $key => $model) {
                    if ($model->id == $related_id) {
                        unset($this->model->{$relationship}[$key]);
                        return true;
                    }
                }
                \Log::info("No relationship to $related_id for relationship $relationship");
            }else{
                \Log::error("Invalid relationship [$relationship] provided for detach.");
            }
        }else{
            \Log::error("Cannot un-relate record on non-saved record");
        }
        return false;
    }
} 