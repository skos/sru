<?
/**
 * kary komputera
 */
class UFdao_SruAdmin_ComputerBan
extends UFdao {

	public function listByPenaltyId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->penaltyId, $id);
		$query->order($mapping->computerHost, $query->ASC);

		return $this->doSelect($query);
	}

	public function listByComputerId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->computerId, $id);
		$query->order($mapping->id, $query->DESC);

		return $this->doSelect($query);
	}
}
