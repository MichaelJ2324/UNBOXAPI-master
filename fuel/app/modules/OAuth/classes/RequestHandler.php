<?php

namespace OAuth;

use OAuth2\Server\Request\HandlerInterface;

class RequestHandler implements HandlerInterface{

	/**
	 * @inherit
	 */
	public function getParam($param){
		if (\Request::is_hmvc()){
			if (!empty(\Request::active()->method_params[1])){
				return (isset(\Request::active()->method_params[1][$param])?\Request::active()->method_params[1][$param]:null);
			}
			return null;
		}else {
			return \Input::param($param, NULL);
		}
	}
	/**
	 * @inherit
	 */
	public function getHeader($header){
		return \Input::headers($header,null);
	}

	/**
	 * @inherit
	 */
	public function getMethod(){
		return \Input::method();
	}
	/**
	 * @inherit
	 */
	public function getUri(){
		return \Input::uri();
	}
	/**
	 * @inherit
	 */
	public function getHost(){
		return \Input::server('HTTP_HOST');
	}
	/**
	 * @inherit
	 */
	public function getPort(){
		return \Input::server('SERVER_PORT');
	}

}