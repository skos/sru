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
				UFbean_Sru_Computer::TYPE_ORGANIZATION
			));

			$d['computers'] = $bean;

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
}
