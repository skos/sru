<?
/**
 * komputery
 */
class UFbean_Sru_ComputerList
extends UFbeanList {
	
	public function search(array $params) {
		$this->data = $this->dao->search($params);
	}
	
	public function updateLocationByUserId($location, $user) {
		$this->data = $this->dao->updateLocationByUserId($location, $user);
	}
}
