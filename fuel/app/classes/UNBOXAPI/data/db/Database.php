<?php
namespace UNBOXAPI\Data\DB;

class Database {

    protected static $if_not_exists = false;
    protected static $charset = 'utf8';
    protected static $db = 'default';

    public $name;

    function __construct($name=""){
        if ($name!==""){
            $this->name = $name;
        }
    }

    public static function create($database,$connection = null,$overwrite = false){
        if ($connection!==null){
            //\DBUtil::set_connection($connection);
            static::$db = $connection;
        }
        if (is_object($database)){
            $db = $database->name;
        }else{
            $db = $database;
        }
        try {
            \Log::info("Creating Database: ".$db);
            return \DBUtil::create_database($db, self::$charset, self::$if_not_exists, static::$db);
        }catch(\Database_Exception $ex){
            \Log::error("Database creation failed. Exception:".$ex->getMessage());
            if ($overwrite) {
                \Log::info("Overwrite is true. Dropping Database: ".$db);
                \DBUtil::drop_database($db,self::$db);
                \Log::info("Creating Database: ".$db);
                return \DBUtil::create_database($db, self::$charset, self::$if_not_exists, static::$db);
            }else{
                return false;
            }
        }catch(\Exception $ex){
            \Log::error("Failed to create Database. Exception: (".$ex->getCode().") ".$ex->getMessage());
            return false;
        }
    }

}