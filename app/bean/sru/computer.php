<?
/**
 * komputer
 */
class UFbean_Sru_Computer
extends UFbean_Common {

	const TYPE_STUDENT = 1;
	const TYPE_STUDENT_AP = 2;
	const TYPE_STUDENT_OTHER = 3;
	const LIMIT_STUDENT = 10; //do zapytań bazodanowych
	const TYPE_TOURIST = 11;
	const LIMIT_STUDENT_AND_TOURIST = 20; //do zapytań bazodanowych
	const TYPE_ORGANIZATION = 21;
	const TYPE_ADMINISTRATION = 31;
	const LIMIT_SERVER = 41;
	const TYPE_SERVER = 41;
	const TYPE_SERVER_VIRT = 42;
	const TYPE_MACHINE = 43;
	const TYPE_INTERFACE = 44;
	const TYPE_NOT_SKOS_DEVICE = 45;

	const EDIT_PREFIX = 'computerEdit';
	const ADD_PREFIX = 'computerAdd';

	protected $notifyAbout = array(
		'host',
		'mac',
		'availableTo',
		'carerId',
		'deviceModelId',
		'typeId',
	);
	
	static protected $typeToUser = array(
		self::TYPE_TOURIST => array(UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL),
		self::TYPE_SERVER => array(UFbean_Sru_User::TYPE_SKOS),
		self::TYPE_MACHINE => array(UFbean_Sru_User::TYPE_SKOS),
		self::TYPE_INTERFACE => array(UFbean_Sru_User::TYPE_SKOS, UFbean_Sru_User::TYPE_ORGANIZATION),
		self::TYPE_NOT_SKOS_DEVICE => array(UFbean_Sru_User::TYPE_SKOS),
		self::TYPE_ADMINISTRATION => array(UFbean_Sru_User::TYPE_SKOS, UFbean_Sru_User::TYPE_ADMINISTRATION),
		self::TYPE_ORGANIZATION => array(UFbean_Sru_User::TYPE_SKOS, UFbean_Sru_User::TYPE_ORGANIZATION),
	);
	
	static protected $typeToUserExclusions = array(
		self::TYPE_STUDENT => array(UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL, UFbean_Sru_User::TYPE_ORGANIZATION, UFbean_Sru_User::TYPE_ADMINISTRATION, UFbean_Sru_User::TYPE_SKOS),
		self::TYPE_STUDENT_AP => array(UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL, UFbean_Sru_User::TYPE_ORGANIZATION, UFbean_Sru_User::TYPE_ADMINISTRATION, UFbean_Sru_User::TYPE_SKOS),
		self::TYPE_STUDENT_OTHER => array(UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL, UFbean_Sru_User::TYPE_ORGANIZATION, UFbean_Sru_User::TYPE_ADMINISTRATION, UFbean_Sru_User::TYPE_SKOS),
		self::TYPE_SERVER_VIRT => array(UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL),
	);
	
	static public $defaultUserToComputerType = array(
		UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL => UFbean_Sru_Computer::TYPE_TOURIST,
		UFbean_Sru_User::TYPE_SKOS => UFbean_Sru_Computer::TYPE_SERVER,
		UFbean_Sru_User::TYPE_ADMINISTRATION => UFbean_Sru_Computer::TYPE_ADMINISTRATION,
		UFbean_Sru_User::TYPE_ORGANIZATION => UFbean_Sru_Computer::TYPE_ORGANIZATION,
	);
	
	public static function getHostType($user, $computer) {
		if (array_key_exists($computer->typeId, self::$typeToUser) && in_array($user->typeId, self::$typeToUser[$computer->typeId])
			&& (!array_key_exists($computer->typeId, self::$typeToUserExclusions) || !in_array($user->typeId, self::$typeToUserExclusions[$computer->typeId]))) {
			$typeId = $computer->typeId;
		} else if (array_key_exists($user->typeId, self::$defaultUserToComputerType)) {
			$typeId = self::$defaultUserToComputerType[$user->typeId];
		} else {
			$typeId = self::TYPE_STUDENT;
		}

		return $typeId;
	}

	protected function validateHost($val, $change) {
		if ($change && $this->_srv->get('req')->post->is('computerEdit')) {
			$post = $this->_srv->get('req')->post->computerEdit;
		} else if ($this->_srv->get('req')->post->is('computerAdd')) {
			$post = $this->_srv->get('req')->post->computerAdd;
		} else {
			$post = null;
		}
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$vlanId = $this->getVlanIdFromPost($post);
			$vlan = UFra::factory('UFbean_SruAdmin_Vlan');
			$vlan->getByPK($vlanId);
			$bean->getByDomainName($val.'.'.$vlan->domainSuffix);
			
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			if (array_key_exists('active', $this->data) && !$this->data['active'] && !is_null($post) && 
				array_key_exists('activateHost', $post) && !$post['activateHost'] &&
				array_key_exists('host', $this->data) && array_key_exists('host', $post) &&
				$this->data['host'] == $post['host']) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
			try {
				$vlanId = $this->getVlanIdFromPost($post);
				$vlan = UFra::factory('UFbean_SruAdmin_Vlan');
				$vlan->getByPK($vlanId);
				
				$alias = UFra::factory('UFbean_SruAdmin_ComputerAlias');
				$alias->getByDomainName($val.'.'.$vlan->domainSuffix);
				return 'duplicated';
			} catch (UFex_Dao_NotFound $e) {
			}
		}
		if (in_array($val, UFra::shared('UFconf_Sru')->invalidHostNames)) {
			return 'duplicated';
		}
	}
	
	private function getVlanIdFromPost($post) {
		if (!is_null($post) && array_key_exists('ip', $post) && $post['ip'] != '') {
			$ipv4 = UFra::factory('UFbean_Sru_Ipv4');
			$ipv4->getByIp($post['ip']);
			$vlanId = $ipv4->vlan;
		} else if (!is_null($post) && isset($post['typeId'])) {
			$vlanId = $this->getVlanByComputerType($post['typeId']);
		} else {
			$vlanId =  UFbean_SruAdmin_Vlan::getDefaultVlan();
		}
		
		return $vlanId;
	}

	protected function normalizeHost($val, $change) {
		return strtolower($val);
	}

	protected function validateMac($val, $change) {
		$invalidMac = array(
			'/^ffffffffffff/',
			'/^01000ccccccc/',
			'/^01000ccccccd/',
			'/^0180c2000000/',
			'/^0180c2000001/',
			'/^0180c2000002/',
			'/^0180c2000003/',
		    	'/^0180c2000008/',
			'/^0180c200000e/',
			'/^01005e/',
			'/^3333/'
		);
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');
			$val2 = $this->normalizeMac($val, null);
			$val2 = str_replace(':', '', $val2);
			$val2 = str_replace('-', '', $val2);
			$val2 = str_replace(' ', '', $val2);
			foreach($invalidMac as $invalid){
				$ok = preg_match($invalid, $val2);
				if($ok){
					return 'regexp';
				}
			}
			try {
				// sprawdzamy, czy mamy do czynienia z serwerem lub organizacją (urządzenia się nie liczą)
				$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
				if (isset($post['typeId']) && ($post['typeId'] == self::TYPE_SERVER ||
					$post['typeId'] == self::TYPE_SERVER_VIRT || $post['typeId'] == self::TYPE_INTERFACE ||
					$post['typeId'] == self::TYPE_ORGANIZATION)) {
					return;
				} else if (!isset($post['typeId'])) {
					$computer = UFra::factory('UFbean_Sru_Computer');
					$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
					if ($computer->typeId == self::TYPE_SERVER ||
						$computer->typeId == self::TYPE_SERVER_VIRT || $computer->typeId == self::TYPE_INTERFACE ||
						$computer->typeId == self::TYPE_ORGANIZATION) {
						return;
					}
				}
			} catch (UFex $e) {
			}
			try {
				// sprawdzamy, czy mamy do czynienia z serwerem  lub organizacją (urządzenia się nie liczą)
				$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
				if (isset($post['typeId']) && ($post['typeId'] == self::TYPE_SERVER ||
					$post['typeId'] == self::TYPE_SERVER_VIRT || $post['typeId'] == self::TYPE_INTERFACE ||
					$post['typeId'] == self::TYPE_ORGANIZATION)) {
					return;
				}
			} catch (UFex $e) {
			}
			// skoro nie serwer, to sprawdzamy, czy MAC sie powtarza
			$bean->listByMac($val);
			if (($change && count($bean) == 1 && $bean[0]['id'] == $this->data['id']) || count($bean) == 0) {
				return;
			} 			

			return 'duplicated';
		} catch (UFex_Db_QueryFailed $e) {
			return 'regexp';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function normalizeMac($val, $change) {
		return strtolower($val);
	}

	protected function validateLocationAlias($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'computerEdit':'computerAdd'};
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByPK((int)$post['dormitory']);
		} catch (UFex $e) {
			return 'noDormitory';
		}
		try {
			$loc = UFra::factory('UFbean_Sru_Location');
			$loc->getByAliasDormitory((string)$val, $dorm->id);
			if (!$change || (isset($this->data['locationId']) && $this->data['locationId']!=$loc->id)) {
				$this->data['locationAlias'] = $val;
				$this->dataChanged['locationAlias'] = $val;
				$this->data['locationId'] = $loc->id;
				$this->dataChanged['locationId'] = $loc->id;
			}
		} catch (UFex $e) {
			return 'noRoom';
		}
	}

	protected function validateIp($val, $change) {
		if ($change && $this->_srv->get('req')->post->is('computerEdit')) {
			$post = $this->_srv->get('req')->post->computerEdit;
		} else if ($this->_srv->get('req')->post->is('computerAdd')) {
			$post = $this->_srv->get('req')->post->computerAdd;
		} else {
			$post = null;
		}
		$ips = explode('.', $val);
		foreach ($ips as $ip) {
			if ($ip < 0 || $ip >255) {
				return 'regexp';
			}
		}
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByIp($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			if (array_key_exists('active', $this->data) && !$this->data['active'] && !is_null($post) && 
				array_key_exists('activateHost', $post) && !$post['activateHost'] &&
				array_key_exists('ip', $this->data) && array_key_exists('ip', $post) &&
				$this->data['ip'] == $post['ip']) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	public function notifyByEmail() {
		// nie mozna tego zrobic w jednej linii, bo php rzuca bledem "Can't use
		// function return value in write context"
		$ans = array_intersect(array_keys($this->dataChanged), $this->notifyAbout);
		return !empty($ans);
	}
	
	public function getVlanByComputerType($computerType) {
		if (array_key_exists($computerType, UFra::shared('UFconf_Sru')->computerTypeToVLAN)) {
			return UFra::shared('UFconf_Sru')->computerTypeToVLAN[$computerType];
		}
		return UFbean_SruAdmin_Vlan::getDefaultVlan();
	}
	
	protected function validateSkosCarerId($val, $change) {
		try {
			$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
			if (isset($post['typeId']) && 
				($post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || 
				$post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
				$post['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE ||
				$post['typeId'] == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) && 
				(is_null($val) || (int)$val == 0)) {
				return 'null';
			} else if (!isset($post['typeId'])) {
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
				if (($computer->typeId == UFbean_Sru_Computer::TYPE_SERVER || 
					$computer->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
					$computer->typeId == UFbean_Sru_Computer::TYPE_MACHINE ||
					$computer->typeId == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) && 
					(is_null($val) || (int)$val == 0)) {
					return 'null';
				}
			}
		} catch (UFex $e) {
		}
		try {
			$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
			if (isset($post['typeId']) && 
				($post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || 
				$post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
				$post['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE ||
				$post['typeId'] == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) && 
				(is_null($val) || (int)$val == 0)) {
				return 'null';
			}
		} catch (UFex $e) {
		}
	}
	
	protected function validateWaletCarerId($val, $change) {
		try {
			$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
			if (isset($post['typeId']) && 
				($post['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) && 
				(is_null($val) || (int)$val == 0)) {
				return 'null';
			} else if (!isset($post['typeId'])) {
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
				if (($computer->typeId == UFbean_Sru_Computer::TYPE_ADMINISTRATION)  && 
					(is_null($val) || (int)$val == 0)) {
					return 'null';
				}
			}
		} catch (UFex $e) {
		}
		try {
			$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
			if (isset($post['typeId']) && 
				($post['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) && 
				(is_null($val) || (int)$val == 0)) {
				return 'null';
			}
		} catch (UFex $e) {
		}
	}

	protected function validateMasterHostId($val, $change) {
		try {
			$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
			if (isset($post['typeId']) && ($post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
				$post['typeId'] == UFbean_Sru_Computer::TYPE_INTERFACE) && (is_null($val) || (int)$val == 0)) {
				return 'null';
			}
		} catch (UFex $e) {
		}
		try {
			$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
			if (isset($post['typeId']) && ($post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
				$post['typeId'] == UFbean_Sru_Computer::TYPE_INTERFACE) && (is_null($val) || (int)$val == 0)) {
				return 'null';
			}
		} catch (UFex $e) {
		}
	}
	
	protected function validateTypeId($val, $change) {
		if (array_key_exists($val, self::$typeToUser)) {
			if ($this->_srv->get('req')->get->is('userId')) {
				try {
					$user = UFra::factory('UFbean_Sru_User'); 
					$user->getByPK((int)$this->_srv->get('req')->get->userId);
					if (!in_array($user->typeId, self::$typeToUser[$val])) {
						return 'wrongHostType';
					}
					return;
				} catch (UFex_Core_DataNotFound $e) {
				}
			} else if ($this->_srv->get('req')->get->is('computerId')) {
				try {
					$computer = UFra::factory('UFbean_Sru_Computer'); 
					$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
					$user = UFra::factory('UFbean_Sru_User'); 
					$user->getByPK($computer->userId);
					if (!in_array($user->typeId, self::$typeToUser[$val])) {
						return 'wrongHostType';
					}
					return;
				} catch (UFex_Core_DataNotFound $e) {
				}
			} else if ($this->_srv->get('session')->is('auth')) {
				try {
					$userId = $this->_srv->get('session')->auth;
					$user = UFra::factory('UFbean_Sru_User'); 
					$user->getByPK((int)$userId);
					if (!in_array($user->typeId, self::$typeToUser[$val])) {
						return 'wrongHostType';
					}
					return;
				} catch (UFex_Core_DataNotFound $e) {
				}
			}
			UFra::error('Invalidated computer typeId: '.$val.($this->_srv->get('session')->is('auth') ? ' User: '.$this->_srv->get('session')->auth : '').($this->_srv->get('session')->is('authAdmin') ? ' Admin: '.$this->_srv->get('session')->authAdmin : '').($this->_srv->get('session')->is('authWalet') ? ' Walet: '.$this->_srv->get('session')->authWalet : ''));
		}
	}
}
