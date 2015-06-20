<?

/**
 * sru
 */
class UFbox_Sru
extends UFbox {

	private function getPenalizedComputers($penalties){
		$result = null;
		
		foreach($penalties as $penalty) {
			if (UFbean_SruAdmin_Penalty::TYPE_COMPUTER === $penalty['typeId'] || UFbean_SruAdmin_Penalty::TYPE_COMPUTERS === $penalty['typeId']) {
				try {
					$computers = UFra::factory('UFbean_SruAdmin_ComputerBanList');
					$computers->listByPenaltyId($penalty['id']);
					foreach($computers as $computer){
						$result[$penalty['id']][] = array(
							'name' => $computer['computerHost'],
							'id' => $computer['computerId']
						);
					}
				} catch (UFex_Dao_NotFound $e) {
					$result[$penalty['id']] = null;
				}
			} else {
				$result[$penalty['id']] = null;
			}
		}
		
		return $result;
	}
	
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
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		$d['user'] = $user;

		return $this->render(__FUNCTION__, $d);
	}

	public function hostsInfo() {
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listByUserId($user->id);
		} catch (UFex_Dao_NotFound $e) {
			$bean = null;
			try {
				// jeżeli nie ma aktywnych, spróbujmy poszukać niekatywnych
				$inactive = UFra::factory('UFbean_Sru_ComputerList');
				$inactive->listByUserIdInactive($user->id);

				$d['inactive'] = $inactive;
			} catch (UFex_Dao_NotFound $e) {
				$d['inactive'] = null;
			}
		}

		$d['computers'] = $bean;
		return $this->render(__FUNCTION__, $d);
	}

	public function penaltyInfo() {
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		$d['user'] = $user;

		try {
			$bean = UFra::factory('UFbean_Sru_PenaltyList');
			$bean->listByUserId($user->id);
			$d['penalties'] = $bean;

			$d['computers'] = $this->getPenalizedComputers($d['penalties']);
			
		} catch (UFex_Dao_NotFound $e) {
			$d['penalties'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}
	
	public function functionsInfo() {
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		$d['user'] = $user;
		
		try {
			$functions = UFra::factory('UFbean_Sru_UserFunctionList');
			$functions->listByUserId($user->id); 

			$d['functions'] = $functions;
		} catch (UFex_Dao_NotFound $e) {
			$d['functions'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}

	public function contact() {
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		$d['user'] = $user;

		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listUpcomingByDormId($user->dormitoryId);
			$d['dutyHours'] = $hours;
		} catch (UFex_Dao_NotFound $e) {
			$d['dutyHours'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}

	public function userAddByAdminMailTitle($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function userAddByAdminMailBody($user) {
                try {
                        $d['password'] = $this->_srv->get('req')->get->password;
                } catch (UFex_Core_DataNotFound $e) {
                        $d['password'] = null;
                }
                $d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function userAddMailTitle($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function userAddMailBody($user) {
		$d['user'] = $user;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;

		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listUpcomingByDormId($user->dormitoryId);
			$d['dutyHours'] = $hours;
		} catch (UFex_Dao_NotFound $e) {
			$d['dutyHours'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}

	public function userEdit() {
		try{		
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getFromSession();
	
			$faculties = UFra::factory('UFbean_Sru_FacultyList');
			$faculties->listAll();
	
			$d['user'] = $bean;
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
			try {
				// jeżeli nie ma aktywnych, spróbujmy poszukać niekatywnych
				$user = UFra::factory('UFbean_Sru_User');
				$user->getFromSession();

				$bean = UFra::factory('UFbean_Sru_ComputerList');
				$bean->listByUserIdInactive($user->id); 

				$d['computers'] = $bean;
				return $this->render(__FUNCTION__.'NotFound', $d);
			} catch (UFex_Dao_NotFound $e) {
				return $this->render(__FUNCTION__.'NotFound');
			}
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
			
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();
			$d['user'] = $user;
			
			try {
				$fwExceptions = UFra::factory('UFbean_SruAdmin_FwExceptionList');
				$fwExceptions->listActiveByComputerId($bean->id);
				$d['fwExceptions'] = $fwExceptions;
			} catch (UFex $e) {
				$d['fwExceptions'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerEdit($activate = false) {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$d['computer'] = $bean;
			$d['user'] = $user;
			$d['activate'] = $activate;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerAdd() {
		$bean = UFra::factory('UFbean_Sru_Computer');
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		$prohibited = array('.', '@', '_', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		
		$used = true;
		$login = $user->login;
		$login = strtolower(str_replace($prohibited, '', $login));
		try {
			$bean->getByHost($login);
		} catch (UFex_Dao_NotFound $e) {
			$used = false;
		}
		if ($used) {
			for($i = 1; $i < 100; $i++) {
				try {
					$bean->getByHost($login.$i);
				} catch (UFex_Dao_NotFound $e) {
					$used = false;
					break;
				}
			}
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->host = $login.$i;
		} else {
			$bean->host = $login;
		}

		$d['computer'] = $bean;
		$d['macAddress'] = $this->getMacAddress();
		$d['user'] = $user;

		return $this->render(__FUNCTION__, $d);
	}
	
	public function userComputerFwExceptionsAdd() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$d['computer'] = $bean;
			$d['user'] = $user;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}
	
	public function userApplications() {
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$fwApplications = UFra::factory('UFbean_Sru_FwExceptionApplicationList');
			$fwApplications->listByUser($user->id);
			$d['fwApplications'] = $fwApplications;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
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
			try {
				$d['lastInvLoginIp'] = $sess->lastInvLoginIp;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastInvLoginIp'] = null;
			}
			try {
				$d['lastInvLoginAt'] = $sess->lastInvLoginAt;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastInvLoginAt'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function userBanned() {
		$d['penalties'] = null;
			
		$serv = $this->_srv->get('req')->server;
		if($serv->is('HTTP_X_FORWARDED_FOR') && $serv->HTTP_X_FORWARDED_FOR != '' ) {
			$ip = $serv->HTTP_X_FORWARDED_FOR;
		} else {
			$ip =  $serv->REMOTE_ADDR;
		}

		if (strlen($ip) < 7 || substr($ip, 0, 7) != '172.16.') {
			return $this->render(__FUNCTION__, $d);
		}
		$ip = str_replace('172.16.', '153.19.', $ip);

		try {
			$bean = UFra::factory('UFbean_SruAdmin_ComputerBanList');
			$bean->listByComputerIp($ip);
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__, $d);
		}
	}

	public function userPenalties() {
		try 
		{
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$bean = UFra::factory('UFbean_Sru_PenaltyList');	
			$bean->listAllByUserId($user->id);
			$d['penalties'] = $bean;
			
			$d['computers'] = $this->getPenalizedComputers($d['penalties']);
			
			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('userPenaltiesNotFound');
		}
	}
	
	public function applicationFwExceptions() {
		try {
			$fwApplications = UFra::factory('UFbean_Sru_FwExceptionApplicationList');
			$fwApplications->listAll();
			$d['fwApplications'] = $fwApplications;
		} catch (UFex_Dao_NotFound $e) {
			$d['fwApplications'] = null;
		}
		return $this->render(__FUNCTION__, $d);
	}
	
	public function applicationFwException() {
		try {
			$fwApplication = UFra::factory('UFbean_Sru_FwExceptionApplication');
			$fwApplication->getByPK((int)$this->_srv->get('req')->get->appId);
			$d['fwApplication'] = $fwApplication;
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('applicationFwExceptionNotFound');
		}
		return $this->render(__FUNCTION__, $d);
	}

	public function userRecoverPasswordMailTitle($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function userRecoverPasswordMailBodyToken($user, $token) {
		$d['user'] = $user;
		$d['token'] = $token;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function userRecoverPasswordMailBodyPassword($user, $password) {
		$d['user'] = $user;
		$d['password'] = $password;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function penaltyAddMailTitle($penalty, $user) {
		$d['penalty'] = $penalty;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function penaltyAddMailBody($penalty, $user, $computers) {
		$d['penalty'] = $penalty;
		$d['user'] = $user;
		$d['computers'] = $computers;

		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listUpcomingByDormId($user->dormitoryId);
			$d['dutyHours'] = $hours;
		} catch (UFex_Dao_NotFound $e) {
			$d['dutyHours'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}

	public function penaltyEditMailTitle($penalty, $user) {
		$d['penalty'] = $penalty;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function penaltyEditMailBody($penalty, $user) {
		$d['penalty'] = $penalty;
		$d['user'] = $user;

		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listUpcomingByDormId($user->dormitoryId);
			$d['dutyHours'] = $hours;
		} catch (UFex_Dao_NotFound $e) {
			$d['dutyHours'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}

	public function dataChangedMailTitle($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function dataChangedMailBody($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostChangedMailTitle($host, $user) {
		$d['host'] = $host;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostChangedMailBody($host, $action, $user) {
		$d['host'] = $host;
		$d['action'] = $action;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function hostFwExceptionsChangedMailTitle($user, $host) {
		$d['user'] = $user;
		$d['host'] = $host;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostFwExceptionsChangedMailBody($user, $host, array $deleted, array $added) {
		$d['user'] = $user;
		$d['host'] = $host;
		$d['deleted'] = $deleted;
		$d['added'] = $added;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function newFwExceptionApplicationMailBody($application) {
		$d['application'] = $application;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function rejectedFwExceptionApplicationMailTitle($application, $user) {
		$d['application'] = $application;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function rejectedFwExceptionApplicationMailBody($application, $user) {
		$d['application'] = $application;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function approvedFwExceptionApplicationMailTitle($application, $user) {
		$d['application'] = $application;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function approvedFwExceptionApplicationMailBody($application, $user) {
		$d['application'] = $application;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	private function getMacAddress() {
		$serv = $this->_srv->get('req')->server;
		if($serv->is('HTTP_X_FORWARDED_FOR') && $serv->HTTP_X_FORWARDED_FOR != '' ) {
			$ip = $serv->HTTP_X_FORWARDED_FOR;
		} else {
			$ip =  $serv->REMOTE_ADDR;
		}
		if (strlen($ip) < 7 || substr($ip, 0, 7) != '172.16.') {
			return null;
		}
		$mac = shell_exec('sudo arping '.$ip.' -f -w 2 | grep -E -o \'[[:xdigit:]]{2}(:[[:xdigit:]]{2}){5}\'');

		return trim($mac);
	}
}
