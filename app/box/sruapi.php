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
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_STUDENT);
	}

	public function dhcpOrg() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_ORGANIZATION);
	}

	public function dhcpAdm() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_ADMINISTRATION);
	}

	public function dhcpServ() {
		return $this->configDhcp(UFbean_Sru_Computer::TYPE_SERVER);
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
				UFbean_Sru_Computer::TYPE_ORGANIZATION,
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
	
	public function error500() {
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

	public function myLanstats() {
		try {
			$serv = $this->_srv->get('req')->server;
			$ip =  $serv->REMOTE_ADDR;
			// znajdujemy właściciela
			$computer = UFra::factory('UFbean_Sru_Computer');
			$computer->getByIp($ip);
			// znajdujemy wszystkie komputery właściciela
			$computersList = UFra::factory('UFbean_Sru_ComputerList');
			$computersList->listByUserId($computer->userId);
			// znajdujemy upload dla każdego komputera
			$upload = array();
			foreach ($computersList as $computer) {
				try {
					$transfer = UFra::factory('UFbean_SruAdmin_Transfer');
					$transfer->listByIp($computer['ip']);
					$upload[$computer['host']] = $transfer;
				} catch (UFex_Dao_NotFound $e) {
					$upload[$computer['host']] = null;
				}
			}
			$d['upload'] = $upload;
			$d['transfer'] = UFra::factory('UFbean_SruAdmin_Transfer');

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
}
