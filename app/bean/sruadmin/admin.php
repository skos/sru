<?
/**
 * admin
 */
class UFbean_SruAdmin_Admin
extends UFbeanSingle {

	protected $_password = null;

	/**
	 * zaszyfrowane haslo
	 * 
	 * @param string $password - haslo
	 * @return string
	 */
	static function generatePassword($password) {
		return md5($password);
	}

	protected function validateLogin($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getByLogin($val);
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validatePassword($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'adminEdit':'adminAdd'};
		try {
			if ($post['password2'] !== $val) {
				return 'mismatch';
			}
		} catch (UFex $e) {
			return 'unknown';
		}
	}
	
	protected function validateActiveTo($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'adminEdit':'adminAdd'};
		if ($val != "" && strtotime($val) <= time()){
			return 'tooOld';
		}
	}

	protected function normalizePassword($val, $change) {
		$this->_password = $val;
		return self::generatePassword($val);
	}

	protected function normalizeLogin($val, $change) {
		if (is_string($this->_password)) {
			$pass = $this->_password;
		} else {
			$pass = microtime();
		}
		if (isset($this->_password)) {
			$this->data['password'] = self::generatePassword($pass);
			$this->dataChanged['password'] = $this->data['password'];
		}
		return $val;
	}

	protected function normalizeDormitoryId($val, $change) {
		if ('0' === $val) {
			return null;
		} elseif ($change && '' === $val) {
			return null;
		} else {
			return (int)$val;
		}
	}
}
