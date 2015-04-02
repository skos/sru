<?
/**
 * historia admina
 */
class UFdao_SruAdmin_AdminHistory
extends UFdao {

	public function listByAdminId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->adminId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
