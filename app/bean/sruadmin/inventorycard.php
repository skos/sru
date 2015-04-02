<?
/**
 * karta wyposazenia
 */
class UFbean_SruAdmin_InventoryCard
extends UFbeanSingle {
	
	const TABLE_COMPUTER = 1;
	const TABLE_SWITCH = 2;
	const TABLE_DEVICE = 3;

	protected function validateSerialNo($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_InventoryCard');
			$bean->getBySerialNo($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validateInventoryNo($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_InventoryCard');
			$bean->getByInventoryNo($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}
	
	protected function validateLocationAlias($val, $change) {
		$post = $this->_srv->get('req')->post->{$change?'inventoryCardEdit':'inventoryCardAdd'};
		try {
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByPK((int)$post['dormitory']);
		} catch (UFex $e) {
			return 'noDormitory';
		}
		try {
			$loc = UFra::factory('UFbean_Sru_Location');
			$loc->getByAliasDormitory((string)$val, $dorm->id);
			if (!$change || (isset($this->data['locationId']) && $this->data['locationId']!=$loc->id)) {
				$this->data['locationAlias'] = $val;
				$this->dataChanged['locationAlias'] = $val;
				$this->data['locationId'] = $loc->id;
				$this->dataChanged['locationId'] = $loc->id;
			}
		} catch (UFex $e) {
			return 'noRoom';
		}
	}
}