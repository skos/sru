<?

/**
 * sru api
 */
class UFbox_SruApi
extends UFbox {

	protected function configDhcp($type) {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByType($type);

			$d['computers'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dhcpStuds() {
		return $this->configDhcp(array(UFbean_Sru_Computer::TYPE_STUDENT, UFbean_Sru_Computer::TYPE_STUDENT_AP, UFbean_Sru_Computer::TYPE_STUDENT_OTHER, UFbean_Sru_Computer::TYPE_TOURIST));
	}
	
	public function dhcpOrg() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_ORGANIZATION);
	}

	public function dhcpAdm() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_ADMINISTRATION);
	}

	public function dhcpServ() {
		return $this->configDhcp(array(UFbean_Sru_Computer::TYPE_SERVER, UFbean_Sru_Computer::TYPE_SERVER));
	}

	public function dnsRev() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByIpClass((int)$this->_srv->get('req')->get->ipClass);

			$d['computers'] = $bean;

			return $this->render('configDnsRev', $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dnsDs() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByType(array(
				UFbean_Sru_Computer::TYPE_STUDENT,
				UFbean_Sru_Computer::TYPE_STUDENT_AP,
				UFbean_Sru_Computer::TYPE_STUDENT_OTHER,
				UFbean_Sru_Computer::TYPE_TOURIST,
				UFbean_Sru_Computer::TYPE_ORGANIZATION,
				UFbean_Sru_Computer::TYPE_SERVER,
				UFbean_Sru_Computer::TYPE_SERVER_VIRT,
				UFbean_Sru_Computer::TYPE_SERVER
			));

			$d['computers'] = $bean;

			try {
				$aliases = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
				$aliases->listAll();
				$d['aliases'] = $aliases;
			} catch (UFex_Dao_NotFound $e) {
				$d['aliases'] = null;
			}

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
	}

	public function dnsAdm() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$bean->listAllActiveByType(UFbean_Sru_Computer::TYPE_ADMINISTRATION);

			$d['computers'] = $bean;

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
	
	public function apiPenaltiesTimelineMailBody($added, $modified) {
		$d['added'] = $added;
		$d['modified'] = $modified;
		return $this->render(__FUNCTION__, $d);
	}

	public function dutyHours() {
		try {
			$hours = UFra::factory('UFbean_SruAdmin_DutyHoursList');
			$hours->listAllForTable();
			$d['hours'] = $hours;

			$dorms = UFra::factory('UFbean_Sru_DormitoryList');
			$dorms->listAll();
			$d['dormitories'] = array();
			foreach($dorms as $c){
				try {
					$dormAdm = UFra::factory('UFbean_SruWalet_AdminDormitoryList');
					$dormAdm->listAllByDormId($c['id']);
					$d['dormitories'][$c['id']] = $dormAdm;
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
			$admins = UFra::factory('UFbean_Sru_ComputerList');
			$admins->listAdmins();
			$d['admins'] = $admins;
			
			$exadmins = UFra::factory('UFbean_Sru_ComputerList');
			$exadmins->listExAdmins();
			$d['exadmins'] = $exadmins;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return '';
		}
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
