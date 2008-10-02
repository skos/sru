<?
/**
 * uzytkownik
 */
class UFbean_Sru_User
extends UFbeanSingle {

	protected $_locationId = null;
	protected $_password = null;

	/**
	 * zaszyfrowane haslo
	 * 
	 * @param string $login - login
	 * @param string $password - haslo
	 * @return string
	 */
	static function generatePassword($login, $password) {
		return md5($login.$password);
	}

	protected function validateLogin($val, $change) {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByLogin($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validateStudyYearId($val, $change) {	
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

	protected function validateFacultyId($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
		if ('-' === $val) {
			if ('-' !== $post['facultyId']) {
				return 'facultyMismatch';
			}
		} else {
			$this->validate('facultyId', $val, $change);
		}
	}

	protected function normalizeStudyYearId($val, $change) {
		if ('-' === $val) {
			return null;
		} else {
			return (int)$val;
		}
	}

	protected function validateLocationAlias($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
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
}
