<?php
/**
 * switch
 */
class UFbean_SruAdmin_Switch
extends UFbeanSingle {

	protected function validateSerialNo($val, $change) {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
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
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->getByInventoryNo($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}

	protected function validateIp($val, $change) {
		try {
			if ($val == '') return;
			$bean = UFra::factory('UFbean_SruAdmin_Switch');
			$bean->getByIp($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
		}
	}        
}
