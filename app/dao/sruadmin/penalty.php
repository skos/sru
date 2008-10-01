<?
/**
 * admin
 */
class UFdao_SruAdmin_Penalty
extends UFdao {
	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->order($mapping->modifiedAt,  $query->DESC);
		
//		$query->where($mapping->active, true); 
			
		return $this->doSelect($query);
	}
}
