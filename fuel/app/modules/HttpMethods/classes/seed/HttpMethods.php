<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 4/3/15
 * Time: 5:20 PM
 */

namespace HttpMethods\Seed;


use UNBOXAPI\Data\Seed\Seeder;

class HttpMethods extends Seeder{

    protected static $_module = 'HttpMethods';

    public static function records(){
        $Class = static::$_module."\\HttpMethod";
        static::$_records = $Class::get();
        return parent::records();
    }

}