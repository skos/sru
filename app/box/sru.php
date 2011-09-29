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
		} catch (UFex_Dao_NotFound $e) {
			$d['penalties'] = null;
		}

		return $this->render(__FUNCTION__, $d);
	}

	public function servicesInfo() {
		$user = UFra::factory('UFbean_Sru_User');
		$user->getFromSession();
		$d['user'] = $user;

		try {
			$bean = UFra::factory('UFbean_Sru_UserServiceList');
			$bean->listAllByUserId($user->id);
			$d['userServices'] = $bean;
		}
		catch (UFex_Dao_NotFound $e) {
			$d['userServices'] = null;
		}

		$bean = UFra::factory('UFbean_Sru_ServiceList');
		$bean->listAllServices();
		$d['allServices'] = $bean;

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

	public function banners() {
		$content = @file_get_contents(UFURL_BASE.'i/banners/banners.htm');
		if ($content !== false) {
			$content = ($content);
			$d['content'] = $content;
		} else {
			$d['content'] = null;
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

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userComputerNotFound');
		}
	}

	public function userComputerEdit($activate = false) {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;
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

	public function userComputerDel() {
		try {
			$bean = $this->_getComputerFromGetByCurrentUser();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function computerStats() {
		try {
			$get = $this->_srv->get('req')->get;

			$bean = $this->_getComputerFromGetByCurrentUser();
			$d['computer'] = $bean;

			$d['statHour'] = $get->statHour;
			$hour = explode(':', $d['statHour']);
			$d['statDate'] = $get->statDate;
			if (strlen($d['statHour']) != 5 || strpos($d['statHour'], ':') !== 2 || !is_numeric($hour[0]) || !is_numeric($hour[1]) || $hour[0] > 24 || $hour[0] < 0 || $hour[1] > 59 || $hour[1] < 0) {
				$d['statHour'] = date('H:i');
			}
			if ((int)$d['statDate'] <= 0 || strlen($d['statDate']) != 8) {
				$d['statDate'] = date('Ymd');
			}
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}

		$mac = str_replace(':', '', $bean->mac);
		$rrd = UFra::factory('UFlib_Rrd');
		$file = $rrd->generatePng($mac, $bean->host, $d['statHour'], $d['statDate']);

		if (!file_exists(UFURL_BASE.'i/stats-img/'.$file.'.png')) {
			return $this->render(__FUNCTION__.'NotFound');
		} else {
			$d['file'] = $file;
		}

		return $this->render(__FUNCTION__, $d);
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

	public function userPenalties() {
		try 
		{
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			$bean = UFra::factory('UFbean_Sru_PenaltyList');	
			$bean->listAllByUserId($user->id);
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('userPenaltiesNotFound');
		}
	}

	public function userServicesEdit() {
		try {
			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();
			$d['user'] = $user;
			
			try {
				$bean = UFra::factory('UFbean_Sru_UserServiceList');	
				$bean->listAllByUserId($user->id);
				$d['userServices'] = $bean;
			}
			catch (UFex_Dao_NotFound $e) {
				$d['userServices'] = null;
			}

			$bean = UFra::factory('UFbean_Sru_ServiceList');	
			$bean->listAllServices();
			$d['allServices'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('userServicesNotFound');
		}
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
		$mac = `arping -r -c 1 $ip`;

		return trim($mac);
	}
}
