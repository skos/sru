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
/* 
	public function listByUserIdAll($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->order($mapping->active, $query->DESC);
		$query->order($mapping->host, $query->ASC);

		return $this->doSelect($query);
	}
*/
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
					default:
						$query->where($var, $val);
				}
			}

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function updateLocationByUserId($location, $user, $modifiedBy=null) {
		$mapping = $this->mapping('set');

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->locationId => $location,
			$mapping->modifiedById => $modifiedBy,
			$mapping->modifiedAt => NOW,
		);
		$query->values($mapping->columns(), $data,  $mapping->columnTypes());
		$query->where($mapping->userId, $user);
		$query->where($mapping->active, true);

		$return = $this->doUpdate($query);
		return $return;
	}
	public function listAllServers() {
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

	public function listByRoom($roomId) {
	
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $roomId);
		$query->order($mapping->host);

		return $this->doSelect($query);
	}			
}
