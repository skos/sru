<?php

class UFacl
extends UFlib_ClassWithService {
	
	function __call($module, $params) {
		$acl = UFra::shared('UFacl_'.ucfirst($module).'_'.ucfirst($params[0]));
		$method = $params[1];
		unset($params[0], $params[1]);
		return (bool)call_user_func_array(array($acl, $method), $params);
	}
	
}
