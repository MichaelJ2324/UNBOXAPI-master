<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 8:24 AM
 */

namespace Applications;


use UNBOXAPI\Module;

class Application extends \UNBOXAPI\Module{
    protected static $_name = "Applications";
    protected static $_label = "Application";
    protected static $_label_plural = "Applications";

    public $version_id;
    public $description;

    public static function apis($id){
        $application = new Model\Applications();
        return $application->getAPIs($id);
    }
    public static function entryPoints($id){
        $application = new Model\Applications();
        return $application->getEntryPoints($id);
    }
} 