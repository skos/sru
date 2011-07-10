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

	public function listByLocationId($id, $withPenalty = true) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->locationId, $id);
		$query->where($mapping->switchIp, NULL, UFlib_Db_Query::NOT_EQ);
		if (!$withPenalty) {
			$query->where($mapping->penaltyId, NULL);
		}
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

	public function listByPenaltyId($penaltyId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->penaltyId, $penaltyId);

		return $this->doSelect($query);
	}

	public function updatePenaltyIdByPortId($portId, $penaltyId = null) {
		$mapping = $this->mapping('set');

		$query = UFra::factory('UFlib_Db_Query');
		$query->tables($mapping->tables());
		$query->joins($mapping->joins(), $mapping->joinOns());
		$data = array(
			$mapping->penaltyId => $penaltyId,
		);
		$query->values($mapping->columns(), $data, $mapping->columnTypes());
		$query->where($mapping->ida, $portId);

		$return = $this->doUpdate($query);
		return $return;
	}
}
