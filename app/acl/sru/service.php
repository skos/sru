<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Sru_Service
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('auth');
	}

	public function edit() {
		return $this->_loggedIn();
	}
}
