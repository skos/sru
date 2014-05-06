<?php
/**
 * urządzenie
 */
class UFdao_SruAdmin_Device
extends UFdao {
	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->deviceModelName);

		return $this->doSelect($query);
	}
	
	public function listByDormitoryId($dormitoryId, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		$query->order($mapping->deviceModelName);

		return $this->doSelect($query);
	}
}