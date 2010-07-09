<?
/**
 * komputer
 */
class UFbean_Sru_Computer
extends UFbeanSingle {

	const TYPE_STUDENT = 1;
	const TYPE_ORGANIZATION = 2;
	const TYPE_ADMINISTRATION = 3;
	const TYPE_SERVER = 4;

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
			// sprawdzamy, czy mamy do czynienia z serwerem
			if ($this->data['typeId'] == self::TYPE_SERVER && $bean->typeId == self::TYPE_SERVER) {
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
			$this->data['locationAlias'] = $val;
			$this->dataChanged['locationAlias'] = $val;
			$this->data['locationId'] = $loc->id;
			$this->dataChanged['locationId'] = $loc->id;
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
			// sprawdzamy, czy mamy do czynienia z serwerem
			if ($this->data['typeId'] == self::TYPE_SERVER && $bean->typeId == self::TYPE_SERVER) {
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
