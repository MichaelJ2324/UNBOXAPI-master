<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/15/15
 * Time: 10:20 AM
 */

namespace UNBOXAPI;


class Installer {

    private $configFile = "install";
    private $cacheFile = "database";
    private $base_db_config = array(
        'active' => 'default',
        'default' => array(
            'type'        => 'mysqli',
            'connection'  => array(
                'persistent' => false,
            ),
            'identifier'   => '`',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'enable_cache' => false,
            'profiling'    => false,
            'readonly'     => false,
        ),
        'dbUtil' => array(
            'type'        => 'mysqli',
            'connection'  => array(
                'persistent' => false,
            ),
            'identifier'   => '`',
            'table_prefix' => '',
            'charset'      => 'utf8',
            'enable_cache' => false,
            'profiling'    => false,
            'readonly'     => false,
        ),
    );
    public $config = array();
    public $message = "";

    private $tables = array();
    private $relatedTables = array();
    private $foreignKeys = array();
    private $versionedModules = array();

    function __construct($autoInstall=false)
    {
        $this->config = \Config::load($this->configFile);
        if ($this->config['locked']===true){
            throw new \Exception("Installer is locked. Please set the 'locked' property of the install configuration to false and try again.");
        }
        if ($autoInstall){
            $this->start();
        }
    }
    public function start(){
        print("Setting up database...");
        if ($this->setup_database()){
            print(" SUCCESS!\n");
            //installs Config Table to Database
            print("Installing config table...");
            $this->install_configTable();
            print(" DONE!\n");
            //installs Application Tables
            print("Installing Module Tables...");
            if ($this->install_tables()){
                print(" SUCCESS!\n");
                print("Configuring instance...");
                $this->setup_config();
                print("Done \n");
                print("Locking Configuration...");
                $this->lockInstaller();
                print("Done \n");
                $this->message = "Install Complete";
            }
        }
    }
    private function lockInstaller(){
        $this->config['locked'] = true;
        \Config::save('install',$this->config);
    }
    private function setup_database(){
        $database = $this->config['database']['database'];
        $this->setDatabaseConfig($this->config['database']);
        $DB = new Data\DB\Database($database);
        if (!(Data\DB\Database::create($DB,"dbUtil",true))){
            throw new \Exception("Database not created. Error:" . \DB::error_info());
        }
        \Cache::set($this->cacheFile.".name",$database);
        return true;
    }
    private function setup_config(){
        $unboxConfig = \Config::get('unbox');
        //TODO::Client ID and Secret generation during Installation. Currently using md5'd GUIDS.
        $unboxConfig['client']['id'] = md5(\UNBOXAPI\Data\Util\Guid::make());
        $unboxConfig['client']['secret'] = md5(\UNBOXAPI\Data\Util\Guid::make());
        \Config::save('unbox.php',$unboxConfig);
    }
    private function setDatabaseConfig($dbConfig){
        $currentConfig = $this->base_db_config;
        $currentConfig['default']['connection'] = array_merge($currentConfig['default']['connection'],$dbConfig);
        unset($dbConfig['database']);
        $currentConfig['dbUtil']['connection'] = array_merge($currentConfig['dbUtil']['connection'],$dbConfig);
        $dbConfigFile = APPPATH."config/db.php";
        $prodConfigFile = APPPATH."config/production/db.php";
        $stageConfigFile = APPPATH."config/staging/db.php";
        $testConfigFile = APPPATH."config/test/db.php";
        $devConfigFile = APPPATH."config/development/db.php";
        \Config::save($dbConfigFile,$currentConfig);
        \Config::save($prodConfigFile,$currentConfig);
        \Config::save($stageConfigFile,$currentConfig);
        \Config::save($testConfigFile,$currentConfig);
        \Config::save($devConfigFile,$currentConfig);
    }
    private function install_configTable(){
        $attributes = array(
            'fields' => array(
                'identifier' => array(
                    'type' => 'char',
                    'null' => false,
                    'constraint' => 100
                ),
                'config' => array(
                    'type' => 'longtext',
                    'null' => false,
                ),
                'hash' => array(
                    'type' => 'char',
                    'null' => false,
                    'constraint' => 13
                )
            ),
            'primary_keys' => array(),
            'foreign_keys' => array()
        );
        Data\DB\Table::create("config",$attributes);
    }
    private function install_tables(){
        $modules = \Module::loaded();
        foreach ($modules as $module=>$path){
            if (substr($module, -1) === "s"){
                $class = substr($module,0,-1);
            }else{
                $class = $module;
            }
            if ($class !== 'Version') {
                $Class = "\\$module\\$class";
                if (get_parent_class($Class) == 'UNBOXAPI\Module') {
                    $Model = $Class::model();
                    $options = $Class::options();
                    if ($options['versioning']==true){
                        $this->versionedModules[] = $module;
                    }
                    $Table = new Data\DB\Table($Model);
                    $this->tables[$Table->name] = $Table;
                    if (!(Data\DB\Table::create($Table))) {
                        throw new \Exception("Table " . $Table->name . " not created. Error:" . \DB::error_info());
                    }
                    if (is_array($Table->foreignKeys)) {
                        foreach ($Table->foreignKeys as $key => $definition) {
                            if ($definition['added'] == false) {
                                $foreignKeys[$Table->name][] = $definition;
                            }
                        }
                    }
                    $relatedTables = $Table->getRelatedTables();
                    if (is_array($relatedTables)) {
                        $this->relatedTables = array_merge_recursive($this->relatedTables, $relatedTables);
                    }
                    unset($relatedTables);
                    unset($Model);
                    unset($Table);
                    sleep(1);
                }else{
                    if ($class=='Oauth'){
                        $models = $Class::models();
                        foreach($models as $modelName){
                            $Model = "$module\\Model\\$modelName";
                            $Table = new Data\DB\Table($Model);
                            $this->tables[$Table->name] = $Table;
                            if (!(Data\DB\Table::create($Table))) {
                                throw new \Exception("Table " . $Table->name . " not created. Error:" . \DB::error_info());
                            }
                            if (is_array($Table->foreignKeys)) {
                                foreach ($Table->foreignKeys as $key => $definition) {
                                    if ($definition['added'] == false) {
                                        $foreignKeys[$Table->name][] = $definition;
                                    }
                                }
                            }
                            $relatedTables = $Table->getRelatedTables();
                            if (is_array($relatedTables)) {
                                $this->relatedTables = array_merge_recursive($this->relatedTables, $relatedTables);
                            }
                            unset($Model);
                            unset($Table);
                            unset($relatedTables);
                            sleep(1);
                        }
                    }
                }
            }
        }
        //versioning
        $Class = "\\Versions\\Version";
        foreach($this->versionedModules as $key => $module){
            $Model = $Class::model($module);
            $Table = new Data\DB\Table($Model);
            $this->tables[$Table->name] = $Table;
            if (!(Data\DB\Table::create($Table))) {
                throw new \Exception("Table " . $Table->name . " not created. Error:" . \DB::error_info());
            }
            if (is_array($Table->foreignKeys)) {
                foreach ($Table->foreignKeys as $key => $definition) {
                    if ($definition['added'] == false) {
                        $foreignKeys[$Table->name][] = $definition;
                    }
                }
            }
            $relatedTables = $Table->getRelatedTables();
            if (is_array($relatedTables)) {
                $this->relatedTables = array_merge_recursive($this->relatedTables, $relatedTables);
            }
            unset($relatedTables);
            unset($Model);
            unset($Table);
            sleep(1);
        }
        //Oauth

        //related tables
        foreach($this->relatedTables as $tableName=>$properties){
            $RelateTable = new Data\DB\RelateTable();
            $RelateTable->name = $tableName;
            if (strpos($RelateTable->name,"oauth")!==FALSE){
                $softDelete = false;
                $user_fields = false;
            }else{
                $softDelete = true;
                $user_fields = true;
            }
            $RelateTable->setFields($properties['fields'],$softDelete,$user_fields);
            $RelateTable->primaryKeys = array('id');
            $RelateTable->setForeignKeys($properties['foreign_keys']);
            $this->tables[$RelateTable->name] = $RelateTable;

            if (!(Data\DB\Table::create($RelateTable))){
                throw new \Exception("Table ".$RelateTable->name." not created. Error:".\DB::error_info());
            }
            unset($RelateTable);
            sleep(1);
        }
        \Cache::set($this->cacheFile.".tables",$this->tables);
        return true;
    }
    public static function installForeignKeys(){
        try{
            $tables = \Cache::get("database.tables");
            foreach($tables as $tableName => $TableObject){
                if (is_object($TableObject)) {
                    if (count($TableObject->foreignKeys) > 0) {
                        print("Adding foreign keys for $tableName...");
                        $addedCount = 0;
                        foreach ($TableObject->foreignKeys as $foreignKey => $definition) {
                            if ($definition['added'] !== true) {
                                if (!(Data\DB\Table::addForeignKey($tableName, $definition))) {
                                    print \Cli::color("Failed to add foreign key: {$definition['key']} on $tableName. Review Log for further details. Last error:" . \DB::error_info() . "\n", "red");
                                } else {
                                    $tables[$tableName]->foreignKeys[$foreignKey]['added'] = true;
                                    $addedCount++;
                                    \Cache::set("database.tables", $tables);
                                }
                            }
                        }
                        print(" $addedCount Keys added\n");
                    }
                }
            }
            print \Cli::color("All Foreign Keys added!\n","green");
        }catch(\Exception $ex){
            print \Cli::color("Exception: (".$ex->getCode().") ".$ex->getMessage(),"red");
            return false;
        }
    }
    public static function installSeedData($examples = false){
        try{
            $modules = \Module::loaded();
            foreach ($modules as $module=>$path){
                if (substr($module, -1) === "s"){
                    $class = substr($module,0,-1);
                }else{
                    $class = $module;
                }
                $Class = "\\$module\\$class";
                if (get_parent_class($Class) !== 'UNBOXAPI\Layout') {
                    $ModelNS = "\\$module\\Seed\\";
                    $seedModels = $Class::seeds();
                    foreach ($seedModels as $modelName) {
                        print("Seeding $modelName in Module $module\n");
                        $SeedModel = $ModelNS . $modelName;
                        $SeedModel::run();
                    }
                }
            }
            print \Cli::color("All seed data entered!\n","green");
        }catch(\Exception $ex){
            print \Cli::color("Exception: (".$ex->getCode().") ".$ex->getMessage(),"red");
            return false;
        }
    }
    public static function seedModule($module,$model){
        try{
            if (substr($module, -1) === "s"){
                $class = substr($module,0,-1);
            }else{
                $class = $module;
            }
            $Class = "\\$module\\$class";
            $ModelNS = "\\$module\\Seed\\";
            $seedModels = $Class::seeds();
            if (in_array($model,$seedModels)) {
                foreach ($seedModels as $modelName) {
                    print("Seeding $modelName in Module $module\n");
                    $SeedModel = $ModelNS . $modelName;
                    $SeedModel::run();
                }
                print \Cli::color("All seed data entered!\n", "green");
            }else{
                print \Clie::color("Model has not seeder defined.\n","red");
            }
        }catch(\Exception $ex){
            print \Cli::color("Exception: (".$ex->getCode().") ".$ex->getMessage(),"red");
            return false;
        }
    }
}