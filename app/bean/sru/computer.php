<?
/**
 * komputer
 */
class UFbean_Sru_Computer
extends UFbeanSingle {

	protected $_locationId = null;

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
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function normalizeMac($val, $change) {
		return strtolower($val);
	}

	protected function validateLocationId($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'computerEdit':'computerAdd'};
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			if (array_key_exists('dormitory', $post)) {
				$id = $post['dormitory'];
			} else {
				$id = $this->data['dormitory'];
			}
			$dorm->getByPK($id);
		} catch (UFex $e) {
			return;
		}
		try {
			$loc = UFra::factory('UFbean_Sru_Location');
			$loc->getByAliasDormitory((string)$val, $dorm->id);
			$this->_locationId = $loc->id;
		} catch (UFex $e) {
			return 'noRoom';
		}
	}

	protected function normalizeLocationId($val, $change) {
		return (int)$this->_locationId;
	}

	protected function validateIp($val, $change) {
		$ips = explode('.', $val);
		foreach ($ips as $ip) {
			if ($ip < 0 || $ip >255) {
				return 'regexp';
			}
		}
	}
}
