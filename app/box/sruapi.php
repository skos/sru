<?

/**
 * sru api
 */
class UFbox_SruApi
extends UFbox {

	public function dhcp() {
		try {
			$domain = $this->_srv->get('req')->get->domain;
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByDomain($domain);
			
			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dnsRev() {
		$bean = UFra::factory('UFbean_Sru_ComputerList');
		$switches = UFra::factory('UFbean_SruAdmin_SwitchList');
		$ipClass = $this->_srv->get('req')->get->ipClass;
		if (preg_match('/^[0-9]{1,3}(\.[0-9]{1,3}){2}$/', $ipClass)) {
			try {
				$bean->listAllActiveByIpClass($this->_srv->get('req')->get->ipClass, 24);
				$d['computers'] = $bean;
			} catch (UFex_Dao_NotFound $e) {
				$d['computers'] = null;
			}
			try {
				$switches->listAllActiveByIpClass($this->_srv->get('req')->get->ipClass, 24);
				$d['switches'] = $switches;
			} catch (UFex_Dao_NotFound $e) {
				$d['switches'] = null;
			}
			$d['mask'] = 24;
		} else if (preg_match('/^[0-9]{1,3}(\.[0-9]{1,3}){1}$/', $ipClass)) {
			try {
				$bean->listAllActiveByIpClass($this->_srv->get('req')->get->ipClass, 16);
				$d['computers'] = $bean;
			} catch (UFex_Dao_NotFound $e) {
				$d['computers'] = null;
			}
			try {
				$switches->listAllActiveByIpClass($this->_srv->get('req')->get->ipClass, 16);
				$d['switches'] = $switches;
			} catch (UFex_Dao_NotFound $e) {
				$d['switches'] = null;
			}
			$d['mask'] = 16;
		} else {
			return '';
		}

		return $this->render(__FUNCTION__, $d);
	}
	
	public function dns() {
		try {
			$domain = $this->_srv->get('req')->get->domain;
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByDomain($domain);
			$d['computers'] = $bean;
			
			try {
				$switches = UFra::factory('UFbean_SruAdmin_SwitchList');
				$switches->listAllActiveByDomain($domain);
				$d['switches'] = $switches;
			} catch (UFex_Dao_NotFound $e) {
				$d['switches'] = null;
			}

			try {
				$aliases = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
				$aliases->listAllByDomain($domain);
				$d['aliases'] = $aliases;
			} catch (UFex_Dao_NotFound $e) {
				$d['aliases'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function ethers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listEthers();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function skosEthers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listSkosEthers();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function admins() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAdmins();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function exadmins() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listExAdmins();

			$d['computers'] = $bean;

			return $this->render('admins', $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function tourists() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listTourists();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function switches() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_SwitchList');
			$bean->listEnabled();

			$d['switches'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function findMac() {
		try {
			$hp = UFra::factory('UFlib_Snmp_Hp');
			$switchPort = $hp->findMac($this->_srv->get('req')->get->mac);
			if (is_null($switchPort)) {
				return '';
			}

			$d['switchPort'] = $switchPort;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function switchesStructure() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_SwitchPortList');
			$bean->listPortsWithSwitches();

			$d['switchPorts'] = $bean;

			try {
				$dorm = UFra::factory('UFbean_Sru_Dormitory');
				$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
				$d['dormitory'] = $dorm;
			} catch (UFex_Dao_NotFound $e) {
				return '';
			} catch (UFex_Core_DataNotFound $e) {
				$d['dormitory'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function switchesModelIps() {
		try {
			$model = $this->_srv->get('req')->get->model;
			
			$bean = UFra::factory('UFbean_SruAdmin_SwitchList');
			$bean->listEnabledByModelNo($model);

			$d['switches'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function switchesModels() {
		try {
			try {
				$ds = $this->_srv->get('req')->get->ds;
			} catch (UFex_Core_DataNotFound $e) {
				$ds = null;
			}

			$bean = UFra::factory('UFbean_SruAdmin_SwitchList');
			if (is_null($ds)) {
				$bean->listEnabled();
			} else {
				$bean->listEnabledByDormAlias($ds);
			}

			$d['switches'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function penaltiesPast() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_PenaltyList');	
			$bean->listPast();
			$d['penalties'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function error403() {
		UFlib_Http::notAuthorised('SRU');
		return '';
	}

	public function status200() {
		return '';
	}

	public function computersLocations() {
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listActiveStudsByDormitoryId($dorm->id);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function computersOutdated() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listOutdated();

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function computersNotSeen() {
		try {
			$conf = UFra::shared('UFconf_Sru'); 
			
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listNotSeen($conf->computersMaxNotSeen);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function computersServers() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllServers(true, true);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	
	public function usersToDeactivate() {
		try {
			$bean = UFra::factory('UFbean_Sru_UserList');
			$bean->listToDeactivate();

			$d['users'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function usersToRemove() {
		try {
			$bean = UFra::factory('UFbean_Sru_UserList');
			$bean->listToRemove();

			$d['users'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function adminsOutdated() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_AdminList');
			$bean->listOutdated();

			$d['admins'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function dormitoryIps() {
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
			$ips = UFra::factory('UFbean_Sru_Ipv4List');
			$ips->listByDormitoryId($dorm->id);
			$d['ips'] = $ips;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dormitoryFreeIps() {
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
			$sum = UFra::factory('UFbean_Sru_Ipv4');
			$sum->getUsedByDorm($dorm->id);
			$d['used'] = $sum;
			$sum = UFra::factory('UFbean_Sru_Ipv4');
			$sum->getSumByDorm($dorm->id);
			$d['sum'] = $sum;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function apiPenaltiesTimelineMailBody($added, $modified, $ending) {
		$d['added'] = $added;
		$d['modified'] = $modified;
		$d['ending'] = $ending;
		return $this->render(__FUNCTION__, $d);
	}

	public function dutyHours() {
		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listAllForTable();
			$d['hours'] = $hours;

			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();
			$d['dormitories'] = $dorms;
			$d['dormAdmins'] = array();
			foreach($dorms as $c){
				try {
					$dormAdm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$dormAdm->listAllByDormId($c['id']);
					$d['dormAdmins'][$c['id']] = $dormAdm;
				} catch(UFex_Dao_NotFound $e) {
				}
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dutyHoursUpcoming() {
		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listAllUpcoming();
			$d['hours'] = $hours;

			$days = $this->_srv->get('req')->get->days;
			if ($days > 6 || $days < 0) $days = 0;
			$d['days'] = $days;

			$admins = UFra::factory('UFbean_SruAdmin_AdminList');
			$admins->listAll();
			$d['dormitories'] = array();
			foreach($admins as $c){
				try {
					$admDorm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$admDorm->listAllById($c['id']);
					$d['dormitories'][$c['id']] = $admDorm;
				} catch(UFex_Dao_NotFound $e) {
					$d['dormitories'][$c['id']] = null;	//na pewno zaden ds nie bedzie mial id null
				}
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function firewallExceptions() {
		try {
			$fwExceptions = UFra::factory('UFbean_SruAdmin_FwExceptionList');
			$fwExceptions->listActive();
			$d['fwExcpetions'] = $fwExceptions;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function firewallExceptionsOutdated() {
		try {
			$fwExceptions = UFra::factory('UFbean_SruAdmin_FwExceptionList');
			$fwExceptions->listOutdated();
			$d['fwExcpetions'] = $fwExceptions;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}
	
	public function validatorResults() {
		$get = $this->_srv->get('req')->get;
		$d['test'] = $get->validatorTest;
		$d['object'] = $get->validatorObject;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function hostDeactivatedMailTitle($host, $user) {
		$d['host'] = $host;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
	
	public function hostDeactivatedMailBody($host, $action, $user) {
		$d['host'] = $host;
		$d['action'] = $action;
		$d['user'] = $user;
		return $this->render(__FUNCTION__, $d);
	}
}
