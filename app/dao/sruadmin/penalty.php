<?
/**
 * admin
 */
class UFdao_SruAdmin_Penalty
extends UFdao {
	public function listAll() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		
		$query->order($mapping->modifiedAt);
		
//		$query->where($mapping->active, true); 
			
		return $this->doSelect($query);
	}
}
