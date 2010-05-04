<?
/**
 * Obsługa SNMP w HP
 */
class UFlib_Snmp_Hp 
extends UFlib_Snmp {

	protected $ip = '';
	protected $communityR = '';
	protected $communityW = '';
	protected $timeout = 300000;

	const UP = "up";
	const DOWN = "down";
	const DISABLED = "disabled";
	const ENABLED = "enabled";

	protected $OIDs = array(
		'ios' => '1.3.6.1.2.1.1.1',
		'uptime' => '1.3.6.1.2.1.1.3',
		'cpu' => '1.3.6.1.4.1.11.2.14.11.5.1.9.6.1.0',
		'memAll' => '1.3.6.1.4.1.11.2.14.11.5.1.1.2.1.1.1.5.1',
		'memUsed' => '1.3.6.1.4.1.11.2.14.11.5.1.1.2.1.1.1.6.1',
		'serialNo' => 'mib-2.47.1.1.1.1.11.1',
		'portAliases' => '.1.3.6.1.2.1.31.1.1.1.18',
		'portActivities' => '.1.3.6.1.2.1.2.2.1.8',
		'portStatuses' => '.1.3.6.1.2.1.2.2.1.7',
		'macs' => '1.3.6.1.4.1.11.2.14.2.10.5.1.3.1',
		'lockouts' => 'mib-2.17.7.1.3.1.1.4.4095',
		'port' => '.1.3.6.1.2.1.17.4.3.1.2',
	);

	public function uFlib_Snmp_Hp ($ip = null) {
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$this->ip = $ip;
		// ustawienie community
		$conf = UFra::shared('UFconf_Sru');
		$this->communityR = $conf->communityRead;
		$this->communityW = $conf->communityWrite;
	}

	public function getInfo() {
		$info = array();
		$ios = @snmpwalk($this->ip , $this->communityR , $this->OIDs['ios'], $this->timeout);
		if ($ios == false) {
			return null;
		}
		$info['ios'] = $ios[0];
		$uptime = @snmpwalk($this->ip, $this->communityR, $this->OIDs['uptime'], $this->timeout);
		$info['uptime'] = $uptime[0];
		$info['cpu'] = @snmpget($this->ip, $this->communityR, $this->OIDs['cpu'], $this->timeout);
		$info['memAll'] = @snmpget($this->ip, $this->communityR, $this->OIDs['memAll'], $this->timeout);
		$info['memUsed'] = @snmpget($this->ip, $this->communityR, $this->OIDs['memUsed'], $this->timeout);
		$info['serialNo'] = trim(@snmpget($this->ip, $this->communityR, $this->OIDs['serialNo'], $this->timeout));
		return $info;
	}

	public function getPortAliases() {
		$aliases = @snmpwalk($this->ip , $this->communityR, $this->OIDs['portAliases'], $this->timeout);
		if ($aliases == false) {
			return null;
		}
		return $aliases;
	}

	public function getPortAlias($port) {
		$alias = @snmpget($this->ip , $this->communityR, $this->OIDs['portAliases'].'.'.$port, $this->timeout);
		if ($alias == false) {
			return null;
		}
		return $alias;
	}

	public function getPortStatuses() {
		$activities = @snmpwalk($this->ip, $this->communityR, $this->OIDs['portActivities'], $this->timeout);
		if ($activities == false) {
			return null;
		}
		$statuses = @snmpwalk($this->ip, $this->communityR, $this->OIDs['portStatuses'], $this->timeout);

		$result = array();
		for ($i = 0; $i < count($statuses); $i++) {
			if ($statuses[$i] == 2) {
				$result[$i] = self::DISABLED;
			} else if ($activities[$i] == 2) {
				$result[$i] = self::DOWN;
			} else {
				$result[$i] = self::UP;
			}
		}

		return $result;
	}

	public function getPortStatus($port) {
		$status = @snmpget($this->ip, $this->communityR, $this->OIDs['portStatuses'].'.'.$port, $this->timeout);
		if ($status == false) {
			return null;
		}
		if ($status == 2) {
			$result = self::DISABLED;
		} else {
			$result = self::ENABLED;
		}
		return $result;
	}

	public function setPortStatus($port, $status) {
		if ($status == self::DISABLED) {
			$statusInt = 2;
		} else {
			$statusInt = 1;
		}
		return @snmpset($this->ip, $this->communityW, $this->OIDs['portStatuses'].'.'.$port, 'i', $statusInt, $this->timeout);
	}

	public function getLockouts() {
		$lockouts = @snmprealwalk($this->ip , $this->communityR, $this->OIDs['lockouts'], $this->timeout);
		if ($lockouts == false) {
			return null;
		}
		$lockouts = array_keys($lockouts);
		for ($i = 0; $i < count($lockouts); $i++) {
			$lockouts[$i] = $this->int2mac(str_replace('SNMPv2-SMI::mib-2.17.7.1.3.1.1.4.4095.', '', $lockouts[$i]));
		}
		return $lockouts;
	}

	public function setPortAlias($port, $name) {
		return @snmpset($this->ip, $this->communityW, $this->OIDs['portAliases'].'.'.$port, 's', $name, $this->timeout);
	}

	public function getMacsFromPort($port) {
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$macs = @snmpwalk($this->ip , $this->communityR, $this->OIDs['macs'].'.'.$port, $this->timeout);
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		if ($macs == false) {
			return null;
		}
		return str_replace(' ', ':', $this->clearResults($macs));
	}

	public function setLockoutMac($mac, $insert = true) {
		if ($insert) {
			$op = '3';
		} else {
			$op = '2';
		}
		return @snmpset($this->ip, $this->communityW, $this->OIDs['lockouts'].'.'.$this->mac2int($mac).'.0', 'i', $op, $this->timeout);
	}

	public function findMac($searchMac) {
		$conf = UFra::shared('UFconf_Sru');
		// pobranie pierwszego mac-a z tablicy
		$switchIp = $conf->masterSwitch;
		$needle = $this->mac2int($searchMac);

		$watchdog = 20;
		while ($watchdog > 0) {
			$portUser = @snmpget($switchIp, $this->communityR, $this->OIDs['port'].'.'.$needle, $this->timeout);
			if ($portUser) {
				$portUser = (int)$portUser;

				// sprawdzamy, czy na znalezionym porcie jest jakis switch
				try {
					$switchPort = UFra::factory('UFbean_SruAdmin_SwitchPort');
					$switchPort->getByIpAndOrdinalNo($switchIp, $portUser);
					if (!is_null($switchPort->connectedSwitchIp)) {
						$switchIp = $switchPort->connectedSwitchIp;
						continue;
					} else {
						return $switchPort;
					}
				} catch (UFex $e) {
					return null;
				}
			} else {
				return null;
			}
			--$watchdog;
		}
		return null;
	}
}
