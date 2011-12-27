<?
/**
 * komputer
 */
class UFdao_Sru_Computer
extends UFdao {

	public function edit(array $data, array $dataAll=array()) {
		$return = parent::edit($data, $dataAll);
		$this->cacheDel($this->cachePrefix.'/getByUserIdPK/'.$dataAll['userId'].'/'.$dataAll['id']);
		$this->cacheDel($this->cachePrefix.'/listAllByUserId/'.$dataAll['userId']);
		$this->cacheDel($this->cachePrefix.'/getByHost/'.$dataAll['host']);
		return $return;
	}

	public function getByTypeId($typeId) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, $typeId);
		$query->where($mapping->active, true);

		return $this->doSelectFirst($query);
	}

	public function getByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $ip);
		$query->where($mapping->active, true);

		return $this->doSelectFirst($query);
	}

	public function getByHost($host) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->host, $host);
		$query->where($mapping->active, true);

		return $this->doSelectFirst($query);
	}

	public function getByMac($mac) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->mac, $mac);
		$query->where($mapping->active, true);

		return $this->doSelectFirst($query);
	}
	
	public function listByUserId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
	
	public function listByUserIdInactive($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->where($mapping->active, false);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}	

	public function getInactiveByMacUserId($mac, $user) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.$mac.'/'.$user;
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->mac, $mac);
			$query->where($mapping->userId, $user);
			$query->where($mapping->active, false);

			$return = $this->doSelectFirst($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function getInactiveByHostUserId($host, $user) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.$host.'/'.$user;
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->host, $host);
			$query->where($mapping->userId, $user);
			$query->where($mapping->active, false);

			$return = $this->doSelectFirst($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function getByUserIdPK($user, $pk) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.$user.'/'.$pk;
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->pkName(), $pk);
			$query->where($mapping->userId, $user);

			$return = $this->doSelectFirst($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}
	
	public function getByUserId($user, $mode = 'asc') {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $user);
		if($mode == 'desc'){
			$query->order($mapping->id, $query->DESC);
		}

		return $this->doSelect($query);
	}

	public function listAllActiveByType($type=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		if (is_int($type)) {
			$query->where($mapping->typeId, $type);
		} elseif (is_array($type)) {
			$query->where($mapping->typeId, $type, $query->IN);
		}
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function search($params) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('list');

			$query = $this->prepareSelect($mapping);
			$query->order($mapping->active, $query->DESC);
			$query->order($mapping->host, $query->ASC);
			foreach ($params as $var=>$val) {
				switch ($var) {
					case 'host':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->where($var, $val, UFlib_Db_Query::LIKE);
						break;
					case 'ip':
						if (substr($val, 0, 6) != '153.19') {
							$val = '153.19.' . $val;
						}
						$query->where($var, $val);
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

	public function listAllServers() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, UFbean_Sru_Computer::LIMIT_SERVER, $query->GTE);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

		public function listAllPhysicalServers() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_SERVER);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function listAllOrganization() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_ORGANIZATION);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
	
	public function listAllAdministration() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_ADMINISTRATION);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function listAllActiveByIpClass($class) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->where($mapping->column('ip').' <<= \'153.19.'.(int)$class.'/24\'', null, $query->SQL);
		$query->order($mapping->ip, $query->ASC);

		return $this->doSelect($query);
	}

	public function listEthers() {
		$mapping = $this->mapping('ethers');
		
		$conf = UFra::factory('UFconf_Sru');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $conf->noEthers, $query->NOT_IN);
		$query->distinct();
		$query->order($mapping->ip, $query->ASC);

		return $this->doSelect($query);
	}

	public function listAdmins() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->canAdmin, true);
		$query->where($mapping->active, true);
		$query->order($mapping->ip, $query->ASC);

		return $this->doSelect($query);
	}

	public function listTourists() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_TOURIST);
		$query->where($mapping->active, true);
		$query->order($mapping->ip, $query->ASC);

		return $this->doSelect($query);
	}

	public function listByRoom($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $roomId);
		$query->order($mapping->host);

		return $this->doSelect($query);
	}			
	
	public function listByRoomActiveOnly($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $roomId);
		$query->where($mapping->active, true);
		$query->order($mapping->host);

		return $this->doSelect($query);
	}

	public function listActiveStudsByDormitoryId($dormId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormId);
		$query->where($mapping->active, true);
		$query->where($mapping->typeId, UFbean_Sru_Computer::LIMIT_STUDENT_AND_TOURIST, $query->LTE);
		$query->order($mapping->mac);

		return $this->doSelect($query);
	}			

	public function listOutdated($limit=100) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->where($mapping->availableTo, time(), $query->LT);
		$query->limit($limit);

		return $this->doSelect($query);
	}
	
	/**
	 * 
	 * Aktualizuje typ i lokalizację komputera
	 * @param int $userId id użytkownika, do którego należy komputer
	 * @param int $location id pokoju, do którego przenoszony jest komputer
	 * @param int $typeId id typu, jak przypisywany jest komputerowi
	 * @param int $modifiedBy id osoby dokonującej zmian
	 */
	public function updateLocationAndTypeByUserId($userId, $location, $typeId, $modifiedBy = null){
		$mapping = $this->mapping('set');

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->typeId => $typeId,
			$mapping->locationId => $location,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
		);
		$query->values($mapping->columns(), $data,  $mapping->columnTypes());
		$query->where($mapping->userId, $userId);
		$query->where($mapping->active, true);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_SERVER, $query->NOT_EQ);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_SERVER_VIRT, $query->NOT_EQ);

		$return = $this->doUpdate($query);

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->locationId => $location,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
		);
		$query->values($mapping->columns(), $data,  $mapping->columnTypes());
		$query->where($mapping->userId, $userId);
		$query->where($mapping->active, true);
		$query->where(
			'('.$mapping->column('typeId').'!='.UFbean_Sru_Computer::TYPE_SERVER.' OR '.$mapping->column('typeId').'!='.UFbean_Sru_Computer::TYPE_SERVER_VIRT.')',
			null, $query->SQL
		);
		$query->where($mapping->locationId, $location, $query->NOT_EQ);

		$return = $return && $this->doUpdate($query);

		return $return;
	}

	/**
	 * Aktualizuje opiekuna hosta
	 */
	public function updateCarerByCarerId($oldCarerId, $newCarerId, $modifiedBy=null) {
		$mapping = $this->mapping('set');

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->carerId => $newCarerId,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
		);
		$query->values($mapping->columns(), $data,  $mapping->columnTypes());
		$query->where($mapping->carerId, $oldCarerId);
		$query->where($mapping->active, true);

		$return = $this->doUpdate($query);
		return $return;
	}

	public function updateActiveByMasterId($masterId, $active, $modifiedBy=null) {
		$mapping = $this->mapping('set');

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->active => $active,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
		);
		$query->values($mapping->columns(), $data,  $mapping->columnTypes());
		$query->where($mapping->masterHostId, $masterId);
		$query->where($mapping->active, !$active);

		$return = $this->doUpdate($query);
		return $return;
	}
	
	/**
	 * Funkcja konstruująca zapytanie wyciągające 10 ostatnio zmodyfikowanych/dodanych komputerów.
	 *
	 */
	public function listLastModified($id = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('fullHistory');
		$query = $this->prepareSelect($mapping);
		
		$query->raw("SELECT computer_id AS id, hoste AS host, EXTRACT (EPOCH FROM max(modifieda)) AS modifiedat, 
							userid, name, surname, login, banned, active FROM
							(SELECT user_id as userid, id AS computer_id, host as hoste, modified_at AS modifieda
							 FROM computers WHERE modified_by=" . $id . 
							"UNION SELECT user_id as userid, computer_id as computer_id, host as hoste, 
								modified_at AS modifieda FROM computers_history WHERE modified_by=" . $id .")
							AS foo LEFT JOIN users ON id = userid
							GROUP BY hoste, computer_id, userid, name, surname, login, banned, active 
							ORDER BY modifiedAt DESC LIMIT 10;");
		
		return $this->doSelect($query);
	}

	/**
	 * Dezaktywuje komputery, które nie były widzialne w sieci (i aktywowane) od $days dni
	 * @return bool sukces lub porażka
	 */
	public function deactivateNotSeen($days, $modifiedBy = null) {
		$mapping = $this->mapping('set');
		$data = array(
			$mapping->active => false,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
			$mapping->availableTo => NOW,
			$mapping->canAdmin = false,
			$mapping->exAdmin = false,
		);
		$query = $this->prepareUpdate($mapping, $data);
		$query->where($mapping->active, true);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_SERVER, $query->NOT_EQ);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_SERVER_VIRT, $query->NOT_EQ);
		$query->where($mapping->autoDeactivation, true);
		$query->where($mapping->lastActivated, time() - $days*24*60*60, $query->LT);
		$query->where(
			'('.$mapping->column('lastSeen').' < TO_TIMESTAMP('.(time() - $days*24*60*60).') OR '.$mapping->column('lastSeen').' IS NULL)',
			null, $query->SQL
		);
		
		return $this->doUpdate($query);
	}

	/**
	 * Przywraca komputery użytkownika userId i ustawia im nowe IP w razie potrzeby
	 * @param int $userId nr id użytkownika
	 * @param bool $dormitoryChanged
	 * @return bool sukces
	 */
	public function restore($userId, $dormitoryChanged, $modifiedBy = null){
		try {
			$comps = $this->getByUserId($userId);
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($userId);
		}catch(Exception $e){
			return true;
		}

		for ($i = 0; $i < count($comps); $i++){
			$name = $comps[$i]['host'];
			$newName = '';
			$iterator = '0';
			
			while(1){
				try{
					$this->getByHost($name);
					$name = $comps[$i]['host'] . $iterator;
					$iterator += 1;
				} catch (Exception $e) {
					if ($name != $comps[$i]['host']){
						$newName = $name;
					}
					break;
				}
			}

			try{
				if(!$dormitoryChanged){
					$this->getByIp($comps[$i]['ip']);
					$this->setNewIp($comps[$i], $modifiedBy, $newName);
				}else{
					try{
						$this->setNewIp($comps[$i], $modifiedBy, $newName);
					} catch (Exception $e) {
					}
				}
			}catch(Exception $e){
				try{
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					if($ip->checkIpDormitory($comps[$i]['ip'], $user->dormitoryId)){
						$this->restoreWithOldIp($comps[$i], $modifiedBy, $newName);
					}else{
						$this->setNewIp($comps[$i], $modifiedBy, $newName);
					}
				} catch(Exception $e) {
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Aktywuje komputer z nowym adresem IP o ile to możliwe
	 * @param array $comp tablica zawierająca dane hosta
	 * @param int $modifiedBy id wprowadzającego zmiany
	 * @return bool sukces lub porażka
	 */
	public function setNewIp($comp, $modifiedBy = null, $newName = ''){
		$user = UFra::factory('UFbean_Sru_User');
		$user->getByPK($comp['userId']);
		$ip = UFra::factory('UFbean_Sru_Ipv4');
		try{
			$ip->getFreeByDormitoryId($user->dormitoryId);
		}catch (Exception $e){
			//UFra::error("Nie znaleziono wolnego IP: " . $e);
			return true;
		}
		$mapping = $this->mapping('set');
		$data = array();
		
		if($newName != ''){
			$data = array(
				$mapping->host => $newName,
				$mapping->modifiedById => $modifiedBy,
				$mapping->modifiedAt => NOW,
				$mapping->ip => $ip->ip,
				$mapping->active => true,
				$mapping->availableTo => null,
			);
		} else {
			$data = array(
				$mapping->modifiedById => $modifiedBy,
				$mapping->modifiedAt => NOW,
				$mapping->ip => $ip->ip,
				$mapping->active => true,
				$mapping->availableTo => null,
			);
		}

		$query = $this->prepareUpdate($mapping, $data);
		$query->where($mapping->host, $comp['host']);
		$query->where($mapping->userId, $comp['userId']);
		$query->where($mapping->active, false);
		$query->where($mapping->modifiedAt, time() - UFra::shared('UFconf_Sru')->timeForComputersRestoration, $query->GT);
		
		try{
			return $result = $this->doUpdate($query);
		}catch(Exception $e){
			return false;
		}
		
		return false;
	}
	
	/**
	 * Aktywuje komputer z jego starym adresem IP, o ile to możliwe
	 * @param array $comp tablica zawierająca dane hosta
	 * @param int $modifiedBy id wprowadzającego zmiany
	 * @return bool sukces lub porażka
	 */
	public function restoreWithOldIp($comp, $modifiedBy = null, $newName = ''){
		$user = UFra::factory('UFbean_Sru_User');
		$user->getByPK($comp['userId']);
		$mapping = $this->mapping('set');	
		$data = array();
		
		if($newName != ''){
			$data = array(
				$mapping->host => $newName,
				$mapping->modifiedById => $modifiedBy,
				$mapping->modifiedAt => NOW,
				$mapping->active => true,
				$mapping->availableTo => null,
			);
		} else {
			$data = array(
				$mapping->modifiedById => $modifiedBy,
				$mapping->modifiedAt => NOW,
				$mapping->active => true,
				$mapping->availableTo => null,
			);
		}
		$query = $this->prepareUpdate($mapping, $data);
		$query->where($mapping->host, $comp['host']);
		$query->where($mapping->userId, $comp['userId']);
		$query->where($mapping->active, false);
		$query->where($mapping->modifiedAt, time() - UFra::shared('UFconf_Sru')->timeForComputersRestoration, $query->GT);
		
		try{
			return $result = $this->doUpdate($query);
		}catch(Exception $e){
			return false;
		}
		
		return false;
	}

	/**
	 * Wyświetla serwery wirtualne dla danego serwera
	 */
	public function listVirtualsByComputerId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_SERVER_VIRT);
		$query->where($mapping->masterHostId, $id);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function listCaredByAdminId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->carerId, $id);
		$query->where($mapping->active, true);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}

	public function listActiveWithoutCarerByDorm($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $id);
		$query->where($mapping->active, true);
		$query->where($mapping->carerId, null);
		$query->where($mapping->typeId, UFbean_Sru_Computer::TYPE_ADMINISTRATION);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
	
	
}
