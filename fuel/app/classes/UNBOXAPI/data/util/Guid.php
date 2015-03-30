<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/29/15
 * Time: 4:07 PM
 */

namespace UNBOXAPI\Data\Util;


class Guid {

    private $guid;

    function __construct(){
        $this->guid = static::create();
    }
    protected static function create(){
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    public static function make($object = false){
        if ($object){
            return new Guid();
        }else{
            return static::create();
        }
    }
    public function get(){
        return $this->guid;
    }
}