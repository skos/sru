<?
/**
 * admin
 */
class UFbean_SruAdmin_Admin
extends UFbean_Common {

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
	
	/**
	 * Generowanie hasła zaszyfrowanego Blowfishem
	 * @param type $password
	 * @return null 
	 */
	static function generateBlowfishPassword($password) {
		// Base-2 logarithm of the iteration count used for password stretching
		$hash_cost_log2 = 8;
		// Do we require the hashes to be portable to older systems (less secure)?
		$hash_portable = FALSE;
		$hasher = UFra::factory('UFlib_PasswordHash');
		$hasher->PasswordHash($hash_cost_log2, $hash_portable);
		$hash = $hasher->HashPassword($password);
		unset($hasher);
		if (strlen($hash) < 20) return null;
		
		return $hash;
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
		$admin = UFra::factory('UFbean_SruAdmin_Admin');
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
	
	protected function validateActiveTo($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'adminEdit':'adminAdd'};
		if ($val != "" && strtotime($val) <= time() && $post['active']){
			return 'tooOld';
		}
	}

	protected function validateActive($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'adminEdit':'adminAdd'};
		try {
			if ($val && !array_key_exists('activeTo', $post) && !is_null($this->data['activeTo']) && $this->data['activeTo'] <= time()) {
				return 'tooOld';
			}
		} catch (UFex $e) {
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
