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
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByMac($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			
			try {
				// sprawdzamy, czy mamy do czynienia z serwerem wirtualnym
				$post = $this->_srv->get('req')->post->{self::EDIT_PREFIX};
				if (isset($post['typeId']) && $post['typeId'] == self::TYPE_SERVER_VIRT) {
					return;
				}
			} catch (UFex $e) {
			}
			try {
				// sprawdzamy, czy mamy do czynienia z serwerem wirtualnym
				$post = $this->_srv->get('req')->post->{self::ADD_PREFIX};
				if (isset($post['typeId']) && $post['typeId'] == self::TYPE_SERVER_VIRT) {
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

	public function updateLocationByHost($host, $location, $ip, $modifiedBy=null) {
		return $this->dao->updateLocationByHost($host, $location, $ip, $modifiedBy);
	}
}
