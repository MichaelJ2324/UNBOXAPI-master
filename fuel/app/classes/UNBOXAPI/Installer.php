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
		'auth' => array(
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
        print("Setting up databases...");
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
        $this->setDatabaseConfig($this->config['database']);
		$dbConfig = \Config::load("db",null,true);
		\Config::set("db",$dbConfig);
        $DB = new Data\DB\Database($dbConfig['default']['connection']['database']);
        if (!(Data\DB\Database::create($DB,"dbUtil",true))){
            throw new \Exception("Database not created. Error:" . \DB::error_info());
        }
		unset($DB);
		if (isset($dbConfig['auth'])){
			$DB = new Data\DB\Database($dbConfig['auth']['connection']['database']);
			if (!(Data\DB\Database::create($DB,"dbUtil",true))){
				throw new \Exception("Database not created. Error:" . \DB::error_info());
			}
		}
        return true;
    }
    private function setup_config(){
        $unboxConfig = \Config::get('unbox');
		$unboxConfig['oauth']['server']['host'] = $this->config['auth']['host'];
        $unboxConfig['oauth']['client']['id'] = \OAuth2\Server\Util\SecureKey::generate();
        $unboxConfig['oauth']['client']['secret'] = \OAuth2\Server\Util\SecureKey::generate();
		$unboxConfig['oauth']['client']['name'] = "Unbox API Web Application";
        \Config::save('unbox.php',$unboxConfig);

		$cacheConfig = \Config::get('cache');
    }
    private function setDatabaseConfig($dbConfig){
		$config_locations = array(
			'default',
			'production',
			'staging',
			'test',
			'development');
		if (isset($dbConfig['all'])){
			foreach($config_locations as $location){
				if ($location=='default'){
					$location=APPPATH."config/db.php";
				}else{
					$location=APPPATH."config/".$location."/db.php";
				}
				$config = $this->buildDbConfig($dbConfig['all']);
				\Log::debug("Saving database config to: $location");
				\Config::save($location,$config);
			}
		}else{
			foreach($dbConfig as $location => $databaseConfig){
				if (in_array($location,$config_locations)) {
					if ($location == 'default') {
						$location = APPPATH."config/db.php";
					} else {
						$location = APPPATH."config/".$location."/db.php";
					}
					$config = $this->buildDbConfig($databaseConfig);
					\Log::debug("Saving database config to: $location");
					\Config::save($location, $config);
				}
			}
		}
    }
	private function buildDbConfig($config){
		$baseConfig = $this->base_db_config;
		$baseConfig['default']['connection'] = array_merge($baseConfig['default']['connection'],$config);
		if ($this->config['auth']['host']=='localhost'){
			$authConfig = $config;
			$authConfig['database'] = $config['database']."_auth";
			$baseConfig['auth']['connection'] = array_merge($baseConfig['auth']['connection'],$authConfig);
		}else{
			unset($baseConfig['auth']);
		}
		unset($config['database']);
		$baseConfig['dbUtil']['connection'] = array_merge($baseConfig['dbUtil']['connection'],$config);
		return $baseConfig;
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
            $class = \UNBOXAPI\Data\Util\Module::classify($module);
            $Class = "\\$module\\$class";
            if (class_exists($Class)) {
                if (get_parent_class($Class) == 'UNBOXAPI\Module') {
                    $models = $Class::model();
                    $config = $Class::config();
                    if (isset($config['versioning'])) {
                        if ($config['versioning'] === true) {
                            $this->versionedModules[] = $module;
                        }
                    }
                    if (!is_array($models)) {
                        $models = array($models);
                    }
                    foreach ($models as $model) {
                        $Table = new Data\DB\Table($model);
                        $this->tables[$Table->name] = $Table;
                        if (!(Data\DB\Table::create($Table))) {
                            throw new \Exception("Table " . $Table->name . " not created. Error:" . serialize(\DB::error_info()));
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
                    }
                }
            }
        }

        //related tables
        foreach($this->relatedTables as $tableName=>$properties){
            $RelateTable = new Data\DB\RelateTable();
            $RelateTable->name = $tableName;
            $RelateTable->setFields($properties['fields']);
            $RelateTable->primaryKeys = array('id');
            $RelateTable->setForeignKeys($properties['foreign_keys']);
			$RelateTable->setConnection($properties['connection'][0]);
            $this->tables[$RelateTable->name] = $RelateTable;

            if (!(Data\DB\Table::create($RelateTable))){
                throw new \Exception("Table ".$RelateTable->name." not created. Error:".serialize(\DB::error_info()));
            }
            unset($RelateTable);
        }
        \Cache::set($this->cacheFile.".tables",$this->tables,600);
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
                                    print \Cli::color("Failed to add foreign key: {$definition['key']} on $tableName. Review Log for further details. Last error: \n", "red");
                                    print_r(\DB::error_info());
                                } else {
                                    $tables[$tableName]->foreignKeys[$foreignKey]['added'] = true;
                                    $addedCount++;
                                    \Cache::set("database.tables", $tables,600);
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
    public static function installSeedData($module='all',$relationships=false,$model=null,$relationshipsOnly=false){
        try{
            if ($module!=='all'){
                $modules = array(
                    "$module" => "singular"
                );
            }else{
                $modules = \Module::loaded();
            }
            foreach ($modules as $module=>$path){
                $class = \UNBOXAPI\Data\Util\Module::classify($module);
                $Class = "\\$module\\$class";
                if (class_exists($Class)) {
                    if (get_parent_class($Class) !== 'UNBOXAPI\Layout') {
                        $ModelNS = "\\$module\\Seed\\";
                        $seedModels = $Class::seeds();
                        if ($seedModels !== false) {
                            if ($model !== null) {
                                if (!in_array($model, $seedModels)) {
                                    print \Cli::color("Model has no seeder defined.\n", "red");
                                    break;
                                }
                                $seedModels = array($model);
                            }
                            foreach ($seedModels as $modelName) {
                                print("Seeding $modelName in Module $module\n");
                                $SeedModel = $ModelNS . $modelName;
                                $Seeder = new $SeedModel();
                                $Seeder->seed($relationships, $relationshipsOnly);
                            }
                        }
                    }
                }
            }
            print \Cli::color("All seed data entered!\n","green");
        }catch(\Exception $ex){
            print \Cli::color("Exception: (".$ex->getCode().") ".$ex->getMessage(),"red");
            return false;
        }
    }
}