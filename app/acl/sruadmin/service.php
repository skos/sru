<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_Service
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function edit($userId) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$user = UFra::factory('UFbean_Sru_User');
		$user->getByPK($userId);
		if ($user->typeId < UFbean_Sru_User::DB_STUDENT_MAX || $user->typeId == UFbean_Sru_User::TYPE_ORGANIZATION || $user->typeId == UFbean_Sru_User::TYPE_EXADMIN) {
			return true;
		}
		return false;
	}
}
