<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Sru_User
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('auth');
	}

	public function recover() { 
		return !$this->_loggedIn();
	}

	public function edit() {
		return $this->_loggedIn();
	}

	public function login() {
		return !$this->_loggedIn();
	}
	
	public function logout() {
		return $this->_loggedIn();
	}

	public function viewPersonalData() {
		if (!$this->_loggedIn()) {
			return false;
		}
		// jeśli nie połaczone bezpiecznie - nie pokazuj!
		$conf = UFra::shared('UFconf_Sru');
		if (!$this->_srv->get('session')->secureConnection && !$conf->allowUnsecureConnections) {
			return false;
		}
		return true;
	}
}
