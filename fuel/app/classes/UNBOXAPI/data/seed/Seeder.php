<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/29/15
 * Time: 2:16 PM
 */

namespace UNBOXAPI\Data\Seed;


class Seeder {

    protected static $_module;
    protected static $_records;
    protected static $_relationships;

    public static function run(){
        $module = static::$_module;
        $module = "$module\\$module";

        //TODO: Build OAuth seeder
    }
}