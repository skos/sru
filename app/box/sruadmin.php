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

	protected function _getPenaltyTemplateFromGet() {
		$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
		$bean->getByPK((int)$this->_srv->get('req')->get->penaltyTemplateId);

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

	protected function _getSwitchFromGet() {
		$bean = UFra::factory('UFbean_SruAdmin_Switch');
		$bean->getBySerialNo($this->_srv->get('req')->get->switchSn);

		return $bean;
	}

	protected function _getSwitchPortFromGet($switchId) {
		$bean = UFra::factory('UFbean_SruAdmin_SwitchPort');
		$bean->getBySwitchIdAndOrdinalNo($switchId, (int)$this->_srv->get('req')->get->portNo);

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

			$hp = UFra::factory('UFlib_Snmp_Hp');
			$switchPort = $hp->findMac($bean->mac);
			$d['switchPort'] = $switchPort;

			if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT) {
				try {
					$aliases = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
					$aliases->listByComputerId($bean->id);
					$d['aliases'] = $aliases;
				} catch (UFex $e) {
					$d['aliases'] = null;
				}
			} else {
				$d['aliases'] = null;
			}

			if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER) {
				try {
					$virtuals = UFra::factory('UFbean_Sru_ComputerList');
					$virtuals->listVirtualsByComputerId($bean->id);
					$d['virtuals'] = $virtuals;
				} catch (UFex $e) {
					$d['virtuals'] = null;
				}
			} else {
				$d['virtuals'] = null;
			}

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

	public function computerStats() {
		try {
			$get = $this->_srv->get('req')->get;

			$bean = $this->_getComputerFromGet();
			$d['computer'] = $bean;

			$d['statHour'] = $get->statHour;
			$hour = explode(':', $d['statHour']);
			$d['statDate'] = $get->statDate;
			if (strlen($d['statHour']) != 5 || strpos($d['statHour'], ':') !== 2 || !is_numeric($hour[0]) || !is_numeric($hour[1]) || $hour[0] >= 24 || $hour[0] < 0 || $hour[1] > 59 || $hour[1] < 0) {
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
			try {
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($bean->userId);
				$d['user'] = $user;
			} catch (UFex $e) {
				$d['user'] = null;
			}
			try {
				$servers = UFra::factory('UFbean_Sru_ComputerList');
				$servers->listAllPhysicalServers();
				$d['servers'] = $servers;
			} catch (UFex $e) {
				$d['servers'] = null;
			}
			try {
				$waletAdmins = UFra::factory('UFbean_SruWalet_AdminList');
				$waletAdmins->listAll();
				$d['waletAdmins'] = $waletAdmins;
			} catch (UFex $e) {
				$d['waletAdmins'] = null;
			}

			$d['computer'] = $bean;
			$d['dormitories'] = $dorms;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('computerNotFound');
		}
	}

	public function titleComputerAliasesEdit() {
		try {
			$bean = $this->_getComputerFromGet();
			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleComputerNotFound');
		}
	}

	public function computerAliasesEdit() {
		try {
			$bean = $this->_getComputerFromGet();
			try {
				$aliases = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
				$aliases->listByComputerId($bean->id);
				$d['aliases'] = $aliases;
			} catch (UFex $e) {
				$d['aliases'] = null;
			}

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('computerAliasesNotFound');
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
			$tmp['typeId'] = $get->searchedTypeId;
		} catch (UFex_Core_DataNotFound $e) {
		}
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
				$tmp['typeId'] = $get->searchedTypeId;
			} catch (UFex_Core_DataNotFound $e) {
			}
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
			// sprawdźmy, czy chodzi o niezarejestrowanego MACa
			try {
				$hp = UFra::factory('UFlib_Snmp_Hp');
				$switchPort = $hp->findMac($get->searchedMac);
				if (!is_null($switchPort)) {
					$d['switchPort'] = $switchPort;
					return $this->render(__FUNCTION__.'Unregistered', $d);
				}
			} catch (UFex_Core_DataNotFound $e) {
			}

			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function computerSearchByAliasResults() {
		try {
			$get = $this->_srv->get('req')->get;
			$bean = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
			$bean->search($get->searchedHost);
			$d['aliases'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function computerSearchHistoryResults() {
		try {
			$get = $this->_srv->get('req')->get;
			if (is_null($get->searchedIp)) {
				return '';
			}
			$bean = UFra::factory('UFbean_SruAdmin_ComputerHistoryList');
			$bean->listByComputerIp($get->searchedIp);
			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex $e) {
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
			$tmp['registryNo'] = $get->searchedRegistryNo;
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
		try {
			$tmp['typeId'] = $get->searchedTypeId;
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
				$tmp['registryNo'] = $get->searchedRegistryNo;
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
			try {
				$tmp['typeId'] = $get->searchedTypeId;
			} catch (UFex_Core_DataNotFound $e) {
			}
			$bean->search($tmp);
			if (1 == count($bean)) {
				$get->userId = $bean[0]['id'];
				return $this->user().$this->userComputers().$this->userInactiveComputers();
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
			try {
				$user = $this->_getUserFromGet();
				$d['user'] = $user;
			} catch (UFex_Dao_NotFound $e) {
				return $this->render('computersNotFound');
			}

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

	public function userAdd() {
		try {
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAllForWalet();

			$bean = UFra::factory('UFbean_Sru_User');
	
			$d['user'] = $bean;
			$d['dormitories'] = $dorms;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function userEdit() {
		try {
			$bean = $this->_getUserFromGet();

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

	public function serviceHistory() {
		try {
			$bean = $this->_getUserFromGet();
			$d['user'] = $bean;
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}

		$history = UFra::factory('UFbean_SruAdmin_ServiceHistoryList');
		try {
			$history->listByUserId($bean->id);
		} catch (UFex_Dao_NotFound $e) {
		}
		$d['servicehistory'] = $history;

		return $this->render(__FUNCTION__, $d);
	}

	public function adminBar() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getFromSession();
			$d['admin'] = $bean;


			$sess = $this->_srv->get('session');
			try {
				$d['lastLoginIp'] = $sess->lastLoginIpAdmin;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastLoginIp'] = null;
			}
			try {
				$d['lastLoginAt'] = $sess->lastLoginAtAdmin;
			} catch (UFex_Core_DataNotFound $e) {
				$d['lastLoginAt'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function admins() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');
			$bean->listAll();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) {
			return $this->render('adminsNotFound');
		}
	}
	public function inactiveAdmins() {
		try  {
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');	
			$bean->listAllInactive();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) {
			return $this->render('inactiveAdminsNotFound');
		}
	}
	public function bots() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');	
			$bean->listAllBots();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) {
			return $this->render('botsNotFound');
		}
	}
	public function waletAdmins() {
		try {
			$bean = UFra::factory('UFbean_SruWalet_AdminList');	
			$bean->listAll();
			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) {
			return $this->render('adminsNotFound');
		}
	}
	public function titleAdmin() {
		try {
			$bean = $this->_getAdminFromGet();

			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		}
		catch (UFex_Dao_NotFound $e) {
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

	public function adminDutyHours() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			// godziny dyżurów mają tylko admini SKOS, nawet boty ich nie mają!
			if ($bean->typeId != UFacl_SruAdmin_Admin::CENTRAL && $bean->typeId != UFacl_SruAdmin_Admin::CAMPUS && $bean->typeId != UFacl_SruAdmin_Admin::LOCAL) {
				return '';
			}

			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listByAdminId($bean->id);
			$d['hours'] = $hours;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function adminDorms() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			try {
				$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
				$admDorm->listAllById($bean->id);
				$d['dormList'] = $admDorm;
			} catch (UFex_Dao_NotFound $e) {
				$d['dormList'] = null;
			}
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function adminHosts() {
		try {
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			// tylko admini Waleta opiekują się hostami
			if ($bean->typeId != UFacl_SruWalet_Admin::DORM && $bean->typeId != UFacl_SruWalet_Admin::OFFICE && $bean->typeId != UFacl_SruWalet_Admin::HEAD) {
				return '';
			}

			$hosts = UFra::factory('UFbean_Sru_ComputerList');
			$hosts->listCaredByAdminId($bean->id);
			$d['hosts'] = $hosts;

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
	public function titleAdminEdit() {
		try {
			UFra::factory('UFbean_Sru_DormitoryList');
			$bean = $this->_getAdminFromGet();
			$d['admin'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
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

			try {
				$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
				$hours->listByAdminId($bean->id);
				$d['dutyHours'] = $hours;
			} catch (UFex_Dao_NotFound $e) {
				$d['dutyHours'] = null;
			}

			try {
				$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
				$admDorm->listAllById($bean->id);
				$d['dormList'] = $admDorm;
			} catch (UFex_Dao_NotFound $e) {
				$d['dormList'] = null;
			}
	
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

	public function switches() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_SwitchList');
			$d['dorm'] = null;
			try {
				$dorm = $this->_getDormFromGet();
				$d['dorm'] = $dorm;
				$bean->listByDormitoryId($dorm->id);
			} catch (UFex_Core_DataNotFound $e) {
				$bean->listAll();
			}
			$d['switches'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchesNotFound');
		}
	}

	public function switchDetails() {
		try {
			$bean = $this->_getSwitchFromGet();
			$d['switch'] = $bean;
			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['info'] = $switch->getStdInfo();
				$d['lockouts'] = $switch->getLockouts();
			} else {
				$d['info'] = null;
				$d['lockouts'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function switchData() {
		try {
			$ip = $this->_srv->get('req')->get->switchIp;
			if (!is_null($ip) || $ip == '') {
				$switch = UFra::factory('UFlib_Snmp_Hp', $ip);
				$d['info'] = $switch->getQuickInfo();
			} else {
				$d['info'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function switchTech() {
		try {
			$bean = $this->_getSwitchFromGet();
			$d['switch'] = $bean;
			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['info'] = $switch->getInfo();
				if ($bean->modelSfpPorts > 0) {
					$d['gbics'] = $switch->getGbics($bean->modelSfpPorts);
				} else {
					$d['gbics'] = null;
				}
			} else {
				$d['info'] = null;
				$d['gbics'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function switchPorts() {
		try {
			$switch = $this->_getSwitchFromGet();
			$bean = UFra::factory('UFbean_SruAdmin_SwitchPortList');
			$bean->listBySwitchId($switch->id);
			$d['ports'] = $bean;
			$d['switch'] = $switch;

			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['portStatuses'] = $switch->getPortStatuses();
				$d['trunks'] = $switch->getTrunks();
			} else {
				$d['portStatuses'] = null;
				$d['trunks'] = null;
			}

			try {
				$port = $this->_getSwitchPortFromGet($d['switch']->id);
				$d['port'] = $port;
			} catch (UFex_Core_DataNotFound $ex) {
				$d['port'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchPortsNotFound');
		}
	}

	public function roomSwitchPorts() {
		try {
			$bean = $this->_getRoomFromGet();
			$d['room'] = $bean;

			$ports = UFra::factory('UFbean_SruAdmin_SwitchPortList');
			$ports->listByLocationId($bean->id);
			$d['ports'] = $ports;
			
			$statuses = array();
			foreach ($ports as $port) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $port['switchIp']);
				$status = $switch->getPortStatus($port['ordinalNo']);
				if (is_null($status)) {
					return $this->render('switchNotFound');
				}
				$statuses[] = $status;
			}
			$d['portStatuses'] = $statuses;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchPortsNotFound');
		}
	}

	public function switchPortDetails() {
		try {
			$switch = $this->_getSwitchFromGet();
			$d['switch'] = $switch;
			$bean = $this->_getSwitchPortFromGet($switch->id);
			$d['port'] = $bean;

			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['alias'] = $switch->getPortAlias($bean->ordinalNo);
			} else {
				$d['alias'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function switchPortMacs() {
		try {
			$switch = $this->_getSwitchFromGet();
			$d['switch'] = $switch;
			$bean = $this->_getSwitchPortFromGet($switch->id);
			$d['port'] = $bean;

			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['macs'] = $switch->getMacsFromPort($bean->ordinalNo);
			} else {
				$d['macs'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function titleSwitch() {
		try {
			$bean = $this->_getSwitchFromGet();
			$d['switch'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleSwitchNotFound');
		}
	}

	public function switchAdd() {
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();

		$swModels = UFra::factory('UFbean_SruAdmin_SwitchModelList');
		$swModels->listAll();
		
		$bean = UFra::factory('UFbean_SruAdmin_Switch');

		$d['switch'] = $bean;
		$d['swModels'] = $swModels;
		$d['dormitories'] = $dorms;


		return $this->render(__FUNCTION__, $d);
	}
	public function titleSwitchEdit() {
		try {
			$bean = $this->_getSwitchFromGet();
			$d['switch'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function switchEdit() {
		try {
			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();

			$swModels = UFra::factory('UFbean_SruAdmin_SwitchModelList');
			$swModels->listAll();
			
			$bean = $this->_getSwitchFromGet();
	
			$d['switch'] = $bean;
			$d['swModels'] = $swModels;
			$d['dormitories'] = $dorms;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function titleSwitchPortsEdit() {
		try {
			$bean = $this->_getSwitchFromGet();
			$d['switch'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function switchPortsEdit() {
		try {		
			$bean = $this->_getSwitchFromGet();

			$ports = UFra::factory('UFbean_SruAdmin_SwitchPortList');
			$ports->listBySwitchId($bean->id);

			$enabledSwitches = UFra::factory('UFbean_SruAdmin_SwitchList');
			$enabledSwitches->listEnabled();
	
			$d['switch'] = $bean;
			$d['ports'] = $ports;
			$d['enabledSwitches'] = $enabledSwitches;

			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['portAliases'] = $switch->getPortAliases();
			} else {
				$d['portAliases'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchPortsNotFound');
		}
	}

	public function switchPortEdit() {
		try {
			$switch = $this->_getSwitchFromGet();
			$d['switch'] = $switch;
			$bean = $this->_getSwitchPortFromGet($switch->id);
			$d['port'] = $bean;

			$enabledSwitches = UFra::factory('UFbean_SruAdmin_SwitchList');
			$enabledSwitches->listEnabled();
			$d['enabledSwitches'] = $enabledSwitches;

			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['status'] = $switch->getPortStatus($bean->ordinalNo);
			} else {
				$d['status'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchNotFound');
		}
	}

	public function switchLockoutsEdit() {
		try {		
			$bean = $this->_getSwitchFromGet();	
			$d['switch'] = $bean;

			if (!is_null($d['switch']->ip)) {
				$switch = UFra::factory('UFlib_Snmp_Hp', $d['switch']->ip);
				$d['lockouts'] = $switch->getLockouts();
			} else {
				$d['lockouts'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('switchPortsNotFound');
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
			if(!isset($_COOKIE['SRUDisplayUsers']) || $_COOKIE['SRUDisplayUsers'] == '0' || isset($_COOKIE['SRUDisplayUsersChanged']))
				$bean->listByRoom($room->id);
			else if($_COOKIE['SRUDisplayUsers'] == '1')
				$bean->listByRoomActiveOnly($room->id);
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
			if(!isset($_COOKIE['SRUDisplayUsers']) || $_COOKIE['SRUDisplayUsers'] == '0' || isset($_COOKIE['SRUDisplayUsersChanged'])){
				$bean->listByRoom($room->id);
				if(isset($_COOKIE['SRUDisplayUsersChanged']))
					setcookie('SRUDisplayUsersChanged', '', time() - 3600, '/');
			}
			else if($_COOKIE['SRUDisplayUsers'] == '1')
				$bean->listByRoomActiveOnly($room->id);
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

		try {
			$servers = UFra::factory('UFbean_Sru_ComputerList');
			$servers->listAllPhysicalServers();
			$d['servers'] = $servers;
		} catch (UFex $e) {
			$d['servers'] = null;
		}
		try {
			$waletAdmins = UFra::factory('UFbean_SruWalet_AdminList');
			$waletAdmins->listAll();
			$d['waletAdmins'] = $waletAdmins;
		} catch (UFex $e) {
			$d['waletAdmins'] = null;
		}

		$d['computer'] = $bean;
		$user = $this->_getUserFromGet();
		$d['user'] = $user;

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
	public function serverAliases() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
			$bean->listAll();

			$d['aliases'] = $bean;

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
			$user = $this->_getUserFromGet();
			$d['user'] = $user;

			if ($this->_srv->get('req')->get->is('templateId') && $this->_srv->get('req')->get->templateId>0) {
				try {
					$tpl = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
					$tpl->getByPK($this->_srv->get('req')->get->templateId);
					if ($user->lang == 'en' && $tpl->reasonEn != '') {
						$bean->reason = $tpl->reasonEn;
					} else {
						$bean->reason = $tpl->reason;
					}
					$bean->duration = $tpl->duration;
					$bean->after = $tpl->amnesty;
					$typeId = $tpl->typeId;
				} catch (UFex_Dao_NotFound $e) {
				}
			}
			$d['penalty'] = $bean;

			try{
				$comp = UFra::factory('UFbean_Sru_ComputerList');
				$d['computers'] =& $comp;
				$comp->listByUserId($d['user']->id); 
				if (isset($typeId)) {
					switch ($typeId) {
						case UFbean_SruAdmin_Penalty::TYPE_WARNING:
							$d['computerId'] = 0;
							break;
						case UFbean_SruAdmin_Penalty::TYPE_COMPUTER:
							$d['computerId'] = $this->_srv->get('req')->get->computerId;
							break;
						case UFbean_SruAdmin_Penalty::TYPE_COMPUTERS:
							$d['computerId'] = '';
							break;
					}
				} else {
					$d['computerId'] = $this->_srv->get('req')->get->computerId;
				}
			} catch (UFex_Dao_NotFound $e) {
				if ($typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
					$d['computerId'] = 0;
				} else {
					$d['computerId'] = '';
				}
			} catch (UFex_Core_DataNotFound $e) {
				$d['computerId'] = null;
			}
			
			$templates = UFra::factory('UFbean_SruAdmin_PenaltyTemplateList');
			$templates->listAll();
			$d['templates'] = $templates;		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userNotFound');
		}		

	}
	
	public function penaltyTemplateChoose() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplateList');
			$bean->listAll();
			$d['templates'] = $bean;

			$user = $this->_getUserFromGet();
			$d['user'] = $user;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}		
	}

	public function penaltyTemplates() {
		try {
			try {
				$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplateList');
				$bean->listAll();
				$d['templates'] = $bean;
			} catch (UFex_Dao_NotFound $e) {
				$d['templates'] = null;
			}
			try {
				$inactive = UFra::factory('UFbean_SruAdmin_PenaltyTemplateList');
				$inactive->listInactive();
				$d['inactive'] = $inactive;
			} catch (UFex_Dao_NotFound $e) {
				$d['inactive'] = null;
			}
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function penaltyTemplateChange() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplateList');
			$bean->listAll();
			$d['templates'] = $bean;

			$bean = $this->_getPenaltyFromGet();
			$d['penalty'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function penaltyTemplateAdd() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
			$d['template'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function titlePenaltyTemplateEdit() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
			$bean = $this->_getPenaltyTemplateFromGet();
			$d['template'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function penaltyTemplateEdit() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
			$bean = $this->_getPenaltyTemplateFromGet();
			$d['template'] = $bean;
			
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
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

	public function penaltyEdit() {
		try {
			$bean = $this->_getPenaltyFromGet();
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);

			if ($this->_srv->get('req')->get->is('templateId') && $this->_srv->get('req')->get->templateId>0) {
				try {
					$tpl = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
					$tpl->getByPK($this->_srv->get('req')->get->templateId);
					if ($user->lang == 'en' && $tpl->reasonEn != '') {
						$bean->reason = $tpl->reasonEn;
					} else {
						$bean->reason = $tpl->reason;
					}
					$bean->duration = $tpl->duration;
					$bean->endAt = ($bean->startAt + $tpl->duration * 24 * 3600);
					$bean->after = $tpl->amnesty;
					$bean->amnestyAfter = ($bean->startAt + $tpl->amnesty * 24 * 3600);
					$bean->templateId = $tpl->id;
					$d['templateTitle'] = $tpl->title;
				} catch (UFex_Dao_NotFound $e) {
				}
			}
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

	public function penaltyHistory() {
		try {
			$bean = $this->_getPenaltyFromGet();
			$d['penalty'] = $bean;

			$history = UFra::factory('UFbean_SruAdmin_PenaltyHistoryList');
			try {
				$history->listByPenaltyId($bean->id);
			} catch (UFex_Dao_NotFound $e) {
			}
			$d['history'] = $history;

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
	
	public function computerPenalties() 
	{
		try 
		{
			$computer = $this->_getComputerFromGet();
			$d['computer'] = $computer;

			$bean = UFra::factory('UFbean_SruAdmin_ComputerBanList');
			$bean->listAllByComputerId($computer->id);
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('penaltiesNotFound');
		}
	}
	public function titleComputerPenalties() {
		try {
			$bean = $this->_getComputerFromGet();

			$d['computer'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('titleComputerNotFound');
		}
	}

	public function penaltyActions() {
		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$bean->listLastAdded(2);
			$d['addedPenalties'] = $bean;

			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$bean->listLastAdded(1);
			$d['addedWarnings'] = $bean;

			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$bean->listLastModified(2);
			$d['modifiedPenalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} 
		catch (UFex_Dao_NotFound $e) 
		{
			return $this->render('penaltiesNotFound');
		}
	}

	public function servicesEdit() {
		$d[''] = null;
		try {
			$allServices = UFra::factory('UFbean_Sru_ServiceList');	
			$allServices->listAllServices();
			$d['allServices'] = $allServices;
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userServicesNotFound');
		}

		$serviceType = null;
		if ($this->_srv->get('req')->post->is('serviceSelect')) {
			$get = $this->_srv->get('req')->post->serviceSelect;
			if (isset($get['serviceId']) && $get['serviceId'] > 0) {
				$serviceType = $get['serviceId'];
			}
		}
		try 
		{		
			$bean = UFra::factory('UFbean_SruAdmin_UserServiceList');	
			$bean->listToActivate($serviceType);
			$d['toActivate'] = $bean;
		}
		catch (UFex_Dao_NotFound $e) {}

		try 
		{		
			$bean = UFra::factory('UFbean_SruAdmin_UserServiceList');	
			$bean->listToDeactivate($serviceType);
			$d['toDeactivate'] = $bean;
		}
		catch (UFex_Dao_NotFound $e) {}

		return $this->render(__FUNCTION__, $d);
	}

	public function servicesList() {
		$d[''] = null;
		try {
			$allServices = UFra::factory('UFbean_Sru_ServiceList');	
			$allServices->listAllServices();
			$d['allServices'] = $allServices;
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('userServicesNotFound');
		}

		$serviceType = null;
		if ($this->_srv->get('req')->post->is('serviceSelect')) {
			$get = $this->_srv->get('req')->post->serviceSelect;
			if (isset($get['serviceId']) && $get['serviceId'] > 0) {
				$serviceType = $get['serviceId'];
			}
		}

		try 
		{
			$bean = UFra::factory('UFbean_SruAdmin_UserServiceList');
			$bean->listActive($serviceType);
			$d['active'] = $bean;
		}
		catch (UFex_Dao_NotFound $e) {}
		
		return $this->render(__FUNCTION__, $d);
	}

	public function userServicesEdit() {
		try {
			$user = $this->_getUserFromGet();
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

	public function ips() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Ips');
			$d['ips'] =& $bean;	

			$d['dorm'] = null;
			try {
				$dorm = $this->_getDormFromGet();
				$d['dorm'] = $dorm;
				$bean->listByDormitory($dorm->id);

				$used = UFra::factory('UFbean_Sru_Ipv4');
				$used->getUsedByDorm($dorm->id);
				$d['used'] = $used;
				$sum = UFra::factory('UFbean_Sru_Ipv4');
				$sum->getSumByDorm($dorm->id);
				$d['sum'] = $sum;
			} catch (UFex_Core_DataNotFound $e) {
				$bean->listAll();
				$d['used'] = null;
				$d['sum'] = null;
			}
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function statsTransfer() {
		try {
			$transfer = UFra::factory('UFbean_SruAdmin_Transfer');
			$transfer->listTop();
			$d['transfer'] = $transfer;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function statsUsers() {
		try {
			$user = UFra::factory('UFbean_Sru_UserList');
			$user->listAllActive();
			$d['users'] = $user;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function statsDormitories() {
		try {
			$user = UFra::factory('UFbean_Sru_UserList');
			$user->listAllActive();
			$d['users'] = $user;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function statsPenalties() {
		try {
			$penalty = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$penalty->listAllPenalties();
			$d['penalties'] = $penalty;
		
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound', $d);
		}
	}

	public function adminPenaltiesAdded() {
		try {
			$bean = $this->_getAdminFromGet();
			
			$added = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$added->listLastAdded(2, $bean->id);
			$d['addedPenalties'] = $added;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function adminWarningsAdded() {
		try {
			$bean = $this->_getAdminFromGet();

			$added = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$added->listLastAdded(1, $bean->id);
			$d['addedWarnings'] = $added;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function adminPenaltiesModified() {
		try {
			$bean = $this->_getAdminFromGet();
			
			$modified = UFra::factory('UFbean_SruAdmin_PenaltyList');
			$modified->listLastModified(2, $bean->id);
			$d['modifiedPenalties'] = $modified;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	/**
	 * Ostatnio modyfikowani użytkownicy
	 *
	 */
	public function adminUsersModified() {
		try{
			$bean = $this->_getAdminFromGet();
			
			$modified = UFra::factory('UFbean_Sru_UserList');
			$modified->listLastModified($bean->id);
			$d['modifiedUsers'] = $modified;
			
			return $this->render(__FUNCTION__, $d);
		}catch(UFex_Dao_NotFound $e){
			return '';
		}	
	}
	
	/**
	 * Ostatnio modyfikowane komputery
	 *
	 */
	public function adminComputersModified() {
		try{
			$bean = $this->_getAdminFromGet();
			
			$modified = UFra::factory('UFbean_Sru_ComputerList');
			$modified->listLastModified($bean->id);
			$d['modifiedComputers'] = $modified;
			
			return $this->render(__FUNCTION__, $d);
		}catch(UFex_Dao_NotFound $e){
			return '';
		}	
	}
	
	/**
	 * Ostatnio modyfikowane usługi (poziom administratora)
	 *
	 */
	public function adminUserServicesModified() {
		try{
			$bean = $this->_getAdminFromGet();
			
			$modified = UFra::factory('UFbean_Sru_UserServiceList');
			$modified->listLastModified($bean->id, 1);
			$d['modifiedUserServices'] = $modified;
			
			return $this->render(__FUNCTION__, $d);
		}catch(UFex_Dao_NotFound $e){
			return '';
		}	
	}
	
	/**
	 * Ostatnio modyfikowane usługi (poziom użytkownika)
	 *
	 */
	public function adminUserServicesRequested() {
		try{
			$bean = $this->_getAdminFromGet();
			
			$modified = UFra::factory('UFbean_Sru_UserServiceList');
			$modified->listLastModified($bean->id, 2);
			$d['requestedUserServices'] = $modified;
			
			return $this->render(__FUNCTION__, $d);
		}catch(UFex_Dao_NotFound $e){
			return '';
		}	
	}
	
	public function penaltyAddMailTitle($penalty, $user) {
		$d['user'] = $user;
		$d['penalty'] = $penalty;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function penaltyAddMailBody($penalty, $user, $computers, $admin) {
		$d['penalty'] = $penalty;
		$d['user'] = $user;
		$d['computers'] = $computers;
		$d['admin'] = $admin;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function switchPortModifiedMailTitle($switchPort) {
		$d['port'] = $switchPort;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function switchPortModifiedMailBody($switchPort, $admin, $enabled) {
		$d['port'] = $switchPort;
		$d['admin'] = $admin;
		$d['enabled'] = $enabled;
		return $this->render(__FUNCTION__, $d);
	}

	public function penaltyEditMailTitle($penalty, $user) {
		$d['user'] = $user;
		$d['penalty'] = $penalty;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function penaltyEditMailBody($penalty, $oldPenalty, $newTpl, $user, $admin) {
		$d['penalty'] = $penalty;
		$d['oldPenalty'] = $oldPenalty;
		$d['newTpl'] = $newTpl;
		$d['user'] = $user;
		$d['admin'] = $admin;
		$d['host'] = $this->_srv->get('req')->server->HTTP_HOST;
		return $this->render(__FUNCTION__, $d);
	}

	public function dataChangedMailTitle($user) {
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function dataChangedMailBody($user, $history) {
		$d['user'] = $user;
		$d['history'] = $history;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostChangedMailTitle($host, $user) {
		$d['host'] = $host;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostChangedMailBody($host, $action, $user, $history = null, $admin = null) {
		$d['host'] = $host;
		$d['action'] = $action;
		$d['user'] = $user;
		$d['history'] = $history;
		$d['admin'] = $admin;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostAliasesChangedMailTitle($host) {
		$d['host'] = $host;
		return $this->render(__FUNCTION__, $d);
	}

	public function hostAliasesChangedMailBody($host, array $deleted, $added, $admin) {
		$d['host'] = $host;
		$d['deleted'] = $deleted;
		$d['added'] = $added;
		$d['admin'] = $admin;
		return $this->render(__FUNCTION__, $d);
	}
}
