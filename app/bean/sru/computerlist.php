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

	public function deactivateNotSeen($days, $modifiedBy) { 
		return $this->dao->deactivateNotSeen($days, $modifiedBy); 
	}

	public function updateCarerByCarerId($oldCarerId, $newCarerId, $modifiedBy=null) {
		return $this->dao->updateCarerByCarerId($oldCarerId, $newCarerId, $modifiedBy);
	}

	public function updateLocationByUserId($userId, $location, $ip, $modifiedBy=null) {
		return $this->dao->updateLocationByUserId($userId, $location, $ip, $modifiedBy);
	}

	public function updateTypeByUserId($userId, $typeId, $modifiedBy=null) {
		return $this->dao->updateTypeByUserId($userId, $typeId, $modifiedBy);
	}

	public function updateActiveByMasterId($masterId, $active, $modifiedBy=null) {
		return $this->dao->updateActiveByMasterId($masterId, $active, $modifiedBy);
	}
}
