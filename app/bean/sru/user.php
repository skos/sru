<?
/**
 * uzytkownik
 */
class UFbean_Sru_User
extends UFbeanSingle {

	protected $_locationId = null;
	protected $_password = null;

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
			$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
			try {
				if ($val !== $post['password2']) {
					return 'mismatch';
				}
			} catch (UFex $e) {
				return 'unknown';
			}
	}

	protected function normalizePassword($val, $change) {
		$this->_password = $val;
		if (array_key_exists('login', $this->data)) {
			$login = $this->data['login'];
		} else {
			$login = md5(microtime());
		}
		return md5($login.$val);
	}

	protected function normalizeLogin($val, $change) {
		if (is_string($this->_password)) {
			$pass = $this->_password;
		} else {
			$pass = microtime();
		}
		if (array_key_exists('password', $this->data)) {
			$this->data['password'] = md5($val.$pass);
			$this->dataChanged['password'] = $this->data['password'];
		}
		return $val;
	}

	protected function validateFacultyId($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
		if ('-' === $val) {
			if ('-' !== $post['studyYearId']) {
				return 'studyYearMismatch';
			}
		}
	}

	protected function normalizeFacultyId($val, $change) {
		if ('-' === $val) {
			return null;
		} else {
			return (int)$val;
		}
	}

	protected function validateStudyYearId($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
		if ('-' === $val) {
			if ('-' !== $post['facultyId']) {
				return 'facultyMismatch';
			}
		} else {
			$this->validate('studyYearId', $val, $change);
		}
	}

	protected function normalizeStudyYearId($val, $change) {
		if ('-' === $val) {
			return null;
		} else {
			return (int)$val;
		}
	}

	protected function validateLocationId($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByPK((int)$post['dormitory']);
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
}
