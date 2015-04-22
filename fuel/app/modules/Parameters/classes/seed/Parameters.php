<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 12:24 AM
 */

namespace Parameters\seed;


class Parameters extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Parameters';
    protected static $_model = 'Parameters';

    protected static $_records = array(
        array(
            'id' => 'seed_param',
            'data_type' => 'string',
            'api_type' => '',
            'description' => 'IP Address you wish to ping',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'IP Address',
            'created_by' => '1',
            'modified_by' => '1'
        )
    );
    protected static function records(){
        $records = array();
        foreach(static::$_records as $record => $values){
            $dataType = \ParameterTypes\Model\ParameterTypes::findByName($values['data_type'],1);
            if ($dataType!==null){
                $dataType = $dataType->id;
            }
            $values['data_type'] = $dataType;
            $apiType = \ParameterTypes\Model\ParameterTypes::findByName($values['api_type'],2);
            if ($apiType!==null){
                $apiType = $apiType->id;
                $values['api_type'] = $apiType;
            }
            $records[] = $values;
        }
        static::$_records = $records;
        return parent::records();
    }
    protected static $_relationships = array(
        array(
            'id' => 'seed_param',
            'name' => 'entryPoints',
            'related_id' => 'seed_ep_2'
        )
    );
}