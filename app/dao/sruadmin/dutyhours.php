<?php
/**
 * godziny dyzurow
 */
class UFdao_SruAdmin_DutyHours
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->day);
		$query->order($mapping->startHour);

		return $this->doSelect($query);
	}

	public function listByAdminId($adminId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->adminId, $adminId);
		$query->order($mapping->day);
		$query->order($mapping->startHour);

		return $this->doSelect($query);
	}
}
