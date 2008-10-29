<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_Sru_Computer
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('auth');
	}

	protected function _notBanned() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getByPK($this->_srv->get('session')->auth);
		return !$bean->banned;
	}

	public function edit() {
		return $this->_loggedIn() && $this->_notBanned();
	}

	public function add() {
		return $this->_loggedIn();
	}

	public function del() {
		return $this->_loggedIn();
	}
}
