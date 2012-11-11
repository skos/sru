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
		if ($bean->active === false || is_null($bean->email) || $bean->email == '' || (is_null($bean->studyYearId) && $bean->facultyId != 0)) {
			return false;
		}
		return true;
	}
	
	public function editServer() {
		if (!$this->_loggedIn()) {
			return false;
		}
		$bean = UFra::factory('UFbean_Sru_Computer');
		$bean->getByPK($this->_srv->get('req')->get->computerId);
		
		if ($bean->active && ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT)) {
		    return true;
		}
		return false;
	}

	public function del() {
		return $this->_loggedIn();
	}
}
