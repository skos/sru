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
		if (!$this->_loggedIn()) {
			return false;
		}
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		return $user->servicesAvailable;
	}
}
