<?
/**
 * uzytkownik
 */
class UFdao_Sru_User
extends UFdao {

	public function getByLoginPassword($login, $password) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);
		$query->where($mapping->password, $password);
		$query->where($mapping->active, true);

		return $this->doSelectFirst($query);
	}

	public function getByLogin($login) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->login, $login);

		return $this->doSelectFirst($query);
	}

	public function getByRegistryNo($registryNo) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->registryNo, $registryNo);

		return $this->doSelectFirst($query);
	}

	public function getByPesel($pesel) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->pesel, $pesel);

		return $this->doSelectFirst($query);
	}

	public function getFromSession() {
		return $this->getByPK($this->_srv->get('session')->auth);
	}

	public function search($params, $studentsOnly = false) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('search');

			$query = $this->prepareSelect($mapping);
			$query->order($mapping->dormitoryId, $query->ASC);
			$query->order($mapping->room, $query->ASC);
			$query->order($mapping->surnameSearch, $query->ASC);
			$query->order($mapping->nameSearch, $query->ASC);
			$query->order($mapping->active, $query->DESC);
			if ($studentsOnly) {
				$query->where($mapping->typeId, UFtpl_Sru_User::$userTypesLimit, $query->LTE); //sami studenci i turyści
			}
			foreach ($params as $var=>$val) {
				switch ($var) {
					case 'surname':
					case 'name':
					case 'login':
					case 'email':
					case 'room':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->where($var.'Search', $val, UFlib_Db_Query::LIKE);
						break;
					case 'dormitory':
					case 'typeId':
						if ($val == UFbean_Sru_User::DB_STUDENT_MAX) {
							$query->where($var, UFbean_Sru_User::DB_STUDENT_MAX, UFlib_Db_Query::LTE);
							$query->where($var, UFbean_Sru_User::DB_STUDENT_MIN, UFlib_Db_Query::GTE);
							break;
						}
						if ($val == UFbean_Sru_User::DB_TOURIST_MAX) {
							$query->where($var, UFbean_Sru_User::DB_TOURIST_MAX, UFlib_Db_Query::LTE);
							$query->where($var, UFbean_Sru_User::DB_TOURIST_MIN, UFlib_Db_Query::GTE);
							break;
						}
					case 'registryNo':
					case 'pesel':
					default:
						$query->where($var, $val);
				}
			}

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function quickSearch($params) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('search');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->typeId, UFtpl_Sru_User::$userTypesLimit, $query->LTE); //sami studenci i turyści
			$query->order($mapping->surnameSearch, $query->ASC);
			foreach ($params as $var=>$val) {
				switch ($var) {
					case 'surname':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->where($var.'Search', $val, UFlib_Db_Query::LIKE);
						break;
					default:
						$query->where($var, $val);
				}
			}

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function listByRoom($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $roomId);
		$query->order($mapping->surname);

		return $this->doSelect($query);
	}
	
	public function listByRoomActiveOnly($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $roomId);
		$query->where($mapping->active, true);
		$query->order($mapping->surname);

		return $this->doSelect($query);
	}

	public function listByEmailActive($email, $active=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->email, $email);
		if (is_bool($active)) {
			$query->where($mapping->active, $active);
		}

		return $this->doSelect($query);
	}

	public function getByEmailActive($email, $active=null) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->email, $email);
		if (is_bool($active)) {
			$query->where($mapping->active, $active);
		}

		return $this->doSelectFirst($query);
	}

	public function listAllActive() {
		$mapping = $this->mapping('stats');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);

		return $this->doSelect($query);
	}

	public function listActiveByDorm($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $id);
		$query->where($mapping->active, true);
		$query->order($mapping->surname);
		$query->order($mapping->name);

		return $this->doSelect($query);
	}
	
	/**
	 * Pobiera dane użytkowników do ksiązki meldunkowej
	 * - wszystkich, którzy w danym okresie mieszkali w danym DSie
	 * @param type $id
	 * @return type 
	 */
	public function listToRegBookByDorm($id, $activeSince) {
		$mapping = $this->mapping('regbook');
		$query = $this->prepareSelect($mapping);
		
		$query->raw("SELECT id, name, surname, active, alias, study_year_id, EXTRACT (EPOCH FROM birth_date) AS birth_date, address, registry_no, type_id, faculty_id, (SELECT alias from faculties WHERE id = faculty_id) AS faculty_alias, document_type, document_number, EXTRACT (EPOCH FROM referral_start) AS referral_start, EXTRACT (EPOCH FROM referral_end) AS referral_end, EXTRACT (EPOCH FROM last_location_change) AS last_location_change FROM
					(SELECT u.id, u.name, u.surname, u.active, l.alias, u.study_year_id, u.birth_date, u.address, u.registry_no, u.type_id, u.faculty_id, u.document_type, u.document_number, u.referral_start, u.referral_end, u.last_location_change
					FROM users u, locations l WHERE l.dormitory_id=" . $id . " AND u.referral_end > '".$activeSince."' AND u.location_id = l.id ".
					"UNION SELECT u.user_id AS id, u.name, u.surname, u.active, l.alias, u.study_year_id, u.birth_date, u.address, u.registry_no, u.type_id, u.faculty_id, u.document_type::smallint, u.document_number, u.referral_start, u.referral_end, u.last_location_change
					FROM users_history u, locations l WHERE l.dormitory_id=" . $id . " AND u.referral_end > '".$activeSince."' AND u.location_id = l.id)
					AS foo
					ORDER BY surname, name, last_location_change DESC;");

		return $this->doSelect($query);
	}

	public function listActiveWithoutCompulsoryDataByDorm($id) {
		$mapping = $this->mapping('list');
		$conf = UFra::factory('UFconf_Sru');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $id);
		$query->where($mapping->active, true);
		$query->where($mapping->typeId, UFbean_Sru_User::DB_TOURIST_MAX, UFlib_Db_Query::LTE);
		$query->where(
			'('.$mapping->column('nationality').' IS NULL
				OR '.$mapping->column('address').' IS NULL
				OR '.$mapping->column('address').' = \'\'
				OR '.$mapping->column('documentNumber').' IS NULL
				OR '.$mapping->column('documentNumber').' = \'\'
				OR ('.$mapping->column('pesel').' IS NULL AND '.$mapping->column('nationality').' = \''.UFbean_Sru_User::NATIONALITY_PL_ID.'\')
				OR ('.$mapping->column('pesel').' = \'\' AND '.$mapping->column('nationality').' = \''.UFbean_Sru_User::NATIONALITY_PL_ID.'\')
			)',
			null, $query->SQL
		);
		$query->order($mapping->surname);
		$query->order($mapping->name);

		return $this->doSelect($query);
	}
	
	/**
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio zmodyfikowanych/dodanych użytkowników.
	 *
	 */
	public function listLastModified($id = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('fullHistory');
		$query = $this->prepareSelect($mapping);
		
		$query->raw("SELECT EXTRACT (EPOCH FROM max(modifieda)) AS modifiedat, 
					id, name, surname, login, banned, active FROM
					(SELECT id , name, surname, login, banned, active, modified_at AS modifieda
					FROM users WHERE modified_by=" . $id . 
					"UNION SELECT user_id AS id, name, surname, login, 
						(SELECT banned FROM users WHERE id = user_id) AS banned, 
						(SELECT active FROM users WHERE id = user_id) AS active, 
						modified_at AS modifieda 
					FROM users_history WHERE modified_by=" . $id . ")
					AS foo
					GROUP BY id, name, surname, login, banned, active 
					ORDER BY modifiedat DESC LIMIT 10;");

		return $this->doSelect($query);
	}
}
