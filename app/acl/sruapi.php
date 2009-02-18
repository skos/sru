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
			return true;
		} catch (UFex_Core_DataNotFound $e) {
			return false;
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
	}
}
