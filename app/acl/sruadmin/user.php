<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_User
extends UFlib_ClassWithService {
	
	protected $adminTypes = array(1, 2, 3, 4);
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function add() {
		$sess = $this->_srv->get('session');
		
		if($this->_loggedIn() && $sess->is('typeId') && ($sess->typeId == UFacl_SruAdmin_Admin::CENTRAL || $sess->typeId == UFacl_SruAdmin_Admin::CAMPUS) )
		{
			return true;
		}
		return false;
	}

	public function edit() {
		return $this->_loggedIn();
	}

	public function fullEdit($userId) {
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
			return true;
		}

		return false;
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
