<?php

namespace UNBOXAPI\Box;

abstract class Layout {
    protected static $_name = "";
    protected static $_label = "";
    protected static $_label_plural = "";
    protected static $_link = "";
    protected static $_icon = "";
    protected static $_links = array();
    protected static $_templates;
    protected static $_config;

    public static function metadata(){
        $metadata = array();
        $module = BoxFactory::modulify(get_class());
        if (MetadataManager::cacheEnabled()){
            try{
                $cach_name = $module.".metadata";
                $metadata = \Cache::get($cach_name);
            }catch(\CacheNotFoundException $e){
                \Log::error("Metadata cache for $module not found.");
            }
        }
        if (empty($metadata)){
            $metadata = MetadataManager::generateBoxMetadata($module);
        }
        return $metadata;
    }

}
