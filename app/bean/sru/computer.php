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

	const EDIT_PREFIX = 'computerEdit';
	const ADD_PREFIX = 'computerAdd';

	protected $notifyAbout = array(
		'host',
		'mac',
		'availableTo',
		'carerId',
	);
	
	protected $typeToVLAN = array(
		self::TYPE_ADMINISTRATION => UFbean_SruAdmin_Vlan::DS_ADM,
		self::TYPE_ORGANIZATION => UFbean_SruAdmin_Vlan::DS_ORGAN,
	);

	protected function validateHost($val, $change) {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByHost($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
			try {
				$alias = UFra::factory('UFbean_SruAdmin_ComputerAlias');
				$alias->getByHost($val);
				return 'duplicated';
			} catch (UFex_Dao_NotFound $e) {
			}
		}
	}

	protected function normalizeHost($val, $change) {
		return strtolower($val);
	}

	protected function validateMac($val, $change) {
		$invalidMac = array(
			'FFFFFFFFFFFF',
			'01000CCCCCCC',
			'01000CCCCCCD',
			'0180C2000000',
			'0180C2000008',
			'0180C2000002',
			'01005Exxxxxx',
			'3333xxxxxxxx'
		);
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$val2 = strtoupper($val);
			$val2 = str_replace(':', '', $val2);
			$val2 = str_replace('-', '', $val2);
			$val2 = str_replace(' ', '', $val2);
			foreach($invalidMac as $invalid){
				$ok = false;
				for($i=0; $i<12; $i++){
					if($invalid{$i} !== $val2{$i} && $invalid{$i} !== 'x'){
						$ok = true;
						break;
					}
				}
				if(!$ok){
					return 'regexp';
				}
			}
			$bean->getByMac($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			try {
				// sprawdzamy, czy mamy do czynienia z serwerem
				$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
				if (isset($post['typeId']) && ($post['typeId'] == self::TYPE_SERVER || $post['typeId'] == self::TYPE_SERVER_VIRT)) {
					return;
				}
			} catch (UFex $e) {
			}
			try {
				// sprawdzamy, czy mamy do czynienia z serwerem
				$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
				if (isset($post['typeId']) && ($post['typeId'] == self::TYPE_SERVER || $post['typeId'] == self::TYPE_SERVER_VIRT)) {
					return;
				}
			} catch (UFex $e) {
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
		if (array_key_exists($computerType, $this->typeToVLAN)) {
			return $this->typeToVLAN[$computerType];
		}
		return UFbean_SruAdmin_Vlan::DEFAULT_VLAN;
	}
	
	protected function validateSkosCarerId($val, $change) {
		try {
			$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
			if (isset($post['typeId']) && 
				($post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || 
				$post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT) && 
				(is_null($val) || (int)$val == 0)) {
				return 'null';
			} else if (!isset($post['typeId'])) {
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
				if (($computer->typeId == UFbean_Sru_Computer::TYPE_SERVER || 
					$computer->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT) && 
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
				$post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT) && 
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
			if (isset($post['typeId']) && $post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT && (is_null($val) || (int)$val == 0)) {
				return 'null';
			}
		} catch (UFex $e) {
		}
		try {
			$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
			if (isset($post['typeId']) && $post['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT && (is_null($val) || (int)$val == 0)) {
				return 'null';
			}
		} catch (UFex $e) {
		}
	}
	
	protected function validateTypeId($val, $change) {
		if ($val == UFbean_Sru_Computer::TYPE_SERVER) {
			try {
				$user = UFra::factory('UFbean_Sru_User'); 
				$user->getByPK((int)$this->_srv->get('req')->get->userId);
				if ($user->typeId != UFbean_Sru_User::TYPE_SKOS) {
					return 'notSkos';
				}
			} catch (UFex_Core_DataNotFound $e) {
				$computer = UFra::factory('UFbean_Sru_Computer'); 
				$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
				$user = UFra::factory('UFbean_Sru_User'); 
				$user->getByPK($computer->userId);
				if ($user->typeId != UFbean_Sru_User::TYPE_SKOS) {
					return 'notSkos';
				}
			} catch (UFex_Dao_NotFound $e) {
			}
		}
	}
}
