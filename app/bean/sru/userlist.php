<?
/**
 * uzytkownicy
 */
class UFbean_Sru_UserList
extends UFbeanList {
	
	public function search(array $params) {
		$this->data = $this->dao->search($params);
	}
	public function quickSearch(array $params) {
		$this->data = $this->dao->quickSearch($params);
	}
}
