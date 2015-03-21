<?
/**
 * admin
 */
class UFdao_SruAdmin_FwException
extends UFdao {

	public function listActive() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true);
		$query->order($mapping->host,  $query->ASC);
		$query->order($mapping->port,  $query->ASC);
			
		return $this->doSelect($query);
	}
	
	public function listActiveByComputerId($computerId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->computerId, $computerId);
		$query->where($mapping->active, true);
		$query->order($mapping->port,  $query->ASC);
			
		return $this->doSelect($query);
	}
	
	public function getActive($port, $computerId) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->port, $port);
		$query->where($mapping->computerId, $computerId);
		$query->where($mapping->active, true);
			
		return $this->doSelect($query);
	}
}
