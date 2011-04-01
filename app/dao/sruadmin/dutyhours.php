<?php
/**
 * godziny dyzurow
 */
class UFdao_SruAdmin_DutyHours
extends UFdao {

	public function listAllUpcoming() {
		$mapping = $this->mapping('listDetails');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->order($mapping->day);
		$query->order($mapping->startHour);

		return $this->doSelect($query);
	}

	public function listAllForTable() {
		$mapping = $this->mapping('listDetails');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->adminDormId);
		$query->order($mapping->adminName);
		$query->order($mapping->day);

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

	public function getByAdminIdAndDay($adminId, $day) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->adminId, $adminId);
		$query->where($mapping->day, $day);

		return $this->doSelectFirst($query);
	}
}
