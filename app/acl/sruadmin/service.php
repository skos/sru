<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_Service
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function edit() {
		return $this->_loggedIn();
	}
}
