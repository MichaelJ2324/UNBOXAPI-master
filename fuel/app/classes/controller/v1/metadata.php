<?php

namespace Controller\V1;

use UNBOXAPI\Data\Metadata\Manager as MetadataManager;

class Metadata extends Rest{

    protected $auth_required = false;

    public function get_index($module="",$action="",$related_module="",$related_id=""){
        try {
			$metadata = MetadataManager::systemMetadata();
			$response = array();
			foreach($metadata as $key => $value){
			    $response[] = array(
			        'key' => $key,
			        'value' => $value
                );
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