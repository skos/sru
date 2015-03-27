<?
/**
 * funckja
 */
class UFdao_Sru_UserFunction
extends UFdao {

	public function listForUserId($userId) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $userId);
		$query->order($mapping->functionId,  $query->ASC);
			
		return $this->doSelect($query);
	}
	
	public function listForDormitoryId($dormId = null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormId);
		$query->order($mapping->functionId,  $query->ASC);
		$query->order($mapping->userSurname,  $query->ASC);
			
		return $this->doSelect($query);
	}
}
