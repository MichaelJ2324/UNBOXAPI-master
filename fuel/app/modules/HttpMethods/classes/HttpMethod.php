<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 1:45 AM
 */

namespace HttpMethods;


class HttpMethod extends \UNBOXAPI\Module{

    protected static $_name = "HttpMethods";
    protected static $_type = "Module";
    protected static $_enabled = true;

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

    function __construct(){
        unset($this->date_created);
        unset($this->date_modified);
        unset($this->created_by);
        unset($this->modified_by);
        unset($this->name);
        unset($this->deleted);
    }

    public static function get($id=""){
        $methods = array();
        $count=1;
        foreach(static::$_available_methods as $method){
            $methods[] = array(
                'id' => $count,
                'method' => $method
            );
            $count++;
        }
        return $methods;
    }
} 