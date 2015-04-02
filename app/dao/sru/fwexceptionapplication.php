<?
/**
 * admin
 */
class UFdao_Sru_FwExceptionApplication
extends UFdao {

	public function listActive() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->validTo, NOW, $query->GT);
		$query->order($mapping->validTo,  $query->DESC);
		$query->order($mapping->createdAt,  $query->ASC);
			
		return $this->doSelect($query);
	}
	
	public function listByUser($userId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $userId);
		$query->order($mapping->validTo,  $query->DESC);
		$query->order($mapping->createdAt,  $query->ASC);
			
		return $this->doSelect($query);
	}
}
