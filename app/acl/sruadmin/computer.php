<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_Computer
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function edit() {
		return $this->_loggedIn();
	}

	public function add() {
		return $this->_loggedIn();
	}

	public function addForUser($id) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$bean = UFra::factory('UFbean_Sru_User');
		try {
			$bean->getByPK($id);
		} catch (Exception $e) {
			return false;
		}
		// hosta można dodać tylko aktywnemu użytkownikowi
		if ($bean->active === false || is_null($bean->email) || $bean->email == '' || is_null($bean->studyYearId)) {
			return false;
		}
		return true;
	}

	public function editAliases() {
		if (!$this->_loggedIn()) {
			return false;
		}
		$bean = UFra::factory('UFbean_Sru_Computer');
		$bean->getByPK($this->_srv->get('req')->get->computerId);
		return $bean->active;
	}

	public function del() {
		return $this->_loggedIn();
	}
}
