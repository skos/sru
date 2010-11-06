<?php
/**
 * switch
 */
class UFdao_SruAdmin_Switch
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->dormitoryId);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}

	public function listByDormitoryId($dormitoryId, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		$query->order($mapping->dormitoryId);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}

	public function listEnabled() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->hierarchyNo, null, UFlib_Db_Query::NOT_EQ);
		$query->where($mapping->ip, null, UFlib_Db_Query::NOT_EQ);
		$query->order($mapping->dormitoryId);
		$query->order($mapping->hierarchyNo);

		return $this->doSelect($query);
	}

	public function getBySerialNo($no) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->serialNo, $no);

		return $this->doSelectFirst($query);
	}

	public function getByInventoryNo($no) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->inventoryNo, $no);

		return $this->doSelectFirst($query);
	}

	public function getByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $ip);

		return $this->doSelectFirst($query);
	}

	public function getByHierarchyNoAndDorm($no, $dorm) {
		try {
			$mapping = $this->mapping('get');

			$query = $this->prepareSelect($mapping);
			$query->where($mapping->hierarchyNo, $no);
			$query->where($mapping->dormitoryId, $dorm);

			return $this->doSelectFirst($query);
		} catch (UFex_Dao_NotFound $e) {
			return null;
		}
	}
}
