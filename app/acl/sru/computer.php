<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Sru_Computer
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('auth');
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
}
