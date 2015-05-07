<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/6/15
 * Time: 11:59 AM
 */

namespace UNBOXAPI\Data\Util;


class Module {

    public static function classify($module){
        if (substr($module, -1) === "s"){
            return substr($module,0,-1);
        }else{
            return $module;
        }
    }

}