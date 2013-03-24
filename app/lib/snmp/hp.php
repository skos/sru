<?
/**
 * Obsługa SNMP w HP
 */
class UFlib_Snmp_Hp 
extends UFlib_Snmp {

	protected $ip = '';
	protected $switch = null;
	protected $communityR = '';
	protected $communityW = '';
	protected $timeout = 300000;

	const UP = "up";
	const DOWN = "down";
	const DISABLED = "disabled";
	const ENABLED = "enabled";
	
	static public $learnModes = array(
		1 => 'learnContinuous',
		2 => 'learnFirstN',
		3 => 'learnFirstNConditionally',
		4 => 'configureSpecific',
		5 =>'learn8021xAuthorized',
		6 =>'learnLimitedContinuous',
	);
	
	static public $alarmStates = array(
		1 => 'wyłączony',
		2 => 'wyślij powiadomienie',
		3 => 'wyślij powiadomienie i wyłącz port',
	);

	protected $OIDs = array(
		'ios' => '1.3.6.1.2.1.1.1',
		'uptime' => '1.3.6.1.2.1.1.3',
		'cpu' => '1.3.6.1.4.1.11.2.14.11.5.1.9.6.1.0',
		'memAll' => '1.3.6.1.4.1.11.2.14.11.5.1.1.2.1.1.1.5.1',
		'memFree' => '1.3.6.1.4.1.11.2.14.11.5.1.1.2.1.1.1.6.1',
		'serialNo' => 'SNMPv2-SMI::mib-2.47.1.1.1.1.11.1',
		'model' => '1.3.6.1.4.1.11.2.36.1.1.2.5.0',
		'portAliases' => '.1.3.6.1.2.1.31.1.1.1.18',
		'portActivities' => '.1.3.6.1.2.1.2.2.1.8',
		'portStatuses' => '.1.3.6.1.2.1.2.2.1.7',
		'portSpeeds' => '1.3.6.1.2.1.31.1.1.1.15',
		'macs' => '1.3.6.1.4.1.11.2.14.11.5.1.9.4.2.1.2',
		'lockouts' => 'mib-2.17.7.1.3.1.1.4.4095',
		'port' => '.1.3.6.1.2.1.17.4.3.1.2',
		'trunk' => '1.3.6.1.4.1.11.2.14.11.5.1.7.1.3.1.1.8',
		'gbicModel' => 'SNMPv2-SMI::mib-2.47.1.1.1.1.13',
		'gbicSerial' => '1.3.6.1.4.1.11.2.14.10.5.1.1.1.1.2',
		'vlans' => '1.3.6.1.2.1.17.7.1.4.3.1.1',
		'untaggedVlanOnPorts' => '1.3.6.1.2.1.17.7.1.4.5.1.1',
		'intrusionFlag' => '1.3.6.1.4.1.11.2.14.2.10.3.1.7.1',
		'learnMode' => '1.3.6.1.4.1.11.2.14.2.10.3.1.4.1',
		'addrLimit' => '1.3.6.1.4.1.11.2.14.2.10.3.1.3.1',
		'alarmState' => '1.3.6.1.4.1.11.2.14.2.10.3.1.6.1',
		'loopProtect' => '1.3.6.1.4.1.11.2.14.11.5.1.12.1.5.2.1.1.1',
	);

	public $biggerTrunkNumbers = array(
		'J4906A',
		'J8435A',
		'J8693A',
		'J9145A',
		'J9147A',
		'J9452A',
		'J8697A',
	);

	public $movedPortsNumbers = array(
		'J9452A',
	);

	public function uFlib_Snmp_Hp ($ip = null, $switch = null) {
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$this->ip = $ip;
		$this->switch = $switch;
		// ustawienie community
		$conf = UFra::shared('UFconf_Sru');
		$this->communityR = $conf->communityRead;
		$this->communityW = $conf->communityWrite;
	}

	public function getInfo() {
		$info = array();
		$ios = snmpwalk($this->ip , $this->communityR , $this->OIDs['ios'], $this->timeout);
		if ($ios == false) {
			return null;
		}
		$info['ios'] = $ios[0];
		$uptime = snmpwalk($this->ip, $this->communityR, $this->OIDs['uptime'], $this->timeout);
		$info['uptime'] = $uptime[0];
		$info['cpu'] = snmpget($this->ip, $this->communityR, $this->OIDs['cpu'], $this->timeout);
		$info['memAll'] = snmpget($this->ip, $this->communityR, $this->OIDs['memAll'], $this->timeout);
		$info['memFree'] = snmpget($this->ip, $this->communityR, $this->OIDs['memFree'], $this->timeout);
		$info['serialNo'] = trim(snmpget($this->ip, $this->communityR, $this->OIDs['serialNo'], $this->timeout));
		$info['vlans'] = snmprealwalk($this->ip, $this->communityR, $this->OIDs['vlans'], $this->timeout);
		return $info;
	}

	public function getStdInfo() {
		$info = array();
		$ios = snmpwalk($this->ip , $this->communityR , $this->OIDs['ios'], $this->timeout);
		if ($ios == false) {
			return null;
		}
		$info['ios'] = $ios[0];
		$info['serialNo'] = trim(snmpget($this->ip, $this->communityR, $this->OIDs['serialNo'], $this->timeout));
		return $info;
	}

	public function getQuickInfo() {
		$info = array();
		$model = snmpget($this->ip , $this->communityR , $this->OIDs['model'], $this->timeout);
		if ($model == false) {
			return null;
		}
		$info['model'] = $model;
		$info['serialNo'] = trim(snmpget($this->ip, $this->communityR, $this->OIDs['serialNo'], $this->timeout));
		return $info;
	}

	public function getPortAliases() {
		$aliases = snmpwalk($this->ip , $this->communityR, $this->OIDs['portAliases'], $this->timeout);
		if ($aliases == false) {
			return null;
		}
		return $aliases;
	}

	public function getPortAlias($port) {
		$alias = snmpget($this->ip , $this->communityR, $this->OIDs['portAliases'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($alias == false) {
			return null;
		}
		return $alias;
	}
	
	public function getPortSpeed($port) {
		if ($this->getPortStatus($port) != self::UP) {
			return null;
		}
		$speed = snmpget($this->ip , $this->communityR, $this->OIDs['portSpeeds'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($speed == false) {
			return null;
		}
		return $speed;
	}
	
	public function getUntaggedVlan($port) {
		$vlan = snmpget($this->ip , $this->communityR, $this->OIDs['untaggedVlanOnPorts'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($vlan == false || $vlan == 1) {
			return null;
		}
		$vlanName = snmpget($this->ip, $this->communityR, $this->OIDs['vlans'].'.'.$vlan, $this->timeout);
		return $vlanName.' ('.$vlan.')';
	}
	
	public function getIntrusionFlag($port) {
		$flag = snmpget($this->ip , $this->communityR, $this->OIDs['intrusionFlag'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($flag == false) {
			return null;
		}
		if ($flag == 1) {
			$result = self::UP;
		} else {
			$result = self::DOWN;
		}
		return $result;
	}
	
	public function setIntrusionFlag($port, $flag) {
		if ($flag == self::UP) {
			$flagInt = 1;
		} else {
			$flagInt = 2;
		}
		return snmpset($this->ip, $this->communityW, $this->OIDs['intrusionFlag'].'.'.$this->translateSwitchPort($port), 'i', $flagInt, $this->timeout);
	}
	
	public function getIntrusionFlags() {
		$flags = snmpwalk($this->ip, $this->communityR, $this->OIDs['intrusionFlag'], $this->timeout);
		if ($flags == false) {
			return null;
		}

		$result = array();
		for ($i = 0; $i < count($flags); $i++) {
			if ($flags[$i] == 1) {
				$result[$i] = self::UP;
			} else {
				$result[$i] = self::DOWN;
			}
		}

		return $result;
	}
	
	public function getLearnMode($port) {
		$learnMode = snmpget($this->ip , $this->communityR, $this->OIDs['learnMode'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($learnMode == false) {
			return null;
		}
		return $learnMode;
	}
	
	public function getAddrLimit($port) {
		$addrLimit = snmpget($this->ip , $this->communityR, $this->OIDs['addrLimit'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($addrLimit == false) {
			return null;
		}
		return $addrLimit;
	}
	
	public function getAlarmState($port) {
		$alarmState = snmpget($this->ip , $this->communityR, $this->OIDs['alarmState'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($alarmState == false) {
			return null;
		}
		return $alarmState;
	}
	
	public function getLoopProtect($port) {
		$loopProtect = snmpget($this->ip , $this->communityR, $this->OIDs['loopProtect'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($loopProtect == false) {
			return null;
		}
		if ($loopProtect == 1) {
			$result = self::ENABLED;
		} else {
			$result = self::DISABLED;
		}
		return $result;
	}

	public function getPortStatuses() {
		$activities = snmpwalk($this->ip, $this->communityR, $this->OIDs['portActivities'], $this->timeout);
		if ($activities == false) {
			return null;
		}
		$statuses = snmpwalk($this->ip, $this->communityR, $this->OIDs['portStatuses'], $this->timeout);

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
		$status = snmpget($this->ip, $this->communityR, $this->OIDs['portStatuses'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($status == false) {
			return null;
		}
		$activity = snmpget($this->ip, $this->communityR, $this->OIDs['portActivities'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($status == 2) {
			$result = self::DISABLED;
		} else if ($activity == 2) {
			$result = self::DOWN;
		} else {
			$result = self::UP;
		}
		return $result;
	}

	public function setPortStatus($port, $status) {
		if ($status == self::DISABLED) {
			$statusInt = 2;
		} else {
			$statusInt = 1;
		}
		return snmpset($this->ip, $this->communityW, $this->OIDs['portStatuses'].'.'.$this->translateSwitchPort($port), 'i', $statusInt, $this->timeout);
	}

	public function getLockouts() {
		$lockouts = snmprealwalk($this->ip , $this->communityR, $this->OIDs['lockouts'], $this->timeout);
		if ($lockouts == false) {
			return null;
		}
		$lockouts = array_keys($lockouts);
		for ($i = 0; $i < count($lockouts); $i++) {
			$lockouts[$i] = $this->int2mac(str_replace('SNMPv2-SMI::mib-2.17.7.1.3.1.1.4.4095.', '', $lockouts[$i]));
		}
		return $lockouts;
	}

	public function getTrunks() {
		$trunks = snmpwalk($this->ip , $this->communityR, $this->OIDs['trunk'], $this->timeout);
		if ($trunks == false) {
			return null;
		}
		return $trunks;
	}
	
	public function isTrunk($port) {
		$trunk = snmpget($this->ip , $this->communityR, $this->OIDs['trunk'].'.'.$this->translateSwitchPort($port), $this->timeout);
		if ($trunk == false) {
			return self::DISABLED;
		}
		if ($trunk > 0) {
			$result = self::ENABLED;
		} else {
			$result = self::DISABLED;
		}
		return $result;
	}

	public function getGbics($sfpPorts) {
		$gbics = array();
		$serials = snmprealwalk($this->ip , $this->communityR, $this->OIDs['gbicSerial'], $this->timeout);
		$models = snmpwalk($this->ip , $this->communityR, $this->OIDs['gbicModel'], $this->timeout);
		foreach ($serials as $portOid => $serial) {
			$oid = explode(".", $portOid);
			$port = array_pop($oid);
			$gbics[$port][0] = $serial;
			$gbics[$port][1] = $models[$port + 10];
		}
		return $gbics;
	}

	public function setPortAlias($port, $name) {
		return snmpset($this->ip, $this->communityW, $this->OIDs['portAliases'].'.'.$this->translateSwitchPort($port), 's', $name, $this->timeout);
	}

	public function getMacsFromPort($port) {
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$macs = snmpwalk($this->ip , $this->communityR, $this->OIDs['macs'].'.'.$this->translateSwitchPort($port), $this->timeout);
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
		return snmpset($this->ip, $this->communityW, $this->OIDs['lockouts'].'.'.$this->mac2int($mac).'.0', 'i', $op); // brak timeoutu w zwiazku z #429
	}

	public function findMac($searchMac) {
		$conf = UFra::shared('UFconf_Sru');
		// pobranie pierwszego mac-a z tablicy
		$switchIp = $conf->masterSwitch;
		$needle = $this->mac2int($searchMac);

		$watchdog = 20;
		while ($watchdog > 0) {
			--$watchdog;
			$portUser = snmpget($switchIp, $this->communityR, $this->OIDs['port'].'.'.$needle, $this->timeout);
			if ($portUser) {
				$switch = UFra::factory('UFbean_SruAdmin_Switch');
				$switch->getByIp($switchIp);
				$this->switch = $switch;
				$portUser = $this->recoverSwitchPort((int)$portUser);
				// sprawdzamy, czy na znalezionym porcie jest jakis switch
				try {
					if ($portUser > $switch->modelPorts) {
						if ($portUser <= 50 && $switch->modelPorts < 50) {
							$name = $portUser - 48;
						} else if ($portUser < 200) {
							$name = $portUser - 50;
						} else if ($portUser < 280) {
							$name = $portUser - 218;
						} else {
							$name = $portUser - 288;
						}
						if ($name < 0) $name = $name + 2;
						$trunks = snmpwalk($switchIp, $this->communityR, $this->OIDs['trunk'], $this->timeout);
						for ($i = 0; $i < count($trunks); $i++) {
							if ($trunks[$i] == $name) {
								$portUser = $i + 1;
								break;
							}
						}
					}
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
		}
		return null;
	}

	private function translateSwitchPort($port) {
		if (is_null($this->switch)) {
			return $port;
		}
		if (in_array($this->switch->modelNo, $this->movedPortsNumbers)) {
			if ($port <= 48) {
				return $port+24;
			} else if ($port > 48 && $port <= 50) {
				return $port+48;
			} else {
				return $port+70;
			}
		}
		return $port;
	}

	private function recoverSwitchPort($port) {
		if (is_null($this->switch)) {
			return $port;
		}
		if (in_array($this->switch->modelNo, $this->movedPortsNumbers)) {
			if ($port <= 48) {
				return $port-24;
			} else if ($port > 48 && $port <= 50) {
				return $port-48;
			} else {
				return $port-70;
			}
		}
		return $port;
	}
}
