<?
/**
 * usługi usera
 */
class UFdao_Sru_UserService
extends UFdao {

	public function listAllByUserId($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
			
		return $this->doSelect($query);
	}
}
