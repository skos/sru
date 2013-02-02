<?
/**
 * admin Waleta
 */
class UFbean_SruWalet_Admin
extends UFbeanSingle {

	protected $_password = null;
	
	static function generateBlowfishPassword($password) {
		return UFbean_SruAdmin_Admin::generateBlowfishPassword($password);
	}
	
	static function validateBlowfishPassword($password, $hash) {
		return UFbean_SruAdmin_Admin::validateBlowfishPassword($password, $hash);
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
		if ($change) {
			try {
				$admin->getByPK($this->data['id']);
				if (self::validateBlowfishPassword($val, $admin->password)) {
					return 'same';
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

	protected function normalizePassword($val, $change) {
		$this->_password = $val;
		return self::generateBlowfishPassword($val);
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
