<?
/**
 * usługi userów
 */
class UFdao_SruAdmin_UserService
extends UFdao {

	public function listToActivate($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->state, false);
		$query->order($mapping->servName,  $query->ASC);
		$query->order($mapping->userLogin,  $query->ASC);
	
		return $this->doSelect($query);
	}

	public function listToDeactivate($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->state, null);
		$query->order($mapping->servName,  $query->ASC);
		$query->order($mapping->userLogin,  $query->ASC);
	
		return $this->doSelect($query);
	}

	public function listActive($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->state, true);
		$query->order($mapping->servName,  $query->ASC);
		$query->order($mapping->userLogin,  $query->ASC);
	
		return $this->doSelect($query);
	}
}
