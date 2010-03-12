<?
/**
 * usługi userów
 */
class UFdao_SruAdmin_UserService
extends UFdao {

	public function listToActivate($serviceType = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->state, false);
		if ($serviceType != null) {
			$query->where($mapping->servType, $serviceType);
		}
		$query->order($mapping->servName,  $query->ASC);
		$query->order($mapping->userLogin,  $query->ASC);
	
		return $this->doSelect($query);
	}

	public function listToDeactivate($serviceType = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->state, null);
		if ($serviceType != null) {
			$query->where($mapping->servType, $serviceType);
		}
		$query->order($mapping->servName,  $query->ASC);
		$query->order($mapping->userLogin,  $query->ASC);
	
		return $this->doSelect($query);
	}

	public function listActive($serviceType = null, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->state, true);
		if ($serviceType != null) {
			$query->where($mapping->servType, $serviceType);
		}
		$query->order($mapping->servName,  $query->ASC);
		$query->order($mapping->userLogin,  $query->ASC);
	
		return $this->doSelect($query);
	}
}
