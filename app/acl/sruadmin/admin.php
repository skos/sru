<?
/**
 * sprawdzanie uprawnien administratora
 */
class UFacl_SruAdmin_Admin
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}
	
	public function login() {
		return !$this->_loggedIn();
	}
	public function add() {
		return $this->_loggedIn();
	}	
	
	public function logout() {
		return $this->_loggedIn();
	}
}
