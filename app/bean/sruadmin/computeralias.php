<?
/**
 * alias komputera
 */
class UFbean_SruAdmin_ComputerAlias
extends UFbeanSingle {
	protected function validateHost($val, $change) {
		if (in_array($val, UFra::shared('UFconf_Sru')->invalidHostNames)) {
			return 'duplicated';
		}
	}	
	
	protected function validateDomainName($val, $change) {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByDomainName($val);
			if ($change && $this->data['id'] == $bean->id) {
				return;
			}
			return 'duplicated';
		} catch (UFex_Dao_NotFound $e) {
			try {
				$alias = UFra::factory('UFbean_SruAdmin_ComputerAlias');
				$alias->getByDomainName($val);
				return 'duplicated';
			} catch (UFex_Dao_NotFound $e) {
			}
		}
	}

	protected function normalizeHost($val, $change) {
		return strtolower($val);
	}
}
