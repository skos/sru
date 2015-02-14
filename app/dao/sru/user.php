<?
/**
 * uzytkownik
 */
clASs UFdao_Sru_User
extends UFdao {

	public function getByLoginPASsword($id, $pASsword) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->id, $id);
		$query->WHERE($mapping->pASsword, $pASsword);
		$query->WHERE($mapping->active, true);

		return $this->doSelectFirst($query);
	}

	public function getByLogin($id) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->id, $id);

		return $this->doSelectFirst($query);
	}

	public function getByRegistryNo($registryNo) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->registryNo, $registryNo);

		return $this->doSelectFirst($query);
	}

	public function getByPesel($pesel) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->pesel, $pesel);

		return $this->doSelectFirst($query);
	}

	public function getFromSession() {
		return $this->getByPK($this->_srv->get('session')->auth);
	}

	public function search($params, $studentsOnly = false, $activeOnly = false) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('search');

			$query = $this->prepareSelect($mapping);
			$query->ORDER($mapping->dormitoryId, $query->ASC);
			$query->ORDER($mapping->room, $query->ASC);
			$query->ORDER($mapping->surnameSearch, $query->ASC);
			$query->ORDER($mapping->nameSearch, $query->ASC);
			$query->ORDER($mapping->active, $query->DESC);
                        
                        $acl = UFra::factory('UFacl_SruWalet_Admin');
                        if(!$acl->view()) {
                             $query->WHERE($mapping->active,TRUE);
                        }
			if ($studentsOnly) {
				$query->WHERE($mapping->typeId, UFtpl_Sru_User::$userTypesLimit, $query->LTE); //sami studenci i turyści
			}
			foreach ($params AS $var=>$val) {
				switch ($var) {
					cASe 'surname':
					cASe 'name':
					cASe 'id':
					cASe 'email':
					cASe 'room':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->WHERE($var.'Search', $val, UFlib_Db_Query::LIKE);
						break;
					cASe 'dormitory':
					cASe 'typeId':
						if ($val == UFbean_Sru_User::DB_STUDENT_MAX) {
							$query->WHERE($var, UFbean_Sru_User::DB_STUDENT_MAX, UFlib_Db_Query::LTE);
							$query->WHERE($var, UFbean_Sru_User::DB_STUDENT_MIN, UFlib_Db_Query::GTE);
							break;
						}
						if ($val == UFbean_Sru_User::DB_TOURIST_MAX) {
							$query->WHERE($var, UFbean_Sru_User::DB_TOURIST_MAX, UFlib_Db_Query::LTE);
							$query->WHERE($var, UFbean_Sru_User::DB_TOURIST_MIN, UFlib_Db_Query::GTE);
							break;
						}
					cASe 'active':
					cASe 'registryNo':
					cASe 'pesel':
					default:
						$query->WHERE($var, $val);
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
			$query->WHERE($mapping->typeId, UFtpl_Sru_User::$userTypesLimit, $query->LTE); //sami studenci i turyści
			$query->ORDER($mapping->surnameSearch, $query->ASC);
			foreach ($params AS $var=>$val) {
				switch ($var) {
					cASe 'surname':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->WHERE($var.'Search', $val, UFlib_Db_Query::LIKE);
						break;
					default:
						$query->WHERE($var, $val);
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
		$query->WHERE($mapping->locationId, $roomId);
		$query->ORDER($mapping->surname);

		return $this->doSelect($query);
	}
	
	public function listByRoomActiveOnly($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->locationId, $roomId);
		$query->WHERE($mapping->active, true);
		$query->ORDER($mapping->surname);

		return $this->doSelect($query);
	}

	public function listByEmailActive($email, $active=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->email, $email);
		if (is_bool($active)) {
			$query->WHERE($mapping->active, $active);
		}

		return $this->doSelect($query);
	}

	public function getByEmailActive($email, $active=null) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->email, $email);
		if (is_bool($active)) {
			$query->WHERE($mapping->active, $active);
		}

		return $this->doSelectFirst($query);
	}

	public function listAllActive() {
		$mapping = $this->mapping('stats');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->active, true);

		return $this->doSelect($query);
	}

	public function listActiveByDorm($id, $studentsOnly = false) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->dormitoryId, $id);
		$query->WHERE($mapping->active, true);
		if ($studentsOnly) {
			$query->WHERE($mapping->typeId, UFtpl_Sru_User::$userTypesLimit, $query->LTE); //sami studenci i turyści
		}
		$query->ORDER($mapping->overLimit);
		$query->ORDER($mapping->surname);
		$query->ORDER($mapping->name);

		return $this->doSelect($query);
	}
	
	public function updateToDeactivate($dormitoryId, $modifiedBy){
		$mapping = $this->mapping('set');

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->toDeactivate => true,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
		);
		$query->values($mapping->columns(), $data,  $mapping->columnTypes());
		$query->WHERE(
			$mapping->column('locationId').' IN (SELECT id FROM locations WHERE dormitory_id='.$dormitoryId.')',
			null, $query->SQL
		);
		$query->WHERE($mapping->typeId, UFtpl_Sru_User::$userTypesLimit, $query->LTE); //sami studenci i turyści
		$query->WHERE($mapping->active, true);
		$query->WHERE($mapping->toDeactivate, false);

		return $this->doUpdate($query);
	}
	
	public function listToDeactivate($limit=100) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->active, true);
		$query->WHERE($mapping->toDeactivate, true);
		$query->limit($limit);

		return $this->doSelect($query);
	}
	
	public function listToRemove() {
		$conf = UFra::factory('UFconf_Sru');
		
		$mapping = $this->mapping('toremove');
		$query = $this->prepareSelect($mapping);
		
		$query->raw("select u.id, max(u.modified_at) as deactivated from
			(select r.id, r.modified_at from
			(select f.id, f.active, lead(f.active) over (order by id, f.modified_at desc) as prev_active, f.modified_at from
			(select user_id as id, active, modified_at from users_history where user_id in (select id from users where active = false)
			union
			select id, active, modified_at from users where active = false) as f
			order by id, modified_at desc) as r
			where r.active is distinct from r.prev_active and r.active = false
			order by r.id, r.modified_at desc) as u
			group by u.id
			having max(u.modified_at) < now() - interval '".$conf->userRemoveAfter."months';");

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
		
		$query->raw("SELECT id, name, surname, active, aliAS, study_year_id, EXTRACT (EPOCH FROM birth_date) AS birth_date, pesel, address, registry_no, type_id, faculty_id, (SELECT aliAS FROM faculties WHERE id = faculty_id) AS faculty_aliAS, document_type, document_number, EXTRACT (EPOCH FROM referral_start) AS referral_start, EXTRACT (EPOCH FROM referral_end) AS referral_end, EXTRACT (EPOCH FROM lASt_location_change) AS lASt_location_change, EXTRACT (EPOCH FROM modified_at) AS modified_at FROM
					(SELECT u.id, u.name, u.surname, u.active, l.aliAS, u.study_year_id, u.birth_date, u.pesel, u.address, u.registry_no, u.type_id, u.faculty_id, u.document_type, u.document_number, u.referral_start, u.referral_end, u.lASt_location_change, u.modified_at
					FROM users u, locations l WHERE l.dormitory_id=" . $id . " AND u.referral_end > '".$activeSince."' AND u.location_id = l.id ".
					"UNION SELECT u.user_id AS id, u.name, u.surname, u.active, l.aliAS, u.study_year_id, u.birth_date, u.pesel, u.address, u.registry_no, u.type_id, u.faculty_id, u.document_type::smallint, u.document_number, u.referral_start, u.referral_end, u.lASt_location_change, u.modified_at
					FROM users_history u, locations l WHERE l.dormitory_id=" . $id . " AND u.referral_end > '".$activeSince."' AND u.location_id = l.id)
					AS foo
					ORDER BY id, modified_at DESC, lASt_location_change DESC;");

		return $this->doSelect($query);
	}

	public function listActiveWithoutCompulsoryDataByDorm($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->WHERE($mapping->dormitoryId, $id);
		$query->WHERE($mapping->active, true);
		$query->WHERE($mapping->typeId, UFbean_Sru_User::DB_TOURIST_MAX, UFlib_Db_Query::LTE);
		$query->WHERE(
			'('.$mapping->column('nationality').' IS NULL
				OR '.$mapping->column('address').' IS NULL
				OR '.$mapping->column('address').' = \'\'
				OR ('.$mapping->column('documentNumber').' IS NULL AND '.$mapping->column('documentType').' != \''. UFbean_Sru_User::DOC_TYPE_NONE.'\')
				OR ('.$mapping->column('documentNumber').' = \'\' AND '.$mapping->column('documentType').' != \''. UFbean_Sru_User::DOC_TYPE_NONE.'\')
				OR ('.$mapping->column('pesel').' IS NULL AND '.$mapping->column('nationality').' = \''.UFbean_Sru_User::NATIONALITY_PL_ID.'\')
				OR ('.$mapping->column('pesel').' = \'\' AND '.$mapping->column('nationality').' = \''.UFbean_Sru_User::NATIONALITY_PL_ID.'\')
			)',
			null, $query->SQL
		);
		$query->ORDER($mapping->surname);
		$query->ORDER($mapping->name);

		return $this->doSelect($query);
	}
	
	/**
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio zmodyfikowanych/dodanych użytkowników.
	 *
	 */
	public function listLAStModified($id = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('fullHistory');
		$query = $this->prepareSelect($mapping);
		
		$query->raw("SELECT EXTRACT (EPOCH FROM max(modifieda)) AS modifiedat, 
					id, name, surname, id, banned, active FROM
					(SELECT id , name, surname, id, banned, active, modified_at AS modifieda
					FROM users WHERE modified_by=" . $id . 
					"UNION SELECT user_id AS id, name, surname, id, 
						(SELECT banned FROM users WHERE id = user_id) AS banned, 
						(SELECT active FROM users WHERE id = user_id) AS active, 
						modified_at AS modifieda 
					FROM users_history WHERE modified_by=" . $id . ")
					AS foo
					GROUP BY id, name, surname, id, banned, active 
					ORDER BY modifiedat DESC LIMIT 10;");

		return $this->doSelect($query);
	}
}
