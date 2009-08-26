<?
/**
 * historia komputera
 */
class UFdao_SruAdmin_ComputerHistory
extends UFdao {

	public function listByComputerIdPK($computer, $id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->computerId, $computer);
		$query->where($mapping->pkName(), $id);

		return $this->doSelect($query);
	}

	public function listByComputerId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->computerId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
