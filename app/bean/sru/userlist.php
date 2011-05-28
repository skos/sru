<?
/**
 * uzytkownicy
 */
class UFbean_Sru_UserList
extends UFbeanList {
	
	public function search(array $params, $studentsOnly = false) {
		$this->data = $this->dao->search($params, $studentsOnly);
	}
	public function quickSearch(array $params) {
		$this->data = $this->dao->quickSearch($params);
	}
}
