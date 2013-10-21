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

		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($userId);
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}

		if ($user->typeId > UFtpl_Sru_User::$userTypesLimit) {
			return false;
		}

		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::HEAD) {
			return true;
		}
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
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
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		return true;
	}

	public function del($userId) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($userId);
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}
		return $this->edit($userId) && $user->active;
	}
	
	public function login() {
		return !$this->_loggedIn();
	}
	
	public function logout() {
		return $this->_loggedIn();
	}

	public function view($userId) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($userId);
		} catch (UFex_Dao_NotFound $e) {
			return false;
		}

		if ($user->typeId > UFtpl_Sru_User::$userTypesLimit) {
			return false;
		}
		return true;
	}
	
	public function exportBook() {
		$sess = $this->_srv->get('session');
		if ($sess->typeIdWalet == UFacl_SruWalet_Admin::PORTIER) {
			return false;
		}
		return true;
	}
}
