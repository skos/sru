<?
/**
 * uzytkownicy
 */
class UFbean_Sru_UserList
extends UFbeanList {
	
	public function search(array $params, $studentsOnly = false, $activeOnly = false) {
		$this->data = $this->dao->search($params, $studentsOnly, $activeOnly);
	}
	public function quickSearch(array $params) {
		$this->data = $this->dao->quickSearch($params);
	}
	
	public function updateToDeactivate($dormitoryId, $modifiedBy) {
		return $this->dao->updateToDeactivate($dormitoryId, $modifiedBy);
	}
}
