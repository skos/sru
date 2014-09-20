<?
/**
 * sprawdzanie uprawnien
 */
class UFacl_SruAdmin_Computer
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

	public function addForUser($id) {
		if (!$this->_loggedIn()) {
			return false;
		}
		$bean = UFra::factory('UFbean_Sru_User');
		try {
			$bean->getByPK($id);
		} catch (Exception $e) {
			return false;
		}
		// hosta można dodać tylko aktywnemu użytkownikowi
		if ($bean->active === false || is_null($bean->email) || $bean->email == '' || (is_null($bean->studyYearId) && $bean->facultyId != 0)) {
			return false;
		}
		return true;
	}
	
	public function editAliases() {
		try {
			if (!$this->_loggedIn()) {
				return false;
			}
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK($this->_srv->get('req')->get->computerId);

			if ($bean->active && ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT || 
				$bean->typeId == UFbean_Sru_Computer::TYPE_MACHINE || $bean->typeId == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE || 
				$bean->typeId == UFbean_Sru_Computer::TYPE_ADMINISTRATION  || 
				$bean->typeId == UFbean_Sru_Computer::TYPE_ORGANIZATION || $bean->canAdmin || $bean->exAdmin)) {
			    return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function inventoryCardAdd() {
		try {
			if (!$this->_loggedIn()) {
				return false;
			}
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK($this->_srv->get('req')->get->computerId);

			if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_MACHINE) {
				try {
					$ic = UFra::factory('UFbean_SruAdmin_InventoryCard');
					$ic->getByDeviceIdAndDeviceTable($bean->id, UFbean_SruAdmin_InventoryCard::TABLE_COMPUTER);
					return false;
				} catch (UFex_Dao_NotFound $e) {
					return true;
				}
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
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK($this->_srv->get('req')->get->computerId);

			if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_MACHINE) {
				try {
					$ic = UFra::factory('UFbean_SruAdmin_InventoryCard');
					$ic->getByDeviceIdAndDeviceTable($bean->id, UFbean_SruAdmin_InventoryCard::TABLE_COMPUTER);
					return true;
				} catch (UFex_Dao_NotFound $e) {
					return false;
				}
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function inventoryCardView() {
		return $this->inventoryCardEdit();
	}

	public function del() {
		return $this->_loggedIn();
	}
}
