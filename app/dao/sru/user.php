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

	public function getFromSession() {
		return $this->getByPK($this->_srv->get('session')->auth);
	}

	public function search($params) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('search');

			$query = $this->prepareSelect($mapping);
			$query->order($mapping->surnameSearch, $query->ASC);
			$query->order($mapping->nameSearch, $query->ASC);
			$query->order($mapping->locationAlias, $query->ASC);
			foreach ($params as $var=>$val) {
				switch ($var) {
					case 'surname':
					case 'name':
					case 'login':
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

	public function getOldByEmail($email) {
		$mapping = $this->mapping('old');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->email, $email);

		return $this->doSelectFirst($query);
	}

	public function getFromWalet($name, $surname, $room, $dormitory) {
		// mb_strtolower() ma jakies problemy, jak php wewnetrznie nie jest utf
		$enc = mb_internal_encoding();
		mb_internal_encoding('UTF-8');
		$name = trim(mb_strtolower($name));
		$surname = trim(mb_strtolower($surname));
		mb_internal_encoding($enc);

		$md5 = md5($surname.' '.$name);
		$room = ltrim(trim(mb_strtoupper($room)), '0');
		$dormitory = (int)$dormitory;

		$mapping = $this->mapping('walet');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hash, $md5);
		$query->where($mapping->dormitory, $dormitory);
		$query->where($mapping->room, $room);

		return $this->doSelectFirst($query);
	}
}
