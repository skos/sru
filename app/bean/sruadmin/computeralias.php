<?
/**
 * alias komputera
 */
class UFbean_SruAdmin_ComputerAlias
extends UFbeanSingle {
	protected function validateHost($val, $change) {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByHost($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
			try {
				$alias = UFra::factory('UFbean_SruAdmin_ComputerAlias');
				$alias->getByHost($val);
				return 'duplicated';
			} catch (UFex_Dao_NotFound $e) {
			}
		}
	}

	protected function normalizeHost($val, $change) {
		return strtolower($val);
	}
}
