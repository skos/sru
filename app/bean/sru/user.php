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

	const NATIONALITY_PL_ID = 1;
	const NATIONALITY_PL = 'polska';
	
	const DOC_TYPE_NONE = 3;

	protected $_locationId = null;
	protected $_password = null;

	protected $notifyAbout = array(
		'login',
		'name',
		'surname',
		'email',
		'facultyId',
		'studyYearId',
		'locationId',
		'active',
		'registryNo',
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
	
	public static function isDate($str) {
		$stamp = strtotime( $str );

		if (!is_numeric($stamp)) {
			return FALSE;
		}
		if($stamp > time()) {
			return FALSE;
		}

		$month = date( 'm', $stamp );
		$day   = date( 'd', $stamp );
		$year  = date( 'Y', $stamp );

		if (checkdate($month, $day, $year))  {
			return TRUE;
		}

		return FALSE;
	}

	public static function validatePeselFormat($pesel) {
		if (!preg_match('/^[0-9]{11}$/', $pesel)) { //sprawdzamy czy ciąg ma 11 cyfr
			return false;
		}

		$arrSteps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3); // tablica z odpowiednimi wagami
		$intSum = 0;
		for ($i = 0; $i < 10; $i++) {
			$intSum += $arrSteps[$i] * $pesel[$i]; //mnożymy każdy ze znaków przez wagę i sumujemy wszystko
		}
		$int = 10 - $intSum % 10; //obliczamy sumę kontrolną
		$intControlNr = ($int == 10) ? 0 : $int;
		if ($intControlNr == $pesel[10]) { //sprawdzamy czy taka sama suma kontrolna jest w ciągu
			return true;
		}

		return false;
	}

	protected function validatePesel($val, $change) {
		$user = UFra::factory('UFbean_Sru_User');
		if (!is_null($val) && $val != '' && !UFbean_Sru_User::validatePeselFormat($val)) {
			return 'invalid';
		}

		// sprawdźmy jeszcze unikalność
		try {
			if(!is_null($val) && $val != '') {
				$user->getByPesel($val);
			} else {
				return;	
			}
			if ($change && $this->data['id'] == $user->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}
	
	protected function validateBirthDate($val, $change) {
		if(!is_null($val) && $val != '' && !UFbean_Sru_User::isDate($val)){
			return '105';
		}else {
			return;
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
			if ($loc->usersMax == 0 && ($loc->typeId == UFbean_SruAdmin_Room::TYPE_STUDENT || $loc->typeId == UFbean_SruAdmin_Room::TYPE_GUEST)) {
				return 'excluded';
			}
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

	protected function validateAddress($val, $change) {
		if(is_null($val) || $val == '')
			return ;//'noAddress';
		else
			return;
	}
	
	protected function validateDocumentType($val, $change) {
		if(is_null($val) || $val == '')
			return ;//'noDocumentType';
		else
			return;
	}
	
	protected function validateDocumentNumber($val, $change) {
		if(is_null($val) || $val == '')
			return ;//'noDocumentNumber';
		else
			return;
	}
	
	protected function validateFacultyId($val, $change) {
		if(is_null($val) || $val == '')
			return 'faculty';
		else
			return;
	}
	
	protected function validateSex($val, $change) {
		if(is_null($val) | $val == '')
			return 'sex';
		else
			return;
	}
	
	protected function validateRegistryNo($val, $change) {
                $post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
		$user = UFra::factory('UFbean_Sru_User');
		try {
			if(isset($this->data['id'])){
				$user->getByPK($this->data['id']);
				if((in_array($user->typeId, UFra::shared('UFconf_Sru')->mustBeRegistryNo) && (is_null($val) || $val == '')
					&& in_array($post['typeId'], UFra::shared('UFconf_Sru')->mustBeRegistryNo))
				|| (in_array($post['typeId'], UFra::shared('UFconf_Sru')->mustBeRegistryNo) && (is_null($val) || $val == ''))) {
					return 'noRegistryNo';
				}
			}else if(in_array($post['typeId'], UFra::shared('UFconf_Sru')->mustBeRegistryNo) && (is_null($val) || $val == '')) {
				return 'noRegistryNo';
			}
		} catch (UFex $e) {
		}
                
		try {
			if(is_null($val) || $val == '') {
                return;
	}
			$user->getByRegistryNo($val);
			if ($change && $this->data['id'] == $user->id) {
					return;
			}
				return 'duplicated';
			} catch (UFex_Dao_NotFound $e) {
		}
	
		return;
	}
        
        public static function checkRegistryNo($val) {
                if (!preg_match('/^[0-9]{5,6}$/', $val)) {  //sprawdzenie poprawnosci formatu nr indeksu
                        $user='invalid';
                } else {
                        $user = UFra::factory('UFbean_Sru_User');
                        try{
                                $user->getByRegistryNo($val);   //odczytanie danych usera o takim samym nr indeksu
                                return $user;
                        } catch(UFex_Dao_NotFound $e){  //unikalny nr indeksu
                                $user='ok';
                        }
                }
		return $user;
        }
	
	protected function validateTypeId($val, $change) {
		if($val == 0 || $val == 20 || is_null($val) || $val == '')
			return 'noTypeId';
		else
			return;
	}

	protected function validateReferralEnd($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'userEdit':'userAdd'};
		if ($post['referralStart'] >= $val) {
			return 'tooOld';
		}
		return;
	}

	public function notifyByEmail() {
		// nie mozna tego zrobic w jednej linii, bo php rzuca bledem "Can't use
		// function return value in write context"
		$ans = array_intersect(array_keys($this->dataChanged), $this->notifyAbout);
		return !empty($ans);
	}
}
