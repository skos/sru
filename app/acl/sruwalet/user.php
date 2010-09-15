<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruWalet_User
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authWaletAdmin');
	}

	public function edit($userId) {
		if (!$this->_loggedIn()) {
			return false;
		}

		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::HEAD) {
			return true;
		}

		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($userId);
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}

		if (!$user->active || is_null($user->referralStart) || $user->referralStart == 0) {
			return true;
		}

		try {
			$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
			$admDorm->listAllById($sess->authWaletAdmin);
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
		foreach ($admDorm as $dorm) {
			if ($dorm['dormitoryId'] == $user->dormitoryId) {
				return true;
			}
		}
		return false;
	}

	public function add() {
		return $this->_loggedIn();
	}

	public function del() {
		return $this->_loggedIn();
	}
	
	public function login() {
		return !$this->_loggedIn();
	}
	
	public function logout() {
		return $this->_loggedIn();
	}
}
