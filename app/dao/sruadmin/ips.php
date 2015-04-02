<?
/**
 * admin
 */
class UFdao_SruAdmin_Ips
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->vlan,  $query->ASC);
		$query->order($mapping->ip,  $query->ASC);
			
		return $this->doSelect($query);
	}

	public function listByDormitory($dormitory, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where(
			'('.$mapping->column('dormitoryId').'='.$dormitory.' OR '.$mapping->column('computerDormitoryId').'='.$dormitory.')',
			null, $query->SQL
		);
		$query->order($mapping->vlan, $query->ASC);
		$query->order($mapping->ip, $query->ASC);
			
		return $this->doSelect($query);
	}
	
	public function listByVlanId($vlan, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->vlan, $vlan); 
		$query->order($mapping->ip, $query->ASC);
			
		return $this->doSelect($query);
	}
}
