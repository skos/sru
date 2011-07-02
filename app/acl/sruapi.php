<?php
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruApi
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getFromHttp();

			$serv = $this->_srv->get('req')->server;
			if($serv->is('HTTP_X_FORWARDED_FOR') && $serv->HTTP_X_FORWARDED_FOR != '' ) {
				$bean->lastLoginIp = $serv->HTTP_X_FORWARDED_FOR;
			} else {
				$bean->lastLoginIp =  $serv->REMOTE_ADDR;
			}
			$bean->lastLoginAt = NOW;
			$bean->save();

			return true;
		} catch (UFex_Core_DataNotFound $e) {
			return false;
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
	}
}
