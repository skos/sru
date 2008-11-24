<?
/**
 * user
 */
class UFdao_Sru_Penalty
extends UFdao {

	public function listByUserId($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->where($mapping->endAt, time(), $query->GTE); 
		$query->order($mapping->endAt,  $query->DESC);
		
			
		return $this->doSelect($query);
	}

	public function listAllByUserId($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->where($mapping->endAt, time(), $query->LTE); 
		$query->order($mapping->endAt,  $query->DESC);
		
			
		return $this->doSelect($query);
	}
}
