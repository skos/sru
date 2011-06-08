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
		if ($user->servicesAvailable && !$user->banned && ($user->typeId < UFbean_Sru_User::DB_STUDENT_MAX || $user->typeId == UFbean_Sru_User::TYPE_EXADMIN)) {
			return true;
		}
		return false;
	}

	public function view() {
		if (!$this->_loggedIn()) {
			return false;
		}
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		if ($user->typeId < UFbean_Sru_User::DB_STUDENT_MAX || $user->typeId == UFbean_Sru_User::TYPE_EXADMIN) {
			return true;
		}
		return false;
	}
}
