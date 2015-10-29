<?php

namespace Applications;

use UNBOXAPI\Box\Module;

class Application extends Module{

    protected static $_canisters = array(
        'Applications',
        'Apis'
    );

    public $version_id;
    public $description;

    public static function apis($id){
        $application = new Model\Applications();
        return $application->getAPIs($id);
    }
    public static function entrypoints($id){
        $application = new Model\Applications();
        return $application->getEntrypoints($id);
    }
} 