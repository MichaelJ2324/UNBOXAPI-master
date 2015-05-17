<?php
namespace Fuel\Tasks;


class Unbox
{

    public static function install(){
        try{
            $Installer = new \UNBOXAPI\Installer(true);
            return \Cli::color($Installer->message,"green");
        }catch(\Exception $ex){
            return \Cli::color("Error Install Failed: ".$ex->getMessage(),"red");
        }
    }
    public static function setupForeignKeys(){
        $installConfig = \Config::load('install');
        if ($installConfig['locked']==true){
            print \Cli::color("Setting up Foreign Keys on Database.\n","blue");
            \UNBOXAPI\Installer::installForeignKeys();
        }
    }
    public static function seed($module='all',$model=null,$relationships=false,$relationshipsOnly=false){
        $installConfig = \Config::load('install');
        if ($model=='null'){
            $model = null;
        }
        if ($relationships=='true'||$relationships=="1"||$relationships==1){
            $relationships = true;
        }
        if ($relationshipsOnly=='true'||$relationshipsOnly=="1"||$relationshipsOnly==1){
            $relationshipsOnly = true;
        }
        if ($installConfig['locked']==true){
            print \Cli::color("Seeding Database.\n","blue");
            \UNBOXAPI\Installer::installSeedData($module,$relationships,$model,$relationshipsOnly);
        }
    }
}