<?
/**
 * uzytkownik
 */
class UFbean_Sru_User
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
				if ($val !== $this->_srv->get('req')->post->userAdd['password2']) {
					return 'mismatch';
				}
			} catch (UFex $e) {
				return 'unknown';
			}
		}
	}

	protected function normalizePassword($val, $change) {
		$this->password = $val;
		return md5($this->data['login'].$val);
	}

	protected function normalizeLogin($val, $change) {
		$this->data['login'] = md5($val.$this->password);
		$this->dataChanged['login'] = $this->data['login'];
		return $val;
	}

	protected function validateFacultyId($val, $change) {
		if ('-' === $val) {
			if ('-' !== $this->_srv->get('req')->post->userAdd['studyYearId']) {
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
		if ('-' === $val) {
			if ('-' !== $this->_srv->get('req')->post->userAdd['facultyId']) {
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
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByPK((int)$this->_srv->get('req')->post->userAdd['dormitory']);
		} catch (UFex $e) {
			return;
		}
		try {
			$loc = UFra::factory('UFbean_Sru_Location');
			$loc->getByAliasDormitory((string)$val, $dorm->id);
			$this->locationId = $loc->id;
		} catch (UFex $e) {
			return 'noRoom';
		}
	}

	protected function normalizeLocationId($val, $change) {
		return (int)$this->locationId;
	}
}
