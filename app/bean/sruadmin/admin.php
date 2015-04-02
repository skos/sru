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
	static function generateMd5Password($password) {
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
	
	static function validateBlowfishPassword($password, $hash) {
		$hasher = UFra::factory('UFlib_PasswordHash');
		$result = $hasher->CheckPassword($password, $hash);
		unset($hasher);
		
		return $result;
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
	    $post = '';
	    if($change == true){
		if($this->_srv->get('req')->post->is('adminEdit')){
		    $post = $this->_srv->get('req')->post->adminEdit;
		}else{
		    $post = $this->_srv->get('req')->post->adminOwnPswEdit;
		}
	    }else{
		$post = $this->_srv->get('req')->post->adminAdd;
	    }
	    
	    $admin = UFra::factory('UFbean_SruAdmin_Admin');
	    if ($change) {
		    try {
			    $admin->getByPK($this->data['id']);
			    if (self::validateBlowfishPassword($val, $admin->password)) {
				    return 'same';
			    }
			    if ($admin->passwordInner == self::generateMd5Password($val)) {
				    return 'sameAsInner';
			    }
		    } catch (UFex $e) {
			    return 'unknown';
		    }
	    }

	    try {
		    if ($post['password2'] !== $val) {
			    return 'mismatch';
		    }
	    } catch (UFex $e) {
		    return 'unknown';
	    }
	}
	
	protected function validatePasswordInner($val, $change) {
		if ($change) {
			$post = $this->_srv->get('req')->post->adminEdit;
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
		
			try {
				$admin->getByPK($this->data['id']);
				if ($admin->passwordInner == self::generateMd5Password($val)) {
					return 'same';
				}
				if (self::validateBlowfishPassword($val, $admin->password)) {
					return 'sameAsMain';
				}
			} catch (UFex $e) {
				return 'unknown';
			}
		
			try {
				if ($post['passwordInner2'] !== $val) {
					return 'mismatch';
				}
			} catch (UFex $e) {
				return 'unknown';
			}

			// gdy edytujemy bota, to nie ustawiamy hasła
			if (array_key_exists('password', $post)) {
				try {
					if ($post['password'] === $post['passwordInner']) {
						return 'sameAsMain';
					}
				} catch (UFex $e) {
					return 'unknown';
				}
			}
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
		return self::generateBlowfishPassword($val);
	}
	
	protected function normalizePasswordInner($val, $change) {
		$this->passwordInner = $val;
		return self::generateMd5Password($val);
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
