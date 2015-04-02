<?
/**
 * historia kary
 */
class UFdao_SruAdmin_PenaltyHistory
extends UFdao {

	public function listByPenaltyId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->penaltyId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
