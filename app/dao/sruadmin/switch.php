<?php
/**
 * switch
 */
class UFdao_SruAdmin_Switch
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->displayOrder);
		$query->order($mapping->lab);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}

	public function listByDormitoryId($dormitoryId, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		$query->order($mapping->displayOrder);
		$query->order($mapping->lab);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}
	
	public function listByRoom($room, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $room);
		$query->order($mapping->lab);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}

	public function listEnabled() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hierarchyNo, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->ip, null, UFlib_Db_Query::NOT_EQ);
		$query->order($mapping->displayOrder);
		$query->order($mapping->lab);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}
	
	public function listEnabledByModelNo($model) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hierarchyNo, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->ip, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->modelNo, $model);
		$query->order($mapping->displayOrder);
		$query->order($mapping->lab);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}

	public function listEnabledByDormAlias($dorm) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hierarchyNo, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->ip, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->dormitoryAlias, $dorm);
		$query->order($mapping->lab);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}
	
	public function listAllActiveByIpClass($class, $mask) {
		$mapping = $this->mapping('dns');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hierarchyNo, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->ip, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->column('ip').' <<= \''.$class.'/'.$mask.'\'', null, $query->SQL);
		$query->order($mapping->ip, $query->ASC);

		return $this->doSelect($query);
	}
	
	public function listAllActiveByDomain($domain=null) {
		$mapping = $this->mapping('dns');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hierarchyNo, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->ip, null, UFlib_Db_Query::NOT_EQ);
		if (!is_null($domain)) {
			$query->where($mapping->domainSuffix, $domain);
		}
		$query->order($mapping->ip, $query->ASC);

		return $this->doSelect($query);
	}

	//TODO do usunięcia w ramach #343
	public function getBySerialNo($no) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->serialNo, $no);

		return $this->doSelectFirst($query);
	}

	public function getByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $ip);

		return $this->doSelectFirst($query);
	}

	public function getByHierarchyNoDormLab($no, $dorm, $lab) {
		try {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->hierarchyNo, $no);
			$query->where($mapping->dormitoryId, $dorm);
			$query->where($mapping->lab, $lab);

			return $this->doSelectFirst($query);
		} catch (UFex_Dao_NotFound $e) {
			return null;
		}
	}
}
