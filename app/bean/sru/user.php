<?
/**
 * uzytkownik
 */
class UFbean_Sru_User
extends UFbean_Common {

	const TYPE_TOURIST_STUDENT = 21;
	const TYPE_TOURIST_DIDACTICS = 22;
	const TYPE_TOURIST_INDIVIDUAL = 23;
	const TYPE_SKOS = 51;
	const TYPE_ADMINISTRATION = 52;
	const TYPE_ORGANIZATION = 53;
	const TYPE_EXADMIN = 54;

	const DB_STUDENT_MIN = 1;
	const DB_STUDENT_MAX = 10;
	const DB_TOURIST_MIN = 21;
	const DB_TOURIST_MAX = 30;

	protected $_locationId = null;
	protected $_password = null;

	protected $notifyAbout = array(
		'login',
		'name',
		'surname',
		'email',
		'gg',
		'facultyId',
		'studyYearId',
		'locationId',
		'active',
	);

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

	protected function normalizeName($val, $change) {
		return trim($val);
	}

	protected function normalizeSurname($val, $change) {
		return trim($val);
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
			if (!$change || (isset($this->data['locationId']) && $this->data['locationId']!=$loc->id)) {
				$this->data['locationAlias'] = $val;
				$this->dataChanged['locationAlias'] = $val;
				$this->data['locationId'] = $loc->id;
				$this->dataChanged['locationId'] = $loc->id;
			}
		} catch (UFex $e) {
			return 'noRoom';
		}
	}

	protected function validateRegistryNo($val, $change) {
		if (is_null($val) || $val == '') {
			return;
		}
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByRegistryNo($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	public function notifyByEmail() {
		// nie mozna tego zrobic w jednej linii, bo php rzuca bledem "Can't use
		// function return value in write context"
		$ans = array_intersect(array_keys($this->dataChanged), $this->notifyAbout);
		return !empty($ans);
	}
}
