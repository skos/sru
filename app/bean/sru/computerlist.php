<?
/**
 * komputery
 */
class UFbean_Sru_ComputerList
extends UFbeanList {
	
	public function search(array $params) {
		$this->data = $this->dao->search($params);
	}
}
