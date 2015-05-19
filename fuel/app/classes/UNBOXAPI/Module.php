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
            \Log::debug("Results:". serialize($results));
            foreach($results as $key => $model){
                if (is_object($model)){
                    $modelArray = $model->to_array();
                }else{
                    $modelArray = $model;
                }
                $newResult[] = array();
                foreach($viewConfig as $returnKey => $modelKey){
                    $value = "";
                    if (is_array($modelKey)){
                        foreach($modelKey as $arrayKey => $key){
                            if (array_key_exists($key,$modelArray)){
                                $value .= $modelArray[$key];
                            }else{
                                $value .= $key;
                            }
                        }
                    }else {
                        $value = $modelArray[$modelKey];
                    }
                    $newResult[$c][$returnKey] = $value;
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
    public static function update($id,array $properties = array()){
        $model = static::model(true);
        $record = $model::find($id);
        if (count($properties)==0){
            foreach (\Input::json() as $key => $value) {
                if (!($key == "id" || $key == "date_created" || $key == "date_modified" || $key == "deleted_at" || $key == "created_by" )) {
                    $properties[$key] = $value;
                }
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
        if ($id!=='all'){
            $records = $model::find($id);
        }else{
            $offset = \Input::param("offset")||0;
            $limit = \Config::get("unbox.record_limit")||20;
            $records = $model::query()->limit($limit)->offset($offset)->get();
        }
        $records = static::formatResult($records);
        return $records;
    }

    /**
     * Get records based on related Module and Related ID
     * @param $related_module
     * @param $related_id
     */
    public static function getByRelated($relationship,$related_id,$record_id=""){
        $model = static::model(true);
        $results = array(
            'total' => 0,
            'records' => array(),
            'page' => 1
        );
        $relationship = strtolower($relationship);
        $Relationship = $model::relations($relationship);
        if ($Relationship!==false){
            $records = array();
            $offset = \Input::param("offset") || 0;
            $limit = \Config::get('unbox.record_limit') || 20;
            $total = 0;
            $query = $model::query();

            $relationshipModel = $Relationship->__get('model_to');
            if (strpos(get_parent_class($relationshipModel),'Relationship')!==false){
                $relatedKey = substr($relationship, 0, -1)."_id";
            }else{
                $relatedKey = $Relationship->__get('key_through_to');
            }
            $query->related($relationship,array('where' => array(array($relatedKey, '=', $related_id))));
            if ($record_id!==""){
                $query->where("id",$record_id);
            }
            $total = $query->count();
            $records = $query->limit($limit)->offset($offset)->get();
            if (count($records)>0) {
                $records = static::formatResult($records);
                $results = array(
                    'total' => $total,
                    'records' => $records,
                    'page' => ($offset % $offset)
                );
            }else{
                \Log::debug("No records found in relation to $relationship with $related_id");
            }
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::Relate");
        }
        return $results;
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
    public static function filter(array $filters = array(),$relationship="",$related_id=""){
        $model = static::model(true);
        $results = array(
            'total' => 0,
            'records' => array(),
            'page' => 1
        );
        $fields = static::fields();

        if (count($filters)===0){
            $filters = \Input::param("filters");
        }
        \Log::debug(serialize($fields));
        $offset = \Input::param('offset')||0;
        $limit = \Config::get("unbox.record_limit")||20;
        //TODO::Bug $limit is not getting pulled from Config
        $limit = 20;

        $query = $model::query();
        if ($relationship!==""&&$related_id!==""){
            $Relationship = $model::relations($relationship);
            if ($Relationship!==false){
                $relationshipModel = $Relationship->__get('model_to');
                if (strpos(get_parent_class($relationshipModel),'Relationship')!==false){
                    $relatedKey = substr($relationship, 0, -1)."_id";
                }else{
                    $relatedKey = $Relationship->__get('key_through_to');
                }
                $query->related($relationship);
                $query->where("$relationship.$relatedKey",$related_id);
            }else{
                \Log::error("Invalid relationship passed to Filter. Filtering without Related record $related_id on $relationship");
            }
        }else{
            \Log::error("Relationship and Related ID are both required if filtering on with Related Records");
        }
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

        $total = $query->count();
        \Log::debug("Limit:".$limit);
        $query->rows_limit($limit)->rows_offset($offset);
        $records = $query->get();
        \Log::debug(\DB::last_query());
        if (count($records)>0) {
            $records = static::formatResult($records);
            $results = array(
                'total' => $total,
                'records' => $records,
                'page' => ($offset % 20)
            );
        }else{
            \Log::debug("Filter returned no results");
        }
        return $results;
    }
    /**
     * Relate a record to another (HasMany and ManyMany relationship)
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
     * Get the related Model(s) from the Related Module
     * @param $record_id
     * @param $relationship
     * @param string $related_id
     * @return bool|void
     */
    public static function related($record_id,$relationship,$related_id=""){
        $model = static::model(true);
        $results = array(
            'total' => 0,
            'records' => array(),
            'page' => 1
        );
        $Relationship = $model::relations(strtolower($relationship));
        if ($Relationship!==false){
            /**
             * We build out the query from the other side of relationship
             *  - Get Related Module
             *  - static::getByRelated
             * */
            $class = \UNBOXAPI\Data\Util\Module::classify($relationship);
            $Class = $relationship."\\".$class;
            return $Class::getByRelated(strtolower(static::$_name),$record_id,$related_id);
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::Relate");
        }
        return $results;
    }

    /**
     * Filter the related models on the Related Module
     * @param $record_id
     * @param $relationship
     * @param string $related_id
     * @return array|bool
     */
    public static function filterRelated($record_id,$relationship){
        $model = static::model(true);
        $results = array(
            'total' => 0,
            'records' => array(),
            'page' => 1
        );
        $Relationship = $model::relations(strtolower($relationship));
        if ($Relationship!==false){
            /**
             * We build out the query from the other side of relationship
             *  - Get Related Module
             *  - static::filter
             * */
            $class = \UNBOXAPI\Data\Util\Module::classify($relationship);
            $Class = $relationship."\\".$class;
            return $Class::filter(array(),strtolower(static::$_name),$record_id);
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::Relate");
        }
        return $results;
    }
    /**
     * Get the current Record, including the related models associated with the related Module
     * @param $record_id
     * @param $relationship
     * @param string $related_id
     * @return array|bool
     */
    public static function recordRelated($record_id,$relationship,$related_id=""){
        $model = static::model(true);
        $results = array(
            'total' => 0,
            'records' => array(),
            'page' => 1
        );
        $relationship = strtolower($relationship);
        $Relationship = $model::relations($relationship);
        if ($Relationship!==false){
            $records = array();
            $offset = \Input::param("offset") || 0;
            $limit = \Config::get('unbox.record_limit') || 20;
            $total = 0;
            $query = $model::query();

            $relationshipModel = $Relationship->__get('model_to');
            if (strpos(get_parent_class($relationshipModel),'Relationship')!==false){
                //Many to Many relationship handling
                $pivotRelationship = substr($relationship, 0, -1);
                $relatedKey = $pivotRelationship."_id";
                $query->related($relationship)->related("$relationship.$pivotRelationship");
                $query->where('id',$record_id);
                if ($related_id!==""){
                    $query->where("$relationship.$relatedKey",$related_id);
                }
            }else{
                $query->related($relationship);
                $query->where('id',$record_id);
                if ($related_id!==""){
                    $query->where("$relationship.id",$related_id);
                }
            }
            $total = $query->count();
            $query->rows_limit($limit)->rows_offset($offset);
            $records = $query->get();
            if (count($records)>0){
                $records = static::formatResult($records);
                $results = array(
                    'total' => $total,
                    'records' => $results,
                    'page' => ($offset%20)
                );
            }else{
                \Log::debug("No related records found for $record_id".($related_id==""?"":" and $related_id")." on relationship $relationship");
            }
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::Relate");
        }
        return $results;
    }

    public static function createRelated($record_id,$relationship){
        $model = static::model(true);
        $relationship = strtolower($relationship);
        $Relationship = $model::relations(strtolower($relationship));
        if ($Relationship!==false){
            $relationshipModel = $Relationship->__get('model_to');
            $related_id = false;
            if (strpos(get_parent_class($relationshipModel),'Relationship')!==false){
                //Many to Many relationship handling
                /**
                 * TODO: Finish this function
                 * - Build related record to pass ID to relate function
                 */
            }else{

            }
            if ($related_id!==false) {
                return static::relate($record_id, $relationship, $related_id);
            }
        }else{
            \Log::error("Invalid relationship [$relationship] passed to Module::createRelated");
        }
        return true;
    }

    public static function unrelate($record_id,$relationship,$related_id){
        //TODO::Build this function
        return true;
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
            $relationship = strtolower($relationship);
            $Relationship = $this->model->relations($relationship);
            if ($Relationship!==false){
                $relationshipModel = $Relationship->__get('model_to');
                if (!$this->model->is_fetched($relationship)){
                    $this->model->get($relationship);
                }
                $exists = false;
                if (strpos(get_parent_class($relationshipModel), "Relationship") !== false) {
                    $key_to = $Relationship->__get('key_to');
                    $foreignKey = $key_to[0];
                    $relatedKey = substr($relationship, 0, -1)."_id";
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