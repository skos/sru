<?
/**
 * admin
 */
class UFbean_SruAdmin_Admin
extends UFbeanSingle {

	protected $locationId = null;
	protected $password = null;

	/*
	public function validate($var, $val, $change) {
		parent::validate($var, $val, $change);
	}
	*/

	protected function validateLogin($val, $change) {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByLogin($val);
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validatePassword($val, $change) {
		if (!$change) {
			try {
				if ($val !== $this->_srv->get('req')->post->adminAdd['password2']) {
					return 'mismatch';
				}
			} catch (UFex $e) {
				return 'unknown';
			}
		}
	}

	protected function normalizePassword($val, $change) {
		$this->password = $val;
		if (array_key_exists('login', $this->data)) {
			$login = $this->data['login'];
		} else {
			$login = md5(microtime());
		}
		return md5($login.$val);
	}

	protected function normalizeLogin($val, $change) {
		if (is_string($this->password)) {
			$pass = $this->password;
		} else {
			$pass = microtime();
		}
		$this->data['password'] = md5($val.$pass);
		$this->dataChanged['password'] = $this->data['password'];
		return $val;
	}

	protected function validateDormitoryId($val, $change) {
		if ('-' == $val) {
			return;
		}
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByPK((int)$val);
		} catch (UFex $e) {
			return 'notFound';
		}
	}

	protected function normalizeDormitoryId($val, $change) {
		if ('-' === $val) {
			return null;
		} else {
			return (int)$val;
		}
	}
}
