<?
/**
 * historia switcha
 */
class UFdao_SruAdmin_SwitchHistory
extends UFdao {

	public function listBySwitchId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->switchId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
