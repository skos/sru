<?
/**
 * komputery
 */
class UFbean_Sru_ComputerList
extends UFbeanList {
	
	public function search(array $params) {
		$this->data = $this->dao->search($params);
	}

	public function updateAvailableMaxTo($date, $modifiedBy=null) {
		return $this->dao->updateAvailableMaxTo($date, $modifiedBy);
	}
}
