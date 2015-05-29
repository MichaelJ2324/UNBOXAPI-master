<?php

namespace Controller\V1;


class Metadata extends Rest{

    protected $auth_required = false;

    public function get_index($module="",$action="",$related_module="",$related_id=""){
        try {
            $response = "";
            if ($module == "" || !isset($module)) {
                $response = \UNBOXAPI\Metadata::get_metaData();
            } else {
                if (\Module::exists($module) !== false) {
                    if (substr($module, -1) === "s") {
                        $class = substr($module, 0, -1);
                    } else {
                        $class = $module;
                    }
                    $Class = "\\$module\\$class";
                    $response = $Class::metadata();
                } else {
                    if ($module == 'Config') {
                        $response = \UNBOXAPI\Metadata::get_config();
                    } else {
                        throw new \Exception("Module does not exist", 500);
                    }
                }
            }
            return $this->response(
                $response
            );
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: " . $e->getMessage() . "\n",
                )
            );
        }
    }

    public function post_index($id="",$action="",$related_module="",$related_id=""){
        try
        {
            throw new \Exception("Cannot POST to Metadata.");
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function put_index($id,$action="",$related_module="",$related_id=""){
        try
        {
            throw new \Exception("Cannot PUT to Metadata.");
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function delete_index($id,$action="",$related_module="",$related_id=""){
        try
        {
            throw new \Exception("Cannot DELETE Metadata.");
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }

}