<?php

namespace HttpMethods;

use UNBOXAPI\Box\Module;

class HttpMethod extends Module {

	protected static $_canisters = array(
		'HttpMethods'
	);

    protected static $_available_methods = array(
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'TRACE',
        'HEAD',
        'OPTIONS'
    );

    public $method;

    function __construct($args){
        unset($this->date_created);
        unset($this->date_modified);
        unset($this->created_by);
        unset($this->modified_by);
        unset($this->name);
        unset($this->deleted);
        parent::__construct();
    }

    public static function get($id=""){
        $methods = array();
        $count=1;
        if ($id!==""){
            $methods[] = array(
                'id' => $id,
                'method' => static::$_available_methods[($id-1)]
            );
        }else{
            foreach(static::$_available_methods as $method){
                $methods[] = array(
                    'id' => $count,
                    'method' => $method
                );
                $count++;
            }
        }
        return $methods;
    }
    public static function filter(array $filters = array(),$relationship="",$related_id=""){
        if (count($filters)==0){
            $filters = \Input::param("filters");
        }
        $methods = static::get();
        $name = "";
        foreach($filters as $field => $value){
            if ($field=="name"){
                $name = $value;
                break;
            }
        }
        if ($name!==""){
            $filteredMethods = array();
            foreach($methods as $key => $record){
                if (strpos($record['method'],$name)!==false){
                    $filteredMethods[] = $methods[$key];
                }
            }
            $methods = $filteredMethods;
        }
        $records = static::formatResult($methods);

        return array(
            'total' => count($records),
            'records' => $records,
            'page' => 1
        );
    }
} 