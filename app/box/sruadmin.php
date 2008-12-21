<?

/**
 * administracja sru
 */
class UFbox_SruAdmin
extends UFbox {

	protected function _getComputerFromGet() {
		$bean = UFra::factory('UFbean_Sru_Computer');
		$bean->getByPK((int)$this->_srv->get('req')->get->computerId);

		return $bean;
	}

	protected function _getUserFromGet() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getByPK((int)$this->_srv->get('req')->get->userId);

		return $bean;
	}

	protected function _getPenaltyFromGet() {
		$bean = UFra::factory('UFbean_SruAdmin_Penalty');
		$bean->getByPK((int)$this->_srv->get('req')->get->penaltyId);

		return $bean;
	}		

	protected function _getAdminFromGet() {
		$bean = UFra::factory('UFbean_SruAdmin_Admin');
		$bean->getByPK((int)$this->_srv->get('req')->get->adminId);

		return $bean;
	}	
	
	protected function _getDormFromGet() {
		$bean = UFra::factory('UFbean_Sru_Dormitory');
		$bean->getByAlias($this->_srv->get('req')->get->dormAlias);

		return $bean;
	}

	protected function _getRoomFromGet() {
		$bean = UFra::factory('UFbean_SruAdmin_Room');
		$bean->getByAlias($this->_srv->get('req')->get->dormAlias, $this->_srv->get('req')->get->roomAlias);

		return $bean;
	}	

	public function login() {
		$bean = UFra::factory('UFbean_SruAdmin_Admin');

		$d['admin'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function logout() {
		try{
		$bean = UFra::factory('UFbean_SruAdmin_Admin');
		$bean->getFromSession();

		$d['admin'] = $bean;

		return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function titleComputer() {
		try {
			$bean = $this->_getComputerFromGet();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleComputerNotFound');
		}
	}

	public function computer() {
		try {
			$bean = $this->_getComputerFromGet();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('computerNotFound');
		}
	}

	public function computerHistory() {
		try {
			$bean = $this->_getComputerFromGet();
			$d['computer'] = $bean;
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}

		$history = UFra::factory('UFbean_SruAdmin_ComputerHistoryList');
		try {
			$history->listByComputerId($bean->id);
		} catch (UFex_Dao_NotFound $e) {
		}
		$d['history'] = $history;

		return $this->render(__FUNCTION__, $d);
	}
/*
	public function computers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActive();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('computersNotFound');
		}
	}
*/
	public function titleComputerEdit() {
		try {
			$bean = $this->_getComputerFromGet();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleComputerNotFound');
		}
	}

	public function computerEdit() {
		try {
			$bean = $this->_getComputerFromGet();
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();

			try {
				$get = $this->_srv->get('req')->get;
				$compId = $get->computerId;
				$histId = $get->computerHistoryId;
				// lista, zeby mozna bylo podac tablice dla $bean->fill()
				$history = UFra::factory('UFbean_SruAdmin_ComputerHistoryList');
				$history->listByComputerIdPK($compId, $histId);
				$d['history'] = $history[0];
			} catch (UFex $e) {
				$d['history'] = null;
			}

			$d['computer'] = $bean;
			$d['dormitories'] = $dorms;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('computerNotFound');
		}
	}

	public function computerDel() {
		try {
			$bean = $this->_getComputerFromGet();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleComputerNotFound');
		}
	}

	public function computerSearch() {
		$bean = UFra::factory('UFbean_Sru_Computer');

		$d['computer'] = $bean;

		$get = $this->_srv->get('req')->get;
		$tmp = array();
		try {
			$tmp['host'] = $get->searchedHost;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['mac'] = $get->searchedMac;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['ip'] = $get->searchedIp;
		} catch (UFex_Core_DataNotFound $e) {
		}
		$d['searched'] = $tmp;

		return $this->render(__FUNCTION__, $d);
	}

	public function computerSearchResults() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');

			$d['computers'] =& $bean;

			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$tmp['host'] = $get->searchedHost;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['mac'] = $get->searchedMac;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['ip'] = $get->searchedIp;
			} catch (UFex_Core_DataNotFound $e) {
			}
			$bean->search($tmp);
			if (1 == count($bean)) {
				$get->computerId = $bean[0]['id'];
				return $this->computer();
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Db_QueryFailed $e) {
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function userSearch() {
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;

		$get = $this->_srv->get('req')->get;
		$tmp = array();
		try {
			$tmp['login'] = $get->searchedLogin;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['name'] = $get->searchedName;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['surname'] = $get->searchedSurname;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['email'] = $get->searchedEmail;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['room'] = $get->searchedRoom;
		} catch (UFex_Core_DataNotFound $e) {
		}
		try {
			$tmp['dormitory'] = $get->searchedDormitory;
		} catch (UFex_Core_DataNotFound $e) {
		}
		$d['searched'] = $tmp;

		return $this->render(__FUNCTION__, $d);
	}

	public function userSearchResults() {
		try {
			$bean = UFra::factory('UFbean_Sru_UserList');

			$get = $this->_srv->get('req')->get;
			$tmp = array();
			try {
				$tmp['name'] = $get->searchedName;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['surname'] = $get->searchedSurname;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['login'] = $get->searchedLogin;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['email'] = $get->searchedEmail;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['room'] = $get->searchedRoom;
			} catch (UFex_Core_DataNotFound $e) {
			}
			try {
				$tmp['dormitory'] = $get->searchedDormitory;
			} catch (UFex_Core_DataNotFound $e) {
			}
			$bean->search($tmp);
			if (1 == count($bean)) {
				$get->userId = $bean[0]['id'];
				return $this->user().$this->userComputers();
			}

			$d['users'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titleUser() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function user() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function userComputers() {
		try {
			$user = $this->_getUserFromGet();
			$d['user'] = $user;

			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listByUserId($user->id); 
			$d['computers'] = $bean;


			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}
	public function userInactiveComputers() {
		try {
			$user = $this->_getUserFromGet();

			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listByUserIdInactive($user->id); 

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}	

	public function userEdit() {
		try {
			$bean = $this->_getUserFromGet();
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();

			try {
				$get = $this->_srv->get('req')->get;
				$userId = $get->userId;
				$histId = $get->userHistoryId;
				// lista, zeby mozna bylo podac tablice dla $bean->fill()
				$history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
				$history->listByUserIdPK($userId, $histId);
				$history = $history[0];
				$bean->fill($history);
			} catch (UFex $e) {
			}
			$faculties = UFra::factory('UFbean_Sru_FacultyList');
			$faculties->listAll();

			$d['user'] = $bean;
			$d['dormitories'] = $dorms;
			$d['faculties'] = $faculties;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('computerNotFound');
		}
	}

	public function titleUserEdit() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function userHistory() {
		try {
			$bean = $this->_getUserFromGet();
			$d['user'] = $bean;
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}

		$history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
		try {
			$history->listByUserId($bean->id);
		} catch (UFex_Dao_NotFound $e) {
		}
		$d['history'] = $history;

		return $this->render(__FUNCTION__, $d);
	}

	public function adminBar() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getFromSession();
			$d['admin'] = $bean;


			$sess = $this->_srv->get('session');
			try {
				$d['lastLoginIp'] = $sess->lastLoginIp;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastLoginIp'] = null;
			}
			try {
				$d['lastLoginAt'] = $sess->lastLoginAt;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastLoginAt'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function admins() 
	{
		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');	
			$bean->listAll();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('adminsNotFound');
		}
	}
	public function inactiveAdmins() 
	{
		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');	
			$bean->listAllInactive();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('inactiveAdminsNotFound');
		}
	}
	public function bots() 
	{
		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');	
			$bean->listAllBots();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('botsNotFound');
		}
	}		
	public function titleAdmin()
	{
		try
		{
			$bean = $this->_getAdminFromGet();

			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		}
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('titleAdminNotFound');
		}
	}

	public function admin() {
		try {
			$bean = $this->_getAdminFromGet();

			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	public function adminAdd() {
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();
		
		$bean = UFra::factory('UFbean_SruAdmin_Admin');

		$d['admin'] = $bean;
		$d['dormitories'] = $dorms;


		return $this->render(__FUNCTION__, $d);
	}
	public function titleAdminEdit()
	{
		try
		{
			$bean = $this->_getAdminFromGet();

			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		}
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('titleAdminNotFound');
		}
	}

	public function adminEdit() {
		try {
			$bean = $this->_getAdminFromGet();
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();
			
			$bean = $this->_getAdminFromGet();
			$acl  = $this->_srv->get('acl');
	
			$d['admin'] = $bean;
			$d['dormitories'] = $dorms;
			$d['advanced'] = $acl->sruAdmin('admin', 'advancedEdit');

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('adminNotFound');
		}
	}
	public function dorms() 
	{
		try 
		{
			$bean = UFra::factory('UFbean_Sru_DormitoryList');	
			$bean->listAll();
			$d['dorms'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('dormsNotFound');
		}
	}
	public function titleDorm()
	{
		try
		{
			$bean = $this->_getDormFromGet();

			$d['dorm'] = $bean;

			return $this->render(__FUNCTION__, $d);
		}
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	public function dorm() {
		try {
			$bean = $this->_getDormFromGet();

			$d['dorm'] = $bean;
			$d['rooms'] = array();
			
			try {
				$rooms = UFra::factory('UFbean_SruAdmin_RoomList');
				
				$rooms->listByDormitoryId($bean->id); 
				
						
				$d['rooms'] = $rooms;
			} catch (UFex_Dao_NotFound $e) {

			}			
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	public function titleRoom()
	{
		try
		{
			$bean = $this->_getRoomFromGet();

			$d['room'] = $bean;

			return $this->render(__FUNCTION__, $d);
		}
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	public function room() {
		try {
			$bean = $this->_getRoomFromGet();

			$d['room'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function roomUsers() {
		try {
			$room = $this->_getRoomFromGet();
			$bean = UFra::factory('UFbean_Sru_UserList');
			$bean->listByRoom($room->id);
			$d['users'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function roomComputers() {
		try {
			$room = $this->_getRoomFromGet();
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listByRoom($room->id);
			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function roomEdit() {
		try {
			$bean = $this->_getRoomFromGet();
	
			$d['room'] = $bean;
					
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('roomNotFound');
		}
	}
	public function computerAdd() {
		$bean = UFra::factory('UFbean_Sru_Computer');

		$d['computer'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}
	public function serverComputers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllServers();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	public function administrationComputers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllAdministration();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}	
	public function organizationsComputers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllOrganization();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}
	public function penalties() 
	{
		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');	
			$bean->listAll();
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('penaltiesNotFound');
		}
	}
	public function penaltyAdd() {
			
			try {
				
			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
	
			$d['penalty'] = $bean;
					
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;
			
			$comp = UFra::factory('UFbean_Sru_ComputerList');
			$d['computers'] =& $comp;
			
			try{
			
				$comp->listByUserId($d['user']->id); 
	
			} catch (UFex_Dao_NotFound $e) {}			
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userNotFound');
		}		
	}

	public function titlePenaltyAdd() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleUserNotFound');
		}
	}

	public function penalty() {
		try {
			$bean = $this->_getPenaltyFromGet();

			$d['penalty'] = $bean;
			if (UFbean_SruAdmin_Penalty::TYPE_COMPUTER === $bean->typeId || UFbean_SruAdmin_Penalty::TYPE_COMPUTERS === $bean->typeId) {
				try {
					$computers = UFra::factory('UFbean_SruAdmin_ComputerBanList');
					$computers->listByPenaltyId($bean->id);
					$d['computers'] = $computers;
				} catch (UFex_Dao_NotFound $e) {
					$d['computers'] = null;
				}
			} else {
				$d['computers'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}	
	public function userPenalties() 
	{
		try 
		{
			$user = $this->_getUserFromGet();
			$d['user'] = $user;

			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');	
			$bean->listAllByUserId($user->id);
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('penaltiesNotFound');
		}
	}
	public function titleUserPenalties() {
		try {
			$bean = $this->_getUserFromGet();

			$d['user'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleUserNotFound');
		}
	}

	public function penaltyActions() {
		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');	
			$bean->listLastAdded();
			$d['added'] = $bean;

			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$bean->listLastModified();
			$d['modified'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('penaltiesNotFound');
		}
	}					
}
