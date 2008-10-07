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
}
