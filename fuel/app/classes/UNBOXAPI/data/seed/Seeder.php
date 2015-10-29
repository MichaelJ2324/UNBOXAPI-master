<?php

namespace UNBOXAPI\Data\Seed;

use \UNBOXAPI\Box\Factory as BoxFactory;

abstract class Seeder {

    protected static $_module;
    protected static $_model;
    /**
     * @var = array(
     *      'model_field' => 'value'
     * )
     */
    protected static $_records = array();
    /**
     * @var = array(
     *      'id' => 'Model ID',
     *      'name' => 'Relationship Name',
     *      'related_id' => 'Related ID',
     *      'related_properties' => array(
     *          'field' => 'value'
     *      )
     * )
     */
    protected static $_relationships = array();

    private $module;
    private $model;

    protected static function model(){
        if (!isset(static::$_model)){
            static::$_model = true;
        }
        return static::$_model;
    }
    protected static function records(){
        return static::$_records;
    }
    protected static function relationships(){
        return static::$_relationships;
    }
    public function __construct(){
        $this->module = static::$_module;
        $this->model = static::model();
    }
    public function seed($relationships=false,$relationshipsOnly=false){
        if (!$relationshipsOnly) {
            $this->insert();
        }
        if ($relationships) {
            $this->relate();
        }
    }
    public function insert(){
        $records = static::records();
		foreach ($records as $record => $values){
            print("\n Instantiating ".$this->module." with Model ".$this->model);
			$Record = BoxFactory::build($this->module,array('model' => $this->model));
			$Record->setProperties($values);
			$Record->save();
			unset($Record);
		}
    }
    public function relate(){
        $relationships = static::relationships();
        $count = 1;
        if (count($relationships)>0) {
            $previousId = "";
            foreach ($relationships as $relationship => $properties) {
                if ($properties['id']!==$previousId){
                    $previousId = $properties['id'];
					$Record = BoxFactory::build($this->module,array('model' => $this->model));
					$Record->load($properties['id']);
                }
                //Get relationship object
                if (!isset($properties['related_properties'])) {
                    $properties['related_properties'] = array();
                }
				$Record->attach($properties['name'], $properties['related_id'], $properties['related_properties']);
				$Record->save();
                $count++;
            }
        }
    }
}