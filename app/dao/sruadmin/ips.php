<?
/**
 * admin
 */
class UFdao_SruAdmin_Ips
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->ip,  $query->ASC);
			
		return $this->doSelect($query);
	}
}
