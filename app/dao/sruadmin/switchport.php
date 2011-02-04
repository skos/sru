<?php
/**
 * porty switcha
 */
class UFdao_SruAdmin_SwitchPort
extends UFdao {

	public function listBySwitchId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->switchId, $id);
		$query->order($mapping->ordinalNo, $query->ASC);

		return $this->doSelect($query);
	}

	public function listByLocationId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $id);
		$query->order($mapping->switchNo, $query->ASC);
		$query->order($mapping->ordinalNo, $query->ASC);

		return $this->doSelect($query);
	}

	public function getByIpAndOrdinalNo($ip, $ordinalNo) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->switchIp, $ip);
		$query->where($mapping->ordinalNo, $ordinalNo);

		return $this->doSelectFirst($query);
	}

	public function getBySwitchIdAndOrdinalNo($switchId, $ordinalNo) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->switchId, $switchId);
		$query->where($mapping->ordinalNo, $ordinalNo);

		return $this->doSelectFirst($query);
	}

	public function listPortsWithSwitches() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->connectedSwitchIp, NULL, UFlib_Db_Query::NOT_EQ);
		$query->order($mapping->switchIp, $query->ASC);
		$query->order($mapping->ordinalNo, $query->ASC);

		return $this->doSelect($query);
	}
}
