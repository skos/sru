<?
/**
 * admin Waleta
 */
class UFbean_SruWalet_Admin
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
	
	static function generateBlowfishPassword($password) {
		return UFbean_SruAdmin_Admin::generateBlowfishPassword($password);
	}

	protected function validateLogin($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruWalet_Admin');
			$bean->getByLogin($val);
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validatePassword($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'adminEdit':'adminAdd'};
		$admin = UFra::factory('UFbean_SruWalet_Admin');
		try {
			$admin->getByPK($this->data['id']);
			if ($admin->password == self::generatePassword($val)) {
				return 'same';
			}
		} catch (UFex $e) {
			return 'unknown';
		}
		
		try {
			if ($post['password2'] !== $val) {
				return 'mismatch';
			}
		} catch (UFex $e) {
			return 'unknown';
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
