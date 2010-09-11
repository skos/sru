<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruWalet_User
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authWaletAdmin');
	}

	public function edit() {
		return $this->_loggedIn();
	}

	public function add() {
		return $this->_loggedIn();
	}

	public function del() {
		return $this->_loggedIn();
	}
	
	public function login() {
		return !$this->_loggedIn();
	}
	
	public function logout() {
		return $this->_loggedIn();
	}
}
