<?
/**
 * sprawdzanie uprawnien przy wyowalniach przez api
 */
class UFacl_SruApi_Admin
extends UFacl_SruApi {
	
	public function show() {
		return $this->_loggedIn();
	}
	
	public function delete() {
		return $this->_loggedIn();
	}
}
