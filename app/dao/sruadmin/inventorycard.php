<?php
/**
 * karta wyposazenia
 */
class UFdao_SruAdmin_InventoryCard
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->inventoryNo);
		$query->order($mapping->serialNo);

		return $this->doSelect($query);
	}

	public function listByDormitoryId($dormitoryId, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitoryId);
		$query->order($mapping->inventoryNo);
		$query->order($mapping->serialNo);

		return $this->doSelect($query);
	}
	
	public function listByRoom($room, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $room);
		$query->order($mapping->inventoryNo);
		$query->order($mapping->serialNo);

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
}
