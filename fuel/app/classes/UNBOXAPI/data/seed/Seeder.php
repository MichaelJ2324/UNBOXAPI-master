<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/29/15
 * Time: 2:16 PM
 */

namespace UNBOXAPI\Data\Seed;


abstract class Seeder {

    protected static $_module;
    protected static $_model;
	protected static $_eav = false;
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
    private $Model;
	private $eav;

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
		$this->eav = static::$_eav;
    }
    public function seed($relationships=false,$relationshipsOnly=false){
        if (!$relationshipsOnly) {
            $this->insert($this->Model);
        }
        if ($relationships) {
            $this->relate();
        }
    }
    public function insert($model){
        $records = static::records();
		$class = \UNBOXAPI\Data\Util\Module::classify($this->module);
		$Module = $this->module."\\".$class;
		foreach ($records as $record => $values){
			$Record = new $Module();
			$Record->setProperties($values);
			$Record->save();
		}
    }
    public function relate(){
        $relationships = static::relationships();
        $class = \UNBOXAPI\Data\Util\Module::classify($this->module);
        $Module = $this->module."\\".$class;
        $count = 1;
        if (count($relationships)>0) {
            $previousId = "";
            foreach ($relationships as $relationship => $properties) {
                if ($properties['id']!==$previousId){
                    $previousId = $properties['id'];
                    $Module = new $Module();
                    $Module->load($properties['id']);
                }
                //Get relationship object
                if (!isset($properties['related_properties'])) {
                    $properties['related_properties'] = array();
                }
                $Module->attach($properties['name'], $properties['related_id'], $properties['related_properties']);
                $Module->save();
                $count++;
            }
        }
    }
}