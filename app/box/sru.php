<?

/**
 * sru
 */
class UFbox_Sru
extends UFbox {

	protected function _getComputerFromGetByCurrentUser() {
		$bean = UFra::factory('UFbean_Sru_Computer');
		$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);

		return $bean;
	}

	public function login() {
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function userInfo() {
		try 
		{
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$bean = UFra::factory('UFbean_Sru_PenaltyList');	
			$bean->listByUserId($user->id);
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('penaltiesNotFound');
		}
	}

	public function userAdd($admin=false) {
		try{
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();
			$faculties = UFra::factory('UFbean_Sru_FacultyList');
			$faculties->listAll();
			$bean = UFra::factory('UFbean_Sru_User');
	
			$d['user'] = $bean;
			$d['dormitories'] = $dorms;
			$d['faculties'] = $faculties;
			$d['admin'] = $admin;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}		
	}

	public function userAddMailTitle($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function userAddMailBodyToken($user, $password, $token) {
		$d['user'] = $user;
		$d['password'] = $password;
		$d['token'] = $token;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function userAddMailBodyNoToken($user, $password) {
		$d['user'] = $user;
		$d['password'] = $password;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function userAddMailBodyNoInfo($user, $password) {
		$d['user'] = $user;
		$d['password'] = $password;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function userAddMailHeaders($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function userEdit() {
		try{		
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getFromSession();
	
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();
			$faculties = UFra::factory('UFbean_Sru_FacultyList');
			$faculties->listAll();
	
			$d['user'] = $bean;
			$d['dormitories'] = $dorms;
			$d['faculties'] = $faculties;
	
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}			
	}

	public function userComputers() {
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listByUserId($user->id);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titleUserComputer() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleUserComputerNotFound');
		}
	}

	public function userComputer() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerEdit() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerAdd() {
		$bean = UFra::factory('UFbean_Sru_Computer');

		$d['computer'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function userComputerDel() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function userBar() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getFromSession();
			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
}
