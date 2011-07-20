<?
/**
 * historia usług userów
 */
class UFdao_SruAdmin_ServiceHistory
extends UFdao {

		public function listByUserId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->order($mapping->pkName(), $query->DESC);

		return $this->doSelect($query);
	}
}
