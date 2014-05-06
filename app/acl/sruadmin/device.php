<?
/**
 * sprawdzanie uprawnien urzadzenia
 */
class UFacl_SruAdmin_Device
extends UFlib_ClassWithService {
	
	protected function _loggedIn() {
		return $this->_srv->get('session')->is('authAdmin');
	}

	public function edit() {
		return $this->_loggedIn();
	}

	public function add() {
		return $this->_loggedIn();
	}
	
	public function inventoryCardAdd() {
		try {
			if (!$this->_loggedIn()) {
				return false;
			}
			$bean = UFra::factory('UFbean_SruAdmin_Device');
			$bean->getByPK($this->_srv->get('req')->get->deviceId);

			try {
				$ic = UFra::factory('UFbean_SruAdmin_InventoryCard');
				$ic->getByDeviceIdAndDeviceTable($bean->id, UFbean_SruAdmin_InventoryCard::TABLE_DEVICE);
				return false;
			} catch (UFex_Dao_NotFound $e) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function inventoryCardEdit() {
		try {
			if (!$this->_loggedIn()) {
				return false;
			}
			$bean = UFra::factory('UFbean_SruAdmin_Device');
			$bean->getByPK($this->_srv->get('req')->get->deviceId);

			try {
				$ic = UFra::factory('UFbean_SruAdmin_InventoryCard');
				$ic->getByDeviceIdAndDeviceTable($bean->id, UFbean_SruAdmin_InventoryCard::TABLE_DEVICE);
				return true;
			} catch (UFex_Dao_NotFound $e) {
				return false;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function inventoryCardView() {
		return $this->inventoryCardEdit();
	}

}
